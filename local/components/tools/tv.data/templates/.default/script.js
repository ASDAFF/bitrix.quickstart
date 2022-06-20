/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$(document).ready(function() {
	$('div.ys-timeline').off("mouseenter mouseleave").hover(function() {
		var lunch = $(this).find('div.ys-lunch');
		lunch.stop(true, true).fadeIn('normal');
	}, function() {
		var lunch = $(this).find('div.ys-lunch');
		lunch.stop(true, false).fadeOut('normal');
	});

});