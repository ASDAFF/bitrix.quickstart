/*!
 * $.fn.apiReviewsStat
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};
	var options = {};
	var methods = {
		init: function (params) {

			options = $.extend(true, {}, defaults, options, params);

			if (!this.data('apiReviewsStat')) {
				this.data('apiReviewsStat', options);
			}
			return this;
		},
		update: function () {
			$.ajax({
				type: 'POST',
				async: false,
				data: {
					sessid: BX.bitrix_sessid(),
					API_REVIEWS_STAT_AJAX: 'Y'
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('textStatus: ' + textStatus);
					console.log('errorThrown: ' + errorThrown);
				},
				success: function (data) {
					$('#api-reviews-stat').replaceWith(data);
				}
			});
		}
	};

	$.fn.apiReviewsStat = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviewsStat');
		}
	};

})(jQuery);