var RSGoPro_AJAXPAGES_processing = false;

function RSGoPro_PutJSon(json,$linkObj,ajaxpagesid) {
	if(json.TYPE=='OK') {
		if(ajaxpagesid && ajaxpagesid==json.IDENTIFIER) {
			if(json.HTML.catalognames) {
				$('#'+ajaxpagesid).find('.names > tbody > tr:last').after( json.HTML.catalognames );
			}
			if(json.HTML.catalogproducts) {
				$('#'+ajaxpagesid).find('.products > tbody > tr:last').after( json.HTML.catalogproducts );
			}
			if(json.HTML.showcaseview) {
				$('#'+ajaxpagesid).find('#showcaseview').append( json.HTML.showcaseview );
			}
			if($linkObj && json.HTML.catalogajaxpages) {
				$linkObj.parents('.ajaxpages').replaceWith( json.HTML.catalogajaxpages );
			} else if($linkObj) {
				$linkObj.parents('.ajaxpages').remove();
			}
		} else {
			console.warn( 'PutJSon -> no ajaxpages' );
		}
		if(json.HTMLBYID) {
			for(var key in json.HTMLBYID) {
				if( $('#'+key) ) {
					$('#'+key).html( json.HTMLBYID[key] );
				}
			}
		}
	} else {
		console.warn( 'PutJSon -> request return error' );
	}
}

// AjaxPages
function RSGoPro_AjaxPages(linkObj) {
	if(linkObj.parent().hasClass('animation')) {
		linkObj.parent().removeClass('animation');
		// if that was table - repaint lines
		var $div = $( '#'+linkObj.data('ajaxpagesid') );
		if( $div.length>0 && $div.find('.artables').length>0 && $div.find('.artables .names > tbody > tr').length>1 ) {
			var id = 0;
			$div.find('.artables .names > tbody > tr').each(function(i){
				id = $(this).data('elementid');
				if( i % 2 == 0 ) {
					$(this).addClass('even');
					$div.find('.artables .products tr.js-elementid'+id).addClass('even');
				} else {
					$(this).removeClass('even');
					$div.find('.artables .products tr.js-elementid'+id).removeClass('even');
				}
			});
		}
		// /if that was table - repaint lines
	} else {
		linkObj.parent().addClass('animation');
	}
}

// Area2Darken
function RSGoPro_Area2Darken(areaObj, anim, options) {
	var opt = $.extend( {
		'progressLeft': false,
		'progressTop': false,
    }, options);
	if(!areaObj.hasClass('areadarken')){
		areaObj.addClass('areadarken').css({"position":"relative"}).append('<div class="area2darken"></div>');
		if(anim == 'animashka'){
			areaObj.find('.area2darken').append('<i class="icon animashka"></i>');
			if(opt.progressTop){
				areaObj.find('.animashka').css({'top': opt.progressTop});
			}
		}
	} else {
		areaObj.removeClass('areadarken').removeAttr('style').find('.area2darken').remove();
	}
}

function RSGoPro_SetSet() {
	RSGoPro_SetFavorite();
	RSGoPro_SetCompared();
	RSGoPro_SetInBasket();
}
// set favorite
function RSGoPro_SetFavorite() {
	$('.add2favorite').removeClass('in');
	for(element_id in RSGoPro_FAVORITE) {
		if(RSGoPro_FAVORITE[element_id]=='Y' && $('.js-elementid'+element_id).find('.add2favorite').length>0) {
			$('.js-elementid'+element_id).find('.add2favorite').addClass('in');
		}
	}
}

// set compare
function RSGoPro_SetCompared() {
	$('.add2compare').removeClass('in').html( '<i class="icon pngicons"></i>'+BX.message('RSGOPRO_JS_COMPARE') );;
	for(element_id in RSGoPro_COMPARE) {
		if(RSGoPro_COMPARE[element_id]=='Y' && $('.js-elementid'+element_id).find('.add2compare').length>0) {
			$('.js-elementid'+element_id).find('.add2compare').addClass('in').html( '<i class="icon pngicons"></i>'+BX.message('RSGOPRO_JS_COMPARE_IN') );
		}
	}
}

