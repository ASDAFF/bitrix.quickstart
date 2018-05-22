var RSGOPRO_PopupPrefix = 'rsgppopup_',
	RSGOPRO_DivsLeft = '<div class="outer"><div class="inner">',
	RSGOPRO_DivsRight = '</div></div>',
	RSGOPRO_ParentsObj;

function RSGoPro_OnOfferChangePopup($elementObj) {
	var finedOfferID = $elementObj.find('.js-add2basketpid').val();
	var element_id = $elementObj.data('elementid');
	if(finedOfferID>0) {
		// image
		if( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].IMAGES[0].src && 
			RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].IMAGES[0].src.indexOf("redsign_devfunc_nophoto") < 0 && 
			$elementObj.find('.pic img').length>0 ) {
			$elementObj.find('.pic img').attr('src', RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].IMAGES[0].src );
		}
	}
}

function RSGoProPricesJScrollPaneReinitialize() {
	setTimeout(function(){ // fix for slow shit
		var pane2api;
		$('.prs_jscrollpane').parents('.prices').removeClass('jspHasScroll');
		$('.prs_jscrollpane').each(function(i){
			pane2api = $(this).data('jsp');
			pane2api.reinitialise();
			if( $(this).hasClass('jspScrollable') ) {
				$(this).parents('.prices').addClass('jspHasScroll');
			}
		});
	},50);
}

function RSGoPro_FixPreviewText(element_id) {
	var max_h1 = 350;
	var line_h = 18;
	var h1 = $('#'+RSGOPRO_PopupPrefix+element_id).find('.block.right').outerHeight(true);
	if( h1!=null ) {
		if( h1>max_h1 ) {
			var $text = $('#'+RSGOPRO_PopupPrefix+element_id).find('.description').find('.text');
			var res = Math.floor( ($text.outerHeight(true)-(h1-max_h1))/line_h )*line_h;
			$text.css('maxHeight',res+'px');
		}
	}
}

function RSGoPro_GoPopup(element_id,$parentsObj) {
	element_id = parseInt( element_id );
	RSGOPRO_ParentsObj = $parentsObj;
	if(element_id>0) {
		if( $('#'+RSGOPRO_PopupPrefix+element_id).length>0 ) {
			RSGoPro_ShowPopup(element_id);
		} else {
			RSGoPro_AddPopup(element_id);
		}
	} else {
		console.warn( 'GoPopup: element_id is empty' );
	}
}

function RSGoPro_ShowPopup(element_id) {
	RSGoPro_ChangePosition(element_id);
	$('#'+RSGOPRO_PopupPrefix+element_id).fadeIn("fast",function() {
		// Animation complete
		RSGoPro_FixPreviewText(element_id);
		RSGoPro_SetSet();
		//RSGoProPricesJScrollPaneReinitialize();
		RSGoPro_ScrollReinit('.elementpopupinner .prs_jscrollpane', 1);
	});
}

function RSGoPro_HidePopup(element_id) {
	$('#'+RSGOPRO_PopupPrefix+element_id).fadeOut("fast",function() {
		// Animation complete
	});
}
function RSGoPro_ChangePosition(element_id) {
	var $jsPos;
	if( RSGOPRO_ParentsObj.find('td.name').outerWidth(true) > 5 ) {
		$jsPos = RSGOPRO_ParentsObj.find('.js-position');
	} else {
		$jsPos = RSGOPRO_ParentsObj.parents('.artables').find('.js-name'+element_id).find('.js-position');
	}
	var pos_left = $jsPos.position().left + $jsPos.outerWidth(true) + 20; // 20 - td padding
	$('#'+RSGOPRO_PopupPrefix+element_id).css({'top':$jsPos.position().top+'px','left':pos_left+'px'});
}

function RSGoPro_HideAllPopup() {
	$('.rsgppopup:visible').fadeOut("fast",function() {
		// Animation complete
	});
}

function RSGoPro_AddPopup(element_id,$parentsObj) {
	var url = SITE_DIR+SITE_CATALOG_PATH+'/?AJAX_CALL=Y&action=rsgppopup&element_id='+element_id+'';
	var html = '<div id="'+RSGOPRO_PopupPrefix+element_id+'" class="rsgppopup" style="display:none;">'+RSGOPRO_DivsLeft+'<div class="loading"></div>'+RSGOPRO_DivsRight+'</div>';
	$('body').append( html );
	RSGoPro_ShowPopup(element_id);
	$.ajax({
		url: url
	}).done(function(data){
		$('#'+RSGOPRO_PopupPrefix+element_id).find('.inner').html( data );
		RSGoPro_SetSet();
		RSGoPro_FixPreviewText(element_id);
		if( $('.elementpopupinner .prs_jscrollpane').length>0 ) {
			RSGoPro_ScrollInit('.elementpopupinner .prs_jscrollpane');
			$(window).resize(function(){
				RSGoPro_ScrollReinit('.elementpopupinner .prs_jscrollpane', 1);
			});
		}
	}).fail(function() {
		console.warn( 'Popup: wrong ajax request' );
	});
}

$(document).ready(function(){
	
	// listeners
	$(document).on('keydown',function(e){
		if(e.keyCode==27) { // esc
			RSGoPro_HideAllPopup();
		}
	});
	$(document).on('click',function(e){
		if( $(e.target).parents('.rsgppopup').length>0 ) {
			
		} else {
			RSGoPro_HideAllPopup();
		}
	});
	
	// window.resize
	$(window).resize(function(){
		RSGoPro_HideAllPopup();
	});
	
	// change offer
	$(document).on('RSGoProOnOfferChange',function(e,elementObj){
		RSGoPro_OnOfferChangePopup(elementObj);
	});
	
});