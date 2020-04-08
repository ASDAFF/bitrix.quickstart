/**
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

'use strict';

/**
 * XXX: Individ namespace
 */
var indi = new function()
{
	/**
	 * Признак инициализации
	 *
	 * @var boolean
	 */
	var initialized = false;

	/**
	 * Инициализирует все функциональные части
	 *
	 * @return void
	 */
	this.init = function()
	{
		if (initialized) {
			return;
		}
		initialized = true;

		indi.app.init();
		indi.ui.init();
	};
};




/**
 * XXX: Приложение
 */
indi.app = new function()
{
	/**
	 * Инициализирует приложение
	 *
	 * @return void
	 */
	this.init = function()
	{
		//Инициализируем локаль
		this.locale.init();

		//Инициализируем функциональные блоки
		this.blocks.init();
	};
};




/**
 * XXX: Функциональные блоки приложения
 */
indi.app.blocks = new function()
{
	/**
	 * Ф-ии обратного вызова о готовности всех функциональных блоков
	 *
	 * @var array
	 */
	var initCallbacks = [];

	/**
	 * Зарезервированные названия блоков, которые нельзя переопределять
	 *
	 * @var object
	 */
	var reserved = {};

	/**
	 * Добавляет ф-ю обратного вызова о готовности всех блоков
	 *
	 * @param object callback Ф-я обратного вызова
	 * @param object context Контекст вызова ф-ии
	 * @return void
	 */
	this.onInit = function(callback, context)
	{
		initCallbacks.push({
			callback: callback,
			context: context = context || this
		});
	};

	/**
	 * Инициализирует функциональные блоки
	 *
	 * @return void
	 */
	this.init = function()
	{
		for (var key in this) {
			if (reserved[key]) {
				continue;
			}

			var blockConstructor = this[key];
			if (typeof blockConstructor == 'function') {
				var blockExists = blockConstructor.exists ? blockConstructor.exists() : true;
				if (blockExists) {
					this[key] = new blockConstructor();
				}
			}
		}

		for (var i = 0; i < initCallbacks.length; i++) {
			initCallbacks[i].callback.call(
				initCallbacks[i].context
			);
		};
	};

	//Заполняем зарезервированные названия блоков
	for (var key in this) {
		reserved[key] = true;
	}
};




/**
 * XXX: Локаль
 */
indi.app.locale = new function()
{
	/**
	 * Настройки локали
	 *
	 * @var object
	 */
	this.settings = {
		date: 'dd.mm.yy',
		time: 'hh:mm',
		dateTime: 'dd.mm.yy hh:mm',
		firstDay: 1,
		isRTL: false
	};

	/**
	 * Сообщения локали
	 *
	 * @var object
	 */
	this.messages = {
		monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
		close: 'Close',
		datePicker: {
			prev: '&larr;Previous',
			next: 'Next&rarr;',
			current: 'Today',
			showMonthAfterYear: false,
			weekHeader: 'Wk',
			yearSuffix: ''
		},
		timePicker: {
			timeOnlyTitle: 'Choose time',
			timeText: 'Time',
			hourText: 'Hours',
			minuteText: 'Minutes',
			secondText: 'Seconds',
			millisecText: 'Milliseconds',
			currentText: 'Now',
			ampm: true
		},
		alert: {
			title: 'System message',
			ok: 'Ok'
		}
	};

	/**
	 * XXX: Возвращает отформатированную строку
	 * Требуется jQuery UI datepicker.
	 *
	 * @param Date date Дата для форматирования
	 * @param String format Формат (по-умолчанию indi.app.locale.settings.date)
	 * @return String
	 */
	this.format = function(date, format)
	{
		format = format === undefined ? this.settings.date : format;

		return $.datepicker.formatDate(format, date);
	};

	/**
	 * XXX: Разбирает отформатированную строку
	 * Требуется jQuery UI datepicker.
	 *
	 * @param String date Отформатированная дата
	 * @param String format Формат (по-умолчанию indi.app.locale.settings.date)
	 * @return Date
	 */
	this.parse = function(date, format)
	{
		format = format === undefined ? this.settings.date : format;

		return $.datepicker.parseDate(format, date);
	};

	/**
	 * Инициализирует локаль
	 *
	 * @return void
	 */
	this.init = function()
	{
		if($.datepicker)
		{
			$.datepicker.regional[''] = {
				closeText: this.messages.close,
				prevText: this.messages.datePicker.prev,
				nextText: this.messages.datePicker.next,
				currentText: this.messages.datePicker.current,
				monthNames: this.messages.monthNames,
				monthNamesShort: this.messages.monthNamesShort,
				dayNames: this.messages.dayNames,
				dayNamesShort: this.messages.dayNamesShort,
				dayNamesMin: this.messages.dayNamesMin,
				weekHeader: this.messages.datePicker.weekHeader,
				dateFormat: this.settings.date,
				firstDay: this.settings.firstDay,
				isRTL: this.settings.isRTL,
				showMonthAfterYear: this.messages.datePicker.showMonthAfterYear,
				yearSuffix: this.messages.datePicker.yearSuffix
			};
			$.datepicker.setDefaults($.datepicker.regional['']);
		}

		if($.timepicker)
		{
			$.timepicker.regional[''] = {
				timeOnlyTitle: this.messages.timePicker.timeOnlyTitle,
				timeText: this.messages.timePicker.timeText,
				hourText: this.messages.timePicker.hourText,
				minuteText: this.messages.timePicker.minuteText,
				secondText: this.messages.timePicker.secondText,
				millisecText: this.messages.timePicker.millisecText,
				currentText: this.messages.timePicker.currentText,
				closeText: this.messages.close,
				ampm: this.messages.timePicker.ampm
			};
			$.timepicker.setDefaults($.timepicker.regional['']);
		}
	};
};




