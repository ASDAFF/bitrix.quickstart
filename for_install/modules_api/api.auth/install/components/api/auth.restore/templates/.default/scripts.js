/**
 * $.fn.apiAuthRestore
 */
(function ($) {
	var defaults = {};
	var options = {};
	var methods = {
		init: function (params) {

			var options = $.extend({}, defaults, options, params);

			if (!this.data('apiAuthRestore')) {
				this.data('apiAuthRestore', options);

				var wrapper = $(options.wrapperId);
				var form = $(options.formId);

				$(form).on('click', '[type="button"]', function (e) {

					$.fn.apiAuthRestore('showWait', form);

					var formData = {
						API_AUTH_RESTORE_AJAX: 1,
						sessid: BX.message('bitrix_sessid'),
						siteId: BX.message('SITE_ID'),
					};
					var submitFormData = $(form).serialize() + '&' + $.param(formData);

					$.ajax({
						type: 'POST',
						url: '/bitrix/components/api/auth.restore/ajax.php',
						dataType: 'json',
						data: submitFormData,
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
						},
						success: function (data) {
							$.fn.apiAuthRestore('hideWait', form);

							$.fn.apiAuthRestore('showError', form, data.MESSAGE);
						}
					});
					e.preventDefault();
				});
			}
			return this;
		},
		showError: function (form, message) {
			$(form).find('.api_error').slideUp(200, function () {
				$(this).html(message).slideDown(200);
			});
		},
		showWait: function (form) {
			$(form).addClass('api_form_wait').find('.api_field').prop('readonly', true);
		},
		hideWait: function (form) {
			$(form).removeClass('api_form_wait').find('.api_field').prop('readonly', false);
		}
	};

	$.fn.apiAuthRestore = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiAuthRestore');
		}
	};

	$.fn.apiAuthRestore('init');

})(jQuery);

