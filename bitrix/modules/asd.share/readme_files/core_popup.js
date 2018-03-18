;(function(window) {

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
	BX.onCustomEvent("onPopupWindowInit", [uniquePopupId, bindElement, params ]);

	this.uniquePopupId = uniquePopupId;
	this.params = params || {};
	this.params.zIndex = parseInt(this.params.zIndex);
	this.params.zIndex = isNaN(this.params.zIndex) ? 0 : this.params.zIndex;
	this.buttons = this.params.buttons && BX.type.isArray(this.params.buttons) ? this.params.buttons : [];
	this.offsetTop = BX.PopupWindow.getOption("offsetTop");
	this.offsetLeft = BX.PopupWindow.getOption("offsetLeft");
	this.firstShow = false;
	this.bordersWidth = 20;
	this.bindElementPos = null;
	this.closeIcon = null;
	this.angle = null;
	this.overlay = null;
	this.titleBar = null;
	this.bindOptions = typeof(this.params.bindOptions) == "object" ? this.params.bindOptions : {};
	this.isAutoHideBinded = false;
	this.closeByEsc = !!this.params.closeByEsc;
	this.isCloseByEscBinded = false;

	this.dragged = false;
	this.dragPageX = 0;
	this.dragPageY = 0;

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
			zIndex: this.getZindex(),
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
		tableClassName += params.lightShadow ? " popup-window-titlebar-light" : " popup-window-titlebar";
	if (params.className && BX.type.isNotEmptyString(params.className))
		tableClassName += " " + params.className;

	this.popupContainer.innerHTML = ['<table class="', tableClassName,'" cellspacing="0"> \
		<tr class="popup-window-top-row"> \
			<td class="popup-window-left-column"><div class="popup-window-left-spacer"></div></td> \
			<td class="popup-window-center-column">', (params.titleBar ? '<div class="popup-window-titlebar" id="popup-window-titlebar-' + uniquePopupId + '"></div>' : ""),'</td> \
			<td class="popup-window-right-column"><div class="popup-window-right-spacer"></div></td> \
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
			(this.closeIcon = BX.create("a", {
				props : { className: "popup-window-close-icon" + (params.titleBar ? " popup-window-titlebar-close-icon" : ""), href : ""},
				style : (typeof(params.closeIcon) == "object" ? params.closeIcon : {} ),
				events : { click : BX.proxy(this._onCloseIconClick, this) } } )
			)
		);

		if (BX.browser.IsIE())
			BX.adjust(this.closeIcon, { attrs: { hidefocus: "true" } });
	}

	this.contentContainer = BX("popup-window-content-" +  uniquePopupId);
	this.titleBar = BX("popup-window-titlebar-" +  uniquePopupId);
	this.buttonsContainer = this.buttonsHr = null;

	if (params.angle)
		this.setAngle(params.angle);

	if (params.overlay)
		this.setOverlay(params.overlay);

	this.setOffset(this.params);
	this.setBindElement(bindElement);
	this.setTitleBar(this.params.titleBar);
	this.setContent(this.params.content);
	this.setButtons(this.params.buttons);

	if (this.params.bindOnResize !== false)
	{
		BX.bind(window, "resize", BX.proxy(this._onResizeWindow, this));
	}
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

	if (BX.type.isDomNode(bindElement) || (BX.type.isNumber(bindElement.top) && BX.type.isNumber(bindElement.left)))
		this.bindElement = bindElement;
	else if (BX.type.isNumber(bindElement.clientX) && BX.type.isNumber(bindElement.clientY))
	{
		BX.fixEventPageXY(bindElement);
		this.bindElement = { left : bindElement.pageX, top : bindElement.pageY, bottom : bindElement.pageY };
	}
};