/**
 * XXX: User interface
 */
indi.ui = new function()
{
	/**
	 * Ф-ии обратного вызова о готовности виджетов
	 *
	 * @var array
	 */
	var initCallbacks = [];

	/**
	 * XXX: Отображает диалог с сообщением
	 *
	 * @param string messge Текст сообщения
	 * @param object config Конфигурация диалога
	 * Конфиг может содержать следующие параметры type - тип оповещения(модальное окно или замещение элементов),
	 * status - статус оповещения (success, warning, notification и т.д.), elem - заменяемый элемент обязателен для замещения элементов
	 * @return void
	 */
	this.alert = function(message, config)
	{
		config = $.extend(
			{type: 'modal', engine: 'bootstrap', status: 'notification'},
			config || {}
		);

		switch(config.type){
			case 'modal':
				if( config.engine == 'fancybox' && typeof $.fancybox == 'function' ){
					var message =	'<p class="notification notification_' + config.status + '">' +
						'<span class="notification__text">' + message + '</span>' +
						'</p>';
					$.fancybox(message);
				}
				else{
					var message = '<div id="notification-modal" class="modal fade" tabindex="-1" role="dialog">' +
						'<div class="modal-dialog">' +
						'<div class="modal-content">' +
						'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
						'<div class="modal-body">' +
						'<p class="notification notification_' + config.status + '">' +
						'<span class="notification__text">' + message + '</span>' +
						'</p>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'</div>';
					$(message).appendTo('body').modal('show');
				}

				break;

			case 'elem':
				if( !(config.elem.length) ){
					return false;
				}

				var $messHtml = '<p class="notification notification_' + config.status + '">' +
					'<span class="notification__text">' + message + '</span>' +
					'</p>';
				config.elem.after($messHtml).hide();
		}
	};

	/**
	 * Добавляет ф-ю обратного вызова при инициализации UI
	 *
	 * @param object callback Ф-я обратного вызова
	 * @param object context Контекст вызова ф-ии
	 * @return void
	 */
	this.onInit = function(callback, context)
	{
		initCallbacks.push({
			callback: callback,
			context: context || this
		});
	};

	/**
	 * Инициализирует UI
	 *
	 * @param string selector Родительский элемент
	 * @return void
	 */
	this.init = function(selector)
	{
		if (selector === undefined) {
			selector = $('body');
		} else {
			selector = $(selector);
		}

		this.widgets.init(selector);

		for (var i = 0; i < initCallbacks.length; i++) {
			initCallbacks[i].callback.call(
				initCallbacks[i].context,
				selector

			);
		};
	}
};

/**
 * XXX: Виджеты
 */
