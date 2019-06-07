/**
 * $.fn.apiTab
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	// настройки со значением по умолчанию
	var defaults = {
		id: ''
	};
	var options = {};

	var methods = {

		init: function (params) {

			options = $.extend(true, {}, defaults, options, params);

			if (!this.data('apiTab')) {
				this.data('apiTab', options);

				// код плагина

				var api_tab = $(document).find(options.id);

				api_tab.find('.api_tab_nav').on('click', '>div', function (e) {

					//console.log($(this));

					$(this).siblings().removeClass('api_active');
					$(this).addClass('api_active');

					var href = $(this).find('a').attr('href');

					var tab_panel = $(document).find(href);
					tab_panel.siblings().removeClass('api_active');
					tab_panel.addClass('api_active');

					e.preventDefault();
				});
			}

			return this;
		},
		show: function (options) {
			var tab_panel = $(document).find(options.id);
			tab_panel.siblings().removeClass('api_active');
			tab_panel.addClass('api_active');
		}
	};

	$.fn.apiTab = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiTab');
		}
	};

})(jQuery);