BX.PopupWindow.prototype.getBindElementPos = function(bindElement)
{
	if (BX.type.isDomNode(bindElement))
	{
		return BX.pos(bindElement, false);
	}
	else if (bindElement && typeof(bindElement) == "object")
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
		var position = this.bindOptions.position && this.bindOptions.position == "top" ? "bottom" : "top";
		var angleMinLeft = BX.PopupWindow.getOption(position == "top" ? "angleMinTop" : "angleMinBottom");
		var defaultOffset = BX.type.isNumber(params.offset) ? params.offset : 0;

		var angleLeftOffset = BX.PopupWindow.getOption("angleLeftOffset", null);
		if (defaultOffset > 0 && BX.type.isNumber(angleLeftOffset))
			defaultOffset += angleLeftOffset - BX.PopupWindow.defaultOptions.angleLeftOffset;

		this.angle = {
			element : BX.create("div", { props : { className: className + " " + className +"-" + position }}),
			position : position,
			offset : 0,
			defaultOffset : Math.max(defaultOffset, angleMinLeft)
			//Math.max(BX.type.isNumber(params.offset) ? params.offset : 0, angleMinLeft)
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
		var offset = params.offset;
		var minOffset, maxOffset;
		if (this.angle.position == "top")
		{
			minOffset = BX.PopupWindow.getOption("angleMinTop");
			maxOffset = this.popupContainer.offsetWidth - BX.PopupWindow.getOption("angleMaxTop");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.left = this.angle.offset + "px";
			this.angle.element.style.marginLeft = "auto";
		}
		else if (this.angle.position == "bottom")
		{
			minOffset = BX.PopupWindow.getOption("angleMinBottom");
			maxOffset = this.popupContainer.offsetWidth - BX.PopupWindow.getOption("angleMaxBottom");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.marginLeft = this.angle.offset + "px";
			this.angle.element.style.left = "auto";
		}
		else if (this.angle.position == "right")
		{
			minOffset = BX.PopupWindow.getOption("angleMinRight");
			maxOffset = this.popupContainer.offsetHeight - BX.PopupWindow.getOption("angleMaxRight");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.top = this.angle.offset + "px";
		}
		else if (this.angle.position == "left")
		{
			minOffset = BX.PopupWindow.getOption("angleMinLeft");
			maxOffset = this.popupContainer.offsetHeight - BX.PopupWindow.getOption("angleMaxLeft");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.top = this.angle.offset + "px";
		}
	}
};

BX.PopupWindow.prototype.isTopAngle = function()
{
	return this.angle != null && this.angle.position == "top";
};

BX.PopupWindow.prototype.isBottomAngle = function()
{
	return this.angle != null && this.angle.position == "bottom";
};

BX.PopupWindow.prototype.isTopOrBottomAngle = function()
{
	return this.angle != null && BX.util.in_array(this.angle.position, ["top", "bottom"]);
};

BX.PopupWindow.prototype.getAngleHeight = function()
{
	return (this.isTopOrBottomAngle() ? BX.PopupWindow.getOption("angleTopOffset") : 0);
};

BX.PopupWindow.prototype.setOffset = function(params)
{
	if (typeof(params) != "object")
		return;

	if (params.offsetLeft && BX.type.isNumber(params.offsetLeft))
		this.offsetLeft = params.offsetLeft + BX.PopupWindow.getOption("offsetLeft");

	if (params.offsetTop && BX.type.isNumber(params.offsetTop))
		this.offsetTop = params.offsetTop + BX.PopupWindow.getOption("offsetTop");
};

BX.PopupWindow.prototype.setTitleBar = function(params)
{
	if (!this.titleBar || typeof(params) != "object" || !BX.type.isDomNode(params.content))
		return;

	this.titleBar.innerHTML = "";
	this.titleBar.appendChild(params.content);

	if (this.params.draggable)
	{
		this.titleBar.parentNode.style.cursor = "move";
		BX.bind(this.titleBar.parentNode, "mousedown", BX.proxy(this._startDrag, this));
	}
};

BX.PopupWindow.prototype.setClosingByEsc = function(enable)
{
	enable = !!enable;
	if (enable)
	{
		this.closeByEsc = true;
		if (!this.isCloseByEscBinded)
		{
			BX.bind(document, "keyup", BX.proxy(this._onKeyUp, this));
			this.isCloseByEscBinded = true;
		}
	}
	else
	{
		this.closeByEsc = false;
		if (this.isCloseByEscBinded)
		{
			BX.unbind(document, "keyup", BX.proxy(this._onKeyUp, this));
			this.isCloseByEscBinded = false;
		}
	}
};

