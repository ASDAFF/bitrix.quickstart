/**
 * $.fn.apiModal
 */
(function ($) {

	// настройки со значением по умолчанию
	var defaults = {
		id: '',
		header: '',
		footer: '',
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
				$(window).on('resize', function () {
					$.fn.apiModal('resize',options);
				});

				$(document).on('click', '.api_modal, .api_modal_close', function (e) {
					e.preventDefault();

					$('.api_modal .api_modal_dialog').css({
						'transform': 'translateY(-200px)',
						'-webkit-transform': 'translateY(-200px)'
					});
					$('.api_modal').animate({opacity: 0}, 250, function () {
						$(this).hide().removeClass('api_modal_open');
						$('html').removeClass('api_modal_active');
					});
				});
				$(document).on('click', '.api_modal .api_modal_dialog', function (e) {
					//e.preventDefault();
					e.stopPropagation();
				});
			}

			return this;
		},
		show: function (options) {
			$('html').addClass('api_modal_active');
			if(options.header){
				$(options.id + ' .api_modal_header').html(options.header);
			}
			$(options.id + ' .api_modal_dialog').removeAttr('style');
			$(options.id).show().animate({opacity: 1}, 1, function () {
				$(this).addClass('api_modal_open');
				$.fn.apiModal('resize',options);
			});
		},
		resize: function (options) {

			var dialog = options.id + ' .api_modal_dialog';

			if(options.width){
				$(dialog).width(options.width);
			}

			if($(options.id + '.api_modal_open').length){
				var dh  = $(dialog).outerHeight(),
				    pad = parseInt($(dialog).css('margin-top'), 10) + parseInt($(dialog).css('margin-bottom'), 10);

				if ((dh + pad) < window.innerHeight) {
					$(dialog).animate({top: (window.innerHeight - (dh + pad)) / 2}, 100);
				} else {
					$(dialog).animate({top: ''}, 100);
				}
			}

		},
		hide: function (options) {
			$(options.id).hide().removeClass('api_modal_open');
			$('html').removeClass('api_modal_active');
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

})(jQuery);