BX.CMessageDialog = function(arParams) {

	BX.CMessageDialog.superclass.constructor.apply(this);

	this.oData = new Array();
	this.getCounter = 0;
	this.iGetDay = 0;
	this.iGetTS = 0;

	this.oCurrentMessagePointer = 0;
	this.Notifier = null;
	this.change_cnt = null;
	this.stop = false;
	this.NotifierOnClick = false;
	this.lastMessageTs = 0;
	this.bPosFromOption = false;
	this.PosFromOptionLeft = 0;
	this.PosFromOptionTop = 0;	
			
	this.PARAMS = arParams || {};
	
	this.PARAMS.width = this.PARAMS.width ? this.PARAMS.width : this.defaultParams['width'];
	this.PARAMS.height = this.PARAMS.height ? this.PARAMS.height : this.defaultParams['height'];

	BX.addClass(this.DIV, 'pm-messages-box');
	BX.removeClass(this.DIV, 'bx-core-window');	

	this.PARTS = {};

	this.PARTS.INNER = this.DIV.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-inner'}
	}));

	this.PARTS.BOTTOM = this.DIV.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-bottom'},
		children: [
			BX.create('DIV', { props: {className: 'pm-messages-box-bottom-left'}}),
			BX.create('DIV', { props: {className: 'pm-messages-box-bottom-center'}}),
			BX.create('DIV', { props: {className: 'pm-messages-box-bottom-right'}})
		]
	}));


	this.PARTS.CONTENT = this.PARTS.INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-content'}
	}));


	this.PARTS.TITLE = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-title'},
		html: '<a href="javascript:void(0);"></a><span></span>'
	}));

	this.SetClose(this.PARTS.TITLE.firstChild);
	this.PARTS.TITLE.firstChild.title = BX.message('JS_CORE_WINDOW_CLOSE');

	this.PARTS.PROFILE = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-profile'}
	}));

	this.PARTS.PROFILE_INNER = this.PARTS.PROFILE.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-profile-inner'}
	}));

	this.PARTS.PROFILE_AVATAR = this.PARTS.PROFILE_INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-profile-avatar'}
	}));
	this.PARTS.PROFILE_INFO = this.PARTS.PROFILE_INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-profile-info'}
	}));
	this.PARTS.DATE = this.PARTS.PROFILE_INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-date'}
	}));

	this.PARTS.TEXT = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-text'}
	}));
	this.PARTS.TEXT_INNER = this.PARTS.TEXT.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-text-inner'}
	}));
	this.PARTS.TEXT_CONTENT = this.PARTS.TEXT_INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-text-content'}
	}));

	this.PARTS.ACTIONS = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-actions'}
	}));
	this.PARTS.ACTIONS_INNER = this.PARTS.ACTIONS.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-actions-inner'}
	}));

	this.PARTS.BUTTONS = this.PARTS.ACTIONS_INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-buttons'}
	}));

	BX.adjust(this.DIV, {
		style: {
			height: this.PARAMS.height + 'px',
			width: this.PARAMS.width + 'px',
			zIndex: 500
		}
	});
	this.SetDraggable(this.PARTS.TITLE);
}
BX.extend(BX.CMessageDialog, BX.CWindow);

BX.CMessageDialog.prototype.defaultParams = {
	width: 100,
	height: 100,
	resizable: false,
	draggable: true,
	title: '',
	icon: ''
}