BX.PopupWindow.prototype.setOverlay = function(params)
{
	if (this.overlay == null)
	{
		this.overlay = {
			element : BX.create("div", { props : { className: "popup-window-overlay", id : "popup-window-overlay-" + this.uniquePopupId } })
		};

		this.adjustOverlayZindex();
		this.resizeOverlay();
		document.body.appendChild(this.overlay.element);
	}

	if (params && params.opacity && BX.type.isNumber(params.opacity) && params.opacity >= 0 && params.opacity <= 100)
	{
		if (BX.browser.IsIE() && !BX.browser.IsIE9())
			this.overlay.element.style.filter =  "alpha(opacity=" + params.opacity +")";
		else
		{
			this.overlay.element.style.filter = "none";
			this.overlay.element.style.opacity = parseFloat(params.opacity/100).toPrecision(3);
		}
	}

	if (params && params.backgroundColor)
		this.overlay.element.style.backgroundColor = params.backgroundColor;
};

BX.PopupWindow.prototype.removeOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
		BX.remove(this.overlay.element);

	this.overlay = null;
};

BX.PopupWindow.prototype.hideOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
		this.overlay.element.style.display = "none";
};

BX.PopupWindow.prototype.showOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
		this.overlay.element.style.display = "block";
};

BX.PopupWindow.prototype.resizeOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
	{
		var windowSize = BX.GetWindowScrollSize();
		this.overlay.element.style.width = windowSize.scrollWidth + "px";
		this.overlay.element.style.height = windowSize.scrollHeight + "px";
	}
};

BX.PopupWindow.prototype.getZindex = function()
{
	if (this.overlay != null)
		return BX.PopupWindow.getOption("popupOverlayZindex") + this.params.zIndex;
	else
		return BX.PopupWindow.getOption("popupZindex") + this.params.zIndex;
};


BX.PopupWindow.prototype.adjustOverlayZindex = function()
{
	if (this.overlay != null && this.overlay.element != null)
	{
		this.overlay.element.style.zIndex = parseInt(this.popupContainer.style.zIndex) - 1;
	}
};


