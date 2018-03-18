/* Main*/
$(document).ready(function(){
	$('.slobel-social-icons').find('a').hover(
			function(){
				$(this).find('.hover').stop().animate({opacity: "1"}, 800);
				$(this).find('.nohover').stop().animate({opacity: "0"}, 800);
			},
			function(){
				$(this).find('.hover').stop().animate({opacity: "0"}, 800);
				$(this).find('.nohover').stop().animate({opacity: "1"}, 800);
			}
	);
});