BX.CMessageDialog.prototype.GetContent = function(data) {

	this.oData = data;

	var currentDate = new Date();
	this.iGetDay = currentDate.getDate();
	this.iGetTS = currentDate.getTime();

	if (this.oData.length > 0 && this.oData[0]["TYPE"] != null)
	{
		this.lastMessageTs = this.oData[this.oData.length - 1]["DATE_TIMESTAMP"];

		if (this.Notifier == null)
		{
			this.Notifier = document.body.appendChild(BX.create('DIV', {
				props: {className: 'pm-messages-notification'},
				style: { 
					top: '-30px'
				},
				children: [
					BX.create('SPAN', { props: {className: 'pm-messages-notification-left'}}),
					BX.create('SPAN', { 
						props: {className: 'pm-messages-notification-text'},
						children: [
							BX.create('SPAN', {}),
							BX.create('SPAN', {props: { className: 'pm-messages-notification-text-text' }})
						]
					}),
					BX.create('A', { 
						props: { className: 'pm-messages-notification-close' },
						attrs: { href: 'javascript:void(0);', title: BX.message('sonetDynevClose') },
						children: [
							BX.create('SPAN', {})
						],
						events: {
							'click': BX.delegate(this.CloseTab, this)
						}
					}),
					BX.create('SPAN', { props: {className: 'pm-messages-notification-right'}})
				]
			}));

			if ((BX.admin == null || BX.admin.panel.DIV == null) && !BX.browser.IsIE())
			{
				this.Notifier.style.position = 'fixed';
				this.DIV.style.position = 'fixed';
			}
			else
				BX.bind(window, 'scroll', BX.proxy(MessageDialog.onScroll, MessageDialog));

			this.Notifier.firstChild.nextSibling.firstChild.innerHTML = this.oData.length;

			itemsSuffix = '';
			itemsCounter = this.oData.length % 100;

			if ((itemsCounter - 20) >= 0)
				itemsSuffix = 'xx';
			else if ((itemsCounter - 10) >= 0)
				itemsSuffix = 'x';

			this.Notifier.firstChild.nextSibling.firstChild.nextSibling.innerHTML = BX.message('sonetDynevNfier_' + (itemsCounter % 10) + itemsSuffix);

			var fxStart = 0;
			var fxFinish = 0;

			var windowSize = BX.GetWindowInnerSize();
			var windowScroll = BX.GetWindowScrollPos();
			this.Notifier.style.left = parseInt(windowScroll.scrollLeft + windowSize.innerWidth / 2 - parseInt(this.Notifier.clientWidth) / 2) + 'px';

			fxStart = -26;
			fxFinish = GetTabOffset();

			(new BX.fx({
				time: 1.0,
				step: 0.01,
				type: 'decelerated',
				start: fxStart,
				finish: fxFinish,
				callback: BX.delegate(SetTabTop, this.Notifier)
			})).start();
		}
		else if (this.oData.length > this.getCounter)
		{
			var fxStart = GetTabOffset();
			var fxFinish = -26;

			(new BX.fx({
				time: 1.0,
				step: 0.01,
				type: 'accelerated',
				start: fxStart,
				finish: fxFinish,
				callback: BX.delegate(SetTabTop, this.Notifier),
				callback_complete: BX.delegate(function() 
					{
						this.Notifier.firstChild.nextSibling.firstChild.innerHTML = this.oData.length;

						itemsSuffix = '';
						itemsCounter = this.oData.length % 100;

						if ((itemsCounter - 20) >= 0)
							itemsSuffix = 'xx';
						else if ((itemsCounter - 10) >= 0)
							itemsSuffix = 'x';

						this.Notifier.firstChild.nextSibling.firstChild.nextSibling.innerHTML = BX.message('sonetDynevNfier_' + (itemsCounter % 10) + itemsSuffix);

						fxStart = GetTabOffset();
						fxFinish = -26;

						(new BX.fx({
							time: 1.0,
							step: 0.01,
							type: 'decelerated',
							start: fxFinish,
							finish: fxStart,
							callback: BX.delegate(SetTabTop, this.Notifier)
						})).start();}, this)
				})).start();

		}

		this.getCounter = this.oData.length;
		if (BX(BX.message('sonetDynevUnreadCntId')) != null)
			BX(BX.message('sonetDynevUnreadCntId')).innerHTML = BX.message('sonetDynevUnreadCntStrBefore') + this.getCounter + BX.message('sonetDynevUnreadCntStrAfter');
	}
}

BX.CMessageDialog.prototype.SetPaging = function() 
{

	this.PARTS.PAGING = this.PARTS.ACTIONS_INNER.appendChild(BX.create('DIV', {
		props: {className: 'pm-messages-box-paging'},
		children: [
			BX.create('A', { props: { href: 'javascript:void(0);', className: 'pm-messages-box-larr', title: BX.message('sonetDynevPrev') } }),
			BX.create('I', {} ),
			BX.create('SPAN', { html: BX.message('sonetDynevPagerFrom') }),
			BX.create('B', {} ),
			BX.create('A', { props: { href: 'javascript:void(0);', className: 'pm-messages-box-rarr', title: BX.message('sonetDynevNext') } })
		]
	}));

	BX.bind(document, 'keyup', BX.proxy(this.__checkPrevNextKeyPress, this));
}

BX.CMessageDialog.prototype.RemovePaging = function() 
{
	if (this.PARTS.PAGING != null && this.PARTS.PAGING.parentNode != null)
		this.PARTS.PAGING.parentNode.removeChild(this.PARTS.PAGING);
}

BX.CMessageDialog.prototype.ShowCheckboxRead = function(id, name)
{
	var result = [];

	var _this = this;

	var chbx = {
		props: {
			'type': 'checkbox',
			'name': name,
			'value': 'Y',
			'id': id,
			className: 'read-checkbox',
			'checked': true,
			'defaultChecked': true			
		},
		events: {
			'click': function () {
				_this.SetMessageRead(_this.oData[_this.oCurrentMessagePointer]["ID"], this.checked);
			}
		}
	};

	checkbox = BX.create('INPUT', chbx);
	result.push(checkbox);

	var lbl = {
		attrs: {
			'for': id
		},
		html: BX.message('sonetDynevRead')
	};

	label = BX.create('LABEL', lbl);
	result.push(label);

	return result;
}

