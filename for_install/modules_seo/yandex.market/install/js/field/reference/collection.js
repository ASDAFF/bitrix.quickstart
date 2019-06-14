(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Reference = BX.namespace('YandexMarket.Field.Reference');

	var constructor = Reference.Collection = Reference.Base.extend({

		defaults: {
			persistent: false,
			maxLength: null,

			itemElement: null,

			langPrefix: null,
			lang: {}
		},

		initialize: function() {
			this.callParent('initialize', constructor);
			this.initializeIndexList();
			this.setBaseNameList();
		},

		cloneInstance: function(newInstance) {
			var baseName = this.getBaseName();
			var newItems = newInstance.getElement('item');
			var newItemIndex = 0;

			newInstance.setBaseName(baseName);

			this.callItemList(function(itemInstance) {
				var newItem = newItems.eq(newItemIndex);
				var newItemInstance = newInstance.getItemInstance(newItem);

				itemInstance.cloneInstance(newItemInstance);
				newItemInstance.setParentField(newInstance);

				newItemIndex++;
			});
		},

		clear: function() {
			var itemCollection = this.getElement('item');
			var item;
			var itemIndex;

			for (itemIndex = itemCollection.length - 1; itemIndex >= 0; itemIndex--) {
				item = itemCollection.eq(itemIndex);

				this.deleteItem(item);
			}
		},

		clearItem: function(item) {
			var instance = this.getItemInstance(item);

			instance.clear();
		},

		initEdit: function(index) {
			var item;
			var instance;

			if (index != null) {
				item = this.getItem(index);
				instance = this.getItemInstance(item);
			} else {
				item = this.getElement('item').eq(0);

				if (item.hasClass('is--hidden')) {
					instance = this.addItem();
				} else {
					instance = this.getItemInstance(item);
				}
			}

			return instance.initEdit();
		},

		initializeIndexList: function() {
			var index = 0;
			var _this = this;

			this.callItemList(function(instance) {
				instance.setIndex(index);
				instance.setParentField(_this);
				index++;
			});
		},

		setBaseName: function(baseName) {
			this.callParent('setBaseName', [baseName], constructor);
			this.setBaseNameList();
		},

		setBaseNameList: function() {
			var baseName = this.getBaseName();

			this.callItemList(function(instance) {
				var index = instance.getIndex();
				var itemName = baseName + '[' + index + ']';

				instance.setBaseName(itemName);
			});
		},

		updateName: function() {
			this.callItemList('updateName');
		},

		unsetName: function() {
			this.callItemList('unsetName');
		},

		getValue: function() {
			var result = [];
			var itemCollection = this.getElement('item');
			var item;
			var itemIndex;
			var itemInstance;
			var i;

			for (i = 0; i < itemCollection.length; i++) {
				item = itemCollection.eq(i);
				itemInstance = this.getItemInstance(item);
				itemIndex = itemInstance.getIndex();

				result[itemIndex] = itemInstance.getValue();
			}

			return result;
		},

		setValue: function(valueList) {
			var result = [];
			var itemCollection = this.getElement('item');
			var item;
			var itemIndex;
			var itemInstance;
			var itemValue;
			var i;

			for (i = 0; i < itemCollection.length; i++) {
				item = itemCollection.eq(i);
				itemInstance = this.getItemInstance(item);
				itemIndex = itemInstance.getIndex();
				itemValue = valueList[itemIndex];

				itemInstance.setValue(itemValue)
			}

			return result;
		},

		getDisplayValue: function() {
			var result = [];
			var itemCollection = this.getElement('item');
			var item;
			var itemIndex;
			var itemInstance;
			var i;

			for (i = 0; i < itemCollection.length; i++) {
				item = itemCollection.eq(i);
				itemInstance = this.getItemInstance(item);
				itemIndex = itemInstance.getIndex();

				result[itemIndex] = itemInstance.getDisplayValue();
			}

			return result;
		},

		getItemPlugin: function() {
			// abstract
		},

		getItemInstance: function(item, isDisableCreate) {
			var plugin = this.getItemPlugin();

			return plugin.getInstance(item, isDisableCreate);
		},

		appendItem: function(item, context, method) {
			context[method](item);
			Plugin.manager.initializeContext(item);
		},

		detachItem: function(item) {
			Plugin.manager.destroyContext(item);
			item.detach();
		},

		getItem: function(targetIndex) {
			var itemCollection = this.getElement('item');
			var item;
			var itemInstance;
			var itemIndex;
			var i;
			var result;

			for (i = 0; i < itemCollection.length; i++) {
				item = itemCollection.eq(i);
				itemInstance = this.getItemInstance(item);
				itemIndex = itemInstance.getIndex();

				if (itemIndex === targetIndex) {
					result = item;
				}
			}

			return result;
		},

		addItem: function(source, context, method) {
			var itemCollection = this.getElement('item');
			var activeCollection = itemCollection.not('.is--hidden');
			var activeCollectionLength = activeCollection.length;
			var sourceItem = source || itemCollection.eq(-1);
			var appendContext = context || activeCollection.eq(-1);
			var appendInstance;
			var appendMethod = method || 'after';
			var newItem;
			var newItemIndex;
			var newInstance;

			if (this.options.maxLength !== null && activeCollectionLength >= this.options.maxLength) {
				this.notify('ADD_LIMIT_REACHED');
				return null;
			}

			if (sourceItem.hasClass('is--hidden')) { // is placeholder
				newItem = sourceItem;
				newItem.removeClass('is--hidden');
			} else {
				newItem = sourceItem.clone(false, false);
			}

			if (appendContext.length > 0 && appendContext[0] !== newItem[0]) {
				appendInstance = this.getItemInstance(appendContext);
				newItemIndex = appendInstance.getIndex() + (appendMethod === 'after' ? 1 : -1);

				this.appendItem(newItem, appendContext, appendMethod, activeCollectionLength);
			} else {
				newItemIndex = activeCollectionLength;
			}

			this.spliceIndex(newItemIndex);

			newInstance = this.initializeItem(newItem, newItemIndex);

			return newInstance;
		},

		deleteItem: function(item) {
			var itemCollection = this.getElement('item');
			var itemCollectionLength = itemCollection.length;

			if (itemCollectionLength > 1) {
				this.destroyItem(item);
				this.detachItem(item, itemCollectionLength);
			} else if (this.options.persistent) {
				this.clearItem(item);
			} else {
				item.addClass('is--hidden');
				this.destroyItem(item, true);
			}
		},

		initializeItem: function(item, index) {
			var instance = this.getItemInstance(item);
			var baseName = this.getBaseName();
			var fullName = baseName + '[' + index + ']';

			Plugin.manager.initializeContext(item);

			instance.clear();
			instance.setParentField(this);
			instance.setIndex(index);
			instance.setBaseName(fullName);
			instance.updateName();

			return instance;
		},

		destroyItem: function(item, isPlaceholder) {
			var instance = this.getItemInstance(item, true);
			var index;

			if (instance) {
				index = instance.getIndex();

				if (isPlaceholder) {
					instance.clear();
					instance.unsetName();
				}

				instance.setParentField(null);
				instance.destroy();

				this.sliceIndex(index);
			}
		},

		exchangeIndex: function(prevItem, nextItem) {
			var baseName = this.getBaseName();
			var prevInstance = this.getItemInstance(prevItem);
			var prevIndex = prevInstance.getIndex();
			var prevFullName = baseName + '[' + prevIndex + ']';
 			var nextInstance = this.getItemInstance(nextItem);
			var nextIndex = nextInstance.getIndex();
			var nextFullName = baseName + '[' + nextIndex + ']';

			prevInstance.setIndex(nextIndex);
			prevInstance.setBaseName(nextFullName);
			prevInstance.updateName();

			nextInstance.setIndex(prevIndex);
			nextInstance.setBaseName(prevFullName);
			nextInstance.updateName();
		},

		spliceIndex: function(index) {
			var lastIndex = index;
			var baseName = this.getBaseName();

			this.callItemList(function(instance) {
				var itemIndex = instance.getIndex();
				var newItemIndex;
				var newItemFullName;

				if (itemIndex === lastIndex) {
					newItemIndex = itemIndex + 1;
					newItemFullName = baseName + '[' + newItemIndex + ']';

					instance.setIndex(newItemIndex);
					instance.setBaseName(newItemFullName);
					instance.updateName();

					lastIndex = newItemIndex;
				}
			});
		},

		sliceIndex: function(index) {
			var lastIndex = index + 1;
			var baseName = this.getBaseName();

			this.callItemList(function(instance) {
				var itemIndex = instance.getIndex();
				var newItemIndex;
				var newItemFullName;

				if (itemIndex === lastIndex) {
					newItemIndex = itemIndex - 1;
					newItemFullName = baseName + '[' + newItemIndex + ']';

					instance.setIndex(newItemIndex);
					instance.setBaseName(newItemFullName);
					instance.updateName();

					lastIndex = lastIndex + 1;
				}
			});
		},

		callItemList: function(method, args) {
			var elementList = this.getElement('item');
			var element;
			var i;

			for (i = 0; i < elementList.length; i++) {
				element = elementList.eq(i);

				if (!element.hasClass('is--hidden')) { // is not placeholder
					this.callItem(element, method, args);
				}
			}
		},

		callItem: function(element, method, args) {
			var instance = this.getItemInstance(element);

			if (typeof method === 'string') {
				instance[method].apply(instance, args);
			} else {
				method(instance);
			}
		},

		notify: function(lang) {
			var message = this.getLang(lang);

			if (message != null) {
				alert(message);
			}
		}

	});

})(BX, jQuery, window);
