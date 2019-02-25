$(document).ready(function() {
	$('.ys-time-work, .ys-time-weekend').mouseover(function() {
		if ($('.ys-lunch').css('display') == "none") {
			$('.ys-lunch').fadeIn('normal');
		}
	});

	$('.ys-time-work, .ys-time-weekend').mouseout(function() {
		if ($('.ys-lunch').css('display') == "block") {
			$('.ys-lunch').fadeOut('normal');
		}
	});
});