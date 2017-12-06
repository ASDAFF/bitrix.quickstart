
$.fn.lw_rk_check_form = function() {
	var $this = $(this);

	var handle_each = function($each_element) {
		var self = {},
			$form_wrapper = $each_element,
			$form_block = $form_wrapper.find('.form-block'),
			$field_blocks = $form_block.find('.field-block'),
			$submit_button = $form_block.find('.lw-rk-form-submit'),
			params = $form_block.find('#PARAMS').attr('data-params');

		self.submit = function() {
			$.ajax({
				type: "POST",
				url: $form_block.attr('action'),
				dataType: 'json',
				data: {
					name: $form_block.attr('name'),
					fields: $form_block.serialize(),
					params: params
				},
				success: function(response){
					if (response['SENT'] == 'Y' && response['ERROR'].length == 0) {
						$form_block.find('#PARAMS').attr('name','PARAMS').val(params);
						$form_block.find('#sessid').remove();
						$form_block.submit();
						self.clear();
					} else {
						if (response['ERROR']['REQUIRED_FIELDS']) {
							$form_block.find('.field').each(function(i) {
								var $field = $(this);

								if (response['ERROR']['REQUIRED_FIELDS'][i] == $field.attr('name')) {
									$field.parent().addClass('error');
									$field.val('');
									$field.attr('placeholder',$field.attr('data-error'));
								}
							});
						}
						if (response['ERROR']["SMS_RU_ERROR"]) {
							
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
			$form_wrapper.removeClass('sent');
		};
		self.checker = function(){
			var errors = false,
				answer = false;

			$field_blocks.each(function(){
				var $field_block = $(this),
					$field = $field_block.find('.field');

				if ($field.is('[required]') && $field.val().trim() == '') {
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
		$submit_button.bind('click',function(){
			if (self.checker()) {
				self.submit();
			}
			return false;
		});
		return $form_block;
	};
	return $this.each(function(){
		handle_each($(this));
	});
};

$.fn.lw_rk_check_get_form = function() {
	var $this = $(this);

	var handle_each = function($each_element) {
		var self = {},
			$form_wrapper = $each_element,
			$form_block = $form_wrapper.find('.form-block'),
			$field_blocks = $form_block.find('.field-block'),
			$submit_button = $form_block.find('.lw-rk-form-submit');

		self.submit = function() {
			$.ajax({
				type: "POST",
				url: $form_block.attr('action'),
				dataType: 'json',
				data: {
					order_id: $form_block.find('input[name=ORDER_ID]').val().trim(),
					password: $form_block.find('input[name=PASSWORD]').val().trim(),
					params: $form_block.find('input[name=PARAMS]').val()
				},
				success: function(response){
					var $message_block = $form_wrapper.find('.message-block'),
						$message = $message_block.find('.message'),
						$email_value = $message_block.find('.email-value');

					if (response['ERROR'].length == 0) {
						$message.text($message.attr('data-old-title'));
						$email_value.show().text(response['EMAIL']);
						$form_wrapper.addClass('sent');
					} else {
						$message.text(response['ERROR'][0]['message']);
						$email_value.find('.email-value').hide();
						$form_wrapper.addClass('sent');
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
			$form_wrapper.removeClass('sent');
		};
		self.checker = function(){
			var errors = false,
				answer = false;

			$field_blocks.each(function(){
				var $field_block = $(this),
					$field = $field_block.find('.field');

				if ($field.is('[required]') && $field.val().trim() == '') {
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
		$submit_button.bind('click',function(){
			if (self.checker()) {
				self.submit();
			}
			return false;
		});
		return $form_block;
	};
	return $this.each(function(){
		handle_each($(this));
	});
};

$(document).ready(function(){

	$('.lw-rk-buy-button').le_window({
		element_id: true,
		before_open: function(button,element){
			var $button = $(button),
				$element = $(element),
				product_id = $button.attr('data-product-id'),
				product_title = $button.attr('data-product-title'),
				product_description = $button.attr('data-product-description'),
				product_price = $button.attr('data-product-price');

			$element.find('.product-title').text(product_title);
			$element.find('.product-description').text(product_description);
			$element.find('.product-price').text(product_price);
			$element.find('#PRODUCT-ID').val(product_id);
		}
	});
	$('.rk-window-wrapper').lw_rk_check_form();

	$('.lw-rk-get-order').le_window({
		element_id: true,
		before_open: function(button,element){
			var $element = $(element);

			$element.find('.form-block').trigger('form_clear');
		}
	});
	$('.rk-get-wrapper').lw_rk_check_get_form();

});

