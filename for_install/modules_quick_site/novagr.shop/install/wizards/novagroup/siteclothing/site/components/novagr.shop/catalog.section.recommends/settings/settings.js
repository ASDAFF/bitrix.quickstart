function OnNovagrShopCSRListSettingsEdit(arParams)
{
	if (null != window.jsNovagrShopCSRCEOpener)
	{
		try {window.jsNovagrShopCSRCEOpener.Close();}catch (e) {}
		window.jsNovagrShopCSRCEOpener = null;
	}

	window.jsNovagrShopCSRCEOpener = new JCEditorOpener(arParams);
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

	if (null != window.jsPopup_novagr_shop_csr)
	{
		window.jsPopup_novagr_shop_csr.Close();
	}
}

JCEditorOpener.prototype.btnClick = function ()
{
	this.arElements = this.arParams.getElements();
	if (!this.arElements)
		return false;

	if (null == window.jsPopup_novagr_shop_csr)
	{
		var strUrl = '/local/components/novagr.shop/catalog.section.recommends/settings/settings.php'
			+ '?lang=' + this.jsOptions[0];
		
		strUrlPost = 'MAP_DATA=' + BX.util.urlencode(this.arParams.oInput.value);

		window.jsPopup_novagr_shop_csr = new BX.CDialog({
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width':800, 'height':500,
			'resizable':false
		});
	}
	
	window.jsPopup_novagr_shop_csr.Show();
	window.jsPopup_novagr_shop_csr.PARAMS.content_url = '';
	return false;
}

JCEditorOpener.prototype.__saveData = function(strData)
{
    console.log(strData);
	this.arParams.oInput.value = strData;
	if (null != this.arParams.oInput.onchange)
		this.arParams.oInput.onchange();
	
	this.Close(false);
}