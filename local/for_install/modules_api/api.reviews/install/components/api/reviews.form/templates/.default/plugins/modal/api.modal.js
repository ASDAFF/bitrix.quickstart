/**
 * $.fn.apiModal
 */
(function ($) {

	// настройки со значением по умолчанию
	var defaults = {
		id: ''
	};

	// публичные методы
	var methods = {

		// инициализация плагина
		init: function (params) {

			// актуальные настройки, будут индивидуальными при каждом запуске
			var options = $.extend({}, defaults, options, params);

			// инициализируем лишь единожды
			if (!this.data('apiModal')) {

				// закинем настройки в реестр data
				this.data('apiModal', options);

				// код плагина

				$(document).on('click', '.api-modal, .api-modal-close', function (e) {
					e.preventDefault();

					$('.api-modal .api-modal-dialog').css({
						'transform': 'translateY(-200px)',
						'-webkit-transform': 'translateY(-200px)'
					});
					$('.api-modal').animate({opacity: 0}, 300, function () {
						$(this).hide().removeClass('api-modal-open');
						$('html').removeClass('api-modal-active');
					});
				});


				$(document).on('click', '.api-modal .api-modal-dialog', function (e) {
					//e.preventDefault();
					e.stopPropagation();
				});
			}

			return this;
		},
		show: function (options) {
			$('html').addClass('api-modal-active');

			var modal =  $(options.id);
			var dialog =  $(options.id + ' .api-modal-dialog');

			dialog.removeAttr('style');
			modal.show().animate({opacity: 1}, 1, function () {

				var dh  = dialog.outerHeight(),
				    pad = parseInt(dialog.css('margin-top'), 10) + parseInt(dialog.css('margin-bottom'), 10);

				if ((dh + pad) < window.innerHeight) {
					dialog.css({top: (window.innerHeight - (dh + pad)) / 2});
				} else {
					dialog.css({top: ''});
				}

				modal.addClass('api-modal-open');
			});
		},
		resize: function () {

			var dialog = $('.api-modal .api-modal-dialog');

			$('.api-modal.api-modal-open').each(function () {
				var dh  = dialog.outerHeight(),
				    pad = parseInt(dialog.css('margin-top'), 10) + parseInt(dialog.css('margin-bottom'), 10);

				if ((dh + pad) < window.innerHeight) {
					dialog.animate({top: (window.innerHeight - (dh + pad)) / 2}, 100);
				} else {
					dialog.animate({top: ''}, 100);
				}
			});

			/*
			 var timeout;
			 var wait   = 150;

			 clearTimeout(timeout);
			timeout = setTimeout(function () {
				//Yeah buddy, light weight baby!!!
			}, wait);
			*/
		},
		alert: function (options) {

			/*
			$.fn.apiModal('alert',{
				type: 'success',
			  autoHide: true,
				modalId: modalId,
				message: data.MESSAGE
			});
			*/
			var dialogStyle = $(options.modalId + ' .api-modal-dialog').attr('style');

			var content = '' +
				 '<div class="api-modal-dialog api-alert" style="'+dialogStyle+'">' +
					 '<div class="api-modal-close"></div>' +
					 '<div class="api-alert-'+options.type+'">' +
						 '<span></span>' +
						 '<div class="api-alert-title">'+options.message+'</div>' +
					 '</div>' +
				 '</div>';

			$(options.modalId).html(content);
			$.fn.apiModal('resize');

			if(options.autoHide)
			{
				window.setTimeout(function(){
					$.fn.apiModal('hide', options);

					//TODO: say what?!
					$.fn.apiReviewsList('refresh');
				},options.autoHide);
			}
		},
		hide: function (options) {
			$(options.modalId).hide().removeClass('api-modal-open');
			$('html').removeClass('api-modal-active');
		}
	};

	$.fn.apiModal = function (method) {
		if (methods[method]) {
			// если запрашиваемый метод существует, мы его вызываем
			// все параметры, кроме имени метода прийдут в метод
			// this так же перекочует в метод
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			// если первым параметром идет объект, либо совсем пусто
			// выполняем метод init
			return methods.init.apply(this, arguments);
		} else {
			// если ничего не получилось
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiModal');
		}
	};

	$(window).on('resize', function () {
		$.fn.apiModal('resize');
	});

	//$(window).trigger('resize');

	$.fn.apiModal('init');

})(jQuery);