indi.ui.widgets = new function()
{
	/**
	 * Конструкторы виджетов
	 *
	 * @var object
	 */
	this.items = {};

	/**
	 * XXX: Создает стилизованное поле загрузки файла. Если указан multiple - множественная загрузка
	 * Пример вызова:
	 * <form method="post"enctype="multipart/form-data">
	 * <div class="span_file js-file-add">
	 *		 <input name="FILE[]" multiple data-max-files="3" type="file" class="widget uploadpicker" placeholder="Прикрепить файл">
	 *		 <span class="question question--file" title="Например, фото продукта. Не более 5 штук."></span>
	 *		 <div class="is-label">
	 *			 <small class="block-form__delete">
	 *			    <span class="upload-field-label"><i class="js-file-reset"></i></span>
	 *			 </small>
	 *		 </div>
	 *	 </div>
	 *	 <input type="submit">
	 * </form>
	 *
	 * @param string|object selector Селектор узлов DOM
	 * @return void
	 */
	this.items['uploadpicker'] = function(selector)
	{
		/*if (!$.uploadpicker) {
			return;
		}*/

		$(selector).each(function() {
			var field = $(this);
			var required = field.is('[required]');
			var disabled = field.is('[disabled]');
			var isMultiple = field.is('[multiple]');
			var placeholder = field.attr('placeholder');
			var maxFiles = field.data("max-files");
			if(!maxFiles)
				maxFiles = 10;

			field
				.addClass('upload-field-overlay')
				.removeAttr('required')
				.css({
					cursor: 'pointer',
					fontSize: '200px',
					height: 'auto',
					opacity: 0,
					position: 'absolute',
					right: 0,
					top: '-0.5em',
					width: 'auto'
				})
				.wrap('<span class="widget-upload-field"/>')

			var wrapper = field.parent();
			wrapper
				.css({
					backgroundColor: 'transparent',
					display: 'block',
					overflow: 'hidden',
					position: 'relative'
				})
				.prepend('<input class="upload-field-value form-control" type="text"'
					+ (required ? ' required=""' : '')
					+ (disabled ? ' disabled=""' : '')
					+ (placeholder ? ' placeholder="' + placeholder + '"' : '')
					+ ' />')
				.prepend("<a class='add-file fake'>"+field.attr("placeholder")+"</a>");
			var $fileAdd = wrapper.closest(".js-file-add");
			var $fileAddLabel = $fileAdd.find('.upload-field-label');
			field.on({'change': function() {
				//Если множественная загрузка
				if(isMultiple){
					if($(this).closest(".js-new-add-container").length == 0){
						$(this).closest(".span_file").wrap("<div class='js-new-add-container'></div>");
					}
					var count = selector.closest(".js-new-add-container").find(".js-file-add").length;
					/*console.log(maxFiles);
					console.log(count);*/
					if(maxFiles < count){
						alert("Можно прикрепить максимум "+maxFiles+" файлов");
						return false;
					}

					$(this).closest(".js-new-add-container").append('<div class="span_file span_file--new js-file-add js-file-add--new">'+
						'<input  name="FILE[]" type="file" multiple class="widget uploadpicker js-new-uploadpicker js-new-uploadpicker-'+count+'" data-max-files="'+maxFiles+'" placeholder="Еще">'+
						'<div class="is-label">'+
						'<small class="block-form__delete">'+
						'<span class="upload-field-label"><i class="js-file-reset"></i></span>'+
						'</small>'+
						'</div>'+
						'</div>');
					indi.ui.widgets.items.uploadpicker($(".js-new-uploadpicker-"+count));
					$(this).closest(".js-new-add-container").find(".js-file-add--new:not(:last-child)").find(".add-file").remove();
				}

				var values = [this.value.split(/[\/\\]/).pop()];
				if (this.files) {
					values = [];
					for (var i = 0; i < this.files.length; i++) {
						values.push(this.files[i].name);
					}
				}
				values.length ? wrapper.addClass('has-value') : wrapper.removeClass('has-value');
				wrapper.find('.upload-field-value').val(values.join(', '));
			}, 'keypress': function(event) {
				if (event.key == 'Backspace') {
					if (this.value) {
						event.preventDefault();
						this.value = '';
						$(this).trigger('change');
					}
				}
			}
			});
		});
	};

	/**
	 * XXX: Создает поле выбора даты
	 *
	 * @param string|object selector Селектор узлов DOM
	 * @return void
	 * Параметр config передается в таком виде data-config='{"minDate": 0}'
	 */
	this.items['datepicker'] = function (selector) {
		if (!$.datepicker) {
			return;
		}

		var defaults = indi.ui.widgets.items.datepicker.defaults;

		$(selector).each(function () {
			var element = $(this);

			var $datapickerWrap = $(element).parent();
			$datapickerWrap.on('click', '.datepicker-clear', function() {
				var $iconDatePicker = $datapickerWrap.find('.datepicker-icon');
				element.datepicker('setDate', null);
				$iconDatePicker.show();
				$(this).hide();
			});

			element.datepicker($.extend(
				{},
				defaults,
				element.data('config') || {}
			));

			if (element.is(':input')) {
				element
					.keypress(function (event) {
						switch (event.keyCode) {
							case 13:
								//Если Enter нажимается при открытом календаре - убираем каленрарь и сабмитим форму
								if ($(this).datepicker('widget').css('display') != 'none') {
									event.stopPropagation();
									$(this).datepicker('hide');
									if (this.form) {
										$(this.form).trigger('submit');
									}
								}
								break;
						}
					})
					.after(
						'<span class="datepicker-icon" title="Выбрать дату"></span>'
					)
					.mask("99.99.9999")
					.next('.datepicker-icon')
					.click(function () {
						$(this).prev('.datepicker').datepicker('show');
					});
			}
		});
	};

	this.items['datepicker'].defaults = {
		changeMonth: false,
		changeYear: false,
		minDate: new Date(1900, 1, 1),
		maxDate: '',
		onClose: function (selectedDate, widgetInstance) {
			if ($('.startDate').length && $('.endDate').length) {
				if ($(widgetInstance.input[0]).hasClass('startDate'))
					$('.endDate').datepicker("option", "minDate", selectedDate);
				else if ($(widgetInstance.input[0]).hasClass('endDate'))
					$('.startDate').datepicker("option", "maxDate", selectedDate);
			}
		},
		onSelect: function(date, inst) {
			var $iconClear = $(this).parent().find('.datepicker-clear');
			var $iconDatePicker = $(this).parent().find('.datepicker-icon');
			if(!$iconClear.length) {
				$(this).after(
					'<span class="datepicker-clear" title="Очистить дату"></span>'
				)
			}
			else {
				$iconClear.show();
			}

			$iconDatePicker.hide();
		}
	};

	/**
	 * XXX: Создает поле выбора времени
	 *
	 * @param string|object selector Селектор узлов DOM
	 * @return void
	 */
	this.items['timepicker'] = function(selector)
	{
		if (!$.timepicker || !$.mask) {
			return;
		}

		$(selector)
			.timepicker()
			.mask('99:99');
	};

	/**
	 * Виджет кастомизации селектов
	 * http://selectize.github.io/selectize.js/
	 *
	 * @param selector
	 */
	this.items['selectize'] = function(selector)
	{
		if (!$.fn.selectize) {
			return;
		}

		var defaults = indi.ui.widgets.items.selectize.defaults;

		$(selector).each(function() {
			var element = $(this);

			var config = $.extend(
				{},
				defaults,
				element.data('config') || {}
			)

			element.selectize(config);
		});
	};

	this.items['selectize'].defaults = {};


	/**
	 * Виджет слайдера
	 * http://kenwheeler.github.io/slick/
	 *
	 * @param selector
	 */
	this.items['slick'] = function(selector)
	{

		if (!$.fn.slick) {
			return;
		}

		var defaults = indi.ui.widgets.items.slick.defaults;

		$(selector).each(function() {
			var element = $(this);

			var config = $.extend(
				{},
				defaults,
				element.data('config') || {}
			)

			element.slick(config);
		});
	};

	this.items['slick'].defaults = {
		initialSlide: 0,
		autoplay: true,
		autoplaySpeed: 5000
	};

	/**
	 * XXX: Создает переключатель в виде закладок (табов)
	 * Примерный markup:
	 * <div class="widget tabpane">
	 * 	<ul class="tabs">
	 * 		<li><a class="fake" href="#tab-1">Tab 1</a></li>
	 * 		<li><a class="fake" href="#tab-2">Tab 2</a></li>
	 * 	</ul>
	 * 	<div class="panes">
	 * 		<div id="tab-1" class="pane">Content 1</div>
	 * 		<div id="tab-2" class="pane">Content 2</div>
	 * 	</div>
	 * </div>
	 *
	 * @param string|object selector Селектор узлов DOM
	 * @return void
	 */
	this.items['tabpane'] = function(selector)
	{
		var defaults = indi.ui.widgets.items.tabpane.defaults;

		$(selector).each(function() {
			//Виджет
			var tabPane = $(this);

			//Конфигурация
			var config = $.extend(
				{},
				defaults,
				tabPane.data('config') || {}
			);

			//Ищет контроллер закладки с заданным hash
			var findTabController = function(hash) {
				return tabPane.find(config.tabsSelector + ' a[href="' + hash + '"]');
			};

			//Активирует закладку с заданным hash
			var activateTab = function(hash, reason) {
				if (hash.substr(0, 1) != '#') {
					if (reason == 'click') {
						document.location.href = hash;
					}
					return;
				}
				var controller = findTabController(hash);
				var item = controller.closest('li').length ? controller.closest('li') : controller;
				item
					.addClass('active')
					.siblings().removeClass('active');

				tabPane.find(config.tabsSelector + ' a').each(function() {
					var href = $(this).attr('href');
					if (href.substr(0, 1) == '#') {
						var pane = tabPane.find(
							config.panesSelector + href
						);
						if (this === controller.get(0)) {
							pane
								.show()
								.trigger('widget.tabpane:show')
						} else {
							pane
								.hide()
								.trigger('widget.tabpane:hide');
						}
					}
				});

				//Если виджет в виджете, то нужно запустить цепочку переключений вверх по DOM
				if (!tabPane.is(':visible')) {
					tabPane.closest(config.panesSelector).trigger('widget.tabpane:activate');
				}
			};

			//Событие активации, позволяет управлять виджетом извне
			tabPane.on('widget.tabpane:activate', function(event) {
				var id = $(event.target).attr('id');
				if (id) {
					activateTab('#' + id, 'activate');
				}
			});

			//Класс закладок
			if (config.tabsClass) {
				tabPane.find(config.tabsSelector).addClass(config.tabsClass);
			}

			//Клики по закладкам
			tabPane.find(config.tabsSelector + ' a').click(function(event) {
				if (event.isPropagationStopped()) {
					return false;
				}
				event.stopPropagation();

				activateTab($(this).attr('href'), 'click');

				return false;
			});

			//Клики по родителям закладок
			if (config.tabsHandleParents) {
				tabPane.find(config.tabsSelector + ' a').parent().click(function(event) {
					$(this).find('a').trigger('click');
				});
			}

			//Класс панелей
			if (config.panesClass) {
				tabPane.find(config.panesSelector).addClass(config.panesClass);
			}

			//Заголовки панелей
			if (config.panesTitleAdd) {
				tabPane.find(config.tabsSelector + ' a').each(function() {
					var href = $(this).attr('href');
					if (href.substr(0, 1) == '#') {
						var pane = tabPane.find(
							config.panesSelector + href
						);
						$('<' + config.panesTitleTag + '/>')
							.addClass(config.panesTitleClass)
							.html($(this).html())
							.prependTo(pane);
					}
				});
			}

			//Активная по умолчанию закладка
			var activeTabLink = null;
			if (config.followHash) {
				activeTabLink = findTabController(document.location.hash);
			}
			if (!activeTabLink || !activeTabLink.length) {
				activeTabLink = tabPane.find(config.tabsSelector + ' a:first');
			}
			if (activeTabLink.length) {
				activateTab(activeTabLink.attr('href'), 'init');
			}
		});
	};

	this.items['tabpane'].defaults = {
		tabsSelector: '> .tabs',//Селектор закладок
		tabsClass: '',//Добавить класс для закладок
		tabsHandleParents: true,//Отслеживать клики по родителям ссылок в закладках
		panesSelector: '> .panes > .pane',//Селектор панелей
		panesClass: '',//Добавить класс в каждую панель
		panesTitleAdd: false,//Добавлять заголовок перед панелями
		panesTitleTag: 'h2',//Тег для заголовка панели
		panesTitleClass: 'pane-title',//Класс для заголовка панели
		followHash: true//Активировать закладку по hash в document.location
	};

	/**
	 * XXX: Создает таблицу с thead, фиксирующимся при прокрутке документа в окне браузера
	 *
	 * @param string|object selector Селектор узлов DOM
	 * @return void
	 */
	this.items['fixedtable'] = function(selector)
	{
		$(selector).each(function() {
			var table = $(this);
			var thead = table.find('> thead').eq(0);
			var isFixed = null;

			var applyWidth = function(cell) {
				cell.find('> div').width(cell.width());
			};

			var onScroll = function() {
				var scrollTop = $(window).scrollTop();
				var offset = table.offset();

				if (
					scrollTop > offset.top
					&& scrollTop < offset.top + table.height() - thead.height()
				) {
					if(isFixed !== true) {
						isFixed = true;
						thead.css({'position': 'fixed'});
					}
				} else {
					if (isFixed !== false) {
						isFixed = false;
						thead.css({'position': 'absolute'});
					}
				}
			};

			table.css('position', 'relative');

			thead
				.width(thead.width())
				.height(thead.height());

			table.find('> thead > tr > *, > tbody > tr > *').wrapInner(
				$('<div>')
					.addClass('fixedtable-cell-wrapper')
					.css('min-height', '1px')
			);
			table.find('> thead > tr > *').each(function() {
				applyWidth($(this));
			});
			table.find('> tbody > tr:first > *').each(function() {
				applyWidth($(this));
			});

			var theadPlacheholder = thead
				.clone()
				.css('visibility', 'hidden');
			theadPlacheholder.insertAfter(thead);

			thead.css({'top': '0'});

			$(window).scroll(onScroll);
			onScroll();
		});
	};

	/**
	 * XXX: Создает таблицу с интерлейсом
	 *
	 * @param string|object selector Селектор узлов DOM
	 * @return void
	 */
	this.items['interlace'] = function(selector)
	{
		$(selector).find('tr:even').addClass('even');
		$(selector).find('tr:odd').addClass('odd');
	};

	/**
	 * Инициализирует виджеты
	 *
	 * @param string selector Родительский элемент
	 * @return void
	 */
	this.init = function(selector)
	{
		if (selector === undefined) {
			selector = $('body');
		} else {
			selector = $(selector);
		}

		$.each(this.items, function(name) {
			this.call(this, selector.find('.widget.' + name));
		});
	};
};




