jQuery(function(){
	$(".owl-carousel.clients").owlCarousel({
		items: 6,
		autoPlay: true,
		pagination: false,
		itemsDesktop : [1000,6], //6 items between 1000px and 901px
		itemsDesktopSmall : [900,4], //4 items betweem 900px and 601px
		itemsTablet: [600,4], //4 items between 600 and 0
	});
});