function On2GisMapSettingsEdit(arParams)
{
	if( null != window.js2gisCEOpener )
	{
		try { window.js2gisCEOpener.Close(); } catch (e) {}
		window.js2gisCEOpener = null;
	}
	window.js2gisCEOpener = new JCEditorOpener( arParams );
}

function JCEditorOpener(arParams)
{
	this.arParams = arParams;
	this.jsOptions = this.arParams.data.split('||');
	// 'JS_DATA'  => LANGUAGE_ID.'||'.GetMessage('2GIS_PARAM_DATA_SET')
	// jsOptions[0] - language
	// jsOptions[1] - button text

	var obButton = this.arParams.oCont.appendChild( BX.create( 'BUTTON', {
		html: this.jsOptions[1]
	} ) );
	obButton.onclick = BX.delegate( this.btnClick, this );
	this.saveData = BX.delegate( this.__saveData, this );
}

JCEditorOpener.prototype.btnClick = function ()
{
	this.arElements = this.arParams.getElements();

	if (!this.arElements)
		return false;

	if (window.jsPopup_2gis_map == null)
	{
		var strUrl = '/bitrix/components/simai/maps.2gis.simple/settings/settings.php'
			+ '?lang=' + this.jsOptions[0];
		var strUrlPost = 'MAP_DATA=' + BX.util.urlencode( this.arParams.oInput.value );

		window.jsPopup_2gis_map = new BX.CDialog({
			'content_url' : strUrl,
			'content_post': strUrlPost,
			'width'       : 800,
			'height'      : 500,
			'resizable'   : false
		});
	}

	window.jsPopup_2gis_map.Show();
	window.jsPopup_2gis_map.PARAMS.content_url = '';

	return false;
}


JCEditorOpener.prototype.Close = function(e)
{
	if( false !== e ) BX.util.PreventDefault(e);
	if( null != window.jsPopup_2gis_map )
	{
		window.jsPopup_2gis_map.Close();
	}
}


JCEditorOpener.prototype.SaveData = function( strData )
{
	this.arParams.oInput.value = strData;
    // invoke onchange handler if any, on input
	if( null != this.arParams.oInput.onchange )
		this.arParams.oInput.onchange();
    // close dialog
	this.Close( false );
}