/**
 * XXX: Создает индикатор загрузки
 * Пример использования:
 * var loading = new indi.ui.loading('#content');
 * ...
 * loading.hide();
 *
 * @param string|object selector Селектор элемента, у которого отображается индикатор
 * @return void
 */
indi.ui.loading = function(selector)
{
	if (indi.ui.loading.template === undefined) {
		indi.ui.loading.template = '<div class="loading-layer"></div>' +
			'<div class="loading-icon">' +
			$('#loading-indicator-template').html() +
			'</div>';
	}

	/**
	 * Показывает индикатор загрузки
	 *
	 * @return void
	 */
	this.show = function()
	{
		$(selector)
			.addClass('loading-indicator')
			.append(indi.ui.loading.template);
	};

	/**
	 * Скрывает индикатор загрузки
	 *
	 * @return void
	 */
	this.hide = function()
	{
		$(selector)
			.removeClass('loading-indicator')
			.find('> .loading-layer, > .loading-icon').remove();
	};

	selector = selector || 'body';

	this.show();
};

/**
 * XXX: Работа с историей браузера
 *
 * @param string callback Имя ф-ии обратного вызова, вызываемой при переходах по истории
 * @param object data Данные текущей страницы
 */
indi.history = function(callback, data)
{
	/**
	 * Формирует history state
	 *
	 * @param object data State data
	 * @return object
	 */
	var buildState = function(data)
	{
		var state = {
			data: data instanceof Object ? data : {}
		};

		if (state.data.title === undefined) {
			state.data.title = document.title;
		}

		state.generatedByIndiHistory = true;
		state.handledBy = callback;

		return state;
	};

	/**
	 * XXX: Добавляет обработчик браузерного события
	 *
	 * @return void
	 */
	var addListener = function()
	{
		if (window.addEventListener) {
			window.addEventListener('popstate', function(event) {
				//Пропускаем события, созданные не через indi.history (например, Chrome такие генерирует)
				if (!(event.state instanceof Object)
					|| event.state.generatedByIndiHistory === undefined
				) {
					return;
				}

				if (event.state.data !== undefined
					&& event.state.data.title !== undefined
				) {
					document.title = event.state.data.title;
				}

				//Дергаем ф-ии обратного вызова только для своих хозяев
				if (event.state.handledBy && event.state.handledBy == callback) {
					(new Function(
						'url, data',
						event.state.handledBy + '(url, data);'
					))(
						document.location.href,
						event.state.data
					);
				}
			}, false);
		}
	};

	/**
	 * XXX: Возвращает текущий пункт истории
	 *
	 * @return object
	 */
	this.current = function()
	{
		return history.state;
	};

	/**
	 * XXX: Обновляет текущий пункт истории
	 *
	 * @param string url URL
	 * @param object data Данные
	 * @return void
	 */
	this.replace = function(url, data)
	{
		if (history.replaceState) {
			var state = buildState(data);

			history.replaceState(state, state.data.title, indi.utils.url.getFull(url));
		}
	};

	/**
	 * XXX: Добавляет новый пункт в историю
	 *
	 * @param string url URL
	 * @param object data Данные
	 * @return void
	 */
	this.push = function(url, data)
	{
		if (history.pushState) {
			var state = buildState(data);

			history.pushState(state, state.data.title, indi.utils.url.getFull(url));
		}
	};




	/**
	 * Инициализия
	 */
	this.replace(document.location.href, data);

	addListener();
};