// set in basket
function RSGoPro_SetInBasket() {
	$('.add2basketform').removeClass('in');
	for(element_id in RSGoPro_INBASKET) {
		if(RSGoPro_INBASKET[element_id]=='Y' && $(".js-add2basketpid[value='"+element_id+"']").length>0) {
			$('.js-add2basketpid[value="'+element_id+'"]').parents('.add2basketform').addClass('in');
		}
		if( parseInt(RSGoPro_INBASKET[element_id])>0 && $('.products').find('.js-add2basketform'+RSGoPro_INBASKET[element_id]).length>0 ) {
			$('.products').find('.js-add2basketform'+RSGoPro_INBASKET[element_id]).addClass('in');
		}
	}
}

// AJAXPAGES
function RSGoPro_AJAXPAGESAuto() {
	$('.ajaxpages.auto').each(function(i){
		var porog = 200,
			$ajaxpObj = $(this);
		var ajaxpOffsetTop = $ajaxpObj.offset().top,
			window_height = $(window).height();
		if( porog>(ajaxpOffsetTop-window.pageYOffset-window_height) && !RSGoPro_AJAXPAGES_processing && !$ajaxpObj.hasClass('') ) {
			$ajaxpObj.find('a').trigger('click');
		}
	});
}

// TIMER
function RSGoPro_TIMER() {	
	var datenow = new Date;
	datenow = (Date.parse(datenow))/1000;
	$('.timer').each(function(index){			
		timetimer1 = $(this).find('.intimer');
		var dateFrom = $(this).data('datefrom');
		var dateTo = $(timetimer1).data('dateto');
		var autoReUse = $(timetimer1).data('autoreuse');
		var timeLimit = dateTo - dateFrom;
		var diff = dateTo - datenow;		
		if(diff < 0 && autoReUse == 'Y'){
			for (lim = 0; lim < 100; lim++) {
				newdateTo = (lim*timeLimit+dateTo) - datenow;
				if(newdateTo > 0){
					diff = newdateTo;
					break;
				}
			}
		}		
		if (diff > 0) {
			var days = parseInt((diff / (60 * 60 ))/24);
			if (days < 10) {
			  days = "0" + days;
			}
			days = days.toString();		
		    var hours = parseInt((diff / (60 * 60 )) % 24);
		    if (hours < 10) {
		      hours = "0" + hours;
		    }
		    hours = hours.toString();	
			var minutes = parseInt(diff / (60)) % 60;
			if (minutes < 10) {
			  minutes = "0" + minutes;
			}
			minutes = minutes.toString();
			var seconds = parseInt(diff) % 60;
			if (seconds < 10) {
			  seconds = "0" + seconds;
			}
			seconds = seconds.toString();
			$(timetimer1).find(".result-day").text(days);
			$(timetimer1).find(".result-hour").text(hours);
			$(timetimer1).find(".result-minute").text(minutes);
			$(timetimer1).find(".result-second").text(seconds);
		} else {
			$(timetimer1).parents('.timer').css('display', 'none');
			$(timetimer1).parents('.js-element').removeClass('da2 qb').find('.price').removeClass('new');
		}
	});
}

// phone mask
function RSGoPro_InitMaskPhone() {
	if( $('.maskPhone').length>0 ) {
		$(".maskPhone").mask("+7 (999) 999-9999");
	}
}

