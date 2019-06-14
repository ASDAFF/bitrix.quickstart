/**
 * $.fn.apiAuthAjax
 */
(function ($) {
	var defaults = {};
	var options  = {};
	var methods  = {
		init: function (params) {

			var options = $.extend({}, defaults, options, params);

			if (!this.data('apiAuthAjax')) {
				this.data('apiAuthAjax', options);

				var modalOptions = {
					id:options.modalId
				};
				$.fn.apiModal('init',modalOptions);

				//Запускает модальное окно
				$(document).on('click', options.authId + ' >.api_link', function (e) {

					$(options.modalId).find($(this).attr('href')).show().siblings().hide();

					var modalOptions = {
						id: options.modalId,
						header:$(this).data('header')
					};
					$.fn.apiModal('show',modalOptions);

					e.preventDefault();
					return false;
				});

				//#href click
				$(options.modalId).on('click', '.api_link', function (e) {
					$(options.modalId).find('.api_modal_header').html($(this).data('header'));
					$(options.modalId).find($(this).attr('href')).show().siblings().hide();

					e.preventDefault();
					return false;
				});
			}
			return this;
		},
		/*getCss: function () {
			$(modal).find('[data-css]').each(function () {
				var css = '<link type="text/css"  href="' + $(this).attr('data-css') + '" rel="stylesheet">';
				$(css).appendTo("head");
			});
		},
		getJs: function () {
			$(modal).find('[data-js]').each(function () {
				var js = '<script type="text/javascript" src="' + $(this).attr('data-js') + '"></script>';
				$(js).appendTo("head");
			});
		}*/
	};

	$.fn.apiAuthAjax = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiAuthAjax');
		}
	};

})(jQuery);