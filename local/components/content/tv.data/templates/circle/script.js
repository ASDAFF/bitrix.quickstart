/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$(document).ready(function () {
$('div.ys-timeline').off("mouseenter mouseleave").hover(
	function () {
		$(this).find('div.ys-lunch').stop(true, true).fadeIn('normal');
	},
	function () {
		$(this).find('div.ys-lunch').stop(true, false).fadeOut('normal');
	}
);
}
);