BX.PopupWindow.prototype.show = function()
{
	if (!this.firstShow)
	{
		BX.onCustomEvent(this, "onPopupFirstShow", [this]);
		this.firstShow = true;
	}
	BX.onCustomEvent(this, "onPopupShow", [this]);

	this.showOverlay();
	this.popupContainer.style.display = "block";

	this.adjustPosition();

	BX.onCustomEvent(this, "onAfterPopupShow", [this]);

	if (this.closeByEsc && !this.isCloseByEscBinded)
	{
		BX.bind(document, "keyup", BX.proxy(this._onKeyUp, this));
		this.isCloseByEscBinded = true;
	}

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

	if (event && !(BX.getEventButton(event)&BX.MSLEFT))
		return true;

	BX.onCustomEvent(this, "onPopupClose", [this, event]);

	this.hideOverlay();
	this.popupContainer.style.display = "none";

	if (this.isCloseByEscBinded)
	{
		BX.unbind(document, "keyup", BX.proxy(this._onKeyUp, this));
		this.isCloseByEscBinded = false;
	}

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

BX.PopupWindow.prototype._onCloseIconClick = function(event)
{
	event = event || window.event;
	this.close(event);
	BX.PreventDefault(event);
};

BX.PopupWindow.prototype._onKeyUp = function(event)
{
	event = event || window.event;
	if (event.keyCode == 27)
		this.close(event);
};

BX.PopupWindow.prototype.destroy = function()
{
	BX.onCustomEvent(this, "onPopupDestroy", [this]);
	BX.unbindAll(this);
	BX.unbind(document, "keyup", BX.proxy(this._onKeyUp, this));
	BX.unbind(document, "click", BX.proxy(this.close, this));
	BX.unbind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this._stopDrag, this));
	BX.unbind(window, "resize", BX.proxy(this._onResizeWindow, this));
	BX.remove(this.popupContainer);
	this.removeOverlay();
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

	var angleTopOffset = BX.PopupWindow.getOption("angleTopOffset");

	var left = this.bindElementPos.left + this.offsetLeft -
				(this.isTopOrBottomAngle() ? BX.PopupWindow.getOption("angleLeftOffset") : 0);

	if ( !this.bindOptions.forceLeft &&
		(left + popupWidth + this.bordersWidth) >= (windowSize.innerWidth + windowScroll.scrollLeft) &&
		(windowSize.innerWidth + windowScroll.scrollLeft - popupWidth - this.bordersWidth) > 0)
	{
			var bindLeft = left;
			left = windowSize.innerWidth + windowScroll.scrollLeft - popupWidth - this.bordersWidth;
			if (this.isTopOrBottomAngle())
			{
				this.setAngle({ offset : bindLeft - left + this.angle.defaultOffset});
			}
	}
	else if (this.isTopOrBottomAngle())
	{
		this.setAngle({ offset : this.angle.defaultOffset + (left < 0 ? left : 0) });
	}

	if (left < 0)
		left = 0;

	var top = 0;

	if (this.bindOptions.position && this.bindOptions.position == "top")
	{
		top = this.bindElementPos.top - popupHeight - this.offsetTop - (this.isBottomAngle() ? angleTopOffset : 0);
		if (top < 0 || (!this.bindOptions.forceTop && top < windowScroll.scrollTop))
		{
			top = this.bindElementPos.bottom + this.offsetTop;
			if (this.angle != null)
			{
				top += angleTopOffset;
				this.setAngle({ position: "top"});
			}
		}
		else if (this.isTopAngle())
		{
			top = top - angleTopOffset + BX.PopupWindow.getOption("positionTopXOffset");
			this.setAngle({ position: "bottom"});
		}
		else
		{
			top += BX.PopupWindow.getOption("positionTopXOffset");
		}
	}
	else
	{
		top = this.bindElementPos.bottom + this.offsetTop + this.getAngleHeight();

		if ( !this.bindOptions.forceTop &&
			(top + popupHeight) > (windowSize.innerHeight + windowScroll.scrollTop) &&
			(this.bindElementPos.top - popupHeight - this.getAngleHeight()) >= 0) //Can we place the PopupWindow above the bindElement?
		{
			//The PopupWindow doesn't place below the bindElement. We should place it above.
			top = this.bindElementPos.top - popupHeight;
			if (this.isTopOrBottomAngle())
			{
				top -= angleTopOffset;
				this.setAngle({ position: "bottom"});
			}

			top += BX.PopupWindow.getOption("positionTopXOffset");
		}
		else if (this.isBottomAngle())
		{
			top += angleTopOffset;
			this.setAngle({ position: "top"});
		}
	}

	if (top < 0)
		top = 0;

	BX.adjust(this.popupContainer, { style: {
		top: top + "px",
		left: left + "px",
		zIndex: this.getZindex()
	}});

	this.adjustOverlayZindex();
};

BX.PopupWindow.prototype._onResizeWindow = function(event)
{
	if (this.isShown())
	{
		this.adjustPosition();
		if (this.overlay != null)
			this.resizeOverlay();
	}
};

BX.PopupWindow.prototype.move = function(offsetX, offsetY)
{
	var left = parseInt(this.popupContainer.style.left) + offsetX;
	var top = parseInt(this.popupContainer.style.top) + offsetY;

	if (typeof(this.params.draggable) == "object" && this.params.draggable.restrict)
	{
		//Left side
		if (left < 0)
			left = 0;

		//Right side
		var scrollSize = BX.GetWindowScrollSize();
		var floatWidth = this.popupContainer.offsetWidth;
		var floatHeight = this.popupContainer.offsetHeight;

		if (left > (scrollSize.scrollWidth - floatWidth))
			left = scrollSize.scrollWidth - floatWidth;

		if (top > (scrollSize.scrollHeight - floatHeight))
			top = scrollSize.scrollHeight - floatHeight;

		//Top side
		if (top < 0)
			top = 0;
	}

	this.popupContainer.style.left = left + "px";
	this.popupContainer.style.top = top + "px";
};

