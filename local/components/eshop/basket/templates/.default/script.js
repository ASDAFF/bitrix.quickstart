/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

(function ($) {
	
	$('input.beono-basket-action').live('change', function () {
		
		var checked_length = $('input.beono-basket-action:checked').length;
		
		if ($(this).is(':checked')) {
			$('#beono_basket_action').css('visibility', 'visible');
			if (checked_length == $('input.beono-basket-action').length) {
				$('input.beono-basket-action_all').attr('checked', 'checked');
			}
		} else {			
			if (checked_length < $('input.beono-basket-action').length) {
				if (checked_length == 0) {
					$('#beono_basket_action').css('visibility', 'hidden');
				}
				$('input.beono-basket-action_all').removeAttr('checked');
			}
		}
	});
		
	$('input.beono-basket-action_all').live('change', function () {
		if ($(this).is(':checked')) {
			$('input.beono-basket-action').attr('checked', 'checked').change();			
		} else {
			$('input.beono-basket-action').removeAttr('checked').change();
		}
	});
	
	$('td.beono-basket-item-quantity a').live('click', function () {
		var operation = $(this).html();		
		$(this).siblings('input').val(function(index, value) {
			if (operation == '+') { 
				return parseInt(value) + 1;
			} else if (value > 1) {
				return parseInt(value) - 1;
			}
			return parseInt(value);
		}).change();
		
		return false;				
	});
	
	$('td.beono-basket-action a').live('click', function () {
		$(this).closest('tr').fadeTo('fast', 0);
	});
	
	/*$('td.beono-basket-item-quantity input').live('change', function () {
		if (window.form_timeout) { clearTimeout(window.form_timeout); }
		window.form_timeout = setTimeout( function () { $('input[name=basketrefresh]').click(); }, 300);					
	});*/
	
})(jQuery);