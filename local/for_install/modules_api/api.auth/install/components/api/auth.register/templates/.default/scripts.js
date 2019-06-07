/**
 * $.fn.apiAuthRegister
 */
(function ($) {
	var defaults = {};
	var options = {};
	var methods = {
		init: function (params) {

			options = $.extend({}, defaults, options, params);

			if (!this.data('apiAuthRegister')) {
				this.data('apiAuthRegister', options);

				//Code here
				var wrapper = $(options.wrapperId);
				var form = $(options.formId);

				if (options.usePrivacy) {
					$(form).find('.api-row-privacy').on('click', '.api-accept-label', function () {
						if ($(this).find(':checkbox').is(':checked')) {
							$(this).find(':checkbox').prop('checked', false).change();
							$(this).parents('.api_controls').find('.api-error').slideDown(200);
						}
						else {
							$(this).find(':checkbox').prop('checked', true).change();
							$(this).parents('.api_controls').find('.api-error').slideUp(200);
						}
					});
				}

				$(form).on('click', '[type="button"]', function (e) {

					var bError = false;

					if (options.usePrivacy) {
						$(form).find('.api-row-privacy :checkbox').each(function () {
							if (!$(this).prop('checked')) {
								$(this).parents('.api_controls').find('.api-error').slideDown(200);
								bError = true;
							}
							else {
								$(this).parents('.api_controls').find('.api-error').slideUp(200);
							}
						});
					}

					if (options.useConsent) {
						$(form).find('input[name*=USER_CONSENT]').each(function () {
							if (!$(this).prop('checked')) {
								$(this).parents('.api_control').find('.api-error').slideDown(200);
								bError = true;
							}
							else {
								$(this).parents('.api_control').find('.api-error').slideUp(200);
							}
						});
					}

					if (bError)
						return false;

					$.fn.apiAuthRegister('showWait', form);

					if (options.secureAuth)
						rsasec_form(options.secureData);

					var formData = {
						API_AUTH_REGISTER_AJAX: 1,
						sessid: BX.message('bitrix_sessid'),
						siteId: BX.message('SITE_ID'),
						REQUIRED_FIELDS: options.REQUIRED_FIELDS
					};
					var submitFormData = $(form).serialize() + '&' + $.param(formData);

					$.ajax({
						type: 'POST',
						url: '/bitrix/components/api/auth.register/ajax.php',
						dataType: 'json',
						data: submitFormData,
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
						},
						success: function (data) {
							$.fn.apiAuthRegister('hideWait', form);

							if (data.TYPE == 'ERROR') {
								$.fn.apiAuthRegister('showError', form, data.MESSAGE);
							}
							else {
								$(wrapper).html(data.MESSAGE);
								window.setTimeout('location.reload(true)', 3000);
							}
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
			$(form).removeClass('api_form_wait').find('.api_field').prop('readonly', false).prop('disabled', false);
		}
	};

	$.fn.apiAuthRegister = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiAuthRegister');
		}
	};

	$.fn.apiAuthRegister('init');

})(jQuery);

