/** @namespace */
var Crosspixel = {};/** @include "../index.js" */
Crosspixel.Utils = {};/**
 * @include "namespace.js"
 * @include "CookieStore.js"
 * @include "StateChanger.js"
 * @include "EventProvider.js"
 * @include "EventSender.js"
 */
Crosspixel.Utils = {

	documentBodyElement: null,

	/**
	 * @private
	 * @return {Element} body
	 */
	getDocumentBodyElement: function () {
		if ( this.documentBodyElement == null )
			this.documentBodyElement = document.getElementsByTagName("body")[0];

		return this.documentBodyElement;
	},

	/**
	 * Сливает два хэша
	 * @private
	 * @param {Object} defaults значения по-умолчанию
	 * @param {Object} params переопределенные значения
	 * @return {Object} объект из ключей и значений по-умолчанию и новых значений
	 */
	createParams: function (defaults, params) {
		result = defaults;
		Crosspixel.Utils.mergeParams(defaults || {}, params);

		return result;
	},

	/**
	 * Сливает два объекта
	 * @private
	 * @param {Object} result
	 * @param {Object} params
	 */
	mergeParams: function(result, params) {
		for ( var key in params ) {
			if ( params.hasOwnProperty(key) ) {
				if ( result[key] && Crosspixel.Utils.isObject(result[key]) ) {
					Crosspixel.Utils.mergeParams(result[key], params[key]);
				}
				else {
					result[key] = params[key];
				}
			}
		}
	},

	isObject: function(o) {
  		return Object.prototype.toString.call(o) === '[object Object]';
	},

	defaultStyleValueParams: {
		display: 'block',
		width: '100%',
		height: '100%',
		opacity: 1.0,
		background: 'transparent',
		'float': 'none',
		visibility: 'visible',
		border: '0'
	},

	/**
	 * Возвращает CSS-строку для свойства style
	 * @private
	 * @param {Object} params параметры для строки
	 * @return {String} CSS-строка для свойства style
	 */
	createStyleValue: function (params, defaultParams) {
		var fromParams = defaultParams || Crosspixel.Utils.defaultStyleValueParams;
		var styleParams = Crosspixel.Utils.createParams(fromParams, params);

		var result = '';
		for (var key in styleParams) {
			if ( styleParams[key] || styleParams[key] === 0 )
				result += key + ':' + styleParams[key] + ';';

			if ( styleParams[key] == 'opacity')
				result += '-khtml-opacity:' + styleParams[key] + ';-moz-opacity:' + styleParams[key] + ';filter:progid:DXImageTransform.Microsoft.Alpha(opacity=' + (styleParams[key] * 100) + ');';
		}

		return result;
	}

};/** @include "index.js" */

/**
 * Обертка события от браузера.
 * @constructor
 * @param {String} eventName название события, например "keydown"
 * @param {Function} prepareParams преобразователь event браузера в хеш для обработчика
 * @return {Crosspixel.EventProvider}
 */
Crosspixel.Utils.EventProvider = function(eventName, prepareParams, target) {
	this.eventName = eventName;
	this.prepareParams = prepareParams;
	this.target = target || 'document';

	this.handlers = null;

	return this;
};

/**
 * Формирует хеш параметоров с помощью this.prepareParams и вызывает все обработчики
 * @private
 * @param {Object} event
 */
Crosspixel.Utils.EventProvider.prototype.genericHandler = function(event) {
	var params = (this.prepareParams ? this.prepareParams(event) : event);

	for(var i = 0, length = this.handlers.length; i < length; i++)
		this.handlers[i](params);
};

/**
 * Создает массив обработчиков, вешает обработчик события браузера
 * @private
 */
Crosspixel.Utils.EventProvider.prototype.initHandlers = function () {
	this.handlers = [];

	var code = this.target + '.on' + this.eventName.toLowerCase() +  '=function(event){self.genericHandler(event);};';

	var self = this;
	eval(code);
};

/**
 * Добавляет обработчик события в конец очереди обработчиков
 * @param {Function} handler обработчик события
 */
Crosspixel.Utils.EventProvider.prototype.addHandler = function (handler) {
	if ( this.handlers == null )
		this.initHandlers();

	this.handlers[this.handlers.length] = handler;
};/** @include "index.js" */