BX.PopupWindow.prototype._startDrag = function(event)
{
	event = event || window.event;
	BX.fixEventPageXY(event);

	this.dragPageX = event.pageX;
	this.dragPageY = event.pageY;
	this.dragged = false;

	BX.bind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.bind(document, "mouseup", BX.proxy(this._stopDrag, this));

	if (document.body.setCapture)
		document.body.setCapture();

	//document.onmousedown = BX.False;
	document.body.ondrag = BX.False;
	document.body.onselectstart = BX.False;
	document.body.style.cursor = "move";
	document.body.style.MozUserSelect = "none";
	this.popupContainer.style.MozUserSelect = "none";

	return BX.PreventDefault(event);
};

BX.PopupWindow.prototype._moveDrag = function(event)
{
	event = event || window.event;
	BX.fixEventPageXY(event);

	if(this.dragPageX == event.pageX && this.dragPageY == event.pageY)
		return;

	this.move((event.pageX - this.dragPageX), (event.pageY - this.dragPageY));
	this.dragPageX = event.pageX;
	this.dragPageY = event.pageY;

	if (!this.dragged)
	{
		BX.onCustomEvent(this, "onPopupDragStart");
		this.dragged = true;
	}

	BX.onCustomEvent(this, "onPopupDrag");
};

BX.PopupWindow.prototype._stopDrag = function(event)
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this._stopDrag, this));

	//document.onmousedown = null;
	document.body.ondrag = null;
	document.body.onselectstart = null;
	document.body.style.cursor = "";
	document.body.style.MozUserSelect = "";
	this.popupContainer.style.MozUserSelect = "";

	BX.onCustomEvent(this, "onPopupDragEnd");
	this.dragged = false;

	return BX.PreventDefault(event);
};

BX.PopupWindow.options = {};
BX.PopupWindow.defaultOptions = {

	angleLeftOffset : 15,

	positionTopXOffset : 0,
	angleTopOffset : 8,

	popupZindex : 1000,
	popupOverlayZindex : 1100,

	angleMinLeft : 10,
	angleMaxLeft : 10,

	angleMinRight : 10,
	angleMaxRight : 10,

	angleMinBottom : 7,
	angleMaxBottom : 25,

	angleMinTop : 7,
	angleMaxTop : 25,

	offsetLeft : 0,
	offsetTop: 0
};
BX.PopupWindow.setOptions = function(options)
{
	if (!options || typeof(options) != "object")
		return;

	for (var option in options)
		BX.PopupWindow.options[option] = options[option];
};

