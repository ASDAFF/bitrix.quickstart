/* PULL manager JS class */

;(function(window){

	if (!window.BX)
	{
		if (typeof(console) == 'object') console.log('PULL notice: bitrix core not loaded');
		return;
	}
	if (window.BX.PULL)
	{
		if (typeof(console) == 'object') console.log('PULL notice: script is already loaded');
		return;
	}

	var BX = window.BX,
	_updateStateVeryFastCount = 0,
	_updateStateFastCount = 0,
	_updateStateStep = 60,
	_updateGetChannelTimeout = null,
	_updateStateTimeout = null,
	_updateStateSend = false,
	_pullTryConnect = true,
	_pullPath = null,
	_pullMethod = 'PULL',
	_pullTimeConfig = 0,
	_pullTimeConst = (new Date(2022, 2, 19)).toUTCString(),
	_pullTime = _pullTimeConst,
	_pullTag = 1,
	_pullTimeout = 60,
	_watchTag = {},
	_watchTimeout = null,
	_channelID = null,
	_channelClear = null,
	_channelLastID = 0,
	_channelStack = {},
	_WS = null,
	_wsPath = '',
	_wsSupport = false,
	_wsConnected = false,
	_wsTryReconnect = 0,
	_mobileMode = false,
	_lsSupport = false,
	_escStatus = false,
	_sendAjaxTry = 0;

	BX.PULL = function() {};

	BX.PULL.init = function()
	{
		if (_channelID == null)
			BX.PULL.getChannelID('init');
		else
			BX.PULL.updateState('init');

		BX.PULL.updateWatch();
	}

	BX.PULL.start = function(params)
	{
		_mobileMode = false;
		if (typeof(params) == "object" && params.MOBILE == 'Y')
			_mobileMode = true;

		_lsSupport = true;
		if (typeof(params) == "object" && params.LOCAL_STORAGE == 'N')
			_lsSupport = false;

		_wsSupport = true;
		if (typeof(params) == "object" && params.WEBSOCKET == 'N')
			_wsSupport = false;

		BX.bind(window, "offline", function(){
			_pullTryConnect = false;
		});

		BX.bind(window, "online", function(){
			if (!BX.PULL.tryConnect())
				BX.PULL.updateState('10', true);
		});

		if (BX.browser.IsFirefox())
		{
			BX.bind(window, "keypress", function(event){
				if (event.keyCode == 27)
					_escStatus = true;
			});
		}

		if (typeof(WebSocket) != 'function')
			_wsSupport = false;

		if (typeof(params) == "object" && params.CHANNEL_ID)
		{
			_channelID = params.CHANNEL_ID;
			_pullPath = params.PATH.replace('#DOMAIN#', location.hostname);
			_wsPath = params.PATH_WS.replace('#DOMAIN#', location.hostname);
			_pullMethod = params.METHOD;
			_pullTimeConfig = parseInt(params.CHANNEL_DT)+parseInt(BX.message('SERVER_TZ_OFFSET'))+parseInt(BX.message('USER_TZ_OFFSET'));
			_channelLastID = parseInt(params.LAST_ID);
		}

		if (!BX.browser.SupportLocalStorage())
			_lsSupport = false;

		if (_lsSupport)
		{
			BX.addCustomEvent(window, "onLocalStorageSet", BX.PULL.storageSet);
			BX.localStorage.set('pset', {'CHANNEL_ID': _channelID, 'LAST_ID': _channelLastID, 'PATH': _pullPath, 'PATH_WS': _wsPath, 'TIME_LAST_GET': _pullTimeConfig, 'METHOD': _pullMethod}, 5);
		}
		BX.addCustomEvent("onImError", function(error) {
			if (error == 'AUTHORIZE_ERROR')
				_sendAjaxTry++;
		});
		BX.PULL.expireConfig();
		BX.PULL.init();
	}

	BX.PULL.expireConfig = function()
	{
		if (!_channelID)
			return false;

		clearTimeout(_channelClear);
		_channelClear = setTimeout(BX.PULL.expireConfig, 60000);

		if (_channelID && _pullMethod!='PULL' && _pullTimeConfig+43200 < Math.round(+(new Date)/1000)+parseInt(BX.message('SERVER_TZ_OFFSET'))+parseInt(BX.message('USER_TZ_OFFSET')))
		{
			_channelID = null;
			if (_WS) _WS.close();
		}
	}

	BX.PULL.tryConnect = function()
	{
		if (_pullTryConnect)
			return false;

		_pullTryConnect = true;
		BX.PULL.init();

		return true;
	}

	BX.PULL.getChannelID = function(code, withoutCache, send)
	{
		if (!_pullTryConnect)
			return false;

		send = send == false? false: true;
		withoutCache = withoutCache == true? true: false;
		code = typeof(code) == 'undefined'? '0': code;

		BX.ajax({
			url: '/bitrix/components/bitrix/pull.request/ajax.php?GET_CHANNEL&CODE='+code.toUpperCase()+(_mobileMode? '&MOBILE':''),
			method: 'POST',
			dataType: 'json',
			lsId: 'PULL_GET_CHANNEL',
			lsTimeout: 1,
			timeout: 30,
			data: {'PULL_GET_CHANNEL' : 'Y', 'SITE_ID': BX.message('SITE_ID'), 'MOBILE': _mobileMode? 'Y':'N', 'CACHE': withoutCache? 'N': 'Y', 'PULL_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
			onsuccess: BX.delegate(function(data) {
				if (send)
					BX.localStorage.set('pgc', withoutCache, 5);

				if (data.ERROR == '')
				{
					_channelID = data.CHANNEL_ID;
					_pullPath = data.PATH.replace('#DOMAIN#', location.hostname);
					_wsPath = data.PATH_WS.replace('#DOMAIN#', location.hostname);
					_pullMethod = data.METHOD;
					_pullTimeConfig = parseInt(data.CHANNEL_DT)+parseInt(BX.message('SERVER_TZ_OFFSET'))+parseInt(BX.message('USER_TZ_OFFSET'));
					_channelLastID = _pullMethod=='PULL'? data.LAST_ID: _channelLastID;
					data.TIME_LAST_GET = _pullTimeConfig;
					BX.PULL.updateState('11');
					BX.PULL.expireConfig();
					if (_lsSupport)
						BX.localStorage.set('pset', data, 600);
				}
				else
				{
					_sendAjaxTry++;
					_channelID = null;
					BX.onCustomEvent(window, 'onPullStatus', ['offline']);
					if (data.ERROR == 'SESSION_ERROR')
					{
						BX.message({'bitrix_sessid': data.BITRIX_SESSID});
						clearTimeout(_updateGetChannelTimeout);
						_updateGetChannelTimeout = setTimeout(function(){BX.PULL.updateState('12', true)}, (_sendAjaxTry < 2? 2000: BX.PULL.tryConnectTimeout()));
						BX.onCustomEvent(window, 'onPullError', [data.ERROR, data.BITRIX_SESSID]);
					}
					else if (data.ERROR == 'AUTHORIZE_ERROR')
					{
						var setNextCheck = true;
						if (_sendAjaxTry >= 2 && BXIM && !BXIM.desktop.ready())
							setNextCheck = false;

						clearTimeout(_updateGetChannelTimeout);
						if (setNextCheck)
							_updateGetChannelTimeout = setTimeout(function(){BX.PULL.updateState('13', true)}, BX.PULL.tryConnectTimeout());

						BX.onCustomEvent(window, 'onPullError', [data.ERROR]);
					}
					if (send && typeof(console) == 'object')
					{
						var text = "\n========= PULL ERROR ===========\n"+
									"Error type: getChannel error\n"+
									"Error: "+data.ERROR+"\n"+
									"\n"+
									"Data array: "+JSON.stringify(data)+"\n"+
									"================================\n\n";
						console.log(text);
					}
				}
			}, this),
			onfailure: BX.delegate(function(data)
			{
				_sendAjaxTry++;
				_channelID = null;
				BX.onCustomEvent(window, 'onPullStatus', ['offline']);
				if (data == "timeout")
				{
					setTimeout(function(){
						BX.PULL.getChannelID('1')
					}, 10000);
				}
				else if (typeof(console) == 'object')
				{
					var text = "\n========= PULL ERROR ===========\n"+
								"Error type: getChannel onfailure\n"+
								"Error: "+data.ERROR+"\n"+
								"\n"+
								"Data array: "+JSON.stringify(data)+"\n"+
								"================================\n\n";
					console.log(text);
				}
				setTimeout(function(){BX.PULL.updateState('14', true)}, BX.PULL.tryConnectTimeout());

			}, this)
		});
	};

	BX.PULL.updateState = function(code, force)
	{
		if (!_pullTryConnect || _updateStateSend)
			return false;

		code = typeof(code) == 'undefined'? '': code;
		if (_channelID == null || _pullPath == null)
		{
			BX.PULL.getChannelID(code.length>0? code: _channelID == null? '2': '3');
		}
		else
		{
			if (_wsSupport && typeof(_wsPath) == "string" && _wsPath.length > 1 && _pullMethod != 'PULL')
				BX.PULL.connectWebSocket();
			else
				BX.PULL.connectPull(force);
		}
	};

	BX.PULL.connectWebSocket = function()
	{
		if (!_wsSupport)
			return false;

		_updateStateSend = true;
		_WS = new WebSocket(_wsPath);

		_WS.onopen = function() {
			_wsConnected = true;
			_wsTryReconnect = 0;
			_sendAjaxTry = 0;
			BX.onCustomEvent(window, 'onPullStatus', ['online']);
		};
		_WS.onclose = function(data)
		{
			_updateStateSend = false;
			// if user never connected
			if (!_wsConnected)
			{
				if (_wsTryReconnect == 1)
				{
					BX.PULL.getChannelID('4');
				}
				else if (_wsTryReconnect <= 5)
				{
					_updateStateTimeout = setTimeout(function(){
						BX.PULL.getChannelID('5');
					}, 10000);
				}
				else
				{
					_updateStateTimeout = setTimeout(function(){
						BX.PULL.getChannelID('6');
					}, 30000);
				}
				return false;
			}
			_wsConnected = false;

			// if user press ESC button (FF bug)
			if (data.wasClean && (_escStatus || data.code == 1005))
			{
				BX.PULL.updateState('15');
			}
			else if (!data.wasClean)
			{
				BX.PULL.updateState('16');
			}
		};
		_WS.onmessage = function(event)
		{
			var messageCount = 0;
			var dataArray = event.data.match(/#!NGINXNMS!#(.*?)#!NGINXNME!#/gm);
			if (dataArray != null)
			{
				for (var i = 0; i < dataArray.length; i++)
				{
					dataArray[i] = dataArray[i].substring(12, dataArray[i].length-12);
					if (dataArray[i].length <= 0)
						continue;

					var message = BX.parseJSON(dataArray[i]);
					var data = message.text;
					if (typeof (data) == "object")
					{
						if (data.ERROR == "")
						{
							if (message.id)
							{
								message.id = parseInt(message.id);
								if (!_channelStack[''+data.CHANNEL_ID+message.id])
								{
									_channelStack[''+data.CHANNEL_ID+message.id] = message.id;

									if (_channelLastID < message.id)
										_channelLastID = message.id;

									BX.PULL.executeMessages(data.MESSAGE);
								}
							}
						}
						else
						{
							BX.onCustomEvent(window, 'onPullStatus', ['offline']);
							if (typeof(console) == 'object')
							{
								var text = "\n========= PULL ERROR ===========\n"+
											"Error type: updateState fetch\n"+
											"Error: "+data.ERROR+"\n"+
											"\n"+
											"Connect CHANNEL_ID: "+_channelID+"\n"+
											"Connect WS_PATH: "+_wsPath+"\n"+
											"\n"+
											"Data array: "+JSON.stringify(data)+"\n"+
											"================================\n\n";
								console.log(text);
							}
							_channelID = null;
						}
					}
					_pullTag = message.tag;
					_pullTime = message.time;
					messageCount++;
				}
			}
			if (_channelID == null)
			{
				if (_WS) _WS.close();
			}
		};
		_WS.onerror = function() {
			_wsTryReconnect++;
		};
	}

	BX.PULL.connectPull = function(force)
	{
		force = force == true? true: false;
		clearTimeout(_updateStateTimeout);
		_updateStateTimeout = setTimeout(function(){
			if (!_pullPath || typeof(_pullPath) != "string" || _pullPath.length <= 32)
			{
				_pullPath = null;

				clearTimeout(_updateStateTimeout);
				_updateStateTimeout = setTimeout(function(){
					BX.PULL.updateState(!_pullPath? '17': '18');
				}, 10000);

				return false;
			}

			BX.onCustomEvent(window, 'onPullStatus', ['online']);
			_updateStateSend = true;
			var _ajax = BX.ajax({
				url: _pullMethod=='PULL'? _pullPath: (_pullPath+(_pullTag != null? "&tag="+_pullTag:"")+"&rnd="+(+new Date)),
				method: _pullMethod=='PULL'?'POST':'GET',
				dataType: _pullMethod=='PULL'?'json':'html',
				timeout: _pullTimeout,
				headers: [
					{'name':'If-Modified-Since', 'value':_pullTime},
					{'name':'If-None-Match', 'value':'0'}
				],
				data: _pullMethod=='PULL'? {'PULL_UPDATE_STATE' : 'Y', 'CHANNEL_ID': _channelID, 'CHANNEL_LAST_ID': _channelLastID, 'SITE_ID': BX.message('SITE_ID'), 'PULL_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()}: {},
				onsuccess: function(data)
				{
					_updateStateSend = false;
					if (_pullMethod=='PULL' && typeof(data) == "object")
					{
						if (data.ERROR == "")
						{
							_sendAjaxTry = 0;
							BX.PULL.executeMessages(data.MESSAGE);
							if (_lsSupport)
								BX.localStorage.set('pus', {'TAG':null, 'TIME':null, 'MESSAGE':data.MESSAGE}, 5);
						}
						else
						{
							BX.onCustomEvent(window, 'onPullStatus', ['offline']);
							if (data.ERROR == 'SESSION_ERROR')
							{
								BX.message({'bitrix_sessid': data.BITRIX_SESSID});
								BX.onCustomEvent(window, 'onPullError', [data.ERROR, data.BITRIX_SESSID]);
							}
							else
							{
								BX.onCustomEvent(window, 'onPullError', [data.ERROR]);
							}
							if (typeof(console) == 'object')
							{
								var text = "\n========= PULL ERROR ===========\n"+
											"Error type: updateState error\n"+
											"Error: "+data.ERROR+"\n"+
											"\n"+
											"Connect CHANNEL_ID: "+_channelID+"\n"+
											"Connect PULL_PATH: "+_pullPath+"\n"+
											"\n"+
											"Data array: "+JSON.stringify(data)+"\n"+
											"================================\n\n";
								console.log(text);
							}
							_channelID = null;
						}
						if (_channelID != null && _lsSupport)
							BX.localStorage.set('pset', {'CHANNEL_ID': _channelID, 'LAST_ID': _channelLastID, 'PATH': _pullPath, 'PATH_WS': _wsPath, 'TAG': _pullTag, 'TIME': _pullTime, 'TIME_LAST_GET': _pullTimeConfig, 'METHOD': _pullMethod}, 600);

						BX.PULL.setUpdateStateStep();
					}
					else
					{
						if (data.length > 0)
						{
							var messageCount = 0;
							_sendAjaxTry = 0;

							var dataArray = data.match(/#!NGINXNMS!#(.*?)#!NGINXNME!#/gm);
							if (dataArray != null)
							{
								for (var i = 0; i < dataArray.length; i++)
								{
									dataArray[i] = dataArray[i].substring(12, dataArray[i].length-12);
									if (dataArray[i].length <= 0)
										continue;

									var message = BX.parseJSON(dataArray[i]);
									var data = message.text;
									if (typeof (data) == "object")
									{
										if (data.ERROR == "")
										{
											if (message.id)
											{
												message.id = parseInt(message.id);
												if (!_channelStack[''+data.CHANNEL_ID+message.id])
												{
													_channelStack[''+data.CHANNEL_ID+message.id] = message.id;

													if (_channelLastID < message.id)
														_channelLastID = message.id;

													BX.PULL.executeMessages(data.MESSAGE);
												}
											}
										}
										else
										{
											if (typeof(console) == 'object')
											{
												var text = "\n========= PULL ERROR ===========\n"+
															"Error type: updateState fetch\n"+
															"Error: "+data.ERROR+"\n"+
															"\n"+
															"Connect CHANNEL_ID: "+_channelID+"\n"+
															"Connect PULL_PATH: "+_pullPath+"\n"+
															"\n"+
															"Data array: "+JSON.stringify(data)+"\n"+
															"================================\n\n";
												console.log(text);
											}
											_channelID = null;
											BX.onCustomEvent(window, 'onPullStatus', ['offline']);
										}
									}
									else
									{
										if (typeof(console) == 'object')
										{
											var text = "\n========= PULL ERROR ===========\n"+
														"Error type: updateState parse\n"+
														"\n"+
														"Connect CHANNEL_ID: "+_channelID+"\n"+
														"Connect PULL_PATH: "+_pullPath+"\n"+
														"\n"+
														"Data string: "+dataArray[i]+"\n"+
														"================================\n\n";
											console.log(text);
										}
										_channelID = null;
										BX.onCustomEvent(window, 'onPullStatus', ['offline']);
									}
									_pullTag = message.tag;
									_pullTime = message.time;
									messageCount++;
								}
							}
							else
							{
								if (typeof(console) == 'object')
								{
									var text = "\n========= PULL ERROR ===========\n"+
												"Error type: updateState error getting message\n"+
												"\n"+
												"Connect CHANNEL_ID: "+_channelID+"\n"+
												"Connect PULL_PATH: "+_pullPath+"\n"+
												"\n"+
												"Data string: "+data+"\n"+
												"================================\n\n";
									console.log(text);
								}
								_channelID = null;
								BX.onCustomEvent(window, 'onPullStatus', ['offline']);
							}
							if (messageCount > 0 || _ajax && _ajax.status == 0)
							{
								BX.PULL.updateState(messageCount > 0? '19': '20');
							}
							else
							{
								_channelID = null;
								_updateStateTimeout = setTimeout(function(){BX.PULL.updateState('21')}, 10000);
							}
						}
						else
						{
							if (_ajax && _ajax.status == 304)
							{
								BX.PULL.updateState('22');
							}
							else if (_ajax && (_ajax.status == 502 || _ajax.status == 500))
							{
								BX.onCustomEvent(window, 'onPullStatus', ['offline']);
								_sendAjaxTry++;
								_channelID = null;
								_updateStateTimeout = setTimeout(function(){
									BX.PULL.updateState('23');
								}, BX.PULL.tryConnectTimeout());
							}
							else
							{
								BX.onCustomEvent(window, 'onPullStatus', ['offline']);
								if (_ajax && _ajax.status == 0 && _escStatus)
								{
									var timeout = 2000;
									_escStatus = false;
								}
								else
								{
									_sendAjaxTry++;
									_channelID = null;
									var timeout = BX.PULL.tryConnectTimeout();
								}
								var code = (_ajax && _ajax.status? _ajax.status: 'NaN');
								_updateStateTimeout = setTimeout(function(){
									BX.PULL.updateState('24-'+code+'-'+(timeout/1000));
								}, timeout);
							}
						}
					}
				},
				onfailure: function(data)
				{
					BX.onCustomEvent(window, 'onPullStatus', ['offline']);
					_updateStateSend = false;
					_sendAjaxTry++;
					if (data == "timeout")
					{
						if (_pullMethod=='PULL')
							BX.PULL.setUpdateStateStep();
						else
							BX.PULL.updateState('25');
					}
					else if (_ajax && (_ajax.status == 403 || _ajax.status == 404 || _ajax.status == 400))
					{
						_channelID = null;
						setTimeout(function(){
							BX.PULL.getChannelID('7-'+_ajax.status, _ajax.status == 403? true: false)
						}, (_sendAjaxTry < 2? 50: BX.PULL.tryConnectTimeout()));
					}
					else if (_ajax && (_ajax.status == 500 || _ajax.status == 502))
					{
						_channelID = null;
						setTimeout(function(){
							BX.PULL.getChannelID('8-'+_ajax.status)
						}, (_sendAjaxTry < 2? 50: BX.PULL.tryConnectTimeout()));
					}
					else
					{
						if (typeof(console) == 'object')
						{
							var text = "\n========= PULL ERROR ===========\n"+
										"Error type: updateState onfailure\n"+
										"\n"+
										"Connect CHANNEL_ID: "+_channelID+"\n"+
										"Connect PULL_PATH: "+_pullPath+"\n"+
										"\n"+
										"Data array: "+JSON.stringify(data)+"\n"+
										"================================\n\n";
							console.log(text);
						}
						if (_pullMethod=='PULL')
							_updateStateTimeout = setTimeout(BX.PULL.setUpdateStateStep, 10000);
						else
							_updateStateTimeout = setTimeout(function(){BX.PULL.updateState('26');}, 10000);
					}
				}
			});
		}, force? 150: (_pullMethod == 'PULL'? _updateStateStep: 0.3)*1000);
	}

	BX.PULL.extendWatch = function(tag, force)
	{
		if (tag.length <= 0)
			return false;

		_watchTag[tag] = true;

		if (force === true)
			BX.PULL.updateWatch(true);
	};

	BX.PULL.clearWatch = function(id)
	{
		if (id == 'undefined')
			_watchTag = {};
		else if (_watchTag[id])
			delete _watchTag[id];
	}

	BX.PULL.updateWatch = function(force)
	{
		if (!_pullTryConnect)
			return false;

		force = force == true? true: false;
		clearTimeout(_watchTimeout);
		_watchTimeout = setTimeout(function()
		{
			var arWatchTag = [];
			for(var i in _watchTag)
				arWatchTag.push(i);

			if (arWatchTag.length > 0)
			{
				BX.ajax({
					url: '/bitrix/components/bitrix/pull.request/ajax.php?UPDATE_WATCH',
					method: 'POST',
					dataType: 'json',
					timeout: 30,
					lsId: 'PULL_WATCH_'+location.pathname,
					lsTimeout: 5,
					data: {'PULL_UPDATE_WATCH' : 'Y', 'WATCH' : arWatchTag, 'SITE_ID': BX.message('SITE_ID'), 'PULL_AJAX_CALL' : 'Y', 'sessid': BX.bitrix_sessid()},
					onsuccess: BX.delegate(function() {
						BX.localStorage.set('puw', location.pathname, 5);
					}, this)
				});
			}
			BX.PULL.updateWatch();
		}, force? 5000: 1740000);
	};

	BX.PULL.executeMessages = function(message, pull)
	{
		pull = pull == false? false: true;
		for (var i = 0; i < message.length; i++)
		{
			if (message[i].id)
			{
				message[i].id = parseInt(message[i].id);
				if (_channelStack[''+_channelID+message[i].id])
					continue;
				else
					_channelStack[''+_channelID+message[i].id] = message[i].id;

				if (_channelLastID < message[i].id)
					_channelLastID = message[i].id;
			}
			if (message[i].module_id == 'pull')
			{
				if (pull)
				{
					if (message[i].command == 'channel_die')
						_channelID = null;

					if (message[i].command == 'config_die')
						_pullPath = null;
				}
			}
			else
			{
				if (!(message[i].module_id == 'main' && message[i].command == 'user_counter'))
					BX.PULL.setUpdateStateStepCount(1,4);

				try { BX.onCustomEvent(window, 'onPullEvent', [message[i].module_id, message[i].command, message[i].params], true); }
				catch(e)
				{
					if (typeof(console) == 'object')
					{
						var text = "\n========= PULL ERROR ===========\n"+
									"Error type: onPullEvent onfailure\n"+
									"Error event: "+JSON.stringify(e)+"\n"+
									"\n"+
									"Message MODULE_ID: "+message[i].module_id+"\n"+
									"Message COMMAND: "+message[i].command+"\n"+
									"Message PARAMS: "+message[i].params+"\n"+
									"\n"+
									"Message array: "+JSON.stringify(message[i])+"\n"+
									"================================\n";
						console.log(text);
						BX.debug(e);
					}
				}
			}
		}
	}

	BX.PULL.setUpdateStateStep = function(send)
	{
		var send = send == false? false: true;
		var step = 60;

		if (_updateStateVeryFastCount > 0)
		{
			step = 10;
			_updateStateVeryFastCount--;
		}
		else if (_updateStateFastCount > 0)
		{
			step = 20;
			_updateStateFastCount--;
		}

		_updateStateStep = parseInt(step);

		BX.PULL.updateState('27');

		if (send && _lsSupport)
			BX.localStorage.set('puss', _updateStateStep, 5);
	}

	BX.PULL.setUpdateStateStepCount = function(veryFastCount, fastCount)
	{
		_updateStateVeryFastCount = parseInt(veryFastCount);
		_updateStateFastCount = parseInt(fastCount);
	}

	BX.PULL.storageSet = function(params)
	{
		if (params.key == 'pus')
		{
			if (params.value.TAG != null)
				_pullTag = params.value.TAG;

			if (params.value.TIME != null)
				_pullTime = params.value.TIME;

			BX.PULL.executeMessages(params.value.MESSAGE, false);
		}
		else if (params.key == 'puss')
		{
			_updateStateStep = 70;
			BX.PULL.updateState('28');
		}
		else if (params.key == 'pgc')
		{
			BX.PULL.getChannelID('9', params.value, false);
		}
		else if (params.key == 'pset')
		{
			_channelID = params.value.CHANNEL_ID;
			_channelLastID = params.value.LAST_ID;
			_pullPath = params.value.PATH;
			_wsPath = params.value.PATH_WS;
			_pullMethod = params.value.METHOD;
			if (params.value.TIME)
				_pullTime = params.value.TIME;
			if (params.value.TAG)
				_pullTag = params.value.TAG;
			if (params.value.TIME_LAST_GET)
				_pullTimeConfig = params.value.TIME_LAST_GET;

			if (_channelID != null)
			{
				if (!BX.PULL.tryConnect())
					BX.PULL.updateState('29', true);
			}
		}
		else if (params.key == 'puw')
		{
			if (params.value == location.pathname)
				BX.PULL.updateWatch();
		}
	}

	BX.PULL.updateChannelID = function(params)
	{
		if (typeof(params) != 'object')
			return false;

		var method = params.METHOD;
		var channelID = params.CHANNEL_ID;
		var channelDie = params.CHANNEL_DT;
		var pullPath = params.PATH;
		var lastId = params.LAST_ID;
		var wsPath = params.PATH_WS;

		if (typeof(channelID) == 'undefined' || typeof(pullPath) == 'undefined')
			return false;

		if (channelID == _channelID && pullPath == _pullPath && wsPath == _wsPath)
			return false;

		_channelID = channelID;
		_pullTimeConfig = parseInt(channelDie)+parseInt(BX.message('SERVER_TZ_OFFSET'))+parseInt(BX.message('USER_TZ_OFFSET'));
		_pullPath = pullPath.replace('#DOMAIN#', location.hostname);
		_wsPath = wsPath.replace('#DOMAIN#', location.hostname);
		_channelLastID = _pullMethod=='PULL' && typeof(lastId) == 'number'? lastId: _channelLastID;
		if (typeof(method) == 'string')
			_pullMethod = method;

		if (_lsSupport)
			BX.localStorage.set('pset', {'CHANNEL_ID': _channelID, 'LAST_ID': _channelLastID, 'PATH': _pullPath, 'PATH': _wsPath, 'TAG': _pullTag, 'TIME': _pullTime, 'TIME_LAST_GET': _pullTimeConfig, 'METHOD': _pullMethod}, 600);

		if (_WS) _WS.close();

		return true;
	}

	BX.PULL.tryConnectTimeout = function()
	{
		var timeout = 0;
		if (_sendAjaxTry <= 2)
			timeout = 15000;
		else if (_sendAjaxTry > 2 && _sendAjaxTry <= 5)
			timeout = 45000;
		else if (_sendAjaxTry > 5 && _sendAjaxTry <= 10)
			timeout = 600000;
		else if (_sendAjaxTry > 10)
		{
			_pullTryConnect = false;
			timeout = 3600000;
		}

		return timeout;
	}

	/* DEBUG commands */
	BX.PULL.tryConnectSet = function(sendAjaxTry, pullTryConnect)
	{
		if (typeof(sendAjaxTry) == 'number')
			_sendAjaxTry = parseInt(sendAjaxTry);

		if (typeof(pullTryConnect) == 'boolean')
			_pullTryConnect = pullTryConnect;
	}

	BX.PULL.getPullServerStatus = function()
	{
		return _pullMethod == 'PULL'? false: true;
	}
	BX.PULL.getDebugInfo = function()
	{
		if (!console || !console.log || !JSON || !JSON.stringify)
			return false;

		var textWT = JSON.stringify(_watchTag);
		var text = "\n========= PULL DEBUG ===========\n"+
					"Connect: "+(_updateStateSend? 'Y': 'N')+"\n"+
					"WebSocket connect: "+(_wsConnected? 'Y': 'N')+"\n"+
					"LocalStorage status: "+(_lsSupport? 'Y': 'N')+"\n"+
					"WebSocket support: "+(_wsSupport && _wsPath.length > 0? 'Y': 'N')+"\n"+
					"Queue Server: "+(_pullMethod == 'PULL'? 'N': 'Y')+"\n"+
					"Try connect: "+(_pullTryConnect? 'Y': 'N')+"\n"+
					"Try number: "+(_sendAjaxTry)+"\n"+
					"\n"+
					"Path: "+_pullPath+"\n"+
					(_wsPath.length > 0? "WebSocket Path: "+_wsPath+"\n": '')+
					"ChannelID: "+_channelID+"\n"+
					"ChannelDie: "+(parseInt(_pullTimeConfig))+"\n"+
					"\n"+
					"Last message: "+(_channelLastID > 0? _channelLastID: '-')+"\n"+
					"Time init connect: "+(_pullTimeConst)+"\n"+
					"Time last connect: "+(_pullTime == _pullTimeConst? '-': _pullTime)+"\n"+
					"Watch tags: "+(textWT == '{}'? '-': textWT)+"\n"+
					"================================\n";

		return console.log(text);
	}

	BX.PULL.clearChannelId = function(send)
	{
		send = send == false? false: true;

		_channelID = null;
		_pullPath = null;
		_updateStateSend = false;
		clearTimeout(_updateStateTimeout);

		if (send)
			BX.PULL.updateState('30');
	}

	BX.PULL();
})(window);
