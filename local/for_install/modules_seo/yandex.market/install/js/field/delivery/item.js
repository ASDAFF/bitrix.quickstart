(function(BX, $, window) {

	'use strict';

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Delivery = BX.namespace('YandexMarket.Field.Delivery');

	var constructor = Delivery.Item = Reference.Base.extend({

		defaults: {
			inputElement: '.js-delivery-item__input',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_DELIVERY_'
		}

	}, {
		dataName: 'FieldDeliveryItem'
	});

})(BX, jQuery, window);