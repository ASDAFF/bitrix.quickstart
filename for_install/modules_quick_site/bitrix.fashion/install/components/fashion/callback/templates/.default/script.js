(function($) {
	$.fn.simpleCallback = function() {
	
		// метод центирования
		$.fn.center = function() {
			var callbackMarginLeft = -this.width()/2;
			return this.css('margin-left', callbackMarginLeft);
		}
		// Общая функция скрытия
		function hide() {
			$('.callback_body, .callback').fadeOut(300, 0);
		}
		// Закрытие по кнопке esc
		$('body').keyup(function(e) {
			if (e.keyCode == 27) {
				hide();
			}
		});
		// Закрытие по фону и по крестику
		$('.callback_body, .callback_close').click(function() { 
			hide();
			return false;
		});
	
		return this.each(function() {
		
			$(this).click(function() {
				$(".callback_body").fadeTo(300, 0.7); 
				$(".callback").center().fadeTo(300, 1);
				return false;		
			});
			
		});
		
	}
})(jQuery);