(function(BX, $, window) {

	'use strict';

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Utils = BX.namespace('YandexMarket.Utils');

	function Base(element, options) {
		this.setOrigin(element);

		this.options = $.extend(true, {}, this.defaults, this.$el.data());

		this.setOptions(options);
		this.initVars();
		this.initialize();
	}

	Plugin.Base = Base;

	$.extend(true, Base, {

		dataName: null,

		extend: function(protoProps, staticProps) {
			var parent = this;
            var child = function() {
                return Base.apply(this, arguments);
            };

            return Utils.inherit(parent, child, protoProps, staticProps);
		},

		getInstance: function(element, isDisableCreate) {
			var dataName = this.dataName;
			var elementNode;
			var result;

			if (dataName) {
				elementNode = element instanceof $ ? element[0] : element;
				result = $.data(elementNode, dataName);

				if (!result && !isDisableCreate) {
					result = new this(element);
				}
			}

			return result;
		}

	});

	$.extend(true, Base.prototype, {

		defaults: {},

		initVars: function() {},

		initialize: function() {},

		destroy: function() {
			this.releaseOrigin();
		},

		getParent: function(key, constructor) {
			var parent;
			var result;

			if (!constructor) { constructor = this.constructor; }

			parent = constructor.prototype.__super__;

			if (!parent) {
				// nothing
			} else if (key in parent) {
				result = parent[key];
			} else if (parent.constructor) {
				result = this.getParent(key, parent.constructor);
			}

			return result;
		},

		callParent: function(method, args, constructor) {
			var fn;

			if (typeof args === 'function') {
				constructor = args;
				args = null;
			}

			fn = this.getParent(method, constructor);

			return fn && fn.apply(this, args);
		},

		getStatic: function(key) {
			return this.constructor[key];
		},

		releaseOrigin: function() {
			var dataName = this.getStatic('dataName');

			if (this.el && dataName) {
				$.removeData(this.el, dataName);
			}

			this.$el = null;
			this.el = null;
		},

		setOrigin: function(element) {
			var dataName = this.getStatic('dataName');

			this.$el = $(element);
			this.el = this.$el.get(0);

			if (dataName && this.el) {
				$.data(this.el, dataName, this);
			}
		},

		setOptions: function(options) {
			this.options = $.extend(true, this.options, options);
		},

		getElement: function(key, context, method) {
			var selector = this.getElementSelector(key);

			return this.getNode(selector, context, method);
		},

		getElementSelector: function(key) {
			var optionKey = key + 'Element';

			return this.options[optionKey];
		},

		getTemplate: function(key) {
			var optionKey = key + 'Template';
			var option = this.options[optionKey];
			var optionFirstSymbol = option.substr(0, 1);
			var result;

			if (optionFirstSymbol === '.' || optionFirstSymbol === '#') {
				result = this.getNode(option).html();
			} else {
				result = option;
			}

			return result;
		},

		getNode: function (selector, context, method) {
			var result;

			if (selector.substr(0, 1) === '#') { // is id query
				context = $(document);
				method = 'find';
			} else {
				if (!context) { context = this.$el }
			}

			if (method) {
				result = context[method](selector);
			} else {
				result = context.find(selector);
			}

			return result;
		},

		getLang: function(key, replaces) {
			var langKey;
			var result;

			if (key in this.options.lang) {
				result = this.options.lang[key];
			} else {
				langKey = this.options.langPrefix + key;
				result = BX.message(langKey) || '';
			}

			if (result && replaces) {
				result = Utils.compileTemplate(result, replaces);
			}

			return result;
		}

	});

})(BX, jQuery, window);
