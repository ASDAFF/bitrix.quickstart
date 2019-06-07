/*!
 * $.fn.apiReviewsFilter
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};

	var methods = {

		init: function (params) {

			var options = $.extend(true, {}, defaults, params);

			if (!this.data('apiReviewsFilter')) {
				this.data('apiReviewsFilter', options);

				var reviews = '#reviews';
				var reviewsList = '#reviews .api-reviews-list';

				$(reviews).on('click','.api-reviews-filter a',function(e){
					var href = $(this).attr('href');
					if(href.length){
						$.fn.apiWait('show');
						$.ajax({
							type: 'POST',
							url: href,
							data: {
								sessid: BX.bitrix_sessid(),
								API_REVIEWS_LIST_AJAX: 'Y'
							},
							error: function (jqXHR, textStatus, errorThrown) {
								console.log('textStatus: ' + textStatus);
								console.log('errorThrown: ' + errorThrown);
							},
							success: function (data) {
								$(reviewsList).replaceWith(data);
								$.fn.apiReviewsList('refreshGallery',reviewsList);
								$.fn.apiWait('hide');
							}
						});
					}

					/**
					 @param {Object} [data]
					 @param {string} [title]
					 @param {string} [url]
					 @return {void}
					 */
					history.pushState({}, '', href);

					e.preventDefault();
					return false;
				});

				$(reviews).on('click', '.js-delFilter', function (e) {
					var href = $(this).parent().attr('href');

					$.fn.apiWait('show');
					$.ajax({
						type: 'POST',
						url: href,
						data: {
							sessid: BX.bitrix_sessid(),
							API_REVIEWS_LIST_AJAX: 'Y',
							API_FILTER: 'DEL',
							API_RATING: $(this).data('rating'),
						},
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
						},
						success: function (data) {
							$(reviewsList).replaceWith(data);
							$.fn.apiReviewsList('refreshGallery',reviewsList);
							$.fn.apiWait('hide');
						}
					});

					e.preventDefault();
					return false;
				});

			}

			return this;
		},

	};

	$.fn.apiReviewsFilter = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviewsFilter');
		}
	};

})(jQuery);