BX.CMessageDialog.prototype.adjustSize = function()
{
	return;
	setTimeout(BX.delegate(this.__adjustSize, this), 10);
}

BX.CMessageDialog.prototype.OnPrev = function()
{
	this.Prev();
}

BX.CMessageDialog.prototype.Prev = function()
{
	if (this.oCurrentMessagePointer > 0)
		this.oCurrentMessagePointer--;
	else
		this.oCurrentMessagePointer = this.oData.length - 1;

	this.ShowContent(this.oCurrentMessagePointer);
}


BX.CMessageDialog.prototype.OnNext = function()
{
	this.Next();
}

BX.CMessageDialog.prototype.Next = function()
{
	if (this.oCurrentMessagePointer < this.oData.length - 1)
		this.oCurrentMessagePointer++;
	else
		this.oCurrentMessagePointer = 0;

	this.ShowContent(this.oCurrentMessagePointer);
}

BX.CMessageDialog.prototype.ShowDialog = function()
{

	this.oCurrentMessagePointer = 0;
	clearTimeout(sonetDynevTout);
	this.SetPaging();
	BX.WindowManager.register(this);
	BX.onCustomEvent(this, 'onWindowRegister');
	BX.addCustomEvent(this, 'onWindowDragFinished', this.onDrop);

	if (this.oData != null && this.oData[0] != null && this.oData[0]["POS_LEFT"] !== undefined && this.oData[0]["POS_TOP"] !== undefined)
	{
		if ((BX.admin == null || BX.admin.panel.DIV == null) && !BX.browser.IsIE())
		{
			var left = parseInt(this.oData[0]["POS_LEFT"]);
			var top = parseInt(this.oData[0]["POS_TOP"]);		
		}
		else
		{
			var windowScroll = BX.GetWindowScrollPos();
			var left = windowScroll.scrollLeft + parseInt(this.oData[0]["POS_LEFT"]);
			var top = windowScroll.scrollTop + parseInt(this.oData[0]["POS_TOP"]);
			this.bPosFromOption = true;
			this.PosFromOptionLeft = parseInt(this.oData[0]["POS_LEFT"]);
			this.PosFromOptionTop = parseInt(this.oData[0]["POS_TOP"]);
		}
	}
	else
	{
		var windowSize = BX.GetWindowInnerSize();
		var windowScroll = BX.GetWindowScrollPos();
		var left = parseInt(windowScroll.scrollLeft + windowSize.innerWidth / 2 - parseInt(MessageDialog.DIV.style.width) / 2);
		var top = (GetTabOffset() + 50);
	}

	BX.adjust(this.DIV, {
		style: {
			left: left + 'px',
			top: top + 'px'
		}
	});

	this.ShowContent(this.oCurrentMessagePointer);
	this.DIV.style.display = 'block';
}

BX.CMessageDialog.prototype.adjustButtons = function(pointer)
{
	_this = this;
	BX.cleanNode(this.PARTS.BUTTONS);

	var arButtons = new Array;

	if (this.oData[pointer]["TYPE"] == "M")
	{
		var arCheckBox = this.ShowCheckboxRead('pm-message-id-' + this.oData[pointer]["ID"], 'read');
		for (var k = 0; k < arCheckBox.length; k++)
			arButtons[arButtons.length] = arCheckBox[k];
	}

	if (this.oData[pointer]["BUTTONS"] != null)
	{
		for (var i = 0; i < this.oData[pointer]["BUTTONS"].length; i++)
		{
			ix = i;

			if (_this.oData[pointer]["BUTTONS"][ix]["URL"] != null)
				functionClick = BX.delegate(function () { sonet_dynev_msgs_set(this["URL"]); }, _this.oData[pointer]["BUTTONS"][ix]);
			else if (_this.oData[pointer]["BUTTONS"][ix]["ONCLICK"] != null)
				functionClick = new Function('return (' + _this.oData[pointer]["BUTTONS"][ix]["ONCLICK"] + ')(arguments);');
			else
				functionClick = function () { return false; }

			arButtons[arButtons.length] = 
					BX.create('INPUT', {
						props: {
							'type': 'button',
							'name': this.oData[pointer]["BUTTONS"][ix]["ID"],
							'value': this.oData[pointer]["BUTTONS"][ix]["NAME"],
							'id': this.oData[pointer]["BUTTONS"][ix]["ID"]
						},
						events: {
							'click': functionClick
						}
					});
		}
	}

	if (arButtons.length > 0)
	{
		BX.adjust(this.PARTS.BUTTONS, {
			children: arButtons
		});
	}
}

