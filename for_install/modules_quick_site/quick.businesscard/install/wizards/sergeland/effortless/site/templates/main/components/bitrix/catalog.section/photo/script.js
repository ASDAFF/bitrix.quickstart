jQuery(document).ready(function($) {
	$(window).load(function(){
		$('.isotope-container').fadeIn();
		var $container = $('.isotope-container').isotope({
			itemSelector: '.isotope-item',
			layoutMode: 'masonry',
			transitionDuration: '0.6s',
			filter: "*"
		});
	});
});