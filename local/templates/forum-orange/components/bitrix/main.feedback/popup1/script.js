(function(window) {

if (BX.PopupWindowManager)
	return;

BX.PopupWindowManager =
{
	_popups : [],
	_currentPopup : null,

	create : function(uniquePopupId, bindElement, params)
	{
		var index = -1;
		if ( (index = this._getPopupIndex(uniquePopupId)) !== -1)
			return this._popups[index];

		var popupWindow = new BX.PopupWindow(uniquePopupId, bindElement, params);

		BX.addCustomEvent(popupWindow, "onPopupShow", BX.delegate(this.onPopupShow, this));
		BX.addCustomEvent(popupWindow, "onPopupClose", BX.delegate(this.onPopupClose, this));
		BX.addCustomEvent(popupWindow, "onPopupDestroy", BX.delegate(this.onPopupDestroy, this));

		if (params && params.events)
		{
			for (var eventName in params.events)
				BX.addCustomEvent(popupWindow, eventName, params.events[eventName]);
		}

		this._popups.push(popupWindow);

		return popupWindow;
	},

	onPopupShow : function(popupWindow)
	{
		if (this._currentPopup !== null)
			this._currentPopup.close();

		this._currentPopup = popupWindow;
	},

	onPopupClose : function(popupWindow)
	{
		this._currentPopup = null;
	},

	onPopupDestroy : function(popupWindow)
	{
		var index = -1;
		if ( (index = this._getPopupIndex(popupWindow.uniquePopupId)) !== -1)
			this._popups = BX.util.deleteFromArray(this._popups, index);
	},

	getCurrentPopup : function()
	{
		return this._currentPopup;
	},

	isPopupExists : function(uniquePopupId)
	{
		return this._getPopupIndex(uniquePopupId) !== -1
	},

	_getPopupIndex : function(uniquePopupId)
	{
		var index = -1;

		for (var i = 0; i < this._popups.length; i++)
			if (this._popups[i].uniquePopupId == uniquePopupId)
				return i;

		return index;
	}
};

BX.PopupWindow = function(uniquePopupId, bindElement, params)
{
	this.uniquePopupId = uniquePopupId;
	this.params = params || {};
	this.params.zIndex = parseInt(this.params.zIndex);
	this.params.zIndex = isNaN(this.params.zIndex) ? 0 : this.params.zIndex;
	this.buttons = this.params.buttons && BX.type.isArray(this.params.buttons) ? this.params.buttons : [];
	this.offsetTop = this.offsetLeft = 0;
	this.firstShow = false;
	this.bordersWidth = 20;
	this.bindElementPos = null;
	this.closeIcon = null;
	this.angle = null;
	this.titleBar = null;
	this.bindOptions = typeof(this.params.bindOptions) == "object" ? this.params.bindOptions : {};
    this.isAutoHideBinded = false;

	if (this.params.events)
	{
		for (var eventName in this.params.events)
			BX.addCustomEvent(this, eventName, this.params.events[eventName]);
	}

	this.popupContainer = document.createElement("DIV");

	BX.adjust(this.popupContainer, {
		props : {
			id : uniquePopupId
		},
		style : {
			zIndex: 0,
			position: "absolute",
			display: "none",
			top: "0px",
			left: "0px"
		}
	});

	var tableClassName = "popup-window";
	if (params.lightShadow)
		tableClassName += " popup-window-light";
	if (params.titleBar)
		tableClassName += " popup-window-titlebar";
	if (params.className && BX.type.isNotEmptyString(params.className))
		tableClassName += " " + params.className;

	this.popupContainer.innerHTML = ['<table class="', tableClassName,'" cellspacing="0"> \
		<tr class="popup-window-top-row"> \
			<td class="popup-window-left-column"></td> \
			<td class="popup-window-center-column">', (params.titleBar ? '<div class="popup-window-titlebar" id="popup-window-titlebar-' + uniquePopupId + '"></div>' : ""),'</td> \
			<td class="popup-window-right-column"></td> \
		</tr> \
		<tr class="popup-window-content-row"> \
			<td class="popup-window-left-column"></td> \
			<td class="popup-window-center-column"><div class="popup-window-content" id="popup-window-content-', uniquePopupId ,'"> \
			</div></td> \
			<td class="popup-window-right-column"></td> \
		</tr> \
		<tr class="popup-window-bottom-row"> \
			<td class="popup-window-left-column"></td> \
			<td class="popup-window-center-column"></td> \
			<td class="popup-window-right-column"></td> \
		</tr> \
	</table>'].join("");
	document.body.appendChild(this.popupContainer);

	if (params.closeIcon)
	{
		this.popupContainer.appendChild(
			(this.closeIcon = BX.create("span", {
				props : { className: "popup-window-close-icon"},
				style : (typeof(params.closeIcon) == "object" ? params.closeIcon : {} ),
				events : { click : BX.proxy(this.close, this) } } )
			)
		);
	}

	this.contentContainer = BX("popup-window-content-" +  uniquePopupId);
	this.titleBar = BX("popup-window-titlebar-" +  uniquePopupId);
	this.buttonsContainer = this.buttonsHr = null;

	if (params.angle)
		this.setAngle(params.angle);
	this.setOffset(this.params);
	this.setBindElement(bindElement);
	this.setTitleBar(this.params.titleBar);
	this.setContent(this.params.content);
	this.setButtons(this.params.buttons);

	BX.bind(window, "resize", BX.delegate(this._onResizeWindow, this));
};

BX.PopupWindow.prototype.setContent = function(content)
{
	if (!this.contentContainer || !content)
		return;

	if (BX.type.isElementNode(content))
	{
        BX.cleanNode(this.contentContainer);
		this.contentContainer.appendChild(content.parentNode ? content.parentNode.removeChild(content) : content );
		content.style.display = "block";
	}
	else if (BX.type.isString(content))
	{
		this.contentContainer.innerHTML = content;
	}
	else
		this.contentContainer.innerHTML = "&nbsp;";

};

BX.PopupWindow.prototype.setButtons = function(buttons)
{
	this.buttons = buttons && BX.type.isArray(buttons) ? buttons : [];

	if (this.buttonsHr)
		BX.remove(this.buttonsHr);
	if (this.buttonsContainer)
		BX.remove(this.buttonsContainer);

	if (this.buttons.length > 0 && this.contentContainer)
	{
		var newButtons = [];
		for (var i = 0; i < this.buttons.length; i++)
		{
			var button = this.buttons[i];
			if (button == null || !BX.is_subclass_of(button, BX.PopupWindowButton))
				continue;

			button.popupWindow = this;
			newButtons.push(button.render());
		}

		this.buttonsHr = this.contentContainer.parentNode.appendChild(
			BX.create("div",{
				props : { className : "popup-window-hr popup-window-buttons-hr" },
				children : [ BX.create("i", {}) ]
			})
		);

		this.buttonsContainer = this.contentContainer.parentNode.appendChild(
			BX.create("div",{
				props : { className : "popup-window-buttons" },
				children : newButtons
			})
		);
	}
};

BX.PopupWindow.prototype.setBindElement = function(bindElement)
{
    if (!bindElement || typeof(bindElement) != "object")
        return;

	if (
        BX.type.isDomNode(bindElement) ||
        (BX.type.isNumber(bindElement.clientX) && BX.type.isNumber(bindElement.clientY)) ||
        (BX.type.isNumber(bindElement.top) && BX.type.isNumber(bindElement.left))
    )
		this.bindElement = bindElement;
};

BX.PopupWindow.prototype.getBindElementPos = function(bindElement)
{
    if (BX.type.isDomNode(bindElement))
        return BX.pos(bindElement, false);
    else if (bindElement && BX.type.isNumber(bindElement.clientX) && BX.type.isNumber(bindElement.clientY))
    {
        BX.fixEventPageXY(bindElement);
        return { left : bindElement.pageX, top : bindElement.pageY, bottom : bindElement.pageY};
    }
    else if(bindElement && typeof(bindElement) == "object")
    {
        if (!BX.type.isNumber(bindElement.bottom))
            bindElement.bottom = bindElement.top;
        return bindElement;
    }
    else
    {
        var windowSize =  BX.GetWindowInnerSize();
       	var windowScroll = BX.GetWindowScrollPos();
       	var popupWidth = this.popupContainer.offsetWidth;
       	var popupHeight = this.popupContainer.offsetHeight;

        return {
            left : windowSize.innerWidth/2 - popupWidth/2 + windowScroll.scrollLeft,
            top : windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop,
            bottom : windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop,

            //for optimisation purposes
            windowSize : windowSize,
            windowScroll : windowScroll,
            popupWidth : popupWidth,
            popupHeight : popupHeight
        };
    }
};

BX.PopupWindow.prototype.setAngle = function(params)
{
	var className = this.params.lightShadow ? "popup-window-light-angly" : "popup-window-angly";
	if (this.angle == null)
	{
		this.angle = {
			element : BX.create("div", { props : { className: className + " " + className +"-top" }}),
			position : "top",
			offset : 0
		};
		this.popupContainer.appendChild(this.angle.element);
	}

	if (typeof(params) == "object" && params.position && BX.util.in_array(params.position, ["top", "right", "bottom", "left", "hide"]))
	{
		BX.removeClass(this.angle.element, className + "-" +  this.angle.position);
		BX.addClass(this.angle.element, className + "-" +  params.position);
		this.angle.position = params.position;
	}

	if (typeof(params) == "object" && BX.type.isNumber(params.offset))
	{
		if (this.angle.position == "top")
		{
			this.angle.element.style.left = (this.angle.offset = Math.max(7, params.offset)) + "px";
			this.angle.element.style.marginLeft = "auto";
		}
		else if (this.angle.position == "right")
			this.angle.element.style.top = (this.angle.offset = Math.max(2, params.offset)) + "px";
		else if (this.angle.position == "bottom")
		{
			this.angle.element.style.marginLeft = (this.angle.offset = Math.max(7, params.offset)) + "px";
			this.angle.element.style.left = "auto";
		}
		else if (this.angle.position == "left")
			this.angle.element.style.top = (this.angle.offset = Math.max(2, params.offset)) + "px";
	}
};

BX.PopupWindow.prototype.setOffset = function(params)
{

	if (typeof(params) != "object")
		return;

	if (params.offsetLeft && BX.type.isNumber(params.offsetLeft))
		this.offsetLeft = params.offsetLeft;

	if (params.offsetTop && BX.type.isNumber(params.offsetTop))
		this.offsetTop = params.offsetTop;
};

BX.PopupWindow.prototype.setTitleBar = function(params)
{
	if (!this.titleBar || typeof(params) != "object" || !BX.type.isDomNode(params.content))
		return;

	this.titleBar.innerHTML = "";
	this.titleBar.appendChild(params.content);
};

BX.PopupWindow.prototype.show = function()
{
	if (!this.firstShow)
	{
		BX.onCustomEvent(this, "onPopupFirstShow", [this]);
		this.firstShow = true;
	}
	BX.onCustomEvent(this, "onPopupShow", [this]);

	this.popupContainer.style.display = "block";

	this.adjustPosition();

	BX.onCustomEvent(this, "onAfterPopupShow", [this]);

	if (this.params.autoHide && !this.isAutoHideBinded)
	{
		setTimeout(
			BX.proxy(function() {
                this.isAutoHideBinded = true;
				BX.bind(this.popupContainer, "click", this.cancelBubble);
				BX.bind(document, "click", BX.proxy(this.close, this));
			}, this), 0
		);

	}
};

BX.PopupWindow.prototype.isShown = function()
{
   return this.popupContainer.style.display == "block";
};

BX.PopupWindow.prototype.cancelBubble = function(event)
{
	if(!event)
		event = window.event;

	if (event.stopPropagation)
		event.stopPropagation();
	else
		event.cancelBubble = true;
};

BX.PopupWindow.prototype.close = function(event)
{
    if (!this.isShown())
        return;

	BX.onCustomEvent(this, "onPopupClose", [this, event]);
	this.popupContainer.style.display = "none";
	setTimeout(BX.proxy(this._close, this), 0);
};

BX.PopupWindow.prototype._close = function()
{
	if (this.params.autoHide && this.isAutoHideBinded)
	{
        this.isAutoHideBinded = false;
		BX.unbind(this.popupContainer, "click", this.cancelBubble);
		BX.unbind(document, "click", BX.proxy(this.close, this));
	}
};

BX.PopupWindow.prototype.destroy = function()
{
	BX.onCustomEvent(this, "onPopupDestroy", [this]);
	BX.unbindAll(this);
	BX.remove(this.popupContainer);
};

BX.PopupWindow.prototype.adjustPosition = function(bindOptions)
{
	if (bindOptions && typeof(bindOptions) == "object")

		this.bindOptions = bindOptions;

	var bindElementPos = this.getBindElementPos(this.bindElement);

	if (!this.bindOptions.forceBindPosition && this.bindElementPos != null &&
         bindElementPos.top == this.bindElementPos.top &&
         bindElementPos.left == this.bindElementPos.left
    )
		return;

	this.bindElementPos = bindElementPos;

	var windowSize = bindElementPos.windowSize ? bindElementPos.windowSize : BX.GetWindowInnerSize();
	var windowScroll = bindElementPos.windowScroll ? bindElementPos.windowScroll : BX.GetWindowScrollPos();
	var popupWidth = bindElementPos.popupWidth ? bindElementPos.popupWidth : this.popupContainer.offsetWidth;
	var popupHeight = bindElementPos.popupHeight ? bindElementPos.popupHeight : this.popupContainer.offsetHeight;

	var angleMinLeft = 7;
	var angleTopOffset = 5;
	var angleLeftOffset = 15;

	var top = this.bindElementPos.bottom + this.offsetTop + (this.angle != null && this.angle.position == "top" ? angleTopOffset : 0);
	var left = this.bindElementPos.left + this.offsetLeft - (this.angle != null && BX.util.in_array(this.angle.position, ["top", "bottom"]) ? angleLeftOffset : 0);


	if ( !this.bindOptions.forceLeft &&
		(left + popupWidth) >= (windowSize.innerWidth + windowScroll.scrollLeft) &&
		(windowSize.innerWidth + windowScroll.scrollLeft - popupWidth - this.bordersWidth) > 0)
	{
			var bindLeft = left;
			left = windowSize.innerWidth + windowScroll.scrollLeft - popupWidth - this.bordersWidth;
			if (this.angle != null && BX.util.in_array(this.angle.position, ["top", "bottom"]))
				this.setAngle({ offset : bindLeft - left + angleMinLeft });
	}
	else if (this.angle != null && BX.util.in_array(this.angle.position, ["top", "bottom"]))
		this.setAngle({ offset : 0 });

	if (left < 0)
		left = 0;

	if ( !this.bindOptions.forceTop && (top + popupHeight) > (windowSize.innerHeight + windowScroll.scrollTop) && (this.bindElementPos.top - popupHeight) >= 0)
	{
		top =  this.bindElementPos.top - popupHeight;
		if (this.angle != null)
		{
			top -= angleTopOffset;
			this.setAngle({ position: "bottom"});
		}
	}
	else if (this.angle != null && this.angle.position == "bottom")
	{
		top += angleTopOffset;
		this.setAngle({ position: "top"});
	}

	if (top < 0)
		top = 0;

	BX.adjust(this.popupContainer, {
		style: {
			top: top + "px",
			left: left + "px",
			zIndex: 1000 + this.params.zIndex
		}
	});

};

BX.PopupWindow.prototype._onResizeWindow = function(event)
{
	this.adjustPosition();
};

/*========================================Buttons===========================================*/

BX.PopupWindowButton = function(params)
{
	this.popupWindow = null;

	this.params = params || {};

	this.text = this.params.text || "";
	this.id = this.params.id || "";
	this.className = this.params.className || "";
	this.events = this.params.events || {};

	this.contextEvents = {};
	for (var eventName in this.events)
		this.contextEvents[eventName] = BX.proxy(this.events[eventName], this);

	this.nameNode = BX.create("span", { props : { className : "popup-window-button-text"}, text : this.text } );
	this.buttonNode = BX.create(
		"span",
		{
			props : { className : "popup-window-button" + (this.className.length > 0 ? " " + this.className : ""), id : this.id },
			children : [
				BX.create("span", { props : { className : "popup-window-button-left"} } ),
				this.nameNode,
				BX.create("span", { props : { className : "popup-window-button-right"} } )
			],
			events : this.contextEvents
		}
	);
};

BX.PopupWindowButton.prototype.render = function()
{
	return this.buttonNode;
};

BX.PopupWindowButton.prototype.setName = function(name)
{
	this.text = name || "";
	if (this.nameNode)
	{
		BX.cleanNode(this.nameNode);
		BX.adjust(this.nameNode, { text : this.text} );
	}
};

BX.PopupWindowButton.prototype.setClassName = function(className)
{
	if (this.buttonNode)
	{
		BX.removeClass(this.buttonNode, this.className);
		BX.addClass(this.buttonNode, className)
	}

	this.className = className;
};

BX.PopupWindowButtonLink = function(params)
{
	BX.PopupWindowButtonLink.superclass.constructor.apply(this, arguments);

	this.nameNode = BX.create("span", { props : { className : "popup-window-button-link-text" }, text : this.text, events : this.contextEvents });
	this.buttonNode = BX.create(
		"span",
		{
			props : { className : "popup-window-button popup-window-button-link" + (this.className.length > 0 ? " " + this.className : ""), id : this.id },
			children : [this.nameNode]
		}
	);

};

BX.extend(BX.PopupWindowButtonLink, BX.PopupWindowButton);

})(window);