BX.CMessageDialog.prototype.adjustPaging = function(change_cnt, stop)
{
	if (change_cnt == null)
		change_cnt = true;
	if (stop == null)
		stop = false;

	if (this.PARTS.PAGING != null)
	{
		this.PARTS.PAGING.firstChild.nextSibling.innerHTML = this.oCurrentMessagePointer + 1;
		this.PARTS.PAGING.firstChild.nextSibling.nextSibling.nextSibling.innerHTML = this.oData.length;
	}

	this.change_cnt = change_cnt;
	this.stop = stop;

	if (this.oData.length <= 0)
	{
		var fxStart = GetTabOffset();
		var fxFinish = -26;

		(new BX.fx({
			time: 1.0,
			step: 0.01,
			type: 'accelerated',

			start: fxStart,
			finish: fxFinish,
			callback: BX.delegate(SetTabTop, this.Notifier),
			callback_complete: BX.delegate(function() 
			{
					this.DIV.style.display = 'none';
					this.RemovePaging();
					
					if (this.Notifier != null)
					{
						this.Notifier.parentNode.removeChild(this.Notifier);
						this.Notifier = null;
					}
					
					this.NotifierOnClick = false;

					BX.unbind(document, 'keyup', BX.proxy(this.__checkPrevNextKeyPress, this));
					BX.unbind(window, 'scroll', BX.proxy(MessageDialog.onScroll, MessageDialog));
					BX.removeCustomEvent(this, 'onWindowDragFinished', this.onDrop);

					bSetTitle = false;
					sonet_dynev_settitle();

					if (BX(BX.message('sonetDynevUnreadCntId')) != null && this.change_cnt === true)
						BX(BX.message('sonetDynevUnreadCntId')).innerHTML = "";

					clearTimeout(sonetDynevTout);
					if (this.stop !== true)
						sonetDynevTout = setTimeout("sonet_dynev_msgs_get();", 1);
			}, this)
		})).start();
	}
	else
	{
		this.Notifier.firstChild.nextSibling.firstChild.innerHTML = this.oData.length;
		if (BX(BX.message('sonetDynevUnreadCntId')) != null && this.change_cnt === true)
			BX(BX.message('sonetDynevUnreadCntId')).innerHTML = BX.message('sonetDynevUnreadCntStrBefore') + this.oData.length + BX.message('sonetDynevUnreadCntStrAfter');
	}
}

