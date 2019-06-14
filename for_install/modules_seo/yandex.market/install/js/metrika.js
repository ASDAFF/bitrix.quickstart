(function($, window) {

	function YandexMarketBitrixMetrika(options) {
		this.options = $.extend(true, {}, this.defaults, options);

		this.initialize();
	}

	$.extend(true, YandexMarketBitrixMetrika.prototype, {

		defaults: {
			id: 49982011,
			clickmap: true,
			trackLinks: true,
 			accurateTrackBounce: true,
 			webvisor: false
        },

        initialize: function() {
            this.bind();
        },

        destroy: function() {
            this.unbind();
        },

        bind: function() {
            this.handleWindowLoad(true);
        },

        unbind: function() {
            this.handleWindowLoad(false);
        },

        handleWindowLoad: function(dir) {
            $(window)[dir ? 'on' : 'off']('load', $.proxy(this.onWindowLoad, this));
        },

        onWindowLoad: function() {
            this.handleWindowLoad(false);
            setTimeout($.proxy(this.load, this), 0);
        },

        load: function() {
            var w = window;
            var d = window.document;
            var options = this.options;
            var counterName = this.getCounterName();

            // register callback

            this.pushCallback(function() {
                try { window[counterName] = new Ya.Metrika2(options); } catch(e) {}
            }, true);

            // load script

			var n = d.getElementsByTagName("script")[0],
			s = d.createElement("script");
			s.type = "text/javascript";
			s.async = true;
			s.src = "https://mc.yandex.ru/metrika/tag.js";
			n.parentNode.insertBefore(s, n);
        },

        callMethod: function(method, arguments) {
            var counterName = this.getCounterName();

            if (counterName in window) {
                if (arguments !== null) {
                    window[counterName][method].apply(window[counterName], arguments);
                } else {
                    window[counterName][method]();
                }
            } else {
				this.pushCallback($.proxy(this.callMethod, this, method, arguments));
            }
        },

        pushCallback: function(callback, prepend) {
            var callbackName = this.getCallbackName();
            var callbackList = (window[callbackName] = window[callbackName] || []);

            if (prepend) {
                callbackList.unshift(callback);
            } else {
                callbackList.push(callback);
            }
        },

        getCounterName: function() {
            return 'yaCounter' + this.options.id;
        },

        getCallbackName: function() {
            return 'yandex_metrika_callbacks2';
        }

	});

	window.YandexMarketBitrixMetrika = YandexMarketBitrixMetrika;

})(jQuery, window);