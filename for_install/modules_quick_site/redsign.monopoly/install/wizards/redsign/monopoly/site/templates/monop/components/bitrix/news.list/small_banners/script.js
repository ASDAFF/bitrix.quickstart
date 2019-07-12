$(document).ready(function () {	
	'use strict';
	
	var $owlSmallBanners = $(".owlslider.smallbanners");
	var defaultMargin = $owlSmallBanners.data('margin') || 35;
	var sliderParams = {
		nav: false,
		items: 3,
		loop: true,
		margin: defaultMargin,
		autoplaySpeed: $owlSmallBanners.data('changespeed') || 2000,
		autoplayTimeout: $owlSmallBanners.data('changedelay') || 8000,
		smartSpeed: $owlSmallBanners.data('changespeed') || 2000,
		responsive: {
			0: {
				items: 1,
				margin: 0,
				autoWidth: false
			},
			420: {
				items: 1,
				autoWidth: true,
				margin: 2
			},
			768: {
				items: 2
			},
			991: {
				item: 3
			}
		}
	};
	
	owlInit($owlSmallBanners, sliderParams);
});