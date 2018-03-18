function CBXSession()
{
	var _this = this;
	this.mess = {};
	this.timeout = null;
	this.sessid = null;
	this.bShowMess = true;
	this.dateStart = new Date();
	this.dateInput = new Date();
	this.dateCheck = new Date();
	this.activityInterval = 0;
	this.notifier = null;
	
	this.Expand = function(timeout, sessid, bShowMess, key)
	{
		this.timeout = timeout;
		this.sessid = sessid;
		this.bShowMess = bShowMess;
		this.key = key;
		
		BX.ready(function(){
			BX.bind(document, "keypress", _this.OnUserInput);
			BX.bind(document.body, "mousemove", _this.OnUserInput);
			BX.bind(document.body, "click", _this.OnUserInput);
			
			setTimeout(_this.CheckSession, (_this.timeout-60)*1000);
		})
	},
		
	this.OnUserInput = function()
	{
		var curr = new Date();
		_this.dateInput.setTime(curr.valueOf());
	}
	
	this.CheckSession = function()
	{
		var curr = new Date();
		if(curr.valueOf() - _this.dateCheck.valueOf() < 30000)
			return;

		_this.activityInterval = Math.round((_this.dateInput.valueOf() - _this.dateStart.valueOf())/1000);
		_this.dateStart.setTime(_this.dateInput.valueOf());
		var interval = (_this.activityInterval > _this.timeout? (_this.timeout-60) : _this.activityInterval);
		BX.ajax.get('/bitrix/tools/public_session.php?sessid='+_this.sessid+'&interval='+interval+'&k='+_this.key, function(data){_this.CheckResult(data)});
	}
	
	this.CheckResult = function(data)
	{
		if(data == 'SESSION_EXPIRED')
		{
			if(_this.bShowMess)
			{
				_this.notifier = document.body.appendChild(BX.create('DIV', {
					props: {className: 'bx-session-message'},
					style: {top: '-1000px'},
					html: '<a class="bx-session-message-close" href="javascript:bxSession.Close()"></a>'+_this.mess.messSessExpired
				}));
	
				var windowScroll = BX.GetWindowScrollPos();
				var windowSize = BX.GetWindowInnerSize();

				_this.notifier.style.left = parseInt(windowScroll.scrollLeft + windowSize.innerWidth / 2 - parseInt(_this.notifier.clientWidth) / 2) + 'px';

				var fxStart = windowScroll.scrollTop - _this.notifier.offsetHeight;
				var fxFinish = windowScroll.scrollTop;
	
				(new BX.fx({
					time: 0.5,
					step: 0.01,
					type: 'accelerated',
					start: fxStart,
					finish: fxFinish,
					callback: function(top){_this.notifier.style.top = top + 'px';},
					callback_complete: function()
					{
						if(BX.browser.IsIE())
						{
							BX.bind(window, 'scroll', function()
							{
								var windowScroll = BX.GetWindowScrollPos();
								_this.notifier.style.top = windowScroll.scrollTop + 'px';
							});
						}
						else
						{
							_this.notifier.style.top='0px';
							_this.notifier.style.position='fixed';
						}
					}
				})).start();
			}
		}
		else
		{
			var timeout;
			if(data == 'SESSION_CHANGED')
				timeout = (_this.timeout-60);
			else
				timeout = (_this.activityInterval < 60? 60 : (_this.activityInterval > _this.timeout? (_this.timeout-60) : _this.activityInterval));

			var curr = new Date();
			_this.dateCheck.setTime(curr.valueOf());
			setTimeout(_this.CheckSession, timeout*1000);
		}
	}
	
	this.Close = function()
	{
		this.notifier.style.display = 'none';
	}
}

var bxSession = new CBXSession();