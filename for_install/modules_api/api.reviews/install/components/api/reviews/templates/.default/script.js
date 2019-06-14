/*!
 * $.fn.apiReviews
 */

if (typeof jQuery === 'undefined') {
	console.error('api:reviews component requires jQuery. jQuery must be included before all plugins in one copy');
}

(function ($) {
	var version = $.fn.jquery.split(' ')[0].split('.');
	if ((version[0] < 1 && version[1] < 8) || (version[0] === 1 && version[1] === 8 && version[2] < 3) || (version[0] >= 4)) {
		console.error('api:reviews component requires at least jQuery v1.8.3');
	}
})(jQuery);

(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};

	var methods = {

		init: function (params) {
			var options = $.extend(true, {}, defaults, params);

			if (!this.data('apiReviews')) {
				this.data('apiReviews', options);

				// код плагина

				//api-noindex
				$('.api-reviews .api-noindex').each(function(){
					$(this).replaceWith('<a href="'+$(this).data('url')+'" title="'+$(this).text()+'" target="_blank">'+$(this).html()+'</a>');
				});

				$('.api-reviews').on('click', '.js-getDownload', function (e) {
					var href = $(this).closest('a').attr('href');
					if(href.length){
						methods.download('', 'API_REVIEWS_FILE=' + $(this).closest('a').attr('href') + '&API_REVIEWS_AJAX=Y&API_REVIEWS_ACTION=FILE_DOWNLOAD');
					}
					e.preventDefault();
				});
			}

			return this;
		},
		download: function (url, data, method) {
			if (data) {
				data = (typeof data === 'string' ? data : $.param(data));
				var inputs = '';
				$.each(data.split('&'), function () {
					var pair = this.split('=');
					inputs += '<input type="hidden" name="' + pair[0] + '" value="' + pair[1] + '" />';
				});
				$('<form action="' + url + '" method="' + (method || 'post') + '">' + inputs + '</form>')
					 .appendTo('body').submit().remove();
			}
		}

	};

	$.fn.apiReviews = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviews');
		}
	};

})(jQuery);