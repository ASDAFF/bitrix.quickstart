(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Ui = BX.namespace('YandexMarket.Ui');

	var constructor = Ui.Collapse = Plugin.Base.extend({

		defaults: {
			target: null,
			duration: 200,

			alt: null,

			lang: {},
		},

		initialize: function() {
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleClick(true);
		},

		unbind: function() {
			this.handleClick(false);
		},

		handleClick: function(dir) {
			this.$el[dir ? 'on' : 'off']('click', $.proxy(this.onClick, this));
		},

		onClick: function(evt) {
			this.toggle();
			evt.preventDefault();
		},

		isActive: function() {
			return this.$el.hasClass('is--active');
		},

		activate: function() {
			this.toggle(true);
		},

		deactivate: function() {
			this.toggle(false);
		},

		toggle: function(dir) {
			var dirNormalized = (dir != null ? !!dir : !this.isActive());

			this.toggleSelf(dirNormalized);
			this.toggleTarget(dirNormalized);
		},

		toggleSelf: function(dir) {
			var alt = this.options.alt;

			this.$el.toggleClass('is--active', dir);

			if (alt != null) {
				this.options.alt = this.$el.text();
				this.$el.text(alt);
			}
		},

		toggleTarget: function(dir) {
			var target = this.getElement('target');

			target[dir ? 'slideDown' : 'slideUp'](this.options.duration, function() {
				target.toggleClass('is--active', dir);
			});
		},

	}, {
		dataName: 'uiCollapse'
	});

})(BX, jQuery, window);