/**
 * Объект, который умеет посылать события.
 * @constructor
 * @return {Crosspixel.EventSender}
 */
Crosspixel.Utils.EventSender = function() {
	this.handlers = {};

	return this;
};

/**
 * Добавляет обработчик события в конец очереди обработчиков
 * @param {String} eventName название события
 * @param {Function} handler обработчик события
 */
Crosspixel.Utils.EventSender.prototype.addHandler = function (eventName, handler) {
	if ( !this.handlers[eventName] ) {
		this.handlers[eventName] = [];
	}

	this.handlers[eventName][this.handlers[eventName].length] = handler;
};

/**
 * Вызывает обработчики события с указанными параметрами
 * @param {String} eventName название события
 * @param {Object} params параметры обработчиков событий
 */
Crosspixel.Utils.EventSender.prototype.occurEvent = function (eventName, params) {
	var target = this.handlers[eventName];

	if ( this.handlers[eventName] ) {
		for(var i = 0, length = this.handlers[eventName].length; i < length; i++ ) {
			this.handlers[eventName][i](params);
		}
	}
};/** @include "index.js" */

Crosspixel.Utils.CookieStore = {

	setValue: function(name, value) {
		Crosspixel.Utils.CookieStore.setCookie(name, value)
	},

	getValue: function(name) {
		return Crosspixel.Utils.CookieStore.getCookie(name);
	},

	/**
	 * Backend to save value
	 * @private
	 * @param {String} name имя сохраняемой переменной
	 * @param {Object} value занчение сохраняемой переменной
	 */
	setCookie: function(name, value) {
		var today = new Date(), expires = new Date();
		expires.setTime(today.getTime() + 31536000000); //3600000 * 24 * 365

		document.cookie = name + "=" + escape(value) + "; expires=" + expires;
	},

	/**
	 * Backend to restore value
	 * @private
	 * @param {String} name имя сохранённой переменной
	 * @return {Object} значение сохранённой переменной
	 */
	getCookie: function(name) {
		var cookie = " " + document.cookie;
		var search = " " + name + "=";
		var setStr = null;
		var offset = 0;
		var end = 0;

		if (cookie.length > 0) {
			offset = cookie.indexOf(search);
			if (offset != -1) {
				offset += search.length;
				end = cookie.indexOf(";", offset)
				if (end == -1) {
					end = cookie.length;
				}
				setStr = unescape(cookie.substring(offset, end));
			}
		}

		return(setStr);
	}

};/** @include "index.js" */

/**
 * Меняет состояние объекта по внешнему событию.
 * @constructor
 * @param {Crosspixel.EventProvider} eventProvider прослойка, чье событие слушать
 * @param {Function} shouldChange если вернет true при возникновении события от eventProvider, то вызовится stateChange
 * @param {Function} stateChange вызывается, когда нужно поменять состояние
 * @return {Crosspixel.StateChanger}
 */
Crosspixel.Utils.StateChanger = function (eventProvider, shouldChange, stateChange) {
	eventProvider.addHandler(
		function (params) {
			if ( shouldChange(params) )
				stateChange();
		}
	);

	return this;
};
/** @include "../index.js" */

Crosspixel.OpacityChanger = {};Crosspixel.OpacityChanger = {

	params: null,

	/** @type {Crosspixel.Utils.EventSender} */
	eventSender: null,

	/**
	 * Устанавливает настройки для гайдов
	 *
	 * @param {Object}
	 *            params параметры гайдов
	 */
	init: function(params) {
		this.params = Crosspixel.Utils.createParams(this.defaults, params);
		this.eventSender = new Crosspixel.Utils.EventSender();
	},

	setOpacity: function(value) {
		this.params.opacity = value;
		this.params.opacity = (this.params.opacity < 0 ? 0.0 : this.params.opacity);
		this.params.opacity = (this.params.opacity > 1 ? 1.0 : this.params.opacity);

		this.updateOpacity(this.params.opacity);

		return this.params.opacity;
	},

	stepDownOpacity: function() {
		return this.setOpacity(this.params.opacity - this.params.opacityStep);
	},

	stepUpOpacity: function() {
		return this.setOpacity(this.params.opacity + this.params.opacityStep);
	},

	updateOpacity: function(opacity) {
		this.eventSender.occurEvent('opacityChanged', this.params.opacity);
	},

	changeElementOpacity: function (element) {
		if (element)
			element.style.opacity = this.params.opacity;
	}
};/** @include "index.js" */

