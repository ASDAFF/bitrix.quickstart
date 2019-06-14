(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Input = BX.namespace('YandexMarket.Ui.Input');

	var constructor = Input.CopyClipboard = Plugin.Base.extend({

		defaults: {
			inputElement: 'input',

			lang: {},
			langPrefix: 'YANDEX_MARKET_INPUT_COPY_CLIPBOARD_'
		},

		activate: function() {
			var input = this.getElement('input', this.$el, 'siblings');

			input.focus();
			input.select();

			try {
				document.execCommand('copy');
				alert(this.getLang('SUCCESS'));
			} catch (err) {
				alert(this.getLang('FAIL'));
			}
		}

	}, {
		dataName: 'UiCopyClipboard'
	});

})(BX, jQuery, window);