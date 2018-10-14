function OnYandexMapSettingsEdit(arParams)
{
	if (null != window.jsYandexCEOpener)
	{
		try {window.jsYandexCEOpener.Close();}catch (e) {}
		window.jsYandexCEOpener = null;
	}

	window.jsYandexCEOpener = new JCEditorOpener(arParams);
}

function JCEditorOpener(arParams)
{
	this.arParams = arParams;
	this.jsOptions = this.arParams.data.split('||');

	var obButton = document.createElement('BUTTON');
	this.arParams.oCont.appendChild(obButton);
	
	obButton.innerHTML = this.jsOptions[1];
	obButton.onclick = BX.delegate(this.btnClick, this);
	this.saveData = BX.delegate(this.__saveData, this);
	
	BX.ready(BX.delegate(this.AppendTextToKey, this));
}

JCEditorOpener.prototype.AppendTextToKey = function()
{
	this.arElements = this.arParams.getElements();
	var obKey = this.arElements.KEY;
	
	if (!obKey.value)
	{
		obKey.parentNode.appendChild(document.createElement('BR'));
		obKey.parentNode.appendChild(BX.create('A', {
			attrs: {href: this.jsOptions[4], target: '_blank'},
			text: this.jsOptions[3]
		}));
	}
}

JCEditorOpener.prototype.Close = function(e)
{
	if (false !== e)
		BX.util.PreventDefault(e);

	if (null != window.jsPopup_yandex_map)
	{
		window.jsPopup_yandex_map.Close();
	}
}

JCEditorOpener.prototype.btnClick = function ()
{
	//this.arElements = this.arParams.getElements();
	if (!this.arElements)
		return false;

	var map_key = this.arElements.KEY.value;
	
	if (BX.util.trim(map_key) == '')
	{
		alert(this.jsOptions[2]);
		return false;
	}

	if (null == window.jsPopup_yandex_map)
	{
		var strUrl = '/bitrix/components/bitrix/map.yandex.view/settings/settings.php'
			+ '?lang=' + this.jsOptions[0]
			+ '&KEY=' + BX.util.urlencode(map_key)
			+ '&INIT_MAP_TYPE=' + BX.util.urlencode(this.arElements.INIT_MAP_TYPE.value),
		strUrlPost = 'MAP_DATA=' + BX.util.urlencode(this.arParams.oInput.value);
			
		window.jsPopup_yandex_map = new BX.CDialog({
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width':800, 'height':500, 
			'resizable':false
		});
	}
	
	window.jsPopup_yandex_map.Show();
	window.jsPopup_yandex_map.PARAMS.content_url = '';

	return false;
}

JCEditorOpener.prototype.__saveData = function(strData, view)
{
	this.arParams.oInput.value = strData;
	if (null != this.arParams.oInput.onchange)
		this.arParams.oInput.onchange();
	
	if (view)
	{
		this.arElements.INIT_MAP_TYPE.value = view;
		if (null != this.arElements.INIT_MAP_TYPE.onchange)
			this.arElements.INIT_MAP_TYPE.onchange();
	}
	
	this.Close(false);
}