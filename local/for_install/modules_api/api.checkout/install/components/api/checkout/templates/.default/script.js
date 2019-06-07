/**
 * $.fn.apiCheckout
 */
(function ($) {

	// настройки со значением по умолчанию
	var defaults = {};
	var options = {};

	// публичные методы
	var methods = {

		// инициализация плагина
		init: function (params) {

			// актуальные настройки
			options = $.extend({}, defaults, options, params);

			// инициализируем лишь единожды
			if (!this.data('apiCheckout')) {

				// закинем настройки в реестр data
				this.data('apiCheckout', options);

				// код плагина

				var checkout       = $('#API_CHECKOUT');
				var checkoutForm   = $('#API_CHECKOUT form');
				var checkoutSubmit = $('#API_CHECKOUT .api_submit .api_button');


				//api_js_basket_toggle
				var api_js_basket_toggle = '#API_CHECKOUT .api_js_basket_toggle';
				$(api_js_basket_toggle).on('click',function(){
					$('#API_CHECKOUT .api_block_basket .api_block_content').slideToggle(200,function () {
						if($(this).is(':visible'))
							$(api_js_basket_toggle + ' .api_link').text(options.message.mess_basket_hide);
						else
							$(api_js_basket_toggle + ' .api_link').text(options.message.mess_basket_show);
					});
				});

				//api_js_prop_comment_toggle
				var api_js_prop_comment_toggle = '#API_CHECKOUT .api_js_prop_comment_toggle';
				$(api_js_prop_comment_toggle).before('<div class="api_row api_js_prop_comment_link api_link"><span class="api_link">'+options.message.mess_prop_comment_link+'</span></div>')
				$('#API_CHECKOUT .api_js_prop_comment_link').on('click',function(){
					$(this).slideUp(200);
					$(api_js_prop_comment_toggle).slideToggle(200);
				});

				//autoresize textarea
				checkoutForm.find('[data-autoresize]').each(function () {
					var offset         = this.offsetHeight - this.clientHeight;
					var resizeTextarea = function (el) {
						$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
					};
					$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
					resizeTextarea(this);
				});

				checkoutForm.on('submit',function(){

					BX.showWait('wait');

					//block fields before ajax
					checkoutSubmit.prop('disabled', true).html(options.message.mess_submit_text_ajax);
					checkoutForm.find('.api_field').attr('readonly', true);

					$.ajax({
						type: 'POST',
						dataType: 'json',
						data: checkoutForm.serialize(),
						timeout: 60000,
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
							alert(textStatus);
						},
						success: function (response) {

							//console.log(response);
							if(response.redirect && response.redirect.length){
								location.href = response.redirect;
							}
							else{
								var order = response.order;
								var error = order.ERROR;

								if(order.REDIRECT_URL) {
									location.href = order.REDIRECT_URL;
								}
								else if(error){
									//error.AUTH
									if(error.PROPERTY){
										var error_html = error.PROPERTY.join('<br>');
										$('html, body').animate({
											scrollTop: checkout.offset().top - 30
										}, 300, function(){
											checkout.find('.api_alert').addClass('api_alert_danger').html(error_html).slideDown('fast');
										});
									}
									else {
										var error_html = error.MAIN.join('<br>');
										$('html, body').animate({
											scrollTop: checkout.offset().top - 30
										}, 300, function(){
											checkout.find('.api_alert').addClass('api_alert_danger').html(error_html).slideDown('fast');
										});
									}

									//block fields before ajax
									checkoutSubmit.prop('disabled', false).html(options.message.mess_submit_text_default);
									checkoutForm.find('.api_field').attr('readonly', false);

									BX.closeWait('wait');
								}

								checkoutForm.find('.api_field_error').each(function () {
									$(this).on('keyup change', function () {
										if ($(this).val().length){
											$(this).removeClass('api_field_error')
												 .parents('.api_control')
												 .find('.api_field_alert')
												 .html('')
												 .slideUp('fast');
										}
									});
								});
							}
						}
					});

					return false;
				});
			}

			return this;
		},
		alert: function (modalId, data) {

			$.fn.apiModal('alert', {
				type: 'success',
				autoHide: 2000,
				modalId: modalId,
				message: data.MESSAGE
			});
		}

	};

	$.fn.apiCheckout = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiCheckout');
		}
	};

})(jQuery);