/**
 * Позволяет получить singleton instance indi.history
 *
 * @param string callback Имя ф-ии обратного вызова, вызываемой при переходах
 * @param object data Данные текущей страницы
 *
 * @return indi.history
 */
indi.history.getInstance = function(callback, data)
{
	if (this.instances === undefined) {
		this.instances = {};
	}

	if (this.instances[callback] === undefined) {
		this.instances[callback] = new indi.history(callback, data);
	}

	return this.instances[callback];
};




/**
 * XXX: Утилиты для работы с Cookie
 */
indi.cookie = {
	/**
	 * XXX: Set cookie
	 *
	 * @param string name Cookie name
	 * @param string value Cookie value
	 * @param string time Lifetime
	 * @param string path
	 * @return void
	 */
	set: function(name, value, time, path)
	{
		var time = time === undefined ? 0 : time;
		var path = path === undefined ? '/' : path;

		var expires = new Date();
		time = expires.getTime() + time * 1000;
		expires.setTime(time);

		document.cookie = name + '=' + value + '; expires=' + expires.toGMTString() + '; path=' + path;
	},

	/**
	 * XXX: Get cookie
	 *
	 * @param string name Cookie name
	 * @return string Cookie value
	 */
	get: function(name)
	{
		var cookie = ' ' + document.cookie;
		var search = ' ' + name + '=';
		var setStr = null;
		var offset = 0;
		var end = 0;
		if (cookie.length > 0) {
			offset = cookie.indexOf(search);
			if (offset != -1) {
				offset += search.length;
				end = cookie.indexOf(';', offset);
				if (end == -1) {
					end = cookie.length;
				}
				setStr = unescape(cookie.substring(offset, end));
			}
		}

		return setStr;
	}
};




