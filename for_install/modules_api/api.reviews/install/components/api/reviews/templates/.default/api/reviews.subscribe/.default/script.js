/*!
 * $.fn.apiReviewsSubscribe
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};
	var methods = {
		init: function (params) {

			var options = $.extend(true, {}, defaults, params);
			if (!this.data('apiReviewsSubscribe')) {
				this.data('apiReviewsSubscribe', options);

				// код плагина

				var subscribe_form = $('.api-reviews-subscribe .api-subscribe-form');

				$('.api-reviews-subscribe .api-link').on('click', function () {
					subscribe_form.toggle();
				});

				$(document).on('click', function (e) {
					if (!$(e.target).closest('.api-reviews-subscribe').length) {
						subscribe_form.hide();
					}
					e.stopPropagation();
				});

				subscribe_form.on('click', '.api_button', function () {

					subscribe_form.append('<div id="api-subscribe-wait"><span class="api-image"></span><span class="api-bg"></span></div>').show();
					subscribe_form.find('#api-subscribe-wait').show();

					$.ajax({
						url: options.AJAX_URL,
						type: 'POST',
						dataType: 'json',
						data: {
							sessid: BX.bitrix_sessid(),
							siteId: options.SITE_ID,
							params: options,
							form: {
								email: subscribe_form.find('.api-field-email').val()
							}
						},
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
						},
						success: function (data) {
							subscribe_form.find('#api-subscribe-wait').hide().remove();

							if (data.status == 'error') {
								if (subscribe_form.find('.api-error-email').length)
									subscribe_form.find('.api-error-email').text(data.message);
								else
									subscribe_form.find('.api-field-email').after('<div class="api-field-error api-error-email">' + data.message + '</div>');
							}
							if (data.status == 'ok') {
								subscribe_form.html('<div class="api-form-edge"></div><div class="api-form-success"><span></span><div>' + data.message + '</div></div>');

								setTimeout(function () {
									subscribe_form.hide();
								}, 2000)
							}
						}
					});
				});
			}

			return this;
		}
	};

	$.fn.apiReviewsSubscribe = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviewsSubscribe');
		}
	};

})(jQuery);