jQuery(function(){
	$(".owl-carousel.clients").owlCarousel({
		items: 3,
		autoPlay: true,
		pagination: false,
		itemsDesktop : [1000,3], //3 items between 1000px and 901px
		itemsDesktopSmall : [900,3], //3 items betweem 900px and 601px
		itemsTablet: [600,3], //3 items between 600 and 0
	});
});