Crosspixel.OpacityChanger.defaults = {
	/**
	 * Функция вызывается каждый раз при нажатии клавиш в браузере.
	 * @param {Object} params информация о нажатой комбинации клавиш (params.ctrlKey, params.altKey, params.keyCode)
	 * @return {Boolean} true, если нужно сделать изображение менее прозрачным на opacityStep процентов
	 */
	shouldStepUpOpacity:
		function (params) {
			// Ctrl o
			var result = !params.occured_in_form && (params.ctrlKey && (params.character == 'o' || params.character == 'O' || params.character == 'щ' || params.character == 'Щ'));
			return result;
		},
	/**
	 * Функция вызывается каждый раз при нажатии клавиш в браузере.
	 * @param {Object} params информация о нажатой комбинации клавиш (params.ctrlKey, params.altKey, params.keyCode)
	 * @return {Boolean} true, если нужно сделать изображение более прозрачным на opacityStep процентов
	 */
	shouldStepDownOpacity:
		function (params) {
			// Ctrl u
			var result = !params.occured_in_form && (params.ctrlKey && (params.character == 'u' || params.character == 'U' || params.character == 'г' || params.character == 'Г'));
			return result;
		},

	/**
	 * Начальное значение прозрачности изображения от 0 до 1 (0 - абсолютно прозрачное, 1 - абсолютно непрозрачное)
	 * @type Number
	 */
	opacity: 0.25,
	/**
	 * Шаг изменения значения прозрачности для изображения от 0 до 1
	 * @type Number
	 */
	opacityStep: 0.05
};/** @include "../index.js" */

Crosspixel.Image = {};/** @include "namespace.js" */
Crosspixel.Image = {

	showing: false,
	parentElement: null,

	params: null,

	imgElement: null,
	/** @type {Crosspixel.Utils.EventSender} */
	eventSender: null,

	/**
	 * Устанавливает настройки для гайдов
	 *
	 * @param {Object}
	 *            params параметры гайдов
	 */
	init: function(params) {
		this.params = Crosspixel.Utils.createParams(this.defaults, params);
		this.eventSender = new Crosspixel.Utils.EventSender();
	},

	/**
	 * Создает корневой HTML-элемент и HTML для гайдов и добавляет его в DOM
	 *
	 * @private
	 * @param {Object}
	 *            params параметры создания элемента и гайдов
	 * @return {Element} корневой HTML-элемент
	 */
	createParentElement: function(params) {
		// создаем элемент и ресетим style
		var parentElement = document.createElement("div");

		var parentElementStyle = {
			position : 'absolute',
			left : '0',
			top : '0',

			width : '100%',
			height : params.height + 'px',

			opacity: 1,
			'z-index' : params['z-index']
		};

		parentElement.setAttribute("style", Crosspixel.Utils.createStyleValue(parentElementStyle));

		// создаём HTML гайдов
		parentElement.appendChild(this.createImageDOM(params));

		// добавляем элемент в DOM
		Crosspixel.Utils.getDocumentBodyElement().appendChild(parentElement);

		return parentElement;
	},

	/**
	 * Создает HTML-строку для отображения гайдов
	 *
	 * @private
	 * @param {Array}
	 *            items массив настроек для создания гайдов
	 * @return {String} HTML-строка для отображения гайдов
	 */
	createImageDOM: function(params) {
		var imageStyle = {
			position: 'static',

			width : 'auto',
			height : 'auto',

			opacity : Crosspixel.OpacityChanger.params.opacity
		};
		var imageContainerStyle = {
			position: 'static',

			'padding-top' : params['margin-top'],

			width : 'auto',
			height : 'auto'
		};

		if (params.centered) {
			imageContainerStyle['text-align'] = 'center';
			imageStyle.margin = '0 auto';
		} else {
			imageContainerStyle['padding-left'] = params['margin-left'], imageContainerStyle['padding-right'] = params['margin-right'];
		};

		var imageDOMParent = document.createElement('div');
		imageDOMParent.setAttribute("style", Crosspixel.Utils.createStyleValue(imageContainerStyle));

		this.imgElement = document.createElement('img');
		this.imgElement.setAttribute('src', params.src);
		this.imgElement.setAttribute('width', params.width);
		this.imgElement.setAttribute('height', params.height);
		this.imgElement.setAttribute('style', Crosspixel.Utils.createStyleValue(imageStyle));

		imageDOMParent.appendChild(this.imgElement);

		return imageDOMParent;
	},

	opacityHandler: function () {
		Crosspixel.OpacityChanger.changeElementOpacity(Crosspixel.Image.imgElement);
	},

	/**
	 * Скрывает-показывает гайды
	 */
	toggleVisibility: function() {
		this.showing = !this.showing;
		this.eventSender.occurEvent('visibilityChanged', this.showing);

		if (this.showing && this.parentElement == null) {
			this.parentElement = this.createParentElement(this.params);
		}

		if (this.parentElement)
			this.parentElement.style.display = (this.showing ? 'block' : 'none');
	}
};/** @include "index.js" */