/**
 * XXX: User-agent пользователя
 */
indi.ua = new function()
{
	/**
	 * User agent - iPad
	 *
	 * @var boolean
	 */
	this.isIPad = /iPad/.test(navigator.userAgent);

	/**
	 * User agent - iPhone
	 *
	 * @var boolean
	 */
	this.isIPhone = /iPhone/.test(navigator.userAgent);

	/**
	 * User agent - iPod
	 *
	 * @var boolean
	 */
	this.isIPod = /iPod/.test(navigator.userAgent);

	/**
	 * User agent - iOS
	 *
	 * @var boolean
	 */
	this.isIOS = this.isIPad || this.isIPhone || this.isIPod;

	/**
	 * User agent - Android
	 *
	 * @var boolean
	 */
	this.isAndroid = /Android/.test(navigator.userAgent);

	/**
	 * User agent работает на WebKit
	 *
	 * @var boolean
	 */
	this.isWebKit = /WebKit/.test(navigator.userAgent);

	/**
	 * User agent поддерживает тоuch интерфейс
	 *
	 * @var boolean
	 */
	this.isTouchable = function()
	{
		var result = (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
		this.isTouchable = function() {
			return result;
		};
		return result;
	};
};




/**
 * XXX: Утилиты
 */
indi.utils = {
	/**
	 * XXX: Наследует один класс от другого
	 *
	 * @param object child Потомок
	 * @param object parent Родитель
	 * @param object overrides Перезаписываемые св-ва и методы
	 * @return object Потомок
	 */
	extend: function(child, parent, overrides)
	{
		if (typeof parent == 'object') {
			overrides = parent;
			parent = child;
			child = function() {
				child.superclass.constructor.apply(this, arguments)
			};
		}

		var f = function(){};
		f.prototype = parent.prototype;
		child.prototype = new f();
		child.prototype.constructor = child;
		child.superclass = parent.prototype;

		this.override(child, overrides);

		return child;
	},

	/**
	 * XXX: Перезаписывает св-ва и методы класса
	 *
	 * @param object clss Перезаписываемый класс
	 * @param object parent Родитель
	 * @param object overrides Перезаписываемые св-ва и методы
	 * @return void
	 */
	override: function(clss, overrides)
	{
		this.apply(clss.prototype, overrides);
	},

	/**
	 * XXX: Расширяет сво-ва одного объекта другим
	 *
	 * @param object object Расширяемый объект
	 * @param object overrides Перезаписываемые св-ва
	 * @return void
	 */
	apply: function(object, overrides)
	{
		overrides = overrides || {};
		var primitiveObject = {};
		for (var i in overrides) {
			if (typeof primitiveObject[i] == 'undefined' || primitiveObject[i] != overrides[i]) {
				object[i] = overrides[i];
			}
		}
	},

	/**
	 * XXX: Возвращает уникальный ID
	 *
	 * @return integer|string
	 */
	getId: function(prefix)
	{
		return prefix + (new Date().getTime()) + Math.floor(999 * Math.random());
	},

	/**
	 * XXX: Заменяет в шаблоне конструкции {name} на значения из объекта с данными
	 * Пример:
	 * indi.utils.parse('<a href="{url}">{title}</a>', {url: 'http://my.server/', title: 'My link'});
	 *
	 * @param string template Шаблон
	 * @param object data Данные
	 * @param string def Значение по умолчанию
	 * @return string
	 */
	parse: function(template, data, def)
	{
		def = def || '';
		return template.replace(/{([^}]*)}/gm, function() {
			return arguments.length > 1 ? data[arguments[1]] : '';
		});
	},

	/**
	 * XXX: Возвращает текст в атрибуте href гиперссылки, чтоящий после #
	 * Учитывает баг IE, который выдает полный URL для атрибута href
	 *
	 * @param object|string link Элемент-ссылка
	 * @return string
	 */
	getHashWord: function(link)
	{
		return $(link).attr('href').replace(/^.*#/, '#');
	},

	/**
	 * XXX: Предзагрузчик изображений с поддержкой jQuery deferred
	 * Пример:
	 * $.when(
	 *     indi.utils.preloadImage(src1),
	 *     indi.utils.preloadImage([src2, src3])
	 * ).done(function()
	 * {
	 *     //Images are preloaded
	 * });
	 *
	 * @param string|array|jQuery sources Адрес(а) изображений
	 * @return object
	 */
	preloadImage: function(sources)
	{
		if (sources instanceof jQuery) {
			var urls = [];
			sources.each(function() {
				if ($(this).is('img')) {
					urls.push($(this).attr('src'));
				} else if ($(this).is('a')) {
					urls.push($(this).attr('href'));
				}
			});
			return this.preloadImage(urls);
		}

		if (!(sources instanceof Array)) {
			sources = [sources];
		}

		var defer = $.Deferred();
		var images = {};
		var totalCount = 0;
		for (var i = 0; i < sources.length; i++) {
			var url = sources[i];
			var id = 'id' + url.replace(/[^A-z0-9]/g, '');
			if (images[id] === undefined) {
				images[id] = {
					image: new Image(),
					url: url
				};
				totalCount++;
			}
		}
		var loadedCount = 0;
		for (var id in images) {
			images[id].image.onload = function() {
				if (++loadedCount >= totalCount) {
					defer.resolve();
				}
			};
			images[id].image.src = images[id].url;
		}

		return defer.promise();
	},

	/**
	 * XXX: Склоняет существительное с числительным
	 *
	 * @param integer number Число
	 * @param array cases Варианты существительного в разных падежах и числах (nominativ, genetiv, plural). Пример: ['комментарий', 'комментария', 'комментариев']
	 * @param boolean incNum Добавить само число в результат (по умолчанию true)
	 * @return string
	 */
	getNumEnding: function(number, cases, incNum)
	{
		var numberMod = number % 100;
		incNum = incNum === undefined ? true : incNum;
		var result = '';

		if (numberMod >= 11 && numberMod <= 19) {
			result = cases[2];
		} else {
			numberMod = numberMod % 10;
			switch (numberMod) {
				case 1:
					result = cases[0];
					break;
				case 2:
				case 3:
				case 4:
					result = cases[1];
					break;
				default:
					result = cases[2];
			}
		}

		return incNum ? number + ' ' + result : result;
	},

	/**
	 * XXX: Форматирует число
	 *
	 * @param mixed number Число
	 * @param integer decimals Кол-во знаков после запятой (по умолчанию 0)
	 * @param string decPoint Разделитель целой и дробной части (по умолчанию '.')
	 * @param string thousandsSep Разделитель сотен (по умолчанию '`')
	 * @return string
	 */
	numberFormat: function(number, decimals, decPoint, thousandsSep)
	{
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousandsSep === 'undefined') ? '`' : thousandsSep,
			dec = (typeof decPoint === 'undefined') ? '.' : decPoint,
			s = '',
			toFixedFix = function(n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};

		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}

		return s.join(dec);
	}
};

