(function(BX, $, window) {

	'use strict';

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Condition = BX.namespace('YandexMarket.Field.Condition');
	var utils = BX.namespace('YandexMarket.Utils');
	var Source = BX.namespace('YandexMarket.Source');

	var constructor = Condition.Collection = Reference.Collection.extend({

		defaults: {
			persistent: true,

			junctionElement: '.js-condition-collection__junction',
			junctionTemplate: '.js-condition-collection__junction-template',

			itemElement: '.js-condition-collection__item',
			itemDeleteElement: '.js-condition-collection__item-delete',

			addButtonElement: '.js-condition-collection__add-button',

			countElement: '.js-condition-collection__count',
			countMessageElement: '.js-condition-collection__count-message',
			countMessageTextElement: '.js-condition-collection__count-message-text',

			managerElement: '.js-condition-manager',

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_CONDITION_',

			refreshCountDelay: 500
		},

		initVars: function() {
			this.callParent('initVars', constructor);
			this._refreshCountTimeout = null;
			this._refreshCountQuery = null;
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
			this.handleAddButtonClick(true);
			this.handleItemDeleteClick(true);
			this.handleItemChange(true);
		},

		unbind: function() {
			this.handleAddButtonClick(false);
			this.handleItemDeleteClick(false);
			this.handleItemChange(false);
		},

		handleAddButtonClick: function(dir) {
			var addButton = this.getElement('addButton');

			addButton[dir ? 'on' : 'off']('click', $.proxy(this.onAddButtonClick, this));
		},

		handleItemDeleteClick: function(dir) {
			var itemDeleteSelector = this.getElementSelector('itemDelete');

			this.$el[dir ? 'on' : 'off']('click', itemDeleteSelector, $.proxy(this.onItemDeleteClick, this));
		},

		handleItemChange: function(dir) {
			var itemSelector = this.getElementSelector('item');

			this.$el[dir ? 'on' : 'off']('change', itemSelector, $.proxy(this.onItemChange, this));
		},

		onAddButtonClick: function(evt) {
			var instance = this.addItem();

			instance.initEdit();

			evt.preventDefault();
		},

		onItemDeleteClick: function(evt) {
			var deleteButton = $(evt.target);
			var item = this.getElement('item', deleteButton, 'closest');

			this.deleteItem(item);

			if (this.isValid()) {
				this.refreshCountDelayed();
			} else {
				this.invalidateCount();
				this.refreshCountCancel(true);
			}

			evt.preventDefault();
		},

		onItemChange: function() {
			if (this.isValid()) {
				this.refreshCountDelayed();
			} else {
				this.invalidateCount();
				this.refreshCountCancel(true);
			}
		},

		clear: function() {
			this.callParent('clear', [constructor]);
			this.invalidateCount();
		},

		toggleItemDeleteView: function(dir) {
			this.getElement('itemDelete').toggleClass('is--hidden', !dir);
		},

		appendItem: function(item, context, method) {
			var junctionTemplate = this.getTemplate('junction');
			var junctionElement = $(junctionTemplate);

			context[method](junctionElement);
			item.insertAfter(junctionElement);

			this.toggleItemDeleteView(true);
		},

		detachItem: function(item) {
			var junctionElement = this.getElement('junction', item, 'prev');

			if (junctionElement.length === 0) { // first delete
				junctionElement = this.getElement('junction', item, 'next');
			}

			junctionElement.detach();
			item.detach();

			if (this.getElement('item').length <= 1) {
				this.toggleItemDeleteView(false);
			}
		},

		invalidateCount: function() {
			this.getElement('count').addClass('is--hidden');
			this.getElement('countMessage').addClass('is--hidden');
		},

		updateCount: function(countList, warningList) {
			var baseName = this.getBaseName();
			var count = countList && baseName in countList ? countList[baseName] : null;
			var countInteger = parseInt(count, 10);
			var countElement = this.getElement('count');
			var countText;
			var warning = warningList && baseName in warningList ? warningList[baseName] : null;
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

		progressCount: function() {
			var countElement = this.getElement('count');
			var messageElement = this.getElement('countMessage');
			var messageTextElement = this.getElement('countMessageText', messageElement);
			var countText = this.getLang('COUNT_PROGRESS');

			countElement.text(countText);
			countElement.removeClass('is--hidden');

			messageElement.addClass('is--hidden');
			messageTextElement.text('');
		},

		refreshCountDelayed: function() {
			this.progressCount();
			this.refreshCountDelayedCancel();

			this._refreshCountTimeout = setTimeout(
				$.proxy(this.refreshCount, this),
				this.options.refreshCountDelay
			);
		},

		refreshCountDelayedCancel: function() {
			clearTimeout(this._refreshCountTimeout);
		},

		refreshCount: function() {
			this.refreshCountCancel(true);
			this.progressCount();

			this._refreshCountQuery = this.buildRefreshQuery();

			this._refreshCountQuery.then(
				$.proxy(this.refreshCountEnd, this),
				$.proxy(this.refreshCountStop, this)
			);
		},

		refreshCountCancel: function(isSilent) {
			this.refreshCountDelayedCancel();

			if (this._refreshCountQuery !== null) {
				this._refreshCountQuery.abort(isSilent ? 'silent' : 'manual');
			}
		},

		refreshCountStop: function(xhr, status) {
			if (status !== 'silent') {
				this.updateCount();
			}

			this._refreshCountQuery = null;
		},

		refreshCountEnd: function(data) {
			this.updateCount(data['countList'], data['warningList']);

			this._refreshCountQuery = null;
		},

		buildRefreshQuery: function() {
			var config = {
				url: '',
				type: 'post',
				data: this.getFormData(),
				dataType: 'json'
			};

			config['data'].push({
				name: 'ajaxAction',
				value: 'filterCount'
			});

			config['data'].push({
				name: 'baseName',
				value: this.getBaseName()
			});

			return $.ajax(config);
		},

		isValid: function() {
			var result = true;
			var hasItems = false;

			this.callItemList(function(instance) {
				hasItems = true;

				if (!instance.isValid()) {
					result = false;
				}
			});

			if (!hasItems) {
				result = false;
			}

			return result;
		},

		getFormData: function() {
			var parent = this.getParentField();
			var form;
			var result;
			var baseName;
			var i;

			if (parent) {
				form = parent.$el.closest('form');
				baseName = this.getBaseName();
				result = form.serializeArray();

				for (i = result.length - 1; i >= 0; i--) {
					if (result[i].name.indexOf(baseName) === 0) {
						result.splice(i, 1);
					}
				}

				result = result.concat(
					this.$el.find('input, select, textarea').serializeArray()
				);
			} else {
				form = this.$el.closest('form');
				result = form.serializeArray();
			}

			return result;
		},

		getItemPlugin: function() {
			return Condition.Item;
		},

		getManager: function() {
			var parent = this.getParentField();
			var managerElement;

			if (parent) {
				managerElement = this.getElement('manager', parent.$el, 'closest');
			} else {
				managerElement = this.getElement('manager', this.$el, 'closest');
			}

			return Source.Manager.getInstance(managerElement);
		}

	}, {
		dataName: 'FieldConditionCollection'
	});

})(BX, jQuery, window);