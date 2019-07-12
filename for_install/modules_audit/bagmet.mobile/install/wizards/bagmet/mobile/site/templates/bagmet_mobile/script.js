BX.addCustomEvent(window, "onTopPanelCollapse", function(isCollapsed) {
	var header = BX("gritter-notice-wrapper", true);
	if (header)
		header.style.top = isCollapsed ? "75px" : "184px";
});

/* To Top of the page arrow - begin */
$(function(){
	$.fn.scrollToTop=function(){
		$(this).hide().removeAttr("href");
		if($(window).scrollTop()!="0"){
			$(this).fadeIn("slow")
		}
		var scrollDiv=$(this);
		$(window).scroll(function(){
			if($(window).scrollTop()=="0"){
				$(scrollDiv).fadeOut("slow");
			}else{
				$(scrollDiv).fadeIn("slow");
			}
		});
		$(this).click(function(){
			$("html, body").animate({scrollTop:0},"slow")
		})
	}
});
$(function() {$("#toTop").scrollToTop();});
/* To Top of the page arrow - end */

function addOffer2Cart(element, offers, elementName, cartPath)
{
	$("#buy_layer_1").css("display", "block");
	$(".layer_bg").css("display", "block");
	$('#popup_offer_props').html("");
	$("#popup_offer_img").html("");
	$("#popup_offer_title").html("");
	$("#popup_offer_buy_button").css("display","none");
	$("#popup_offer_subscribe_button").css("display","none");

	var curImg = $('<img/>', {
		src: $(element).parents(".catalog_item").find(".item_img").attr("src"),
		alt: elementName
	});
	$("#popup_offer_img").append(curImg);

	$("#popup_offer_title").html(elementName);


	var firstOffer = false;
	for (var curOffer in offers)
	{
		var mylist = $('<div/>', {
			"class":"tovar_buy_content"
		});
	//offer container
		var sku_container = $('<div/>', {
			class:"tovar_sku"+(firstOffer==false ? " active" : ""),
			id:curOffer,
			click:function()
			{
				$('.tovar_buy_content .tovar_sku').removeClass('active');
				$(this).addClass('active');

				var offerTmp = offers[$(this).attr("id")];

				if (offerTmp["CAN_BUY"])
				{
					$("#popup_offer_buy_button").css("display","block");
					$("#popup_offer_buy_button .tovar_buy_button").unbind('click');
					$("#popup_offer_subscribe_button").css("display","none");
					$("#popup_offer_buy_button .tovar_buy_button").attr("href", offerTmp.ADD_URL);
					$("#popup_offer_buy_button .tovar_buy_button").click(function(){
						$("#buy_layer_1").css("display", "none");
						$(".layer_bg").css("display", "none");
						return add2basket(this, elementName, cartPath);
					});
				}
				else
				{
					$("#popup_offer_buy_button").css("display","none");
					$("#popup_offer_subscribe_button .tovar_mail_button").unbind('click');
					$("#popup_offer_subscribe_button").css("display","block");
					if (BX.message["USER_ID"] > 0)
					{
						$("#popup_offer_subscribe_button .tovar_mail_button").attr("href", offerTmp.SUBSCRIBE_URL);
						$("#popup_offer_subscribe_button .tovar_mail_button").click(function(){
							$("#buy_layer_1").css("display", "none");
							$(".layer_bg").css("display", "none");
							add2subscribe(this, elementName);
							return false;
						});
					}
					else
					{
						$("#popup_offer_subscribe_button .tovar_mail_button").addClass("unactive");
						$("#popup_offer_subscribe_button .tovar_mail_button").click(function(){
							subscribePopup(this);
							return false;
						});
					}
				}

			}
		});

		if (firstOffer==false)
		{
			if (offers[curOffer]["CAN_BUY"])
			{
				$("#popup_offer_buy_button").css("display","block");
				$("#popup_offer_subscribe_button").css("display","none");
				$("#popup_offer_buy_button .tovar_buy_button").attr("href", offers[curOffer].ADD_URL);
				$('#popup_offer_buy_button .tovar_buy_button').click(function(){
					$("#buy_layer_1").css("display", "none");
					$(".layer_bg").css("display", "none");
					return add2basket(this, elementName, cartPath);
				});
			}
			else
			{
				$("#popup_offer_buy_button").css("display","none");
				$("#popup_offer_subscribe_button").css("display","block");
				if (BX.message["USER_ID"] > 0)
				{
					$("#popup_offer_subscribe_button .tovar_mail_button").attr("href", offerTmp.SUBSCRIBE_URL);
					$("#popup_offer_subscribe_button .tovar_mail_button").click(function(){
						$("#buy_layer_1").css("display", "none");
						$(".layer_bg").css("display", "none");
						add2subscribe(this, elementName);
						return false;
					});
				}
				else
				{
					$("#popup_offer_subscribe_button .tovar_mail_button").addClass("unactive");
					$("#popup_offer_subscribe_button .tovar_mail_button").click(function(){
						return false;
					});
				}
			}
		}

		var ullist = $('<ul/>'); //for props

		for (var prop in offers[curOffer].PROPS)
		{
			var mybutton = $('<li/>', {
				id : curOffer,
				click: function () {},
			});

			$("<b/>",{
				text: offers[curOffer].PROPS[prop].PROP_NAME + ": "
			}).appendTo(mybutton);
			$("<span/>",{
				text: offers[curOffer].PROPS[prop].PROP_VALUE,
			}).appendTo(mybutton);
			mybutton.appendTo(ullist);

		}

		ullist.appendTo(sku_container);
		


		var pricelist = $('<div/>', {
			"class":"sku_prices"
		});

		for (var price in offers[curOffer].PRICES)
		{
			if ( offers[curOffer].PRICES[price]["TITLE"])
			{
				$("<span/>", {"class":"price_name_sku", text: offers[curOffer].PRICES[price]["TITLE"]}).appendTo(pricelist);
				$("<br/>").appendTo(pricelist);
			}
			if (offers[curOffer].PRICES[price]["DISCOUNT_PRICE"] != "")
			{
				$("<span/>", {"class":"price_sku new_price_sku", text: offers[curOffer].PRICES[price]["DISCOUNT_PRICE"]}).appendTo(pricelist);
				$("<br/>").appendTo(pricelist);
				$("<span/>", {"class":"old_price_sku", text: offers[curOffer].PRICES[price]["PRICE"]}).appendTo(pricelist);
			}
			else
			{
				$("<span/>", {"class":"price_sku", text: offers[curOffer].PRICES[price]["PRICE"]}).appendTo(pricelist)
			}
		}


		pricelist.appendTo(sku_container);
		$('<div/>', {
			"class":"splitter"
		}).appendTo(sku_container);

//compare
		if (offers[curOffer]["DISPLAY_COMPARE"])
		{
			$('<a/>', {
				text: BX.message('COMPARE_ADD'),
				"class":"tovar_item_compare",
				href: offers[curOffer]["COMPARE_URL"],
				click:function()
				{
					$("#buy_layer_1").css("display", "none");
					$(".layer_bg").css("display", "none");
					return add2compare(this, elementName, offers[curOffer]["COMPARE_PATH"]);
				}
			}).appendTo(sku_container);
		}

		sku_container.appendTo(mylist);
		mylist.appendTo($('#popup_offer_props'));

		firstOffer = true;
	}
}

