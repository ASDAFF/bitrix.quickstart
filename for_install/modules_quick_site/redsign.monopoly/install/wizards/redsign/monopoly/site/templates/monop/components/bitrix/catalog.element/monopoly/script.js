// popup gallery
function RSMONOPOLY_PopupGallerySetHeight() {
    if($('.popupgallery').length>0) {
        if($(document).width()>767) {
            var innerHeight = parseInt($('.popupgallery').parents('.fancybox-inner').height()),
                h1 = innerHeight-55,
                h2 = h1-30,
                h3 = innerHeight-55-parseInt($('.popupgallery').find('.preview').height());
            $('.popupgallery').find('.thumbs.style1').css('maxHeight', h3 );
        } else {
            var fullrightHeight = parseInt($('.popupgallery').find('.fullright').height());
            var innerHeight = parseInt($('.popupgallery').parents('.fancybox-inner').height()),
                h1 = innerHeight-55-fullrightHeight-25,
                h2 = h1-30-fullrightHeight-25,
                h3 = innerHeight-55-parseInt($('.popupgallery').find('.preview').height());
            $('.popupgallery').find('.thumbs.style1').css('maxHeight', 100 );
        }
        $('.popupgallery').find('.changeit').css('height', h1 );
        $('.popupgallery').find('.changeit').find('img').css('maxHeight', h2 );
    }
}
function RSMONOPOLY_PopupGallerySetPicture() {
    if($('.popupgallery').length>0) {
        $('.js-gallery').find('.thumbs').find('a[href="'+$('.changeFromSlider:not(.cantopen)').find('img').attr('src')+'"]').trigger('click');
    }
}

$(document).ready(function(){
	
	// Change item in image slider 
	$(document).on("RSMONOPOLY_changePicture", function (e, img) {
		'use strict';
		
		console.log("____" + img.id);
		var index = $(".js-general_images .owl-item:not(.cloned) img[data-index=" + img.id +"]")
						.parents(".owl-item")
						.index(".js-general_images .owl-item:not(.cloned)");
		
		$(".js-general_images").trigger("to.owl.carousel", index);
	});

    $('.part2').find('.tabs .nav > li:first').addClass('active');
    $('.part2').find('.tabs .tab-content > .tab-pane:first').addClass('in active');

	// images
	$(".fancybox").fancybox();
	$(document).on('click','.thumb a',function(){
		$('.js-detail .slider').find('.checked').removeClass('checked');
		$('.js-detail .slider').find('.pic'+$(this).data('index')).addClass('checked');
		$('.js-detail .thumbs').find('.thumb').removeClass('checked');
		$('.js-detail .thumbs').find('.pic'+$(this).data('index')).addClass('checked');
		return false;
	});
	
	$(document).on("RSMONOPOLY_fancyBeforeShow", function (e) {
		var $jsGallery = $(".js-gallery");
		
		if($jsGallery.length > 0) {
			$jsGallery.find(".thumbs a:eq(0)").click();
		}
	});
	
	var $owl = $('.owlslider.js-slider_images');
	owlInit($owl, {
		items: 3,
		margin: 16,
		loop: true,
		responsive: {"0":{"items":"2"},"768":{"items":"3"}},
		onResize: function () {
			if (this._items.length <= this.settings.items) {
                this.settings.loop = false;
            }
		},
		onRefreshed: function () {
			$owl.removeClass('noscroll');
            if($owl.find('.cloned').length<1) {
                $owl.addClass('noscroll');
            }
		}
	});
	
	var $owlGeneralImages = $(".owlslider.js-general_images");
	owlInit($owlGeneralImages, {
		margin: 0,
		nav: false,
		items: 1,
		onChanged: function (event) {
			
			if(event.item && event.item.index) {
				var $owlImagesSlider = $(".thumbs .js-slider_images"),
					activeId = this.$element.find("img:eq(" + event.item.index + ")").data('index'),
					$owlImagesSliderActive = $owlImagesSlider.find("a[data-index=" + activeId + "]");
				
				$owlImagesSlider.find(".checked").removeClass("checked");
				$owlImagesSliderActive.parent().addClass("checked");
			}
			
		},
		onInitialize: function () {
			$owlGeneralImages.addClass('owl-carousel owl-theme withdots');
		}
	});

    $(document).on('click','.js-detail .moretext',function(){
        $('.part2 .tabs a.detailtext').trigger('click');
    });
    $(document).on('click','.js-detail .moreprops',function(){
        $('.part2 .tabs a.properties').trigger('click');
    });

})