Crosspixel.Image.defaults = {
	/**
	 * Функция вызывается каждый раз при нажатии клавиш в браузере.
	 * @param {Object} params информация о нажатой комбинации клавиш (params.ctrlKey, params.altKey, params.keyCode)
	 * @return {Boolean} true, если нужно показать/скрыть изображение
	 */
	shouldToggleVisibility:
		function (params) {
			// Ctrl i
			var result = !params.occured_in_form && (params.ctrlKey && (params.character == 'i' || params.character == 'I' || params.character == 'ш' || params.character == 'Ш'));
			return result;
		},

	/**
	 * Значения CSS-свойства z-index HTML-контейнера изображения
	 * @type Number
	 */
	'z-index': 255,

	/**
	 * Центрировать ли изображение относительно рабочей области браузера
	 * @type Boolean
	 */
	centered: false,

	/**
	 * Отступ от верхнего края рабочей области браузера до изображения в пикселах
	 * @type Number
	 */
	'margin-top': 0,
	/**
	 * Отступ от левого края рабочей области браузера до изображения.
	 * Возможные значения аналогичны значениям CSS-свойства margin-left
	 * @type Number
	 */
	'margin-left': '0px',
	/**
	 * Отступ от правого края рабочей области браузера до изображения.
	 * Возможные значения аналогичны значениям CSS-свойства margin-left
	 * @type Number
	 */
	'margin-right': '0px',

	/**
	 * URL файла изображения
	 * @type String
	 */
	src: '',

	/**
	 * Ширина изображения в пикселах
	 * @type Number
	 */
	width: 100,
	/**
	 * Высота изображения в пикселах
	 * @type Number
	 */
	height: 100
};/** @include "../index.js" */
Crosspixel.Resizer = {};/** @include "namespace.js" */
Crosspixel.Resizer = {

	params: null,

	sizes: null,
	currentSizeIndex: null,

	title: null,

	/** @type {Crosspixel.Utils.EventSender} */
	eventSender: null,

	detectDefaultSize: function () {
		var result = null;

		if ( typeof( window.innerWidth ) == 'number' && typeof( window.innerHeight ) == 'number' ) {
			result =
 				{
					width: window.innerWidth,
					height: window.innerHeight
				};
		}
		else
			if ( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
				result =
					{
						width: document.documentElement.clientWidth,
						height: document.documentElement.clientHeight
					};
			}
			else
				if ( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
					result =
						{
							width: document.body.clientWidth,
							height: document.body.clientHeight
						};
				}

		return result;
	},

	getDefaultSize: function () {
		return this.sizes[0];
	},

	getCurrentSize: function () {
		return this.sizes[this.currentSizeIndex];
	},

	init: function (image_params) {
		this.params = Crosspixel.Utils.createParams(this.defaults, {});
		this.eventSender = new Crosspixel.Utils.EventSender();

		var defaultSize = this.detectDefaultSize();
		if ( defaultSize ) {
			var sizes = [ defaultSize ];
			sizes[sizes.length] = {
				width: image_params.width,
				height: image_params.height
			};

			this.title = document.title;

			this.sizes = sizes;
			this.currentSizeIndex = 0;
		}
	},

	sizeTitle: function(index) {
		var result, current_size = this.sizes[index], default_size = this.sizes[0];
		if ( current_size.title ) {
			result = current_size.title;
		}
		else {
			var width = ( current_size.width ? current_size.width : default_size.width );
			var height = ( current_size.height ? current_size.height : default_size.height );

			result = width + '×' + height;
		}

		return result;
	},

	selectSize: function(index) {
		this.currentSizeIndex = index;

		this.applySize();
	},

	toggleSize: function () {
		if ( this.currentSizeIndex != null ) {
			this.currentSizeIndex++;
			this.currentSizeIndex = ( this.currentSizeIndex == this.sizes.length ? 0 : this.currentSizeIndex );

			this.applySize();
		}
	},

	applySize: function () {
		var width = ( this.getCurrentSize().width ? this.getCurrentSize().width : this.getDefaultSize().width );
		var height = ( this.getCurrentSize().height ? this.getCurrentSize().height : this.getDefaultSize().height );

		window.resizeTo(width, height);

		var titleText = ( this.currentSizeIndex ? this.title + ' (' + width + '×' + height + ')' : this.title );
		if ( this.getCurrentSize().title )
			titleText = this.getCurrentSize().title;

		document.title = titleText;

		this.eventSender.occurEvent('sizeChanged', this.currentSizeIndex);
	}

};/**
 * @include "../index.js"
 * @include "defaults.js"
 */

