(function($) {
	$.fn.simpleCallback = function() {
	
		// ����� ������������
		$.fn.center = function() {
			var callbackMarginLeft = -this.width()/2;
			return this.css('margin-left', callbackMarginLeft);
		}
		// ����� ������� �������
		function hide() {
			$('.callback_body, .callback').fadeOut(300, 0);
		}
		// �������� �� ������ esc
		$('body').keyup(function(e) {
			if (e.keyCode == 27) {
				hide();
			}
		});
		// �������� �� ���� � �� ��������
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