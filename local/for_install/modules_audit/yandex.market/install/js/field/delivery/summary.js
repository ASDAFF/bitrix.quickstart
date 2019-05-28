(function(BX, $, window) {

	'use strict';

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Delivery = BX.namespace('YandexMarket.Field.Delivery');
	var Utils = BX.namespace('YandexMarket.Utils');

	var constructor = Delivery.Summary = Reference.Summary.extend({

		defaults: {
			groupElement: '.js-delivery-summary-group',

			listElement: '.js-delivery-summary__list',
			itemElement: '.js-delivery-summary__item',
			itemTextElement: '.js-delivery-summary__item-text',
			itemDeleteElement: '.js-delivery-summary__item-delete',

			editButtonElement: '.js-delivery-summary__edit-button',
			editModalElement: '.js-delivery-summary__edit-modal',

			modalElement: '.js-delivery-summary__edit-modal',
			fieldElement: '.js-delivery-summary__field',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_DELIVERY_'
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
			this.handleItemTextClick(true);
			this.handleItemDeleteClick(true);
			this.handleEditButtonClick(true);
		},

		unbind: function() {
			this.handleItemTextClick(false);
			this.handleItemDeleteClick(false);
			this.handleEditButtonClick(false);
		},

		handleItemTextClick: function(dir) {
			var list = this.getElement('list');
			var itemTextSelector = this.getElementSelector('itemText');

			list[dir ? 'on' : 'off']('click', itemTextSelector, $.proxy(this.onItemTextClick, this));
		},

		handleItemDeleteClick: function(dir) {
			var list = this.getElement('list');
			var itemDeleteSelector = this.getElementSelector('itemDelete');

			list[dir ? 'on' : 'off']('click', itemDeleteSelector, $.proxy(this.onItemDeleteClick, this));
		},

		handleEditButtonClick: function(dir) {
			var editButton = this.getElement('editButton');

			editButton[dir ? 'on' : 'off']('click', $.proxy(this.onEditButtonClick, this));
		},

		onItemTextClick: function(evt) {
			var itemDeleteElement = $(evt.target);
			var item = this.getElement('item', itemDeleteElement, 'closest');
			var index = item.data('index');

			this.openEditModal(index);

			evt.preventDefault();
		},

		onItemDeleteClick: function(evt) {
			var itemDeleteElement = $(evt.target);
			var item = this.getElement('item', itemDeleteElement, 'closest');

			this.deleteItem(item);

			evt.preventDefault();
		},

		onEditButtonClick: function(evt) {
			this.openEditModal();

			evt.preventDefault();
		},

		deleteItem: function(item) {
			var itemIndex = item.data('index');
			var fieldInstance = this.getFieldInstance();
			var fieldItem = fieldInstance.getItem(itemIndex);

			fieldItem && fieldInstance.deleteItem(fieldItem);
			item.remove();

			this.refreshFillState();
		},

		refreshFillState: function() {
			var activeItemCollection = this.getElement('item').not('.is--hidden');
			var hasItems = (activeItemCollection.length > 0);

			this.updateFillState(hasItems);
		},

		updateFillState: function(hasItems) {
			var elements = [
				this.getElement('list'),
				this.getElement('group', this.$el, 'closest'),
				this.$el
			];
			var element;
			var i;

			for (i = 0; i < elements.length; i++) {
				element = elements[i];

				element.toggleClass('is--empty', !hasItems);
				element.toggleClass('is--fill', hasItems);

				if (hasItems) {
					element.removeClass('is--invalid');
				}
			}
		},

		refreshSummary: function() {
			var displayValueList = this.callField('getDisplayValue');
			var displayValue;
			var displayValueIndex;
			var displayValueText;
			var listElement = this.getElement('list');
			var itemCollection = this.getElement('item');
			var itemCollectionLength = itemCollection.length;
			var itemCollectionLeft;
			var itemElement;
			var itemTextElement;
			var itemIndex = 0;
			var hasItems;

			if (!$.isArray(displayValueList)) {
				displayValueList = [ displayValueList ];
			}

			for (displayValueIndex = 0; displayValueIndex < displayValueList.length; displayValueIndex++) {
				displayValue = displayValueList[displayValueIndex];

				if (displayValue && this.isValidDisplayValue(displayValue)) {
					displayValueText = this.getDisplayValueText(displayValue);
					itemElement = null;

					if (itemIndex < itemCollectionLength) {
						itemElement = itemCollection.eq(itemIndex);
					} else {
						itemElement = itemCollection.eq(0).clone(false, false);
						itemElement.appendTo(listElement);
					}

					itemTextElement = this.getElement('itemText', itemElement);

					itemElement.data('index', displayValueIndex);
					itemTextElement.html(displayValueText);
					itemElement.removeClass('is--hidden'); // remove placeholder

					itemIndex++;
				}
			}

			hasItems = (itemIndex > 0);

			if (itemIndex < itemCollectionLength) {
				itemCollectionLeft = itemCollection.slice(Math.max(1, itemIndex));
				itemCollectionLeft.remove();

				if (!hasItems) {
					itemCollection.eq(0).addClass('is--hidden');
				}
			}

			this.updateFillState(hasItems);
		},

		isValidDisplayValue: function(displayValue) {
			return (
				(displayValue['PERIOD_FROM'].length > 0 || displayValue['PERIOD_TO'].length)
				&& (displayValue['PRICE'].length > 0)
			);
		},

		getDisplayValueText: function(displayValue) {
			var result = '';
			var deliveryType = (displayValue['DELIVERY_TYPE'] || '');
			var hasPeriodFrom = (displayValue['PERIOD_FROM'].length > 0);
			var hasPeriodTo = (displayValue['PERIOD_TO'].length > 0);
			var period;
			var periodForSklon;
			var hasPrice = (displayValue['PRICE'].length > 0);

			if (deliveryType.length > 0) {
				result += this.getLang('DELIVERY_TYPE_' + deliveryType.toUpperCase());
			}

			if (displayValue['NAME'].length > 0) {
				result += ' ' + this.getLang('NAME', {
					'NAME': displayValue['NAME']
				});
			}

			if (displayValue['ORDER_BEFORE'].length > 0) {
				result += ' ' + this.getLang('ORDER_BEFORE_LANG', {
					'ORDER_BEFORE': displayValue['ORDER_BEFORE'],
					'HOUR_LABEL': Utils.sklon(displayValue['ORDER_BEFORE'], [
						this.getLang('HOUR_1'),
						this.getLang('HOUR_2'),
						this.getLang('HOUR_5'),
					])
				});
			}

			if (hasPeriodFrom || hasPeriodTo) {
				period = '';
				periodForSklon = null;

				if (hasPeriodFrom && hasPeriodTo) {
					period = displayValue['PERIOD_FROM'] + '-' + displayValue['PERIOD_TO'];
					periodForSklon = displayValue['PERIOD_TO'];
				} else if (hasPeriodFrom) {
					period = displayValue['PERIOD_FROM'];
					periodForSklon = displayValue['PERIOD_FROM'];
				} else {
					period = displayValue['PERIOD_TO'];
					periodForSklon = displayValue['PERIOD_TO'];
				}

				result += ' ' + this.getLang('PERIOD_LANG', {
					'DAY_PERIOD': period,
					'DAY_LABEL': Utils.sklon(periodForSklon, [
						this.getLang('DAY_1'),
						this.getLang('DAY_2'),
						this.getLang('DAY_5'),
					])
				});
			}

			if (hasPrice) {
				result += ' ' + this.getLang('PRICE_LANG', {
					'PRICE': displayValue['PRICE'],
					'PRICE_CURRENCY': Utils.sklon(displayValue['PRICE'], [
						this.getLang('PRICE_CURRENCY_1'),
						this.getLang('PRICE_CURRENCY_2'),
						this.getLang('PRICE_CURRENCY_5'),
					])
				});
			}

			result = result.trim();
			result = (result.substr(0, 1) || '').toUpperCase() + result.substr(1);

			return result;
		},

		getFieldPlugin: function() {
			return Delivery.Collection;
		}

	}, {
		dataName: 'FieldDeliverySummary'
	});

})(BX, $, window);