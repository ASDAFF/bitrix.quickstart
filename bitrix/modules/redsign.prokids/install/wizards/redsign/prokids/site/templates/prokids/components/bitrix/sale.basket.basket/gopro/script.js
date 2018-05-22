var RSGoPro_BasketTimeoutID = 0;

$(document).ready(function(){
	
	$(document).on('click','.clearitems',function(){
		$(this).parents('.part').find('label').trigger('click');
		$(this).parents('form').find('.hiddensubmit').trigger('click');
		return false;
	});

	$(document).on('click','.clearsolo',function(){
		$(this).parents('form').find('.hiddensubmit').trigger('click');
		return false;
	});
	
	$(document).on('submit','#basket_form',function(){
		$('html').addClass('hidedefaultwaitwindow');
		RSGoPro_Area2Darken( $('#basket_form'), 'animashka' );
	});
	$(document).on('click','#basket_form a.delay, #basket_form a.delete, #basket_form a.add',function(){
		$('html').addClass('hidedefaultwaitwindow');
		RSGoPro_Area2Darken( $('#basket_form'), 'animashka' );
	});
	
	$(document).on('click','#basket_form .js-plus, #basket_form .js-minus',function(){
		var $link = $(this);
		clearTimeout(RSGoPro_BasketTimeoutID);
		RSGoPro_BasketTimeoutID = setTimeout(function(){
			$link.parents('form').find('.hiddensubmit').trigger('click');
		},1200);
	});
	
});