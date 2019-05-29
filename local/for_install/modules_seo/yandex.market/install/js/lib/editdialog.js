(function(BX, window) {

	var YandexMarket = BX.namespace('YandexMarket');

	// constructor

	YandexMarket.EditDialog = function(arParams) {
		YandexMarket.EditDialog.superclass.constructor.apply(this, arguments);
	};

	BX.extend(YandexMarket.EditDialog, BX.CAdminDialog);

	// prototype
	
	YandexMarket.EditDialog.prototype.Submit = function() {
		BX.onCustomEvent(this, 'onWindowSave', [this]);
	};

	// buttons
	
	YandexMarket.EditDialog.prototype.btnSave = YandexMarket.EditDialog.btnSave = {
		title: BX.message('JS_CORE_WINDOW_SAVE'),
		id: 'savebtn',
		name: 'savebtn',
		className: 'adm-btn-save',
		action: function () {
			this.disableUntilError();
			this.parentWindow.Submit();
		}
	};
	
	YandexMarket.EditDialog.btnCancel = YandexMarket.EditDialog.superclass.btnCancel;
	YandexMarket.EditDialog.btnClose = YandexMarket.EditDialog.superclass.btnClose;

})(BX, window);