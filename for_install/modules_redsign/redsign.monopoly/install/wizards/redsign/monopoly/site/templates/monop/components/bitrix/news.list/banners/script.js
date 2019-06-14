function RSMONOPOLY_MoveNav(owlS) {
	var carouselHeight = owlS.find('.owl-stage-outer').height();
	owlS.find('.owl-nav').find('div').css('bottom', (Math.round(carouselHeight/2)-27)+'px');
}

$(window).load(function(){
	$('.owl_banners').each(function(){
		var $owl = $(this),
			RSMONOPOLY_change_speed = 2000,
			RSMONOPOLY_change_delay = 8000;
		if(parseInt($owl.data('changespeed'))>0) {
			RSMONOPOLY_change_speed = $owl.data('changespeed');
		}
		if(parseInt($owl.data('changedelay'))>0) {
			RSMONOPOLY_change_delay = $owl.data('changedelay');
		}
		if( $owl.find('.item').length>1 ) {
			$owl.owlCarousel({
				items				: 1
				,loop				: true
				,autoplay			: true
				,nav				: true
				,navText			: ['<span></span>','<span></span>']
				,navClass			: ['prev', 'next']
				,autoplaySpeed		: RSMONOPOLY_change_speed
				,autoplayTimeout	: RSMONOPOLY_change_delay
				,smartSpeed			: RSMONOPOLY_change_speed
				,onInitialize 		: function (e) {
                    $owl.addClass('owl-carousel owl-theme').removeAttr('style');
                    if (this.$element.children().length <= this.settings.items) {
                        this.settings.loop = false;
                    }
                }
                ,onResize 			: function (e) {
                    if (this._items.length <= this.settings.items) {
                        this.settings.loop = false;
                    }
                }
				,onRefreshed		:function(){
					$owl.removeClass('noscroll');
					if($owl.find('.cloned').length<1) {
						$owl.addClass('noscroll');
					}
				}
				,onInitialized		: function() {
					$owl.find('.owl-nav').addClass('container');
					RSMONOPOLY_MoveNav($owl);
				}
				,onResized			: function() {
					RSMONOPOLY_MoveNav($owl);
				}
			});
		} else {
			$owl.removeAttr('style');
		}
		// play video
		$owl.find('video').each(function(){
			if( $(this).attr('autoplay')=='autoplay' ) {
				$(this).get(0).play();
			}
		});
	});
	
});