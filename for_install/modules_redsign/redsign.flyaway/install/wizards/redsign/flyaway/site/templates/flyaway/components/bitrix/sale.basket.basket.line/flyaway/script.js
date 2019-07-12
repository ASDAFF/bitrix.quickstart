'use strict';

function BitrixSmallCart(){}

BitrixSmallCart.prototype = {

	activate: function ()
	{
		this.cartElement = BX(this.cartId);
    this.isOpen = false;
		this.fixedPosition = this.arParams.POSITION_FIXED == 'Y';
		if (this.fixedPosition)
		{
			this.cartClosed = true;
			this.maxHeight = false;
			this.itemRemoved = false;
			this.verticalPosition = this.arParams.POSITION_VERTICAL;
			this.horizontalPosition = this.arParams.POSITION_HORIZONTAL;
			this.topPanelElement = BX("bx-panel");

			this.fixAfterRender(); // TODO onready
			this.fixAfterRenderClosure = this.closure('fixAfterRender');

			var fixCartClosure = this.closure('fixCart');
			this.fixCartClosure = fixCartClosure;

			if (this.topPanelElement && this.verticalPosition == 'top')
				BX.addCustomEvent(window, 'onTopPanelCollapse', fixCartClosure);

			var resizeTimer = null;
			BX.bind(window, 'resize', function() {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(fixCartClosure, 200);
			});
		}
		this.setCartBodyClosure = this.closure('setCartBody');
		BX.addCustomEvent(window, 'OnBasketChange', this.closure('refreshCart', {}));
	},

	fixAfterRender: function ()
	{
		this.statusElement = BX(this.cartId + 'status');
		if (this.statusElement)
		{
			if (this.cartClosed)
				this.statusElement.innerHTML = this.openMessage;
			else
				this.statusElement.innerHTML = this.closeMessage;
		}
		this.productsElement = BX(this.cartId + 'products');
		this.fixCart();
	},

	closure: function (fname, data)
	{
		var obj = this;
		return data
			? function(){obj[fname](data)}
			: function(arg1){obj[fname](arg1)};
	},

	toggleOpenCloseCart: function ()
	{
		if (this.cartClosed)
		{
			BX.removeClass(this.cartElement, 'bx-closed');
			BX.addClass(this.cartElement, 'bx-opener');
			this.statusElement.innerHTML = this.closeMessage;
			this.cartClosed = false;
			this.fixCart();
		}
		else // Opened
		{
			BX.addClass(this.cartElement, 'bx-closed');
			BX.removeClass(this.cartElement, 'bx-opener');
			BX.removeClass(this.cartElement, 'bx-max-height');
			this.statusElement.innerHTML = this.openMessage;
			this.cartClosed = true;
			var itemList = this.cartElement.querySelector("[data-role='basket-item-list']");
			if (itemList)
				itemList.style.top = "auto";
		}
		setTimeout(this.fixCartClosure, 100);
	},

	setVerticalCenter: function(windowHeight)
	{
		var top = windowHeight/2 - (this.cartElement.offsetHeight/2);
		if (top < 5)
			top = 5;
		this.cartElement.style.top = top + 'px';
	},

	fixCart: function()
	{
		// set horizontal center
		if (this.horizontalPosition == 'hcenter')
		{
			var windowWidth = 'innerWidth' in window
				? window.innerWidth
				: document.documentElement.offsetWidth;
			var left = windowWidth/2 - (this.cartElement.offsetWidth/2);
			if (left < 5)
				left = 5;
			this.cartElement.style.left = left + 'px';
		}

		var windowHeight = 'innerHeight' in window
			? window.innerHeight
			: document.documentElement.offsetHeight;

		// set vertical position
		switch (this.verticalPosition) {
			case 'top':
				if (this.topPanelElement)
					this.cartElement.style.top = this.topPanelElement.offsetHeight + 5 + 'px';
				break;
			case 'vcenter':
				this.setVerticalCenter(windowHeight);
				break;
		}

		// toggle max height
		if (this.productsElement)
		{
			var itemList = this.cartElement.querySelector("[data-role='basket-item-list']");
			if (this.cartClosed)
			{
				if (this.maxHeight)
				{
					BX.removeClass(this.cartElement, 'bx-max-height');
					if (itemList)
						itemList.style.top = "auto";
					this.maxHeight = false;
				}
			}
			else // Opened
			{
				if (this.maxHeight)
				{
					if (this.productsElement.scrollHeight == this.productsElement.clientHeight)
					{
						BX.removeClass(this.cartElement, 'bx-max-height');
						if (itemList)
							itemList.style.top = "auto";
						this.maxHeight = false;
					}
				}
				else
				{
					if (this.verticalPosition == 'top' || this.verticalPosition == 'vcenter')
					{
						if (this.cartElement.offsetTop + this.cartElement.offsetHeight >= windowHeight)
						{
							BX.addClass(this.cartElement, 'bx-max-height');
							if (itemList)
								itemList.style.top = 82+"px";
							this.maxHeight = true;
						}
					}
					else
					{
						if (this.cartElement.offsetHeight >= windowHeight)
						{
							BX.addClass(this.cartElement, 'bx-max-height');
							if (itemList)
								itemList.style.top = 82+"px";
							this.maxHeight = true;
						}
					}
				}
			}

			if (this.verticalPosition == 'vcenter')
				this.setVerticalCenter(windowHeight);
		}
	},

	refreshCart: function (data)
	{
		if (this.itemRemoved)
		{
			this.itemRemoved = false;
			return;
		}
    this.isOpen = $("#dropdown_basket").is(':visible');

		data.sessid = BX.bitrix_sessid();
		data.siteId = this.siteId;
		data.templateName = this.templateName;
		data.arParams = this.arParams;

    rsFlyaway.darken($("#dropdown_basket .basket-table"));

		BX.ajax({
			url: this.ajaxPath,
			method: 'POST',
			dataType: 'html',
			data: data,
			onsuccess: this.setCartBodyClosure
		});
	},

	setCartBody: function (result)
	{
		if (this.cartElement)
			this.cartElement.innerHTML = result;
		if (this.fixedPosition)
			setTimeout(this.fixAfterRenderClosure, 100);

    if(this.isOpen) {
        $(document).off("click.dropdown-basket");
        $("#dropdown_basket_link").click();
    }

    initSelect();
	},

	removeItemFromCart: function (id)
	{
		this.refreshCart ({sbblRemoveItemFromCart: id});
		this.itemRemoved = true;
		BX.onCustomEvent('OnBasketChange');
		$(document).trigger("change.rs_flyaway.inbasket");
	}
};

