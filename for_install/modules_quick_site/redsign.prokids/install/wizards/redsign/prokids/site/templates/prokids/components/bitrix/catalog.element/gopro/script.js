var RSGoPro_DetailBuy1Click = false,
	RSGoPro_DetailCheaper = false,
	RSGoPro_AfterLoading = false;

function RSGoPro_str_replace(search, replace, subject) {
	return subject.split(search).join(replace);
}

function RSGoPro_OnOfferChangeDetail($elementObj) {
	var finedOfferID = $elementObj.find('.js-add2basketpid').val();
	var element_id = $elementObj.data('elementid');
	if( finedOfferID>0 ) {
		// images
		$elementObj.find('.changeimage.imgoffer').hide().removeClass('scrollitem');
		$elementObj.find('.changeimage.imgofferid'+finedOfferID).show().addClass('scrollitem');
		$elementObj.find('.changeimage.imgofferid'+finedOfferID).filter(':first').trigger('click');
		RSGoPro_ScrollReinit('.d_jscrollpane');
		RSGoPro_ScrollReinit('.popd_jscrollpane');
		RSGoPro_ScrollReinit('.prs_jscrollpane');
		setTimeout(function(){
			$elementObj.find('.changeimage:visible:first').trigger('click');
		},50);
	}
}