BX.PopupWindow.getOption = function(option, defaultValue)
{
	if (typeof(BX.PopupWindow.options[option]) != "undefined")
		return BX.PopupWindow.options[option];
	else if (typeof(defaultValue) != "undefined")
		return defaultValue;
	else
		return BX.PopupWindow.defaultOptions[option];
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
		if (BX.type.isString(this.className) && (this.className != ''))
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

BX.PopupMenu = {

	Data : {},
	currentItem : null,
	stack : [],

	onkeypresslistener: null,

	show : function(Id, bindElement, menuItems, params, level)
	{
		if (!level)
			level = 0;

		if (level < this.stack.length)
		{
			for (var i = this.stack.length-1; i >= level; i--)
			{
				this.currentItem = this.stack.pop();

				if (this.currentItem)
					this.currentItem.popupWindow.close();

				if (i > 0)
					this.currentItem = this.stack[i-1];
			}
		}

		if (!this.Data[Id])
		{
			this.Data[Id] = { id : Id, bindElement : bindElement, params : params };
			this.Data[Id].popupWindow = this.__createPopup(this.Data[Id], menuItems, params);
		}

		this.currentItem = this.Data[Id];
		this.stack[level] = this.currentItem;

		this.currentItem.popupWindow.show();

		if (!this.onkeypresslistener)
		{
			this.onkeypresslistener = BX.delegate(function(e) {
				e = e || window.event;
				if (e && e.keyCode == 27)
				{
					this.currentItem = this.stack.pop();

					if (this.currentItem)
						this.currentItem.popupWindow.close();

					if (this.stack.length > 0)
						this.currentItem = this.stack[this.stack.length - 1];
				}
			}, this);

			BX.bind(document, 'keypress', this.onkeypresslistener);
		}
	},

	__createPopup : function(node, menuItems, params)
	{
		var items = [];
		for (var i = 0; i < menuItems.length; i++)
		{
			var item = menuItems[i];

			if (!item)
				continue;

			if (i > 0)
				items.push(BX.create("div", { props : { className : "popup-window-hr" }, html:'<i></i>'}));

			if (!!item.delimiter)
			{
				var a = BX.create('span', {props:{className:'popup-window-delimiter'},html:'<i></i>'});
			}
			else if (!!item.text && BX.type.isNotEmptyString(item.text))
			{

				var a = BX.create(!!item.href ? "a" : "span", {
					props : { className: "menu-popup-item" +  (BX.type.isNotEmptyString(item.className) ? " " + item.className : "")},
					attrs : { title : item.title ? item.title : "", onclick: item.onclick && BX.type.isString(item.onclick) ? item.onclick : null},
					events : item.onclick && BX.type.isFunction(item.onclick) ? { click : BX.proxy(item.onclick, node) } : null,
					html :  '<span class="menu-popup-item-left"></span><span class="menu-popup-item-icon"></span><span class="menu-popup-item-text">' + item.text + '</span><span class="menu-popup-item-right"></span>'
				});

				if (item.href)
					a.href = item.href;
			}

			items.push(a);
		}

		var popupWindow = new BX.PopupWindow("menu-popup-" + node.id, node.bindElement, {
			closeByEsc : false,
			autoHide : typeof(params.autoHide) != "undefined" ? params.autoHide : true,
			offsetTop : params.offsetTop ? params.offsetTop : 1,
			offsetLeft : params.offsetLeft ? params.offsetLeft : 0,
			lightShadow : typeof(params.lightShadow) != "undefined" ? params.lightShadow : true,
			angle : typeof(params.angle) != "undefined" ? params.angle : false,
			content : BX.create("div", { props : { className : "menu-popup" }, children: [
				BX.create("div", { props : { className : "menu-popup-items" }, children: items})
			]})
		});

		if (params && params.events)
		{
			for (var eventName in params.events)
				BX.addCustomEvent(popupWindow, eventName, params.events[eventName]);
		}

		return popupWindow;
	}
};

// TODO: copypaste/update/enhance CSS and images from calendar to MAIN CORE
// this.values = [{ID: 1, NAME : '111', DESCRIPTION: '111', URL: 'href://...'}]

window.BXInputPopup = function(params)
{
	this.id = params.id || 'bx-inp-popup-' + Math.round(Math.random() * 1000000);
	this.handler = params.handler || false;
	this.values = params.values || false;
	this.pInput = params.input;
	this.bValues = !!this.values;
	this.defaultValue = params.defaultValue || '';
	this.openTitle = params.openTitle || '';
	this.className = params.className || '';
	this.noMRclassName = params.noMRclassName || 'ec-no-rm';
	this.emptyClassName = params.noMRclassName || 'ec-label';

	var _this = this;
	this.curInd = false;

	if (this.bValues)
	{
		this.pInput.onfocus = this.pInput.onclick = function(e)
		{
			if (this.value == _this.defaultValue)
			{
				this.value = '';
				this.className = '';
			}
			_this.ShowPopup();
			return BX.PreventDefault(e);
		};
		this.pInput.onblur = function()
		{
			if (_this.bShowed)
				setTimeout(function(){_this.ClosePopup(true);}, 200);
			_this.OnChange();
		};
	}
	else
	{
		this.pInput.className = this.noMRclassName;
		this.pInput.onblur = BX.proxy(this.OnChange, this);
	}
}

BXInputPopup.prototype = {
ShowPopup: function()
{
	if (this.bShowed)
		return;

	var _this = this;
	if (!this.oPopup)
	{
		var
			pRow,
			pWnd = BX.create("DIV", {props:{className: "bxecpl-loc-popup " + this.className}});

		for (var i = 0, l = this.values.length; i < l; i++)
		{
			pRow = pWnd.appendChild(BX.create("DIV", {
				props: {id: 'bxecmr_' + i, title: this.values[i].DESCRIPTION},
				text: this.values[i].NAME,
				events: {
					mouseover: function(){this.className = 'bxecplloc-over';},
					mouseout: function(){this.className = '';},
					click: function()
					{
						var ind = this.id.substr('bxecmr_'.length);
						_this.pInput.value = _this.values[ind].NAME;
						_this.curInd = ind;
						_this.OnChange();
						_this.ClosePopup(true);
					}
				}
			}));

			if (this.values[i].URL)
				pRow.appendChild(BX.create('A', {props: {href: this.values[i].URL, className: 'bxecplloc-view', target: '_blank', title: this.openTitle}}));
		}

		this.oPopup = new BX.PopupWindow(this.id, this.pInput, {
			autoHide : true,
			offsetTop : 1,
			offsetLeft : 0,
			lightShadow : true,
			closeByEsc : true,
			content : pWnd
		});

		BX.addCustomEvent(this.oPopup, 'onPopupClose', BX.proxy(this.ClosePopup, this));
	}

	this.oPopup.show();
	this.pInput.select();

	this.bShowed = true;
	BX.onCustomEvent(this, 'onInputPopupShow', [this]);
},

ClosePopup: function(bClosePopup)
{
	this.bShowed = false;

	if (this.pInput.value == '')
		this.OnChange();

	BX.onCustomEvent(this, 'onInputPopupClose', [this]);

	if (bClosePopup === true)
		this.oPopup.close();
},

OnChange: function()
{
	var val = this.pInput.value;
	if (this.bValues)
	{
		if (this.pInput.value == '' || this.pInput.value == this.defaultValue)
		{
			this.pInput.value = this.defaultValue;
			this.pInput.className = this.emptyClassName;
			val = '';
		}
		else
		{
			this.pInput.className = '';
		}
	}

	if (isNaN(parseInt(this.curInd)) || this.curInd !==false && val != this.values[this.curInd].NAME)
		this.curInd = false;
	else
		this.curInd = parseInt(this.curInd);

	BX.onCustomEvent(this, 'onInputPopupChanged', [this, this.curInd, val]);
	if (this.handler && typeof this.handler == 'function')
		this.handler({ind: this.curInd, value: val});
},

Set: function(ind, val, bOnChange)
{
	this.curInd = ind;
	if (this.curInd !== false)
		this.pInput.value = this.values[this.curInd].NAME;
	else
		this.pInput.value = val;

	if (bOnChange !== false)
		this.OnChange();
},

Get: function(ind)
{
	var
		id = false;
	if (typeof ind == 'undefined')
		ind = this.curInd;

	if (ind !== false && this.values[ind])
		id = this.values[ind].ID;
	return id;
},

GetIndex: function(id)
{
	for (var i = 0, l = this.values.length; i < l; i++)
		if (this.values[i].ID == id)
			return i;
	return false;
},

Deactivate: function(bDeactivate)
{
	if (this.pInput.value == '' || this.pInput.value == this.defaultValue)
	{
		if (bDeactivate)
		{
			this.pInput.value = '';
			this.pInput.className = this.noMRclassName;
		}
		else if (this.oEC.bUseMR)
		{
			this.pInput.value = this.defaultValue;
			this.pInput.className = this.emptyClassName;
		}
	}
	this.pInput.disabled = bDeactivate;
}
};


})(window);