$(document).ready(function(){
	setInterval(function() {
		RSGoPro_TIMER();
	}, 1000);

	$(document).on('RSGoProOnOfferChange', function(e, obj){
		if($(obj).find('.timers').length >0){
			if($(obj).find('.intimer').data('autoreuse') == 'N'){
				var dateNowOfferChange = new Date;
				dateNowOfferChange = (Date.parse(dateNowOfferChange))/1000;
				var dateFromOfferChange = $(obj).find('.timer').data('datefrom');
				var dateToOfferChange = $(obj).find('.intimer').data('dateto');
				if((dateToOfferChange - dateNowOfferChange) < 0){
					$(obj).find('.timers').css('display', 'none');
					$(obj).removeClass('da2 qb');
					$(obj).find('.price').removeClass('new');
				}
			}
		}
	});
	// Click protection
	$(document).on('click','.click_protection',function(e){
		e.stopImmediatePropagation();
		console.warn( 'Click protection' );
		return false;
	});
	// /Click protection
	
	// a -> submit form
	$(document).on('click','form a.submit',function(){
		$(this).parents('form').find('input[type="submit"]').trigger('click');
		return false;
	});
	// /a -> submit form
	
	// AJAX -> add2basket
	$(document).on('submit','.add2basketform',function(){
		var $formObj = $(this);
		var id = parseInt( $formObj.find('.js-add2basketpid').val() );
		if( id>0 ) {
			var seriData = $(this).serialize();
			var url = SITE_DIR+SITE_CATALOG_PATH+'/?'+seriData+'&AJAX_CALL=Y&action=add2basket';
			RSGoPro_Area2Darken( $formObj );
			RSGoPro_Area2Darken( $('#header').find('.basketinhead') );
			$.getJSON(url, {}, function(json){
				if(json.TYPE=='OK') {
					RSGoPro_INBASKET[id] = "Y";
					RSGoPro_SetInBasket();
					RSGoPro_PutJSon( json );
				} else {
					console.warn( 'add2basket - error responsed' );
				}
			}).fail(function(data){
				console.warn( 'add2basket - can\'t load json' );
			}).always(function(){
				RSGoPro_Area2Darken( $formObj );
				RSGoPro_Area2Darken( $('#header').find('.basketinhead') );
			});
		} else if( $formObj.parents('.elementpopup').length<1 ) {
			// id = 0 -> Show popup (if PC)
			if(!RSDevFunc_PHONETABLET) {
				RSGoPro_GoPopup( $formObj.parents('.js-element').data('elementid'), $formObj.parents('.js-element') );
			} else {
				if( $formObj.parents('.js-element').find('.js-detaillink').length>0 ) {
					window.location = 'http://' + window.location.hostname + $formObj.parents('.js-element').find('.js-detaillink').attr('href')
				} else {
					console.warn( 'fail redirect - can\'t find link' );
				}
			}
		} else {
			console.warn( 'add product to basket failed' );
		}
		return false;
	});
	
	// AJAX -> add2compare 
	$(document).on('click','.add2compare',function(){
		var $linkObj = $(this);
		var id = parseInt( $linkObj.parents('.js-element').data('elementid') );
		var action = '';
		if(id>0) {
			RSGoPro_Area2Darken($('.add2compare'));
			if( RSGoPro_COMPARE[id]=='Y' ) { // delete from compare
				action = 'DELETE_FROM_COMPARE_LIST';
			} else {
				action = 'ADD_TO_COMPARE_LIST';
			}
			var url = SITE_DIR+SITE_CATALOG_PATH+'/?AJAX_CALL=Y&action='+action+'&id='+id;
			$.getJSON(url, {}, function(json){
				if(json.TYPE=="OK")
				{
					RSGoPro_PutJSon(json);
					if( action=='DELETE_FROM_COMPARE_LIST' ) // delete from compare
					{
						delete RSGoPro_COMPARE[id];
					} else { // add to compare
						RSGoPro_COMPARE[id] = 'Y';
					}
				} else {
					console.warn( 'compare - error responsed' );
				}
			}).fail(function(data){
				console.warn( 'compare - fail request' );
			}).always(function(){
				RSGoPro_Area2Darken($('.add2compare'));
				RSGoPro_SetCompared();
			});
		}
		return false;
	});
	
	// AJAX -> add2favorite
	$(document).on('click','.add2favorite',function(){
		var $linkObj = $(this);
		var id = parseInt( $linkObj.parents('.js-element').data('elementid') );
		if(id>0)
		{
			RSGoPro_Area2Darken($('.add2favorite'));
			var url = SITE_DIR+SITE_CATALOG_PATH+'/?AJAX_CALL=Y&action=add2favorite&element_id='+id;
			$.getJSON(url, {}, function(json){
				if(json.TYPE=="OK")
				{
					RSGoPro_PutJSon(json);
					if( RSGoPro_FAVORITE[id]=='Y' ) // remove from favorite
					{
						delete RSGoPro_FAVORITE[id];
					} else { // add to favorite
						RSGoPro_FAVORITE[id] = 'Y';
					}
				} else {
					console.warn( 'favorite - error responsed' );
				}
			}).fail(function(data){
				console.warn( 'favorite - fail request' );
			}).always(function(){
				RSGoPro_Area2Darken($('.add2favorite'));
				RSGoPro_SetFavorite();
			});
		}
		return false;
	});

	// AJAXPAGES
	$(document).on('click','.ajaxpages a',function(){
		var $linkObj = $(this);
		var ajaxurl = $linkObj.data('ajaxurl');
		var ajaxpagesid = $linkObj.data('ajaxpagesid');
		var navpagenomer = $linkObj.data('navpagenomer');
		var navpagecount = $linkObj.data('navpagecount');
		var navnum = $linkObj.data('navnum');
		var nextpagenomer = parseInt(navpagenomer) + 1;
		var url = "";
		
		if( $('#'+ajaxpagesid).length>0 && navpagenomer<navpagecount && parseInt(navnum)>0 && ajaxurl!="" ) {
			RSGoPro_AJAXPAGES_processing = true;
			RSGoPro_AjaxPages( $linkObj );
			if(ajaxurl.indexOf("?")<1) {
				url = ajaxurl + '?ajaxpages=Y&ajaxpagesid=' + ajaxpagesid + '&PAGEN_'+navnum+'='+nextpagenomer;
			} else {
				url = ajaxurl + '&ajaxpages=Y&ajaxpagesid=' + ajaxpagesid + '&PAGEN_'+navnum+'='+nextpagenomer;
			}
			$.getJSON(url, {}, function(json){
				RSGoPro_PutJSon( json,$linkObj,ajaxpagesid );
			}).fail(function(json){
				console.warn( 'ajaxpages - error responsed' );
			}).always(function(){
				setTimeout(function(){ // fix for slow shit
					RSGoPro_AJAXPAGES_processing = false;
					RSGoPro_AjaxPages( $linkObj );
				},50);
			});
		} else {
			if( !($('#'+ajaxpagesid).length>0) ) {
				console.warn( 'AJAXPAGES: ajaxpages -> empty DOM element' );
			}
			if( !(navpagenomer<navpagecount) ) {
				console.warn( 'AJAXPAGES: ajaxpages -> navpagenomer !< navpagecount' );
			}
			if( !(parseInt(navnum)>0) ) {
				console.warn( 'AJAXPAGES: ajaxpages -> parseInt(navnum)!>0' );
			}
			if( !(ajaxurl!="") ) {
				console.warn( 'AJAXPAGES: ajaxpages -> ajaxurl is empty' );
			}
		}
		return false;
	});
	//$(window).resize(function(){
	$(window).scroll(function(){
		RSGoPro_AJAXPAGESAuto();
	});
	// /AJAXPAGES
	
	// price table scroll
	$(document).on('click','.prices .arrowtop',function(){
		var $arrow = $(this);
		if($arrow.parent().find('tr').length>3 && !$(this).parent().find('tr:first').is(':visible')) {
			$arrow.parent().find('tr').each(function(i){
				if(!$(this).hasClass('noned')) {
					$arrow.parent().find('tr:eq('+(i-1)+')').removeClass('noned');
					$arrow.parent().find('tr:eq('+(i+2)+')').addClass('noned');
					return false;
				}
			});
		}
		return false;
	});
	$(document).on('click','.prices .arrowbot',function(){
		var $arrow = $(this);
		if($arrow.parent().find('tr').length>3 && !$(this).parent().find('tr:last').is(':visible')) {
			$arrow.parent().find('tr').each(function(i){
				if(!$(this).hasClass('noned')) {
					$(this).addClass('noned');
					$arrow.parent().find('tr:eq('+(i+3)+')').removeClass('noned');
					return false;
				}
			});
		}
		return false;
	});
	
	// disableSelection
	$(document).on('mouseenter mouseleave','.prices .arrowtop, .prices .arrowbot, .js-minus, .js-plus',function(){
		$('html').toggleClass('disableSelection');
	});
	
	// quantity
	$(document).on('click','.js-minus',function(){
		var $btn = $(this);
		var ratio = parseFloat( $btn.parent().find('.js-quantity').data('ratio') );
		var ration2 = ratio.toString().split('.', 2)[1];
		var length = 0;
		if( ration2!==undefined ) { length = ration2.length; }
		var val = parseFloat( $btn.parent().find('.js-quantity').val() );
		if( val>ratio ) {
			$btn.parent().find('.js-quantity').val( (val-ratio).toFixed(length) );
		}
		return false;
	});
	$(document).on('click','.js-plus',function(){
		var $btn = $(this);
		var ratio = parseFloat( $btn.parent().find('.js-quantity').data('ratio') );
		var ration2 = ratio.toString().split('.', 2)[1];
		var length = 0;
		if( ration2!==undefined ) { length = ration2.length; }
		var val = parseFloat( $btn.parent().find('.js-quantity').val() );
		$btn.parent().find('.js-quantity').val( (val+ratio).toFixed(length) );
		return false;
	});
	$(document).on('blur','.js-quantity',function(){
		var $input = $(this);
		var ratio = parseFloat( $input.data('ratio') );
		var ration2 = ratio.toString().split('.', 2)[1];
		var length = 0;
		if( ration2!==undefined ) { length = ration2.length; }
		var val = parseFloat( $input.val() );
		if( val>0 ) {
			$input.val( (ratio*(Math.floor(val/ratio))).toFixed(length) );
		} else {
			$input.val( ratio );
		}
	});
	
	// fancybox -> all
	var RSGoPro_FancyOptions1 = {}, RSGoPro_FancyOptions2 = {};
	RSGoPro_FancyOptions1 = {
		maxWidth		: 900,
		maxHeight		: 600,
		minWidth		: 250,
		minHeight		: 100,
		fitToView		: true,
		autoSize		: true,
		openEffect		: 'none',
		closeEffect		: 'none',
		padding			: 20,
		tpl				: {
			closeBtn : '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"><i class="icon pngicons"></i></a>',
		},
		helpers			: {
			title : {
				type : 'inside',
				position : 'top'
			}
		},
		beforeLoad		: function(){
			RSGoPro_HideAllPopup();
		},
		beforeShow		: function(){
			$('.fancybox-wrap').css({marginLeft: '-10000px'});
			$(document).trigger('RSGoProOnFancyBeforeShow');
		},
		afterShow		: function(){
			setTimeout(function(){
				$.fancybox.toggle();
			},50);
			setTimeout(function(){
				$('.fancybox-wrap').css({marginLeft: '0px'});
				RSGoPro_InitMaskPhone();
			},75);
		}
	};
	$('.fancyajax:not(.big)').fancybox(RSGoPro_FancyOptions1);
	RSGoPro_FancyOptions2 = $.extend({}, RSGoPro_FancyOptions1);;
	RSGoPro_FancyOptions2.width = '80%';
	RSGoPro_FancyOptions2.height = '80%';
	RSGoPro_FancyOptions2.autoSize = false;
	RSGoPro_FancyOptions2.autoHeight = true;
	$('.fancyajax.big').fancybox(RSGoPro_FancyOptions2);

	RSGoPro_InitMaskPhone();

	$(document).on('focus blur','.dropdown-block .bx-ui-sls-fake',function(){
		$(this).parents('.dropdown-block').toggleClass('focus');
	});
	
});