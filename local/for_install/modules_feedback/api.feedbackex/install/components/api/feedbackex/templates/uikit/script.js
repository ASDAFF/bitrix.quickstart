/*!
 * $.fn.apiFeedbackex
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};
	var options = {};

	var methods = {

		init: function (params) {

			var options = $.extend(true, {}, defaults, options, params);

			if (!this.data('apiFeedbackex')) {
				this.data('apiFeedbackex', options);

				//var wrapper = $(options.wrapperId);
				var form = $(options.formId);

				if (options.use_eula) {
					form.find('input[name=EULA_ACCEPTED]').on('change',function(){
						if($(this).is(':checked')){
							form.find('.api-eula-error').slideUp(200);
						}
					});
				}
				if (options.use_privacy) {
					form.find('input[name=PRIVACY_ACCEPTED]').on('change',function(){
						if($(this).is(':checked')){
							form.find('.api-privacy-error').slideUp(200);
						}
					});
				}


				//Form submit
				form.on('submit', function (e) {

					var bError = false;

					if (options.use_eula) {
						if (!form.find('input[name=EULA_ACCEPTED]').prop('checked')) {
							form.find('.api-eula-error').slideDown(200);
							bError = true;
						}
						else {
							form.find('.api-eula-error').slideUp(200);
						}
					}
					if (options.use_privacy) {
						if (!form.find('input[name=PRIVACY_ACCEPTED]').prop('checked')) {
							form.find('.api-privacy-error').slideDown(200);
							bError = true;
						}
						else {
							form.find('.api-privacy-error').slideUp(200);
						}
					}

					if(bError)
						return false;


					var submitFormData = form.serialize() + '&' + $.param(options.params);
					form.find('button').attr('disabled', true);
					$.ajax({
						type: 'POST',
						dataType: 'json',
						data: submitFormData,
						error: function (request, error) {
							alert('Server ' + error + '...');
						},
						success: function (data) {

							form.find('button').attr('disabled', false);

							if (data.result == 'ok')
								form.replaceWith(data.html);

							if (data.result == 'error') {
								if (typeof data.message.danger != 'undefined' && Object.keys(data.message.danger).length) {
									var messageDanger = data.message.danger;
									for (var field in messageDanger) {
										if (messageDanger[field].length)
											form.find(options.formId + '_ROW_' + field + ' .api-field-error').html(messageDanger[field]).slideDown('fast');
										else
											form.find(options.formId + '_ROW_' + field + ' .api-field-error').html('').slideUp('fast');
									}
								}

								if (typeof data.message.warning != 'undefined' && Object.keys(data.message.warning).length) {
									form
										 .find('.api-field-warning')
										 .html(data.message.warning.join('<br>'))
										 .slideDown('fast');
								}
								else
									form.find('.api-field-warning:visible').hide();
							}
						}
					});

					e.preventDefault();
				})

			}

			return this;
		}
	};

	$.fn.apiFeedbackex = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiFeedbackex');
		}
	};

})(jQuery);