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
		blog: false
	};

    this.settings = {
		blog: {
			ajaxUrl: '',
			ajaxParams: {},
			contID: 'bx-cat-soc-comments-blg'
		}
	};

    this.services = {
		blog: {
			obBlogCont: null
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

	} else {
		this.errorCode = -1;
	}

    if (this.errorCode === 0)
	{
		BX.ready(BX.delegate(this.Init, this));
	}
}

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

	if (this.errorCode === 0)
	{
		if (this.serviceList.blog)
		{
			this.loadBlog();
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


})(window);
