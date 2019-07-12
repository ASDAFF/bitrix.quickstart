$(document).ready(function(){

	// images
	$(".fancybox").fancybox();
	$(document).on('click','.thumb a',function(){
		$('.js-detail .slider').find('.checked').removeClass('checked');
		$('.js-detail .slider').find('.pic'+$(this).data('index')).addClass('checked');
		$('.js-detail .thumbs').find('.thumb').removeClass('checked');
		$('.js-detail .thumbs').find('.pic'+$(this).data('index')).addClass('checked');
		return false;
	})
	
	var $owl = $('.owlslider');
    $owl.owlCarousel({
        items               : 6
        ,margin             : 12
        ,loop               : true
        ,autoplay           : false
        ,nav                : true
        ,navText            : ['<span></span>','<span></span>']
        ,navClass           : ['prev','next']
        ,smartSpeed         : 1000
        ,onInitialize       : function (e) {
            $owl.addClass('owl-carousel owl-theme');
            if (this.$element.children().length <= this.settings.items) {
                this.settings.loop = false;
            }
        }
        ,onResize           : function (e) {
            if (this._items.length <= this.settings.items) {
                this.settings.loop = false;
            }
        }
        ,onRefreshed        : function(){
            $owl.removeClass('noscroll');
            if($owl.find('.cloned').length<1) {
                $owl.addClass('noscroll');
            }
        }
        ,responsive         : {"0":{"items":"2"},"768":{"items":"4"},"991":{"items":"5"},"1200":{"items":"6"}}
    });

    $(document).on('RSMONOPOLY_changePicture',function(){
        $('.popupgallery').find('.description').html( $('.popupgallery').find('.checked.thumb').find('a').data('descr') );
    });

})