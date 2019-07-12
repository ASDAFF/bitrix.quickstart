function OnCitrusIBlockElementFormSettingsEdit(arParams)
{
	if (null != window.jsCIEESettingsOpener)
	{
		try {window.jsCIEESettingsOpener.Close();}catch (e) {}
		window.jsCIEESettingsOpener = null;
	}

	window.jsCIEESettingsOpener = new JCEditorOpener(arParams);
}

function JCEditorOpener(arParams)
{
	this.jsOptions = arParams.data.split('||');
	this.arParams = arParams;

	var obButton = document.createElement('BUTTON');
	this.arParams.oCont.appendChild(obButton);
	
	obButton.innerHTML = this.jsOptions[1];
	
	obButton.onclick = BX.delegate(this.btnClick, this);
	this.saveData = BX.delegate(this.__saveData, this);
}

JCEditorOpener.prototype.Close = function(e)
{
	if (false !== e)
		BX.PreventDefault(e);

	if (null != window.jsPopup_CitrusIBlockElementForm)
	{
		window.jsPopup_CitrusIBlockElementForm.Close();
	}
}

JCEditorOpener.prototype.btnClick = function ()
{
	this.arElements = this.arParams.getElements();
	if (!this.arElements)
		return false;

	if (null == window.jsPopup_CitrusIBlockElementForm)
	{
		var strUrl = '/bitrix/components/citrus/iblock.element.form/settings/settings.php'
			+ '?lang=' + this.jsOptions[0]
			+ '&iblockID=' + BX.util.urlencode(this.arElements.IBLOCK_ID.value);

		strUrlPost = 'DATA=' + BX.util.urlencode(this.arParams.oInput.value);

		window.jsPopup_CitrusIBlockElementForm = new BX.CDialog({
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width':800, 'height':500, 
			'resizable':false
		});
	}
	
	window.jsPopup_CitrusIBlockElementForm.Show();
	window.jsPopup_CitrusIBlockElementForm.PARAMS.content_url = '';
	return false;
}

JCEditorOpener.prototype.__saveData = function(strData)
{
	this.arParams.oInput.value = strData;
	if (null != this.arParams.oInput.onchange)
		this.arParams.oInput.onchange();
	
/*

	// change other fields!
	this.arElements.INIT_MAP_TYPE.value = view;
	if (null != this.arElements.INIT_MAP_TYPE.onchange)
		this.arElements.INIT_MAP_TYPE.onchange();

*/
	
	this.Close(false);
}