(function(BX, $, window) {

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Param = BX.namespace('YandexMarket.Field.Param');
	var Source = BX.namespace('YandexMarket.Source');

	var constructor = Param.Node = Reference.Base.extend({

		defaults: {
			type: null,
			valueType: 'string',
			required: false,
			copyType: null,

			managerElement: '.js-param-manager',

			inputElement: '.js-param-node__input',
			sourceElement: '.js-param-node__source',
			fieldWrapElement: '.js-param-node__field-wrap',
			fieldElement: '.js-param-node__field',
			templateButtonElement: '.js-param-node__template-button',

			fieldVariableTemplate: '<input class="b-param-table__input js-param-node__input js-param-node__field" type="text" />',
			fieldSelectTemplate: '<select class="b-param-table__input js-param-node__input js-param-node__field"></select>',
			fieldTemplateTemplate: '<div class="b-control-group js-param-node__field-wrap">' +
			    '<input class="b-control-group__item pos--first b-param-table__input js-param-node__input js-param-node__field" type="text" />' +
			    '<button class="b-control-group__item pos--last adm-btn around--control js-param-node__template-button" type="button">...</button>' +
		    '</div>',

			langPrefix: 'YANDEX_MARKET_FIELD_PARAM_',
			lang: {}
		},

		initVars: function() {
			this.callParent('initVars', constructor);

			this._lastSource = null;
			this._fieldValueUserInput = null;
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
			this.handleSourceChange(true);
			this.handleInputChange(true);
			this.handleTemplateButtonClick(true);
		},

		unbind: function() {
			this.handleSourceChange(false);
			this.handleInputChange(false);
			this.handleTemplateButtonClick(false);
		},

		handleSourceChange: function(dir) {
			var sourceElement = this.getElement('source');

			sourceElement[dir ? 'on' : 'off']('change keyup', $.proxy(this.onSourceChange, this));
		},

		handleParentField: function(field, dir) {
			this.handleCopyTypeFieldChange(field, dir);
			this.handleCopyTypeSelfFieldInput(dir);
		},

		handleCopyTypeFieldChange: function(parentField, dir) {
			var type = this.options.copyType;
			var typeCollection;
			var typeField;

			if (type != null) {
				typeCollection = parentField.getTypeCollection(type);

				typeCollection[dir ? 'on' : 'off']('change keyup', this.getElementSelector('field'), $.proxy(this.onCopyTypeFieldChange, this));
			}
		},

		handleCopyTypeSelfFieldInput: function(dir) {
			var type = this.options.copyType;

			if (type != null) {
				this.$el[dir ? 'on' : 'off']('input paste', this.getElementSelector('field'), $.proxy(this.onCopyTypeSelfFieldInput, this));
			}
		},

		handleInputChange: function(dir) {
			var inputSelector = this.getElementSelector('input');

			this.$el[dir ? 'on' : 'off']('change', inputSelector, $.proxy(this.onInputChange, this));
		},

		handleTemplateButtonClick: function(dir) {
			var buttonSelector = this.getElementSelector('templateButton');

			this.$el[dir ? 'on' : 'off']('click', buttonSelector, $.proxy(this.onTemplateButtonClick, this));
		},

		onSourceChange: function(evt) {
			var sourceElement = $(evt.target);
			var sourceValue = sourceElement.val();

			if (this._lastSource == null || this._lastSource !== sourceValue) {
				this._lastSource = sourceValue;
				this.refreshField(sourceValue);
			}
		},

		onCopyTypeFieldChange: function(evt) {
			var input = evt.currentTarget;

			this.copyFieldValue(input);
		},

		onCopyTypeSelfFieldInput: function(evt) {
			var input = evt.currentTarget;

			this._fieldValueUserInput = (input.value !== '');
		},

		onInputChange: function() {
			this.$el.trigger('FieldParamNodeChange');
		},

		onTemplateButtonClick: function(evt) {
			var button = evt.currentTarget;
			var isReady = ('OPENER' in button);

			if (!isReady) {
				this.openTemplatePopup(button);
			}
		},

		onSelectTemplateOption: function(fieldPath) {
			this.insertFieldText('{=' + fieldPath + '}');
		},

		clear: function() {
			this.callParent('clear', constructor);
			this._fieldValueUserInput = false;
		},

		setParentField: function(field) {
			var previousParent = this.getParentField();

			if (previousParent != null) {
				this.handleParentField(previousParent, false);
			}

			if (field != null) {
				this.handleParentField(field, true);
			}

			this.callParent('setParentField', [field], constructor);
		},

		isFieldValueUserInput: function(field) {
			var fieldElement;
			var fieldValue;

			if (this._fieldValueUserInput == null) {
				fieldElement = field || this.getElement('field');
				fieldValue = fieldElement.val();

				this._fieldValueUserInput = (fieldValue != null && fieldValue !== '');
			}

			return this._fieldValueUserInput;
		},

		copyFieldValue: function(fromElement) {
			var fieldElement = this.getElement('field');
			var fieldTagName = (fieldElement.prop('tagName') || '').toLowerCase();
			var fieldValue = fieldElement.val();
			var fromTagName = (fromElement.tagName || '').toLowerCase();
			var fromValue;
			var option;

			if (fieldTagName === 'input' && fromTagName === 'select' && !this.isFieldValueUserInput(fieldElement)) { // support copy only in input from select
				option = $('option', fromElement).filter(':selected');

				if (option.val()) { // is not placeholder
					fromValue = option.text();
				}

				if (fromValue != null) {
					fromValue = fromValue.replace(/^\[\d+\]/, '').trim(); // remove id

					fieldElement.val(fromValue);
				}
			}
		},

		refreshField: function(typeId) {
			var manager = this.getManager();
			var type = manager.getType(typeId);
			var fieldList = this.getFieldList(manager, typeId);

			this.updateField(fieldList, type);
		},

		updateField: function(fieldEnumList, type) {
			var fieldElement = this.getElement('field');
			var fieldEnum;
			var i;
			var fieldType = (fieldElement.data('type') || '').toLowerCase();
			var needType;
			var content;

			if (fieldEnumList.length > 0) {
				needType = 'select';
				content = '';

				if (!this.options.required) {
					content += '<option value="">' + this.getLang('SELECT_PLACEHOLDER') + '</option>';
				}

				for (i = 0; i < fieldEnumList.length; i++) {
					fieldEnum = fieldEnumList[i];

					content += '<option value="' + fieldEnum['ID'] + '">' + fieldEnum['VALUE'] + '</option>';
				}
			} else if (type['TEMPLATE']) {
				needType = 'template';
			} else {
				needType = 'variable';
			}

			if (fieldType !== needType) {
				fieldElement = this.renderField(fieldElement, needType);
			}

			if (content != null) {
				fieldElement.html(content);
			}
		},

		renderField: function(field, type) {
			var templateKey = 'field' + type.substr(0, 1).toUpperCase() + type.substr(1);
			var template = this.getTemplate(templateKey);
			var fieldSelector = this.getElementSelector('field');
			var oldWrap = this.getElement('fieldWrap', field, 'closest');
			var newWrap = $(template);
			var newField = newWrap.filter(fieldSelector);

			if (oldWrap.length === 0) { oldWrap = field; }
			if (newField.length === 0) { newField = newWrap.find(fieldSelector); }

			this.copyAttrList(field, newField, ['name', 'data-name']);
			newField.data('type', type);

			newWrap.insertAfter(oldWrap);
			oldWrap.remove();

			this._fieldValueUserInput = false;

			return newField;
		},

		copyAttrList: function(fromElement, toElement, attrList) {
			var attrName;
			var attrValue;
			var i;

			for (i = 0; i < attrList.length; i++) {
				attrName = attrList[i];
				attrValue = fromElement.attr(attrName);

				if (attrValue != null) {
					toElement.attr(attrName, attrValue);
				}
			}
		},

		openTemplatePopup: function(button) {
			var options = this.getTemplateOptions();

			BX.adminShowMenu(button, options);
		},

		getTemplateOptions: function() {
			var manager = this.getManager();
			var typeList = manager.getTypeList();
			var typeIndex;
			var type;
			var typeOptions;
			var fieldList = manager.getFieldList();
			var fieldIndex;
			var field;
			var recommendationList;
			var result = [];

			for (typeIndex = 0; typeIndex < typeList.length; typeIndex++) {
				type = typeList[typeIndex];
				typeOptions = [];

				if (type['VARIABLE'] || type['TEMPLATE']) {
					// nothing
				} else if (type['ID'] === 'recommendation') {
					recommendationList = manager.getRecommendationList(this.options.type);

					if (recommendationList !== null) {
						for (fieldIndex = 0; fieldIndex < recommendationList.length; fieldIndex++) {
							field = recommendationList[fieldIndex];

							typeOptions.push({
								'TEXT': field['VALUE'],
								'ONCLICK': $.proxy(this.onSelectTemplateOption, this, field['ID'].replace('|', '.'))
							});
						}
					}
				} else {
					for (fieldIndex = 0; fieldIndex < fieldList.length; fieldIndex++) {
						field = fieldList[fieldIndex];

						if (field['SOURCE'] === type['ID']) {
							typeOptions.push({
								'TEXT': field['VALUE'],
								'ONCLICK': $.proxy(this.onSelectTemplateOption, this, type['ID'] + '.' + field['ID'])
							});
						}
					}
				}

				if (typeOptions.length > 0) {
					result.push({
						'TEXT': type['VALUE'],
						'MENU': typeOptions
					});
				}
			}

			return result;
		},

		insertFieldText: function(text) {
			var field = this.getElement('field');
			var node = field[0];
			var value = node.value;
			var partBefore;
			var partAfter;
			var endIndex;
			var range;

			field.focus();

			if (typeof node.selectionStart !== 'undefined' && typeof node.selectionEnd !== 'undefined') {
				endIndex = node.selectionEnd;
				partBefore = value.slice(0, node.selectionStart);
				partAfter = value.slice(endIndex);
				node.value = (partBefore.length > 0 ? partBefore + ' ' : '') + text + (partAfter.length > 0 ? ' ' + partAfter : '');
				node.selectionStart = node.selectionEnd = endIndex + text.length + (partBefore.length > 0 ? 1 : 0);
			} else {
				partBefore = '' + node.value;
				node.value = (partBefore.length > 0 ? partBefore + ' ' : '') + text;
			}

			field.trigger('change');
			field.focus();
		},

		getFieldList: function(manager, typeId) {
			var result;

			if (typeId === 'recommendation') {
				result = manager.getRecommendationList(this.options.type);
			} else {
				result = manager.getTypeFieldList(typeId, this.options.valueType);
			}

			return result;
		},

		getManager: function() {
			var element = this.getElement('manager', this.$el, 'closest');
			return Source.Manager.getInstance(element);
		}

	}, {
		dataName: 'FieldParamNode'
	});

})(BX, jQuery, window);