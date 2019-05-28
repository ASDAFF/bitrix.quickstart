(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Input = BX.namespace('YandexMarket.Ui.Input');

	var constructor = Input.DependList = Plugin.Base.extend({

		defaults: {
			dependElement: null,
			optionElement: 'option',
			fieldElement: '.js-form-field'
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
			this.handleDependChange(true);
		},

		unbind: function() {
			this.handleDependChange(false);
		},

		handleDependChange: function(dir) {
			var depend = this.getElement('depend');

			depend[dir ? 'on' : 'off']('change keyup', $.proxy(this.onDependChange, this));
		},

		onDependChange: function(evt) {
			var value = evt.target.value;

			this.updateAvailable(value);
		},

		updateAvailable: function(value) {
			var optionList = this.getElement('option');
			var option;
			var availableList;
			var isAvailable;
			var i;
			var needResetSelected;
			var firstAvailable;
			var availableCount = 0;

			for (i = optionList.length - 1; i >= 0; i--) {
				option = optionList.eq(i);
				availableList = (option.data('available').split(',') || '');
				isAvailable = (availableList.indexOf(value) !== -1);

				if (isAvailable) {
					firstAvailable = option;
					option.prop('disabled', false);
					option.prop('hidden', false);
					availableCount++;
				} else {
					option.prop('disabled', true);
					option.prop('hidden', true);

					if (option.prop('selected')) {
						needResetSelected = true;
						option.prop('selected', false);
					}
				}
			}

			if (needResetSelected && firstAvailable) {
				firstAvailable.prop('selected', true);
			}

			this.updateFieldView(availableCount > 1);
		},

		updateFieldView: function(dir) {
			var field = this.getElement('field', this.$el, 'closest');

			field.toggleClass('is--hidden', !dir);
		}

	}, {
		dataName: 'UiInputDependList'
	});

})(BX, jQuery, window);