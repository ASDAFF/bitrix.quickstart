/**
 * $.fn.apiReviewsRecent
 */
(function ($) {
	var defaults = {};
	var options  = {};
	var methods  = {
		init: function (params) {

			options = $.extend({}, defaults, options, params);

			if (!this.data('apiReviewsRecent')) {
				this.data('apiReviewsRecent', options);

				$('.api-reviews-recent .api-noindex').each(function () {
					$(this).replaceWith('<a href="' + $(this).data('url') + '" title="' + $(this).text() + '" target="_blank">' + $(this).html() + '</a>');
				});
			}

			return this;
		}
	};

	$.fn.apiReviewsRecent = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviewsRecent');
		}
	};

})(jQuery);