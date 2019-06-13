/*!
 * $.fn.apiForm
 */
(function ($, undefined ) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};
	var options = {};

	var methods = {
		init: function (params) {

			var options = $.extend(true, {}, defaults, options, params);

			if (!this.data('apiForm')) {
				this.data('apiForm', options);
			}

			if($(this).hasClass('api_form_style')){

				//-----------------------------------//
				//            api_checkbox           //
				//-----------------------------------//
				$(this).find('.api_checkbox').on('click touch', function (e) {
					e.preventDefault();

					if (!$(this).is('.api_active')) {
						$(this).addClass('api_active').find(':checkbox').prop('checked', true).change();
					}
					else {
						$(this).removeClass('api_active').find(':checkbox').prop('checked', false).change();
					}
				});


				//-----------------------------------//
				//            api_radio              //
				//-----------------------------------//
				$(this).find('.api_radio').on('click touch', function (e) {
					e.preventDefault();

					$(this).addClass('api_active').siblings().removeClass('api_active');
					$(this).find(':radio').prop('checked', true).change();

				});
			}

			return this;
		}
	};

	$.fn.apiForm = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiForm');
		}
	};

})(jQuery);