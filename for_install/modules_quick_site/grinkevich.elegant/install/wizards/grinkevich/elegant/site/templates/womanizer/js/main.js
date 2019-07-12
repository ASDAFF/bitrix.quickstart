var slider = new Array();

$(function() {
	try {
		$("a[rel^='prettyPhoto']").prettyPhoto({theme : 'facebook', deeplinking : false});
	} catch(e){};

	$("#zoom").click(function() {
		$('#main-image').click();
	});





	$("a#fn-close").live('click',function(){
		$.fancybox.close();
		return false;
	});

	$("#loginFancy").fancybox({
			'hideOnContentClick': false,
			'zoomSpeedIn': 300,
			'zoomSpeedOut': 300,
			'overlayShow': true,
			'margin'			: 0,
			'padding'			: 0,
			'autoScale'			: false,
			'autoDimensions'	: true,
			'scrolling'			: 'no',
			'opacity'			: true,
			'showCloseButton'	: false,
			'transitionIn'		: 'elastic',
			'transitionOut'		: 'elastic',
			'overlayOpacity'	: '0.7',
			'overlayColor'		: '#000',
			'centerOnScroll'	: false,
			'enableEscapeButton': true,
			'href'				: '#SITE_DIR#includes/login.php'
	});


	$('.goto-select').change(function(){		document.location.href = $(this).val();	});

	$('.chzn-select').bind("change", function(){
		var inpid = $(this).data("inpid");
		var inpname = $(this).find("OPTION:selected").data("name");
		var inpval = "Y";

		$("#in" + inpid).attr("name", inpname);
		$("#in" + inpid).val( inpval );

	}).trigger('change');

	$('#filter-catalog-sections-select').change(function(){		$('#smart-filter-form').attr('action', $(this).val());	}).trigger('change');

	$("#slider").nivoSlider();



//	var ci = $.cookie('cart');
//	$('#cart-items').val(ci);

	var ci = $('#cart-items').val();
	var aci = ci.split(',');
	for (var i = 0; i < aci.length; i++)
	{		$(".dataitem-" + aci[i].replace('id', '')).addClass("added");
		$('.added .pay a[rel=' + aci[i] + ']').text( lang_in_cart ).addClass("payed").parent().css({
					width: "55px",
					marginRight: "10px"
		});	}

//	alert( $.cookie('cart') );


	$('a.button, a.form-trigger').live('click', function() {
  		if ($(this).attr('rel')) {
   			$('#' + $(this).attr('rel')).submit();
  		}
 	});

 	$('.ajax-form').bind('submit', function(){
  		var t = $(this);
        var r = $('.ajax-result', t);

  		if (t.attr('locked') == 1) return false;
  		t.attr('locked', 1);

  		var form = t.serialize();
		$("input", t).attr("disabled", "disabled");
  		r.hide();

  		$.post('/api/' + t.attr('action') + '?' + Math.random(), form,
			function(data) {
		    	t.attr('locked', 0);

		    	$("input", t).removeAttr("disabled");

		    	r.addClass(data.status).html(data.message).show();

		    	if (data.status != 'error') {
		     		$(".pop-form div", t).hide();
		    	}

		    	if (data.refresh)
		    		document.location.href = document.location.href;
		   	},
		   	'json'
		);

		return false;
	});
	$("#cart-table .count-changer").change(function(e) {
		var val = parseInt(this.value, 10);
	   	if (val) {
	  		var price = $(this).attr("rel");
	    	var res = $(this).parent().next().find("strong.red strong");
	    	price = price * val;
	    	price = setBackspaces(price + "");
	    	res.text(price);
	    	setTotalPrice();
			this.value = val;
	   	}
		else {
			this.value = "";
		}

	   	function setTotalPrice()
	   	{
	    	var prices = $("#cart-table strong.red strong");
	    	var totalPrice = $(".total-price strong");
	    	var tp = 0;

	    	for (var i = 0; i < prices.length; i++) {
	     		var p = prices.eq(i).text().split(' ').join('')*1;
	     		tp += p;
	    	}
	    	totalPrice.text(setBackspaces(tp+""));
	   	}
	 });

	$("#left-menu > li i").click(function() {
		if (this.parentNode.className == "opened") {
			$("ul", this.parentNode).slideUp();
			this.parentNode.className = "";
		}
		else {
			$("ul", this.parentNode).slideDown();
			this.parentNode.className = "opened";
		}
	});

    $('.p-list').jcarousel({
		wrap: "circular",
		visible: 3,
		scroll: 1,
		initCallback: initSlider
	});

	$(".p-list li .item, #c-list li .item").hover(
		function() {
			if (!$(this).hasClass("added"))
				$(".pay", this).stop().animate({width: "55px", marginRight: "10px"}, 200)
		},
		function() {
			if (!$(this).hasClass("added"))
				$(".pay", this).stop().animate({width: "0px", marginRight: "0px"}, 200)
		}
	);

	$('#mi-thumbs').jcarousel({
		wrap: "circular",
		visible: 4,
		scroll: 1
	});

	if (document.getElementById("min-val") != null) {
		numSliderInit();
	}

	$("#right-form select, #right-form input[type=checkbox]").change(function() {
		submit(this);
	});
	$("#right-form input[type=text]").blur(function() {
		submit(this);
	});



	$("#overlay, #imagePopup .image .close").click(function() {
		closeImagesPopup();
	});



	$(".p-list .item .pay a, #c-list .item .pay a").click(function() {
		animateCart(this);
		el = $(this).parent().parent().find("strong");
		var curPrice = el.attr('pprice') * 1;
		var cartPrice = $("#price").attr("cprice") * 1;
		var priceCount = $("#priceCount").attr("ccount")*1;
		var pId = $(this).attr('rel');

		 $("#def-cart-mess").hide();
         $("#tp-info").show();




		var moveUrl = "#SITE_DIR#includes/basket_full.php?action=ADD2BASKET&id=" + pId.replace('id', '');
			//alert(moveUrl);
			$.ajax({
				url: moveUrl,
				type: "GET",
				cache	: false,
				success: function(data){					//
				}
			});




		if (!$(this).parents(".item").hasClass("added")) {
			priceCount += 1;
			$("#priceCount").attr("ccount", priceCount).text(priceCount + " " + ruscomp(priceCount, lang_cart_products));

			if (!$("#cart-items").val()) {
		    	$("#cart-items").val(pId);
		   	}
		   	else {
		    	$("#cart-items").val($("#cart-items").val() + "," + pId);
		   	}

			cartPrice += curPrice;
			$("#price").attr("cprice", cartPrice).text(setBackspaces(cartPrice));
			$(".p-list .item .pay a[rel=" + pId + "], #c-list .item .pay a[rel=" + pId + "]").each(function() {
				$(this).text( lang_in_cart ).addClass("payed").parent().css({
					width: "55px",
					marginRight: "10px"
				});
				$(this).parents(".item").addClass("added");
			});

		   	$.cookie('cart', $("#cart-items").val(), { expires: 7, path: '/'});
		   	$.cookie('cartprice', cartPrice, { expires: 7, path: '/'});

		}
	});

	$(".pay-buts .button:not('.notpayed')").click(function() {
		var curPrice = $("#p-rice").attr('pprice') * 1;
		var cartPrice = $("#price").attr("cprice") * 1;
		var priceCount = $("#priceCount").attr("ccount")*1;
		var pId = $(this).attr('ref');

		$("#def-cart-mess").hide();
        $("#tp-info").show();

        var props = '';

        $($(this).attr('props')).each(function(){        	props += '&' + $(this).attr('name') + '=' + $(this).val();        });


		if (!$(this).hasClass("added")) {
			priceCount += 1;
			$("#priceCount").attr("ccount", priceCount).text(priceCount + " " + ruscomp(priceCount, lang_cart_products));
			if (!$("#cart-items").val()) {
				$("#cart-items").val(pId);
			}
			else {
				$("#cart-items").val($("#cart-items").val() + "," + pId);
			}
			cartPrice += curPrice;
			$("#price").attr("cprice", cartPrice).text(setBackspaces(cartPrice));

			$(this).addClass("added").removeClass("orange").find("span").text( $(this).attr('settitle') );
			$(".lnk-q-pay").hide();


			var moveUrl = "#SITE_DIR#includes/basket_full.php?action=ADD2BASKET&id=" + pId.replace('id', '') + props;
			$.ajax({
				url: moveUrl,
				type: "GET",
				cache	: false,
				success: function(data){
					//
				}
			});

		}

	});

	$("#user-cabinet .lnk-change").click(function() {
		var lnk = $(this);
		if (lnk.hasClass("opened")) {
			$("#edit-form").hide();
			$("#skidka").show();
			$("#user-info").show();
			lnk.removeClass("opened").html("<i></i>изменить");
		}
		else {
			$("#skidka").hide();
			$("#user-info").hide();
			$("#edit-form").show();
			lnk.addClass("opened").text("скрыть");
		}
	});

	$(".c-table .lnk-item").click(function() {
		var td = $(this).parents("tr").next().find(".subtd");
		if ($(this).hasClass("opened")) {
			td.find(".z-form").slideToggle(400, function() {
				td.hide();
			});
			$(this).removeClass("opened");
		}
		else {
			td.show();
			td.find(".z-form").slideToggle(400);
			$(this).addClass("opened")
		}
	});

	$(".button, .jcarousel-prev, .jcarousel-next").mousedown(function() {
		$(this).addClass("pushed");
	});
	$(".button, .jcarousel-prev, .jcarousel-next").mouseup(function() {
		$(this).removeClass("pushed");
	});
	$(".button, .jcarousel-prev, .jcarousel-next").mouseout(function() {
		$(this).removeClass("pushed");
	});

});

