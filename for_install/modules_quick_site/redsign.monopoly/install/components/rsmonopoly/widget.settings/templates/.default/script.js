var RSMonopoly_wg_delay = 400;

function RSMWSaveSettings() {
	$('#rsmonopoly_wg').find('.apply').find('i').show();
	var url = '/bitrix/components/rsmonopoly/widget.settings/component.php';
	var data = {
		'AJAX_CALL'			: 'Y',
		'gencolor'			: $('#rsmonopoly_wg').find('.rsmonopoly_colorBlock1').find('.field.hex').find('input').val(),
		'textColorMenu'		: $('#rsmonopoly_wg').find('.rsmonopoly_colorBlock2').find('.field.hex').find('input').val(),
		'headType'			: $('#rsmonopoly_wg').find('.menu_type').find('.checked').data('val'),
		'headStyle'			: $('#rsmonopoly_wg').find('.menu_style').find('.checked').data('val'),
		'blackMode'			: ( $('#rsmonopoly_wg').find('.black_mode').find('.checked').length>0 ? 'Y' : 'N' ),
		// main page settings
		'MSFichi'			: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSFichi.checked').length>0 ? 'Y' : 'N' ),
		'MSCatalog'			: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSCatalog.checked').length>0 ? 'Y' : 'N' ),
		'MSService'			: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSService.checked').length>0 ? 'Y' : 'N' ),
		'MSAboutAndReviews'	: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSAboutAndReviews.checked').length>0 ? 'Y' : 'N' ),
		'MSNews'			: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSNews.checked').length>0 ? 'Y' : 'N' ),
		'MSPartners'		: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSPartners.checked').length>0 ? 'Y' : 'N' ),
		'MSGallery'			: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSGallery.checked').length>0 ? 'Y' : 'N' ),
		'MSSmallBanners'	: ( $('#rsmonopoly_wg').find('.main_settings').find('.MSSmallBanners.checked').length>0 ? 'Y' : 'N' ),
		// catalog page settings
		'filterType'		: $('#rsmonopoly_wg').find('.filter_type').find('.checked').data('val'),
		'sidebarPos'		: ( $('#rsmonopoly_wg').find('.sidebarPos').hasClass('checked') ? $('#rsmonopoly_wg').find('.sidebarPos').data('val2') : $('#rsmonopoly_wg').find('.sidebarPos').data('val1') ),
	};
	console.log( data );
	$.ajax({
		type: "POST",
		url: url,
		data: data
	}).done(function( data ) {
		window.location.reload();
	});
}

$(document).ready(function(){

	if( $('#panel').length>0 ) {
		//$('#rsmonopoly_wg .shesterenka').css('top','150px');
		$('#rsmonopoly_wg').css('top','170px');
	}

	$('#colorpickerHolder1').ColorPicker({
		flat: true,
		color: $('#colorpickerHolder1').data('dcolor'),
		onChange: function (hsb, hex, rgb) {
			$('.rsmonopoly_colorBlock1').find('.field.r').find('input').val( rgb.r );
			$('.rsmonopoly_colorBlock1').find('.field.g').find('input').val( rgb.g );
			$('.rsmonopoly_colorBlock1').find('.field.b').find('input').val( rgb.b );
			$('.rsmonopoly_colorBlock1').find('.field.hex').find('input').val( hex );
		}
	});
	$('#colorpickerHolder2').ColorPicker({
		flat: true,
		color: $('#colorpickerHolder2').data('dcolor'),
		onChange: function (hsb, hex, rgb) {
			$('.rsmonopoly_colorBlock2').find('.field.r').find('input').val( rgb.r );
			$('.rsmonopoly_colorBlock2').find('.field.g').find('input').val( rgb.g );
			$('.rsmonopoly_colorBlock2').find('.field.b').find('input').val( rgb.b );
			$('.rsmonopoly_colorBlock2').find('.field.hex').find('input').val( hex );
		}
	});

	$('#rsmonopoly_wg .shesterenka').on('click',function(){
		if( $('#rsmonopoly_wg').hasClass('opened') ) {
			$('#rsmonopoly_wg').find('.settings').animate({
				width : '0px'
			},RSMonopoly_wg_delay,function(){
				// Animation complete.
				$(this).parents('#rsmonopoly_wg').toggleClass('opened');
				$('#rsmonopoly_wg').find('.shesterenka').animate({
					right: -51
				},RSMonopoly_wg_delay);
			});
		} else {
			$('#rsmonopoly_wg').find('.settings').animate({
				width : '680px'
			},RSMonopoly_wg_delay,function(){
				// Animation complete.
				$(this).parents('#rsmonopoly_wg').toggleClass('opened');
				$('#rsmonopoly_wg').find('.shesterenka').animate({
					right: 0
				},RSMonopoly_wg_delay);
			});
		}
	});

	// settings
	$(document).on('click','#rsmonopoly_wg .menu_type button, #rsmonopoly_wg .radioblock button',function(){
		var $btn = $(this);
		$btn.parent().find('button').removeClass('checked');
		$btn.addClass('checked');
		if($btn.parents('.menu_type').length>0) {
			if($btn.data('val')=='type3') {
				$('#rsmonopoly_wg').find('.menu_style').find('.overlay').show();
			} else {
				$('#rsmonopoly_wg').find('.menu_style').find('.overlay').hide();
			}
		}
	});
	$(document).on('click','#rsmonopoly_wg .switcher button',function(){
		var $btn = $(this);
		$btn.toggleClass('checked');
	});

	$(document).on('click','#rsmonopoly_wg .checkboxes button, #rsmonopoly_wg .black_mode button',function(){
		var $btn = $(this);
		$btn.toggleClass('checked');
	});

	$(document).on('click','#rsmonopoly_wg .apply button',function(){
		RSMWSaveSettings();
	});

	// tabs
	$(document).on('click','.rsmonopoly_tabs .rsmonopoly_headers a',function(){
		$('.rsmonopoly_tabs .rsmonopoly_headers a').removeClass('checked');
		$(this).addClass('checked');
		$('.rsmonopoly_tabs .rsmonopoly_content .rsmonopoly_tab').removeClass('show');
		$( $(this).attr('href') ).addClass('show');
		return false;
	});

	// close by click outside
	$(document).on('click',function(e){
		if( $('#rsmonopoly_wg').hasClass('opened') ) {
			if( $(e.target).parents('#rsmonopoly_wg').length>0 ) {

			} else {
				$('#rsmonopoly_wg .shesterenka').trigger('click');
			}
		}
	});

})