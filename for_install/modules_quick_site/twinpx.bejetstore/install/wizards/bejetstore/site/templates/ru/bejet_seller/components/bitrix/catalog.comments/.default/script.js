;(function(window) {
if (!!window.JCCatalogSocnetsComments)
{
	return;
}

window.JCCatalogSocnetsComments = function(arParams)
{
	var i;

	this.errorCode = 0;

	this.params = {};

	this.serviceList = {
		blog: false,
		facebook: false,
		vk: false
	};
	this.settings = {
		blog: {
			ajaxUrl: '',
			ajaxParams: {},
			contID: 'bx-cat-soc-comments-blg'
		},
		facebook: {
			contID: 'bx-cat-soc-comments-fb',
			contWidthID: '',
			parentContID: 'soc_comments',
			facebookJSDK: 'facebook-jssdk',
			facebookPath: ''
		},
		vk: {}
	};

	this.services = {
		blog: {
			obBlogCont: null
		},
		facebook: {
			obFBCont: null,
			obFBContWidth: null,
			obFBParentCont: null,
			obFBjSDK: null,
			currentWidth: 0
		}
	};

	if ('object' === typeof arParams)
	{
		this.params = arParams;
		if (!!this.params.serviceList && typeof(this.params.serviceList) === 'object')
		{
			for (i in this.serviceList)
			{
				if (this.serviceList.hasOwnProperty(i) && !!this.params.serviceList[i])
				{
					this.serviceList[i] = true;
				}
			}
		}
		if (this.serviceList.blog)
		{
			this.initParams('blog');
		}
		if (this.serviceList.facebook)
		{
			this.initParams('facebook');
		}
	}
	else
	{
		this.errorCode = -1;
	}

	if (this.errorCode === 0)
	{
		BX.ready(BX.delegate(this.Init, this));
	}
};

window.JCCatalogSocnetsComments.prototype.initParams = function(id)
{
	var i;

	if (!!this.params.settings && typeof(this.params.settings) === 'object' && typeof(this.params.settings[id]) === 'object')
	{
		for (i in this.settings[id])
		{
			if (this.settings[id].hasOwnProperty(i) && !!this.params.settings[id][i])
			{
				this.settings[id][i] = this.params.settings[id][i];
			}
		}
	}
};

window.JCCatalogSocnetsComments.prototype.Init = function()
{
	if (this.serviceList.blog)
	{
		this.services.blog.obBlogCont = BX(this.settings.blog.contID);
		if (!this.services.blog.obBlogCont)
		{
			this.serviceList.blog = false;
			this.errorCode = -16;
		}
	}
	if (this.serviceList.facebook)
	{
		this.services.facebook.obFBCont = BX(this.settings.facebook.contID);
		if (!this.services.facebook.obFBCont)
		{
			this.serviceList.facebook = false;
			this.errorCode = -32;
		}
		else
		{
			this.services.facebook.obFBContWidth = this.services.facebook.obFBCont.firstChild;
		}
		this.services.facebook.obFBParentCont = BX(this.settings.facebook.parentContID);
	}

	if (this.errorCode === 0)
	{
		if (this.serviceList.blog)
		{
			this.loadBlog();
		}
		if (this.serviceList.facebook)
		{
			this.loadFB();
		}
	}

	this.params = {};
};

window.JCCatalogSocnetsComments.prototype.loadBlog = function()
{
	var postData;

	if (this.errorCode !== 0 || !this.serviceList.blog || this.settings.blog.ajaxUrl.length === 0)
	{
		return;
	}

	postData = this.settings.blog.ajaxParams;
	postData.sessid = BX.bitrix_sessid();
	BX.ajax({
		timeout:   30,
		method:   'POST',
		dataType: 'html',
		url:       this.settings.blog.ajaxUrl,
		data:      postData,
		onsuccess: BX.proxy(this.loadBlogResult, this)
	});
};

window.JCCatalogSocnetsComments.prototype.loadBlogResult = function(result)
{
	if (BX.type.isNotEmptyString(result))
	{
		BX.adjust(this.services.blog.obBlogCont, { html: result });
	}
};


window.JCCatalogSocnetsComments.prototype.loadFB = function()
{
	var width;

	if (this.services.facebook.obFBParentCont && this.services.facebook.obFBContWidth)
	{
		width = parseInt(this.services.facebook.obFBParentCont.offsetWidth, 10);
		if (!isNaN(width))
		{
			BX.adjust(this.services.facebook.obFBContWidth, { attrs: { 'data-width': (width-20) } });
			this.services.facebook.currentWidth = width;
		}

		if (!this.services.facebook.obFBjSDK)
		{
			this.services.facebook.obFBjSDK = true;
			BX.defer(BX.proxy((function(d, s, id, fbpath) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id))
				{
					return;
				}
				js = d.createElement(s); js.id = id;
				js.src = fbpath;
				fjs.parentNode.insertBefore(js, fjs);
			}(document, "script", this.settings.facebook.facebookJSDK, this.settings.facebook.facebookPath)), this));
		}
	}
};

window.JCCatalogSocnetsComments.prototype.getFBParentWidth = function()
{
	var width = 0;
	if (!!this.services.facebook.obFBParentCont)
	{
		width = parseInt(this.services.facebook.obFBParentCont.offsetWidth, 10);
		if (isNaN(width))
		{
			width = 0;
		}
	}
	return width;
};

window.JCCatalogSocnetsComments.prototype.setFBWidth = function(width)
{
	var obFrame = null,
		src,
		newSrc;

	if (
		this.serviceList.facebook &&
		this.services.facebook.currentWidth !== width &&
		width > 20 &&
		!!this.services.facebook.obFBContWidth
	)
	{
		if (!!this.services.facebook.obFBContWidth.firstChild && !!this.services.facebook.obFBContWidth.firstChild.fitrstChild)
		{
			obFrame = this.services.facebook.obFBContWidth.firstChild.fitrstChild;
			if (!!obFrame)
			{
				src = obFrame.getAttribute("src");
				newSrc = src.replace(/width=(\d+)/ig, "width="+width);
				BX.adjust(this.services.facebook.obFBContWidth, { attrs: { 'data-width': (width-20) } });
				this.services.facebook.currentWidth = width;
				BX.style(this.services.facebook.obFBContWidth.firstChild, 'width', width+'px');
				BX.adjust(obFrame, { attrs : { src: newSrc }, style: { width: width+'px' } });
			}
		}
	}
};

window.JCCatalogSocnetsComments.prototype.onResize = function()
{
	if (this.serviceList.facebook)
	{
		this.setFBWidth(this.getFBParentWidth());
	}
};
})(window);