Crosspixel.GUI = {

	params: null,

	togglerElement: null,

	paneElement: null,
	paneShowing: true,

	checkboxes: {},

	init: function(params) {
		this.params = Crosspixel.Utils.createParams(this.defaults, params);
	},

	create: function() {
		this.createToggler();
		this.createPane();
	},

	createToggler: function() {
		var self = this;

		self.togglerElement = document.createElement("button");
		self.togglerElement.innerHTML = self.params.toggler.label;

		var styleValue = Crosspixel.Utils.createStyleValue(self.params.toggler.style, {});
		self.togglerElement.setAttribute("style", styleValue);

		// добавляем элемент в DOM
		Crosspixel.Utils.getDocumentBodyElement().appendChild(self.togglerElement);


		self.togglerElement.onclick = function () {
			self.paneShowing = !self.paneShowing;

			self.paneElement.style.display = (self.paneShowing ? 'block' : 'none');
		}
	},

	createPaneCheckboxItemHTML: function (id, label, style) {
		var currentStyle = style || '';

		var html = '<div style="width:auto;' + currentStyle + '">';
		html += '<input type="checkbox" id="' + id + '">';
		html += '<label for="' + id + '">&nbsp;' + label + '</label>';
		html += '</div>';

		return html;
	},

	createPane: function() {
		var self = this;
		self.paneElement = document.createElement("div");

		var currentStyle = self.params.pane.style;
		var styleValue = Crosspixel.Utils.createStyleValue(currentStyle, {});
		self.paneElement.setAttribute("style", styleValue);

		var ids = {}, html = '';

		ids.image = self.generateId() + 'image';
		html += self.createPaneCheckboxItemHTML(ids.image, self.params.pane.labels.image, 'margin:0 0 1em');

		html += '<div style="width:auto;margin:1em 0 0">';
		ids.opacity_down = self.generateId() + 'opacitydown';
		ids.opacity_up = self.generateId() + 'opacityup';
		ids.opacity_value = self.generateId() + 'opacityvalue';
		if ( self.params.pane.labels.opacity )
			html += self.params.pane.labels.opacity.label + '<br>';
		html += self.params.pane.labels.opacity.less;
		html += '<button id="' + ids.opacity_down + '">-</button>&nbsp;';
		html += '<span id="' + ids.opacity_value +'">' + Crosspixel.OpacityChanger.params.opacity.toFixed(2) + '</span>';
		html += '&nbsp;<button id="' + ids.opacity_up + '">+</button>';
		html += self.params.pane.labels.opacity.more;
		html += '</div>';

		self.paneElement.innerHTML = html;

		// добавляем элемент в DOM
		Crosspixel.Utils.getDocumentBodyElement().appendChild(this.paneElement);

		self.checkboxes.image = document.getElementById(ids.image);
		if ( self.checkboxes.image ) {
			self.checkboxes.image.onclick = function () {
				Crosspixel.Image.toggleVisibility();
			};
			Crosspixel.Image.eventSender.addHandler(
				'visibilityChanged',
				function(visible) {
					self.checkboxes.image.checked = visible;
				}
			);
		}

		self.checkboxes.opacity_value = document.getElementById(ids.opacity_value);
		if ( self.checkboxes.opacity_value ) {
			Crosspixel.OpacityChanger.eventSender.addHandler(
				'opacityChanged',
				function(opacity) {
					self.checkboxes.opacity_value.innerHTML = opacity.toFixed(2);
				}
			);
		}

		self.checkboxes.opacity_up = document.getElementById(ids.opacity_up);
		if ( self.checkboxes.opacity_up ) {
			self.checkboxes.opacity_up.onclick = function () {
				Crosspixel.OpacityChanger.stepUpOpacity();
			}
		}

		self.checkboxes.opacity_down = document.getElementById(ids.opacity_down);
		if ( self.checkboxes.opacity_down ) {
			self.checkboxes.opacity_down.onclick = function () {
				Crosspixel.OpacityChanger.stepDownOpacity();
			}
		}
	},

	/**
	 * @private
	 * @return {String} уникальный идентификатор
	 */
	generateId: function() {
		var prefix = '_mdg', result = new Date();
		result = prefix + result.getTime();

		return result;
	}

}/** @include "index.js */