function ruscomp($number, $compl) {
	$comp = $compl.split(',');

    if ($number==0 || ($number%10)==0) {return $comp[0];}
    if ($number>=5 && $number<=20) {return $comp[0];}
    if ($number%10>=5 && $number%10<=9) {return $comp[0];}
    if (($number%10)==1) {return $comp[1];}
    if (($number%10)>=2 && ($number%10)<=4) {return $comp[2];}
}



function numSliderInit() {
	var minVal = document.getElementById("min-val").value*1;
	var maxVal = document.getElementById("max-val").value*1;
	var mStep = document.getElementById("m-step").value*1;
	var curMinVal = document.getElementById("min-inp").value*1;
	var curMaxVal = document.getElementById("max-inp").value*1;

	var minSlider = $("#min-count").slider({
		value: curMinVal,
		min: minVal,
		max: maxVal,
		step: mStep,
		slide: function(event, ui) {
			$("#min-inp").val(ui.value);
		},
		change: function() {
			checkSliderVals()
		}
	});

	$("#min-inp")
		.val(minSlider.slider("value"))
		.blur(function() {
			var val = this.value;
			if (!(+val)) {
				val = minVal;
				this.value = minVal;
			}
			minSlider.slider("value", val);
			checkSliderVals();
		});


	var maxSlider = $("#max-count").slider({
		value: curMaxVal,
		min: minVal,
		max: maxVal,
		step: mStep,
		slide: function(event, ui) {
			$("#max-inp").val(ui.value);
		},
		change: function() {
			checkSliderVals();
		}
	});

	$("#max-inp")
		.val(maxSlider.slider("value"))
		.blur(function() {
			var val = this.value;
			if (!(+val)) {
				val = maxVal;
				this.value = maxVal;
			}
			maxSlider.slider("value", val);
			checkSliderVals();
		});

	function checkSliderVals() {
		var min = minSlider.slider("value");
		var max = maxSlider.slider("value");

		if (max < min) {
			document.getElementById("min-inp").value = max;
			document.getElementById("max-inp").value = min;
			minSlider.slider("value", max);
			maxSlider.slider("value", min);
		}

		submit(document.getElementById("max-inp"));
	}
}