BX.CMessageDialog.prototype.ShowContent = function(pointer)
{
	var html = '';
	var anchor_id = '';

	if (this.oData[pointer] == null)
		return;
		
	if  (this.oData[pointer]["IS_LOG"] == "Y")
	{
		this.PARTS.PROFILE_INFO.style.display = "none";
		this.PARTS.PROFILE_AVATAR.style.display = "none";
		this.PARTS.PROFILE_INNER.style.height = "16px";
		this.PARTS.TEXT_INNER.style.height = "143px";
	}
	else
	{
		this.PARTS.PROFILE_INFO.style.display = "block";
		this.PARTS.PROFILE_AVATAR.style.display = "block";
		this.PARTS.PROFILE_INNER.style.height = null;
		this.PARTS.TEXT_INNER.style.height = null;
	}

	if (this.oData[pointer]["TYPE"] == "M" && this.oData[pointer]["MESSAGE_TYPE"] == "P")
		html = BX.message('sonetDynevDivTitleMP');
	else if (this.oData[pointer]["TYPE"] == "M" && this.oData[pointer]["MESSAGE_TYPE"] == "S")
		html = BX.message('sonetDynevDivTitleMS');
	else if (this.oData[pointer]["TYPE"] == "FR")
		html = BX.message('sonetDynevDivTitleFR');
	else if  (this.oData[pointer]["TYPE"] == "GR")
		html = BX.message('sonetDynevDivTitleGR');

	this.PARTS.TITLE.lastChild.innerHTML = html;

	if (this.oData[pointer]["TYPE"] == 'M')
		BX.removeClass(this.DIV, 'pm-messages-box-alert');
	else 
		BX.addClass(this.DIV, 'pm-messages-box-alert');

	if (this.oData[pointer]["CAN_VIEW_USER"] == "Y")
	{
		anchor_id = Math.floor(Math.random()*100000) + 1;
		html = '<a href="' + this.oData[pointer]["URL_USER"] + '" id="anchor_' + anchor_id + '">' + this.oData[pointer]["NAME_USER"] + '</a>';
	}
	else
		html = this.oData[pointer]["NAME_USER"];

	if (this.oData[pointer]["TYPE"] == 'GR')
		this.PARTS.TEXT_CONTENT.innerHTML = '<div class="pm-messages-box-text-content-author">' + BX.message('sonetDynevGrInv') + ': ' + html + '</div>';
	else 
	{
		this.PARTS.PROFILE_INFO.innerHTML = html;
		this.PARTS.TEXT_CONTENT.innerHTML = '';		
	}

	if (this.oData[pointer]["TYPE"] == 'GR')
	{
		if (this.oData[pointer]["CAN_VIEW_GROUP"] == "Y")
			html = '<a href="' + this.oData[pointer]["URL_GROUP"] + '">' + this.oData[pointer]["NAME_GROUP"] + '</a>';
		else
			html = this.oData[pointer]["NAME_GROUP"];	

			this.PARTS.PROFILE_INFO.innerHTML = html;
	}
	
	if (this.oData[pointer]["TYPE"] == 'GR')
	{
		if (this.oData[pointer]["CAN_VIEW_GROUP"] == "Y")
			html = '<a href="' + this.oData[pointer]["URL_GROUP"] + '"><img src="' + this.oData[pointer]["IMAGE_GROUP"] + '" border="0" width="42" height="42" title="' + this.oData[pointer]["NAME_GROUP"] + '"></a>';
		else
			html = '<img src="' + this.oData[pointer]["IMAGE_GROUP"] + '" border="0" width="42" height="42" title="' + this.oData[pointer]["NAME_GROUP"] + '">';	
	}
	else
	{
		if (this.oData[pointer]["CAN_VIEW_USER"] == "Y")
			html = '<a href="' + this.oData[pointer]["URL_USER"] + '"><img src="' + this.oData[pointer]["IMAGE_USER"] + '" border="0" width="42" height="42" title="' + this.oData[pointer]["NAME_USER_TITLE"] + '"></a>';
		else
			html = '<img src="' + this.oData[pointer]["IMAGE_USER"] + '" border="0" width="42" height="42" title="' + this.oData[pointer]["NAME_USER_TITLE"] + '">';
	}
	this.PARTS.PROFILE_AVATAR.innerHTML = html;
	
	var currentDate = new Date();
	var messageDate = new Date(this.oData[pointer]["DATE_TIMESTAMP"] * 1000);
	var iShowTS = currentDate.getTime();
	var iMessageTS = messageDate.getTime();
	var deltaTS = iShowTS - this.iGetTS;
	var messageNow = new Date(iMessageTS + deltaTS);

	if (this.oData[pointer]["DATE_DAY"] == "TODAY" && messageNow.getDate() == messageDate.getDate())
		this.PARTS.DATE.innerHTML = BX.message('sonetDynevDateToday') + this.oData[pointer]["DATE_TIME_FORMATTED"];
	else if (this.oData[pointer]["DATE_DAY"] == "TODAY" && deltaTS < (60*60*24*1000 + currentDate.getHours()*60*60*1000 + currentDate.getMinutes()*60*1000 + currentDate.getSeconds()*1000 + currentDate.getMilliseconds()))
		this.PARTS.DATE.innerHTML = BX.message('sonetDynevDateToday') + this.oData[pointer]["DATE_TIME_FORMATTED"];
	else if (this.oData[pointer]["DATE_DAY"] == "YESTERDAY" && messageNow.getDate() == messageDate.getDate())
		this.PARTS.DATE.innerHTML = BX.message('sonetDynevDateYesterday') + this.oData[pointer]["DATE_TIME_FORMATTED"];
	else
		this.PARTS.DATE.innerHTML = this.oData[pointer]["DATE_DATETIME_FORMATTED"];

	setTimeout(BX.delegate(function() {this.PARTS.TEXT_INNER.scrollTop = 0}, this), 10);
		
	this.PARTS.TEXT_CONTENT.innerHTML += this.oData[pointer]["MESSAGE"];

	if (BX.message('sonetDynevUseTooltip') == 'Y')
		BX.tooltip(this.oData[pointer]["ID_USER"], "anchor_" + anchor_id, BX.message('sonetDynevMULAjaxPage'));

	this.adjustButtons(this.oCurrentMessagePointer);
	this.adjustPaging();

	if (this.oData[pointer]["TYPE"] == "M")
		this.SetMessageRead(this.oData[pointer]["ID"], true);

	BX.unbindAll(this.PARTS.PAGING.firstChild);
	BX.unbindAll(this.PARTS.PAGING.lastChild);

	this.PARTS.PAGING.removeChild(this.PARTS.PAGING.firstChild);
	this.PARTS.PAGING.removeChild(this.PARTS.PAGING.lastChild);

	if (this.oData.length > 1)
	{
		this.PARTS.PAGING.style.display = 'block';
		this.PARTS.PAGING.insertBefore(BX.create('A', { props: { href: 'javascript:void(0);', className: 'pm-messages-box-larr', title: BX.message('sonetDynevPrev') } }), this.PARTS.PAGING.firstChild);
		this.PARTS.PAGING.appendChild(BX.create('A', { props: { href: 'javascript:void(0);', className: 'pm-messages-box-rarr', title: BX.message('sonetDynevNext') } }));

		BX.adjust(this.PARTS.PAGING.firstChild, {
			events: {
				'click': BX.delegate(this.OnPrev, this)
			}
		});

		BX.adjust(this.PARTS.PAGING.lastChild, {
			events: {
				'click': BX.delegate(this.OnNext, this)
			}
		});
	}
	else
	{
		this.PARTS.PAGING.insertBefore(BX.create('SPAN', { }), this.PARTS.PAGING.firstChild);
		this.PARTS.PAGING.appendChild(BX.create('SPAN', { }));
		this.PARTS.PAGING.style.display = 'none';
	}
}