Crosspixel.GUI.defaults = {

	toggler: {
		style: {
			position: "absolute",
			right: '10px',
			top: '10px',
			'z-index': 1000
		},

		label: "Настройки"
	},

	pane: {
		style: {
			position: "absolute",
			right: '10px',
			top: '35px',

			width: 'auto',
			height: 'auto',

			margin: '0',
			padding: '7px 5px',

			background: '#FFF',
			border: '2px solid #CCC',

			'z-index': 1000
		},

		labels: {
			image: 'изображение-макет <span style="color:#555;font-size:80%;margin-left:0.75em">Ctrl i</span>',
			opacity: {
				label: '<span style="margin-left:3.7em">прозрачность</span>',
				less: '<span style="color:#555;font-size:80%;margin:0 0.75em 0 1em">Ctrl u</span> ',
				more: ' <span style="color:#555;font-size:80%;margin-left:0.75em">Ctrl o</span>'
			}
		}
	}

};/**
 * @include "namespace.js"
 * @include "Utils/index.js"
 * @include "Resizer/index.js"
 * @include "OpacityChanger/index.js"
 * @include "GUI/index.js"
 */

Crosspixel.keyDownEventProvider = null;
Crosspixel.resizeEventProvider = null;

/**
 * Возвращает обертку для отлова события изменения размера окна браузера
 * @private
 * @return {Crosspixel.Utils.EventProvider} для события изменения размера окна браузера
 */
Crosspixel.getResizeEventProvider = function () {
	if ( this.resizeEventProvider == null ) {
		this.resizeEventProvider =
			new Crosspixel.Utils.EventProvider(
				'resize',
				function (event) {
					return {
						event: event
					};
				},
				'window'
			);
	};

	return this.resizeEventProvider;
};

/**
 * Возвращает обертку для отлова события нажатия клавиш
 * @private
 * @return {Crosspixel.Utils.EventProvider} для события нажатия клавиш
 */
