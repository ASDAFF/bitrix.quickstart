/*!
 * $.fn.apiOffcanvas
 */
(function ($, undefined ) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {
		theme: 'default'
	};

	var methods = {

		init: function (params) {

			var $html = $('html');
			var options = $.extend(true, {}, defaults, params);

			if (!this.data('apiOffcanvas')) {
				this.data('apiOffcanvas', options);

				if (!$html.hasClass('api-offcanvas-init'))
					$html.addClass('api-offcanvas-init');

				$(this).each(function (index, element) {
					var target = $(this).data('target');

					$(target).show(1);

					$(this).on('click tap', function () {
						$(target).css({'opacity': '1'}).closest('.api_offcanvas').toggleClass('api_offcanvas_open');
						$('html').addClass('api-offcanvas-html');
					})
				});

				$(document).on('click', '.api_offcanvas, .api_offcanvas .api_close', function (e) {
					e.preventDefault();
					if ($('.api_offcanvas').hasClass('api_offcanvas_open')) {
						$('.api_offcanvas').removeClass('api_offcanvas_open').addClass('api_offcanvas_close');
						$('html').removeClass('api-offcanvas-html');
					}
					else {
						$('.api_offcanvas').addClass('api_offcanvas_open').removeClass('api_offcanvas_close');
						$('html').addClass('api-offcanvas-html');
					}
				});
				$(document).on('click', '.api_offcanvas .api_offcanvas_panel', function (e) {
					e.stopPropagation();
					//e.preventDefault();
				});
			}

			return this;
		}
	};

	$.fn.apiOffcanvas = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiOffcanvas');
		}
	};

})(jQuery);