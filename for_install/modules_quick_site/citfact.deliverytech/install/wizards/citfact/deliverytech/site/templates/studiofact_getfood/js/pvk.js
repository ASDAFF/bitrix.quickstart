function is_mobile () {
	var mobile = (/ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));
	return mobile;
}
function getClientWidth () {
	return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
}
function getClientHeight () {
	return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
}

function leftmenu () {
	if ((getClientHeight() > parseInt($("#header").height()) + parseInt($("#left_side").height())) && getClientWidth() > parseInt($(".container").width())) {
		if (is_mobile()) {
			$("#left_side").css({ position: "absolute", top: $(document).scrollTop() + "px", left: "10px" });
			$("#left_sideApp").css({ position: "absolute", top: parseInt($('#left_side').height())+15+$(document).scrollTop() + "px", left: "10px" });		
		} else {
			if (window.location.pathname=='/' && $(document).scrollTop() < 340) {
				$("#left_side").css({ position: "absolute", top: 300 + "px", left: "10px"});
				$("#left_sideApp").css({ position: "absolute", top: parseInt($('#left_side').height())+15+300 + "px", left: "10px"});
			} else {
				$("#left_side").css({ position: "fixed", top: parseInt($("#header").height()) + 15 + "px", left: $(".main_container").offset().left + 10 + "px" });
				$("#left_sideApp").css({ position: "fixed", top: parseInt($('#left_side').height())+15+parseInt($("#header").height()) + 15 + "px", left: $(".main_container").offset().left + 10 + "px" });
			}
		}
	} else {
		$("#left_side").css({ position: "absolute", top: "0px", left: "10px" });
		$("#left_sideApp").css({ position: "absolute", top: parseInt($('#left_side').height())+15+"px", left: "10px" });
	}
}
function topmenu () {
	var top = $(document).scrollTop();
	if (top > 40) {
		$("#header .header_menu").stop(true, true).slideUp("fast", function () { leftmenu (); });
		if ($("#header").hasClass("headerAbsolute")) {
			$("#header").animate({top: top + "px"}, 0, "swing");
		} else {
			$("#header").animate({top: 0 + "px"}, 0, "swing");
		}
	}
	if (getClientWidth() > 768 && top <= 40) {
		$("#header .header_menu").stop(true, true).slideDown("fast", function () { leftmenu (); });
	}
	if (is_mobile() || getClientWidth() < parseInt($(".container").width())) {
		$("#header").addClass("headerAbsolute");
	}

	leftmenu ();
}

$(window).on("scroll", function () {
	var top = 0;
	if ($("#header.headerAbsolute").length > 0) {
		top = $(document).scrollTop();
	}
	if ($(document).scrollTop() > parseInt($("#header").height())) {
		$("#header .header_menu").stop(true, true).slideUp("fast", function () { leftmenu (); });
		$("#header").stop(true, true).animate({top: top + "px"}, 0, "swing");
	} else {
		$("#header").stop(true, true).animate({top: "0px"}, 0, "swing");
	}
	if (getClientWidth() > 768 && $(document).scrollTop() <= 40) {
		$("#header .header_menu").stop(true, true).slideDown("fast", function () { leftmenu (); });
	}

	leftmenu ();
});	

$(document).ready(function () {
	topmenu ();
	resize_open_box ();
	main_block_page ();
});
$(window).load(function() {
	topmenu ();
	resize_open_box ();
	main_block_page ();
});
$(window).resize(function () {
	topmenu ();
	resize_open_box ();
	main_block_page ();
});

$(document).on("click", "#left_side .icon, #mobile_menu_list .icon", function () {
	var parent = $(this).closest(".depth_level_1");
	if (parent.hasClass("active")) {
		parent.find("ul").stop(true, true).slideUp("fast", "swing");
		parent.removeClass("active");
	} else {
		parent.find("ul").stop(true, true).slideDown("fast", "swing");
		parent.addClass("active");
	}
	setTimeout(function() { leftmenu(); }, 160);

	return false;
});

