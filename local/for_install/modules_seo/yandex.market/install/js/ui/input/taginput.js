(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Input = BX.namespace('YandexMarket.Ui.Input');

	var constructor = Input.TagInput = Plugin.Base.extend({

		defaults: {
			width: 200
		},

		initVars: function() {
			this.callParent('initVars', constructor);
			this._isPluginReady = false;
		},

		initialize: function() {
			this.clearClone();
			this.callParent('initialize', constructor);
			this.createPlugin();
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.destroyPlugin();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleRefresh(true);
		},

		unbind: function() {
			this.handleRefresh(false);
		},

		handleRefresh: function(dir) {
			this.$el.on('uiRefresh', $.proxy(this.onRefresh, this));
		},

		onRefresh: function() {
			this.refresh();
		},

		clearClone: function() {
			this.$el.removeClass('chzn-done').removeAttr('id').css('display', '').next().remove();
		},

		createPlugin: function() {
			if (this._isPluginReady) { return; }

			this._isPluginReady = true;

			this.$el.chosen({
				width: this.options.width,
				no_results_text: BX.message('CHOSEN_NO_RESULTS'),
				placeholder_text_multiple: BX.message('CHOSEN_PLACEHOLDER'),
				placeholder_text_single: BX.message('CHOSEN_PLACEHOLDER'),
				search_contains: true
			});
		},

		refresh: function() {
			this.$el.trigger("chosen:updated");
		},

		destroyPlugin: function() {
			this._isPluginReady = false;
			this.$el.chosen('destroy');
		}

	}, {
		dataName: 'uiTagInput'
	});

})(BX, jQuery, window);