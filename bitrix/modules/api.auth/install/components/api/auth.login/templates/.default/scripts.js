/**
 * $.fn.apiAuthLogin
 */
(function ($) {
	var defaults = {};
	var options = {};
	var methods = {
		init: function (params) {

			var options = $.extend({}, defaults, options, params);

			if (!this.data('apiAuthLogin')) {
				this.data('apiAuthLogin', options);

				//$('.api_auth_login').each(function(){

				var wrapper = $(options.wrapperId);
				var form = $(options.formId);

				var ajaxUrl = '/bitrix/components/api/auth.login/ajax.php';
				var formData = {
					API_AUTH_LOGIN_AJAX: 1,
					sessid: options.sessid,
					siteId: options.siteId,
					messLogin: options.messLogin,
					messSuccess: options.messSuccess
				};

				if(options.useCaptcha){
					$(form).find('.api-captcha').slideDown();
				}

				if (options.usePrivacy) {
					$(form).find('.api-row-privacy').on('click','.api-accept-label',function(){
						if($(this).find(':checkbox').is(':checked')){
							$(this).find(':checkbox').prop('checked',false).change();
							$(this).parents('.api_controls').find('.api-error').slideDown(200);
						}
						else{
							$(this).find(':checkbox').prop('checked',true).change();
							$(this).parents('.api_controls').find('.api-error').slideUp(200);
						}
					});
				}


				//---------- Refresh captcha ----------//
				$(form).on('click', '.api-captcha-refresh', function (e) {
					e.preventDefault();

					var btn_refresh = $(this);
					btn_refresh.addClass('api-animation-rotate');
					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						dataType: 'json',
						cache: false,
						data: $.extend(true,{'api_action': 'getCaptcha'},formData),
						success: function (result) {
							var captcha = $(form).find('.api-captcha');
							captcha.find('.api_captcha_sid').val(result.CAPTCHA.SID);
							captcha.find('.api_captcha_src').attr('src', result.CAPTCHA.SRC);
							captcha.find('.api_captcha_word').val('');
							btn_refresh.removeClass('api-animation-rotate');
						}
					});
				});

				//---------- Form submit ----------//
				$(form).on('click', '[type="button"]', function (e) {

					var bError = false;

					if (options.usePrivacy) {

						$(form).find('.api-row-privacy :checkbox').each(function(){
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

					if(bError)
						return false;

					$.fn.apiAuthLogin('showWait', form);

					if (options.secureAuth)
						rsasec_form(options.secureData);

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						dataType: 'json',
						data: $(form).serialize() + '&' + $.param(formData),
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
						},
						success: function (result) {
							$.fn.apiAuthLogin('hideWait', form);

							if (result.TYPE == 'ERROR') {
								$.fn.apiAuthLogin('showError', form, result.MESSAGE);

								if (result.CAPTCHA) {
									var captcha = $(form).find('.api-captcha');
									captcha.slideDown();
									captcha.find('.api_captcha_sid').val(result.CAPTCHA.SID);
									captcha.find('.api_captcha_src').attr('src', result.CAPTCHA.SRC);
									captcha.find('.api_captcha_word').val('');
								}
							}
							else {
								$(wrapper).html(result.MESSAGE);
								window.setTimeout('location.reload(true)', 2000);
							}
						}
					});
					e.preventDefault();
				});

				//});
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

	$.fn.apiAuthLogin = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiAuthLogin');
		}
	};

	$.fn.apiAuthLogin('init');

})(jQuery);