Crosspixel.getKeyDownEventProvider = function () {
	if ( this.keyDownEventProvider == null ) {
		this.keyDownEventProvider =
			new Crosspixel.Utils.EventProvider(
				'keydown',
				function (event) {
					var keyboardEvent = ( event || window.event );
					var keyCode = (keyboardEvent.keyCode ? keyboardEvent.keyCode : (keyboardEvent.which ? keyboardEvent.which : keyboardEvent.keyChar));

					var character = String.fromCharCode(keyCode).toLowerCase();
					var shift_nums = {
						"`":"~",
						"1":"!",
						"2":"@",
						"3":"#",
						"4":"$",
						"5":"%",
						"6":"^",
						"7":"&",
						"8":"*",
						"9":"(",
						"0":")",
						"-":"_",
						"=":"+",
						";":":",
						"'":"\"",
						",":"<",
						".":">",
						"/":"?",
						"\\":"|"
					}
					if ( keyboardEvent.shiftKey && shift_nums[character] )
						character = shift_nums[character];

				var element = ( keyboardEvent.target ? keyboardEvent.target : keyboardEvent.srcElement );
				if ( element && element.nodeType == 3 )
					element = element.parentNode;
				var occured_in_form = ( element && (element.tagName == 'INPUT' || element.tagName == 'TEXTAREA'));

					return {
						occured_in_form: occured_in_form,
						character: character,
						keyCode: keyCode,

						altKey: keyboardEvent.altKey,
						shiftKey: keyboardEvent.shiftKey,
						ctrlKey: keyboardEvent.ctrlKey,

						event: keyboardEvent
					};
				}
			);
	};

	return this.keyDownEventProvider;
};

/**
 * Устанавливает настройки модульной сетки и ставит обработчики событий для показа сетки
 * @param {Object} params параметры инициализации
 */
Crosspixel.init = function (params) {
	var self = this;
	var store = Crosspixel.Utils.CookieStore;

	this.OpacityChanger.init(params.opacity);
	var opacityUpChanger =
		new Crosspixel.Utils.StateChanger(
			this.getKeyDownEventProvider(),
			this.OpacityChanger.params.shouldStepUpOpacity,
			function () {
				if ( !Crosspixel.Image.showing )
					Crosspixel.Image.toggleVisibility();

				self.OpacityChanger.stepUpOpacity();
			}
		);
	var opacityDownChanger =
		new Crosspixel.Utils.StateChanger(
			this.getKeyDownEventProvider(),
			this.OpacityChanger.params.shouldStepDownOpacity,
			function () {
				if ( !Crosspixel.Image.showing )
					Crosspixel.Image.toggleVisibility();

				self.OpacityChanger.stepDownOpacity();
			}
		);
	this.OpacityChanger.eventSender.addHandler(
		'opacityChanged',
		function(opacity) {
			store.setValue('o', opacity);
		}
	);

	// изображение
	this.Image.init(params.image);
	this.OpacityChanger.eventSender.addHandler('opacityChanged', this.Image.opacityHandler);

	var imageStateChanger =
		new Crosspixel.Utils.StateChanger(
			this.getKeyDownEventProvider(),
			this.Image.params.shouldToggleVisibility,
			function () {
				self.Image.toggleVisibility();
			}
		);
	this.Image.eventSender.addHandler(
		'visibilityChanged',
		function (showing) {
			self.Resizer.toggleSize();

			var i_value = (showing ? 'true' : 'false');
			store.setValue('i', i_value);
		}
	);

	self.Resizer.init(this.Image.params);

	self.GUI.init(params.gui);
	self.GUI.create();

	// восстанавливаем состояния из кук
	// по-умолчанию: всё скрыто
	if ( store.getValue('i') == 'true' )
		self.Image.toggleVisibility();

	var image_opacity = parseFloat(store.getValue('o'));
	if ( !isNaN(image_opacity) ) {
		self.OpacityChanger.setOpacity( image_opacity );
	}
};/** @include "index.js" */

Crosspixel.init(
	{

		image: {
			'z-index': 255,

			centered: true,

			'margin-top': '0px',
			'margin-left': '0px',
			'margin-right': '0px',

			src: 'design.png',

			width: 300,
			height: 356
		},

		opacity: {
			opacity: 1,
			opacityStep: 0.05
		},

		gui: {
			toggler: {
				style: {
					position: "absolute",
					right: '10px',
					top: '10px'
				}
			},

			pane: {
				style: {
					position: "absolute",
					right: '10px',
					top: '35px'
				}
			}
		}

	}
);