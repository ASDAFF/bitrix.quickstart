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