function main_block_page () {
	var height = getClientHeight();
	if ($(".wrapper").length > 0) {
		var height_page = $(".wrapper").height();
		if (height >= height_page) {
			height = height - parseFloat($("#main_block_page").offset().top) - parseFloat($("footer").height()) - parseFloat($("footer").css("margin-top")) - parseFloat($("footer").css("margin-bottom"));
			$("#main_block_page").css("min-height", height + "px");
		}
	}

	return false;
}

$(document).ready(function () {
	$(".fancybox").fancybox({
		autoSize : true,
		autoResize : true,
		autoCenter : true,
		openEffect : "fade",
		closeEffect : "fade",
		helpers: {
			overlay: {
				locked: false
			}
		}
	});
	$(".scroll-standard").scrollbar();
});

$(".open_fancybox").fancybox({
	padding : 0,
	type : "iframe",
	autoSize : true,
	autoResize : true,
	autoCenter : true,
	openEffect : "fade",
	closeEffect : "fade",
	nextEffect : "fade",
	prevEffect : "fade",
	helpers: {
		overlay: {
			locked: false
		}
	},
	wrapCSS: "open_popup_body"
});

$(".open_feedback").fancybox({
	autoSize : true,
	autoResize : true,
	autoCenter : true,
	openEffect : "fade",
	closeEffect : "fade",
	helpers: {
		overlay: {
			locked: false
		}
	}
});

$(document).on("click", ".mobile_menu", function () {
	if (!$("#mobile_menu_list").hasClass("active")) {
		$("body, html").animate({ "scrollTop": 0 });
		$("#mobile_menu_list").slideDown("fast", function () {
			$("#mobile_menu_list").addClass("active");
		});
		if ($(".main_banner_big").length > 0) {
			$("#main_block_page").css("margin-top", "0px");
		}
	} else {
		$("#mobile_menu_list").slideUp("fast", function () {
			$("#mobile_menu_list").removeClass("active");
		});
		if ($(".main_banner_big").length > 0) {
			$("#main_block_page").css("margin-top", $(".main_banner_big").height() + "px");
		}
	}

	return false;
});


/* Catalog */
$(document).on("mouseenter", ".item_element", function () {
	if ($(this).find(".hidden_hover_element").length > 0) { $(this).find(".hidden_hover_element").stop(true, true).slideDown("fast"); }
});
$(document).on("mouseleave", ".item_element", function () {
	if ($(this).find(".hidden_hover_element").length > 0) { $(this).find(".hidden_hover_element").stop(true, true).slideUp("fast"); }
});

$(document).on("mouseenter", ".img_box", function () {
	if ($(this).find(".zoom").length > 0) { $(this).find(".zoom").stop(true, true).fadeIn("fast"); }
});
$(document).on("mouseleave", ".img_box", function () {
	if ($(this).find(".zoom").length > 0) { $(this).find(".zoom").stop(true, true).fadeOut("fast"); }
});

function adaptItemScroll (obj) {
	if (obj.length > 0) {
		$(".scroll-wrapper").css("width", $(".content").width() + 10 + "px");
		var shift = 10;
		if ($.browser.webkit) { shift = -5; }
		if (obj.find(".item_element").length > 0) {
			var w = (parseInt(obj.closest(".scroll-standard").width()) - 60 - shift)/3;
			if (w < 250) {
				w = (parseInt(obj.closest(".scroll-standard").width()) - 40 - shift)/2;
				if (w < 250) {
					w = parseInt(obj.closest(".scroll-standard").width()) - 20 - shift;
				}
			}
			obj.find(".item_element").css("width", w + "px");
			obj.css("width", ((w + 20) * obj.find(".item_element").length) - 7 + "px");
			setTimeout(function () {
				if (obj.closest(".section_box").find(".scroll-x").css("display") == "none") {
					obj.closest(".section_box").find(".slide_scroll_left, .slide_scroll_right").hide();
				} else {
					obj.closest(".section_box").find(".slide_scroll_left, .slide_scroll_right").show();
				}
			}, 500);
		}
	}
}
$(document).on("click", ".slide_scroll_left, .slide_scroll_right", function () {
	var obj = $("#"+$(this).parent().find(".section").attr("id"));
	if ($(this).hasClass("slide_scroll_left")) {
		var shift = -1;
	} else {
		var shift = 1;
	}
	var item_w = parseInt(obj.find(".item_element").outerWidth()) + 20;
	var step = Math.round(parseInt(obj.parent().scrollLeft()) / item_w) + shift;
	var left = step * item_w;
	if (left < 0) { left = 0; } else if (left > parseInt(obj.width())) { left = parseInt(obj.width()); }
	obj.parent().stop(true, true).animate({
		scrollLeft: left+"px"
	}, 350);
});