BX.CMessageDialog.prototype.SetMessageRead = function(message_id, bRead)
{
	_this = this;
	
	if (sonetEventXmlHttpSet.readyState % 4)
		return;
	
	if (sonetEventsErrorDiv != null)
		sonetEventsErrorDiv.style.display = "none";

	if (bRead)
		action = 'read';
	else
		action = 'unread';

	params = 'EventType=Message&eventID=' + message_id + '&action=' + action;

	sonetEventXmlHttpSet.open(
		"get",
		BX.message('sonetDynevMsgSetPath') + "?" + BX.message('sonetDynevSessid')
			+ "&" + params
			+ "&r=" + Math.floor(Math.random() * 1000)
	);
	sonetEventXmlHttpSet.send(null);

	sonetEventXmlHttpSet.onreadystatechange = function()
	{
		if (sonetEventXmlHttpSet.readyState == 4 && sonetEventXmlHttpSet.status == 200)
		{
			if (sonetEventXmlHttpSet.responseText)
			{
				if (sonetEventsErrorDiv != null)
				{
					sonetEventsErrorDiv.style.display = "block";
					sonetEventsErrorDiv.innerHTML = sonetEventXmlHttpSet.responseText;
				}
				_this.CloseDialog(true);
			}
		}
	}
}

BX.CMessageDialog.prototype.CloseTab = function()
{
	var _this = this;

	ts = parseInt(this.lastMessageTs) + 1;

	if (sonetEventXmlHttpSet.readyState % 4)
		return;

	if (sonetEventsErrorDiv != null)
		sonetEventsErrorDiv.style.display = "none";

	action = 'setts';
	params = 'EventType=Message&ts=' + ts + '&action=' + action;

	sonetEventXmlHttpSet.open(
		"get",
		BX.message('sonetDynevMsgSetPath') + "?" + BX.message('sonetDynevSessid')
			+ "&" + params
			+ "&r=" + Math.floor(Math.random() * 1000)
	);
	sonetEventXmlHttpSet.send(null);

	sonetEventXmlHttpSet.onreadystatechange = function()
	{
		if (sonetEventXmlHttpSet.readyState == 4 && sonetEventXmlHttpSet.status == 200)
		{
			if (sonetEventXmlHttpSet.responseText && sonetEventsErrorDiv != null)
			{
				sonetEventsErrorDiv.style.display = "block";
				sonetEventsErrorDiv.innerHTML = sonetEventXmlHttpSet.responseText;
			}
			else
			{
				_this.oData = [];
				_this.adjustPaging(false);
			}
		}
	}
}

BX.CMessageDialog.prototype.Close = 
BX.CMessageDialog.prototype.CloseDialog = function(stop)
{
	if (stop == null)
		stop = false;

	this.oData = [];
	this.getCounter = 0;
	this.adjustPaging(true, stop);
	BX.unbind(window, 'scroll', BX.proxy(MessageDialog.onScroll, MessageDialog));
	BX.removeCustomEvent(this, 'onWindowDragFinished', this.onDrop);
	
	if (BX(BX.message('sonetDynevUnreadCntId')) != null)
		BX(BX.message('sonetDynevUnreadCntId')).innerHTML = "";
}

BX.CMessageDialog.prototype.__checkPrevNextKeyPress = function(e)
{
	if (e == null)
		e = window.event;

	if (e.keyCode == 37)
		this.Prev();
	else if (e.keyCode == 39)
		this.Next();
	else if (e.keyCode == 27)
		this.CloseDialog();
}

BX.CMessageDialog.prototype.onScroll = function()
{
	if (this.Notifier != null)
		this.Notifier.style.top = GetTabOffset() + 'px';
		
	if (this.DIV != null)
	{
		if (this.bPosFromOption)
		{
			var windowScroll = BX.GetWindowScrollPos();
			this.DIV.style.top = (windowScroll.scrollTop + this.PosFromOptionTop) + 'px';
		}
		else
			this.DIV.style.top = (GetTabOffset() + 50) + 'px';
	}
}

