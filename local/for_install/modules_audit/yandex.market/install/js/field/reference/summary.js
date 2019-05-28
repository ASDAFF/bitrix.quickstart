(function(BX, $, window) {

	var YandexMarket = BX.namespace('YandexMarket');
	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Reference = BX.namespace('YandexMarket.Field.Reference');

	var constructor = Reference.Summary = Reference.Base.extend({

		defaults: {
			modalElement: null,
			fieldElement: null,
			modalWidth: 830,
			modalHeight: 450
		},

		initVars: function() {
			this._modal = null;
		},

		initialize: function() {
			this.callParent('initialize', constructor);
			this.setFieldBaseName();
		},

		handleEditModal: function(modal, dir) {
			this.handleEditModalSave(modal, dir);
			this.handleEditModalClose(modal, dir);
		},

		handleEditModalSave: function(modal, dir) {
			BX[dir ? 'addCustomEvent' : 'removeCustomEvent'](modal, 'onWindowSave', BX.proxy(this.onEditModalSave, this));
		},

		handleEditModalClose: function(modal, dir) {
			BX[dir ? 'addCustomEvent' : 'removeCustomEvent'](modal, 'onWindowClose', BX.proxy(this.onEditModalClose, this));
		},

		onEditModalSave: function() {
			var modal = this.getModal();
			var modalContent = this.getModalContent();

			this.updateField(modalContent);
			this.refreshSummary();

			this.handleEditModal(modal, false);
			this.closeEditModal();
			this.destroyModal();
		},

		onEditModalClose: function() {
			var modal = this.getModal();

			if (modal) {
				this.handleEditModal(modal, false);
				this.destroyModal();
			}
		},

		cloneInstance: function(newInstance) {
			var fieldInstance = this.getFieldInstance();
			var newFieldInstance = newInstance.getFieldInstance();

			fieldInstance.cloneInstance(newFieldInstance);
			newFieldInstance.setParentField(newInstance);
		},

		initEdit: function() {
			this.openEditModal();

			return true;
		},

		clear: function() {
			this.callField('clear');
			this.refreshSummary();
		},

		setBaseName: function(baseName) {
			this.callParent('setBaseName', [baseName], constructor);
			this.setFieldBaseName();
		},

		setFieldBaseName: function() {
			var baseName = this.getBaseName();

			this.callField('setBaseName', [baseName]);
		},

		updateName: function() {
			this.callField('updateName');
		},

		unsetName: function() {
			this.callField('unsetName');
		},

		getDisplayValue: function() {
			this.callField('getDisplayValue');
		},

		updateField: function(modalContent) {
			var oldField = this.getElement('field');
			var newField = this.getElement('field', modalContent);

			newField.insertAfter(oldField);
			oldField.remove();

			newField.trigger('change'); // notify parents
		},

		refreshSummary: function() {
			// abstract
		},

		openEditModal: function(index) {
			var modal = this.getModal();
			var contents = this.getElement('modal');
			var contentsClone = contents.clone(false, false).removeClass('is--hidden');
			var fieldInstance = this.getFieldInstance();
			var fieldPlugin = this.getFieldPlugin();
			var cloneElement = this.getElement('field', contentsClone);
			var cloneInstance;
			var modalContent;

			// show modal

			modal.SetContent(contentsClone[0]);
			modal.Show();

			// initialize plugin

			modalContent = this.getModalContent();

			cloneInstance = fieldPlugin.getInstance(cloneElement);
			fieldInstance.cloneInstance(cloneInstance);
			cloneInstance.setParentField(this);
			cloneInstance.initEdit(index);

			Plugin.manager.initializeContext(modalContent);

			// handle

			this.handleEditModal(modal, true);

			return cloneInstance;
		},

		closeEditModal: function() {
			var modal = this.getModal();

			modal.Close();
		},

		getModal: function() {
			if (this._modal == null) {
				this._modal = this.createModal();
			}

			return this._modal;
		},

		getModalElement: function() {
			return this._modal && $(this._modal.DIV);
		},

		getModalContent: function() {
			return this._modal && $(this._modal.PARTS.CONTENT_DATA);
		},

		createModal: function() {
			var dialog = new YandexMarket.EditDialog({
				'title': this.getLang('MODAL_TITLE'),
				'draggable': true,
				'resizable': true,
				'buttons': [YandexMarket.EditDialog.btnSave, YandexMarket.EditDialog.btnCancel],
				'width': this.options.modalWidth,
				'height': this.options.modalHeight
			});

			return dialog;
		},

		destroyModal: function() {
			this._modal = null;
		},

		getFieldPlugin: function() {
			// abstract
		},

		getFieldInstance: function() {
			var plugin = this.getFieldPlugin();
			var field = this.getElement('field');

			return plugin.getInstance(field);
		},

		callField: function(method, args) {
			var instance = this.getFieldInstance();
			var result;

			if (typeof method === 'string') {
				if (args) {
					result = instance[method].apply(instance, args);
				} else {
					result = instance[method]();
				}
			} else {
				method(instance);
			}

			return result;
		}


	});

})(BX, jQuery, window);