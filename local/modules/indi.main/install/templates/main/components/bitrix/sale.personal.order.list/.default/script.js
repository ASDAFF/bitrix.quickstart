$(function() {
	// показать детальную информацию заказа
	$('.show-details').click(function() {
		var details = $(this).parents('.order').next();
		var plus = $(this).parent().find('.glyphicon-plus-sign');
		var minus = $(this).parent().find('.glyphicon-minus-sign');
		
		if(details.is(':visible'))
		{
			details.hide();
			plus.show();
			minus.hide();
		}
		else
		{
			details.show();
			plus.hide();
			minus.show();
		}
		
		return false;
	});
	
	$('.show-details').parent().find('span').click(function(){
		$(this).parent().find('a').trigger('click');
	});
});