BX.CMessageDialog.prototype.onDrop = function()
{
	_this = this;
	
	if (sonetEventXmlHttpSet.readyState % 4)
		return;
	
	if (sonetEventsErrorDiv != null)
		sonetEventsErrorDiv.style.display = "none";

	if (this.DIV == null || this.DIV.style.top == null || this.DIV.style.left == null)
		return;

	action = 'setpos';

	if ((BX.admin == null || BX.admin.panel.DIV == null) && !BX.browser.IsIE())
	{
		var left = parseInt(this.DIV.style.left);
		var top = parseInt(this.DIV.style.top);	
	}
	else
	{
		var windowScroll = BX.GetWindowScrollPos();
		var left = parseInt(this.DIV.style.left) - windowScroll.scrollLeft;
		var top = parseInt(this.DIV.style.top) - windowScroll.scrollTop;
	}

	this.bPosFromOption = true;
	this.PosFromOptionLeft = left;
	this.PosFromOptionTop = top;

	params = 'EventType=Dialog&action=' + action + '&left=' + left + '&top=' + top;

	sonetEventXmlHttpSet.open(
		"get",
		BX.message('sonetDynevMsgSetPath') + "?" + BX.message('sonetDynevSessid')
			+ "&" + params
			+ "&r=" + Math.floor(Math.random() * 1000)
	);
	sonetEventXmlHttpSet.send(null);

	sonetEventXmlHttpSet.onreadystatechange = function()
	{
		if (sonetEventXmlHttpSet.readyState == 4 && sonetEventXmlHttpSet.status == 200)
		{
			if (sonetEventXmlHttpSet.responseText)
			{
				if (sonetEventsErrorDiv != null)
				{
					sonetEventsErrorDiv.style.display = "block";
					sonetEventsErrorDiv.innerHTML = sonetEventXmlHttpSet.responseText;
				}
				_this.CloseDialog(true);
			}
		}
	}
}

SetTabTop = function(top) 
{
	if (this != null && this.style != null)
		this.style.top = top + 'px';
}

GetTabOffset = function() {

	if ((BX.admin == null || BX.admin.panel.DIV == null) && !BX.browser.IsIE())
		return 0;

	var wndScroll = BX.GetWindowScrollPos();

	if (BX.admin != null && BX.admin.panel.DIV != null)
	{
		if (BX.admin.panel.isFixed())
			return wndScroll.scrollTop + BX.admin.panel.DIV.offsetHeight;
		else
			return Math.max(wndScroll.scrollTop, BX.admin.panel.DIV.offsetHeight);
	}
	else 
		return wndScroll.scrollTop;

}

var sonetEventsErrorDiv;
var sonetDynevTout;
var sonetDynevTitleTout;
var sonetDynevOldTitle = "";
var bSetTitle = false;

var panel_height = 0;

var MessageDialog = new BX.CMessageDialog({
	height: 245,
	width: 540
});


if (!window.XMLHttpRequest)
{
	var XMLHttpRequest = function()
	{
		try { return new ActiveXObject("MSXML3.XMLHTTP") } catch(e) {}
		try { return new ActiveXObject("MSXML2.XMLHTTP.3.0") } catch(e) {}
		try { return new ActiveXObject("MSXML2.XMLHTTP") } catch(e) {}
		try { return new ActiveXObject("Microsoft.XMLHTTP") } catch(e) {}
	}
}

var sonetEventXmlHttpGet = new XMLHttpRequest();
var sonetEventXmlHttpSet = new XMLHttpRequest();

jsUtils.addEvent(window, "load", sonet_dynev_onload);

function sonet_dynev_msgs_set(params)
{
	if (sonetEventXmlHttpSet.readyState % 4)
		return;

	if (sonetEventsErrorDiv != null)
		sonetEventsErrorDiv.style.display = "none";

	sonetEventXmlHttpSet.open(
		"get",
		BX.message('sonetDynevMsgSetPath') + "?" + BX.message('sonetDynevSessid')
			+ "&" + params
			+ "&uas=" + BX.message('sonetDynevUseAutoSubscribe')
			+ "&r=" + Math.floor(Math.random() * 1000)
	);
	sonetEventXmlHttpSet.send(null);

	sonetEventXmlHttpSet.onreadystatechange = function()
	{
		if (sonetEventXmlHttpSet.readyState == 4 && sonetEventXmlHttpSet.status == 200)
		{
			if (sonetEventXmlHttpSet.responseText)
			{
				if (sonetEventsErrorDiv != null)
				{
					sonetEventsErrorDiv.style.display = "block";
					sonetEventsErrorDiv.innerHTML = sonetEventXmlHttpSet.responseText;
				}
				MessageDialog.CloseDialog(true);
			}
			else
			{
				MessageDialog.Next();
				MessageDialog.oData.splice(MessageDialog.oCurrentMessagePointer-1, 1);
				if (MessageDialog.oCurrentMessagePointer > 0)
					MessageDialog.oCurrentMessagePointer--;
				MessageDialog.adjustPaging();
				MessageDialog.ShowContent(MessageDialog.oCurrentMessagePointer);			
			}
		}
	}
}

