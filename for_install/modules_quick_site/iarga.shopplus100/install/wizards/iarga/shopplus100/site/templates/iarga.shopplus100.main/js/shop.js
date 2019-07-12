$(function(){
	init_props();
	init_delivery();

	$(".unitooltip").blur(function(){
		if(tooltip!=0) tooltip.abort();
		var place = $(this);
		if(place.parents("label").length > 0) place = place.parents("label").eq(0);
		if(place.next().hasClass("tooltip_open")) place.next().fadeOut();
	});

	// Change person type
	jQuery(".PERSON_TYPE .input").bind('click',function(){
		var $this = $(this).find("input");
		preloader_start($this);
		$.post(jQuery("#b_template").val()+"/props.php",{"person_type":$this.val()},function(data){
			preloader_stop($this);
			jQuery(".order_props").html(data);
			Custom.init();
			init_delivery();
			init_props();
		});
	});
	jQuery(".PERSON_TYPE input").bind('change keyup',function(){
		preloader_start(this);
		$.post(jQuery("#b_template").val()+"/props.php",{"person_type":this.value},function(data){
			preloader_stop(this);
			jQuery(".order_props").html(data);
			Custom.init();
			init_delivery();
			init_props();
		});
	});



	// Удаление из корзины
	$(".bt_del-cart").click(function(){
		$(".info-amount").load(SITE_DIR+"inc/ajax/basket.php",{"add":$(this).parents(".cell").find(".in_cart").attr("rel"),'num':0},b_loaded);
		$(this).parents(".cell").remove();
		$(".four-columns .clr").remove();
		for (var num_cell = 0; num_cell <= $('.four-columns .cell').length; num_cell+=4) {
			$('.four-columns .cell').eq(num_cell).addClass('first').before('<div class="clr"></div>');
		}
		return false;
	});




	// Добавление в корзину и в избранное
	$.post(SITE_DIR+"inc/ajax/basket.php",{},function(data){
		if(data.length < 1000) $(".info-amount").html(data);
		b_loaded();
	});
	$.post(SITE_DIR+"inc/ajax/compare.php",{},function(data){
		if(data.length < 1000) $(".info-favorites").html(data);
		c_loaded();
	});

	// Добавление в сравнение
	$(".to_fav").live('click',function(){
		if(!$(this).hasClass("in-favorites")) act = 1; else act = 0;
		$(".info-favorites").load(SITE_DIR+"inc/ajax/compare.php",{"add":$(this).attr('data-rel'),"act":act},c_loaded);
		return false;
	});

	// Удление из сравнения
	$(".remove-favorites").bind('click',function(){
		$(".info-favorites").load(SITE_DIR+"inc/ajax/compare.php",{"add":$(this).attr('data-rel'),"act":'0'},c_loaded);
		$(this).parents(".item").remove();	
		if($(".item").length < 1) location.href = '/favorite/';
		return false;
	});

	// Добавление в корзину
	$(".to_cart").live('click',function(){
		$(this).addClass("active").next().find('input').val(1).addClass('focus');
		$(".info-amount").load(SITE_DIR+"inc/ajax/basket.php",{"add":$(this).attr('data-rel'),'num':1},b_loaded);
		return false;
	});


	// Период
	setInterval(function(){
		$.post(SITE_DIR+"inc/ajax/basket.php",{},function(data){
			if(data.length < 1000) $(".info-amount").html(data);
			b_loaded();
		});
		$.post(SITE_DIR+"inc/ajax/compare.php",{},function(data){
			if(data.length < 1000) $(".info-favorites").html(data);
			c_loaded();
		});

	},10000);

	// Смена по мышке
	$(".amount-card input, .manipulation input").bind("focus",function(){$(this).addClass("focus");});
	$(".amount-card input, .manipulation input").bind("blur",function(){$(this).removeClass("focus");});
	$(".amount-card input, .manipulation input").bind("keyup mouseup",function(event){
		if(event.keyCode==38 || event.keyCode==187) $(this).val(getval($(this).val())+1);
		else if(event.keyCode==40 || event.keyCode==189) $(this).val( $(this).val()>1 ? $(this).val()-1:1);
		var id = $(this).attr('data-rel');
		if(id==undefined) id = $(this).parents('.amount-card').attr('data-rel');
		$(".info-amount").load(SITE_DIR+"inc/ajax/basket.php",{"add":id,'num':$(this).val()},b_loaded);
	});

	// Кнопки плюс и минус
	$(".select-number .plus, .select-number .minus").click(function(){
		var val = $(this).parent().find("input").val();
		if($(this).hasClass("plus")) val ++;
		else if($(this).hasClass("minus")) val --;
		if(val < 1) val = 1;
		$(this).parent().find("input").val(val).keyup();
		return false;
	});
	
	// Удаление из корзины
	$(".remove-card, .link-remove-card").bind("click",function(){
		$(this).parent().find("input").val('-1').removeClass('focus').keyup();
		if($(this).closest(".card").length > 0) $(this).closest(".item").remove();
		return false;
	});
	
	if($("input.location").val()!="") $("input.location").click();
});