var smbtTimerId;
function submit(elem) {	/*
	var pos;
	$(elem).parents(".item").each(function() {
		pos = getElementPosition(this);
	});

	clearTimeout(htTimerId);
	clearTimeout(smbtTimerId);
	hideTooltip();
	$("#rp-tooltip").stop().fadeIn("fast").css({right: "215px", top: pos.top + (pos.height - 62)/2});
	smbtTimerId = setTimeout("ajaxEmulation()", 2000);
	return false;
	*/
}

var htTimerId;
function ajaxEmulation() {
	var t = document.getElementById("rp-tooltip");
	$("img", t).hide();
	$("#rpt-col").show();
	htTimerId = setTimeout('hideTooltip()', 3000);
}

function hideTooltip() {
	$("#rpt-col").hide();
	$("#rp-tooltip img").show();
	$("#rp-tooltip").stop().hide();
}

window.onresize = window.onload = function() {
	changeSliderWidth();
}

function changeSliderWidth() {
	var bh;
	if ($("#center-col").length > 0) {
		bh = $("#center-col").width();
	}
	else {
		bh = $("#right-wide-col").width();
	}
	var cItems = $("#c-list li");

	if (slider.length > 0) {
		for (var i = 0; i < slider.length; i++) {
			if (bh > 1029) {
				slider[i].reload();
				slider[i].options.visible = 7;
				slider[i].reload();
			}
			else if (bh > 882) {
				slider[i].reload();
				slider[i].options.visible = 6;
				slider[i].reload();
			}
			else if (bh > 735) {
				slider[i].reload();
				slider[i].options.visible = 5;
				slider[i].reload();
			}
			else if (bh > 588) {
				slider[i].reload();
				slider[i].options.visible = 4;
				slider[i].reload();
			}
			else {
				slider[i].reload();
				slider[i].options.visible = 3;
				slider[i].reload();
			}
		}
	}

	if (cItems.length > 0) {

		if (bh > 1029) {
			cItems.width("14%");
		}
		else if (bh > 882) {
			cItems.width("16%");
		}
		else if (bh > 735) {
			cItems.width("20%");
		}
		else if (bh > 588) {
			cItems.width("25%");
		}
		else {
			cItems.width("32%");
		}
	}
}

