(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Input = BX.namespace('YandexMarket.Ui.Input');

	var constructor = Input.SymbolCount = Plugin.Base.extend({

		defaults: {
			inputElement: '.js-symbol-count__input',
			valueElement: '.js-symbol-count__value',

			max: null,

			lang: {},
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
			this.handleInputChange(true);
		},

		unbind: function() {
			this.handleInputChange(false);
		},

		handleInputChange: function(dir) {
			var input = this.getElement('input');

			input[dir ? 'on' : 'off']('input change', $.proxy(this.onInputChange, this));
		},

		onInputChange: function(evt) {
			var input = evt.target;

			this.updateValue(input.value);
		},

		updateValue: function(value) {
			var valueString = (value != null ? '' + value : '');
			var valueElement = this.getElement('value');
			var count;

			if (this.options.max != null) {
				count = this.options.max - valueString.length;
			} else {
				count = valueString.length;
			}

			valueElement.text(count);
		}

	}, {
		dataName: 'UiInputSymbolCount'
	});

})(BX, jQuery, window);