$(function () {
	'use strict';
	
	var isSetBuy1Click  = false;
	
	$(".setbuy1click").on("click", function (e) {
		isSetBuy1Click = true;

	});
	
	$(document).on("click", ".fancyajaxwait.setbuy1click", function (e) {
		e.preventDefault();
		
		var $this = $(this),
			urlData = {},
			url = '';
		
		if(!$(this).hasClass("fancybox.ajax")) {
			urlData = {
				RS_EXT_FIELD_0: itemsToString(getSelectedItems()),
				"get_form": "1"
			}  
		}
		
		url = $this.attr("href").indexOf("?") != -1? 
				  $this.attr("href") + "&" + $.param(urlData): 
				  $this.attr("href") + "?" + $.param(urlData);
		
		window.location = url;
	});
	
	$(document).on();
	
	$(document).on("RSFLYAWAY_fancyBeforeShow", function () {
		var items = [],
			text = '';
		
		if(isSetBuy1Click) {
			items = getSelectedItems();
			$(".fancybox-wrap [name=RS_EXT_FIELD_0]").val(itemsToString(items));
		}
		
		isSetBuy1Click = false;
	});
	
	$(".set_add2basket").on("click", function () {
		var $this = $(this),
			$jsConstructor = $this.parents(".js-constructor"),
			data = {},
			setIds = [],
			url = $jsConstructor.data('ajaxpath');
			
		getSelectedItems().forEach(function (item) {
			setIds.push(item.id);
		});
		
		rsFlyaway.darken($('.constructor-wrapper'));
			
		data = {
			sessid: BX.bitrix_sessid(),
			action: 'catalogSetAdd2Basket',
			set_ids: setIds,
			lid: $jsConstructor.data('lid'),	
			iblockId: $jsConstructor.data('iblockid'),
			setOffersCartProps: $jsConstructor.data('setOffersCartProps')			
		};
		$.post(url, data, function (result) {
			updateBasketLine();
			rsFlyaway.darken($('.constructor-wrapper'));
		}, "json")
		
	});
	
	$(".my-sets_link").on("click", function () {
		$(".my-set").toggle();
	});
	
	$(".selected-items ").on("click", ".item > .remove", function () {
		var $this = $(this),
			$item = $(this).parent();
		$(".allitems .item[data-elementid=" + $item.data("elementid") +"] .checkbox").click();
	});
	
	$(".my-set .checkbox").on("click", function () {
		var $this = $(this),
			$item = $this.parent(),
			isSelected = $this.hasClass("selected");
		
		if(!isSelected && getSelectedItems().length >= 5) {
			return false;
		}
		
		$this.toggleClass("selected");
		isSelected = !isSelected;
		
		if(!isSelected) {
			var $selectedItem = $(".selected-items .item[data-elementid=" + $item.data("elementid") +"]");
			removeItem($selectedItem);
		} else {
			addItem($item);
		}
	});
	
	function getSelectedItems () {
		var items = [];
		$(".selected-items .item").each(function (key, item) {
			var $item = $(item);
			items.push({
				id: $item.data("elementid"),
				name: $item.find(".set-name").text().trim()
			});
		});
		
		return items;
	}
	
	function itemsToString (items) {
		var str = '';
		items.map(function (item, i) {
			str += '[' + item.id + ']' + ' ' + item.name;
			if(items.length !== i + 1) {
				str += ", ";
			}	
		});
		return str;
	}
	
	function updatePrices(successFn) {
		var sumNewprice = 0,
			sumOldprice = 0,
			sumDiscount = 0,
			$jsConstructor = $(".js-constructor"),
			url = $jsConstructor.data('ajaxpath'),
			data = {};
			
		successFn = successFn || function () {};
			
		$(".selected-items .item").each(function (key, item) {
			sumNewprice += parseInt($(item).data("price"));
			sumOldprice += parseInt($(item).data("oldprice"));
			sumDiscount += parseInt($(item).data("discount"));
		});
		
		data = {
			sessid: BX.bitrix_sessid(),
			action: "ajax_recount_prices",
			sumPrice: sumNewprice,
			sumOldPrice: sumOldprice,
			sumDiffDiscountPrice: sumDiscount,
			currency: $jsConstructor.data('currency')
		};
		
		$.post(url, data, function (result) {
			var $panel = $jsConstructor.find(".set-panel");
			
			if(result.formatSum) {
				$panel.find(".set-panel-price__cool").html(result.formatSum);
			}
			if(result.formatOldSum) {
				$panel.find(".set-panel-price__old").html(result.formatOldSum);
			}
			if(result.formatDiscDiffSum) {
				$panel.find(".set-price__discount").html(result.formatDiscDiffSum);
			}
			successFn(result);
		}, "json");
	}
	
	function removeItem($item) {
		rsFlyaway.darken($('.constructor-wrapper'));
		$(".selected-items.owlslider").trigger('remove.owl.carousel', $item.parent().index()).trigger("refresh.owl.carousel");
		updatePrices(function () {
			rsFlyaway.darken($('.constructor-wrapper'));
		});
	}
	
	function addItem($item) {
		var $cloneItem = $item.clone();
		
		$cloneItem.find(".checkbox").remove();
		$cloneItem.prepend("<div class = 'remove'><i class='fa fa-close'></i></div>");
		$cloneItem.append("<div class = 'separator plus'></div>");
		
		rsFlyaway.darken($('.constructor-wrapper'));
		$(".selected-items.owlslider").trigger('add.owl.carousel', $cloneItem).trigger("refresh.owl.carousel");
		updatePrices(function () {
			rsFlyaway.darken($('.constructor-wrapper'));
		});
	}
});
$(document).ready(function() {
	$('.item-right').parent().css('float', 'right');
});