function adaptItemSection (obj) {
	if (obj.length > 0) {
		$(".section_box").css("width", $(".content").width() + 25 + "px");
		if (obj.find(".item_element").length > 0) {
			var w = (parseInt(obj.closest(".section_box").width()) - 75)/3;
			if (w < 250) {
				w = (parseInt(obj.closest(".section_box").width()) - 50)/2;
				if (w < 250) {
					w = parseInt(obj.closest(".section_box").width()) - 25;
				}
			}
			obj.find(".item_element").css("width", w + "px");
		}
	}
}
/* Catalog */

$(document).on("click", ".item_quantity a", function () {
	var obj = $(this).closest(".item_quantity").find("input");
	var t = false;
	if ($(this).hasClass("minus")) {
		if (parseFloat(obj.val()) > 1) {
			obj.val(parseFloat(obj.val()) - 1);
			t = true;
		}
	} else {
		obj.val(parseFloat(obj.val()) + 1);
		t = true;
	}
	if (obj.closest(".basket_items_table").length > 0) {
		$(".basket_items_blocks_item").find('input[name='+obj.attr("name")+']').val(obj.val());
	}
	if (obj.closest(".basket_items_blocks_item").length > 0) {
		$(".basket_items_table").find('input[name='+obj.attr("name")+']').val(obj.val());
	}
	if ((obj.closest(".basket_items_blocks_item").length > 0 || obj.closest(".basket_items_table").length > 0) && t) {
		recalcBasketAjax();
	}
	if (obj.closest(".small_basket_hover_quantity").length > 0 && t) {
		$.ajax({
			data: "update_small_basket=Y&SMALL_BASKET_QUANTITY="+obj.val()+"&SMALL_BASKET_ID="+obj.attr("id").replace("QUANTITY_", "")+"&SMALL_BASKET_OPEN=Y",
			url: $("#small_basket_box").attr("data-path"),
			async: true,
			cache: false,
			success: function (html) {
				$("#small_basket_box").html(html);
				$("#small_basket").addClass("update");
				setTimeout(function() { $("#small_basket").removeClass("update") }, 1000);
				$("#SMALL_BASKET_ORDER_PHONE").inputmask("+7 (999) 999 9999");
			}
		});
	}

	return false;
});

$(document).on("click", ".tabs_header .tabs_head a", function () {
	$(this).closest(".tabs_header").find(".tabs_head").removeClass("active");
	$(this).closest(".tabs_head").addClass("active");
	$(".tabs_body").removeClass("active");
	$($(this).attr("href")).addClass("active");
	if ($(this).closest(".bx_item_detail").hasClass("bx_item_detail_popup")) {
		parent.jQuery.fancybox.update();
	}

	return false;
});

function resize_open_box () {
	var width = getClientWidth ();
	if (width > 960 && width <= 1280) {
		$(".search_box").css("width", parseInt($(".header .container").width()) - parseInt($(".header .logo").width()) - parseInt($(".header .logo").css("margin-left")) - parseInt($(".header .logo").css("margin-right")) - parseInt($(".header .phone").width()) - parseInt($(".header .phone").css("margin-left")) - parseInt($(".header .phone").css("margin-right")) - parseInt($("#small_basket").width()) - parseInt($("#small_basket").css("margin-left")) - parseInt($("#small_basket").css("margin-right")) - parseInt($(".header .search_box").css("margin-left")) - parseInt($(".header .search_box").css("margin-right")) - 120 + "px");
	} else if (width > 480) {
		$(".zoom").removeClass("no_zoom");
	} else if (width <= 480) {
		$(".zoom").addClass("no_zoom");
	}
}