function initSlider(carousel) {
	slider.push(carousel);
}

function changeColor(sel) {
	if (sel.options[sel.selectedIndex].value == '') {
		$("#color").hide();
	}
	else {
		$("#color").show().css("background-color", sel.options[sel.selectedIndex].value);
	}
}

var pop;
var popLink;
function getPopup(popId, lnk, isQuickPay) {
	if (popId != null) {
		popLink = lnk;
		pop = document.getElementById(popId);
		pop.style.display = "block";
		addEvent(document, 'click', popClickFunc);

		if (isQuickPay) {
			var pos = getElementPosition(lnk);
			pop.style.top = pos.top - 220 + "px";
			pop.style.left = pos.left - 120 + "px";
			if ($(lnk).parents(".item").length > 0) {
				$("#quick-pay-product-id").val($(lnk).parents(".item").find(".pay a").attr("rel"));
			}
			else {
				$("#quick-pay-product-id").val($("#main-image").attr("ref"));
			}
		}

		if (popId == "login-popup") {
			var pos = getElementPosition(lnk);
			if ($(lnk.parentNode).attr("id") == "lnk-cab") {
				pop.style.position = "fixed";
				pop.style.top = pos.top + pos.height + 2 + "px";
				pop.style.left = pos.left - 254 + $(lnk).width() / 2 + "px";
			}
			else {
				pop.style.top = pos.top + pos.height + 2 + "px";
				pop.style.left = pos.left - 254 + $(lnk).width() / 2 + "px";
			}
		}

	}
}

function popClickFunc(event) {
	var event = event || window.event;
	var t = event.target || event.srcElement;

	if (t != pop && !isChildNode(pop, t) && t != popLink && !isChildNode(popLink, t)) {
		closePopup();
		removeEvent(document, 'click', popClickFunc);
	}
}

function closePopup() {
	$('.popup .pop-form div').show();
 	$('.popup .pop-form p.ajax-result').html('').hide();
	$(".popup").css("display", "none");
	$("#pop-login").show();
	$("#pop-remember").hide();
}

function getImagePopup(imgs, num) {
	if (imgs.length > 0) {
		var pop = $("#imagePopup");
		var imgBlock = pop.find(".image");
		var img = $("#ip-image");

		$("#overlay").height(getDocumentHeight()).show();
		pop.css("top", getBodyScrollTop() + 100).show();

		img.attr("src", imgs[num]).attr("rel", 0);
		img.load(function() {
			imgBlock.height("auto").css("visibility", "visible");
		});
		$("#ip-prev").unbind("click").bind("click", function() {
			changeImage(imgs, "prev");
		});
		$("#ip-next").unbind("click").bind("click",function() {
			changeImage(imgs, "next");
		});
	}
}

