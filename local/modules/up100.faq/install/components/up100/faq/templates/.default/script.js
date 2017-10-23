$(document).ready(function(){
	$('.dropDown .name').click(function(){
		$(this).parent().find('.dropContent').slideToggle(100);
		$(this).toggleClass('active');
	});
	
	$('.dropDown .expand').click(function(){
		$(this).parent().find('.dropContent').slideDown(100);
		$(this).parent().find('.name').addClass('active');
	})
	$('.dropDown .collapse').click(function(){
		$(this).parent().find('.dropContent').slideUp(100);
		$(this).parent().find('.name').removeClass('active');
	})
})