function sonet_dynev_onload()
{

	if (BX.admin != null && BX.admin.panel.DIV != null)
		panel_height = BX.admin.panel.DIV.offsetHeight;
	else
		panel_height = 0;

	BX.addCustomEvent('onTopPanelCollapse', BX.delegate(MessageDialog.onScroll, MessageDialog));
	BX.addCustomEvent('onTopPanelFix', BX.delegate(MessageDialog.onScroll, MessageDialog));

	sonetEventsErrorDiv = document.getElementById('sonet_events_err');
	sonet_dynev_reset();
}

function sonet_dynev_reset()
{
	clearTimeout(sonetDynevTout);
	sonetEventXmlHttpGet.abort();
	bSetTitle = false;
	sonet_dynev_settitle();
	sonetDynevTout = setTimeout("sonet_dynev_msgs_get();", 1);
}

function sonet_dynev_parse(str)
{
	str = str.replace(/^\s+|\s+$/g, '');
	while (str.length > 0 && str.charCodeAt(0) == 65279)
		str = str.substring(1);

	if (str.length <= 0)
		return false;
	
	if (str.substring(0, 1) != '{' && str.substring(0, 1) != '[' && str.substring(0, 1) != '*')
		str = '"*"';
		
	eval("arData = " + str);

	return arData;
}

function sonet_dynev_msgs_get()
{
	if (BX.message('sonetDynevUserId') <= 0)
		return;
	clearTimeout(sonetDynevTout);
	sonetDynevTout = setTimeout("sonet_dynev_msgs_get();", Math.round(1000 * BX.message('sonetDynevTimeout')));
	if (sonetEventXmlHttpGet.readyState % 4)
		return;

	sonetEventXmlHttpGet.open(
		"get",
		BX.message('sonetDynevMsgGetPath') + "?"
		+ "&cuid=" + BX.message('sonetDynevUserId')
		+ "&site=" + BX.message('sonetDynevSiteId')
		+ "&up=" + BX.util.urlencode(BX.message('sonetDynevPath2User'))
		+ "&gp=" + BX.util.urlencode(BX.message('sonetDynevPath2Group'))
		+ "&mpm=" + BX.util.urlencode(BX.message('sonetDynevPath2MessageMess'))
		+ "&nt=" + BX.util.urlencode(BX.message('sonetDynevUserNameTemplate'))
		+ "&sl=" + BX.message('sonetDynevUserShowLogin')
		+ "&log=Y"
		+ "&r=" + Math.floor(Math.random() * 1000)
	);
	sonetEventXmlHttpGet.send(null);

	sonetEventXmlHttpGet.onreadystatechange = function()
	{
		if (sonetEventXmlHttpGet.readyState == 4 && sonetEventXmlHttpGet.status == 200)
		{
			var data = sonet_dynev_parse(sonetEventXmlHttpGet.responseText);

			if (typeof(data) == "object" && data.length > 0)
			{
				if (data[0] == '*')
				{
					if (sonetEventsErrorDiv != null)
					{
						sonetEventsErrorDiv.style.display = "block";
						sonetEventsErrorDiv.innerHTML = sonetEventXmlHttpSet.responseText;
					}
					MessageDialog.CloseDialog(true);
					clearTimeout(sonetDynevTout);
					return;
				}
				
				sonetEventXmlHttpGet.abort();

				BX.ready(function(){
					MessageDialog.DIV.style.display = 'none';
					MessageDialog.GetContent(data);

					if (MessageDialog.oData.length <= 0)
						return;

					if (MessageDialog.Notifier != null && !MessageDialog.NotifierOnClick)
					{
						BX.adjust(MessageDialog.Notifier.firstChild.nextSibling, {
							events: {
								'click': function() { MessageDialog.ShowDialog(); }
							}
						});
						MessageDialog.NotifierOnClick = true;
					}
					if (MessageDialog.oData[0]["TYPE"] != null)
						bSetTitle = true;
				});

				sonet_dynev_settitle();
			}
			else
			{
				if (MessageDialog != null)
					MessageDialog.CloseDialog(true);
				sonetDynevTout = setTimeout("sonet_dynev_msgs_get();", Math.round(1000 * BX.message('sonetDynevTimeout')));
			}
		}
	}
}

var bbb = true;
function sonet_dynev_settitle()
{
	if (bSetTitle)
	{
		if (sonetDynevOldTitle.length <= 0)
			sonetDynevOldTitle = document.title;

		if (!bbb)
			document.title = "* " + sonetDynevOldTitle;
		else
			document.title = sonetDynevOldTitle;
			
		bbb = !bbb;
		clearTimeout(sonetDynevTitleTout);
		sonetDynevTitleTout = setTimeout("sonet_dynev_settitle()", 1000);
	}
	else
	{
		if (sonetDynevOldTitle.length > 0 && document.title != sonetDynevOldTitle)
			document.title = sonetDynevOldTitle;
		sonetDynevOldTitle = "";
	}
}