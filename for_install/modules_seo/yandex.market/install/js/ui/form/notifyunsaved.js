(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Form = BX.namespace('YandexMarket.Ui.Form');

	var constructor = Form.NotifyUnsaved = Plugin.Base.extend({

		defaults: {
			changed: false,

			langPrefix: 'YANDEX_MARKET_FORM_NOTIFY_UNSAVED_',
			lang: {}
		},

		initVars: function() {
			this._isChanged = !!this.options.changed;
			this._isSubmitted = false;
		},

		initialize: function() {
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleChange(true);
			this.handleSubmit(true);
			this.handleWindowUnload(true);
		},

		unbind: function() {
			this.handleChange(false);
			this.handleSubmit(false);
			this.handleWindowUnload(false);
		},

		handleChange: function(dir) {
			if (!this._isChanged || !dir) {
				this.$el[dir ? 'on' : 'off']('change', $.proxy(this.onChange, this));
			}
		},

		handleSubmit: function(dir) {
			this.$el[dir ? 'on' : 'off']('submit', $.proxy(this.onSubmit, this));
		},

		handleWindowUnload: function(dir) {
			$(window)[dir ? 'on' : 'off']('beforeunload', $.proxy(this.onWindowUnload, this));
		},

		onChange: function() {
			this._isChanged = true;

			this.handleChange(false);
		},

		onSubmit: function(evt) {
			if (!evt.isDefaultPrevented()) {
				this._isSubmitted = true;
			}
		},

		onWindowUnload: function(evt) {
			if (this._isChanged && !this._isSubmitted) {
				return this.getLang('MESSAGE');
			}
		}

	}, {
		dataName: 'uiFormNotifyUnsaved'
	});

})(BX, jQuery, window);
