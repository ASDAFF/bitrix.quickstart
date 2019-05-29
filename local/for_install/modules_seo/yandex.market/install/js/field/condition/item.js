(function(BX, $, window) {

	'use strict';

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Condition = BX.namespace('YandexMarket.Field.Condition');
	var Source = BX.namespace('YandexMarket.Source');
	var Ui = BX.namespace('YandexMarket.Ui');

	var constructor = Condition.Item = Reference.Base.extend({

		defaults: {
			inputElement: '.js-condition-item__input',
			inputHolderElement: '.js-condition-item__input-holder',

			fieldElement: '.js-condition-item__field',
			compareElement: '.js-condition-item__compare',
			valueCellElement: '.js-condition-item__value-cell',
			valueElement: '.js-condition-item__value',

			managerElement: '.js-condition-manager',

			valueHiddenTemplate: '<input class="js-condition-item__input js-condition-item__value" type="hidden" />',
			valueInputTemplate: '<input class="b-filter-condition-field__input adm-input js-condition-item__input js-condition-item__value" type="text" />',
			valueSelectTemplate: '<select class="b-filter-condition-field__input js-condition-item__input js-condition-item__value js-plugin" size="1" data-plugin="Ui.Input.TagInput"></select>',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_CONDITION_'
		},

		initVars: function() {
			this.callParent('initVars', [constructor]);
			this._lastField = null;
			this._lastCompare = null;
		},

		initialize: function() {
			this.callParent('initialize', [constructor]);
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', [constructor]);
		},

		bind: function() {
			this.handleFieldChange(true);
			this.handleCompareChange(true);
		},

		unbind: function() {
			this.handleFieldChange(false);
			this.handleCompareChange(false);
		},

		handleFieldChange: function(dir) {
			var field = this.getElement('field');

			field.on('change keyup', $.proxy(this.onFieldChange, this));
		},

		handleCompareChange: function(dir) {
			var compare = this.getElement('compare');

			compare.on('change keyup', $.proxy(this.onCompareChange, this));
		},

		onFieldChange: function(evt) {
			var field = $(evt.target);
			var option = field.find('option').filter(':selected');
			var fieldValue = option.val();
			var type;
			var compareValue;

			if (this._lastField == null || this._lastField !== fieldValue) {
				this._lastField = fieldValue;

				type = option.data('type');
				compareValue = this.refreshCompare(type);

				this._lastCompare = compareValue;

				this.refreshValue(fieldValue, compareValue);
			}
		},

		onCompareChange: function(evt) {
			var compare = $(evt.target);
			var option = compare.find('option').filter(':selected');
			var fieldValue;
			var compareValue = option.val();

			if (this._lastCompare == null || this._lastCompare !== compareValue) {
				this._lastCompare = compareValue;

				fieldValue = this.getElement('field').val();

				this.refreshValue(fieldValue, compareValue);
			}
		},

		updateName: function() {
			var baseName = this.getBaseName();

			this.callParent('updateName', [constructor]);

			this.getElement('inputHolder').each(function(index, element) {
				element.name = baseName + '[' + element.getAttribute('data-name') + ']';
			});
		},

		unsetName: function() {
			this.callParent('unsetName', [constructor]);

			this.getElement('inputHolder').removeAttr('name');
		},

		getDisplayValue: function() {
			var result = this.callParent('getDisplayValue', constructor);
			var valueElement = this.getElement('value');

			result['VALUE_HIDDEN'] = ((valueElement.prop('type') || '').toLowerCase() === 'hidden');

			return result;
		},

		isValid: function() {
			var valueList = this.getValue();
			var result = true;

			if (
				this.isEmptyValue(valueList['FIELD'])
				|| this.isEmptyValue(valueList['COMPARE'])
				|| this.isEmptyValue(valueList['VALUE'])
			) {
				result = false;
			}

			return result;
		},

		isEmptyValue: function(value) {
			var result = false;
			var valueString;

			if (value == null) {
				result = true;
			} else if ($.isArray(value)) {
				result = (value.length === 0);
			} else {
				valueString = ('' + value).trim();
				result = (valueString === '');
			}

			return result;
		},

		refreshCompare: function(fieldType) {
			var manager = this.getManager();
			var compareSelect = this.getElement('compare');
			var optionList = compareSelect.find('option');
			var option;
			var compareValue;
			var compareData;
			var optionTypesAttribute;
			var optionTypes;
			var i;
			var isActive;
			var firstActiveOption;
			var needResetSelected = false;
			var result;

			for (i = optionList.length - 1; i >= 0; i--) {
				option = optionList.eq(i);
				compareValue = option.val();
				compareData = manager.getCompare(compareValue)
				isActive = (!fieldType || !compareData || compareData['TYPE_LIST'].indexOf(fieldType) !== -1);

				option.prop('disabled', !isActive);

				if (isActive) {
					firstActiveOption = option;

					if (option.prop('selected')) {
						result = compareValue;
					}
				} else {
					option.prop('selected') && (needResetSelected = true);
				}
			}

			if (needResetSelected && firstActiveOption) {
				firstActiveOption.prop('selected', true);
				result = firstActiveOption.val();
			} else if (result == null && firstActiveOption) {
				result = firstActiveOption.val();
			}

			return result;
		},

		refreshValue: function(fieldValue, compareValue) {
			var isMultiple = this.isValueMultiple(compareValue);
			var definedValue = this.getValueDefined(compareValue);
			var valueList;

			if (definedValue == null) {
				valueList = this.getValueList(fieldValue, compareValue);
			}

			this.updateValue(valueList, isMultiple, definedValue);
		},

		updateValue: function(enumList, isMultiple, definedValue) {
			var valueCell = this.getElement('valueCell');
			var valueElement = this.getElement('value');
			var enumItem;
			var i;
			var valueTagName = (valueElement.prop('tagName') || '').toLowerCase();
			var needTagName;
			var content;

			if (
				valueTagName === 'input'
				&& (valueElement.prop('type') || '').toLowerCase() === 'hidden'
			) {
				valueTagName = 'hidden';
			}

			if (definedValue != null) {
				needTagName = 'hidden';
			} else if (enumList && enumList.length > 0) {
				needTagName = 'select';
				content = '';

				for (i = 0; i < enumList.length; i++) {
					enumItem = enumList[i];

					content += '<option value="' + enumItem['ID'] + '">' + enumItem['VALUE'] + '</option>';
				}
			} else {
				needTagName = 'input';
			}

			if (valueTagName !== needTagName || isMultiple !== valueElement.prop('multiple')) {
				valueElement = this.renderValue(valueElement, needTagName, isMultiple);
			}

			if (content != null) {
				valueElement.html(content);
				valueElement.triggerHandler('uiRefresh');
			}

			if (definedValue != null) {
				valueElement.val(definedValue);
				valueCell.addClass('visible--hidden');
			} else {
				valueCell.removeClass('visible--hidden');
			}
		},

		renderValue: function(element, tagName, isMultiple) {
			var templateKey = 'value' + tagName.substr(0, 1).toUpperCase() + tagName.substr(1);
			var template = this.getTemplate(templateKey);
			var newField = $(template);

			this.destroyValueUi(element);

			newField.prop('multiple', !!isMultiple);

			this.copyAttrList(element, newField, ['name', 'data-name']);

			newField.insertAfter(element);
			element.remove();

			this.initValueUi(newField);

			return newField;
		},

		destroyValueUi: function(newField) {
			var value = newField || this.getElement('value');

			Plugin.manager.destroyContext(value);
		},

		initValueUi: function(newField) {
			var value = newField || this.getElement('value');

			Plugin.manager.initializeContext(value);
		},

		copyAttrList: function(fromElement, toElement, attrList) {
			var attrName;
			var attrValue;
			var i;

			for (i = 0; i < attrList.length; i++) {
				attrName = attrList[i];
				attrValue = fromElement.attr(attrName);

				if (attrName === 'name' && fromElement.prop('multiple') !== toElement.prop('multiple')) {
					if (toElement.prop('multiple')) {
						attrValue += '[]';
					} else {
						attrValue = attrValue.replace(/\[\]$/, '');
					}
				}

				if (attrValue != null) {
					toElement.attr(attrName, attrValue);
				}
			}
		},

		getValueDefined: function(compareValue) {
			var manager = this.getManager();
			var compareData = manager.getCompare(compareValue);

			return (compareData && 'DEFINED' in compareData ? compareData['DEFINED'] : null);
		},

		isValueMultiple: function(compareValue) {
			var manager = this.getManager();
			var compareData = manager.getCompare(compareValue);

			return (compareData && compareData['MULTIPLE']);
		},

		getValueList: function(fieldValue, compareValue) {
			var manager = this.getManager();
			var compareData = manager.getCompare(compareValue);
			var result;

			if (compareData && 'ENUM' in compareData) {
				result = compareData['ENUM'];
			} else {
				result = manager.getEnumList(fieldValue);
			}

			return result;
		},

		getManager: function() {
			var parent = this.getParentField();
			var managerElement;
			var result;

			if (parent) {
				result = parent.getManager();
			} else {
				managerElement = this.getElement('manager', this.$el, 'closest');
				result = Source.Manager.getInstance(managerElement);
			}

			return result;
		}

	}, {
		dataName: 'FieldConditionItem'
	});

})(BX, jQuery, window);