/* OFFERS */
$(document).ready(function () {
	good_box ();
});
function good_box () {
	if ($(".good_box").length > 0) {
		$(".good_box").each(function () {
			if ($(this).find(".offers_item").length > 0) {
				$(this).find(".offers_item .offer_sku").first().click();
			}
		});
	}

	return true;
}
$(document).on("click", ".offer_sku", function () {
	if (!$(this).hasClass("disable")) {
		var tree = jQuery.parseJSON($(this).attr("data-tree"));
		var big_obj = $(this).closest(".good_box");
		var obj = $(this).closest(".offer_item");
		obj.find(".offer_sku").removeClass("active");
		$(this).addClass("active");
		var next = obj.next();
		if (next.hasClass("offer_item")) {
			var values = {};
			var nextValue = 0;
			if (next.find(".offer_sku.active").length > 0) {
				nextValue = next.find(".offer_sku.active").attr("data-prop-value-id");
				next.find(".offer_sku.active").removeClass("active");
			}
			big_obj.find(".offers_item .offer_item").each(function () {
				if ($(this).find(".offer_sku.active").length > 0) {
					values[$(this).find(".offer_sku.active").attr("data-prop-id")] = $(this).find(".offer_sku.active").attr("data-prop-value-id");
				}
			});
			var idNext = {};
			next.find(".offer_sku").addClass("disable");
			for (var key in tree) {
				for (var key2 in values) {
					if (tree[key][key2] == values[key2]) {
						next.find(".offer_sku").each(function () {
							if ($(this).attr("data-prop-value-id") == tree[key][next.attr("data-prop-id")]) {
								$(this).removeClass("disable");
							}
						});
					}
				}
			}
			if (nextValue > 0) {
				var f = false;
				next.find(".offer_sku").each(function () {
					if (!$(this).hasClass("disable") && $(this).attr("data-prop-value-id") == nextValue) {
						$(this).click();
						f = true;
						return false;
					}
				});
				if (!f) {
					next.find(".offer_sku").each(function () {
						if (!$(this).hasClass("disable")) {
							$(this).click();
							return false;
						}
					});
				}
			} else {
				next.find(".offer_sku").each(function () {
					if (!$(this).hasClass("disable")) {
						$(this).click();
						return false;
					}
				});
			}

			//next = next.next();
		} else {
			var values = {};
			big_obj.find(".offers_item .offer_item").each(function () {
				if ($(this).find(".offer_sku.active").length > 0) {
					values[$(this).find(".offer_sku.active").attr("data-prop-id")] = $(this).find(".offer_sku.active").attr("data-prop-value-id");
				}
			});
			var offer_id = 0;
			for (var key in tree) {
				var f = true;
				for (var key2 in values) {
					if (tree[key][key2] != values[key2]) {
						f = false;
					}
				}
				if (f) {
					offer_id = key;
				}
			}
			change_offer_item (offer_id, big_obj);
		}
	}

	return false;
});
function change_offer_item (offer_id, big_obj) {
	if (big_obj.hasClass("bx_item_detail")) {
		var type = "detail";
		var massive = ["main_detail_preview_text", "main_detail_price", "main_detail_text", "main_detail_props", "main_detail_quant"];
	} else {
		var type = "preview";
		var massive = ["main_preview_image", "main_preview_price", "main_preview_props"];
	}
	big_obj.find(".offers_hide").hide();
	if (type == "detail") {
		big_obj.find(".main_detail_slider_box").css({"z-index": "15", "opacity": "0"}).removeClass("active_box");
		if (big_obj.find(".main_detail_slider_" + offer_id).length > 0) {
			big_obj.find(".main_detail_slider_" + offer_id).css({"z-index": "20", "opacity": "1"}).addClass("active_box");
		} else {
			big_obj.find(".main_detail_slider").css({"z-index": "20", "opacity": "1"}).addClass("active_box");
		}
		img_box_height ();
	}
	for (var i = 0; i < massive.length; i++) {
		if (big_obj.find("." + massive[i] + "_" + offer_id).length > 0) {
			big_obj.find("." + massive[i] + "_" + offer_id).show();
		} else {
			big_obj.find("." + massive[i]).show();
		}
	}

	return false;
}
/* OFFERS */