function RSGoPro_DetailJScrollPaneReinitialize() {
	setTimeout(function(){ // fix for slow shit
		// images
		var pane2api;
		$('.d_jscrollpane').parents('.picslider').removeClass('jspHasScroll');
		$('.d_jscrollpane').each(function(i){
			pane2api = $(this).data('jsp');
			pane2api.reinitialise();
			if( $(this).hasClass('jspScrollable') ) {
				$(this).parents('.picslider').addClass('jspHasScroll');
			}
		});
		// images in fancy
		var pane2api;
		$('.popd_jscrollpane').parents('.picslider').removeClass('jspHasScroll');
		$('.popd_jscrollpane').each(function(i){
			pane2api = $(this).data('jsp');
			pane2api.reinitialise();
			if( $(this).hasClass('jspScrollable') ) {
				$(this).parents('.picslider').addClass('jspHasScroll');
			}
		});
		// prices
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

function RSGoPro_FancyImagesOnUpdate() {
	setTimeout(function(){ // fix for slow shit
		$('.fancygallery').find('.image .max').css('maxHeight', parseInt($('.fancygallery').parents('.fancybox-inner').height())-5 );
		$('.fancygallery').find('.slider .max').css('height', parseInt($('.fancygallery').parents('.fancybox-inner').height())-5-60 );
	},50);
}
function RSGoPro_FancyChangeImageFix() {
	var genImageUrl = $('.fancybox-inner').find('.genimage').attr('src');
	$('.fancybox-inner').find('.changeimage').removeClass('selected');
	$('.fancybox-inner').find('.changeimage').each(function(i){
		if( genImageUrl==$(this).find('img').data('bigimage') ) {
			$(this).addClass('selected');
			RSGoPro_ScrollGoToElement( $(this) );
			return false;
		}
	});
}

function RSGoPro_ScrollToSelector(selector) {
	var scrollTopPos = $( selector ).offset().top + 'px';
	if(!RSDevFunc_PHONETABLET) {
		$('html,body').animate({
			scrollTop: scrollTopPos
		},500);
	} else {
		$('html,body').scrollTop( scrollTopPos )
	}
}

$(document).ready(function(){
	
	// zoom
	if(RSDevFunc_PHONETABLET) {
		$('.elementdetail').find('.zoom').hide();
	}
	
	// add this element to viewed list
	$(window).load(function(){
		setTimeout(function(){
			var viewedUrl = '/bitrix/components/bitrix/catalog.element/ajax.php';
			var viewedData = {
				AJAX		: 'Y',
				SITE_ID		: SITE_ID,
				PARENT_ID	: $('.elementdetail').data('elementid'),
				PRODUCT_ID	: $('.elementdetail').find('.js-add2basketpid').val()
			};
			$.ajax({
				type: 'POST',
				url: viewedUrl,
				data: viewedData
			}).done(function(response){
				console.warn( 'Element add to viewed' );
			}).fail(function(){
				console.warn( 'Element can\'t add to viewed' );
			});
		},500);
	});
	
	// change general image
	$(document).on('click','a.changeimage', function(){
		var $curLink = $(this);
		if( $curLink.parents('.d_jscrollpane').length>0 ) {
			var $jscrollpane = $curLink.parents('.d_jscrollpane');
		} else {
			var $jscrollpane = $curLink.parents('.popd_jscrollpane');
		}
		$jscrollpane.find('a.changeimage').removeClass('selected');
		var bigimage = $curLink.addClass('selected').find('img').data('bigimage');
		if( bigimage!='undefined' && bigimage!='' ) {
			$curLink.parents('.changegenimage').find('.genimage').attr('src', bigimage );
			RSGoPro_ScrollGoToElement( $curLink );
		}
		return false;
	});
	// set selected on general image
	var genImageUrl = $('.elementdetail').find('.genimage').attr('src');
	$('.elementdetail').find('.sliderin').find('.changeimage').removeClass('selected');
	$('.elementdetail').find('.sliderin').find('.changeimage').each(function(i){
		if( genImageUrl==$(this).find('img').data('bigimage') ) {
			$(this).addClass('selected');
			return false;
		}
	});
	
	// jScrollPane -> images and prices
	RSGoPro_ScrollInit('.d_jscrollpane');
	RSGoPro_ScrollInit('.popd_jscrollpane');
	RSGoPro_ScrollInit('.prs_jscrollpane');
	$(window).resize(function(){
		RSGoPro_ScrollReinit('.d_jscrollpane');
		RSGoPro_ScrollReinit('.popd_jscrollpane');
		RSGoPro_ScrollReinit('.prs_jscrollpane');
	});
	
	// Fancybox -> gallery
	if(!RSDevFunc_PHONETABLET) {
		$(document).on('click','.glass_lupa',function(){
			$.fancybox.open(
				$('.fancyimages'),
				{
					type			: 'inline',
					width			: '100%',
					height			: '100%',
					autoSize		: false,
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
					beforeShow		: function(){ RSGoPro_FancyImagesOnUpdate(); },
					afterShow		: function(){ RSGoPro_DetailJScrollPaneReinitialize();RSGoPro_FancyChangeImageFix(); },
					onUpdate		: function(){ RSGoPro_FancyImagesOnUpdate();RSGoPro_DetailJScrollPaneReinitialize(); }
				}
			);
			
			return false;
		});
		
		// stores
		$('.genamount:not(.cantopen)').fancybox({
			maxWidth		: 800,
			maxHeight		: 600,
			minHeight		: 25,
			fitToView		: false,
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
			}
		});
	} else {
		$(document).on('click','.genamount:not(.cantopen)',function(){
			var id = $(this).attr('href');
			$(id).toggleClass('noned').removeAttr('style');
			return false;
		});
	}
	
	// tabs
	$(document).on('click','.tabs .switcher',function(){
		var $switcher = $(this);
		var $tabs = $switcher.parents('.tabs');
		var id = $switcher.attr('href');
		$tabs.find('.switcher').removeClass('selected');
		$tabs.find('.content').removeClass('selected');
		$tabs.find('.switcher[href="'+id+'"]').addClass('selected');
		$tabs.find(id).addClass('selected');
		if(RSGoPro_AfterLoading) {
			if(RSDevFunc_PHONETABLET && $switcher.parent().hasClass('headers')==false) {
				setTimeout(function(){ // fix for slow shit
					var scrollTop = $switcher.offset().top - 8;
					$('html,body').scrollTop(scrollTop);
				},50);
			}
			$(document).trigger('detaltabchange');
			var scrollV = document.body.scrollTop;
	        var scrollH = document.body.scrollLeft;
			document.location.hash = RSGoPro_str_replace('#','',id);
			document.body.scrollTop = scrollV;
	        document.body.scrollLeft = scrollH;
	    }
		return false;
	});
	$(document).on('click','.anchor .switcher',function(){
		RSGoPro_ScrollToSelector( '.contents .switcher[href="'+$(this).attr('href')+'"]' );
		$(document).trigger('detaltabchange');
		return false;
	});
	$(window).load(function(){
		var r = RSDevFunc_GetUrlVars()['result'];
		if( r ) {
			r = r.substr(0,r.indexOf('#'));
		}
		if( window.location.hash=='#postform' || (r && r=='reply')	) {
			$('.detailtabs.tabs').find('.switcher[href="#review"]').trigger('click');
		} else if( $('.detailtabs').find('.switcher[href="'+window.location.hash+'"]').length>0 ) {
			$('.detailtabs.tabs').find('.switcher[href="'+window.location.hash+'"]').trigger('click');
		} else {
			$('.detailtabs.tabs').find('.switcher:first').trigger('click');
		}
		$('.detailtabs.anchor').find('.switcher:first').addClass('selected');
		RSGoPro_AfterLoading = true;
	});
	$(window).on('hashchange', function(){
		if(RSGoPro_AfterLoading) {
			$('.detailtabs.tabs').find('.switcher[href="'+window.location.hash+'"]').trigger('click');
		}
	});
	// tabs -> add review
	$(document).on('click','.add2review',function(e){
		e.stopPropagation();
		$('#detailreviews').find('.reviewform').toggleClass('noned');
		return false;
	});
	
	// change offer
	$(document).on('RSGoProOnOfferChange',function(e,elementObj){
		RSGoPro_OnOfferChangeDetail(elementObj);
		if( $('.elementdetail').find('.soloprice').length>0 ) {
			if( $('.elementdetail').find('.soloprice').find('.discount').html()=='' ) {
				$('.elementdetail').find('.soloprice').find('.hideifzero').hide();
			} else {
				$('.elementdetail').find('.soloprice').find('.hideifzero').show();
			}
		}
	});
	
	// buy1click
	$(document).on('click','.buy1click.detail',function(e){
		RSGoPro_DetailBuy1Click = true;
	});
	// buy1click - put data to form
	$(document).on('RSGoProOnFancyBeforeShow',function(){
		if(RSGoPro_DetailBuy1Click) {
			var value = '';
			value = BX.message("RSGoPro_PROD_ID") + ': ' + $('.elementdetail').find('.js-add2basketpid').val() + '\n' +
				BX.message("RSGoPro_PROD_NAME") + ': ' + $('.elementdetail').data('elementname') + '\n' +
				BX.message("RSGoPro_PROD_LINK") + ': ' + window.location.href + '\n' +
				'-----------------------------------------------------';
			$('.fancybox-inner').find('textarea[name="RS_AUTHOR_ORDER_LIST"]').text( value );
		}
		RSGoPro_DetailBuy1Click = false;
	});
	
	// cheaper
	$(document).on('click','.cheaper.detail',function(e){
		RSGoPro_DetailCheaper = true;
	});
	// cheaper - put data to form
	$(document).on('RSGoProOnFancyBeforeShow',function(){
		if(RSGoPro_DetailCheaper) {
			var value = '';
			value = BX.message("RSGoPro_DETAIL_CHEAPER_TITLE") + '\n' +
				+ '\n' +
				BX.message("RSGoPro_DETAIL_PROD_ID") + ': ' + $('.elementdetail').find('.js-add2basketpid').val() + '\n' +
				BX.message("RSGoPro_DETAIL_PROD_NAME") + ': ' + $('.elementdetail').data('elementname') + '\n' +
				BX.message("RSGoPro_DETAIL_PROD_LINK") + ': ' + window.location.href + '\n' +
				'-----------------------------------------------------';
			$('.fancybox-inner').find('textarea[name="RS_AUTHOR_COMMENT"]').text( value );
		}
		RSGoPro_DetailCheaper = false;
	});
	
	$(document).on('click','.go2detailfrompreview',function(){
		$('.detailtabs.tabs').find('.switcher[href="#detailtext"]').trigger('click');
		RSGoPro_ScrollToSelector( '.switcher[href="#detailtext"]' );
		return false;
	});
	
});