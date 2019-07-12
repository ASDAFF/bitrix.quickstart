var rswidget_setting_delay = 400,
    ball, btn, liColor, blockColor, hrefWidget, colorPick, url, data;

function checkedProp(elem, button) {
  elem.parents(".js-radio").find(button).removeClass('checked');
  elem.addClass('checked');
}

function scrollWidget(classWidget){
  heightWidget = $(classWidget).height() + 170;
  if(heightWidget > $(window).height()) {
    heightWin = $(window).height() - 170;
    $('.widget_blocks.setting_scroll-pane').height(heightWin);
  } else {
    $('.widget_blocks.setting_scroll-pane').height("auto");
  }
}

function RSMWSaveSettings(def) {
    var url = '/bitrix/components/rsflyaway/widget.settings/component.php',
        data = {};
	$('#rswidget_setting').find('.icon_load').css("display","inline-block");
  if(def == "defaultY") {
    data = {
      'AJAX_CALL'        : 'Y',
      'gencolor'        : "ffe062",
      'secondColor'     : "555555",
      'openMenuType'    : "type1",
      'presets'         : "preset_1",
      'bannerType'      : "type1",
      'filterSide'      : "left",
	  'StickyHeader'   : 'N',
      'sidemenuType'    : "dark",
      //'blackMode'       : "N",
      // main page settings
      'Fichi'           : "Y",
      'SmallBanners'    : "Y",
      'New'             : "Y",
      'PopularItem'     : "Y",
      'Service'         : "Y",
      'AboutAndReviews' : "Y",
      'News'            : "Y",
      'Partners'        : "Y",
      'Gallery'         : "Y",
    };
  } else {
    data = {
      'AJAX_CALL'       : 'Y',
      'gencolor'        : $('#rsmw_tab0').find('.field.hex').find('input').val(),
      'secondColor'     : $('#rsmw_tab1').find('.field.hex').find('input').val(),
      'openMenuType'    : $('#rswidget_setting').find('.open_menu_type').find('.checked').data('val'),
      'presets'         : $('#rswidget_setting').find('.presets').find('.checked').data('val'),
      'bannerType'      : $('#rswidget_setting').find('.banner_type').find('.checked').data('val'),
      'filterSide'      : $('#rswidget_setting').find('.filter_side').find('.checked').data('val'),
      'sidemenuType'    : $('#rswidget_setting').find('.sidemenu-type').find('.checked').data('val'),
      'StickyHeader'    : $('#rswidget_setting').find('.header-sticky').find('.checked').length>0 ? 'Y' : 'N',
      //'blackMode'        : ( $('#rswidget_setting').find('.black_mode').find('.checked').length>0 ? 'Y' : 'N' ),
      // main page settings
      'Fichi'           : ( $('#rswidget_setting').find('.Fichi').find('.checked').length>0 ? 'Y' : 'N' ),
      'SmallBanners'    : ( $('#rswidget_setting').find('.SmallBanners').find('.checked').length>0 ? 'Y' : 'N' ),
      'New'             : ( $('#rswidget_setting').find('.New').find('.checked').length>0 ? 'Y' : 'N' ),
      'PopularItem'     : ( $('#rswidget_setting').find('.PopularItem').find('.checked').length>0 ? 'Y' : 'N' ),
      'Service'         : ( $('#rswidget_setting').find('.Service').find('.checked').length>0 ? 'Y' : 'N' ),
      'AboutAndReviews' : ( $('#rswidget_setting').find('.AboutAndReviews').find('.checked').length>0 ? 'Y' : 'N' ),
      'News'            : ( $('#rswidget_setting').find('.News').find('.checked').length>0 ? 'Y' : 'N' ),
      'Partners'        : ( $('#rswidget_setting').find('.Partners').find('.checked').length>0 ? 'Y' : 'N' ),
      'Gallery'         : ( $('#rswidget_setting').find('.Gallery').find('.checked').length>0 ? 'Y' : 'N' ),
    };
  }
	$.ajax({
		type: "POST",
		url: url,
		data: data
	}).done(function(post) {
    $('#rswidget_setting').find('.icon_load').hide();
		window.location.reload();
	});
}

