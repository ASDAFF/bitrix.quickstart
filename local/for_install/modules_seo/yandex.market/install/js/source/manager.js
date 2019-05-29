(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Source = BX.namespace('YandexMarket.Source');

	var constructor = Source.Manager = Plugin.Base.extend({

		defaults: {
			types: [],
			fields: [],
			typeMap: {},
			enums: {},
			compareList: {},
			recommendation: null
		},

		getTypeList: function() {
			return this.options.types;
		},

		getType: function(typeId) {
			var typeList = this.getTypeList();
			var type;
			var i;
			var result;

			for (i = 0; i < typeList.length; i++) {
				type = typeList[i];

				if (type['ID'] === typeId) {
					result = type;
				}
			}

			return result;
		},

		getRecommendationList: function(nodeName) {
			var result = null;

			if (this.options.recommendation !== null && nodeName in this.options.recommendation) {
				result = this.options.recommendation[nodeName];
			}

			return result;
		},

		getFieldList: function() {
			return this.options.fields;
		},

		getTypeFieldList: function(typeId, valueType) {
			var fieldList = this.getFieldList();
			var field;
			var i;
			var result = [];
			var typeList = this.getFieldTypeList(valueType);

			for (i = 0; i < fieldList.length; i++) {
				field = fieldList[i];

				if (
					field['SOURCE'] === typeId
					&& (typeList === null || typeList.indexOf(field['TYPE']) !== -1)
				) {
					result.push(field);
				}
			}

			return result;
		},

		getFieldTypeList: function(valueType) {
			var result = null;

			if (valueType && valueType in this.options.typeMap) {
				result = this.options.typeMap[valueType];
			}

			return result;
		},

		getEnumList: function(fieldId, sourceId) {
			var fullFieldId = (sourceId != null ? sourceId + '.' + fieldId : fieldId);
			var result;

			if (fullFieldId && fullFieldId in this.options.enums) {
				result = this.options.enums[fullFieldId];
			}

			return result;
		},

		getCompare: function(compareId) {
			var result = null;

			if (compareId && compareId in this.options.compareList) {
				result = this.options.compareList[compareId];
			}

			return result;
		}

	}, {
		dataName: 'SourceManagerField'
	});

})(BX, jQuery, window);