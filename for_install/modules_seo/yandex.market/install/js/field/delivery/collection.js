(function(BX, $, window) {

	'use strict';

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Delivery = BX.namespace('YandexMarket.Field.Delivery');

	var constructor = Delivery.Collection = Reference.Collection.extend({

		defaults: {
			persistent: true,
			maxLength: 5,

			itemElement: '.js-delivery-collection__item',
			itemAddElement: '.js-delivery-collection__item-add',
			itemDeleteElement: '.js-delivery-collection__item-delete',

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
			this.handleItemAddClick(true);
			this.handleItemDeleteClick(true);
		},

		unbind: function() {
			this.handleItemAddClick(false);
			this.handleItemDeleteClick(false);
		},

		handleItemAddClick: function(dir) {
			var itemAddSelector = this.getElementSelector('itemAdd');

			this.$el[dir ? 'on' : 'off']('click', itemAddSelector, $.proxy(this.onItemAddClick, this));
		},

		handleItemDeleteClick: function(dir) {
			var itemDeleteSelector = this.getElementSelector('itemDelete');

			this.$el[dir ? 'on' : 'off']('click', itemDeleteSelector, $.proxy(this.onItemDeleteClick, this));
		},

		onItemAddClick: function(evt) {
			var addButton = $(evt.target);
			var item = this.getElement('item', addButton, 'closest');
			var instance = this.addItem(item, item);

			instance && instance.initEdit();

			evt.preventDefault();
		},

		onItemDeleteClick: function(evt) {
			var deleteButton = $(evt.target);
			var item = this.getElement('item', deleteButton, 'closest');

			this.deleteItem(item);

			evt.preventDefault();
		},

		getItemPlugin: function() {
			return Delivery.Item;
		}

	}, {
		dataName: 'FieldDeliveryCollection'
	});

})(BX, jQuery, window);