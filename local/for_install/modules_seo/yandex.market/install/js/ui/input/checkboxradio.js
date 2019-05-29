(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Input = BX.namespace('YandexMarket.Ui.Input');

	var constructor = Input.CheckboxRadio = Plugin.Base.extend({

		defaults: {
			targetElement: '.js-checkbox-radio__input'
		},

		initialize: function() {
			this.callParent('initialize', constructor);
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleTargetChange(true);
		},

		unbind: function() {
			this.handleTargetChange(false);
		},

		handleTargetChange: function(dir) {
			var targetSelector = this.getElementSelector('target');

			this.$el[dir ? 'on' : 'off']('change', targetSelector, $.proxy(this.onTargetChange, this));
		},

		onTargetChange: function(evt) {
			var control = evt.target;

			if (control.checked) {
				this.unsetTarget(control);
			}
		},

		unsetTarget: function(exclude) {
			var targetList = this.getElement('target');

			if (exclude) {
				targetList = targetList.not(exclude);
			}

			targetList.prop('checked', false);
		}

	}, {
		dataName: 'UiCheckboxRadio'
	});

})(BX, jQuery, window);