$(document).ready(function(){
	
	
    scrollWidget("#rswidget_setting");
    $(window).on('resize', function(){
	  scrollWidget("#rswidget_setting");
    });

	if( $('#panel').length>0 ) {
		$('#rswidget_setting').css('top','170px');
	}

	$('.colorpickerHolder').each(function(i){
    $(this).ColorPicker({
      flat: true,
      color: $('#colorpickerHolder'+i).data('dcolor'),
      onChange: function (hsb, hex, rgb) {
        colorPick = $(this).parents('.rswidget_content');
        $(colorPick).find('.field.r').find('input').val( rgb.r );
        $(colorPick).find('.field.g').find('input').val( rgb.g );
        $(colorPick).find('.field.b').find('input').val( rgb.b );
        $(colorPick).find('.field.hex').find('input').val( hex );
        console.log($('.js-select_color').find('a[href="#'+$(colorPick).attr('id')+'"]'));
        $('.js-select_color').find('a[href="#'+$(colorPick).attr('id')+'"]').find('span.color_widget').css('background-color', '#'+hex);
      }
    });
  });

	$('#rswidget_setting .shesterenka').on('click',function(){
		if( $('#rswidget_setting').hasClass('opened') ) {
			$('#rswidget_setting').find('.settings').stop().animate({
				width : '0px'
			},rswidget_setting_delay,function(){
				// Animation complete.
				$(this).parents('#rswidget_setting').toggleClass('opened');
				$('#rswidget_setting').find('.shesterenka').stop().animate({
					right: -51
				},rswidget_setting_delay);
			});
		} else {
			$('#rswidget_setting').find('.settings').animate({
				width : '700px'
			},rswidget_setting_delay,function(){
				// Animation complete.
				$(this).parents('#rswidget_setting').toggleClass('opened');
				$('#rswidget_setting').find('.shesterenka').animate({
					right: 1
				},rswidget_setting_delay);
			});
		}
	});
	$(document).on('click', '#rswidget_setting .js-checkbox', function(){
		$(this).find('.checkbox_img').toggleClass('checked');
  });
  $(document).on('click', '.block_switch', function(){
    ball = $(this).find('.bkcg_switch_ball');
    ball.toggleClass('checked');
  });

	// settings
	$(document).on('click','#rswidget_setting .js-radio button',function(){
		checkedProp($(this), 'button');
	});

	$(document).on('click','#rswidget_setting .save_button',function(){
		RSMWSaveSettings("defaultN");
	});

	$(document).on('click', '.js-select_color', function(){
    liColor = $(this);
    console.log('click Color', liColor);
    if($(this).hasClass('drop_list')) {
      blockColor = liColor.html();
      $('.select_color').html(blockColor);
      liColor.parents('.list-unstyled').hide();
    } else {
      $('.border_color_widget').removeClass('active');
      $(this).find('.border_color_widget').addClass('active');
    }
    hrefWidget = $(this).find('a').attr('href');
    $('.rswidget_content').removeClass("show");
    $(hrefWidget).addClass("show");
    return false;
  });
  $(document).on('click', '.preset_num', function(){
  	$(this).parents('.presets').find('.preset_num').removeClass('checked');
  	$(this).addClass('checked');
  });

	// close by click outside
	$(document).on('click',function(e){
		if( $('#rswidget_setting').hasClass('opened') ) {
			if( $(e.target).parents('#rswidget_setting').length>0 ) {

			} else {
				$('#rswidget_setting .shesterenka').trigger('click');
			}
		}
	});

  $(document).on('click','.after_save_button_default', function(){
    RSMWSaveSettings("defaultY");
  });

})
