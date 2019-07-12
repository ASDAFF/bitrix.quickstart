(function(window) {

	if (!window.BX || BX.CatalogMenu)
		return;

	BX.CatalogMenu = {
		items : {},
		idCnt : 1,
		currentItem : null,
		overItem : null,
		outItem : null,
		timeoutOver : null,
		timeoutOut : null,

		getItem : function(item)
		{
			if (!BX.type.isDomNode(item))
				return null;

			var id = !item.id || !BX.type.isNotEmptyString(item.id) ? (item.id = "menu-item-" + this.idCnt++) : item.id;

			if (!this.items[id])
				this.items[id] = new CatalogMenuItem(item);

			return this.items[id];
		},

		itemOver : function(item)
		{
			var menuItem = this.getItem(item);
			if (!menuItem)
				return;

			if (this.outItem == menuItem)
			{
				clearTimeout(menuItem.timeoutOut);
			}

			this.overItem = menuItem;

			if (menuItem.timeoutOver)
			{
				clearTimeout(menuItem.timeoutOver);
			}

			menuItem.timeoutOver = setTimeout(function() {
				if (BX.CatalogMenu.overItem == menuItem)
				{
					menuItem.itemOver();
				}

			}, 100);
		},

		itemOut : function(item)
		{
			var menuItem = this.getItem(item);
			if (!menuItem)
				return;

			this.outItem = menuItem;

			if (menuItem.timeoutOut)
			{
				clearTimeout(menuItem.timeoutOut);
			}

			menuItem.timeoutOut = setTimeout(function() {

				if (menuItem != BX.CatalogMenu.overItem)
				{
					menuItem.itemOut();
				}
				if (menuItem == BX.CatalogMenu.outItem)
				{
					menuItem.itemOut();
				}

			}, 100);
		}
	};

	var CatalogMenuItem = function(item)
	{
		this.element = item;
		this.popup = BX.findChild(item, { className: "bx_children_container" }, false, false);
		this.isLastItem = BX.lastChild(this.element.parentNode) == this.element;
	};

	CatalogMenuItem.prototype.itemOver = function()
	{
		if (!BX.hasClass(this.element, "hover"))
		{
			BX.addClass(this.element, "hover");
			this.alignPopup();
		}
	};

	CatalogMenuItem.prototype.itemOut = function()
	{
		BX.removeClass(this.element, "hover");
	};

	CatalogMenuItem.prototype.alignPopup = function()
	{
		if (!this.popup)
			return;

		this.popup.style.cssText = "";

		var ulContainer = this.element.parentNode;
		var offsetRightPopup = this.popup.offsetLeft + this.popup.offsetWidth;
		var offsetRightMenu = ulContainer.offsetLeft + ulContainer.offsetWidth;

		if (offsetRightPopup >= offsetRightMenu)
		{
			this.popup.style.right = /*this.isLastItem ? "0px" :*/ "0";
		}
	};
})(window);


function menuCatalogResize(menuID, menuFirstWidth)
{
	var wpasum = 0; // sum of width for all li

	var firstLevelLi = BX.findChildren(BX(menuID), {className : "bx_hma_one_lvl"}, true);

	if (firstLevelLi)
	{
		for(var i = 0; i < firstLevelLi.length; i++)
		{
			var wpa = BX.firstChild(firstLevelLi[i]).clientWidth;
			wpasum += wpa;
		}

		if(menuFirstWidth && (wpasum+20) <= menuFirstWidth)
			BX.addClass(BX(menuID), "small");   //adaptive
		else
			BX.removeClass(BX(menuID), "small");
	}

	return wpasum;
}

function menuCatalogAlign(menuID)
{
	var firstLevelLi = BX.findChildren(BX(menuID), {className : "bx_hma_one_lvl"}, true);
	var wpsum = 0;

	if (firstLevelLi)
	{
		for(var i = 0; i < firstLevelLi.length; i++)
		{
			firstLevelLi[i].removeAttribute("style");
			var wp = firstLevelLi[i].clientWidth;
			wpsum = wpsum+wp;
		}

		var cof_width = wpsum/100;

		for(var i = 0; i < firstLevelLi.length; i++)
		{
			wp = firstLevelLi[i].clientWidth;
			firstLevelLi[i].style.width = (wp/cof_width)+"%";
		}
	}
}

function menuCatalogPadding(menuID)
{
	var firstLevelLi = BX.findChildren(BX(menuID), {className : "bx_hma_one_lvl"}, true);
	var wpsum = 0;

	if (firstLevelLi)
	{
		for(var i = 0; i < firstLevelLi.length; i++)
		{
			BX.firstChild(firstLevelLi[i]).style.padding = "19px 10px";
		}
	}
}

function menuCatalogChangeSectionPicure(element)
{
	var curImgWrapObj = BX.nextSibling(element);
	var curImgObj = BX.clone(BX.firstChild(curImgWrapObj));
	var curDescr = element.getAttribute("data-description");
	var parentObj = BX.hasClass(element, 'bx_hma_one_lvl') ? element : BX.findParent(element, {className:'bx_hma_one_lvl'});
	var sectionImgObj = BX.findChild(parentObj, {className:'bx_section_picture'}, true, false);
	sectionImgObj.innerHTML = "";
	sectionImgObj.appendChild(curImgObj);
	var sectionDescrObj = BX.findChild(parentObj, {className:'bx_section_description'}, true, false);
	sectionDescrObj.innerHTML = curDescr;
	BX.previousSibling(sectionDescrObj).innerHTML = element.innerHTML;
	sectionImgObj.parentNode.href = element.href;
}