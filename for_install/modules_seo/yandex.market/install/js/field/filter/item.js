(function(BX, $, window) {

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Filter = BX.namespace('YandexMarket.Field.Filter');
	var utils = BX.namespace('YandexMarket.Utils');

	var constructor = Filter.Item = Reference.Complex.extend({

		defaults: {
			inputElement: '.js-filter-item__input',
			sortElement: '.js-filter-item__sort',
			childElement: '.js-filter-item__child',
		},

		initEdit: function() {
			var conditionField = this.getChildCondition();
			var result;

			// initialize edit condition

			if (conditionField) {
				result = conditionField.initEdit();
			}

			// if condition initEdit failed, then call parent

			if (!result) {
				result = this.callParent('initEdit', constructor);
			}

			return result;
		},

		setIndex: function(index) {
			this.callParent('setIndex', [index], constructor);
			this.setSort(index); // save index to sort input
		},

		setSort: function(value) {
			this.getElement('sort').val(value);
		},

		updateCount: function(countList, warningList) {
			var conditionField = this.getChildCondition();

			if (conditionField) {
				conditionField.updateCount(countList, warningList);
			}
		},

		progressCount: function() {
			var conditionField = this.getChildCondition();

			if (conditionField) {
				conditionField.progressCount();
			}
		},

		getChildCondition: function() {
			var childMap = this.getChildInstanceMap();
			var result;

			if ('FILTER_CONDITION' in childMap) {
				result = childMap['FILTER_CONDITION'];
			}

			return result;
		}

	}, {
		dataName: 'FieldFilterItem'
	});


})(BX, jQuery, window);