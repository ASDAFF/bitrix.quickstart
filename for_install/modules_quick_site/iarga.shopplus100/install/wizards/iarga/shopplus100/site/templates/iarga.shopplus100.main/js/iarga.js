var tooltip = 0;

$(function(){

	// Size of main page

	if($(".box.view-pictures").length > 0 && $(".box:not(.view-pictures)").length > 0) $(".box.view-pictures").height( $(".catalog-list .box:not(.view-pictures)").height());



	// Save fields

	$(".autosave").find("input, select, textarea").live('mouseup change keyup',function(){

		val = $(this).val();

		if($(this).attr('type')=='checkbox' && !$(this).is(":checked")) val = false; 

		setcookie($(this).parents(".autosave").attr("data-rel")+'_'+$(this).attr('name'),val);

	});





	// Tooltip keys

	$(".tooltip, .location").live('keydown',function(event,form){

		if(event.keyCode==40){

			var act = $(".tooltip_open a.hover").next();

			if(act.length < 1 || act.get(0).tagName!='A') act = $(".tooltip_open a:first");



			act.parent().find("a").removeClass("hover");

			act.addClass("hover");



			return false;

		}else if(event.keyCode==38){

			var act = $(".tooltip_open a.hover").prev();

			if(act.length < 1 || act.get(0).tagName!='A') act = $(".tooltip_open a:last");



			act.parent().find("a").removeClass("hover");

			act.addClass("hover");



			return false;

		}else if(event.keyCode==13){

			var act = $(".tooltip_open a.hover");

			if(act.length > 0){

				if(act.hasClass("section")) location.href = act.attr('href');

				else if(!$(this).hasClass("location")) $(this).val(act.text()).parents('form').submit();

				else{ $(this).val(act.text()); act.parent().fadeOut();}

				return false;

			}

			

		}

	});



	// Filter tooltip

	$(".tooltip").bind('click keyup focus',function(event){		

		if($(this).attr("data-text")!=$(this).val()){

			$(this).attr("data-text",$(this).val());

			if(tooltip!=0) tooltip.abort();

			var place = $(this);

			if(place.parents("label").length > 0) place = place.parents("label").eq(0);

			if(place.next().hasClass("tooltip_open")){

				var tt = place.next();

			}else{

				var tt = $("<div class='tooltip_open'></div>").insertAfter(place).hide();

			}

			tt.load(SITE_DIR+"inc/ajax/tooltip.php",{'q':$(this).val(),"SECTION_ID":place.prev().val(),"IBLOCK_ID":place.prev().prev().val()},function(){			

				if($(this).find("a").length > 0) $(this).stop().fadeIn(200,function(){$(this).css({'opacity':'1','display':'block'});});

				else $(this).stop().fadeOut();

				$(this).find("a").click(function(){

					if(!$(this).hasClass("section")) $(this).parents(".tooltip_open").prev().val($(this).html()).parents("form").submit();

				});

			});

		}

	});

	$(".tooltip, .location").blur(function(){

		if(tooltip!=0) tooltip.abort();

		var place = $(this);

		if(place.parents("label").length > 0) place = place.parents("label").eq(0);

		if(place.next().hasClass("tooltip_open")) place.next().fadeOut();

	});

	$(".tooltip").focus(function(){

		if(tooltip!=0) tooltip.abort();

		var place = $(this);

		if(place.parents("label").length > 0) place = place.parents("label").eq(0);

		if(place.next().hasClass("tooltip_open")) place.next().fadeIn();

	});





	// Youtube

	$(".tubeload").click(function(){

		$(this).css({'position':'relative','z-index':'10000'});

		$(this).load(SITE_DIR+"inc/ajax/tubeload.php?w="+$(this).width()+"&h="+$(this).height()+"&q="+$(this).attr('rel'));		

		return false;

	});



	// To repair blocks of bitrix

	if($("#bx_incl_area_1").length > 0){

		for(i=0;i<=100;i++) $("#bx_incl_area_"+i).css({'display':'inline'});

	}

	var thisParent = $(".item.detail");
	if(thisParent.length){
		var sliderInstance = $('.royalSlider', thisParent).royalSlider({

			fullscreen: {

			  enabled: true,

			  nativeFS: false

			},

			controlNavigation: false,

			autoScaleSlider: true, 

			autoScaleSliderWidth: 960,     

			autoScaleSliderHeight: 650,

			loop: false,

			imageScaleMode: 'fit-if-smaller',

			navigateByClick: true,

			numImagesToPreload:3,

			arrowsNavAutoHide: false,

			arrowsNavHideOnTouch: false,

			keyboardNavEnabled: false,

			fadeinLoadedSlide: true,

			globalCaptionInside: false,

			imageScalePadding: 12

		}).data("royalSlider");			

		$('.img .royalSlider', thisParent).fadeIn(300, function(){

			sliderInstance.updateSliderSize();

		});	
	}

});