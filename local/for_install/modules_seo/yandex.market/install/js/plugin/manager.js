(function(BX, $, window) {

	var YandexMarket = BX.namespace('YandexMarket');
	var Plugin = BX.namespace('YandexMarket.Plugin');

	Plugin.Manager = Plugin.Base.extend({

		defaults: {
			pluginElement: '.js-plugin',
			clickPluginElement: '.js-plugin-click'
		},

		initialize: function() {
			this.bind();
		},

		bind: function() {
			this.handleDocumentReady(true);
			this.handleAjaxSuccessFinish(true);
			this.handlePluginClick(true);
		},

		unbind: function() {
			this.handleDocumentReady(false);
			this.handleAjaxSuccessFinish(false);
			this.handlePluginClick(false);
		},

		handleDocumentReady: function(dir) {
			$(document)[dir ? 'on' : 'off']('ready', $.proxy(this.onDocumentReady, this));
		},

		handleAjaxSuccessFinish: function(dir) {
			BX[dir ? 'addCustomEvent' : 'removeCustomEvent']('onAjaxSuccessFinish', BX.proxy(this.onAjaxSuccessFinish, this));
		},

		handlePluginClick: function() {
			var selector = this.getElementSelector('clickPlugin');

			$(document).on('click', selector, $.proxy(this.onClickPlugin, this));
		},

		onDocumentReady: function() {
			this.initializeContext(document);
			this.handleDocumentReady(false);
			this.handleAjaxSuccessFinish(false);
		},

		onAjaxSuccessFinish: function() {
			this.initializeContext(document);
			this.handleDocumentReady(false);
			this.handleAjaxSuccessFinish(false);
		},

		onClickPlugin: function(evt) {
			var target = $(evt.currentTarget);
			var pluginList = this.initializeElement(target);
			var instance = pluginList.length > 0 ? pluginList[0] : null;

			if (instance) {
				instance.activate();
			}
		},

		initializeContext: function(context) {
			this.callElementList('initializeElement', context);
		},

		destroyContext: function(context) {
			this.callElementList('destroyElement', context);
		},

		callElementList: function(method, context) {
			var elementList = this.getContextPluginElementList(context);
			var element;
			var i;

			for (i = 0; i < elementList.length; i++) {
				element = elementList.eq(i);
				this[method](element);
			}
		},

		initializeElement: function(element) {
			var pluginList = element.data('plugin').split(',');
			var pluginIndex;
			var pluginName;
			var plugin;
			var result = [];

			for (pluginIndex = 0; pluginIndex < pluginList.length; pluginIndex++) {
				pluginName = pluginList[pluginIndex];
				plugin = this.getPlugin(pluginName);

				result.push(plugin.getInstance(element));
			}

			return result;
		},

		destroyElement: function(element) {
			var pluginList = element.data('plugin').split(',');
			var pluginIndex;
			var pluginName;
			var plugin;
			var instance;

			for (pluginIndex = 0; pluginIndex < pluginList.length; pluginIndex++) {
				pluginName = pluginList[pluginIndex];
				plugin = this.getPlugin(pluginName);
				instance = plugin.getInstance(element, true);

				if (instance) {
					instance.destroy();
				}
			}
		},

		getPlugin: function(name) {
			var nameParts = name.split('.');
			var nameNamespace;
			var pluginNamespace;
			var pluginName;

			if (nameParts.length > 1) {
				nameNamespace = nameParts.slice(0, -1).join('.');
				pluginNamespace = BX.namespace('YandexMarket.' + nameNamespace);
				pluginName = nameParts[nameParts.length - 1];
			} else {
				pluginNamespace = YandexMarket;
				pluginName = nameParts[0];
			}

			return pluginNamespace[pluginName];
		},

		getContextPluginElementList: function(contextNode) {
			var context = contextNode instanceof $ ? contextNode : $(contextNode);
			var pluginSelector = this.getElementSelector('plugin');

			return context.filter(pluginSelector).add(context.find(pluginSelector));
		},

	});

	Plugin.manager = new Plugin.Manager();

})(BX, jQuery, window);