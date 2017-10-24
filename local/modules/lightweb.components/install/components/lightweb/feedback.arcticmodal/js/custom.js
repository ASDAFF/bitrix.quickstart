/**
 * checkForm.js v1
 * Yet another jQuery plug-in for checking forms.
 * Dependencies: none
 *
 * Made by Alex Kasimov - 2015
 * https://vk.com/al4str
 * Under MIT License
 */
(function($){
	"use strict";
	$.fn.lw_fa_checker = function() {
		var $this = $(this);

		function handle_each($each_element) {
			var self = {},
				$open_button = $each_element,
				form_id = $open_button.attr('data-window-id'),
				$form_wrapper = $('#'+form_id),
				$form_block = $form_wrapper.find('.form-block'),
				$field_blocks = $form_block.find('.field-block'),
				$submit_button = $form_block.find('.form-submit');

			self.submit = function() {
				$.ajax({
					type: "POST",
					url: $form_block.attr('action'),
					dataType: 'json',
					data: $form_block.serialize(),
					success: function(response){
						if (response['SENT']=='Y' && response['ERROR'].length == 0) {
							$form_wrapper.addClass('show-success-block');
						} else {
							if (response['ERROR']['REQUIRED_FIELDS']) {
								$form_block.find('.field').each(function(i) {
									var $field = $(this);

									if (response['ERROR']['REQUIRED_FIELDS'][i] == $field.attr('name')) {
										$field.parent().addClass('error');
										$field.val('');
										$field.attr('placeholder', $field.attr('data-error'));
									}
								});
							}
							if (response['ERROR']["SMS_RU_ERROR"]) {
								if (response['ERROR']["SMS_RU_ERROR"] == '202') {
									var $phone_field = $form_block.find('.phone-field');

									$phone_field.parent().addClass('error');
									$phone_field.val('');
									$phone_field.attr('placeholder','Неправильный номер');
								}
							}
						}
					}
				});
			};
			self.clear = function() {
				$field_blocks.each(function(){
					var $field_block = $(this),
						$field = $field_block.find('.field');

					$field.val('');
					$field_block.removeClass('error');
					$field.attr('placeholder','');
				});
				$form_wrapper.removeClass('show-success-block');
			};
			self.checker = function(){
				var errors = false,
					answer = false;

				$field_blocks.each(function(){
					var $field_block = $(this),
						$field = $field_block.find('.field');

					if ($field.val().trim() == '' && $field.is(':required')) {
						errors = true;
						$field_block.addClass('error');
						$field.attr('placeholder',$field.attr('data-error'));
					} else {
						$field_block.removeClass('error');
						$field.attr('placeholder','');
					}
				});
				if (errors == true) {
					$submit_button.addClass('error');
					setTimeout(function(){
						$submit_button.removeClass('error');
					},500);
				} else {
					answer = true;
				}
				return answer;
			};
			$form_block.on('form_clear',function(){
				self.clear();
			});
			$submit_button.off('click');
			$submit_button.on('click',function(){
				if (self.checker()) {
					self.submit();
				}
				return false;
			});
			$open_button.off('click');
			$open_button.on('click',function(){
				self.clear();
				$form_wrapper.arcticmodal();
			});
			return $form_block;
		}
		return $this.each(function(){
			handle_each($(this));
		});
	};
	$(document).ready(function(){
		$('.lw-fa-button').lw_fa_checker();
	});
})(jQuery);