/**
 * XXX: Утилиты для работы с URL
 */
indi.utils.url =
{
	/**
	 * XXX: Возвращает полный URL текущей страницы, включая домен и протокольный префикс
	 *
	 * @param string path Pathname
	 * @return string
	 */
	getFull: function(path)
	{
		return path.search('//') == -1 ?
		document.location.protocol + '//' + document.location.host + path
			:
			path;
	},

	/**
	 * XXX: Разбирает URL на части
	 *
	 * @param string url URL
	 * @return object {protocol, host, port, pathname, query, hash}
	 */
	parse: function(url)
	{
		var result = {
			protocol: '',
			host: '',
			port: '',
			pathname: '',
			query: '',
			hash: ''
		}

		//Get protocol
		url = url.split('://');
		if (url.length > 1) {
			result.protocol = url.shift();
			url[0] = '//' + url[0];
		}
		url = url.join('://');

		//Get host
		url = url.split('//');
		if (url.length > 1) {
			url.shift();
			url = url.join('//').split('/');
			result.host = url.shift();
			url = '/' + url.join('/');
		} else {
			url = url.join('//');
		}

		//Get port
		result.host = result.host.split(':');
		if (result.host.length > 1) {
			result.port = result.host.pop();
		}
		result.host = result.host.join(':');

		//Get hash
		url = url.split('#');
		if (url.length > 1) {
			result.hash = url.pop();
		}
		url = url.join('#');

		//Get query
		url = url.split('?');
		if (url.length > 1) {
			result.query = url.pop();
		}
		url = url.join('?');

		//Get path
		result.pathname = url;

		return result;
	}
};