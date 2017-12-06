
; /* Start:"a:4:{s:4:"full";s:61:"/bitrix/templates/marketplace-1c-v3/script.js?143203026814456";s:6:"source";s:45:"/bitrix/templates/marketplace-1c-v3/script.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
var jsPublicTabController =
{
	SetTabCookieId: function(TabSec, TabId)
	{
		document.cookie = "BxTabs[" + TabSec + "]=" + TabId + "; path=/;";
	},

	SetTabCookieIdUnload: function()
	{
		//no more then 2 tablists
		for(i=0;i<2;i++)
		{
			var tab_ul = document.getElementById('tab-list-id'+i);
			if(tab_ul)
			{
				tab_li = tab_ul.getElementsByTagName('LI');
				for(j=0;j<tab_li.length;j++)
				{
					if(tab_li[j].className == 'active')
					{
						tab_a = tab_li[j].getElementsByTagName('A');
						tab_a[0].onclick();
					}
				}
			}
			else
				break;
		}
	},

	TabToTabAnchor: function(TabId)
	{
		tab = BX(TabId);
		if(tab)
		{
			tab_a = tab.getElementsByTagName('A');
			tab_a[0].onclick();
			SetActiveTab(tab_a[0], true);
		}
	}
}

// window.onbeforeunload = jsPublicTabController.SetTabCookieIdUnload;

//change load window
if (window.jsAjaxUtil)
{
	// show ajax visuality
	jsAjaxUtil.ShowLocalWaitWindow = function (TID, cont, bShadow)
	{
		if (typeof cont == 'string' || typeof cont == 'object' && cont.constructor == String)
			var obContainerNode = document.getElementById(cont);
		else
			var obContainerNode = cont;

		if (obContainerNode.getBoundingClientRect)
		{
			var obRect = obContainerNode.getBoundingClientRect();
			var obWndSize = jsAjaxUtil.GetWindowSize();

			var arContainerPos = {
				left: obRect.left + obWndSize.scrollLeft,
				top: obRect.top + obWndSize.scrollTop,
				right: obRect.right + obWndSize.scrollLeft,
				bottom: obRect.bottom + obWndSize.scrollTop
			};
		}
		else
			var arContainerPos = jsAjaxUtil.GetRealPos(obContainerNode);

		var container_id = obContainerNode.id;

		if (!arContainerPos) return;

		if (null == bShadow) bShadow = true;

		if (bShadow)
		{
			var obWaitShadow = document.body.appendChild(document.createElement('DIV'));
			obWaitShadow.id = 'waitshadow_' + container_id + '_' + TID;
			obWaitShadow.className = 'waitwindowlocalshadow';
			obWaitShadow.style.top = (arContainerPos.top - 5) + 'px';
			obWaitShadow.style.left = (arContainerPos.left - 5) + 'px';
			obWaitShadow.style.height = (arContainerPos.bottom - arContainerPos.top + 10) + 'px';
			obWaitShadow.style.width = (arContainerPos.right - arContainerPos.left + 10) + 'px';
		}

		var obWaitMessage = document.body.appendChild(document.createElement('DIV'));
		obWaitMessage.id = 'wait_' + container_id + '_' + TID;
		obWaitMessage.className = 'waitwindow';

		var div_top = arContainerPos.top + 5;
		if (div_top < document.body.scrollTop) div_top = document.body.scrollTop + 5;

		obWaitMessage.style.top = div_top + 'px';
		obWaitMessage.style.left = (arContainerPos.left + 5) + 'px';
		obWaitMessage.innerHTML = 'Загрузка...';

		if(jsAjaxUtil.IsIE())
		{
			var frame = document.createElement("IFRAME");
			frame.src = "javascript:''";
			frame.id = 'waitframe_' + container_id + '_' + TID;
			frame.className = "waitwindow";
			frame.style.width = obWaitMessage.offsetWidth + "px";
			frame.style.height = obWaitMessage.offsetHeight + "px";
			frame.style.left = obWaitMessage.style.left;
			frame.style.top = obWaitMessage.style.top;
			document.body.appendChild(frame);
		}

		function __Close(e)
		{
			if (!e) e = window.event
			if (!e) return;
			if (e.keyCode == 27)
			{
				jsAjaxUtil.CloseLocalWaitWindow(TID, cont);
				jsEvent.removeEvent(document, 'keypress', __Close);
			}
		}

		jsEvent.addEvent(document, 'keypress', __Close);
	}
}

if (document.location.hash.indexOf("#tab-") != -1)
{
		var selectedTabRaw = document.location.hash.substr(5,document.location.hash.length);
		var SubAnchor = false;
		var selectedTabID = false;
		if(selectedTabRaw.indexOf("!") != -1) //Extra Anchor
		{
			SubAnchor = selectedTabRaw.substr(selectedTabRaw.indexOf("!")+1);
			selectedTabID = selectedTabRaw.substr(0, selectedTabRaw.indexOf("!")-5);
		}
		else if(selectedTabRaw.indexOf("@") != -1) //Extra Anchor
		{
			SubAnchor = document.location.hash.substr(1,document.location.hash.length);
			selectedTabID = selectedTabRaw.substr(0, selectedTabRaw.indexOf("@")-5);
		}
		else
			selectedTabID = selectedTabRaw.substr(0, selectedTabRaw.length-5);

	//var selectedTabID = document.location.hash.substr(5,document.location.hash.length-10);
	window.onload = function()
	{
		var tab = document.getElementById("tab-" + selectedTabID);

		if (tab && tab.childNodes[0])
			SetActiveTab(tab.childNodes[0], true);

		if(SubAnchor)
			window.location = String(window.location).replace(/\#.*$/, "") + "#" + SubAnchor;
		else
			window.location = String(window.location).replace(/\#.*$/, "") + "#tab-" + selectedTabRaw;
	};
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

function systemAuthFormComponent_logout()
{
	BX('auth-logout-form').submit();
	return false;
}

BX.foreach = function(nodes,callback)
{
	if(BX.type.isElementNode(nodes))
		callback(nodes);
	if(BX.type.isArray(nodes))
		for(key in nodes)
			callback(nodes[key]);
}

BX.getElementsByClass = function(searchClass, node, tag)
{
	var classElements = new Array();
	if (node == null) node = document;
	if (tag == null) tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)" + searchClass + "(\\s|$)");

	for (i = 0,  j = 0;  i < elsLen;  i++)
	{
		if (pattern.test(els[i].className))
		{
			classElements[j] = els[i];
			j++;
		}
	}

	return classElements;
}

window.BXSite = function(node, bCache)
{
	return null;
}

BXSite.Fix = function(el, params)
{
	if (!el.BXFIXER)
		el.BXFIXER = new BXSite.CFixer(el, params);

	el.BXFIXER.Start()
}

BXSite.UnFix = function(el)
{
	if (!!el && !!el.BXFIXER)
		el.BXFIXER.Stop()
}

BXSite.CFixer = function(node, params)
{
	this.node = node;
	this.params = params || {type: 'top'};

	this.pos = {};
	this.limit = -1;

	this.position_top = null;
	this.position_bottom = null;
	this.position_right = null;

	this.bStarted = false;
	this.bFixed = false;

	this.gutter = null;
}

BXSite.CFixer.prototype.Start = function()
{
	if (this.bStarted)
		return;

	this.pos = BX.pos(this.node);

	BX.bind(window, 'scroll', BX.proxy(this._scroll_listener, this));
	BX.bind(window, 'resize', BX.proxy(this._scroll_listener, this));
	BX.bind(window, 'resize', BX.proxy(this._recalc_pos, this));

	this._scroll_listener();

	this.bStarted = true;
}

BXSite.CFixer.prototype.Stop = function()
{
	if (!this.bStarted)
		return;

	this._UnFix();

	BX.unbind(window, 'scroll', BX.proxy(this._scroll_listener, this));
	BX.unbind(window, 'resize', BX.proxy(this._scroll_listener, this));
	BX.unbind(window, 'resize', BX.proxy(this._recalc_pos, this));

	this.bStarted = false;
}

BXSite.CFixer.prototype._recalc_pos = function()
{
	this.pos = BX.pos(this.gutter || this.node);
	var node_pos = BX.pos(this.node);

	if (this.bFixed)
	{
		if (this.params.type == 'top' || this.params.type == 'bottom')
		{
			if(this.params.paddingWidth > 0)
				this.pos.width -= this.params.paddingWidth;

			this.node.style.width = this.pos.width + 'px';
		}
	}

	this._scroll_listener();
}

BXSite.CFixer.prototype._Fix = function()
{
	if (!this.bFixed)
	{
		this.pos = BX.pos(this.gutter || this.node);

		if (!this.gutter)
			this.gutter = this.node.parentNode.insertBefore(BX.create(
				this.node.tagName, {
					//style: {height: this.pos.height + 'px', width: this.pos.width + 'px'},
					style: {display: 'block', height: this.pos.height + 'px'},
					props: {className: this.node.className}
				}), this.node);

		if(this.params.paddingWidth > 0)
			this.pos.width -= this.params.paddingWidth;

		this._w = this.node.style.width;
		this.node.style.width = this.pos.width + 'px';

		BX.addClass(this.node, 'bxsite-fixed-' + this.params.type);

		if (this['position_' + this.params.type] !== null)
			this.node.style[this.params.type] = this['position_' + this.params.type] + 'px';

		this.bFixed = true;
	}
}

BXSite.CFixer.prototype._UnFix = function(bRefix)
{
	if (this.bFixed)
	{
		this.node.style.width = this._w
		BX.removeClass(this.node, 'bxsite-fixed-' + this.params.type);

		this.node.style[this.params.type] = null;

		this.bFixed = false;

		if (!bRefix)
		{
			if (this.gutter && this.gutter.parentNode)
				this.gutter.parentNode.removeChild(this.gutter);

			this.gutter = null;

			this._check_scroll(this.pos.left, this.pos.top);
		}
	}
}

BXSite.CFixer.prototype._ReFix = function()
{
	if (this.bFixed)
	{
		this._UnFix(true); BX.defer(this._Fix, this)();
	}
}

BXSite.CFixer.prototype._scroll_listener = function()
{
	var wndScroll = BX.GetWindowScrollPos(), bFixed = this.bFixed;

	if (!BX.isNodeInDom(this.node))
		return this.Stop();

	var pos = bFixed ? this.pos : BX.pos(this.node);

	if (this.params.limit_node)
	{
		var pos1 = BX.pos(this.params.limit_node);

		switch(this.params.type)
		{
			case 'top':
				this.limit = pos1.bottom - this.pos.height;
			break;
			case 'bottom':
				this.limit = pos1.top + this.pos.height;
			break;
			case 'right':
				this.limit = pos1.right + this.node.offsetWidth;
			break;
		}
	}

	if (!BX.isNodeHidden(this.node))
	{
		switch(this.params.type)
		{
			case 'top':
				this.position_top = 0;

				if (this.limit > 0 && wndScroll.scrollTop + this.position_top > this.limit)
					this._UnFix();
				else if (!this.bFixed && wndScroll.scrollTop + this.position_top >= pos.top)
					this._Fix();
				else if (this.bFixed && wndScroll.scrollTop + this.position_top < pos.top)
					this._UnFix();

			break;
			case 'bottom':
				var wndSize = BX.GetWindowInnerSize();

				wndScroll.scrollBottom = wndScroll.scrollTop + wndSize.innerHeight;

				if (this.limit > 0 && wndScroll.scrollBottom < this.limit)
					this._UnFix();
				else if (!this.bFixed && wndScroll.scrollBottom < pos.bottom)
					this._Fix();
				else if (this.bFixed && wndScroll.scrollBottom >= pos.bottom)
					this._UnFix();
			break;
			case 'right':
				var wndSize = BX.GetWindowInnerSize();

				// 15 is a browser scrollbar fix
				wndScroll.scrollRight = wndScroll.scrollLeft + wndSize.innerWidth - 15;

				if (this.limit > 0 && wndScroll.scrollRight < this.limit)
					this._UnFix();
				else if (!this.bFixed && wndScroll.scrollRight < pos.right)
					this._Fix();
				else if (this.bFixed && wndScroll.scrollRight >= pos.right)
					this._UnFix();

			break;
		}
	}
	else if (this.bFixed)
	{
		this._UnFix();
	}

	if (this.bFixed)
	{
		this._check_scroll(wndScroll.scrollLeft, wndScroll.scrollTop);
	}
	else
	{
		this._check_scroll(this.pos.left, this.pos.top);
	}

	if (bFixed != this.bFixed)
	{
		BX.onCustomEvent(this.node, 'onFixedNodeChangeState', [this.bFixed]);
	}
}

BXSite.CFixer.prototype._check_scroll = function(scrollLeft, scrollTop)
{
	if (this.params.type == 'top' || this.params.type == 'bottom')
		this.node.style.left = (this.pos.left - scrollLeft) + 'px';
	else
		this.node.style.top = (this.pos.top - scrollTop) + 'px'

	if (this.bFixed && this['position_' + this.params.type] !== null)
	{
		this.node.style[this.params.type] = this['position_' + this.params.type] + 'px';
	}
}

BX.ready(function(){
	var tabs = BX('tab-list-id0');
	if(tabs)
		BXSite.Fix(tabs, {paddingWidth : '25', type: 'top', limit_node: BX.nextSibling(tabs)});
	else
	{
		var tabs = BX('tab-list-id1');
		if(tabs)
			BXSite.Fix(tabs, {paddingWidth : '25', type: 'top', limit_node: BX.nextSibling(tabs)});

	}
});

function SetActiveTab(tab, bNoanimate)
{
	var listElement = tab.parentNode.parentNode;

	BX.foreach(BX.findChildren(listElement,{tag:'li'}), function(arTab){
		//Hide
		arTab.className = "";
		var tabBody = BX(arTab.id + "-body");
		if (tabBody)
			BX.hide(tabBody);
	});

	//Show
	var tabBody = BX(tab.parentNode.id + "-body");
	if (tabBody)
	{
		tab.parentNode.className = "active";
		tab.blur();
		BX.show(tabBody);

		if(!bNoanimate)
		{
			tabsBlock = BX('tab-list-id0');
			if (BX.hasClass(tabsBlock, 'bxsite-fixed-top'))
			{
				var pos = BX.pos(tab.parentNode.parentNode.parentNode), wndScroll = BX.GetWindowScrollPos();

				window.scrollTo(wndScroll.scrollLeft, pos.top - tabsBlock.offsetHeight - parseInt(tabsBlock.style.top));
			}
			// setTimeout(function(){}, 10);
resizeWorkArea(true);
			var easing = new BX.easing({
				duration : 500,
				start : { opacity : 0 },
				finish : { opacity : 100 },
				transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
				step : function(state){
					tabBody.style.opacity = state.opacity / 100;
				},
				complete : function(){
					tabBody.style.height = "auto";
					tabBody.style.overflowY = "visible";


				}
			});
			easing.animate();

		}
	}
}

function resizeWorkArea(bafterTab)
{
	var page = BX('bx-all-page-content');
	var sidebar = BX.findChild(page, {'className':'sidebar'}, true, false);
	var workarea = BX.findChild(page, {'className':'workarea'}, true, false);

	if(workarea && sidebar && sidebar.clientHeight >= workarea.clientHeight)
	{
		var breadcrumbs = 0;
		var banners = 0;

		var el = BX.findChild(page, {'className':'breadcrumbs'}, true, false);
		if(el)
			breadcrumbs = el.clientHeight+15;

		var el = BX.findChild(page, {'className':'wa_banners'}, true, false);
		if(el)
			banners = el.clientHeight+60;

		var el = BX.findChild(page, {'className':'wa_page'}, true, false);
		if(el)
			el.style.minHeight = (sidebar.clientHeight - banners - breadcrumbs - 66 - 25) + 'px';
	}
	else
	{
		if(bafterTab)
		{
			var el = BX.findChild(page, {'className':'wa_page'}, true, false);
			if(el != null && !isNaN(parseInt(el.style.minHeight)) && workarea.clientHeight > parseInt(el.style.minHeight))
				el.style.minHeight = '';
		}
	}
}

var aditCSS = '';
if(BX.browser.IsIOS())
{
	aditCSS = 'ios';
}
else if(BX.browser.IsAndroid())
{
	aditCSS = 'andr';
}
else if(/MSIE 6/.test(navigator.userAgent) || /MSIE 6/.test(navigator.userAgent) || /MSIE 8/.test(navigator.userAgent))
{
	aditCSS = 'ie7';
}
else if(/MSIE 9/.test(navigator.userAgent))
{
	aditCSS = 'ie9';
}
else if(/Opera/.test(navigator.userAgent))
{
	aditCSS = 'op';
}

if(aditCSS.length > 0)
{
	BX.loadCSS('/bitrix/templates/1c-bitrix-new/css/styles_'+aditCSS+'.css');
}
/* End */
;
; /* Start:"a:4:{s:4:"full";s:108:"/bitrix/templates/marketplace-1c-v3/components/bitrix/system.auth.form/hd_loginform/script.js?14115932485413";s:6:"source";s:93:"/bitrix/templates/marketplace-1c-v3/components/bitrix/system.auth.form/hd_loginform/script.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
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
; /* Start:"a:4:{s:4:"full";s:67:"/bitrix/components/bitrix/search.title/script.min.js?14293595356196";s:6:"source";s:48:"/bitrix/components/bitrix/search.title/script.js";s:3:"min";s:52:"/bitrix/components/bitrix/search.title/script.min.js";s:3:"map";s:52:"/bitrix/components/bitrix/search.title/script.map.js";}"*/
function JCTitleSearch(t){var e=this;this.arParams={AJAX_PAGE:t.AJAX_PAGE,CONTAINER_ID:t.CONTAINER_ID,INPUT_ID:t.INPUT_ID,MIN_QUERY_LEN:parseInt(t.MIN_QUERY_LEN)};if(t.WAIT_IMAGE)this.arParams.WAIT_IMAGE=t.WAIT_IMAGE;if(t.MIN_QUERY_LEN<=0)t.MIN_QUERY_LEN=1;this.cache=[];this.cache_key=null;this.startText="";this.running=false;this.currentRow=-1;this.RESULT=null;this.CONTAINER=null;this.INPUT=null;this.WAIT=null;this.ShowResult=function(t){if(BX.type.isString(t)){e.RESULT.innerHTML=t}e.RESULT.style.display=e.RESULT.innerHTML!==""?"block":"none";var s=e.adjustResultNode();var i;var r;var n=BX.findChild(e.RESULT,{tag:"table","class":"title-search-result"},true);if(n){r=BX.findChild(n,{tag:"th"},true)}if(r){var a=BX.pos(n);a.width=a.right-a.left;var l=BX.pos(r);l.width=l.right-l.left;r.style.width=l.width+"px";e.RESULT.style.width=s.width+l.width+"px";e.RESULT.style.left=s.left-l.width-1+"px";if(a.width-l.width>s.width)e.RESULT.style.width=s.width+l.width-1+"px";a=BX.pos(n);i=BX.pos(e.RESULT);if(i.right>a.right){e.RESULT.style.width=a.right-a.left+"px"}}var o;if(n)o=BX.findChild(e.RESULT,{"class":"title-search-fader"},true);if(o&&r){i=BX.pos(e.RESULT);o.style.left=i.right-i.left-18+"px";o.style.width=18+"px";o.style.top=0+"px";o.style.height=i.bottom-i.top+"px";o.style.display="block"}};this.onKeyPress=function(t){var s=BX.findChild(e.RESULT,{tag:"table","class":"title-search-result"},true);if(!s)return false;var i;var r=s.rows.length;switch(t){case 27:e.RESULT.style.display="none";e.currentRow=-1;e.UnSelectAll();return true;case 40:if(e.RESULT.style.display=="none")e.RESULT.style.display="block";var n=-1;for(i=0;i<r;i++){if(!BX.findChild(s.rows[i],{"class":"title-search-separator"},true)){if(n==-1)n=i;if(e.currentRow<i){e.currentRow=i;break}else if(s.rows[i].className=="title-search-selected"){s.rows[i].className=""}}}if(i==r&&e.currentRow!=i)e.currentRow=n;s.rows[e.currentRow].className="title-search-selected";return true;case 38:if(e.RESULT.style.display=="none")e.RESULT.style.display="block";var a=-1;for(i=r-1;i>=0;i--){if(!BX.findChild(s.rows[i],{"class":"title-search-separator"},true)){if(a==-1)a=i;if(e.currentRow>i){e.currentRow=i;break}else if(s.rows[i].className=="title-search-selected"){s.rows[i].className=""}}}if(i<0&&e.currentRow!=i)e.currentRow=a;s.rows[e.currentRow].className="title-search-selected";return true;case 13:if(e.RESULT.style.display=="block"){for(i=0;i<r;i++){if(e.currentRow==i){if(!BX.findChild(s.rows[i],{"class":"title-search-separator"},true)){var l=BX.findChild(s.rows[i],{tag:"a"},true);if(l){window.location=l.href;return true}}}}}return false}return false};this.onTimeout=function(){e.onChange(function(){setTimeout(e.onTimeout,500)})};this.onChange=function(t){if(e.running)return;e.running=true;if(e.INPUT.value!=e.oldValue&&e.INPUT.value!=e.startText){e.oldValue=e.INPUT.value;if(e.INPUT.value.length>=e.arParams.MIN_QUERY_LEN){e.cache_key=e.arParams.INPUT_ID+"|"+e.INPUT.value;if(e.cache[e.cache_key]==null){if(e.WAIT){var s=BX.pos(e.INPUT);var i=s.bottom-s.top-2;e.WAIT.style.top=s.top+1+"px";e.WAIT.style.height=i+"px";e.WAIT.style.width=i+"px";e.WAIT.style.left=s.right-i+2+"px";e.WAIT.style.display="block"}BX.ajax.post(e.arParams.AJAX_PAGE,{ajax_call:"y",INPUT_ID:e.arParams.INPUT_ID,q:e.INPUT.value,l:e.arParams.MIN_QUERY_LEN},function(s){e.cache[e.cache_key]=s;e.ShowResult(s);e.currentRow=-1;e.EnableMouseEvents();if(e.WAIT)e.WAIT.style.display="none";if(!!t)t();e.running=false});return}else{e.ShowResult(e.cache[e.cache_key]);e.currentRow=-1;e.EnableMouseEvents()}}else{e.RESULT.style.display="none";e.currentRow=-1;e.UnSelectAll()}}if(!!t)t();e.running=false};this.UnSelectAll=function(){var t=BX.findChild(e.RESULT,{tag:"table","class":"title-search-result"},true);if(t){var s=t.rows.length;for(var i=0;i<s;i++)t.rows[i].className=""}};this.EnableMouseEvents=function(){var t=BX.findChild(e.RESULT,{tag:"table","class":"title-search-result"},true);if(t){var s=t.rows.length;for(var i=0;i<s;i++)if(!BX.findChild(t.rows[i],{"class":"title-search-separator"},true)){t.rows[i].id="row_"+i;t.rows[i].onmouseover=function(t){if(e.currentRow!=this.id.substr(4)){e.UnSelectAll();this.className="title-search-selected";e.currentRow=this.id.substr(4)}};t.rows[i].onmouseout=function(t){this.className="";e.currentRow=-1}}}};this.onFocusLost=function(t){setTimeout(function(){e.RESULT.style.display="none"},250)};this.onFocusGain=function(){if(e.RESULT.innerHTML.length)e.ShowResult()};this.onKeyDown=function(t){if(!t)t=window.event;if(e.RESULT.style.display=="block"){if(e.onKeyPress(t.keyCode))return BX.PreventDefault(t)}};this.adjustResultNode=function(){var t;var s=BX.findParent(e.CONTAINER,BX.is_fixed);if(!!s){e.RESULT.style.position="fixed";e.RESULT.style.zIndex=BX.style(s,"z-index")+2;t=BX.pos(e.CONTAINER,true)}else{e.RESULT.style.position="absolute";t=BX.pos(e.CONTAINER)}t.width=t.right-t.left;e.RESULT.style.top=t.bottom+2+"px";e.RESULT.style.left=t.left+"px";e.RESULT.style.width=t.width+"px";return t};this._onContainerLayoutChange=function(){if(e.RESULT.style.display!=="none"&&e.RESULT.innerHTML!==""){e.adjustResultNode()}};this.Init=function(){this.CONTAINER=document.getElementById(this.arParams.CONTAINER_ID);BX.addCustomEvent(this.CONTAINER,"OnNodeLayoutChange",this._onContainerLayoutChange);this.RESULT=document.body.appendChild(document.createElement("DIV"));this.RESULT.className="title-search-result";this.INPUT=document.getElementById(this.arParams.INPUT_ID);this.startText=this.oldValue=this.INPUT.value;BX.bind(this.INPUT,"focus",function(){e.onFocusGain()});BX.bind(this.INPUT,"blur",function(){e.onFocusLost()});if(BX.browser.IsSafari()||BX.browser.IsIE())this.INPUT.onkeydown=this.onKeyDown;else this.INPUT.onkeypress=this.onKeyDown;if(this.arParams.WAIT_IMAGE){this.WAIT=document.body.appendChild(document.createElement("DIV"));this.WAIT.style.backgroundImage="url('"+this.arParams.WAIT_IMAGE+"')";if(!BX.browser.IsIE())this.WAIT.style.backgroundRepeat="none";this.WAIT.style.display="none";this.WAIT.style.position="absolute";this.WAIT.style.zIndex="1100"}BX.bind(this.INPUT,"bxchange",function(){e.onChange()})};BX.ready(function(){e.Init(t)})}
/* End */
;; /* /bitrix/templates/marketplace-1c-v3/script.js?143203026814456*/
; /* /bitrix/templates/marketplace-1c-v3/components/bitrix/system.auth.form/hd_loginform/script.js?14115932485413*/
; /* /bitrix/components/bitrix/search.title/script.min.js?14293595356196*/

//# sourceMappingURL=template_a180995ae464f1e7d0d25f133c998f0e.map.js