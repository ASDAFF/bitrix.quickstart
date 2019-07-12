var authFormWindow = {

	form_window: null,
	overlay : null,
	form_window_id : "login-form-window",
	login_field_id : "auth-user-login",

	ShowLoginForm : function()
	{
		if (!this.form_window)
		{
			this.form_window = document.getElementById(this.form_window_id);
			if (!this.form_window)
				return false;

			try {document.body.appendChild(this.form_window);}
			catch (e){}
		}

		this.form_window.style.display = "block";

		if (this.GetOpacityProperty())
			this.CreateOverlay();

		var loginField = document.getElementById(this.login_field_id);
		if (loginField)
		{
			loginField.focus();
			loginField.select();
		}
		return false;
	},

	CloseLoginForm : function()
	{
		if (this.form_window)
			this.form_window.style.display = "none";

		if (this.overlay)
			this.overlay.style.display = "none";

		return false;
	},

	CreateOverlay : function()
	{
		if (!this.overlay)
		{
			this.overlay = document.body.appendChild(document.createElement("DIV"));
			this.overlay.className = "login-form-overlay";

			var _this = this;
			this.overlay.onclick = function() {_this.CloseLoginForm()};
		}

		var windowSize = this.GetWindowScrollSize();

		this.overlay.style.width = windowSize.scrollWidth + "px";
		this.overlay.style.height = windowSize.scrollHeight + "px";
		this.overlay.style.display = "block";
	},

	GetOpacityProperty : function()
	{
		if (typeof document.body.style.opacity == 'string')
			return 'opacity';
		else if (typeof document.body.style.MozOpacity == 'string')
			return 'MozOpacity';
		else if (typeof document.body.style.KhtmlOpacity == 'string')
			return 'KhtmlOpacity';
		else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5)
			return 'filter';

		return false;
	},

	GetWindowScrollSize : function(pDoc)
	{
		var width, height;
		if (!pDoc)
			pDoc = document;

		if ( (pDoc.compatMode && pDoc.compatMode == "CSS1Compat"))
		{
			width = pDoc.documentElement.scrollWidth;
			height = pDoc.documentElement.scrollHeight;
		}
		else
		{
			if (pDoc.body.scrollHeight > pDoc.body.offsetHeight)
				height = pDoc.body.scrollHeight;
			else
				height = pDoc.body.offsetHeight;

			if (pDoc.body.scrollWidth > pDoc.body.offsetWidth || 
				(pDoc.compatMode && pDoc.compatMode == "BackCompat") ||
				(pDoc.documentElement && !pDoc.documentElement.clientWidth)
			)
				width = pDoc.body.scrollWidth;
			else
				width = pDoc.body.offsetWidth;
		}
		return {scrollWidth : width, scrollHeight : height};
	}
}

var authPreloadImages = ["close.gif", "login-form-header-bg.gif"];
for (var imageIndex = 0; imageIndex < authPreloadImages.length; imageIndex++)
{
	var imageObj = new Image();
	imageObj.src = "/bitrix/templates/modern/components/bitrix/system.auth.form/auth/images/" + authPreloadImages[imageIndex];
}
authPreloadImages = null;