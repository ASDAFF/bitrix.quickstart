/**
 * $.fn.apiAuthAjax
 */
(function ($) {
	var modal    = '';
	var auth     = '';
	var defaults = {};
	var options  = {};
	var methods  = {
		init: function (params) {

			options = $.extend({}, defaults, options, params);

			if (!this.data('apiAuthAjax')) {
				this.data('apiAuthAjax', options);

				//$.fn.apiAuthAjax('initModal');

				$(auth).on('click', '.api_link', function (e) {
					//console.log($(this).data('type'));
					$.ajax({
						type: 'POST',
						url: options.ajaxUrl,
						dataType: 'html',
						data: {
							API_AUTH_AJAX: 1,
							sessid: BX.message('bitrix_sessid'),
							siteId: BX.message('SITE_ID'),
							type: $(this).data('type')
						},
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
						},
						success: function (data) {
							$(modal).find('.api_auth_modal_content').html(data);
							$.fn.apiAuthAjax('showModal');
							$.fn.apiAuthAjax('getCss');
							$.fn.apiAuthAjax('getJs');
							$.fn.apiTab('init');
						}
					});
					e.preventDefault();
				});
			}
			return this;
		},
		initModal: function () {
			var modalId   = modal.replace('#', '');
			var modalHtml = '' +
				 '<div id="' + modalId + '" class="api_auth_modal fade">' +
				 '<div class="api_auth_modal_dialog">' +
				 '<div class="api_auth_modal_close api_auth_close"></div>' +
				 '<div class="api_auth_modal_content"></div>' +
				 '</div>' +
				 '</div>';
			$('body').append(modalHtml);

			$(document).on('click', '.api_auth_modal, .api_auth_modal_close', function (e) {
				e.preventDefault();
				$.fn.apiAuthAjax('hideModal');
			});
			$(document).on('click', '.api_auth_modal_dialog', function (e) {
				e.preventDefault();
				e.stopPropagation();
			});

		},
		showModal: function () {
			$('html').addClass('api_auth_modal_active');
			$(modal).show().animate({opacity: 1}, 1, function () {
				$(modal).addClass('api_auth_open');
				$.fn.apiAuthAjax('resize');
			});
		},
		hideModal: function () {
			$('html').removeClass('api_auth_modal_active');
			$(modal).removeClass('api_auth_open');
			$(modal).find('.api_auth_modal_dialog').css({
				'transform': 'translateY(-200px)',
				'-webkit-transform': 'translateY(-200px)'
			});
			$(modal).animate({opacity: 0}, 200, function () {
				$(this).hide().find('.api_auth_modal_dialog').removeAttr('style');
			});
		},
		resize: function () {
			var dialog = $(modal).find('.api_auth_modal_dialog');
			$('.api_auth_modal.api_auth_open').each(function () {
				var dh  = dialog.outerHeight(),
				    pad = parseInt(dialog.css('margin-top'), 10) + parseInt(dialog.css('margin-bottom'), 10);

				if ((dh + pad) < window.innerHeight) {
					dialog.animate({top: (window.innerHeight - (dh + pad)) / 2}, 100);
				} else {
					dialog.animate({top: ''}, 100);
				}
			});
		},
		getCss: function () {
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
		}
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