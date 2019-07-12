(function(window) {

if (!window.BX || BX.PortalTopMenu)
	return;

BX.PortalTopMenu = {
	items : {},
	idCnt : 1,
	getItem : function(item)
	{
		if (!BX.type.isDomNode(item))
			return null;

		var id = !item.id || !BX.type.isNotEmptyString(item.id) ? (item.id = "root-menu-item-" + this.idCnt++) : item.id;

		if (!this.items[id])
			this.items[id] = new PortalTopMenuItem(item);

		return this.items[id];
	},

	itemOver : function(item)
	{
		var menuItem = this.getItem(item);
		if (!menuItem)
			return false;

		menuItem.itemOver();
	},

	itemOut : function(item)
	{
		var menuItem = this.getItem(item);

		if (menuItem)
			menuItem.itemOut();
	}
};


var PortalTopMenuItem = function(item)
{
	this.element = item;
	this.submenu = BX.findChild(item, { className: "submenu" }, true);
	this.timeoutId = null;
	if (this.submenu)
	{
		BX("top-menu-layout", true).appendChild(
			this.submenu.parentNode.removeChild(this.submenu)
		);

		BX.bind(this.submenu, "mouseover", BX.proxy(this.itemOver, this));
		BX.bind(this.submenu, "mouseout", BX.proxy(this.itemOut, this));
	}
};

PortalTopMenuItem.prototype.itemOver = function()
{
	if (this.timeoutId)
		clearTimeout(this.timeoutId);

	if (this.submenu && this.submenu.style.display != "block")
	{
		BX.addClass(this.element, "hover");
		this.adjustPosition();
	}
};

PortalTopMenuItem.prototype.itemOut = function()
{
	this.timeoutId = setTimeout(BX.proxy(this.__itemOut, this), 0);
};

PortalTopMenuItem.prototype.__itemOut = function()
{
	BX.removeClass(this.element, "hover");
	if (this.submenu)
		this.submenu.style.display = "none";
};

PortalTopMenuItem.prototype.adjustPosition = function()
{
	if (!this.submenu)
		return;

	var left = 0;
	var pos = BX.pos(this.element, true);
	var offsetWidth = this.getSubmenuWidth();
	if ((pos.left + offsetWidth + 42) > BX.GetWindowInnerSize().innerWidth)
	{
		left = pos.left - offsetWidth + pos.width + 12;
		BX.addClass(this.submenu, "submenu-rtl");
	}
	else
	{
		left = pos.left;
		BX.removeClass(this.submenu, "submenu-rtl");
	}

	BX.adjust(this.submenu, {
		style: {
			position: "absolute",
			top: pos.bottom + "px",
			left: left + "px",
			display: "block",
			width: pos.width+"px",
			zIndex: 1001
		}
	});
};

PortalTopMenuItem.prototype.getSubmenuWidth = function()
{
	var offsetWidth = BX.hasClass(this.submenu, "submenu-two-columns") ? 400 : 220;
	if (this.element.offsetWidth > offsetWidth - 8)
	{
		offsetWidth = this.element.offsetWidth + 50;
		this.submenu.style.width = this.submenu.style.maxWidth = offsetWidth + "px";
	}
	else
	{
		this.submenu.style.width = "";
		this.submenu.style.maxWidth = "400px";
	}

	return offsetWidth;
};

})(window);