function add2basket(element, element_name, cart_path)
{
	if (!element && !element.href)
		return;

	$.gritter.add({
		title: BX.message('ITEM_ADDED_TO_CART'),
		text: element_name+'<br><a href="'+cart_path+'" class="message_buy_btn">'+BX.message('ORDER_ITEM')+'</a>',//<a href="#" class="message_one_click_buy"> упить в 1 клик</a>',
		sticky: true
	});

	var href = element.href;
	if (href)
		$.get( href+"&ajax_buy=1", function (data){
				$("#cart").html(data);
			}
		);
	return false;
}

function add2subscribe(element, element_name)
{
	if (!element && !element.href)
		return;

	$.gritter.add({
		title: BX.message('SUBSCRIBED'),
		text: element_name,
		sticky: false,
		time: '10000'
	});

	var href = element.href;
	if (href)
		$.get( href+"&ajax_buy=1", function (){}
		);
	return false;
}

function subscribePopup (button)
{
	var SubscribePopup = BX.PopupWindowManager.create('subscribe_window', button, {
		content: '<p class="tovar_pop_up">'+BX.message('CATALOG_SUBSCRIBE_INACT')+BX.message('CATALOG_SUBSCRIBE_INACT2')+'</p>',
		//offsetLeft:-10,
		//offsetTop:7,
		zIndex:9999,
		offsetTop:9,
		angle : true,
		autoHide:true
	});

	SubscribePopup.show();
}

function add2compare(element, element_name, compare_path)
{
	if (!element && !element.href)
		return;

	$.gritter.add({
		title: BX.message('COMPARE_DESCR'),
		text: element_name+'<br><a href="'+compare_path+'" class="message_compare_btn">'+BX.message('COMPARE_PATH')+'</a>',
		sticky: false,
		time: '10000'
	});

	var href = element.href;
	if (href)
		$.get( href+"&ajax_compare=1&backurl="+ decodeURIComponent(window.location.pathname), function (data){
				var compare = $("#compare");
				compare.html(data);

				if (compare.css("display") == "none") {
					compare.css({ "display": "block"});
				}
			}
		);
	return false;
}