function init_props(){

	// Change city
	jQuery(".location select").change(function(ev){
		preloader_start(this);
		$.post(jQuery("#b_template").val()+"/delivery.php",{"city":$(this).val()},function(data){
			preloader_stop(this);
			jQuery(".delivery_ajax").html(data);
			Custom.init();
		});
	});
	jQuery(".location select").change();



	jQuery(".discount_code").on("keyup",function(){
		preloader_start(this);
		jQuery(".info-amount").load(SITE_DIR+"inc/ajax/basket.php",{"discount_code":jQuery(this).val()},function(){b_loaded(); preloader_stop();});
	});
}
function init_delivery(){
	// Change delivery type
	$("input[name='delivery']").live('change',function(){
		$(".info-amount").load(SITE_DIR+"inc/ajax/basket.php",{'delivery_price':$(this).attr('data-rel'), 'delivery':$(this).val()},b_loaded);
	});
}

function b_loaded(){
	$(".to_cart").show();
	$(".amount-card input:not(.focus)").each(function(){$(this).parent().hide();});
	$(".info-amount input").each(function(){
		var cell = $(".to_cart[data-rel="+$(this).attr("name")+"]").hide();
		var inp = $(".amount-card[data-rel="+$(this).attr("name")+"]");
		var val = inp.show().find("input:not(.focus)").val($(this).val());
	});	

	$(".numgoods").html($(".info-amount input[name=numgoods]").val());
	$("span.discount_value").html(jQuery(".info-amount input[name=discount_value]").val());
	$(".allgoods").html($(".info-amount input[name=allgoods]").val());
	$(".total-pay strong").html(($(".info-amount input[name=allsumm]").val())+$(".info-amount input[name=valute]").val());
	$(".amount-card input.focus").focus().parent().show().prev().hide();

	if($(".info-amount input").length > 6) $(".total-pay, .info-amount").show();
	else $(".total-pay, .info-amount").hide();
	
	// If in basket
	if($(".card").length > 0) $(".info-amount").hide();

	if($(".card .item").length < 1){ $(".card, .checkout, .total-pay, .checkout-button").hide(); $(".nocard").show();}
	else { $(".card, .checkout, .total-pay, .checkout-button").show(); $(".nocard").hide();}

	// price-delivery
	if($("input.location").val()!="") $("input.location").click();

}

function c_loaded(){
	$("a.to_fav").text($(".to_fav_lang").val()).removeClass("in-favorites");

	if($(".info-favorites input").length > 2) $(".info-favorites").show();
	else $(".info-favorites").hide();
	$(".info-favorites input").each(function(){
		var cell = $(".to_fav[data-rel="+$(this).attr("name")+"]");
		if(cell.length > 0) cell.text($(".in_fav_lang").val()).addClass("in-favorites");
	});	

	// If in favorites
	if($(".send-mail-favorites").length > 0){
		$(".info-favorites").hide();		
	}
}

// City tooltip
function city_keyup(targ){
	if(tooltip!=0) tooltip.abort();
	var place = $(targ);
	if(place.parents("label").length > 0) place = place.parents("label").eq(0);
	if(place.next().hasClass("tooltip_open")){
		var tt = place.next();
	}else{
		var tt = $("<div class='tooltip_open'></div>").insertAfter(place).hide();
	}
	if(ttload!=false){
		ttload.abort();
		ttload = false;
	}
	ttload = $.post($(targ).attr("data-rel"),{'q':$(targ).val()},function(data){	
		tt.html(data);
		if(tt.find("a").length > 0 && $(".location").hasClass("focus")) tt.stop().fadeIn(200,function(){tt.css({'opacity':'1','display':'block'});});
		else tt.stop().fadeOut();
		tt.find("a").click(function(){
			$(this).parents(".tooltip_open").prev().val($(this).html()).keyup();
			$(this).parents(".tooltip_open").fadeOut();
		});
	});
}