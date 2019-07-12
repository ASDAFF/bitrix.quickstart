function OnCitrusSendEventSettingsEdit(arParams)
{
	if (null != window.jsCSESettingsOpener)
	{
		try {window.jsCSESettingsOpener.Close();}catch (e) {}
		window.jsCSESettingsOpener = null;
	}

	window.jsCSESettingsOpener = new JCEditorOpener(arParams);
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

	if (null != window.jsPopup_CitrusSendEventForm)
	{
		window.jsPopup_CitrusSendEventForm.Close();
	}
}

JCEditorOpener.prototype.btnClick = function ()
{
	this.arElements = this.arParams.getElements();
	if (!this.arElements)
		return false;

	if (window.jsPopup_CitrusSendEventForm)
		window.jsPopup_CitrusSendEventForm.DIV.parentNode.removeChild(window.jsPopup_CitrusSendEventForm.DIV);

	{
		var strUrl = '/bitrix/components/citrus/realty.send_event/settings/settings.php'
			+ '?lang=' + this.jsOptions[0]
			+ '&event=' + BX.util.urlencode(this.arElements.EVENT_TYPE.value);

		strUrlPost = 'DATA=' + BX.util.urlencode(this.arParams.oInput.value);

		window.jsPopup_CitrusSendEventForm = new BX.CDialog({
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width':800, 'height':500, 
			'resizable':false
		});
	}
	
	window.jsPopup_CitrusSendEventForm.Show();
	window.jsPopup_CitrusSendEventForm.PARAMS.content_url = '';
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