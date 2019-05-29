(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Input = BX.namespace('YandexMarket.Ui.Input');

	var constructor = Input.HiddenInput = Plugin.Base.extend({

		defaults: {
			inputElement: '.js-hidden-input__input',
			labelElement: '.js-hidden-input__label',

			lang: {},
			langPrefix: 'YANDEX_MARKET_HIDDEN_INPUT_'
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
			this.handleInputBlur(true);
			this.handleLabelClick(true);
			this.handleLabelKeyUp(true);
		},

		unbind: function() {
			this.handleInputChange(false);
			this.handleInputBlur(false);
			this.handleLabelClick(false);
			this.handleLabelKeyUp(false);
		},

		handleInputChange: function(dir) {
			var input = this.getElement('input');

			input[dir ? 'on' : 'off']('change', $.proxy(this.onInputChange, this));
		},

		handleInputBlur: function(dir) {
			var input = this.getElement('input');

			input[dir ? 'on' : 'off']('blur', $.proxy(this.onInputBlur, this));
		},

		handleLabelClick: function(dir) {
			var label = this.getElement('label');

			label[dir ? 'on' : 'off']('click', $.proxy(this.onLabelClick, this));
		},

		handleLabelKeyUp: function(dir) {
			var label = this.getElement('label');

			label[dir ? 'on' : 'off']('keyup', $.proxy(this.onLabelKeyUp, this));
		},

		onInputChange: function(evt) {
			var input = evt.target;

			this.updateLabel(input.value);
		},

		onInputBlur: function() {
			this.deactivate();
		},

		onLabelClick: function() {
			this.activate();
		},

		onLabelKeyUp: function(evt) {
			if (evt.keyCode === 13) {
				this.activate();
			}
		},

		activate: function() {
			this.toggleView(true);
			this.focusInput();
		},

		deactivate: function() {
			this.toggleView(false);
		},

		toggleView: function(dir) {
			this.$el.toggleClass('is--active', !!dir);
		},

		focusInput: function() {
			var input = this.getElement('input');

			input.eq(0).focus();
		},

		updateLabel: function(value) {
			var label = this.getElement('label');
			var valueText = (value != null ? '' + value : '').trim();
			var labelText;

			if (valueText.length > 0) {
				labelText = valueText;
			} else {
				labelText = this.getLang('TOGGLE');
			}

			label.text(labelText);
		}

	}, {
		dataName: 'UiHiddenInput'
	});

})(BX, jQuery, window);