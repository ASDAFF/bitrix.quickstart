;(function(){

	function UserConsentControl (params)
	{
		this.caller = params.caller;
		this.formNode = params.formNode;
		this.controlNode = params.controlNode;
		this.inputNode = params.inputNode;
		this.config = params.config;
	}
	UserConsentControl.prototype = {

	};

	BX.UserConsent = {
		msg: {
			'title': BX.message('MAIN_USER_CONSENT_REQUEST_TITLE'),
			'btnAccept': BX.message('MAIN_USER_CONSENT_REQUEST_BTN_ACCEPT'),
			'btnReject': BX.message('MAIN_USER_CONSENT_REQUEST_BTN_REJECT'),
			'loading': BX.message('MAIN_USER_CONSENT_REQUEST_LOADING'),
			'errTextLoad': BX.message('MAIN_USER_CONSENT_REQUEST_ERR_TEXT_LOAD')
		},
		events: {
			'save': 'main-user-consent-request-save',
			'refused': 'main-user-consent-request-refused',
			'accepted': 'main-user-consent-request-accepted'
		},
		textList: {},
		current: null,
		autoSave: false,
		isFormSubmitted: false,
		isConsentSaved: false,
		attributeControl: 'data-bx-user-consent',
		load: function (context)
		{
			var item = this.find(context)[0];
			if (!item)
			{
				return null;
			}

			this.bind(item);
			return item;
		},
		loadAll: function (context, limit)
		{
			this.find(context, limit).forEach(this.bind, this);
		},
		loadFromForms: function ()
		{
			var formNodes = document.getElementsByTagName('FORM');
			formNodes = BX.convert.nodeListToArray(formNodes);
			formNodes.forEach(this.loadAll, this);
		},
		find: function (context)
		{
			if (!context)
			{
				return [];
			}

			var controlNodes = context.querySelectorAll('[' + this.attributeControl + ']');
			controlNodes = BX.convert.nodeListToArray(controlNodes);
			return controlNodes.map(this.createItem.bind(this, context)).filter(function (item) { return !!item });
		},
		bind: function (item)
		{
			if (item.config.submitEventName)
			{
				BX.addCustomEvent(item.config.submitEventName, this.onSubmit.bind(this, item));
			}
			else if(item.formNode)
			{
				BX.bind(item.formNode, 'submit', this.onSubmit.bind(this, item));
			}

			BX.bind(item.controlNode, 'click', this.onClick.bind(this, item));
		},
		createItem: function (context, controlNode)
		{
			var inputNode = controlNode.querySelector('input[type="checkbox"]');
			if (!inputNode)
			{
				return;
			}

			try
			{
				var config = JSON.parse(controlNode.getAttribute(this.attributeControl));
				var parameters = {
					'formNode': null,
					'controlNode': controlNode,
					'inputNode': inputNode,
					'config': config
				};

				if (context.tagName == 'FORM')
				{
					parameters.formNode = context;
				}
				else
				{
					parameters.formNode = BX.findParent(inputNode, {tagName: 'FORM'})
				}

				parameters.caller = this;
				return new UserConsentControl(parameters);
			}
			catch (e)
			{
				return null;
			}
		},
		onClick: function (item, e)
		{
			this.requestForItem(item);
			e.preventDefault();
		},
		onSubmit: function (item, e)
		{
			this.isFormSubmitted = true;
			if (this.check(item))
			{
				return true;
			}
			else
			{
				if (e)
				{
					e.preventDefault();
				}

				return false;
			}
		},
		check: function (item)
		{
			if (item.inputNode.checked)
			{
				this.saveConsent(item);
				return true;
			}

			this.requestForItem(item);
			return false;
		},
		requestForItem: function (item)
		{
			this.setCurrent(item);
			this.requestConsent(
				item.config.id,
				{
					'sec': item.config.sec,
					'replace': item.config.replace
				},
				this.onAccepted,
				this.onRefused
			);
		},
		setCurrent: function (item)
		{
			this.current = item;
			this.autoSave = item.config.autoSave;
			this.actionRequestUrl = item.config.actionUrl;
		},
		onAccepted: function ()
		{
			if (!this.current)
			{
				return;
			}

			var item = this.current;
			this.saveConsent(
				this.current,
				function ()
				{
					BX.onCustomEvent(item, this.events.accepted, []);
					BX.onCustomEvent(this, this.events.accepted, [item]);

					this.isConsentSaved = true;

					if (this.isFormSubmitted && item.formNode && !item.config.submitEventName)
					{
						BX.submit(item.formNode);
					}
				}
			);

			this.current.inputNode.checked = true;
			this.current = null;
		},
		onRefused: function ()
		{
			BX.onCustomEvent(this.current, this.events.refused, []);
			BX.onCustomEvent(this, this.events.refused, [this.current]);
			this.current.inputNode.checked = false;
			this.current = null;
			this.isFormSubmitted = false;
		},
		initPopup: function ()
		{
			if (this.popup)
			{
				return;
			}


			this.popup = {

			};
		},
		popup: {
			isInit: false,
			caller: null,
			nodes: {
				container: null,
				shadow: null,
				head: null,
				loader: null,
				content: null,
				textarea: null,
				buttonAccept: null,
				buttonReject: null
			},
			onAccept: function ()
			{
				this.hide();
				BX.onCustomEvent(this, 'accept', []);
			},
			onReject: function ()
			{
				this.hide();
				BX.onCustomEvent(this, 'reject', []);
			},
			init: function ()
			{
				if (this.isInit)
				{
					return true;
				}

				var tmplNode = document.querySelector('script[data-bx-template]');
				if (!tmplNode)
				{
					return false;
				}

				var popup = document.createElement('DIV');
				popup.innerHTML = tmplNode.innerHTML;
				popup = popup.children[0];
				if (!popup)
				{
					return false;
				}
				document.body.insertBefore(popup, document.body.children[0]);

				this.isInit = true;
				this.nodes.container = popup;
				this.nodes.shadow = this.nodes.container.querySelector('[data-bx-shadow]');
				this.nodes.head = this.nodes.container.querySelector('[data-bx-head]');
				this.nodes.loader = this.nodes.container.querySelector('[data-bx-loader]');
				this.nodes.content = this.nodes.container.querySelector('[data-bx-content]');
				this.nodes.textarea = this.nodes.container.querySelector('[data-bx-textarea]');

				this.nodes.buttonAccept = this.nodes.container.querySelector('[data-bx-btn-accept]');
				this.nodes.buttonReject = this.nodes.container.querySelector('[data-bx-btn-reject]');
				this.nodes.buttonAccept.textContent = this.caller.msg.btnAccept;
				this.nodes.buttonReject.textContent = this.caller.msg.btnReject;
				BX.bind(this.nodes.buttonAccept, 'click', this.onAccept.bind(this));
				BX.bind(this.nodes.buttonReject, 'click', this.onReject.bind(this));

				return true;
			},
			setTitle: function (text)
			{
				if (!this.nodes.head)
				{
					return;
				}
				this.nodes.head.textContent = text;
			},
			setContent: function (text)
			{
				if (!this.nodes.textarea)
				{
					return;
				}
				this.nodes.textarea.textContent = text;
			},
			show: function (isContentVisible)
			{
				if (typeof isContentVisible == 'boolean')
				{
					this.nodes.loader.style.display = !isContentVisible ? '' : 'none';
					this.nodes.content.style.display = isContentVisible ? '' : 'none';
				}

				this.nodes.container.style.display = '';
			},
			hide: function ()
			{
				this.nodes.container.style.display = 'none';
			}
		},
		requestConsent: function (id, sendData, onAccepted, onRefused)
		{
			sendData = sendData || {};
			sendData.id = id;

			if (!this.popup.isInit)
			{
				this.popup.caller = this;
				if (!this.popup.init())
				{
					return;
				}

				BX.addCustomEvent(this.popup, 'accept', onAccepted.bind(this));
				BX.addCustomEvent(this.popup, 'reject', onRefused.bind(this));
			}

			if (this.current && this.current.config.text)
			{
				this.textList[id] = this.current.config.text;
			}

			if (this.textList.hasOwnProperty(id))
			{
				this.setTextToPopup(this.textList[id]);
			}
			else
			{
				this.popup.setTitle(this.msg.loading);
				this.popup.show(false);
				this.sendActionRequest(
					'getText', sendData,
					function (data)
					{
						this.textList[id] = data.text || '';
						this.setTextToPopup(this.textList[id]);
					},
					function ()
					{
						this.popup.hide();
						alert(this.msg.errTextLoad);
					}
				);
			}
		},
		setTextToPopup: function (text)
		{
			// set title from a first line from text.
			var titleBar = '';
			var textTitlePos = text.indexOf("\n");
			var textTitleDotPos = text.indexOf(".");
			textTitlePos = textTitlePos < textTitleDotPos ? textTitlePos : textTitleDotPos;
			if (textTitlePos >= 0 && textTitlePos <= 100)
			{
				titleBar = text.substr(0, textTitlePos).trim();
				titleBar  = titleBar.split(".").map(Function.prototype.call, String.prototype.trim).filter(String)[0];
			}
			this.popup.setTitle(titleBar ? titleBar : this.msg.title);
			this.popup.setContent(text);
			this.popup.show(true);
		},
		saveConsent: function (item, callback)
		{
			this.setCurrent(item);

			var data = {
				'id': item.config.id,
				'sec': item.config.sec,
				'url': window.location.href
			};
			if (item.config.originId)
			{
				var originId = item.config.originId;
				if (item.formNode && originId.indexOf('%') >= 0)
				{
					var inputs = item.formNode.querySelectorAll('input[type="text"], input[type="hidden"]');
					inputs = BX.convert.nodeListToArray(inputs);
					inputs.forEach(function (input) {
						if (!input.name)
						{
							return;
						}
						originId = originId.replace('%' + input.name +  '%', input.value ? input.value : '');
					});
				}
				data.originId = originId;
			}
			if (item.config.originatorId)
			{
				data.originatorId = item.config.originatorId;
			}

			BX.onCustomEvent(item, this.events.save, [data]);
			BX.onCustomEvent(this, this.events.save, [item, data]);

			if (this.isConsentSaved || !item.config.autoSave)
			{
				if (callback)
				{
					callback.apply(this, []);
				}
			}
			else
			{
				this.sendActionRequest(
					'saveConsent',
					data,
					callback,
					callback
				);
			}
		},
		sendActionRequest: function (action, sendData, callbackSuccess, callbackFailure)
		{
			callbackSuccess = callbackSuccess || null;
			callbackFailure = callbackFailure || null;

			sendData.action = action;
			sendData.sessid = BX.bitrix_sessid();
			sendData.action = action;

			BX.ajax({
				url: this.actionRequestUrl,
				method: 'POST',
				data: sendData,
				timeout: 10,
				dataType: 'json',
				processData: true,
				onsuccess: BX.proxy(function(data){
					data = data || {};
					if(data.error)
					{
						callbackFailure.apply(this, [data]);
					}
					else if(callbackSuccess)
					{
						callbackSuccess.apply(this, [data]);
					}
				}, this),
				onfailure: BX.proxy(function(){
					var data = {'error': true, 'text': ''};
					if (callbackFailure)
					{
						callbackFailure.apply(this, [data]);
					}
				}, this)
			});
		}
	};

	BX.ready(function () {
		BX.UserConsent.loadFromForms();
	});

})();