$(document).ready(function() {

    $(document).on("click", "#dropdown_basket_link", function(e) {

        if($(window).width() <= rsFlyaway.breakpoints.sm || $(this).siblings("#dropdown_basket").find(".js-smbasket tr").length === 0) {
            return true;
        }

        e.preventDefault();
        $("#dropdown_basket").show();

        $(document).on("click.dropdown-basket", function(e) {
            if(!$(e.target).parents().andSelf().is("#dropdown_basket")){
                $("#dropdown_basket").hide();
                $(document).off("click.dropdown-basket");
            }
        });

    });

    $(document).on("change", ".js-outbasket-quantity", function() {
        var $this = $(this),
            $element = $this.parents("tr.js-element"),
            id = $this.parents("tr.js-element").data("id"),
            quantity = $this.val(),
            $addBasketPopup = $(".js-addbasketpopup");

        if($addBasketPopup.length > 0) {
            rsFlyaway.darken($addBasketPopup);
        }

        Basket.updateQuantity(id, quantity)
            .then(function(data) {
				console.log(data);
                if($addBasketPopup.length > 0) {
						console.log(data.BASKET_DATA.GRID.ROWS[id]);
                        if(data.BASKET_DATA.GRID.ROWS[id]) {
                            $addBasketPopup.find(".js-item-price").html(data.BASKET_DATA.GRID.ROWS[id].FULL_PRICE_FORMATED);
                            $addBasketPopup.find(".js-outbasket-quantity").val(data.BASKET_DATA.GRID.ROWS[id].QUANTITY);
                            $addBasketPopup.find(".js-item-sum").html(data.BASKET_DATA.GRID.ROWS[id].SUM);
                        }


                   $(".js-total-price").html(data.BASKET_DATA.allSum_FORMATED);
                   rsFlyaway.darken($addBasketPopup);
                }

            });
        BX.onCustomEvent('OnBasketChange');
    });

    $(document).on("show.bs.dropdown", ".js-smbasket .js-select", function(e) {
        var $element = $(this),
            $dropdown = $element.find(".select-menu"),
            dropdownPositionTop = $element.position().top + $element.outerHeight(),
            heighterHeight = $(".js-smbasket .heighter").outerHeight();

        if(
            dropdownPositionTop + $dropdown.outerHeight() > heighterHeight &&
            $element.position().top -  $dropdown.outerHeight() > 0
        ) {
            $dropdown.css({
                'top': 'auto',
                'bottom': '100%',
                'margin-bottom': '2px'
            });
        } else if(
            dropdownPositionTop + $dropdown.outerHeight() > heighterHeight &&
            $element.position().top -  $dropdown.outerHeight() <= 0
        ) {
            if($element.position().top <= heighterHeight - dropdownPositionTop) {
                $dropdown.css({
                    'max-height': (heighterHeight - dropdownPositionTop - 5) + "px"
                });
            } else {
                $dropdown.css({
                    'top': 'auto',
                    'bottom': '100%',
                    'margin-bottom': '2px',
                    'max-height': ($element.position().top - 5) + "px"
                });
            }
        }

    });
    $(document).on("hidden.bs.dropdown", ".js-smbasket .js-select", function(e) {
        $(this).find(".select-menu").removeAttr('style');
    });
});
