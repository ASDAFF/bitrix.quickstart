(function(BX, $, window) {

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Filter = BX.namespace('YandexMarket.Field.Filter');
	var utils = BX.namespace('YandexMarket.Utils');

	var constructor = Filter.Collection = Reference.Collection.extend({

		defaults: {
			sortElement: '.js-filter-collection__sort',
			sortButtonElement: '.js-filter-collection__sort-button',
			sortTemplate: '.js-filter-collection__sort-template',

			itemElement: '.js-filter-collection__item',
			itemDeleteElement: '.js-filter-collection__item-delete',

			addButtonElement: '.js-filter-collection__add-button',

			leftCountElement: '.js-filter-collection__left-count',
			leftMessageElement: '.js-filter-collection__left-message',
			leftMessageTextElement: '.js-filter-collection__left-message-text',

			refreshCountOnLoad: false,

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_FILTER_'
		},

		initialize: function() {
			this.callParent('initialize', constructor);
			this.bind();

			if (this.options.refreshCountOnLoad) {
				this.refreshCount();
			}
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleAddButtonClick(true);
			this.handleSortButtonClick(true);
			this.handleItemDeleteClick(true);
			this.handleItemConditionChange(true);
		},

		unbind: function() {
			this.handleAddButtonClick(false);
			this.handleSortButtonClick(false);
			this.handleItemDeleteClick(false);
			this.handleItemConditionChange(false);
		},

		handleAddButtonClick: function(dir) {
			var addButton = this.getElement('addButton');

			addButton[dir ? 'on' : 'off']('click', $.proxy(this.onAddButtonClick, this));
		},

		handleSortButtonClick: function(dir) {
			var sortButtonSelector = this.getElementSelector('sortButton');

			this.$el[dir ? 'on' : 'off']('click', sortButtonSelector, $.proxy(this.onSortButtonClick, this));
		},

		handleItemDeleteClick: function(dir) {
			var itemDeleteSelector = this.getElementSelector('itemDelete');

			this.$el[dir ? 'on' : 'off']('click', itemDeleteSelector, $.proxy(this.onItemDeleteSelector, this));
		},

		handleItemConditionChange: function(dir) {
			this.$el[dir ? 'on' : 'off']('FieldConditionChange', $.proxy(this.onItemConditionChange, this));
		},

		onAddButtonClick: function(evt) {
			var newInstance = this.addItem();

			newInstance.initEdit();

			evt.preventDefault();
		},

		onSortButtonClick: function(evt) {
			var button = $(evt.target);

			this.sortByButton(button);

			evt.preventDefault();
		},

		onItemDeleteSelector: function(evt) {
			var button = $(evt.target);
			var item = this.getElement('item', button, 'closest');

			this.deleteItem(item);

			evt.preventDefault();
		},

		onItemConditionChange: function() {
			this.refreshCount();
		},

		refreshFillState: function() {
			var activeItemCollection = this.getElement('item').not('.is--hidden');
			var hasItems = (activeItemCollection.length > 0);

			this.updateFillState(hasItems);
		},

		updateFillState: function(hasItems) {
			this.$el.toggleClass('is--empty', !hasItems);
			this.$el.toggleClass('is--fill', hasItems);
		},

		addItem: function(source, context, method) {
			var result = this.callParent('addItem', [source, context, method], constructor);

			this.refreshFillState();

			return result;
		},

		deleteItem: function(item) {
			this.callParent('deleteItem', [item], constructor);
			this.refreshFillState();
			this.refreshCount();
		},

		appendItem: function(item, context, method) {
			var sortTemplate = this.getTemplate('sort');
			var sortElement = $(sortTemplate);

			context[method](sortElement);
			item.insertAfter(sortElement);
		},

		detachItem: function(item) {
			var sortElement = this.getElement('sort', item, 'prev');

			if (sortElement.length === 0) { // first delete
				sortElement = this.getElement('sort', item, 'next');
			}

			sortElement.detach();
			item.detach();
		},

		sortByButton: function(sortButton) {
			var sortElement = this.getElement('sort', sortButton, 'closest');
			var prevItem = this.getElement('item', sortElement, 'prev');
			var nextItem = this.getElement('item', sortElement, 'next');

			prevItem.detach();
			prevItem.insertAfter(sortElement);

			nextItem.detach();
			nextItem.insertBefore(sortElement);

			this.exchangeIndex(prevItem, nextItem);
			this.refreshCount();
		},

		updateCount: function(countList, warningList) {
			var baseName = this.getBaseName();
			var leftCount = countList && baseName in countList ? countList[baseName] : null;
			var warning = warningList && baseName in warningList ? warningList[baseName] : null;

			this.updateCountLeft(leftCount, warning);
			this.callItemList('updateCount', [countList, warningList]);
		},

		updateCountLeft: function(count, warning) {
			var countInteger = parseInt(count, 10);
			var countElement = this.getElement('leftCount');
			var countText;
			var messageElement = this.getElement('leftMessage');
			var messageTextElement = this.getElement('leftMessageText', messageElement);
			var messageText;

			if (!isNaN(countInteger)) {
				countText = this.getLang('LEFT_COUNT', {
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
					messageText = this.getLang('LEFT_COUNT_EMPTY');
				}
			} else {
				messageText = this.getLang('LEFT_COUNT_FAIL');
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
			this.progressCountLeft();
			this.callItemList('progressCount');
		},

		progressCountLeft: function() {
			var countElement = this.getElement('leftCount');
			var messageElement = this.getElement('leftMessage');
			var messageTextElement = this.getElement('leftMessageText', messageElement);
			var countText = this.getLang('LEFT_COUNT_PROGRESS');

			countElement.text(countText);
			countElement.removeClass('is--hidden');

			messageElement.addClass('is--hidden');
			messageTextElement.text('');
		},

		refreshCount: function() {
			this.progressCount();

			this.buildRefreshQuery().then(
				$.proxy(this.refreshCountEnd, this),
				$.proxy(this.refreshCountStop, this)
			);
		},

		refreshCountStop: function() {
			this.updateCount();
		},

		refreshCountEnd: function(data) {
			this.updateCount(data['countList'], data['warningList']);
		},

		buildRefreshQuery: function() {
			var config = {
				url: '',
				type: 'post',
				data: this.$el.closest('form').serializeArray(),
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

		getItemPlugin: function() {
			return Filter.Item;
		}

	}, {
		dataName: 'FieldFilterCollection'
	});

})(BX, jQuery, window);