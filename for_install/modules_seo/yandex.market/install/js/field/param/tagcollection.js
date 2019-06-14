(function(BX, jQuery, window) {

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Param = BX.namespace('YandexMarket.Field.Param');
	var Source = BX.namespace('YandexMarket.Source');

	var constructor = Param.TagCollection = Reference.Collection.extend({

		defaults: {
			itemElement: '.js-param-tag-collection__item',
			itemAddHolderElement: '.js-param-tag-collection__item-add-holder',
			itemAddElement: '.js-param-tag-collection__item-add',
			itemDeleteElement: '.js-param-tag-collection__item-delete'
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
			var addButton = $(evt.currentTarget);
			var type = addButton.data('type');
			var typeCollection = this.getTypeCollection(type, true);
			var item = typeCollection.eq(-1);

			this.addItem(item);

			evt.preventDefault();
		},

		onItemDeleteClick: function(evt) {
			var deleteButton = $(evt.currentTarget);
			var item = this.getElement('item', deleteButton, 'closest');

			this.deleteItem(item);

			evt.preventDefault();
		},

		addItem: function(source, context, method) {
			var sourceItem = source || this.getElement('item').eq(-1);
			var isMultiple = sourceItem.data('multiple');
			var isRequired = sourceItem.data('required');
			var itemType = sourceItem.data('type');
			var typeCollection;
			var isPreventDefault = false;

			if (itemType) {
				typeCollection = this.getTypeCollection(itemType);

				if (!isMultiple && typeCollection.length > 0) {
					isPreventDefault = true;
				} else if (isRequired && typeCollection.length === 1) {
					this.toggleItemDeleteView(true, typeCollection);
				}

				if (!isMultiple && typeCollection.length === 0) { // can't add more
					this.toggleItemAddView(false, itemType);
					this.refreshItemAddHolderView();
				}
			}

			if (!isPreventDefault) {
				this.callParent('addItem', [sourceItem, context, method], constructor);
			}
		},

		deleteItem: function(item) {
			var isMultiple = item.data('multiple');
			var isRequired = item.data('required');
			var isPersistent = item.data('persistent');
			var itemType = item.data('type');
			var typeCollection;
			var isPreventDefault = false;

			if (itemType) {
				typeCollection = this.getTypeCollection(itemType);

				if ((isRequired || isPersistent) && typeCollection.length === 2) {
					this.toggleItemDeleteView(false, typeCollection);
				} else if (typeCollection.length === 1) {
					isPreventDefault = true;

					if (isRequired || isPersistent) {
						this.clearItem(item);
					} else {
						item.addClass('is--hidden');
						this.destroyItem(item, true);

						this.toggleItemAddView(true, itemType);
						this.refreshItemAddHolderView();
					}
				} else if (typeCollection.length === 0) {
					isPreventDefault = true;
				}
			}

			if (!isPreventDefault) {
				this.callParent('deleteItem', [item], constructor);
			}
		},

		refreshItemAddHolderView: function() {
			var itemAddList = this.getElement('itemAdd').not('.is--hidden');
			var isActive = (itemAddList.length > 0);
			var holder = this.getElement('itemAddHolder');

			holder.toggleClass('is--hidden', !isActive);
		},

		toggleItemAddView: function(dir, type) {
			var itemAddButton = this.getElement('itemAdd').filter(function(index, node) {
				return $(node).data('type') === type;
			});

			itemAddButton.toggleClass('is--hidden', !dir);
		},

		toggleItemDeleteView: function(dir, context) {
			this.getElement('itemDelete', context).toggleClass('is--hidden', !dir);
		},

		getTypeCollection: function(type, isIncludePlaceholder) {
			return this.getElement('item').filter(function(index, node) {
				var element = $(node);

				return (
					(element.data('type') === type)
					&& (isIncludePlaceholder || !element.hasClass('is--hidden'))
				);
			});
		},

		getItemPlugin: function() {
			return Param.Tag;
		}

	}, {
		dataName: 'FieldParamCollection'
	});

})(BX, jQuery, window);