function closeImagesPopup() {
	$("#imagePopup, #overlay").hide();
}

function changeImage(imgs, param) {
	var img = $("#ip-image");
	var pop = $("#imagePopup");
	var imgBlock = pop.find(".image");
	var curIndx = parseInt(img.attr("rel"));
	var newIndx;

	if (param == "next") {
		if ((imgs.length - 1) > curIndx) {
			newIndx = curIndx + 1;
		}
		else {
			newIndx = 0;
		}
	}
	else {
		if (curIndx > 0) {
			newIndx = curIndx - 1;
		}
		else {
			newIndx = imgs.length - 1;
		}
	}
	imgBlock.css("visibility", "hidden");
	img.attr("src", imgs[newIndx]).attr("rel", newIndx);
	img.load(function() {
		imgBlock.height("auto").css("visibility", "visible");
	});
}

function animateCart(obj) {
	var img = $(obj).parents("li").find(".img img");
	var pos = getElementPosition(img.get(0));
	var tpl = "<div id='prod-img' style='left: " + (pos.left + 5) + "px; top: " + (pos.top - getBodyScrollTop() + 5) + "px'><img style='width: 100%; height: 100%' src='" + img.attr("src") + "' alt='' /></div>";

	$("body").append(tpl);

	$("#prod-img").animate({width: 0, height: 0, top: 0, left: getClientWidth() - 120}, 500, function() {
		$(this).remove();
	});
}

function setBackspaces(p) {
	p = p.toString();

	m = '';
	a = p.split('.');
	if (a.length > 1)
	{
		p = a[0];
		m = '.' + a[1];
	}

	var n = (p.length - (p.length % 3)) / 3;
	if (p.length % 3 == 0)
		n--;

	var k = 1;
	for (var x = n; x > 0; x--) {
		var j = p.length - k - 3 * k;
		var s1 = p.substr(0, j + 1);
		var s2 = p.substr(j + 1, p.length - 1);
		p = s1 + " " + s2;
		k++;
	}
	return p + m;
}

function getElementPosition(elem) {
    var w = elem.offsetWidth;
    var h = elem.offsetHeight;

    var l = 0;
    var t = 0;

    while (elem) {
        l += elem.offsetLeft;
        t += elem.offsetTop;
        elem = elem.offsetParent;
    }

    return { "left": l, "top": t, "width": w, "height": h };
}

function addEvent(obj, type, fn) {
	if (obj.addEventListener)
		obj.addEventListener(type, fn, false);
	else if (obj.attachEvent)
		obj.attachEvent( "on"+type, fn );
}

function removeEvent(obj, type, fn) {
	if (obj.removeEventListener)
		obj.removeEventListener(type, fn, false);
	else if (obj.detachEvent)
		obj.detachEvent( "on"+type, fn );
}

function isChildNode(elem, sell) {
	for (var childItem in elem.childNodes) {
		if (elem.childNodes[childItem].nodeType == 1) {
			if (elem.childNodes[childItem] == sell)
				return true;
			else if (isChildNode(elem.childNodes[childItem], sell))
				return true;
		}
	}
	return false;
}

function getClientWidth() {
	return document.compatMode == 'CSS1Compat' || window.opera ? document.documentElement.clientWidth : document.body.clientWidth;
}

var ua = navigator.userAgent.toLowerCase();
var isOpera = (ua.indexOf('opera') > -1);
var isIE = (!isOpera && ua.indexOf('msie') > -1);

function getViewportHeight() {
	return ((document.compatMode || isIE) && !isOpera) ? (document.compatMode == 'CSS1Compat') ? document.documentElement.clientHeight : document.body.clientHeight : (document.parentWindow || document.defaultView).innerHeight;
}

function getDocumentHeight() {
	return Math.max(document.compatMode != 'CSS1Compat' ? document.body.scrollHeight : document.documentElement.scrollHeight, getViewportHeight());
}

function getBodyScrollTop() {
  return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
}