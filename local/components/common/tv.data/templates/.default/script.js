$(document).ready(function() {
	$('div.ys-timeline').off("mouseenter mouseleave").hover(function() {
		var lunch = $(this).find('div.ys-lunch');
		lunch.stop(true, true).fadeIn('normal');
	}, function() {
		var lunch = $(this).find('div.ys-lunch');
		lunch.stop(true, false).fadeOut('normal');
	});

});