/* BUY BUTTON */
function update_small_basket () {
	$.ajax({
		data: "update_small_basket=Y",
		url: $("#small_basket_box").attr("data-path"),
		async: true,
		cache: false,
		success: function (html) {
			$("#small_basket_box").html(html);
			$("#small_basket").addClass("update");
			setTimeout(function() { $("#small_basket").removeClass("update") }, 1000);
			$("#SMALL_BASKET_ORDER_PHONE").inputmask("+7 (999) 999 9999");
		}
	});

	return false;
}
$(document).on("click", ".show_basket_popup", function () {
	var link = $(this).attr("href");
	var quant = "";
	if ($(this).closest(".good_box").find(".item_quantity input[type='text']").length > 0) { quant = "&" + $(this).closest(".good_box").find(".item_quantity input[type='text']").attr("name") + "=" + $(this).closest(".good_box").find(".item_quantity input[type='text']").val(); }
	var html = '<div class="add_to_basket_box"><div class="head">'+$("#sfp_add_to_basket_head").html()+'</div><div class="img"><img class="radius5" src="'+$(this).attr("data-img")+'" title="'+$(this).attr("data-name")+'"></div><div class="name">'+$(this).attr("data-name")+'</div><div class="pr"><span class="price">'+$(this).attr("data-price")+'<span class="rub">P</span></span></div><div class="nav_buttons"><a href="'+$(this).attr("data-basket")+'" class="button" target="_parent">'+$(this).attr("data-gotobasket")+'</a><a href="javascript: void(0);" class="button_white">'+$(this).attr("data-gotoback")+'</a></div></div>';
	$.fancybox(html, {
		autoSize : true,
		autoResize : true,
		autoCenter : true,
		openEffect : "fade",
		closeEffect : "fade",
		helpers: {
			overlay: {
				locked: false
			}
		}
	});
	$.ajax({
		url: link + quant,
		async: true,
		cache: false,
		success: function(data) {
			update_small_basket ();
		}
	});


	return false;
});
$(document).on("click", ".add_to_basket_box .button_white", function () {
	$.fancybox.close();

	return false;
});
$(document).on("click", ".show_offers_basket_popup", function () {
	var link = $(this).attr("href");
	var quant = "";
	if ($(this).closest(".good_box").find(".item_quantity input[type='text']").length > 0) { quant = "&" + $(this).closest(".good_box").find(".item_quantity input[type='text']").attr("name") + "=" + $(this).closest(".good_box").find(".item_quantity input[type='text']").val(); }
	var html = '<div class="add_to_basket_box good_box"><div class="head">'+$("#sfp_show_offers_head").html()+'</div><div class="img"><img class="radius5" src="'+$(this).attr("data-img")+'" title="'+$(this).attr("data-name")+'"></div><div class="name">'+$(this).attr("data-name")+'</div>'+$("#skuId"+$(this).attr("data-id")).parent().html()+'</div>';
	$.fancybox(html, {
		autoSize : true,
		autoResize : true,
		autoCenter : true,
		openEffect : "fade",
		closeEffect : "fade",
		helpers: {
			overlay: {
				locked: false
			}
		}
	});

	return false;
});
/* BUY BUTTON */

$(document).on("click", "#header .logo", function () {
	if ($(this).find("div").length > 0) { return false; }
});
$(document).ready(function () {
	$("#feedback_form_prop_feedback_formPERSONAL_PHONE").inputmask("+7 (999) 999 9999");
	$("#SMALL_BASKET_ORDER_PHONE").inputmask("+7 (999) 999 9999");
});