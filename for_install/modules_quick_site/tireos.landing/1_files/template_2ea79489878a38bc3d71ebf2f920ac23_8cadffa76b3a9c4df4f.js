
; /* Start:/bitrix/templates/marketplace-1c-v3/components/bitrix/system.auth.form/hd_loginform/script.js*/
var authFormWindow = {

	form_window: null,
	overlay : null,
	form_window_id : "login-form-window",
	login_field_id : "auth-user-login",

	menuFocus: false,

	UserMenuDisplay: function()
	{
		var mn = BX('top-menu');
		if(mn){mn.style.zIndex = 5;}

		var tObject = BX('top-site-menu');
		if(tObject)
		{
			if(tObject.className=='top-site-menu')
			{
				tObject.className = 'top-site-menu-active';
				tObject.style.zIndex = 100;

				BX.unbindAll(tObject);

				BX.bind(tObject, 'mouseover', function(){
					tObject.className = 'top-site-menu-active';
					window.clearTimeout(authFormWindow.menuFocus);
				});

				BX.bind(tObject, 'mouseout', function(){
					authFormWindow.menuFocus = window.setTimeout(function(){BX.unbindAll(tObject); tObject.className = 'top-site-menu'; tObject.style.zIndex = 0; if(mn){mn.style.zIndex = 15;} }, 200);
				});
			}
		}
	},

	UserMenuShow: function(obj, mode)
	{
		var blockObject = obj.parentNode.parentNode.parentNode;
		var clsName = '';
		if(mode==1)
			clsName  = 'user-auth-block';
		else if (mode==2)
			clsName  = 'user-auth-block-small';

		if(clsName !='')
		{
			blockObject.className = clsName;
			BX.unbindAll(blockObject);

			BX.bind(blockObject, 'mouseover', function(){
				blockObject.className = clsName;
				window.clearTimeout(authFormWindow.menuFocus);
			})
			BX.bind(blockObject, 'mouseout', function(){
				authFormWindow.menuFocus = window.setTimeout(function(){BX.unbindAll(blockObject); blockObject.className = '';}, 200);
			});
		}

	},


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

function systemAuthFormComponent_logout()
{
	BX('auth-logout-form').submit();
	return false;
}

function systemAuthFormComponent_openBlock()
{
	var loginForm = BX('hd_loginform_container');
	if (loginForm.style.display == 'block')
		BX.hide(loginForm);
	else
	{
		BX.show(loginForm);

		BX.focus(BX('USER_LOGIN_INPUT'));

		BX.bind(document, 'keyup', function(e){
			if (loginForm.style.display == 'block')
			{
				e=e||window.event;
				switch(e.keyCode)
				{
					case 27: systemAuthFormComponent_closeBlock();
						break;
				}
			}
		})
	}

	return false;
}

function systemAuthFormComponent_closeBlock()
{
	BX.hide(BX('hd_loginform_container'));
}

var authPreloadImages = ["rt_round_menu.png","rb_round_menu.png","lt_round_menu.png","lb_round_menu.png","l_menu.png","t_menu.png","r_menu.png","b_menu.png", "close.gif", "auth-form-bg.gif", "avatar.png", "exit_hover.gif","exit.gif"];
for (var imageIndex = 0; imageIndex < authPreloadImages.length; imageIndex++)
{
	var imageObj = new Image();
	imageObj.src = "/bitrix/templates/.default/components/bitrix/system.auth.form/drop_auth/images/" + authPreloadImages[imageIndex];
}
authPreloadImages = null;

BX.ready(function(){
	BX.bind(BX('authlogin'), 'mouseover', function () {BX.show(BX('authlogin_menu'))});
	BX.bind(BX('authlogin'), 'mouseout', function () {BX.hide(BX('authlogin_menu'))});
})
/* End */
;
; /* Start:/bitrix/components/bitrix/search.title/script.js*/
function JCTitleSearch(arParams)
{
	var _this = this;

	this.arParams = {
		'AJAX_PAGE': arParams.AJAX_PAGE,
		'CONTAINER_ID': arParams.CONTAINER_ID,
		'INPUT_ID': arParams.INPUT_ID,
		'MIN_QUERY_LEN': parseInt(arParams.MIN_QUERY_LEN)
	};
	if(arParams.WAIT_IMAGE)
		this.arParams.WAIT_IMAGE = arParams.WAIT_IMAGE;
	if(arParams.MIN_QUERY_LEN <= 0)
		arParams.MIN_QUERY_LEN = 1;

	this.cache = [];
	this.cache_key = null;

	this.startText = '';
	this.currentRow = -1;
	this.RESULT = null;
	this.CONTAINER = null;
	this.INPUT = null;
	this.WAIT = null;

	this.ShowResult = function(result)
	{
		var pos = BX.pos(_this.CONTAINER);
		pos.width = pos.right - pos.left;
		_this.RESULT.style.position = 'absolute';
		_this.RESULT.style.top = (pos.bottom + 2) + 'px';
		_this.RESULT.style.left = pos.left + 'px';
		_this.RESULT.style.width = pos.width + 'px';
		if(result != null)
			_this.RESULT.innerHTML = result;

		if(_this.RESULT.innerHTML.length > 0)
			_this.RESULT.style.display = 'block';
		else
			_this.RESULT.style.display = 'none';

		//ajust left column to be an outline
		var th;
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl) th = BX.findChild(tbl, {'tag':'th'}, true);
		if(th)
		{
			var tbl_pos = BX.pos(tbl);
			tbl_pos.width = tbl_pos.right - tbl_pos.left;

			var th_pos = BX.pos(th);
			th_pos.width = th_pos.right - th_pos.left;
			th.style.width = th_pos.width + 'px';

			_this.RESULT.style.width = (pos.width + th_pos.width) + 'px';

			//Move table to left by width of the first column
			_this.RESULT.style.left = (pos.left - th_pos.width - 1)+ 'px';

			//Shrink table when it's too wide
			if((tbl_pos.width - th_pos.width) > pos.width)
				_this.RESULT.style.width = (pos.width + th_pos.width -1) + 'px';

			//Check if table is too wide and shrink result div to it's width
			tbl_pos = BX.pos(tbl);
			var res_pos = BX.pos(_this.RESULT);
			if(res_pos.right > tbl_pos.right)
			{
				_this.RESULT.style.width = (tbl_pos.right - tbl_pos.left) + 'px';
			}
		}

		var fade;
		if(tbl) fade = BX.findChild(_this.RESULT, {'class':'title-search-fader'}, true);
		if(fade && th)
		{
			res_pos = BX.pos(_this.RESULT);
			fade.style.left = (res_pos.right - res_pos.left - 18) + 'px';
			fade.style.width = 18 + 'px';
			fade.style.top = 0 + 'px';
			fade.style.height = (res_pos.bottom - res_pos.top) + 'px';
			fade.style.display = 'block';
		}
	}

	this.onKeyPress = function(keyCode)
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(!tbl)
			return false;

		var cnt = tbl.rows.length;

		switch (keyCode)
		{
		case 27: // escape key - close search div
			_this.RESULT.style.display = 'none';
			_this.currentRow = -1;
			_this.UnSelectAll();
		return true;

		case 40: // down key - navigate down on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var first = -1;
			for(var i = 0; i < cnt; i++)
			{
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					if(first == -1)
						first = i;

					if(_this.currentRow < i)
					{
						_this.currentRow = i;
						break;
					}
					else if(tbl.rows[i].className == 'title-search-selected')
					{
						tbl.rows[i].className = '';
					}
				}
			}

			if(i == cnt && _this.currentRow != i)
				_this.currentRow = first;

			tbl.rows[_this.currentRow].className = 'title-search-selected';
		return true;

		case 38: // up key - navigate up on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var last = -1;
			for(var i = cnt-1; i >= 0; i--)
			{
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					if(last == -1)
						last = i;

					if(_this.currentRow > i)
					{
						_this.currentRow = i;
						break;
					}
					else if(tbl.rows[i].className == 'title-search-selected')
					{
						tbl.rows[i].className = '';
					}
				}
			}

			if(i < 0 && _this.currentRow != i)
				_this.currentRow = last;

			tbl.rows[_this.currentRow].className = 'title-search-selected';
		return true;

		case 13: // enter key - choose current search result
			if(_this.RESULT.style.display == 'block')
			{
				for(var i = 0; i < cnt; i++)
				{
					if(_this.currentRow == i)
					{
						if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
						{
							var a = BX.findChild(tbl.rows[i], {'tag':'a'}, true);
							if(a)
							{
								window.location = a.href;
								return true;
							}
						}
					}
				}
			}
		return false;
		}

		return false;
	}

	this.onTimeout = function()
	{
		_this.onChange(function(){
			setTimeout(_this.onTimeout, 500);
		});
	}

	this.onChange = function(callback)
	{
		if(_this.INPUT.value != _this.oldValue && _this.INPUT.value != _this.startText)
		{
			_this.oldValue = _this.INPUT.value;
			if(_this.INPUT.value.length >= _this.arParams.MIN_QUERY_LEN)
			{
				_this.cache_key = _this.arParams.INPUT_ID + '|' + _this.INPUT.value;
				if(_this.cache[_this.cache_key] == null)
				{
					if(_this.WAIT)
					{
						var pos = BX.pos(_this.INPUT);
						var height = (pos.bottom - pos.top)-2;
						_this.WAIT.style.top = (pos.top+1) + 'px';
						_this.WAIT.style.height = height + 'px';
						_this.WAIT.style.width = height + 'px';
						_this.WAIT.style.left = (pos.right - height + 2) + 'px';
						_this.WAIT.style.display = 'block';
					}

					BX.ajax.post(
						_this.arParams.AJAX_PAGE,
						{
							'ajax_call':'y',
							'INPUT_ID':_this.arParams.INPUT_ID,
							'q':_this.INPUT.value,
							'l':_this.arParams.MIN_QUERY_LEN
						},
						function(result)
						{
							_this.cache[_this.cache_key] = result;
							_this.ShowResult(result);
							_this.currentRow = -1;
							_this.EnableMouseEvents();
							if(_this.WAIT)
								_this.WAIT.style.display = 'none';
							if (!!callback)
								callback();
						}
					);
					return;
				}
				else
				{
					_this.ShowResult(_this.cache[_this.cache_key]);
					_this.currentRow = -1;
					_this.EnableMouseEvents();
				}
			}
			else
			{
				_this.RESULT.style.display = 'none';
				_this.currentRow = -1;
				_this.UnSelectAll();
			}
		}
		if (!!callback)
			callback();
	}

	this.UnSelectAll = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				tbl.rows[i].className = '';
		}
	}

	this.EnableMouseEvents = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					tbl.rows[i].id = 'row_' + i;
					tbl.rows[i].onmouseover = function (e) {
						if(_this.currentRow != this.id.substr(4))
						{
							_this.UnSelectAll();
							this.className = 'title-search-selected';
							_this.currentRow = this.id.substr(4);
						}
					};
					tbl.rows[i].onmouseout = function (e) {
						this.className = '';
						_this.currentRow = -1;
					};
				}
		}
	}

	this.onFocusLost = function(hide)
	{
		setTimeout(function(){_this.RESULT.style.display = 'none';}, 250);
	}

	this.onFocusGain = function()
	{
		if(_this.RESULT.innerHTML.length)
			_this.ShowResult();
	}

	this.onKeyDown = function(e)
	{
		if(!e)
			e = window.event;

		if (_this.RESULT.style.display == 'block')
		{
			if(_this.onKeyPress(e.keyCode))
				return BX.PreventDefault(e);
		}
	}

	this.Init = function()
	{
		this.CONTAINER = document.getElementById(this.arParams.CONTAINER_ID);
		this.RESULT = document.body.appendChild(document.createElement("DIV"));
		this.RESULT.className = 'title-search-result';
		this.INPUT = document.getElementById(this.arParams.INPUT_ID);
		this.startText = this.oldValue = this.INPUT.value;
		BX.bind(this.INPUT, 'focus', function() {_this.onFocusGain()});
		BX.bind(this.INPUT, 'blur', function() {_this.onFocusLost()});

		if(BX.browser.IsSafari() || BX.browser.IsIE())
			this.INPUT.onkeydown = this.onKeyDown;
		else
			this.INPUT.onkeypress = this.onKeyDown;

		if(this.arParams.WAIT_IMAGE)
		{
			this.WAIT = document.body.appendChild(document.createElement("DIV"));
			this.WAIT.style.backgroundImage = "url('" + this.arParams.WAIT_IMAGE + "')";
			if(!BX.browser.IsIE())
				this.WAIT.style.backgroundRepeat = 'none';
			this.WAIT.style.display = 'none';
			this.WAIT.style.position = 'absolute';
			this.WAIT.style.zIndex = '1100';
		}

		BX.bind(this.INPUT, 'bxchange', function() {_this.onChange()});
	}

	BX.ready(function (){_this.Init(arParams)});
}

/* End */
;; /* /bitrix/templates/marketplace-1c-v3/components/bitrix/system.auth.form/hd_loginform/script.js*/
; /* /bitrix/components/bitrix/search.title/script.js*/
