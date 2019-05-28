(function(BX, $, window) {

	'use strict';

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Condition = BX.namespace('YandexMarket.Field.Condition');
	var utils = BX.namespace('YandexMarket.Utils');

	var constructor = Condition.Summary = Reference.Summary.extend({

		defaults: {
			textElement: '.js-condition-summary__text',

			editButtonElement: '.js-condition-summary__edit-button',
			editModalElement: '.js-condition-summary__edit-modal',

			modalElement: '.js-condition-summary__edit-modal',
			fieldElement: '.js-condition-summary__field',

			countElement: '.js-condition-summary__count',
			countMessageElement: '.js-condition-summary__count-message',
			countMessageTextElement: '.js-condition-summary__count-message-text',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_CONDITION_'
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
			this.handleTextClick(true);
			this.handleEditButtonClick(true);
		},

		unbind: function() {
			this.handleTextClick(false);
			this.handleEditButtonClick(false);
		},

		handleTextClick: function(dir) {
			var textElement = this.getElement('text');

			textElement[dir ? 'on' : 'off']('click', $.proxy(this.onTextClick, this));
		},

		handleEditButtonClick: function(dir) {
			var editButton = this.getElement('editButton');

			editButton[dir ? 'on' : 'off']('click', $.proxy(this.onEditButtonClick, this));
		},

		onTextClick: function(evt) {
			this.openEditModal();

			evt.preventDefault();
		},

		onEditButtonClick: function(evt) {
			this.openEditModal();

			evt.preventDefault();
		},

		updateField: function(modalContent) {
			this.callParent('updateField', [modalContent], constructor);
			this.$el.trigger('FieldConditionChange');
		},

		refreshSummary: function() {
			var displayValueList = this.callField('getDisplayValue');
			var displayValue;
			var displayValueIndex;
			var displayValueText;
			var textParts = [];
			var junction;
			var text;
			var textElement = this.getElement('text');

			if (!$.isArray(displayValueList)) {
				displayValueList = [ displayValueList ];
			}

			for (displayValueIndex = 0; displayValueIndex < displayValueList.length; displayValueIndex++) {
				displayValue = displayValueList[displayValueIndex];

				if (displayValue && this.isValidDisplayValue(displayValue)) {
					displayValueText = this.getDisplayValueText(displayValue);

					textParts.push(displayValueText);
				}
			}

			if (textParts.length > 0) {
				junction = this.getLang('JUNCTION');
				text = textParts.join(junction);
			} else {
				text = this.getLang('PLACEHOLDER');
			}

			textElement.text(text);
		},

		isValidDisplayValue: function(displayValue) {
			return (
				displayValue['FIELD'].length > 0
				&& displayValue['COMPARE'].length > 0
				&& displayValue['VALUE'].length > 0
			);
		},

		getDisplayValueText: function(displayValue) {
			var result = displayValue['FIELD'] + ' ' + displayValue['COMPARE'];

			if (!displayValue['VALUE_HIDDEN']) {
				result += ' ' + displayValue['VALUE'];
			}

			return result;
		},

		updateCount: function(countList, warningList) {
			var baseName = this.getBaseName();
			var count = countList && baseName in countList ? countList[baseName] : null;
			var warning = warningList && baseName in warningList ? warningList[baseName] : null;

			this.updateCountSelf(count, warning);
			this.callField('updateCount', [countList, warningList]);
		},

		progressCount: function() {
			this.progressCountSelf();
			this.callField('progressCount');
		},

		updateCountSelf: function(count, warning) {
			var countInteger = parseInt(count, 10);
			var countElement = this.getElement('count');
			var countText;
			var messageElement = this.getElement('countMessage');
			var messageTextElement = this.getElement('countMessageText', messageElement);
			var messageText;

			if (!isNaN(countInteger)) {
				countText = this.getLang('COUNT', {
					'COUNT': count,
					'LABEL': utils.sklon(count, [
						this.getLang('PRODUCT_1'),
						this.getLang('PRODUCT_2'),
						this.getLang('PRODUCT_5')
					])
				});

				countElement.html(countText);
				countElement.removeClass('is--hidden');

				if (countInteger === 0) {
					messageText = this.getLang('COUNT_EMPTY');
				}
			} else {
				messageText = this.getLang('COUNT_FAIL');
			}

			if (warning) {
				messageText = warning;
			}

			if (messageText) {
				messageElement.removeClass('is--hidden');
				messageTextElement.html(messageText);
			} else {
				messageElement.addClass('is--hidden');
				messageTextElement.html('');
			}
		},

		progressCountSelf: function() {
			var countElement = this.getElement('count');
			var messageElement = this.getElement('countMessage');
			var messageTextElement = this.getElement('countMessageText', messageElement);
			var countText = this.getLang('COUNT_PROGRESS');

			countElement.text(countText);
			countElement.removeClass('is--hidden');

			messageElement.addClass('is--hidden');
			messageTextElement.text('');
		},

		getFieldPlugin: function() {
			return Condition.Collection;
		}

	}, {
		dataName: 'FieldConditionSummary'
	});

})(BX, $, window);