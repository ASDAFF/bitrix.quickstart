; /* /bitrix/js/main/core/core_autosave.js*/
; /* /bitrix/js/main/core/core_popup.js*/
; /* /bitrix/js/main/core/core_date.js*/
; /* /bitrix/js/main/core/core_dd.js*/
; /* /bitrix/js/main/rating_like.js*/
; /* /bitrix/js/main/core/core_tooltip.js*/
; /* /bitrix/js/main/utils.js*/
; /* /bitrix/js/main/core/core_window.js*/
; /* /bitrix/js/main/core/core.js*/
; /* /bitrix/js/main/core/core_ajax.js*/
; /* /bitrix/js/main/json/json2.min.js*/
; /* /bitrix/js/main/core/core_ls.js*/
; /* /bitrix/js/main/core/core_fx.js*/

; /* Start:/bitrix/js/main/core/core.js*/
/**********************************************************************/
/*********** Bitrix JS Core library ver 0.9.0 beta ********************/
/**********************************************************************/

;(function(window){

if (!!window.BX && !!window.BX.extend)
	return;

window.BXDEBUG = false;

var _bxtmp;
if (!!window.BX)
{
	_bxtmp = window.BX;
}

window.BX = function(node, bCache)
{
	if (BX.type.isNotEmptyString(node))
	{
		var ob;

		if (!!bCache && null != NODECACHE[node])
			ob = NODECACHE[node];
		ob = ob || document.getElementById(node);
		if (!!bCache)
			NODECACHE[node] = ob;

		return ob;
	}
	else if (BX.type.isDomNode(node))
		return node;
	else if (BX.type.isFunction(node))
		return BX.ready(node);

	return null;
};

// language utility
BX.message = function(mess)
{
	if (BX.type.isString(mess))
	{
		if (typeof BX.message[mess] == "undefined")
		{
			BX.onCustomEvent("onBXMessageNotFound", [mess]);
			if (typeof BX.message[mess] == "undefined")
			{
				BX.debug("message undefined: " + mess);
				BX.message[mess] = "";
			}

		}

		return BX.message[mess];
	}
	else
	{
		for (var i in mess)
		{
			if (mess.hasOwnProperty(i))
			{
				BX.message[i] = mess[i];
			}
		}
		return true;
	}
};

if(!!_bxtmp)
{
	for(var i in _bxtmp)
	{
		if(_bxtmp.hasOwnProperty(i))
		{
			if(!BX[i])
			{
				BX[i]=_bxtmp[i];
			}
			else if(i=='message')
			{
				for(var j in _bxtmp[i])
				{
					if(_bxtmp[i].hasOwnProperty(j))
					{
						BX.message[j]=_bxtmp[i][j];
					}
				}
			}
		}
	}

	_bxtmp = null;
}

var

/* ready */
__readyHandler = null,
readyBound = false,
readyList = [],

/* list of registered proxy functions */
proxySalt = Math.random(),
proxyId = 1,
proxyList = [],
deferList = [],

/* getElementById cache */
NODECACHE = {},

/* List of denied event handlers */
deniedEvents = [],

/* list of registered event handlers */
eventsList = [],

/* list of registered custom events */
customEvents = {},

/* list of external garbage collectors */
garbageCollectors = [],

/* list of loaded CSS files */
cssList = [],
cssInit = false,

/* list of loaded JS files */
jsList = [],
jsInit = false,


/* browser detection */
bSafari = navigator.userAgent.toLowerCase().indexOf('webkit') != -1,
bOpera = navigator.userAgent.toLowerCase().indexOf('opera') != -1,
bFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') != -1,
bChrome = navigator.userAgent.toLowerCase().indexOf('chrome') != -1,
bIE = document.attachEvent && !bOpera,

/* regexps */
r = {
	script: /<script([^>]*)>/i,
	script_src: /src=["\']([^"\']+)["\']/i,
	space: /\s+/,
	ltrim: /^[\s\r\n]+/g,
	rtrim: /[\s\r\n]+$/g,
	style: /<link.*?(rel="stylesheet"|type="text\/css")[^>]*>/i,
	style_href: /href=["\']([^"\']+)["\']/i
},

eventTypes = {
	click: 'MouseEvent',
	dblclick: 'MouseEvent',
	mousedown: 'MouseEvent',
	mousemove: 'MouseEvent',
	mouseout: 'MouseEvent',
	mouseover: 'MouseEvent',
	mouseup: 'MouseEvent',
	focus: 'MouseEvent',
	blur: 'MouseEvent'
},

lastWait = [],

CHECK_FORM_ELEMENTS = {tagName: /^INPUT|SELECT|TEXTAREA|BUTTON$/i};

BX.MSLEFT = 1;
BX.MSMIDDLE = 2;
BX.MSRIGHT = 4;

BX.ext = function(ob)
{
	for (var i in ob)
	{
		if(ob.hasOwnProperty(i))
		{
			this[i] = ob[i];
		}
	}
};

/* OO emulation utility */
BX.extend = function(child, parent)
{
	var f = function() {};
	f.prototype = parent.prototype;

	child.prototype = new f();
	child.prototype.constructor = child;

	child.superclass = parent.prototype;
	if(parent.prototype.constructor == Object.prototype.constructor)
	{
		parent.prototype.constructor = parent;
	}
};

BX.namespace = function(namespace)
{
	var parts = namespace.split(".");
	var parent = BX;

	if (parts[0] === "BX")
	{
		parts = parts.slice(1);
	}

	for (var i = 0; i < parts.length; i++) {

		if (typeof parent[parts[i]] === "undefined")
		{
			parent[parts[i]] = {};
		}
		parent = parent[parts[i]];
	}

	return parent;
};

BX.debug = function()
{
	if (window.BXDEBUG)
	{
		if (window.console && window.console.log)
			window.console.log('BX.debug: ', arguments.length > 0 ? arguments : arguments[0]);
		if (window.console && window.console.trace)
			console.trace();
	}
};

BX.is_subclass_of = function(ob, parent_class)
{
	if (ob instanceof parent_class)
		return true;

	if (parent_class.superclass)
		return BX.is_subclass_of(ob, parent_class.superclass);

	return false;
};

BX.clearNodeCache = function()
{
	NODECACHE = {};
	return false;
};

BX.bitrix_sessid = function() {return BX.message("bitrix_sessid"); };

/* DOM manipulation */
BX.create = function(tag, data, context)
{
	context = context || document;

	if (null == data && typeof tag == 'object' && tag.constructor !== String)
	{
		data = tag; tag = tag.tag;
	}

	var elem;
	if (BX.browser.IsIE() && !BX.browser.IsIE9() && null != data && null != data.props && (data.props.name || data.props.id))
	{
		elem = context.createElement('<' + tag + (data.props.name ? ' name="' + data.props.name + '"' : '') + (data.props.id ? ' id="' + data.props.id + '"' : '') + '>');
	}
	else
	{
		elem = context.createElement(tag);
	}

	return data ? BX.adjust(elem, data) : elem;
};

BX.adjust = function(elem, data)
{
	var j,len;

	if (!elem.nodeType)
		return null;

	if (elem.nodeType == 9)
		elem = elem.body;

	if (data.attrs)
	{
		for (j in data.attrs)
		{
			if(data.attrs.hasOwnProperty(j))
			{
				if (j == 'class' || j == 'className')
					elem.className = data.attrs[j];
				else if (j == 'for')
					elem.htmlFor = data.attrs[j];
				else if(data.attrs[j] == "")
					elem.removeAttribute(j);
				else
					elem.setAttribute(j, data.attrs[j]);
			}
		}
	}

	if (data.style)
	{
		for (j in data.style)
		{
			if(data.style.hasOwnProperty(j))
			{
				elem.style[j] = data.style[j];
			}
		}
	}

	if (data.props)
	{
		for (j in data.props)
		{
			if(data.props.hasOwnProperty(j))
			{
				elem[j] = data.props[j];
			}
		}
	}

	if (data.events)
	{
		for (j in data.events)
		{
			if(data.events.hasOwnProperty(j))
			{
				BX.bind(elem, j, data.events[j]);
			}
		}
	}

	if (data.children && data.children.length > 0)
	{
		for (j=0,len=data.children.length; j<len; j++)
		{
			if (BX.type.isNotEmptyString(data.children[j]))
				elem.innerHTML += data.children[j];
			else if (BX.type.isElementNode(data.children[j]))
				elem.appendChild(data.children[j]);
		}
	}
	else if (data.text)
	{
		BX.cleanNode(elem);
		elem.appendChild((elem.ownerDocument || document).createTextNode(data.text));
	}
	else if (data.html)
	{
		elem.innerHTML = data.html;
	}

	return elem;
};

BX.remove = function(ob)
{
	if (ob && null != ob.parentNode)
		ob.parentNode.removeChild(ob);
	ob = null;
	return null;
};

BX.cleanNode = function(node, bSuicide)
{
	node = BX(node);
	bSuicide = !!bSuicide;

	if (node && node.childNodes)
	{
		while(node.childNodes.length > 0)
			node.removeChild(node.firstChild);
	}

	if (node && bSuicide)
	{
		node = BX.remove(node);
	}

	return node;
};

BX.addClass = function(ob, value)
{
	var classNames;
	ob = BX(ob)

	value = BX.util.trim(value);
	if (value == '')
		return ob;

	if (ob)
	{
		if (!ob.className)
		{
			ob.className = value
		}
		else if (!!ob.classList && value.indexOf(' ') < 0)
		{
			ob.classList.add(value);
		}
		else
		{
			classNames = (value || "").split(r.space);

			var className = " " + ob.className + " ";
			for (var j = 0, cl = classNames.length; j < cl; j++)
			{
				if (className.indexOf(" " + classNames[j] + " ") < 0)
				{
					ob.className += " " + classNames[j];
				}
			}
		}
	}

	return ob;
};

BX.removeClass = function(ob, value)
{
	ob = BX(ob);
	if (ob)
	{
		if (ob.className && !!value)
		{
			if (BX.type.isString(value))
			{
				if (!!ob.classList && value.indexOf(' ') < 0)
				{
					ob.classList.remove(value);
				}
				else
				{
					var classNames = value.split(r.space), className = " " + ob.className + " ";
					for (var j = 0, cl = classNames.length; j < cl; j++)
					{
						className = className.replace(" " + classNames[j] + " ", " ");
					}

					ob.className = BX.util.trim(className);
				}
			}
			else
			{
				ob.className = "";
			}
		}
	}

	return ob;
};

BX.toggleClass = function(ob, value)
{
	var className;
	if (BX.type.isArray(value))
	{
		className = ' ' + ob.className + ' ';
		for (var j = 0, len = value.length; j < len; j++)
		{
			if (BX.hasClass(ob, value[j]))
			{
				className = (' ' + className + ' ').replace(' ' + value[j] + ' ', ' ');
				className += ' ' + value[j >= len-1 ? 0 : j+1];

				j--;
				break;
			}
		}

		if (j == len)
			ob.className += ' ' + value[0];
		else
			ob.className = className;

		ob.className = BX.util.trim(ob.className);
	}
	else if (BX.type.isNotEmptyString(value))
	{
		if (!!ob.classList)
		{
			ob.classList.toggle(value);
		}
		else
		{
			className = ob.className;
			if (BX.hasClass(ob, value))
			{
				className = (' ' + className + ' ').replace(' ' + value + ' ', ' ');
			}
			else
			{
				className += ' ' + value;
			}

			ob.className = BX.util.trim(className);
		}
	}

	return ob;
};

BX.hasClass = function(el, className)
{
	if (!el || !BX.type.isDomNode(el))
	{
		BX.debug(el);
		return false;
	}

	if (!el.className || !className)
	{
		return false;
	}

	if (!!el.classList && !!className && className.indexOf(' ') < 0)
	{
		return el.classList.contains(BX.util.trim(className));
	}
	else
		return ((" " + el.className + " ").indexOf(" " + className + " ")) >= 0;
};

BX.setOpacity = function(ob, percentage)
{
	if (ob.style.filter != null)
	{
		//IE
		ob.style.zoom = "100%";

		if (percentage == 100)
		{
			ob.style.filter = "";
		}
		else
		{
			ob.style.filter = 'alpha(opacity=' + percentage.toString() + ')';
		}
	}
	else if (ob.style.opacity != null)
	{
		// W3C
		ob.style.opacity = (percentage / 100).toString();
	}
	else if (ob.style.MozOpacity != null)
	{
		// Mozilla
		ob.style.MozOpacity = (percentage / 100).toString();
	}
};

BX.hoverEvents = function(el)
{
	if (el)
		return BX.adjust(el, {events: BX.hoverEvents()});
	else
		return {mouseover: BX.hoverEventsHover, mouseout: BX.hoverEventsHout};
};

BX.hoverEventsHover = function(){BX.addClass(this,'bx-hover');this.BXHOVER=true;};
BX.hoverEventsHout = function(){BX.removeClass(this,'bx-hover');this.BXHOVER=false;};

BX.focusEvents = function(el)
{
	if (el)
		return BX.adjust(el, {events: BX.focusEvents()});
	else
		return {mouseover: BX.focusEventsFocus, mouseout: BX.focusEventsBlur};
};

BX.focusEventsFocus = function(){BX.addClass(this,'bx-focus');this.BXFOCUS=true;};
BX.focusEventsBlur = function(){BX.removeClass(this,'bx-focus');this.BXFOCUS=false;};

BX.setUnselectable = function(node)
{
	BX.addClass(node, 'bx-unselectable');
	node.setAttribute('unSelectable', 'on');
};

BX.setSelectable = function(node)
{
	BX.removeClass(node, 'bx-unselectable');
	node.removeAttribute('unSelectable');
};

BX.styleIEPropertyName = function(name)
{
	if (name == 'float')
		name = BX.browser.IsIE() ? 'styleFloat' : 'cssFloat';
	else
	{
		var res = BX.browser.isPropertySupported(name);
		if (res)
		{
			name = res;
		}
		else
		{
			var reg = /(\-([a-z]){1})/g;
			if (reg.test(name))
			{
				name = name.replace(reg, function () {return arguments[2].toUpperCase();});
			}
		}
	}
	return name;
};

/* CSS-notation should be used here */
BX.style = function(el, property, value)
{
	if (!BX.type.isElementNode(el))
		return null;

	if (value == null)
	{
		var res;

		if(el.currentStyle)
			res = el.currentStyle[BX.styleIEPropertyName(property)];
		else if(window.getComputedStyle)
		{
			var q = BX.browser.isPropertySupported(property, true);
			if (!!q)
				property = q;

			res = BX.GetContext(el).getComputedStyle(el, null).getPropertyValue(property);
		}

		if(!res)
			res = '';
		return res;
	}
	else
	{
		el.style[BX.styleIEPropertyName(property)] = value;
		return el;
	}
};

BX.focus = function(el)
{
	try
	{
		el.focus();
		return true;
	}
	catch (e)
	{
		return false;
	}
};

BX.firstChild = function(el)
{
	var e = el.firstChild;
	while (e && !BX.type.isElementNode(e))
	{
		e = e.nextSibling;
	}

	return e;
};

BX.lastChild = function(el)
{
	var e = el.lastChild;
	while (e && !BX.type.isElementNode(e))
	{
		e = e.previousSibling;
	}

	return e;
};

BX.previousSibling = function(el)
{
	var e = el.previousSibling;
	while (e && !BX.type.isElementNode(e))
	{
		e = e.previousSibling;
	}

	return e;
};

BX.nextSibling = function(el)
{
	var e = el.nextSibling;
	while (e && !BX.type.isElementNode(e))
	{
		e = e.nextSibling;
	}

	return e;
};

/*
	params: {
		tagName|tag : 'tagName',
		className|class : 'className',
		attribute : {attribute : value, attribute : value} | attribute | [attribute, attribute....],
		property : {prop: value, prop: value} | prop | [prop, prop]
	}

	all values can be RegExps or strings
*/
BX.findChildren = function(obj, params, recursive)
{
	return BX.findChild(obj, params, recursive, true);
};

BX.findChild = function(obj, params, recursive, get_all)
{
	if(!obj || !obj.childNodes) return null;

	recursive = !!recursive; get_all = !!get_all;

	var n = obj.childNodes.length, result = [];

	for (var j=0; j<n; j++)
	{
		var child = obj.childNodes[j];

		if (_checkNode(child, params))
		{
			if (get_all)
				result.push(child)
			else
				return child;
		}

		if(recursive == true)
		{
			var res = BX.findChild(child, params, recursive, get_all);
			if (res)
			{
				if (get_all)
					result = BX.util.array_merge(result, res);
				else
					return res;
			}
		}
	}

	if (get_all || result.length > 0)
		return result;
	else
		return null;
};

BX.findParent = function(obj, params, maxParent)
{
	if(!obj)
		return null;

	var o = obj;
	while(o.parentNode)
	{
		var parent = o.parentNode;

		if (_checkNode(parent, params))
			return parent;

		o = parent;

		if (!!maxParent &&
			(BX.type.isFunction(maxParent)
				|| typeof maxParent == 'object'))
		{
			if (BX.type.isElementNode(maxParent))
			{
				if (o == maxParent)
					break;
			}
			else
			{
				if (_checkNode(o, maxParent))
					break;
			}
		}
	}
	return null;
};

BX.findNextSibling = function(obj, params)
{
	if(!obj)
		return null;
	var o = obj;
	while(o.nextSibling)
	{
		var sibling = o.nextSibling;
		if (_checkNode(sibling, params))
			return sibling;
		o = sibling;
	}
	return null;
};

BX.findPreviousSibling = function(obj, params)
{
	if(!obj)
		return null;

	var o = obj;
	while(o.previousSibling)
	{
		var sibling = o.previousSibling;
		if(_checkNode(sibling, params))
			return sibling;
		o = sibling;
	}
	return null;
};

BX.findFormElements = function(form)
{
	if (BX.type.isString(form))
		form = document.forms[form]||BX(form);

	var res = [];

	if (BX.type.isElementNode(form))
	{
		if (form.tagName.toUpperCase() == 'FORM')
		{
			res = form.elements;
		}
		else
		{
			res = BX.findChildren(form, CHECK_FORM_ELEMENTS, true);
		}
	}

	return res;
};

BX.clone = function(obj, bCopyObj)
{
	var _obj, i, l;
	if (bCopyObj !== false)
		bCopyObj = true;

	if (obj === null)
		return null;

	if (BX.type.isDomNode(obj))
	{
		_obj = obj.cloneNode(bCopyObj);
	}
	else if (typeof obj == 'object')
	{
		if (BX.type.isArray(obj))
		{
			_obj = [];
			for (i=0,l=obj.length;i<l;i++)
			{
				if (typeof obj[i] == "object" && bCopyObj)
					_obj[i] = BX.clone(obj[i], bCopyObj);
				else
					_obj[i] = obj[i];
			}
		}
		else
		{
			_obj =  {};
			if (obj.constructor)
			{
				if (obj.constructor === Date)
					_obj = new Date(obj);
				else
					_obj = new obj.constructor();
			}

			for (i in obj)
			{
				if (typeof obj[i] == "object" && bCopyObj)
					_obj[i] = BX.clone(obj[i], bCopyObj);
				else
					_obj[i] = obj[i];
			}
		}

	}
	else
	{
		_obj = obj;
	}

	return _obj;
};

/* events */
BX.bind = function(el, evname, func)
{
	if (!el)
	{
		return;
	}

	if (evname === 'mousewheel')
	{
		BX.bind(el, 'DOMMouseScroll', func);
	}
	else if (evname === 'transitionend')
	{
		BX.bind(el, 'webkitTransitionEnd', func);
		BX.bind(el, 'msTransitionEnd', func);
		BX.bind(el, 'oTransitionEnd', func);
		// IE8-9 doesn't support this feature!
	}
	else if (evname === 'bxchange')
	{
		BX.bind(el, "change", func);
		BX.bind(el, "cut", func);
		BX.bind(el, "paste", func);
		BX.bind(el, "drop", func);
		BX.bind(el, "keyup", func);

		return;
	}

	if (el.addEventListener) // Gecko / W3C
	{
		el.addEventListener(evname, func, false);
	}
	else if (el.attachEvent) // IE
	{
		el.attachEvent("on" + evname, BX.proxy(func, el));
	}
	else
	{
		el["on" + evname] = func;
	}

	eventsList[eventsList.length] = {'element': el, 'event': evname, 'fn': func};
};

BX.unbind = function(el, evname, func)
{
	if (!el)
	{
		return;
	}

	if (evname === 'mousewheel')
	{
		BX.unbind(el, 'DOMMouseScroll', func);
	}
	else if (evname === 'transitionend')
	{
		BX.unbind(el, 'webkitTransitionEnd', func);
		BX.unbind(el, 'msTransitionEnd', func);
		BX.unbind(el, 'oTransitionEnd', func);
	}
	else if (evname === 'bxchange')
	{
		BX.unbind(el, "change", func);
		BX.unbind(el, "cut", func);
		BX.unbind(el, "paste", func);
		BX.unbind(el, "drop", func);
		BX.unbind(el, "keyup", func);

		return;
	}

	if(el.removeEventListener) // Gecko / W3C
	{
		el.removeEventListener(evname, func, false);
	}
	else if(el.detachEvent) // IE
	{
		el.detachEvent("on" + evname, BX.proxy(func, el));
	}
	else
	{
		el["on" + evname] = null;
	}
};

BX.getEventButton = function(e)
{
	e = e || window.event;

	var flags = 0;

	if (typeof e.which != 'undefined')
	{
		switch (e.which)
		{
			case 1: flags = flags|BX.MSLEFT; break;
			case 2: flags = flags|BX.MSMIDDLE; break;
			case 3: flags = flags|BX.MSRIGHT; break;
		}
	}
	else if (typeof e.button != 'undefined')
	{
		flags = event.button;
	}

	return flags || BX.MSLEFT;
};

BX.unbindAll = function(el)
{
	if (!el)
		return;

	for (var i=0,len=eventsList.length; i<len; i++)
	{
		try
		{
			if (eventsList[i] && (null==el || el==eventsList[i].element))
			{
				BX.unbind(eventsList[i].element, eventsList[i].event, eventsList[i].fn);
				eventsList[i] = null;
			}
		}
		catch(e){}
	}

	if (null==el)
	{
		eventsList = [];
	}
};

var captured_events = null, _bind = null;
BX.CaptureEvents = function(el_c, evname_c)
{
	if (_bind)
		return false;

	_bind = BX.bind; captured_events = [];

	BX.bind = function(el, evname, func)
	{
		if (el === el_c && evname === evname_c)
			captured_events.push(func);

		_bind.apply(this, arguments);
	}
};

BX.CaptureEventsGet = function()
{
	if (_bind)
	{
		BX.bind = _bind;

		var captured = captured_events;

		_bind = null; captured_events = null;
		return captured;
	}
};

// Don't even try to use it for submit event!
BX.fireEvent = function(ob,ev)
{
	var result = false, e = null;
	if (BX.type.isDomNode(ob))
	{
		result = true;
		if (document.createEventObject)
		{
			// IE
			if (eventTypes[ev] != 'MouseEvent')
			{
				e = document.createEventObject();
				e.type = ev;
				result = ob.fireEvent('on' + ev, e);
			}

			if (ob[ev])
			{
				ob[ev]();
			}
		}
		else
		{
			// non-IE
			e = null;

			switch (eventTypes[ev])
			{
				case 'MouseEvent':
					e = document.createEvent('MouseEvent');
					e.initMouseEvent(ev, true, true, top, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, null);
				break;
				default:
					e = document.createEvent('Event');
					e.initEvent(ev, true, true);
			}

			result = ob.dispatchEvent(e);
		}
	}

	return result;
};

BX.getWheelData = function(e)
{
	e = e || window.event;
	e.wheelData = e.detail ? e.detail * -1 : e.wheelDelta / 40;
	return e.wheelData;
};

BX.proxy_context = null;

BX.delegate = function (func, thisObject)
{
	if (!func || !thisObject)
		return func;

	return function() {
		var cur = BX.proxy_context;
		BX.proxy_context = this;
		var res = func.apply(thisObject, arguments);
		BX.proxy_context = cur;
		return res;
	}
};

BX.delegateLater = function (func_name, thisObject, contextObject)
{
	return function()
	{
		if (thisObject[func_name])
		{
			var cur = BX.proxy_context;
			BX.proxy_context = this;
			var res = thisObject[func_name].apply(contextObject||thisObject, arguments);
			BX.proxy_context = cur;
			return res;
		}
	}
};

BX._initObjectProxy = function(thisObject)
{
	if (typeof thisObject['__proxy_id_' + proxySalt] == 'undefined')
	{
		thisObject['__proxy_id_' + proxySalt] = proxyList.length;
		proxyList[thisObject['__proxy_id_' + proxySalt]] = {};
	}
};

BX.proxy = function(func, thisObject)
{
	if (!func || !thisObject)
		return func;

	BX._initObjectProxy(thisObject)

	if (typeof func['__proxy_id_' + proxySalt] == 'undefined')
		func['__proxy_id_' + proxySalt] = proxyId++;

	if (!proxyList[thisObject['__proxy_id_' + proxySalt]][func['__proxy_id_' + proxySalt]])
		proxyList[thisObject['__proxy_id_' + proxySalt]][func['__proxy_id_' + proxySalt]] = BX.delegate(func, thisObject);

	return proxyList[thisObject['__proxy_id_' + proxySalt]][func['__proxy_id_' + proxySalt]];
};

BX.defer = function(func, thisObject)
{
	if (!!thisObject)
		return BX.defer_proxy(func, thisObject);
	else
		return function() {
			var arg = arguments;
			setTimeout(function(){func.apply(this,arg)}, 10);
		};
};

BX.defer_proxy = function(func, thisObject)
{
	if (!func || !thisObject)
		return func;

	var f = BX.proxy(func, thisObject);

	this._initObjectProxy(thisObject);

	if (typeof func['__defer_id_' + proxySalt] == 'undefined')
		func['__defer_id_' + proxySalt] = proxyId++;

	if (!proxyList[thisObject['__proxy_id_' + proxySalt]][func['__defer_id_' + proxySalt]])
	{
		proxyList[thisObject['__proxy_id_' + proxySalt]][func['__defer_id_' + proxySalt]] = BX.defer(BX.delegate(func, thisObject));
	}

	return proxyList[thisObject['__proxy_id_' + proxySalt]][func['__defer_id_' + proxySalt]];
};

BX.bindDelegate = function (elem, eventName, isTarget, handler)
{
	var h = BX.delegateEvent(isTarget, handler);
	BX.bind(elem, eventName, h);
	return h;
};

BX.delegateEvent = function(isTarget, handler)
{
	return function(e)
	{
		e = e || window.event;
		var target = e.target || e.srcElement;

		while (target != this)
		{
			if (_checkNode(target, isTarget))
			{
				return handler.call(target, e);
			}
			if (target && target.parentNode)
				target = target.parentNode;
			else
				break;
		}
	}
};

BX.False = function() {return false;};
BX.DoNothing = function() {};

// TODO: also check event handlers set via BX.bind()
BX.denyEvent = function(el, ev)
{
	deniedEvents.push([el, ev, el['on' + ev]]);
	el['on' + ev] = BX.DoNothing;
};

BX.allowEvent = function(el, ev)
{
	for(var i=0, len=deniedEvents.length; i<len; i++)
	{
		if (deniedEvents[i][0] == el && deniedEvents[i][1] == ev)
		{
			el['on' + ev] = deniedEvents[i][2];
			BX.util.deleteFromArray(deniedEvents, i);
			return;
		}
	}
};

BX.fixEventPageXY = function(event)
{
	BX.fixEventPageX(event);
	BX.fixEventPageY(event);
	return event;
};

BX.fixEventPageX = function(event)
{
	if (event.pageX == null && event.clientX != null)
	{
		event.pageX =
			event.clientX +
			(document.documentElement && document.documentElement.scrollLeft || document.body && document.body.scrollLeft || 0) -
			(document.documentElement.clientLeft || 0);
	}

	return event;
};

BX.fixEventPageY = function(event)
{
	if (event.pageY == null && event.clientY != null)
	{
		event.pageY =
			event.clientY +
			(document.documentElement && document.documentElement.scrollTop || document.body && document.body.scrollTop || 0) -
			(document.documentElement.clientTop || 0);
	}

	return event;
};

BX.PreventDefault = function(e)
{
	if(!e) e = window.event;
	if(e.stopPropagation)
	{
		e.preventDefault();
		e.stopPropagation();
	}
	else
	{
		e.cancelBubble = true;
		e.returnValue = false;
	}
	return false;
};

BX.eventReturnFalse = function(e)
{
	e=e||window.event;
	if (e && e.preventDefault) e.preventDefault();
	else e.returnValue = false;
	return false;
};

BX.eventCancelBubble = function(e)
{
	e=e||window.event;
	if(e && e.stopPropagation)
		e.stopPropagation();
	else
		e.cancelBubble = true;
};

/* custom events */
/*
	BX.addCustomEvent(eventObject, eventName, eventHandler) - set custom event handler for particular object
	BX.addCustomEvent(eventName, eventHandler) - set custom event handler for all objects
*/
BX.addCustomEvent = function(eventObject, eventName, eventHandler)
{
	/* shift parameters for short version */
	if (BX.type.isString(eventObject))
	{
		eventHandler = eventName;
		eventName = eventObject;
		eventObject = window;
	}

	eventName = eventName.toUpperCase();

	if (!customEvents[eventName])
		customEvents[eventName] = [];

	customEvents[eventName].push(
		{
			handler: eventHandler,
			obj: eventObject
		}
	);
};

BX.removeCustomEvent = function(eventObject, eventName, eventHandler)
{
	/* shift parameters for short version */
	if (BX.type.isString(eventObject))
	{
		eventHandler = eventName;
		eventName = eventObject;
		eventObject = window;
	}

	eventName = eventName.toUpperCase();

	if (!customEvents[eventName])
		return;

	for (var i = 0, l = customEvents[eventName].length; i < l; i++)
	{
		if (!customEvents[eventName][i])
			continue;
		if (customEvents[eventName][i].handler == eventHandler && customEvents[eventName][i].obj == eventObject)
		{
			delete customEvents[eventName][i];
			return;
		}
	}
};

// Warning! Don't use secureParams with DOM nodes in arEventParams
BX.onCustomEvent = function(eventObject, eventName, arEventParams, secureParams)
{
	/* shift parameters for short version */
	if (BX.type.isString(eventObject))
	{
		secureParams = arEventParams;
		arEventParams = eventName;
		eventName = eventObject;
		eventObject = window;
	}

	eventName = eventName.toUpperCase();

	if (!customEvents[eventName])
		return;

	if (!arEventParams)
		arEventParams = [];

	var h;
	for (var i = 0, l = customEvents[eventName].length; i < l; i++)
	{
		h = customEvents[eventName][i];
		if (!h || !h.handler)
			continue;

		if (h.obj == window || /*eventObject == window || */h.obj == eventObject) //- only global event handlers will be called
		{
			h.handler.apply(eventObject, !!secureParams ? BX.clone(arEventParams) : arEventParams);
		}
	}
};

BX.parseJSON = function(data, context)
{
	var result = null;
	if (BX.type.isString(data))
	{
		try {
			if (data.indexOf("\n") >= 0)
				eval('result = ' + data);
			else
				result = (new Function("return " + data))();
		} catch(e) {
			BX.onCustomEvent(context, 'onParseJSONFailure', [data, context])
		}
	}

	return result;
};

/* ready */
BX.isReady = false;
BX.ready = function(handler)
{
	bindReady();

	if (!BX.type.isFunction(handler))
	{
		BX.debug('READY: not a function! ', handler);
	}
	else
	{
		if (BX.isReady)
			handler.call(document);
		else if (readyList)
			readyList.push(handler);
	}
};

BX.submit = function(obForm, action_name, action_value, onAfterSubmit)
{
	action_name = action_name || 'save';
	if (!obForm['BXFormSubmit_' + action_name])
	{
		obForm['BXFormSubmit_' + action_name] = obForm.appendChild(BX.create('INPUT', {
			'props': {
				'type': 'submit',
				'name': action_name,
				'value': action_value || 'Y'
			},
			'style': {
				'display': 'none'
			}
		}));
	}

	if (obForm.sessid)
		obForm.sessid.value = BX.bitrix_sessid();

	setTimeout(BX.delegate(function() {BX.fireEvent(this, 'click'); if (onAfterSubmit) onAfterSubmit();}, obForm['BXFormSubmit_' + action_name]), 10);
};


/* browser detection */
BX.browser = {

	IsIE: function()
	{
		return bIE;
	},

	IsIE6: function()
	{
		return (/MSIE 6/i.test(navigator.userAgent));
	},

	IsIE7: function()
	{
		return (/MSIE 7/i.test(navigator.userAgent));
	},

	IsIE8: function()
	{
		return (/MSIE 8/i.test(navigator.userAgent));
	},

	IsIE9: function()
	{
		return !!document.documentMode && document.documentMode >= 9;
	},

	IsIE10: function()
	{
		return !!document.documentMode && document.documentMode >= 10;
	},

	IsIE11: function()
	{
		return BX.browser.DetectIeVersion() >= 11;
	},

	IsOpera: function()
	{
		return bOpera;
	},

	IsSafari: function()
	{
		return bSafari;
	},

	IsFirefox: function()
	{
		return bFirefox;
	},

	IsChrome: function()
	{
		return bChrome;
	},

	IsMac: function()
	{
		return (/Macintosh/i.test(navigator.userAgent));
	},

	IsAndroid: function()
	{
		return (/Android/i.test(navigator.userAgent));
	},

	IsIOS: function()
	{
		return (/(iPad;)|(iPhone;)/i.test(navigator.userAgent));
	},

	DetectIeVersion: function()
	{
		if(BX.browser.IsOpera() || BX.browser.IsSafari() || BX.browser.IsFirefox() || BX.browser.IsChrome())
		{
			return -1;
		}

		var rv = -1;
		if (!!(window.MSStream) && !(window.ActiveXObject) && ("ActiveXObject" in window))
		{
			//Primary check for IE 11 based on ActiveXObject behaviour (please see http://msdn.microsoft.com/en-us/library/ie/dn423948%28v=vs.85%29.aspx)
			rv = 11;
		}
		else if (BX.browser.IsIE10())
		{
			rv = 10;
		}
		else if (BX.browser.IsIE9())
		{
			rv = 9;
		}
		else if (BX.browser.IsIE())
		{
			rv = 8;
		}

		if (rv == -1 || rv == 8)
		{
			var re;
			if (navigator.appName == "Microsoft Internet Explorer")
			{
				re = new RegExp("MSIE ([0-9]+[\.0-9]*)");
				if (re.exec(navigator.userAgent) != null)
					rv = parseFloat( RegExp.$1 );
			}
			else if (navigator.appName == "Netscape")
			{
				//Alternative check for IE 11
				rv = 11;
				re = new RegExp("Trident/.*rv:([0-9]+[\.0-9]*)");
				if (re.exec(navigator.userAgent) != null)
					rv = parseFloat( RegExp.$1 );
			}
		}

		return rv;
	},

	IsDoctype: function(pDoc)
	{
		pDoc = pDoc || document;

		if (pDoc.compatMode)
			return (pDoc.compatMode == "CSS1Compat");

		if (pDoc.documentElement && pDoc.documentElement.clientHeight)
			return true;

		return false;
	},

	SupportLocalStorage: function()
	{
		return !!BX.localStorage && !!BX.localStorage.checkBrowser()
	},

	addGlobalClass: function() {

		var globalClass = "";

		//Mobile
		if (BX.browser.IsIOS())
		{
			globalClass += " bx-ios";
		}
		else if (BX.browser.IsMac())
		{
			globalClass += " bx-mac";
		}
		else if (BX.browser.IsAndroid())
		{
			globalClass += " bx-android";
		}

		globalClass += (BX.browser.IsIOS() || BX.browser.IsAndroid() ? " bx-touch" : " bx-no-touch");
		globalClass += (BX.browser.isRetina() ? " bx-retina" : " bx-no-retina");

		//Desktop
		var ieVersion = -1;
		if (/AppleWebKit/.test(navigator.userAgent))
		{
			globalClass += " bx-chrome";
		}
		else if ((ieVersion = BX.browser.DetectIeVersion()) > 0)
		{
			globalClass += " bx-ie bx-ie" + ieVersion;
			if (ieVersion > 7 && ieVersion < 10 && !BX.browser.IsDoctype())
			{
				// it seems IE10 doesn't have any specific bugs like others event in quirks mode
				globalClass += " bx-quirks";
			}
		}
		else if (/Opera/.test(navigator.userAgent))
		{
			globalClass += " bx-opera";
		}
		else if (/Gecko/.test(navigator.userAgent))
		{
			globalClass += " bx-firefox";
		}

		BX.addClass(document.documentElement, globalClass);

		BX.browser.addGlobalClass = BX.DoNothing;
	},

	isPropertySupported: function(jsProperty, bReturnCSSName)
	{
		if (!BX.type.isNotEmptyString(jsProperty))
			return false;

		var property = jsProperty.indexOf("-") > -1 ? getJsName(jsProperty) : jsProperty;
		bReturnCSSName = !!bReturnCSSName;

		var ucProperty = property.charAt(0).toUpperCase() + property.slice(1);
		var properties = (property + ' ' + ["Webkit", "Moz", "O", "ms"].join(ucProperty + " ") + ucProperty).split(" ");
		var obj = document.body || document.documentElement;

		for (var i = 0; i < properties.length; i++)
		{
			var prop = properties[i];
			if (obj.style[prop] !== undefined)
			{
				var prefix = prop == property
							? ""
							: "-" + prop.substr(0, prop.length - property.length).toLowerCase() + "-";
				return bReturnCSSName ? prefix + getCssName(property) : prop;
			}
		}

		function getCssName(propertyName)
		{
			return propertyName.replace(/([A-Z])/g, function() { return "-" + arguments[1].toLowerCase(); } )
		}

		function getJsName(cssName)
		{
			var reg = /(\-([a-z]){1})/g;
			if (reg.test(cssName))
				return cssName.replace(reg, function () {return arguments[2].toUpperCase();});
			else
				return cssName;
		}

		return false;
	},

	addGlobalFeatures : function(features, prefix)
	{
		if (!BX.type.isArray(features))
			return;

		var classNames = [];
		for (var i = 0; i < features.length; i++)
		{
			var support = !!BX.browser.isPropertySupported(features[i]);
			classNames.push( "bx-" + (support ? "" : "no-") + features[i].toLowerCase());
		}
		BX.addClass(document.documentElement, classNames.join(" "));
	},

	isRetina : function()
	{
		return window.devicePixelRatio && window.devicePixelRatio >= 2;
	}
};

/* low-level fx funcitons*/
BX.show = function(ob, displayType)
{
	if (ob.BXDISPLAY || !_checkDisplay(ob, displayType))
	{
		ob.style.display = ob.BXDISPLAY;
	}
};

BX.hide = function(ob, displayType)
{
	if (!ob.BXDISPLAY)
		_checkDisplay(ob, displayType);

	ob.style.display = 'none';
};

BX.toggle = function(ob, values)
{
	if (!values && BX.type.isElementNode(ob))
	{
		var bShow = true;
		if (ob.BXDISPLAY)
			bShow = !_checkDisplay(ob);
		else
			bShow = ob.style.display == 'none';

		if (bShow)
			BX.show(ob);
		else
			BX.hide(ob);
	}
	else if (BX.type.isArray(values))
	{
		for (var i=0,len=values.length; i<len; i++)
		{
			if (ob == values[i])
			{
				ob = values[i==len-1 ? 0 : i+1]
				break;
			}
		}
		if (i==len)
			ob = values[0];
	}

	return ob;
};

/* some useful util functions */

BX.util = {
	array_values: function(ar)
	{
		if (!BX.type.isArray(ar))
			return BX.util._array_values_ob(ar);
		var arv = [];
		for(var i=0,l=ar.length;i<l;i++)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(ar[i]);
		return arv;
	},

	_array_values_ob: function(ar)
	{
		var arv = [];
		for(var i in ar)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(ar[i]);
		return arv;
	},

	array_keys: function(ar)
	{
		if (!BX.type.isArray(ar))
			return BX.util._array_keys_ob(ar);
		var arv = [];
		for(var i=0,l=ar.length;i<l;i++)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(i);
		return arv;
	},

	_array_keys_ob: function(ar)
	{
		var arv = [];
		for(var i in ar)
			if (ar[i] !== null && typeof ar[i] != 'undefined')
				arv.push(i);
		return arv;
	},

	array_merge: function(first, second)
	{
		if (!BX.type.isArray(first)) first = [];
		if (!BX.type.isArray(second)) second = [];

		var i = first.length, j = 0;

		if (typeof second.length === "number")
		{
			for (var l = second.length; j < l; j++)
			{
				first[i++] = second[j];
			}
		}
		else
		{
			while (second[j] !== undefined)
			{
				first[i++] = second[j++];
			}
		}

		first.length = i;

		return first;
	},

	array_unique: function(ar)
	{
		var i=0,j,len=ar.length;
		if(len<2) return ar;

		for (; i<len-1;i++)
		{
			for (j=i+1; j<len;j++)
			{
				if (ar[i]==ar[j])
				{
					ar.splice(j--,1); len--;
				}
			}
		}

		return ar;
	},

	in_array: function(needle, haystack)
	{
		for(var i=0; i<haystack.length; i++)
		{
			if(haystack[i] == needle)
				return true;
		}
		return false;
	},

	array_search: function(needle, haystack)
	{
		for(var i=0; i<haystack.length; i++)
		{
			if(haystack[i] == needle)
				return i;
		}
		return -1;
	},

	object_search_key: function(needle, haystack)
	{
		if (typeof haystack[needle] != 'undefined')
			return haystack[needle];

		for(var i in haystack)
		{
			if (typeof haystack[i] == "object")
			{
				var result = BX.util.object_search_key(needle, haystack[i]);
				if (result !== false)
					return result;
			}
		}
		return false;
	},

	trim: function(s)
	{
		if (BX.type.isString(s))
			return s.replace(r.ltrim, '').replace(r.rtrim, '');
		else
			return s;
	},

	urlencode: function(s){return encodeURIComponent(s);},

	// it may also be useful. via sVD.
	deleteFromArray: function(ar, ind) {return ar.slice(0, ind).concat(ar.slice(ind + 1));},
	insertIntoArray: function(ar, ind, el) {return ar.slice(0, ind).concat([el]).concat(ar.slice(ind));},

	htmlspecialchars: function(str)
	{
		if(!str.replace) return str;

		return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	},

	htmlspecialcharsback: function(str)
	{
		if(!str.replace) return str;

		return str.replace(/\&quot;/g, '"').replace(/&#39;/g, "'").replace(/\&lt;/g, '<').replace(/\&gt;/g, '>').replace(/\&amp;/g, '&');
	},

	// Quote regular expression characters plus an optional character
	preg_quote: function(str, delimiter)
	{
		if(!str.replace)
			return str;
		return str.replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
	},

	jsencode: function(str)
	{
		if (!str || !str.replace)
			return str;

		var escapes =
		[
			{ c: "\\\\", r: "\\\\" }, // should be first
			{ c: "\\t", r: "\\t" },
			{ c: "\\n", r: "\\n" },
			{ c: "\\r", r: "\\r" },
			{ c: "\"", r: "\\\"" },
			{ c: "'", r: "\\'" },
			{ c: "<", r: "\\x3C" },
			{ c: ">", r: "\\x3E" },
			{ c: "\\u2028", r: "\\u2028" },
			{ c: "\\u2029", r: "\\u2029" }
		];
		for (var i = 0; i < escapes.length; i++)
			str = str.replace(new RegExp(escapes[i].c, 'g'), escapes[i].r);
		return str;
	},

	str_pad: function(input, pad_length, pad_string, pad_type)
	{
		pad_string = pad_string || ' ';
		pad_type = pad_type || 'right';
		input = input.toString();

		if (pad_type == 'left')
			return BX.util.str_pad_left(input, pad_length, pad_string);
		else
			return BX.util.str_pad_right(input, pad_length, pad_string);

	},

	str_pad_left: function(input, pad_length, pad_string)
	{
		var i = input.length, q=pad_string.length;
		if (i >= pad_length) return input;

		for(;i<pad_length;i+=q)
			input = pad_string + input;

		return input;
	},

	str_pad_right: function(input, pad_length, pad_string)
	{
		var i = input.length, q=pad_string.length;
		if (i >= pad_length) return input;

		for(;i<pad_length;i+=q)
			input += pad_string;

		return input;
	},

	strip_tags: function(str)
	{
		return str.split(/<[^>]+>/g).join('');
	},

	strip_php_tags: function(str)
	{
		return str.replace(/<\?(.|[\r\n])*?\?>/g, '');
	},

	popup: function(url, width, height)
	{
		var w, h;
		if(BX.browser.IsOpera())
		{
			w = document.body.offsetWidth;
			h = document.body.offsetHeight;
		}
		else
		{
			w = screen.width;
			h = screen.height;
		}
		return window.open(url, '', 'status=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+',top='+Math.floor((h - height)/2-14)+',left='+Math.floor((w - width)/2-5));
	},

	// BX.util.objectSort(object, sortBy, sortDir) - Sort object by property
	// function params: 1 - object for sort, 2 - sort by property, 3 - sort direction (asc/desc)
	// return: sort array [[objectElement], [objectElement]] in sortDir direction

	// example: BX.util.objectSort({'L1': {'name': 'Last'}, 'F1': {'name': 'First'}}, 'name', 'asc');
	// return: [{'name' : 'First'}, {'name' : 'Last'}]
	objectSort: function(object, sortBy, sortDir)
	{
		sortDir = sortDir == 'asc'? 'asc': 'desc';

		var arItems = [], i;
		for (i in object)
		{
			if (object.hasOwnProperty(i) && object[i][sortBy])
			{
				arItems.push([i, object[i][sortBy]]);
			}
		}

		if (sortDir == 'asc')
		{
			arItems.sort(function(i, ii) {
				var s1, s2;
				if (!isNaN(i[1]) && !isNaN(ii[1]))
				{
					s1 = parseInt(i[1]);
					s2 = parseInt(ii[1]);
				}
				else
				{
					s1 = i[1].toString().toLowerCase();
					s2 = ii[1].toString().toLowerCase();
				}

				if (s1 > s2)
					return 1;
				else if (s1 < s2)
					return -1;
				else
					return 0;
			});
		}
		else
		{
			arItems.sort(function(i, ii) {
				var s1, s2;
				if (!isNaN(i[1]) && !isNaN(ii[1]))
				{
					s1 = parseInt(i[1]);
					s2 = parseInt(ii[1]);
				}
				else
				{
					s1 = i[1].toString().toLowerCase();
					s2 = ii[1].toString().toLowerCase();
				}
				if (s1 < s2)
					return 1;
				else if (s1 > s2)
					return -1;
				else
					return 0;
			});
		}

		var arReturnArray = Array();
		for (i = 0; i < arItems.length; i++)
		{
			arReturnArray.push(object[arItems[i][0]]);
		}

		return arReturnArray;
	},

	// #fdf9e5 => {r=253, g=249, b=229}
	hex2rgb: function(color)
	{
		var rgb = color.replace(/[# ]/g,"").replace(/^(.)(.)(.)$/,'$1$1$2$2$3$3').match(/.{2}/g);
		for (var i=0;  i<3; i++)
		{
			rgb[i] = parseInt(rgb[i], 16);
		}
		return {'r':rgb[0],'g':rgb[1],'b':rgb[2]};
	},

	remove_url_param: function(url, param)
	{
		if (BX.type.isArray(param))
		{
			for (var i=0; i<param.length; i++)
				url = BX.util.remove_url_param(url, param[i])
		}
		else
		{
			url = url.replace(new RegExp('([?&])'+param+'=[^&]*[&]*', 'i'), '$1');
		}

		return url;
	},

	even: function(digit)
	{
		return (parseInt(digit) % 2 == 0)? true: false;
	},

	hashCode: function(str)
	{
		if(!BX.type.isNotEmptyString(str))
		{
			return 0;
		}

		var hash = 0;
		for (var i = 0; i < str.length; i++)
		{
			var c = str.charCodeAt(i);
			hash = ((hash << 5) - hash) + c;
			hash = hash & hash;
		}
		return hash;
	},

	getRandomString: function (length) {
		var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
		var charQty = chars.length;

		length = parseInt(length);
		if(isNaN(length) || length <= 0)
		{
			length = 8;
		}

		var result = "";
		for (var i = 0; i < length; i++)
		{
			result += chars.charAt(Math.floor(Math.random() * charQty));
		}
		return result;
	}
};

BX.type = {
	isString: function(item) {
		return item === '' ? true : (item ? (typeof (item) == "string" || item instanceof String) : false);
	},
	isNotEmptyString: function(item) {
		return BX.type.isString(item) ? item.length > 0 : false;
	},
	isBoolean: function(item) {
		return item === true || item === false;
	},
	isNumber: function(item) {
		return item === 0 ? true : (item ? (typeof (item) == "number" || item instanceof Number) : false);
	},
	isFunction: function(item) {
		return item === null ? false : (typeof (item) == "function" || item instanceof Function);
	},
	isElementNode: function(item) {
		//document.body.ELEMENT_NODE;
		return item && typeof (item) == "object" && "nodeType" in item && item.nodeType == 1 && item.tagName && item.tagName.toUpperCase() != 'SCRIPT' && item.tagName.toUpperCase() != 'STYLE' && item.tagName.toUpperCase() != 'LINK';
	},
	isDomNode: function(item) {
		return item && typeof (item) == "object" && "nodeType" in item;
	},
	isArray: function(item) {
		return item && Object.prototype.toString.call(item) == "[object Array]";
	},
	isDate : function(item) {
		return item && Object.prototype.toString.call(item) == "[object Date]";
	},
	isPlainObject: function(item)
	{
		if(!item || typeof(item) !== "object" || item.nodeType)
		{
			return false;
		}

		var hasProp = Object.prototype.hasOwnProperty;
		try
		{
			if ( item.constructor && !hasProp.call(item, "constructor") && !hasProp.call(item.constructor.prototype, "isPrototypeOf") )
			{
				return false;
			}
		}
		catch (e)
		{
			return false;
		}

		var key;
		for (key in item)
		{
		}
		return typeof(key) === "undefined" || hasProp.call(item, key);
	}
};

BX.isNodeInDom = function(node, doc)
{
	return node === (doc || document) ? true :
		(node.parentNode ? BX.isNodeInDom(node.parentNode) : false);
};

BX.isNodeHidden = function(node)
{
	if (node === document)
		return false;
	else if (BX.style(node, 'display') == 'none')
		return true;
	else
		return (node.parentNode ? BX.isNodeHidden(node.parentNode) : true);
};

BX.evalPack = function(code)
{
	while (code.length > 0)
	{
		var c = code.shift();

		if (c.TYPE == 'SCRIPT_EXT' || c.TYPE == 'SCRIPT_SRC')
		{
			BX.loadScript(c.DATA, function() {BX.evalPack(code)});
			return;
		}
		else if (c.TYPE == 'SCRIPT')
		{
			BX.evalGlobal(c.DATA);
		}
	}
};

BX.evalGlobal = function(data)
{
	if (data)
	{
		var head = document.getElementsByTagName("head")[0] || document.documentElement,
			script = document.createElement("script");

		script.type = "text/javascript";

		if (!BX.browser.IsIE())
		{
			script.appendChild(document.createTextNode(data));
		}
		else
		{
			script.text = data;
		}

		head.insertBefore(script, head.firstChild);
		head.removeChild(script);
	}
};

BX.processHTML = function(HTML, scriptsRunFirst)
{
	var matchScript, matchStyle, matchSrc, matchHref, scripts = [], styles = [], data = HTML;

	while ((matchScript = data.match(r.script)) !== null)
	{
		var end = data.search(/<\/script>/i);
		if (end == -1)
			break;

		var bRunFirst = scriptsRunFirst || (matchScript[1].indexOf('bxrunfirst') != '-1');

		if ((matchSrc = matchScript[1].match(r.script_src)) !== null)
			scripts.push({"bRunFirst": bRunFirst, "isInternal": false, "JS": matchSrc[1]});
		else
		{
			var start = matchScript.index + matchScript[0].length;
			var js = data.substr(start, end-start);

			scripts.push({"bRunFirst": bRunFirst, "isInternal": true, "JS": js});
		}

		data = data.substr(0, matchScript.index) + data.substr(end+9);
	}

	while ((matchStyle = data.match(r.style)) !== null)
	{
		if ((matchHref = matchStyle[0].match(r.style_href)) !== null && matchStyle[0].indexOf('media="') < 0)
		{
			styles.push(matchHref[1]);
		}
		data = data.replace(matchStyle[0], '');
	}

	return {'HTML': data, 'SCRIPT': scripts, 'STYLE': styles};
};

BX.garbage = function(call, thisObject)
{
	garbageCollectors.push({callback: call, context: thisObject});
};

/* window pos functions */

BX.GetDocElement = function (pDoc)
{
	pDoc = pDoc || document;
	return (BX.browser.IsDoctype(pDoc) ? pDoc.documentElement : pDoc.body);
};

BX.GetContext = function(node)
{
	if (BX.type.isElementNode(node))
		return node.ownerDocument.parentWindow || node.ownerDocument.defaultView || window;
	else if (BX.type.isDomNode(node))
		return node.parentWindow || node.defaultView || window;
	else
		return window;
};

BX.GetWindowInnerSize = function(pDoc)
{
	var width, height;

	pDoc = pDoc || document;

	if (window.innerHeight) // all except Explorer
	{
		width = BX.GetContext(pDoc).innerWidth;
		height = BX.GetContext(pDoc).innerHeight;
	}
	else if (pDoc.documentElement && (pDoc.documentElement.clientHeight || pDoc.documentElement.clientWidth)) // Explorer 6 Strict Mode
	{
		width = pDoc.documentElement.clientWidth;
		height = pDoc.documentElement.clientHeight;
	}
	else if (pDoc.body) // other Explorers
	{
		width = pDoc.body.clientWidth;
		height = pDoc.body.clientHeight;
	}
	return {innerWidth : width, innerHeight : height};
};

BX.GetWindowScrollPos = function(pDoc)
{
	var left, top;

	pDoc = pDoc || document;

	if (window.pageYOffset) // all except Explorer
	{
		left = BX.GetContext(pDoc).pageXOffset;
		top = BX.GetContext(pDoc).pageYOffset;
	}
	else if (pDoc.documentElement && (pDoc.documentElement.scrollTop || pDoc.documentElement.scrollLeft)) // Explorer 6 Strict
	{
		left = pDoc.documentElement.scrollLeft;
		top = pDoc.documentElement.scrollTop;
	}
	else if (pDoc.body) // all other Explorers
	{
		left = pDoc.body.scrollLeft;
		top = pDoc.body.scrollTop;
	}
	return {scrollLeft : left, scrollTop : top};
};

BX.GetWindowScrollSize = function(pDoc)
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
};

BX.GetWindowSize = function(pDoc)
{
	var innerSize = this.GetWindowInnerSize(pDoc);
	var scrollPos = this.GetWindowScrollPos(pDoc);
	var scrollSize = this.GetWindowScrollSize(pDoc);

	return  {
		innerWidth : innerSize.innerWidth, innerHeight : innerSize.innerHeight,
		scrollLeft : scrollPos.scrollLeft, scrollTop : scrollPos.scrollTop,
		scrollWidth : scrollSize.scrollWidth, scrollHeight : scrollSize.scrollHeight
	};
};

BX.hide_object = function(ob)
{
	ob = BX(ob);
	ob.style.position = 'absolute';
	ob.style.top = '-1000px';
	ob.style.left = '-1000px';
	ob.style.height = '10px';
	ob.style.width = '10px';
};

BX.is_relative = function(el)
{
	var p = BX.style(el, 'position');
	return p == 'relative' || p == 'absolute';
};

BX.is_float = function(el)
{
	var p = BX.style(el, 'float');
	return p == 'right' || p == 'left';
};

BX.is_fixed = function(el)
{
	var p = BX.style(el, 'position');
	return p == 'fixed';
};

BX.pos = function(el, bRelative)
{
	var r = { top: 0, right: 0, bottom: 0, left: 0, width: 0, height: 0 };
	bRelative = !!bRelative;
	if (!el)
		return r;
	if (typeof (el.getBoundingClientRect) != "undefined" && el.ownerDocument == document && !bRelative)
	{
		var clientRect = {};

		// getBoundingClientRect can return undefined and generate exception in some cases in IE8.
		try
		{
			clientRect = el.getBoundingClientRect();
		}
		catch(e)
		{
			clientRect =
			{
				top: el.offsetTop,
				left: el.offsetLeft,
				width: el.offsetWidth,
				height: el.offsetHeight,
				right: el.offsetLeft + el.offsetWidth,
				bottom: el.offsetTop + el.offsetHeight
			};
		}

		var root = document.documentElement;
		var body = document.body;

		r.top = clientRect.top + (root.scrollTop || body.scrollTop);
		r.left = clientRect.left + (root.scrollLeft || body.scrollLeft);
		r.width = clientRect.right - clientRect.left;
		r.height = clientRect.bottom - clientRect.top;
		r.right = clientRect.right + (root.scrollLeft || body.scrollLeft);
		r.bottom = clientRect.bottom + (root.scrollTop || body.scrollTop);
	}
	else
	{
		var x = 0, y = 0, w = el.offsetWidth, h = el.offsetHeight;
		var first = true;
		for (; el != null; el = el.offsetParent)
		{
			if (!first && bRelative && BX.is_relative(el))
				break;

			x += el.offsetLeft;
			y += el.offsetTop;
			if (first)
			{
				first = false;
				continue;
			}

			var elBorderLeftWidth = parseInt(BX.style(el, 'border-left-width')),
				elBorderTopWidth = parseInt(BX.style(el, 'border-top-width'));

			if (!isNaN(elBorderLeftWidth) && elBorderLeftWidth > 0)
				x += elBorderLeftWidth;
			if (!isNaN(elBorderTopWidth) && elBorderTopWidth > 0)
				y += elBorderTopWidth;
		}

		r.top = y;
		r.left = x;
		r.width = w;
		r.height = h;
		r.right = r.left + w;
		r.bottom = r.top + h;
	}

	for(var i in r)
	{
		if(r.hasOwnProperty(i))
		{
			r[i] = parseInt(r[i]);
		}
	}

	return r;
};

BX.align = function(pos, w, h, type)
{
	if (type)
		type = type.toLowerCase();
	else
		type = '';

	var pDoc = document;
	if (BX.type.isElementNode(pos))
	{
		pDoc = pos.ownerDocument;
		pos = BX.pos(pos);
	}

	var x = pos["left"], y = pos["bottom"];

	var scroll = BX.GetWindowScrollPos(pDoc);
	var size = BX.GetWindowInnerSize(pDoc);

	if((size.innerWidth + scroll.scrollLeft) - (pos["left"] + w) < 0)
	{
		if(pos["right"] - w >= 0 )
			x = pos["right"] - w;
		else
			x = scroll.scrollLeft;
	}

	if(((size.innerHeight + scroll.scrollTop) - (pos["bottom"] + h) < 0) || ~type.indexOf('top'))
	{
		if(pos["top"] - h >= 0 || ~type.indexOf('top'))
			y = pos["top"] - h;
		else
			y = scroll.scrollTop;
	}

	return {'left':x, 'top':y};
};

BX.scrollToNode = function(node)
{
	var obNode = BX(node);

	if (obNode.scrollIntoView)
		obNode.scrollIntoView(true);
	else
	{
		var arNodePos = BX.pos(obNode);
		window.scrollTo(arNodePos.left, arNodePos.top);
	}
};

/* non-xhr loadings */
BX.showWait = function(node, msg)
{
	node = BX(node) || document.body || document.documentElement;
	msg = msg || BX.message('JS_CORE_LOADING');

	var container_id = node.id || Math.random();

	var obMsg = node.bxmsg = document.body.appendChild(BX.create('DIV', {
		props: {
			id: 'wait_' + container_id,
			className: 'bx-core-waitwindow'
		},
		text: msg
	}));

	setTimeout(BX.delegate(_adjustWait, node), 10);

	lastWait[lastWait.length] = obMsg;
	return obMsg;
};

BX.closeWait = function(node, obMsg)
{
	if(node && !obMsg)
		obMsg = node.bxmsg;
	if(node && !obMsg && BX.hasClass(node, 'bx-core-waitwindow'))
		obMsg = node;
	if(node && !obMsg)
		obMsg = BX('wait_' + node.id);
	if(!obMsg)
		obMsg = lastWait.pop();

	if (obMsg && obMsg.parentNode)
	{
		for (var i=0,len=lastWait.length;i<len;i++)
		{
			if (obMsg == lastWait[i])
			{
				lastWait = BX.util.deleteFromArray(lastWait, i);
				break;
			}
		}

		obMsg.parentNode.removeChild(obMsg);
		if (node) node.bxmsg = null;
		BX.cleanNode(obMsg, true);
	}
};

BX.setJSList = function(scripts)
{
	if (BX.type.isArray(scripts))
	{
		jsList = scripts;
	}
};

BX.setCSSList = function(scripts)
{
	if (BX.type.isArray(scripts))
	{
		cssList = scripts;
	}
};

BX.getJSPath = function(js)
{
	return js.replace(/\.js(\?\d*)/, '.js').replace(/^(http[s]*:)*\/\/[^\/]+/i, '');
};

BX.getCSSPath = function(css)
{
	return css.replace(/\.css(\?\d*)/, '.css').replace(/^(http[s]*:)*\/\/[^\/]+/i, '');
};

BX.getCDNPath = function(path)
{
	return path;
};

BX.loadScript = function(script, callback, doc)
{
	if (!BX.isReady)
	{
		var _args = arguments;
		BX.ready(function() {
			BX.loadScript.apply(this, _args);
		});
		return;
	}

	doc = doc || document;

	if (BX.type.isString(script))
		script = [script];
	var _callback = function()
	{
		return (callback && BX.type.isFunction(callback)) ? callback() : null
	}
	var load_js = function(ind)
	{
		if(ind >= script.length)
			return _callback();

		if(!!script[ind])
		{
			var fileSrc = BX.getJSPath(script[ind]);
			if(_isScriptLoaded(fileSrc))
			{
				load_js(++ind);
			}
			else
			{
				var oHead = doc.getElementsByTagName("head")[0] || doc.documentElement;
				var oScript = doc.createElement('script');
				oScript.src = script[ind];

				var bLoaded = false;
				oScript.onload = oScript.onreadystatechange = function()
				{
					if (!bLoaded && (!oScript.readyState || oScript.readyState == "loaded" || oScript.readyState == "complete"))
					{
						bLoaded = true;
						setTimeout(function (){load_js(++ind);}, 50);

						oScript.onload = oScript.onreadystatechange = null;
						if (oHead && oScript.parentNode)
						{
							oHead.removeChild(oScript);
						}
					}
				}

				jsList.push(fileSrc);
				return oHead.insertBefore(oScript, oHead.firstChild);
			}
		}
		else
		{
			load_js(++ind);
		}
	}

	load_js(0);
};

BX.loadCSS = function(arCSS, doc, win)
{
	if (!BX.isReady)
	{
		var _args = arguments;
		BX.ready(function() {
			BX.loadCSS.apply(this, _args);
		});
		return null;
	}

	var bSingle = false;
	if (BX.type.isString(arCSS))
	{
		bSingle = true;
		arCSS = [arCSS];
	}

	var i = 0,
		l = arCSS.length,
		lnk = null,
		pLnk = [];

	if (l == 0)
		return null;

	doc = doc || document;
	win = win || window;

	if (!win.bxhead)
	{
		var heads = doc.getElementsByTagName('HEAD');
		win.bxhead = heads[0];

		if (!win.bxhead)
		{
			return null;
		}
	}

	for (i = 0; i < l; i++)
	{
		var _check = BX.getCSSPath(arCSS[i]);
		if (_isCssLoaded(_check))
		{
			continue;
		}

		lnk = document.createElement('LINK');
		lnk.href = arCSS[i];
		lnk.rel = 'stylesheet';
		lnk.type = 'text/css';

		var templateLink = _getTemplateLink(win.bxhead);
		if (templateLink !== null)
		{
			templateLink.parentNode.insertBefore(lnk, templateLink);
		}
		else
		{
			win.bxhead.appendChild(lnk);
		}

		pLnk.push(lnk);
		cssList.push(_check);
	}

	if (bSingle)
		return lnk;

	return pLnk;
};

BX.reload = function(back_url, bAddClearCache)
{
	if (back_url === true)
	{
		bAddClearCache = true;
		back_url = null;
	}

	var new_href = back_url || top.location.href;

	var hashpos = new_href.indexOf('#'), hash = '';

	if (hashpos != -1)
	{
		hash = new_href.substr(hashpos);
		new_href = new_href.substr(0, hashpos);
	}

	if (bAddClearCache && new_href.indexOf('clear_cache=Y') < 0)
		new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + 'clear_cache=Y';

	if (hash)
	{
		// hack for clearing cache in ajax mode components with history emulation
		if (bAddClearCache && (hash.substr(0, 5) == 'view/' || hash.substr(0, 6) == '#view/') && hash.indexOf('clear_cache%3DY') < 0)
			hash += (hash.indexOf('%3F') == -1 ? '%3F' : '%26') + 'clear_cache%3DY'

		new_href = new_href.replace(/(\?|\&)_r=[\d]*/, '');
		new_href += (new_href.indexOf('?') == -1 ? '?' : '&') + '_r='+Math.round(Math.random()*10000) + hash;
	}

	top.location.href = new_href;
};

BX.clearCache = function()
{
	BX.showWait();
	BX.reload(true);
};

BX.template = function(tpl, callback, bKillTpl)
{
	BX.ready(function() {
		_processTpl(BX(tpl), callback, bKillTpl);
	});
};

BX.isAmPmMode = function()
{
	return BX.message('FORMAT_DATETIME').match('T') == null ? false : true;
};

BX.formatDate = function(date, format)
{
	date = date || new Date();

	var bTime = date.getHours() || date.getMinutes() || date.getSeconds(),
		str = !!format
			? format :
			(bTime ? BX.message('FORMAT_DATETIME') : BX.message('FORMAT_DATE')
		);

	return str.replace(/YYYY/ig, date.getFullYear())
		.replace(/MMMM/ig, BX.util.str_pad_left((date.getMonth()+1).toString(), 2, '0'))
		.replace(/MM/ig, BX.util.str_pad_left((date.getMonth()+1).toString(), 2, '0'))
		.replace(/DD/ig, BX.util.str_pad_left(date.getDate().toString(), 2, '0'))
		.replace(/HH/ig, BX.util.str_pad_left(date.getHours().toString(), 2, '0'))
		.replace(/MI/ig, BX.util.str_pad_left(date.getMinutes().toString(), 2, '0'))
		.replace(/SS/ig, BX.util.str_pad_left(date.getSeconds().toString(), 2, '0'));
};

BX.getNumMonth = function(month)
{
	var wordMonthCut = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
	var wordMonth = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];

	var q = month.toUpperCase();
	for (i = 1; i <= 12; i++)
	{
		if (q == BX.message('MON_'+i).toUpperCase() || q == BX.message('MONTH_'+i).toUpperCase() || q == wordMonthCut[i-1].toUpperCase() || q == wordMonth[i-1].toUpperCase())
		{
			return i;
		}
	}
	return month;
};

BX.parseDate = function(str)
{
	if (BX.type.isNotEmptyString(str))
	{
		var regMonths = '';
		for (i = 1; i <= 12; i++)
		{
			regMonths = regMonths + '|' + BX.message('MON_'+i);
		}

		var expr = new RegExp('([0-9]+|[a-z]+' + regMonths + ')', 'ig');
		var aDate = str.match(expr),
			aFormat = BX.message('FORMAT_DATE').match(/(DD|MI|MMMM|MM|M|YYYY)/ig),
			i, cnt,
			aDateArgs=[], aFormatArgs=[],
			aResult={};

		if (!aDate)
			return null;

		if(aDate.length > aFormat.length)
		{
			aFormat = BX.message('FORMAT_DATETIME').match(/(DD|MI|MMMM|MM|M|YYYY|HH|H|SS|TT|T|GG|G)/ig);
		}

		for(i = 0, cnt = aDate.length; i < cnt; i++)
		{
			if(BX.util.trim(aDate[i]) != '')
			{
				aDateArgs[aDateArgs.length] = aDate[i];
			}
		}

		for(i = 0, cnt = aFormat.length; i < cnt; i++)
		{
			if(BX.util.trim(aFormat[i]) != '')
			{
				aFormatArgs[aFormatArgs.length] = aFormat[i];
			}
		}


		var m = BX.util.array_search('MMMM', aFormatArgs)
		if (m > 0)
		{
			aDateArgs[m] = BX.getNumMonth(aDateArgs[m]);
			aFormatArgs[m] = "MM";
		}
		else
		{
			m = BX.util.array_search('M', aFormatArgs)
			if (m > 0)
			{
				aDateArgs[m] = BX.getNumMonth(aDateArgs[m]);
				aFormatArgs[m] = "MM";
			}
		}

		for(i = 0, cnt = aFormatArgs.length; i < cnt; i++)
		{
			var k = aFormatArgs[i].toUpperCase();
			aResult[k] = k == 'T' || k == 'TT' ? aDateArgs[i] : parseInt(aDateArgs[i], 10);
		}

		if(aResult['DD'] > 0 && aResult['MM'] > 0 && aResult['YYYY'] > 0)
		{
			var d = new Date();
			d.setDate(1);
			d.setFullYear(aResult['YYYY']);
			d.setMonth(aResult['MM']-1);
			d.setDate(aResult['DD']);
			d.setHours(0, 0, 0);

			if(
				(!isNaN(aResult['HH']) || !isNaN(aResult['GG']) || !isNaN(aResult['H']) || !isNaN(aResult['G']))
					&& !isNaN(aResult['MI'])
			)
			{
				if (!isNaN(aResult['H']) || !isNaN(aResult['G']))
				{
					var bPM = (aResult['T']||aResult['TT']||'am').toUpperCase()=='PM';
					aResult['HH'] = parseInt(aResult['H']||aResult['G']||0, 10) + (bPM ? 12 : 0);
				}
				else
				{
					aResult['HH'] = parseInt(aResult['HH']||aResult['GG']||0, 10);
				}

				if (isNaN(aResult['SS']))
					aResult['SS'] = 0;

				d.setHours(aResult['HH'], aResult['MI'], aResult['SS']);
			}

			return d;
		}
	}

	return null;
};

BX.selectUtils =
{
	addNewOption: function(oSelect, opt_value, opt_name, do_sort, check_unique)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			var n = oSelect.length;
			if(check_unique !== false)
			{
				for(var i=0;i<n;i++)
				{
					if(oSelect[i].value==opt_value)
					{
						return;
					}
				}
			}

			var newoption = new Option(opt_name, opt_value, false, false);
			oSelect.options[n]=newoption;
		}

		if(do_sort === true)
		{
			this.sortSelect(oSelect);
		}
	},

	deleteOption: function(oSelect, opt_value)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			for(var i=0;i<oSelect.length;i++)
			{
				if(oSelect[i].value==opt_value)
				{
					oSelect.remove(i);
					break;
				}
			}
		}
	},

	deleteSelectedOptions: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			var i=0;
			while(i<oSelect.length)
			{
				if(oSelect[i].selected)
				{
					oSelect[i].selected=false;
					oSelect.remove(i);
				}
				else
				{
					i++;
				}
			}
		}
	},

	deleteAllOptions: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			for(var i=oSelect.length-1; i>=0; i--)
			{
				oSelect.remove(i);
			}
		}
	},

	optionCompare: function(record1, record2)
	{
		var value1 = record1.optText.toLowerCase();
		var value2 = record2.optText.toLowerCase();
		if (value1 > value2) return(1);
		if (value1 < value2) return(-1);
		return(0);
	},

	sortSelect: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			var myOptions = [];
			var n = oSelect.options.length;
			var i = 0;
			for (i=0;i<n;i++)
			{
				myOptions[i] = {
					optText:oSelect[i].text,
					optValue:oSelect[i].value
				};
			}
			myOptions.sort(this.optionCompare);
			oSelect.length=0;
			n = myOptions.length;
			for(i=0;i<n;i++)
			{
				oSelect[i] = new Option(myOptions[i].optText, myOptions[i].optValue, false, false);
			}
		}
	},

	selectAllOptions: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
			{
				oSelect[i].selected=true;
			}
		}
	},

	selectOption: function(oSelect, opt_value)
	{
		oSelect = BX(oSelect);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
			{
				oSelect[i].selected = (oSelect[i].value == opt_value);
			}
		}
	},

	addSelectedOptions: function(oSelect, to_select_id, check_unique, do_sort)
	{
		oSelect = BX(oSelect);
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
			if(oSelect[i].selected)
				this.addNewOption(to_select_id, oSelect[i].value, oSelect[i].text, do_sort, check_unique);
	},

	moveOptionsUp: function(oSelect)
	{
		oSelect = BX(oSelect)
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
		{
			if(oSelect[i].selected && i>0 && oSelect[i-1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i-1].text, oSelect[i-1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i-1] = option1;
				oSelect[i-1].selected = true;
			}
		}
	},

	moveOptionsDown: function(oSelect)
	{
		oSelect = BX(oSelect);
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=n-1; i>=0; i--)
		{
			if(oSelect[i].selected && i<n-1 && oSelect[i+1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i+1].text, oSelect[i+1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i+1] = option1;
				oSelect[i+1].selected = true;
			}
		}
	}
};

BX.getEventTarget = function(e)
{
	if(e.target)
	{
		return e.target;
	}
	else if(e.srcElement)
	{
		return e.srcElement;
	}
	return null;
};

/******* HINT ***************/
// if function has 2 params - the 2nd one is hint html. otherwise hint_html is third and hint_title - 2nd;
// '<div onmouseover="BX.hint(this, 'This is &lt;b&gt;Hint&lt;/b&gt;')"'>;
// BX.hint(el, 'This is <b>Hint</b>') - this won't work, use constructor
BX.hint = function(el, hint_title, hint_html, hint_id)
{
	if (null == hint_html)
	{
		hint_html = hint_title;
		hint_title = '';
	}

	if (null == el.BXHINT)
	{
		el.BXHINT = new BX.CHint({
			parent: el, hint: hint_html, title: hint_title, id: hint_id
		});
		el.BXHINT.Show();
	}
};

BX.hint_replace = function(el, hint_title, hint_html)
{
	if (null == hint_html)
	{
		hint_html = hint_title;
		hint_title = '';
	}

	if (!el || !el.parentNode || !hint_html)
			return null;

	var obHint = new BX.CHint({
		hint: hint_html,
		title: hint_title
	});

	obHint.CreateParent();

	el.parentNode.insertBefore(obHint.PARENT, el);
	el.parentNode.removeChild(el);

	obHint.PARENT.style.marginLeft = '5px';

	return el;
};

BX.CHint = function(params)
{
	this.PARENT = BX(params.parent);

	this.HINT = params.hint;
	this.HINT_TITLE = params.title;

	this.PARAMS = {}
	for (var i in this.defaultSettings)
	{
		if (null == params[i])
			this.PARAMS[i] = this.defaultSettings[i];
		else
			this.PARAMS[i] = params[i];
	}

	if (null != params.id)
		this.ID = params.id;

	this.timer = null;
	this.bInited = false;
	this.msover = true;

	if (this.PARAMS.showOnce)
	{
		this.__show();
		this.msover = false;
		this.timer = setTimeout(BX.proxy(this.__hide, this), this.PARAMS.hide_timeout);
	}
	else if (this.PARENT)
	{
		BX.bind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
		BX.bind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));
	}

	BX.addCustomEvent('onMenuOpen', BX.delegate(this.disable, this));
	BX.addCustomEvent('onMenuClose', BX.delegate(this.enable, this));
};

BX.CHint.prototype.defaultSettings = {
	show_timeout: 1000,
	hide_timeout: 500,
	dx: 2,
	showOnce: false,
	preventHide: true,
	min_width: 250
};

BX.CHint.prototype.CreateParent = function(element, params)
{
	if (this.PARENT)
	{
		BX.unbind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
		BX.unbind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));
	}

	if (!params) params = {}
	var type = 'icon';

	if (params.type && (params.type == "link" || params.type == "icon"))
		type = params.type;

	if (element)
		type = "element";

	if (type == "icon")
	{
		element = BX.create('IMG', {
			props: {
				src: params.iconSrc
					? params.iconSrc
					: "/bitrix/js/main/core/images/hint.gif"
			}
		});
	}
	else if (type == "link")
	{
		element = BX.create("A", {
			props: {href: 'javascript:void(0)'},
			html: '[?]'
		});
	}

	this.PARENT = element;

	BX.bind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
	BX.bind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));

	return this.PARENT;
};

BX.CHint.prototype.Show = function()
{
	this.msover = true;

	if (null != this.timer)
		clearTimeout(this.timer);

	this.timer = setTimeout(BX.proxy(this.__show, this), this.PARAMS.show_timeout);
};

BX.CHint.prototype.Hide = function()
{
	this.msover = false;

	if (null != this.timer)
		clearTimeout(this.timer);

	this.timer = setTimeout(BX.proxy(this.__hide, this), this.PARAMS.hide_timeout);
};

BX.CHint.prototype.__show = function()
{
	if (!this.msover || this.disabled) return;
	if (!this.bInited) this.Init();

	if (this.prepareAdjustPos())
	{
		this.DIV.style.display = 'block';
		this.adjustPos();

		BX.bind(window, 'scroll', BX.proxy(this.__onscroll, this));

		if (this.PARAMS.showOnce)
		{
			this.timer = setTimeout(BX.proxy(this.__hide, this), this.PARAMS.hide_timeout);
		}
	}
};

BX.CHint.prototype.__onscroll = function()
{
	if (!BX.admin || !BX.admin.panel || !BX.admin.panel.isFixed()) return;

	if (this.scrollTimer) clearTimeout(this.scrollTimer);

	this.DIV.style.display = 'none';
	this.scrollTimer = setTimeout(BX.proxy(this.Reopen, this), this.PARAMS.show_timeout);
};

BX.CHint.prototype.Reopen = function()
{
	if (null != this.timer) clearTimeout(this.timer);
	this.timer = setTimeout(BX.proxy(this.__show, this), 50);
};

BX.CHint.prototype.__hide = function()
{
	if (this.msover) return;
	if (!this.bInited) return;

	BX.unbind(window, 'scroll', BX.proxy(this.Reopen, this));

	if (this.PARAMS.showOnce)
	{
		this.Destroy();
	}
	else
	{
		this.DIV.style.display = 'none';
	}
};

BX.CHint.prototype.__hide_immediately = function()
{
	this.msover = false;
	this.__hide();
};

BX.CHint.prototype.Init = function()
{
	this.DIV = document.body.appendChild(BX.create('DIV', {
		props: {className: 'bx-panel-tooltip'},
		style: {display: 'none'},
		children: [
			BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-top-border'},
				html: '<div class="bx-panel-tooltip-corner bx-panel-tooltip-left-corner"></div><div class="bx-panel-tooltip-border"></div><div class="bx-panel-tooltip-corner bx-panel-tooltip-right-corner"></div>'
			}),
			(this.CONTENT = BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-content'},
				children: [
					BX.create('DIV', {
						props: {className: 'bx-panel-tooltip-underlay'},
						children: [
							BX.create('DIV', {props: {className: 'bx-panel-tooltip-underlay-bg'}})
						]
					})
				]
			})),

			BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-bottom-border'},
				html: '<div class="bx-panel-tooltip-corner bx-panel-tooltip-left-corner"></div><div class="bx-panel-tooltip-border"></div><div class="bx-panel-tooltip-corner bx-panel-tooltip-right-corner"></div>'
			})
		]
	}));

	if (this.ID)
	{
		this.CONTENT.insertBefore(BX.create('A', {
			attrs: {href: 'javascript:void(0)'},
			props: {className: 'bx-panel-tooltip-close'},
			events: {click: BX.delegate(this.Close, this)}
		}), this.CONTENT.firstChild)
	}

	if (this.HINT_TITLE)
	{
		this.CONTENT.appendChild(
			BX.create('DIV', {
				props: {className: 'bx-panel-tooltip-title'},
				text: this.HINT_TITLE
			})
		)
	}

	if (this.HINT)
	{
		this.CONTENT_TEXT = this.CONTENT.appendChild(BX.create('DIV', {props: {className: 'bx-panel-tooltip-text'}})).appendChild(BX.create('SPAN', {html: this.HINT}));
	}

	if (this.PARAMS.preventHide)
	{
		BX.bind(this.DIV, 'mouseout', BX.proxy(this.Hide, this));
		BX.bind(this.DIV, 'mouseover', BX.proxy(this.Show, this));
	}

	this.bInited = true;
};

BX.CHint.prototype.setContent = function(content)
{
	this.HINT = content;

	if (this.CONTENT_TEXT)
		this.CONTENT_TEXT.innerHTML = this.HINT;
	else
		this.CONTENT_TEXT = this.CONTENT.appendChild(BX.create('DIV', {props: {className: 'bx-panel-tooltip-text'}})).appendChild(BX.create('SPAN', {html: this.HINT}));
};

BX.CHint.prototype.prepareAdjustPos = function()
{
	this._wnd = {scrollPos: BX.GetWindowScrollPos(),scrollSize:BX.GetWindowScrollSize()};
	return BX.style(this.PARENT, 'display') != 'none';
};

BX.CHint.prototype.getAdjustPos = function()
{
	var res = {}, pos = BX.pos(this.PARENT), min_top = 0;

	res.top = pos.bottom + this.PARAMS.dx;

	if (BX.admin && BX.admin.panel.DIV)
	{
		min_top = BX.admin.panel.DIV.offsetHeight + this.PARAMS.dx;

		if (BX.admin.panel.isFixed())
		{
			min_top += this._wnd.scrollPos.scrollTop;
		}
	}

	if (res.top < min_top)
		res.top = min_top;
	else
	{
		if (res.top + this.DIV.offsetHeight > this._wnd.scrollSize.scrollHeight)
			res.top = pos.top - this.PARAMS.dx - this.DIV.offsetHeight;
	}

	res.left = pos.left;
	if (pos.left < this.PARAMS.dx)
		pos.left = this.PARAMS.dx;
	else
	{
		var floatWidth = this.DIV.offsetWidth;

		var max_left = this._wnd.scrollSize.scrollWidth - floatWidth - this.PARAMS.dx;

		if (res.left > max_left)
			res.left = max_left;
	}

	return res;
};

BX.CHint.prototype.adjustWidth = function()
{
	if (this.bWidthAdjusted) return;

	var w = this.DIV.offsetWidth, h = this.DIV.offsetHeight;

	if (w > this.PARAMS.min_width)
		w = Math.round(Math.sqrt(1.618*w*h));

	if (w < this.PARAMS.min_width)
		w = this.PARAMS.min_width;

	this.DIV.style.width = w + "px";

	if (this._adjustWidthInt)
		clearInterval(this._adjustWidthInt);
	this._adjustWidthInt = setInterval(BX.delegate(this._adjustWidthInterval, this), 5);

	this.bWidthAdjusted = true;
};

BX.CHint.prototype._adjustWidthInterval = function()
{
	if (!this.DIV || this.DIV.style.display == 'none')
		clearInterval(this._adjustWidthInt);

	var
		dW = 20,
		maxWidth = 1500,
		w = this.DIV.offsetWidth,
		w1 = this.CONTENT_TEXT.offsetWidth;

	if (w > 0 && w1 > 0 && w - w1 < dW && w < maxWidth)
	{
		this.DIV.style.width = (w + dW) + "px";
		return;
	}

	clearInterval(this._adjustWidthInt);
};

BX.CHint.prototype.adjustPos = function()
{
	this.adjustWidth();

	var pos = this.getAdjustPos();

	this.DIV.style.top = pos.top + 'px';
	this.DIV.style.left = pos.left + 'px';
};

BX.CHint.prototype.Close = function()
{
	if (this.ID && BX.WindowManager)
		BX.WindowManager.saveWindowOptions(this.ID, {display: 'off'});
	this.__hide_immediately();
	this.Destroy();
};

BX.CHint.prototype.Destroy = function()
{
	if (this.PARENT)
	{
		BX.unbind(this.PARENT, 'mouseover', BX.proxy(this.Show, this));
		BX.unbind(this.PARENT, 'mouseout', BX.proxy(this.Hide, this));
	}

	if (this.DIV)
	{
		BX.unbind(this.DIV, 'mouseover', BX.proxy(this.Show, this));
		BX.unbind(this.DIV, 'mouseout', BX.proxy(this.Hide, this));

		BX.cleanNode(this.DIV, true);
	}
};

BX.CHint.prototype.enable = function(){this.disabled = false;};
BX.CHint.prototype.disable = function(){this.__hide_immediately(); this.disabled = true;};

/* ready */
if (document.addEventListener)
{
	__readyHandler = function()
	{
		document.removeEventListener("DOMContentLoaded", __readyHandler, false);
		runReady();
	}
}
else if (document.attachEvent)
{
	__readyHandler = function()
	{
		if (document.readyState === "complete")
		{
			document.detachEvent("onreadystatechange", __readyHandler);
			runReady();
		}
	}
}

function bindReady()
{
	if (!readyBound)
	{
		readyBound = true;

		if (document.readyState === "complete")
		{
			return runReady();
		}

		if (document.addEventListener)
		{
			document.addEventListener("DOMContentLoaded", __readyHandler, false);
			window.addEventListener("load", runReady, false);
		}
		else if (document.attachEvent) // IE
		{
			document.attachEvent("onreadystatechange", __readyHandler);
			window.attachEvent("onload", runReady);

			var toplevel = false;
			try {toplevel = (window.frameElement == null);} catch(e) {}

			if (document.documentElement.doScroll && toplevel)
				doScrollCheck();
		}
	}

	return null;
}


function runReady()
{
	if (!BX.isReady)
	{
		if (!document.body)
			return setTimeout(runReady, 15);

		BX.isReady = true;


		if (readyList && readyList.length > 0)
		{
			var fn, i = 0;
			while (readyList && (fn = readyList[i++]))
			{
				try{
					fn.call(document);
				}
				catch(e){
					BX.debug('BX.ready error: ', e);
				}
			}

			readyList = null;
		}
		// TODO: check ready handlers binded some other way;
	}
	return null;
}

// hack for IE
function doScrollCheck()
{
	if (BX.isReady)
		return;

	try {document.documentElement.doScroll("left");} catch( error ) {setTimeout(doScrollCheck, 1); return;}

	runReady();
}
/* \ready */

function _adjustWait()
{
	if (!this.bxmsg) return;

	var arContainerPos = BX.pos(this),
		div_top = arContainerPos.top;

	if (div_top < BX.GetDocElement().scrollTop)
		div_top = BX.GetDocElement().scrollTop + 5;

	this.bxmsg.style.top = (div_top + 5) + 'px';

	if (this == BX.GetDocElement())
	{
		this.bxmsg.style.right = '5px';
	}
	else
	{
		this.bxmsg.style.left = (arContainerPos.right - this.bxmsg.offsetWidth - 5) + 'px';
	}
}

function _checkDisplay(ob, displayType)
{
	if (typeof displayType != 'undefined')
		ob.BXDISPLAY = displayType;

	var d = ob.style.display || BX.style(ob, 'display');
	if (d != 'none')
	{
		ob.BXDISPLAY = ob.BXDISPLAY || d;
		return true;
	}
	else
	{
		ob.BXDISPLAY = ob.BXDISPLAY || 'block';
		return false;
	}
}

function _processTpl(tplNode, cb, bKillTpl)
{
	if (tplNode)
	{
		if (bKillTpl)
			tplNode.parentNode.removeChild(tplNode);

		var res = {}, nodes = BX.findChildren(tplNode, {attribute: 'data-role'}, true);

		for (var i = 0, l = nodes.length; i < l; i++)
		{
			res[nodes[i].getAttribute('data-role')] = nodes[i];
		}

		cb.apply(tplNode, [res]);
	}
}

function _checkNode(obj, params)
{
	params = params || {};

	if (BX.type.isFunction(params))
		return params.call(window, obj);

	if (!params.allowTextNodes && !BX.type.isElementNode(obj))
		return false;
	var i,j,len;
	for (i in params)
	{
		if(params.hasOwnProperty(i))
		{
			switch(i)
			{
				case 'tag':
				case 'tagName':
					if (BX.type.isString(params[i]))
					{
						if (obj.tagName.toUpperCase() != params[i].toUpperCase())
							return false;
					}
					else if (params[i] instanceof RegExp)
					{
						if (!params[i].test(obj.tagName))
							return false;
					}
				break;

				case 'class':
				case 'className':
					if (BX.type.isString(params[i]))
					{
						if (!BX.hasClass(obj, params[i]))
							return false;
					}
					else if (params[i] instanceof RegExp)
					{
						if (!BX.type.isString(obj.className) || !params[i].test(obj.className))
							return false;
					}
				break;

				case 'attr':
				case 'attribute':
					if (BX.type.isString(params[i]))
					{
						if (!obj.getAttribute(params[i]))
							return false;
					}
					else if (BX.type.isArray(params[i]))
					{
						for (j = 0, len = params[i].length; j < len; j++)
						{
							if (params[i] && !obj.getAttribute(params[i]))
								return false;
						}
					}
					else
					{
						for (j in params[i])
						{
							if(params[i].hasOwnProperty(j))
							{
								var q = obj.getAttribute(j);
								if (params[i][j] instanceof RegExp)
								{
									if (!BX.type.isString(q) || !params[i][j].test(q))
									{
										return false;
									}
								}
								else
								{
									if (q != '' + params[i][j])
									{
										return false;
									}
								}
							}
						}
					}
				break;

				case 'property':
					if (BX.type.isString(params[i]))
					{
						if (!obj[params[i]])
							return false;
					}
					else if (BX.type.isArray(params[i]))
					{
						for (j = 0, len = params[i].length; j < len; j++)
						{
							if (params[i] && !obj[params[i]])
								return false;
						}
					}
					else
					{
						for (j in params[i])
						{
							if (BX.type.isString(params[i][j]))
							{
								if (obj[j] != params[i][j])
									return false;
							}
							else if (params[i][j] instanceof RegExp)
							{
								if (!BX.type.isString(obj[j]) || !params[i][j].test(obj[j]))
									return false;
							}
						}
					}
				break;

				case 'callback':
					return params[i](obj);
			}
		}
	}

	return true;
}

function _isCssLoaded(fileSrc)
{
	if(!cssInit)
	{
		var linksCol = document.getElementsByTagName('LINK'), links = [];

		if(!!linksCol && linksCol.length > 0)
		{
			for(var i = 0; i < linksCol.length; i++)
			{
				var href = linksCol[i].getAttribute('href');
				if (BX.type.isNotEmptyString(href))
				{
					cssList.push(BX.getCSSPath(href));
				}
			}
		}
		cssInit = true;
	}

	return (BX.util.in_array(fileSrc, cssList));
}

function _getTemplateLink(head)
{
	var findLink = function(tag)
	{
		var links = head.getElementsByTagName(tag);
		for (var i = 0, length = links.length; i < length; i++)
		{
			var templateStyle = links[i].getAttribute("data-template-style");
			if (BX.type.isNotEmptyString(templateStyle) && templateStyle == "true")
			{
				return links[i];
			}
		}

		return null;
	};

	var link = findLink("link");
	if (link === null)
	{
		link = findLink("style");
	}

	return link;
}

/********* Check for currently loaded core scripts ***********/
function _isScriptLoaded(fileSrc)
{
	if(!jsInit)
	{
		var scriptCol = document.getElementsByTagName('script'), script = [];

		if(!!scriptCol && scriptCol.length > 0)
		{
			for(var i=0; i<scriptCol.length; i++)
			{
				var src = scriptCol[i].getAttribute('src');

				if (BX.type.isNotEmptyString(src))
				{
					jsList.push(BX.getJSPath(src));
				}
			}
		}
		jsInit = true;
	}

	return BX.util.in_array(fileSrc, jsList);
}

/* garbage collector */
function Trash()
{
	var i,len;

	for (i = 0, len = garbageCollectors.length; i<len; i++)
	{
		try {
			garbageCollectors[i].callback.apply(garbageCollectors[i].context || window);
			delete garbageCollectors[i];
			garbageCollectors[i] = null;
		} catch (e) {}
	}

	try {BX.unbindAll();} catch(e) {}
/*
	for (i = 0, len = proxyList.length; i < len; i++)
	{
		try {
			delete proxyList[i];
			proxyList[i] = null;
		} catch (e) {}
	}
*/
}

if(window.attachEvent) // IE
	window.attachEvent("onunload", Trash);
else if(window.addEventListener) // Gecko / W3C
	window.addEventListener('unload', Trash, false);
else
	window.onunload = Trash;
/* \garbage collector */

// set empty ready handler
BX(BX.DoNothing);
window.BX = BX;
BX.browser.addGlobalClass();
BX.browser.addGlobalFeatures(["boxShadow", "borderRadius", "flexWrap", "boxDirection", "transition", "transform"])
})(window);

/* End */
;
; /* Start:/bitrix/js/main/core/core_ajax.js*/
;(function(window){

if (window.BX.ajax)
	return;

var
	BX = window.BX,

	tempDefaultConfig = {},
	defaultConfig = {
		method: 'GET', // request method: GET|POST
		dataType: 'html', // type of data loading: html|json|script
		timeout: 0, // request timeout in seconds. 0 for browser-default
		async: true, // whether request is asynchronous or not
		processData: true, // any data processing is disabled if false, only callback call
		scriptsRunFirst: false, // whether to run _all_ found scripts before onsuccess call. script tag can have an attribute "bxrunfirst" to turn  this flag on only for itself
		emulateOnload: true,
		skipAuthCheck: false, // whether to check authorization failure (SHOUD be set to true for CORS requests)
		start: true, // send request immediately (if false, request can be started manually via XMLHttpRequest object returned)
		cache: true, // whether NOT to add random addition to URL
		preparePost: true, // whether set Content-Type x-www-form-urlencoded in POST
		headers: false, // add additional headers, example: [{'name': 'If-Modified-Since', 'value': 'Wed, 15 Aug 2012 08:59:08 GMT'}, {'name': 'If-None-Match', 'value': '0'}]
		lsTimeout: 30, //local storage data TTL. useless without lsId.
		lsForce: false //wheter to force query instead of using localStorage data. useless without lsId.
/*
other parameters:
	url: url to get/post
	data: data to post
	onsuccess: successful request callback. BX.proxy may be used.
	onfailure: request failure callback. BX.proxy may be used.

	lsId: local storage id - for constantly updating queries which can communicate via localStorage. core_ls.js needed

any of the default parameters can be overridden. defaults can be changed by BX.ajax.Setup() - for all further requests!
*/
	},
	ajax_session = null,
	loadedScripts = {},
	loadedScriptsQueue = [],
	r = {
		'url_utf': /[^\034-\254]+/g,
		'script_self': /\/bitrix\/js\/main\/core\/core(_ajax)*.js$/i,
		'script_self_window': /\/bitrix\/js\/main\/core\/core_window.js$/i,
		'script_self_admin': /\/bitrix\/js\/main\/core\/core_admin.js$/i,
		'script_onload': /window.onload/g
	};

// low-level method
BX.ajax = function(config)
{
	var status, data;

	if (!config || !config.url || !BX.type.isString(config.url))
	{
		return false;
	}

	for (var i in tempDefaultConfig)
		if (typeof (config[i]) == "undefined") config[i] = tempDefaultConfig[i];

	tempDefaultConfig = {};

	for (i in defaultConfig)
		if (typeof (config[i]) == "undefined") config[i] = defaultConfig[i];

	config.method = config.method.toUpperCase();

	if (!BX.localStorage)
		config.lsId = null;

	if (BX.browser.IsIE())
	{
		var result = r.url_utf.exec(config.url);
		if (result)
		{
			do
			{
				config.url = config.url.replace(result, BX.util.urlencode(result));
				result = r.url_utf.exec(config.url);
			} while (result);
		}
	}

	if(config.dataType == 'json')
		config.emulateOnload = false;

	if (!config.cache && config.method == 'GET')
		config.url = BX.ajax._uncache(config.url);

	if (config.method == 'POST' && config.preparePost)
	{
		config.data = BX.ajax.prepareData(config.data);
	}

	var bXHR = true;
	if (config.lsId && !config.lsForce)
	{
		var v = BX.localStorage.get('ajax-' + config.lsId);
		if (v !== null)
		{
			bXHR = false;

			var lsHandler = function(lsData) {
				if (lsData.key == 'ajax-' + config.lsId && lsData.value != 'BXAJAXWAIT')
				{
					var data = lsData.value,
						bRemove = !!lsData.oldValue && data == null;
					if (!bRemove)
						BX.ajax.__run(config, data);
					else if (config.onfailure)
						config.onfailure("timeout");

					BX.removeCustomEvent('onLocalStorageChange', lsHandler);
				}
			};

			if (v == 'BXAJAXWAIT')
			{
				BX.addCustomEvent('onLocalStorageChange', lsHandler);
			}
			else
			{
				setTimeout(function() {lsHandler({key: 'ajax-' + config.lsId, value: v})}, 10);
			}
		}
	}

	if (bXHR)
	{
		config.xhr = BX.ajax.xhr();
		if (!config.xhr) return;

		if (config.lsId)
		{
			BX.localStorage.set('ajax-' + config.lsId, 'BXAJAXWAIT', config.lsTimeout);
		}

		config.xhr.open(config.method, config.url, config.async);

		if (!config.skipBxHeader && !BX.ajax.isCrossDomain(config.url))
		{
			config.xhr.setRequestHeader('Bx-ajax', 'true');
		}

		if (config.method == 'POST' && config.preparePost)
		{
			config.xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		}
		if (typeof(config.headers) == "object")
		{
			for (i = 0; i < config.headers.length; i++)
				config.xhr.setRequestHeader(config.headers[i].name, config.headers[i].value);
		}

		var bRequestCompleted = false;
		var onreadystatechange = config.xhr.onreadystatechange = function(additional)
		{
			if (bRequestCompleted)
				return;

			if (additional === 'timeout')
			{
				if (config.onfailure)
				{
					config.onfailure("timeout");
				}

				BX.onCustomEvent(config.xhr, 'onAjaxFailure', ['timeout', '', config]);

				config.xhr.onreadystatechange = BX.DoNothing;
				config.xhr.abort();

				if (config.async)
				{
					config.xhr = null;
				}
			}
			else
			{
				if (config.xhr.readyState == 4 || additional == 'run')
				{
					status = BX.ajax.xhrSuccess(config.xhr) ? "success" : "error";
					bRequestCompleted = true;
					config.xhr.onreadystatechange = BX.DoNothing;

					if (status == 'success')
					{
						var authHeader = (!!config.skipAuthCheck || BX.ajax.isCrossDomain(config.url))
							? false
							: config.xhr.getResponseHeader('X-Bitrix-Ajax-Status');

						if(!!authHeader && authHeader == 'Authorize')
						{
							if (config.onfailure)
							{
								config.onfailure("auth", config.xhr.status);
							}

							BX.onCustomEvent(config.xhr, 'onAjaxFailure', ['auth', config.xhr.status, config]);
						}
						else
						{
							var data = config.xhr.responseText;

							if (config.lsId)
							{
								BX.localStorage.set('ajax-' + config.lsId, data, config.lsTimeout);
							}

							BX.ajax.__run(config, data);
						}
					}
					else
					{
						if (config.onfailure)
						{
							config.onfailure("status", config.xhr.status);
						}

						BX.onCustomEvent(config.xhr, 'onAjaxFailure', ['status', config.xhr.status, config]);
					}

					if (config.async)
					{
						config.xhr = null;
					}
				}
			}
		};

		if (config.async && config.timeout > 0)
		{
			setTimeout(function() {
				if (config.xhr && !bRequestCompleted)
				{
					onreadystatechange("timeout");
				}
			}, config.timeout * 1000);
		}

		if (config.start)
		{
			config.xhr.send(config.data);

			if (!config.async)
			{
				onreadystatechange('run');
			}
		}

		return config.xhr;
	}
};

BX.ajax.xhr = function()
{
	if (window.XMLHttpRequest)
	{
		try {return new XMLHttpRequest();} catch(e){}
	}
	else if (window.ActiveXObject)
	{
		try { return new window.ActiveXObject("Msxml2.XMLHTTP.6.0"); }
			catch(e) {}
		try { return new window.ActiveXObject("Msxml2.XMLHTTP.3.0"); }
			catch(e) {}
		try { return new window.ActiveXObject("Msxml2.XMLHTTP"); }
			catch(e) {}
		try { return new window.ActiveXObject("Microsoft.XMLHTTP"); }
			catch(e) {}
		throw new Error("This browser does not support XMLHttpRequest.");
	}

	return null;
};

BX.ajax.isCrossDomain = function(url, location)
{
	location = location || window.location;

	//Relative URL gets a current protocol
	if (url.indexOf("//") === 0)
	{
		url = location.protocol + url;
	}

	//Fast check
	if (url.indexOf("http") !== 0)
	{
		return false;
	}

	var link = window.document.createElement("a");
	link.href = url;

	return  link.protocol !== location.protocol ||
			link.hostname !== location.hostname ||
			BX.ajax.getHostPort(link.protocol, link.host) !== BX.ajax.getHostPort(location.protocol, location.host);
};

BX.ajax.getHostPort = function(protocol, host)
{
	var match = /:(\d+)$/.exec(host);
	if (match)
	{
		return match[1];
	}
	else
	{
		if (protocol === "http:")
		{
			return "80";
		}
		else if (protocol === "https:")
		{
			return "443";
		}
	}

	return "";
};

BX.ajax.__prepareOnload = function(scripts)
{
	if (scripts.length > 0)
	{
		BX.ajax['onload_' + ajax_session] = null;

		for (var i=0,len=scripts.length;i<len;i++)
		{
			if (scripts[i].isInternal)
			{
				scripts[i].JS = scripts[i].JS.replace(r.script_onload, 'BX.ajax.onload_' + ajax_session);
			}
		}
	}

	BX.CaptureEventsGet();
	BX.CaptureEvents(window, 'load');
};

BX.ajax.__runOnload = function()
{
	if (null != BX.ajax['onload_' + ajax_session])
	{
		BX.ajax['onload_' + ajax_session].apply(window);
		BX.ajax['onload_' + ajax_session] = null;
	}

	var h = BX.CaptureEventsGet();

	if (h)
	{
		for (var i=0; i<h.length; i++)
			h[i].apply(window);
	}
};

BX.ajax.__run = function(config, data)
{
	if (!config.processData)
	{
		if (config.onsuccess)
		{
			config.onsuccess(data);
		}

		BX.onCustomEvent(config.xhr, 'onAjaxSuccess', [data, config]);
	}
	else
	{
		data = BX.ajax.processRequestData(data, config);
	}
};


BX.ajax._onParseJSONFailure = function(data)
{
	this.jsonFailure = true;
	this.jsonResponse = data;
	this.jsonProactive = /^\[WAF\]/.test(data);
};

BX.ajax.processRequestData = function(data, config)
{
	var result, scripts = [], styles = [];
	switch (config.dataType.toUpperCase())
	{
		case 'JSON':
			BX.addCustomEvent(config.xhr, 'onParseJSONFailure', BX.proxy(BX.ajax._onParseJSONFailure, config));
			result = BX.parseJSON(data, config.xhr);
			BX.removeCustomEvent(config.xhr, 'onParseJSONFailure', BX.proxy(BX.ajax._onParseJSONFailure, config));

		break;
		case 'SCRIPT':
			scripts.push({"isInternal": true, "JS": data, bRunFirst: config.scriptsRunFirst});
			config.processScriptsConsecutive = true;
			result = data;
		break;

		default: // HTML
			var ob = BX.processHTML(data, config.scriptsRunFirst);
			result = ob.HTML; scripts = ob.SCRIPT; styles = ob.STYLE;
		break;
	}

	var bSessionCreated = false;
	if (null == ajax_session)
	{
		ajax_session = parseInt(Math.random() * 1000000);
		bSessionCreated = true;
	}

	if (styles.length > 0)
		BX.loadCSS(styles);

	if (config.emulateOnload)
			BX.ajax.__prepareOnload(scripts);

	var cb = BX.DoNothing;
	if(config.emulateOnload || bSessionCreated)
	{
		cb = BX.defer(function()
		{
			if (config.emulateOnload)
				BX.ajax.__runOnload();
			if (bSessionCreated)
				ajax_session = null;
			BX.onCustomEvent(config.xhr, 'onAjaxSuccessFinish', [config]);
		});
	}

	try
	{
		if (!!config.jsonFailure)
		{
			throw {type: 'json_failure', data: config.jsonResponse, bProactive: config.jsonProactive};
		}

		config.scripts = scripts;

		BX.ajax.processScripts(config.scripts, true);


		if (config.onsuccess)
		{
			config.onsuccess(result);
		}

		BX.onCustomEvent(config.xhr, 'onAjaxSuccess', [result, config]);

		if(!config.processScriptsConsecutive)
		{
			BX.ajax.processScripts(config.scripts, false, cb);
		}
		else
		{
			BX.ajax.processScriptsConsecutive(config.scripts, false);
			cb();
		}
	}
	catch (e)
	{
		if (config.onfailure)
			config.onfailure("processing", e);
		BX.onCustomEvent(config.xhr, 'onAjaxFailure', ['processing', e, config]);
	}
};

BX.ajax.processScripts = function(scripts, bRunFirst, cb)
{
	var scriptsExt = [], scriptsInt = '';

	cb = cb || BX.DoNothing;

	for (var i = 0, length = scripts.length; i < length; i++)
	{
		if (typeof bRunFirst != 'undefined' && bRunFirst != !!scripts[i].bRunFirst)
			continue;

		if (scripts[i].isInternal)
			scriptsInt += ';' + scripts[i].JS;
		else
			scriptsExt.push(scripts[i].JS);
	}

	scriptsExt = BX.util.array_unique(scriptsExt);

	var l = scriptsExt.length;
	var l1 = l;
	var f = scriptsInt.length > 0 ? function() { BX.evalGlobal(scriptsInt); } : BX.DoNothing;

	if (l > 0)
	{
		var c = function() {
			if (--l1 <= 0)
			{
				f();
				cb();
				f = BX.DoNothing;
			}
		};

		for (i=0; i < l; i++)
		{
			BX.loadScript(scriptsExt[i], c);
		}
	}
	else
	{
		//f();BX.defer(cb)();
		f();
		cb();
	}
};

BX.ajax.processScriptsConsecutive = function(scripts, bRunFirst)
{
	for (var i = 0, length = scripts.length; i < length; i++)
	{
		if (null != bRunFirst && bRunFirst != !!scripts[i].bRunFirst)
			continue;

		if (scripts[i].isInternal)
		{
			BX.evalGlobal(scripts[i].JS);
		}
		else
		{
			BX.ajax.loadScriptAjax([scripts[i].JS]);
		}
	}
};

// TODO: extend this function to use with any data objects or forms
BX.ajax.prepareData = function(arData, prefix)
{
	var data = '';
	if (BX.type.isString(arData))
		data = arData;
	else if (null != arData)
	{
		for(var i in arData)
		{
			if (arData.hasOwnProperty(i))
			{
				if (data.length > 0)
					data += '&';
				var name = BX.util.urlencode(i);
				if(prefix)
					name = prefix + '[' + name + ']';
				if(typeof arData[i] == 'object')
					data += BX.ajax.prepareData(arData[i], name);
				else
					data += name + '=' + BX.util.urlencode(arData[i]);
			}
		}
	}
	return data;
};

BX.ajax.xhrSuccess = function(xhr)
{
	return (xhr.status >= 200 && xhr.status < 300) || xhr.status === 304 || xhr.status === 1223 || xhr.status === 0;
};

BX.ajax.Setup = function(config, bTemp)
{
	bTemp = !!bTemp;

	for (var i in config)
	{
		if (bTemp)
			tempDefaultConfig[i] = config[i];
		else
			defaultConfig[i] = config[i];
	}
};

BX.ajax.replaceLocalStorageValue = function(lsId, data, ttl)
{
	if (!!BX.localStorage)
		BX.localStorage.set('ajax-' + lsId, data, ttl);
};


BX.ajax._uncache = function(url)
{
	return url + ((url.indexOf('?') !== -1 ? "&" : "?") + '_=' + (new Date()).getTime());
};

/* simple interface */
BX.ajax.get = function(url, data, callback)
{
	if (BX.type.isFunction(data))
	{
		callback = data;
		data = '';
	}

	data = BX.ajax.prepareData(data);

	if (data)
	{
		url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
		data = '';
	}

	return BX.ajax({
		'method': 'GET',
		'dataType': 'html',
		'url': url,
		'data':  '',
		'onsuccess': callback
	});
};

BX.ajax.getCaptcha = function(callback)
{
	return BX.ajax.loadJSON('/bitrix/tools/ajax_captcha.php', callback);
};

BX.ajax.insertToNode = function(url, node)
{
	node = BX(node);
	if (!!node)
	{
		BX.onCustomEvent('onAjaxInsertToNode', [{url: url, node: node}]);

		var show = null;
		if (!tempDefaultConfig.denyShowWait)
		{
			show = BX.showWait(node);
			delete tempDefaultConfig.denyShowWait;
		}

		return BX.ajax.get(url, function(data) {
			node.innerHTML = data;
			BX.closeWait(node, show);
		});
	}
};

BX.ajax.post = function(url, data, callback)
{
	data = BX.ajax.prepareData(data);

	return BX.ajax({
		'method': 'POST',
		'dataType': 'html',
		'url': url,
		'data':  data,
		'onsuccess': callback
	});
};

/* load and execute external file script with onload emulation */
BX.ajax.loadScriptAjax = function(script_src, callback, bPreload)
{
	if (BX.type.isArray(script_src))
	{
		for (var i=0,len=script_src.length;i<len;i++)
		{
			BX.ajax.loadScriptAjax(script_src[i], callback, bPreload);
		}
	}
	else
	{
		var script_src_test = script_src.replace(/\.js\?.*/, '.js');

		if (r.script_self.test(script_src_test)) return;
		if (r.script_self_window.test(script_src_test) && BX.CWindow) return;
		if (r.script_self_admin.test(script_src_test) && BX.admin) return;

		if (typeof loadedScripts[script_src_test] == 'undefined')
		{
			if (!!bPreload)
			{
				loadedScripts[script_src_test] = '';
				return BX.loadScript(script_src);
			}
			else
			{
				return BX.ajax({
					url: script_src,
					method: 'GET',
					dataType: 'script',
					processData: true,
					emulateOnload: false,
					scriptsRunFirst: true,
					async: false,
					start: true,
					onsuccess: function(result) {
						loadedScripts[script_src_test] = result;
						if (callback)
							callback(result);
					}
				});
			}
		}
		else if (callback)
		{
			callback(loadedScripts[script_src_test]);
		}
	}
};

/* non-xhr loadings */
BX.ajax.loadJSON = function(url, data, callback, callback_failure)
{
	if (BX.type.isFunction(data))
	{
		callback_failure = callback;
		callback = data;
		data = '';
	}

	data = BX.ajax.prepareData(data);

	if (data)
	{
		url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
		data = '';
	}

	return BX.ajax({
		'method': 'GET',
		'dataType': 'json',
		'url': url,
		'onsuccess': callback,
		'onfailure': callback_failure
	});
};

/*
arObs = [{
	url: url,
	type: html|script|json|css,
	callback: function
}]
*/
BX.ajax.load = function(arObs, callback)
{
	if (!BX.type.isArray(arObs))
		arObs = [arObs];

	var cnt = 0;

	if (!BX.type.isFunction(callback))
		callback = BX.DoNothing;

	var handler = function(data)
		{
			if (BX.type.isFunction(this.callback))
				this.callback(data);

			if (++cnt >= len)
				callback();
		};

	for (var i = 0, len = arObs.length; i<len; i++)
	{
		switch(arObs[i].type.toUpperCase())
		{
			case 'SCRIPT':
				BX.loadScript([arObs[i].url], BX.proxy(handler, arObs[i]));
			break;
			case 'CSS':
				BX.loadCSS([arObs[i].url]);

				if (++cnt >= len)
					callback();
			break;
			case 'JSON':
				BX.ajax.loadJSON(arObs[i].url, BX.proxy(handler, arObs[i]));
			break;

			default:
				BX.ajax.get(arObs[i].url, '', BX.proxy(handler, arObs[i]));
			break;
		}
	}
};

/* ajax form sending */
BX.ajax.submit = function(obForm, callback)
{
	if (!obForm.target)
	{
		if (null == obForm.BXFormTarget)
		{
			var frame_name = 'formTarget_' + Math.random();
			obForm.BXFormTarget = document.body.appendChild(BX.create('IFRAME', {
				props: {
					name: frame_name,
					id: frame_name,
					src: 'javascript:void(0)'
				},
				style: {
					display: 'none'
				}
			}));
		}

		obForm.target = obForm.BXFormTarget.name;
	}

	obForm.BXFormCallback = callback;
	BX.bind(obForm.BXFormTarget, 'load', BX.proxy(BX.ajax._submit_callback, obForm));

	BX.submit(obForm);

	return false;
};

BX.ajax.submitComponentForm = function(obForm, container, bWait)
{
	if (!obForm.target)
	{
		if (null == obForm.BXFormTarget)
		{
			var frame_name = 'formTarget_' + Math.random();
			obForm.BXFormTarget = document.body.appendChild(BX.create('IFRAME', {
				props: {
					name: frame_name,
					id: frame_name,
					src: 'javascript:void(0)'
				},
				style: {
					display: 'none'
				}
			}));
		}

		obForm.target = obForm.BXFormTarget.name;
	}

	if (!!bWait)
		var w = BX.showWait(container);

	obForm.BXFormCallback = function(d) {
		if (!!bWait)
			BX.closeWait(w);

		var callOnload = function(){
			if(!!window.bxcompajaxframeonload)
			{
				setTimeout(function(){window.bxcompajaxframeonload();window.bxcompajaxframeonload=null;}, 10);
			}
		};

		BX(container).innerHTML = d;
		BX.onCustomEvent('onAjaxSuccess', [null,null,callOnload]);
	};

	BX.bind(obForm.BXFormTarget, 'load', BX.proxy(BX.ajax._submit_callback, obForm));

	return true;
};

// func will be executed in form context
BX.ajax._submit_callback = function()
{
	//opera and IE8 triggers onload event even on empty iframe
	try
	{
		if(this.BXFormTarget.contentWindow.location.href.indexOf('http') != 0)
			return;
	} catch (e) {
		return;
	}

	if (this.BXFormCallback)
		this.BXFormCallback.apply(this, [this.BXFormTarget.contentWindow.document.body.innerHTML]);

	BX.unbindAll(this.BXFormTarget);
};

// TODO: currently in window extension. move it here.
BX.ajax.submitAjax = function(obForm, callback)
{

};

BX.ajax.UpdatePageData = function (arData)
{
	if (arData.TITLE)
		BX.ajax.UpdatePageTitle(arData.TITLE);
	if (arData.NAV_CHAIN)
		BX.ajax.UpdatePageNavChain(arData.NAV_CHAIN);
	if (arData.CSS && arData.CSS.length > 0)
		BX.loadCSS(arData.CSS);
	if (arData.SCRIPTS && arData.SCRIPTS.length > 0)
	{
		var f = function(result,config,cb){

			if(!!config && BX.type.isArray(config.scripts))
			{
				for(var i=0,l=arData.SCRIPTS.length;i<l;i++)
				{
					config.scripts.push({isInternal:false,JS:arData.SCRIPTS[i]});
				}
			}
			else
			{
				BX.loadScript(arData.SCRIPTS,cb);
			}

			BX.removeCustomEvent('onAjaxSuccess',f);
		};
		BX.addCustomEvent('onAjaxSuccess',f);
	}
	else
	{
		var f1 = function(result,config,cb){
			if(BX.type.isFunction(cb))
			{
				cb();
			}
			BX.removeCustomEvent('onAjaxSuccess',f1);
		};
		BX.addCustomEvent('onAjaxSuccess', f1);
	}
};

BX.ajax.UpdatePageTitle = function(title)
{
	var obTitle = BX('pagetitle');
	if (obTitle)
	{
		obTitle.removeChild(obTitle.firstChild);
		if (!obTitle.firstChild)
			obTitle.appendChild(document.createTextNode(title));
		else
			obTitle.insertBefore(document.createTextNode(title), obTitle.firstChild);
	}

	document.title = title;
};

BX.ajax.UpdatePageNavChain = function(nav_chain)
{
	var obNavChain = BX('navigation');
	if (obNavChain)
	{
		obNavChain.innerHTML = nav_chain;
	}
};

/* user options handling */
BX.userOptions = {
	options: null,
	bSend: false,
	delay: 5000
};

BX.userOptions.save = function(sCategory, sName, sValName, sVal, bCommon)
{
	if (null == BX.userOptions.options)
		BX.userOptions.options = {};

	bCommon = !!bCommon;
	BX.userOptions.options[sCategory+'.'+sName+'.'+sValName] = [sCategory, sName, sValName, sVal, bCommon];

	var sParam = BX.userOptions.__get();
	if (sParam != '')
		document.cookie = BX.message('COOKIE_PREFIX')+"_LAST_SETTINGS=" + sParam + "&sessid="+BX.bitrix_sessid()+"; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/;";

	if(!BX.userOptions.bSend)
	{
		BX.userOptions.bSend = true;
		setTimeout(function(){BX.userOptions.send(null)}, BX.userOptions.delay);
	}
};

BX.userOptions.send = function(callback)
{
	var sParam = BX.userOptions.__get();
	BX.userOptions.options = null;
	BX.userOptions.bSend = false;

	if (sParam != '')
	{
		document.cookie = BX.message('COOKIE_PREFIX') + "_LAST_SETTINGS=; path=/;";
		BX.ajax({
			'method': 'GET',
			'dataType': 'html',
			'processData': false,
			'cache': false,
			'url': '/bitrix/admin/user_options.php?'+sParam+'&sessid='+BX.bitrix_sessid(),
			'onsuccess': callback
		});
	}
};

BX.userOptions.del = function(sCategory, sName, bCommon, callback)
{
	BX.ajax.get('/bitrix/admin/user_options.php?action=delete&c='+sCategory+'&n='+sName+(bCommon == true? '&common=Y':'')+'&sessid='+BX.bitrix_sessid(), callback);
};

BX.userOptions.__get = function()
{
	if (!BX.userOptions.options) return '';

	var sParam = '', n = -1, prevParam = '', aOpt, i;

	for (i in BX.userOptions.options)
	{
		if(BX.userOptions.options.hasOwnProperty(i))
		{
			aOpt = BX.userOptions.options[i];

			if (prevParam != aOpt[0]+'.'+aOpt[1])
			{
				n++;
				sParam += '&p['+n+'][c]='+BX.util.urlencode(aOpt[0]);
				sParam += '&p['+n+'][n]='+BX.util.urlencode(aOpt[1]);
				if (aOpt[4] == true)
					sParam += '&p['+n+'][d]=Y';
				prevParam = aOpt[0]+'.'+aOpt[1];
			}

			sParam += '&p['+n+'][v]['+BX.util.urlencode(aOpt[2])+']='+BX.util.urlencode(aOpt[3]);
		}
	}

	return sParam.substr(1);
};

BX.ajax.history = {
	expected_hash: '',

	obParams: null,

	obFrame: null,
	obImage: null,

	obTimer: null,

	bInited: false,
	bHashCollision: false,
	bPushState: !!(history.pushState && BX.type.isFunction(history.pushState)),

	startState: null,

	init: function(obParams)
	{
		if (BX.ajax.history.bInited)
			return;

		this.obParams = obParams;
		var obCurrentState = this.obParams.getState();

		if (BX.ajax.history.bPushState)
		{
			BX.ajax.history.expected_hash = window.location.pathname;
			if (window.location.search)
				BX.ajax.history.expected_hash += window.location.search;

			BX.ajax.history.put(obCurrentState, BX.ajax.history.expected_hash, '', true);
			// due to some strange thing, chrome calls popstate event on page start. so we should delay it
			setTimeout(function(){BX.bind(window, 'popstate', BX.ajax.history.__hashListener);}, 500);
		}
		else
		{
			BX.ajax.history.expected_hash = window.location.hash;

			if (!BX.ajax.history.expected_hash || BX.ajax.history.expected_hash == '#')
				BX.ajax.history.expected_hash = '__bx_no_hash__';

			jsAjaxHistoryContainer.put(BX.ajax.history.expected_hash, obCurrentState);
			BX.ajax.history.obTimer = setTimeout(BX.ajax.history.__hashListener, 500);

			if (BX.browser.IsIE())
			{
				BX.ajax.history.obFrame = document.createElement('IFRAME');
				BX.hide_object(BX.ajax.history.obFrame);

				document.body.appendChild(BX.ajax.history.obFrame);

				BX.ajax.history.obFrame.contentWindow.document.open();
				BX.ajax.history.obFrame.contentWindow.document.write(BX.ajax.history.expected_hash);
				BX.ajax.history.obFrame.contentWindow.document.close();
			}
			else if (BX.browser.IsOpera())
			{
				BX.ajax.history.obImage = document.createElement('IMG');
				BX.hide_object(BX.ajax.history.obImage);

				document.body.appendChild(BX.ajax.history.obImage);

				BX.ajax.history.obImage.setAttribute('src', 'javascript:location.href = \'javascript:BX.ajax.history.__hashListener();\';');
			}
		}

		BX.ajax.history.bInited = true;
	},

	__hashListener: function(e)
	{
		e = e || window.event || {state:false};

		if (BX.ajax.history.bPushState)
		{
			BX.ajax.history.obParams.setState(e.state||BX.ajax.history.startState);
		}
		else
		{
			if (BX.ajax.history.obTimer)
			{
				window.clearTimeout(BX.ajax.history.obTimer);
				BX.ajax.history.obTimer = null;
			}

			var current_hash;
			if (null != BX.ajax.history.obFrame)
				current_hash = BX.ajax.history.obFrame.contentWindow.document.body.innerText;
			else
				current_hash = window.location.hash;

			if (!current_hash || current_hash == '#')
				current_hash = '__bx_no_hash__';

			if (current_hash.indexOf('#') == 0)
				current_hash = current_hash.substring(1);

			if (current_hash != BX.ajax.history.expected_hash)
			{
				var state = jsAjaxHistoryContainer.get(current_hash);
				if (state)
				{
					BX.ajax.history.obParams.setState(state);

					BX.ajax.history.expected_hash = current_hash;
					if (null != BX.ajax.history.obFrame)
					{
						var __hash = current_hash == '__bx_no_hash__' ? '' : current_hash;
						if (window.location.hash != __hash && window.location.hash != '#' + __hash)
							window.location.hash = __hash;
					}
				}
			}

			BX.ajax.history.obTimer = setTimeout(BX.ajax.history.__hashListener, 500);
		}
	},

	put: function(state, new_hash, new_hash1, bStartState)
	{
		if (this.bPushState)
		{
			if(!bStartState)
			{
				history.pushState(state, '', new_hash);
			}
			else
			{
				BX.ajax.history.startState = state;
			}
		}
		else
		{
			if (typeof new_hash1 != 'undefined')
				new_hash = new_hash1;
			else
				new_hash = 'view' + new_hash;

			jsAjaxHistoryContainer.put(new_hash, state);
			BX.ajax.history.expected_hash = new_hash;

			window.location.hash = BX.util.urlencode(new_hash);

			if (null != BX.ajax.history.obFrame)
			{
				BX.ajax.history.obFrame.contentWindow.document.open();
				BX.ajax.history.obFrame.contentWindow.document.write(new_hash);
				BX.ajax.history.obFrame.contentWindow.document.close();
			}
		}
	},

	checkRedirectStart: function(param_name, param_value)
	{
		var current_hash = window.location.hash;
		if (current_hash.substring(0, 1) == '#') current_hash = current_hash.substring(1);

		var test = current_hash.substring(0, 5);
		if (test == 'view/' || test == 'view%')
		{
			BX.ajax.history.bHashCollision = true;
			document.write('<' + 'div id="__ajax_hash_collision_' + param_value + '" style="display: none;">');
		}
	},

	checkRedirectFinish: function(param_name, param_value)
	{
		document.write('</div>');

		var current_hash = window.location.hash;
		if (current_hash.substring(0, 1) == '#') current_hash = current_hash.substring(1);

		BX.ready(function ()
		{
			var test = current_hash.substring(0, 5);
			if (test == 'view/' || test == 'view%')
			{
				var obColNode = BX('__ajax_hash_collision_' + param_value);
				var obNode = obColNode.firstChild;
				BX.cleanNode(obNode);
				obColNode.style.display = 'block';

				// IE, Opera and Chrome automatically modifies hash with urlencode, but FF doesn't ;-(
				if (test != 'view%')
					current_hash = BX.util.urlencode(current_hash);

				current_hash += (current_hash.indexOf('%3F') == -1 ? '%3F' : '%26') + param_name + '=' + param_value;

				var url = '/bitrix/tools/ajax_redirector.php?hash=' + current_hash;

				BX.ajax.insertToNode(url, obNode);
			}
		});
	}
};

BX.ajax.component = function(node)
{
	this.node = node;
};

BX.ajax.component.prototype.getState = function()
{
	var state = {
		'node': this.node,
		'title': window.document.title,
		'data': BX(this.node).innerHTML
	};

	var obNavChain = BX('navigation');
	if (null != obNavChain)
		state.nav_chain = obNavChain.innerHTML;

	return state;
};

BX.ajax.component.prototype.setState = function(state)
{
	BX(state.node).innerHTML = state.data;
	BX.ajax.UpdatePageTitle(state.title);

	if (state.nav_chain)
		BX.ajax.UpdatePageNavChain(state.nav_chain);
};

var jsAjaxHistoryContainer = {
	arHistory: {},

	put: function(hash, state)
	{
		this.arHistory[hash] = state;
	},

	get: function(hash)
	{
		return this.arHistory[hash];
	}
};


BX.ajax.FormData = function()
{
	this.elements = [];
	this.files = [];
	this.features = {};
	this.isSupported();
	this.log('BX FormData init');
};

BX.ajax.FormData.isSupported = function()
{
	var f = new BX.ajax.FormData();
	var result = f.features.supported;
	f = null;
	return result;
};

BX.ajax.FormData.prototype.log = function(o)
{
	if (false) {
		try {
			if (BX.browser.IsIE()) o = JSON.stringify(o);
			console.log(o);
		} catch(e) {}
	}
};

BX.ajax.FormData.prototype.isSupported = function()
{
	var f = {};
	f.fileReader = (window.FileReader && window.FileReader.prototype.readAsBinaryString);
	f.readFormData = f.sendFormData = !!(window.FormData);
	f.supported = !!(f.readFormData && f.sendFormData);
	this.features = f;
	this.log('features:');
	this.log(f);

	return f.supported;
};

BX.ajax.FormData.prototype.append = function(name, value)
{
	if (typeof(value) === 'object') { // seems to be files element
		this.files.push({'name': name, 'value':value});
	} else {
		this.elements.push({'name': name, 'value':value});
	}
};

BX.ajax.FormData.prototype.send = function(url, callbackOk, callbackProgress, callbackError)
{
	this.log('FD send');
	this.xhr = BX.ajax({
			'method': 'POST',
			'dataType': 'html',
			'url': url,
			'onsuccess': callbackOk,
			'onfailure': callbackError,
			'start': false,
			'preparePost':false
		});

	if (callbackProgress)
	{
		this.xhr.upload.addEventListener(
			'progress',
			function(e) {
				if (e.lengthComputable)
					callbackProgress(e.loaded / (e.total || e.totalSize));
			},
			false
		);
	}

	if (this.features.readFormData && this.features.sendFormData)
	{
		var fd = new FormData();
		this.log('use browser formdata');
		for (var i in this.elements)
		{
			if(this.elements.hasOwnProperty(i))
				fd.append(this.elements[i].name,this.elements[i].value);
		}
		for (i in this.files)
		{
			if(this.files.hasOwnProperty(i))
				fd.append(this.files[i].name, this.files[i].value);
		}
		this.xhr.send(fd);
	}

	return this.xhr;
};

BX.addCustomEvent('onAjaxFailure', BX.debug);
})(window);

/* End */
;
; /* Start:/bitrix/js/main/json/json2.min.js*/

var JSON;if(!JSON){JSON={};}
(function(){'use strict';function f(n){return n<10?'0'+n:n;}
if(typeof Date.prototype.toJSON!=='function'){Date.prototype.toJSON=function(key){return isFinite(this.valueOf())?this.getUTCFullYear()+'-'+
f(this.getUTCMonth()+1)+'-'+
f(this.getUTCDate())+'T'+
f(this.getUTCHours())+':'+
f(this.getUTCMinutes())+':'+
f(this.getUTCSeconds())+'Z':null;};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(key){return this.valueOf();};}
var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'},rep;function quote(string){escapable.lastIndex=0;return escapable.test(string)?'"'+string.replace(escapable,function(a){var c=meta[a];return typeof c==='string'?c:'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';}
function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key);}
if(typeof rep==='function'){value=rep.call(holder,key,value);}
switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';}
gap+=indent;partial=[];if(Object.prototype.toString.apply(value)==='[object Array]'){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||'null';}
v=partial.length===0?'[]':gap?'[\n'+gap+partial.join(',\n'+gap)+'\n'+mind+']':'['+partial.join(',')+']';gap=mind;return v;}
if(rep&&typeof rep==='object'){length=rep.length;for(i=0;i<length;i+=1){if(typeof rep[i]==='string'){k=rep[i];v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}else{for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}
v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+mind+'}':'{'+partial.join(',')+'}';gap=mind;return v;}}
if(typeof JSON.stringify!=='function'){JSON.stringify=function(value,replacer,space){var i;gap='';indent='';if(typeof space==='number'){for(i=0;i<space;i+=1){indent+=' ';}}else if(typeof space==='string'){indent=space;}
rep=replacer;if(replacer&&typeof replacer!=='function'&&(typeof replacer!=='object'||typeof replacer.length!=='number')){throw new Error('JSON.stringify');}
return str('',{'':value});};}
if(typeof JSON.parse!=='function'){JSON.parse=function(text,reviver){var j;function walk(holder,key){var k,v,value=holder[key];if(value&&typeof value==='object'){for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=walk(value,k);if(v!==undefined){value[k]=v;}else{delete value[k];}}}}
return reviver.call(holder,key,value);}
text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return'\\u'+
('0000'+a.charCodeAt(0).toString(16)).slice(-4);});}
if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){j=eval('('+text+')');return typeof reviver==='function'?walk({'':j},''):j;}
throw new SyntaxError('JSON.parse');};}}());
/* End */
;
; /* Start:/bitrix/js/main/core/core_ls.js*/
;(function(window){
if (window.BX.localStorage) return;

var
	BX = window.BX,
	localStorageInstance = null,
	_prefix = null,
	_key = '_bxCurrentKey',
	_support = false;

BX.localStorage = function()
{
	this.keyChanges = {}; // flag to skip self changes in IE
	BX.bind(
		(BX.browser.IsIE() && !BX.browser.IsIE9()) ? document : window, // HATE!
		'storage',
		BX.proxy(this._onchange, this)
	);

	setInterval(BX.delegate(this._clear, this), 5000);
};

/* localStorage public interface */

BX.localStorage.checkBrowser = function()
{
	return _support;
};

BX.localStorage.set = function(key, value, ttl)
{
	return BX.localStorage.instance().set(key, value, ttl);
};

BX.localStorage.get = function(key)
{
	return BX.localStorage.instance().get(key);
};

BX.localStorage.remove = function(key)
{
	return BX.localStorage.instance().remove(key);
};

BX.localStorage.instance = function()
{
	if (!localStorageInstance)
	{
		var support = BX.localStorage.checkBrowser();
		if (support == 'native')
			localStorageInstance = new BX.localStorage();
		else if (support == 'ie8')
			localStorageInstance = new BX.localStorageIE8();
		else if (support == 'ie7')
			localStorageInstance = new BX.localStorageIE7();
		else
		{
			localStorageInstance = {
				'set' : BX.DoNothing,
				'get' : function(){return null},
				'remove' : BX.DoNothing
			};
		}
	}
	return localStorageInstance;
};

/* localStorage prototype */
BX.localStorage.prototype.prefix = function()
{
	if (!_prefix)
	{
		_prefix = 'bx' + BX.message('USER_ID') + '-' + (BX.message('SITE_ID')||'admin') + '-';
	}

	return _prefix;
};

BX.localStorage.prototype._onchange = function(e)
{
	e = e || window.event;

	if (!e.key)
		return;

	if (BX.browser.DetectIeVersion() > 0 && this.keyChanges[e.key])
	{
		this.keyChanges[e.key] = false;
		return;
	}

	if (!!e.key && e.key.substring(0,this.prefix().length) == this.prefix())
	{
		var d = {
			key: e.key.substring(this.prefix().length, e.key.length),
			value: !!e.newValue? this._decode(e.newValue.substring(11, e.newValue.length)): null,
			oldValue: !!e.oldValue? this._decode(e.oldValue.substring(11, e.oldValue.length)): null
		};

		switch(d.key)
		{
			case 'BXGCE': // BX Global Custom Event
				if (d.value)
				{
					BX.onCustomEvent(d.value.e, d.value.p);
				}
			break;
			default:
				// normal event handlers
				if (e.newValue)
					BX.onCustomEvent(window, 'onLocalStorageSet', [d]);
				if (e.oldValue && !e.newValue)
					BX.onCustomEvent(window, 'onLocalStorageRemove', [d]);

				BX.onCustomEvent(window, 'onLocalStorageChange', [d]);
			break;
		}
	}
};

BX.localStorage.prototype._clear = function()
{
	var curDate = +new Date(), key, i;

	for (i=0; i<localStorage.length; i++)
	{
		key = localStorage.key(i);
		if (key.substring(0,2) == 'bx')
		{
			var ttl = localStorage.getItem(key).split(':', 1)*1000;
			if (curDate >= ttl)
				localStorage.removeItem(key);
		}
	}
};

BX.localStorage.prototype._encode = function(value)
{
	if (typeof(value) == 'object')
		value = JSON.stringify(value);
	else
		value = value.toString();
	return value;
};

BX.localStorage.prototype._decode = function(value)
{
	var answer = null;
	if (!!value)
	{
		try {answer = JSON.parse(value);}
		catch(e) { answer = value; }
	}
	return answer;
};

BX.localStorage.prototype._trigger_error = function(e, key, value, ttl)
{
	BX.onCustomEvent(this, 'onLocalStorageError', [e, {key: key, value: value, ttl: ttl}]);
};

BX.localStorage.prototype.set = function(key, value, ttl)
{
	if (!ttl || ttl <= 0)
		ttl = 60;

	if (key == undefined || key == null || value == undefined)
		return false;

	this.keyChanges[this.prefix()+key] = true;
	try
	{
		localStorage.setItem(
			this.prefix()+key,
			(Math.round((+new Date())/1000)+ttl)+':'+this._encode(value)
		);
	}
	catch (e)
	{
		this._trigger_error(e, key, value, ttl);
	}
};

BX.localStorage.prototype.get = function(key)
{
	var storageAnswer = localStorage.getItem(this.prefix()+key);

	if (storageAnswer)
	{
		var ttl = storageAnswer.split(':', 1)*1000;
		if ((+new Date()) <= ttl)
		{
			storageAnswer = storageAnswer.substring(11, storageAnswer.length);
			return this._decode(storageAnswer);
		}
	}

	return null;
};

BX.localStorage.prototype.remove = function(key)
{
	this.keyChanges[this.prefix()+key] = true;
	localStorage.removeItem(this.prefix()+key);
};

/************** IE 7 ******************/

BX.localStorageIE7 = function()
{
	this.NS = 'BXLocalStorage';
	this.__current_state = {};
	this.keyChanges = {};

	BX.ready(BX.delegate(this._Init, this));
};

BX.extend(BX.localStorageIE7, BX.localStorage);

BX.localStorageIE7.prototype._Init = function()
{
	this.storage_element = document.body.appendChild(BX.create('DIV'));
	this.storage_element.addBehavior('#default#userData');
	this.storage_element.load(this.NS);

	var doc = this.storage_element.xmlDocument,
		len = doc.firstChild.attributes.length;

	for (var i = 0; i<len; i++)
	{
		if (!!doc.firstChild.attributes[i])
		{
			var k = doc.firstChild.attributes[i].nodeName;
			if (k.substring(0,this.prefix().length) == this.prefix())
			{
				this.__current_state[k] = doc.firstChild.attributes[i].nodeValue;
			}
		}
	}

	setInterval(BX.delegate(this._Listener, this), 500);
	setInterval(BX.delegate(this._clear, this), 5000);
};

BX.localStorageIE7.prototype._Listener = function(bInit)
{
	this.storage_element.load(this.NS);

	var doc = this.storage_element.xmlDocument,
		len = doc.firstChild.attributes.length,
		i,k,v;

	var new_state = {}, arChanges = [];

	for (i = 0; i<len; i++)
	{
		if (!!doc.firstChild.attributes[i])
		{
			k = doc.firstChild.attributes[i].nodeName;
			if (k.substring(0,this.prefix().length) == this.prefix())
			{
				v = doc.firstChild.attributes[i].nodeValue;

				if (this.__current_state[k] != v)
				{
					arChanges.push({
						key: k, newValue: v, oldValue: this.__current_state[k]
					});
				}

				new_state[k] = v;
				delete this.__current_state[k];
			}
		}
	}

	for (i in this.__current_state)
	{
		if(this.__current_state.hasOwnProperty(i))
		{
			arChanges.push({
				key: i, newValue: undefined, oldValue: this.__current_state[i]
			});
		}
	}

	this.__current_state = new_state;

	for (i=0; i<arChanges.length; i++)
	{
		this._onchange(arChanges[i]);
	}
};

BX.localStorageIE7.prototype._clear = function()
{
	this.storage_element.load(this.NS);

	var doc = this.storage_element.xmlDocument,
		len = doc.firstChild.attributes.length,
		curDate = +new Date(),
		i,k,v,ttl;

	for (i = 0; i<len; i++)
	{
		if (!!doc.firstChild.attributes[i])
		{
			k = doc.firstChild.attributes[i].nodeName;
			if (k.substring(0,2) == 'bx')
			{
				v = doc.firstChild.attributes[i].nodeValue;
				ttl = v.split(':', 1)*1000;
				if (curDate >= ttl)
				{
					doc.firstChild.removeAttribute(k)
				}
			}
		}
	}

	this.storage_element.save(this.NS);
};

BX.localStorageIE7.prototype.set = function(key, value, ttl)
{
	if (!ttl || ttl <= 0)
		ttl = 60;

	try
	{
		this.storage_element.load(this.NS);

		var doc = this.storage_element.xmlDocument;

		this.keyChanges[this.prefix()+key] = true;

		doc.firstChild.setAttribute(
			this.prefix()+key,
			(Math.round((+new Date())/1000)+ttl)+':'+this._encode(value)
		);

		this.storage_element.save(this.NS);
	}
	catch(e)
	{
		this._trigger_error(e, key, value, ttl);
	}
};

BX.localStorageIE7.prototype.get = function(key)
{
	this.storage_element.load(this.NS);
	var doc = this.storage_element.xmlDocument;

	var storageAnswer = doc.firstChild.getAttribute(this.prefix()+key);

	if (storageAnswer)
	{
		var ttl = storageAnswer.split(':', 1)*1000;
		if ((+new Date()) <= ttl)
		{
			storageAnswer = storageAnswer.substring(11, storageAnswer.length);
			return this._decode(storageAnswer);
		}
	}

	return null;
};

BX.localStorageIE7.prototype.remove = function(key)
{
	this.storage_element.load(this.NS);

	var doc = this.storage_element.xmlDocument;
	doc.firstChild.removeAttribute(this.prefix()+key);

	this.keyChanges[this.prefix()+key] = true;
	this.storage_element.save(this.NS);
};

/************** IE 8 & FF 3.6 ***************/

BX.localStorageIE8 = function()
{
	this.key = _key;

	this.currentKey = null;
	this.currentValue = null;

	BX.localStorageIE8.superclass.constructor.apply(this);
};
BX.extend(BX.localStorageIE8, BX.localStorage);

BX.localStorageIE8.prototype._onchange = function(e)
{
	if (null == this.currentKey)
	{
		this.currentKey = localStorage.getItem(this.key);
		if (this.currentKey)
		{
			this.currentValue = localStorage.getItem(this.prefix() + this.currentKey);
		}
	}
	else
	{
		e = {
			key: this.prefix() + this.currentKey,
			newValue: localStorage.getItem(this.prefix() + this.currentKey),
			oldValue: this.currentValue
		};

		this.currentKey = null;
		this.currentValue = null;

		// especially for FF3.6
		if (this.keyChanges[e.key])
		{
			this.keyChanges[e.key] = false;
			return;
		}

		BX.localStorageIE8.superclass._onchange.apply(this, [e]);
	}
};

BX.localStorageIE8.prototype.set = function(key, value, ttl)
{
	this.currentKey = null;
	this.keyChanges[this.prefix()+key] = true;

	try
	{
		localStorage.setItem(this.key, key);
		BX.localStorageIE8.superclass.set.apply(this, arguments);
	}
	catch(e)
	{
		this._trigger_error(e, key, value, ttl);
	}
};

BX.localStorageIE8.prototype.remove = function(key)
{
	this.currentKey = null;
	this.keyChanges[this.prefix()+key] = true;

	localStorage.setItem(this.key, key);
	BX.localStorageIE8.superclass.remove.apply(this, arguments);
};

/* additional functions */

BX.onGlobalCustomEvent = function(eventName, arEventParams, bSkipSelf)
{
	if (!!BX.localStorage.checkBrowser())
		BX.localStorage.set('BXGCE', {e:eventName,p:arEventParams}, 1);

	if (!bSkipSelf)
		BX.onCustomEvent(eventName, arEventParams);
};

/***************** initialize *********************/

try {
	_support = !!localStorage.setItem;
} catch(e) {}

if (_support)
{
	_support = 'native';

	// hack to check FF3.6 && IE8
	var _target = (BX.browser.IsIE() && !BX.browser.IsIE9()) ? document : window,
		_checkFFnIE8 = function(e) {
		if (typeof(e||window.event).key == 'undefined')
			_support = 'ie8';
		BX.unbind(_target, 'storage', _checkFFnIE8);
		BX.localStorage.instance();
	};
	BX.bind(_target, 'storage', _checkFFnIE8);
	localStorage.setItem(_key, null);
}
else if (BX.browser.IsIE7())
{
	_support = 'ie7';
	BX.localStorage.instance();
}

})(window);

/* End */
;
; /* Start:/bitrix/js/main/core/core_fx.js*/
;(function(window){

var defaultOptions = {
	time: 1.0,
	step: 0.05,
	type: 'linear',

	allowFloat: false
}

/*
options: {
	start: start value or {param: value, param: value}
	finish: finish value or {param: value, param: value}
	time: time to transform in seconds
	type: linear|accelerated|decelerated|custom func name
	callback,
	callback_start,
	callback_complete,

	step: time between steps in seconds
	allowFloat: false|true
}
*/
BX.fx = function(options)
{
	this.options = options;

	if (null != this.options.time)
		this.options.originalTime = this.options.time;
	if (null != this.options.step)
		this.options.originalStep = this.options.step;

	if (!this.__checkOptions())
		return false;

	this.__go = BX.delegate(this.go, this);

	this.PARAMS = {};
}

BX.fx.prototype.__checkOptions = function()
{
	if (typeof this.options.start != typeof this.options.finish)
		return false;

	if (null == this.options.time) this.options.time = defaultOptions.time;
	if (null == this.options.step) this.options.step = defaultOptions.step;
	if (null == this.options.type) this.options.type = defaultOptions.type;
	if (null == this.options.allowFloat) this.options.allowFloat = defaultOptions.allowFloat;

	this.options.time *= 1000;
	this.options.step *= 1000;

	if (typeof this.options.start != 'object')
	{
		this.options.start = {_param: this.options.start};
		this.options.finish = {_param: this.options.finish};
	}

	var i;
	for (i in this.options.start)
	{
		if (null == this.options.finish[i])
		{
			this.options.start[i] = null;
			delete this.options.start[i];
		}
	}

	if (!BX.type.isFunction(this.options.type))
	{
		if (BX.type.isFunction(window[this.options.type]))
			this.options.type = window[this.options.type];
		else if (BX.type.isFunction(BX.fx.RULES[this.options.type]))
			this.options.type = BX.fx.RULES[this.options.type];
		else
			this.options.type = BX.fx.RULES[defaultOptions.type];
	}

	return true;
}

BX.fx.prototype.go = function()
{
	var timeCurrent = new Date().valueOf();
	if (timeCurrent < this.PARAMS.timeFinish)
	{
		for (var i in this.PARAMS.current)
		{
			this.PARAMS.current[i][0] = this.options.type.apply(this, [{
				start_value: this.PARAMS.start[i][0],
				finish_value: this.PARAMS.finish[i][0],
				current_value: this.PARAMS.current[i][0],
				current_time: timeCurrent - this.PARAMS.timeStart,
				total_time: this.options.time
			}]);
		}

		this._callback(this.options.callback);

		if (!this.paused)
			this.PARAMS.timer = setTimeout(this.__go, this.options.step);
	}
	else
	{
		this.stop();
	}
}

BX.fx.prototype._callback = function(cb)
{
	var tmp = {};

	cb = cb || this.options.callback;

	for (var i in this.PARAMS.current)
	{
		tmp[i] = (this.options.allowFloat ? this.PARAMS.current[i][0] : Math.round(this.PARAMS.current[i][0])) + this.PARAMS.current[i][1];
	}

	return cb.apply(this, [null != tmp['_param'] ? tmp._param : tmp]);
}

BX.fx.prototype.start = function()
{
	var i,value, unit;

	this.PARAMS.start = {};
	this.PARAMS.current = {};
	this.PARAMS.finish = {};

	for (i in this.options.start)
	{
		value = +this.options.start[i];
		unit = (this.options.start[i]+'').substring((value+'').length);
		this.PARAMS.start[i] = [value, unit];
		this.PARAMS.current[i] = [value, unit];
		this.PARAMS.finish[i] = [+this.options.finish[i], unit];
	}

	this._callback(this.options.callback_start);
	this._callback(this.options.callback);

	this.PARAMS.timeStart = new Date().valueOf();
	this.PARAMS.timeFinish = this.PARAMS.timeStart + this.options.time;
	this.PARAMS.timer = setTimeout(BX.delegate(this.go, this), this.options.step);

	return this;
}

BX.fx.prototype.pause = function()
{
	if (this.paused)
	{
		this.PARAMS.timer = setTimeout(this.__go, this.options.step);
		this.paused = false;
	}
	else
	{
		clearTimeout(this.PARAMS.timer);
		this.paused = true;
	}
}

BX.fx.prototype.stop = function(silent)
{
	silent = !!silent;
	if (this.PARAMS.timer)
		clearTimeout(this.PARAMS.timer);

	if (null != this.options.originalTime)
		this.options.time = this.options.originalTime;
	if (null != this.options.originalStep)
		this.options.step = this.options.originalStep;

	this.PARAMS.current = this.PARAMS.finish;
	if (!silent) {
		this._callback(this.options.callback);
		this._callback(this.options.callback_complete);
	}
}

/*
type rules of animation
 - linear - simple linear animation
 - accelerated
 - decelerated
*/

/*
	params: {
		start_value, finish_value, current_time, total_time
	}
*/
BX.fx.RULES =
{
	linear: function(params)
	{
		return params.start_value + (params.current_time/params.total_time) * (params.finish_value - params.start_value);
	},

	decelerated: function(params)
	{
		return params.start_value + Math.sqrt(params.current_time/params.total_time) * (params.finish_value - params.start_value);
	},

	accelerated: function(params)
	{
		var q = params.current_time/params.total_time;
		return params.start_value + q * q * (params.finish_value - params.start_value);
	}
}

/****************** effects realizaion ************************/

/*
	type = 'fade' || 'scroll' || 'scale' || 'fold'
*/

BX.fx.hide = function(el, type, opts)
{
	el = BX(el);

	if (typeof type == 'object' && null == opts)
	{
		opts = type;
		type = opts.type
	}

	if (!BX.type.isNotEmptyString(type))
	{
		el.style.display = 'none';
		return;
	}

	var fxOptions = BX.fx.EFFECTS[type](el, opts, 0);
	fxOptions.callback_complete = function () {
		if (opts.hide !== false)
			el.style.display = 'none';

		if (opts.callback_complete)
			opts.callback_complete.apply(this, arguments);
	}

	return (new BX.fx(fxOptions)).start();
}

BX.fx.show = function(el, type, opts)
{
	el = BX(el);

	if (typeof type == 'object' && null == opts)
	{
		opts = type;
		type = opts.type
	}

	if (!opts) opts = {};

	if (!BX.type.isNotEmptyString(type))
	{
		el.style.display = 'block';
		return;
	}

	var fxOptions = BX.fx.EFFECTS[type](el, opts, 1);

	fxOptions.callback_complete = function () {
		if (opts.show !== false)
			el.style.display = 'block';

		if (opts.callback_complete)
			opts.callback_complete.apply(this, arguments);
	}

	return (new BX.fx(fxOptions)).start();
}

BX.fx.EFFECTS = {
	scroll: function(el, opts, action)
	{
		if (!opts.direction) opts.direction = 'vertical';

		var param = opts.direction == 'horizontal' ? 'width' : 'height';

		var val = parseInt(BX.style(el, param));
		if (isNaN(val))
		{
			val = BX.pos(el)[param];
		}

		if (action == 0)
			var start = val, finish = opts.min_height ? parseInt(opts.min_height) : 0;
		else
			var finish = val, start = opts.min_height ? parseInt(opts.min_height) : 0;

		return {
			'start': start,
			'finish': finish,
			'time': opts.time || defaultOptions.time,
			'type': 'linear',
			callback_start: function () {
				if (BX.style(el, 'position') == 'static')
					el.style.position = 'relative';

				el.style.overflow = 'hidden';
				el.style[param] = start + 'px';
				el.style.display = 'block';
			},
			callback: function (val) {el.style[param] = val + 'px';}
		}
	},

	fade: function(el, opts, action)
	{
		var fadeOpts = {
			'time': opts.time || defaultOptions.time,
			'type': action == 0 ? 'decelerated' : 'linear',
			'start': action == 0 ? 1 : 0,
			'finish': action == 0 ? 0 : 1,
			'allowFloat': true
		};

		if (BX.browser.IsIE() && !BX.browser.IsIE9())
		{
			fadeOpts.start *= 100; fadeOpts.finish *= 100; fadeOpts.allowFloat = false;

			fadeOpts.callback_start = function() {
				el.style.display = 'block';
				el.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity=" + fadeOpts.start + ")";
			};

			fadeOpts.callback = function (val) {
				(el.filters['DXImageTransform.Microsoft.alpha']||el.filters.alpha).opacity = val;
			}
		}
		else
		{
			fadeOpts.callback_start = function () {
				el.style.display = 'block';
			}

			fadeOpts.callback = function (val) {
				el.style.opacity = el.style.KhtmlOpacity = el.style.MozOpacity = val;
			};
		}

		return fadeOpts;
	},

	fold: function (el, opts, action) // 'fold' is a combination of two consequential 'scroll' hidings.
	{
		if (action != 0) return;

		var pos = BX.pos(el);
		var coef = pos.height / (pos.width + pos.height);
		var old_opts = {time: opts.time || defaultOptions.time, callback_complete: opts.callback_complete, hide: opts.hide};

		opts.type = 'scroll';
		opts.direction = 'vertical';
		opts.min_height = opts.min_height || 10;
		opts.hide = false;
		opts.time = coef * old_opts.time;
		opts.callback_complete = function()
		{
			el.style.whiteSpace = 'nowrap';

			opts.direction = 'horizontal';
			opts.min_height = null;

			opts.time = old_opts.time - opts.time;
			opts.hide = old_opts.hide;
			opts.callback_complete = old_opts.callback_complete;

			BX.fx.hide(el, opts);
		}

		return BX.fx.EFFECTS.scroll(el, opts, action);
	},

	scale: function (el, opts, action)
	{
		var val = {width: parseInt(BX.style(el, 'width')), height: parseInt(BX.style(el, 'height'))};
		if (isNaN(val.width) || isNaN(val.height))
		{
			var pos = BX.pos(el)
			val = {width: pos.width, height: pos.height};
		}

		if (action == 0)
			var start = val, finish = {width: 0, height: 0};
		else
			var finish = val, start = {width: 0, height: 0};

		return {
			'start': start,
			'finish': finish,
			'time': opts.time || defaultOptions.time,
			'type': 'linear',
			callback_start: function () {
				el.style.position = 'relative';
				el.style.overflow = 'hidden';
				el.style.display = 'block';
				el.style.height = start.height + 'px';
				el.style.width = start.width + 'px';
			},
			callback: function (val) {
				el.style.height = val.height + 'px';
				el.style.width = val.width + 'px';
			}
		}
	}
}

// Color animation
//
// Set animation rule
// BX.fx.colorAnimate.addRule('animationRule1',"#FFF","#faeeb4", "background-color", 100, 1, true);
// BX.fx.colorAnimate.addRule('animationRule2',"#fc8282","#ff0000", "color", 100, 1, true);
// Params: 1 - animation name, 2 - start color, 3 - end color, 4 - count step, 5 - delay each step, 6 - return color on end animation
//
// Animate color for element
// BX.fx.colorAnimate(BX('element'), 'animationRule1,animationRule2');

var defaultOptionsColorAnimation = {
	arStack: {},
	arRules: {},
	globalAnimationId: 0
}

BX.fx.colorAnimate = function(element, rule, back)
{
	if (element == null)
		return;

	animationId = element.getAttribute('data-animation-id');
	if (animationId == null)
	{
		animationId = defaultOptionsColorAnimation.globalAnimationId;
		element.setAttribute('data-animation-id', defaultOptionsColorAnimation.globalAnimationId++);
	}
	var aRuleList = rule.split(/\s*,\s*/);

	for (var j	= 0; j < aRuleList.length; j++)
	{
		rule = aRuleList[j];

		if (!defaultOptionsColorAnimation.arRules[rule]) continue;

		var i=0;

		if (!defaultOptionsColorAnimation.arStack[animationId])
		{
			defaultOptionsColorAnimation.arStack[animationId] = {};
		}
		else if (defaultOptionsColorAnimation.arStack[animationId][rule])
		{
			i = defaultOptionsColorAnimation.arStack[animationId][rule].i;
			clearInterval(defaultOptionsColorAnimation.arStack[animationId][rule].tId);
		}

		if ((i==0 && back) || (i==defaultOptionsColorAnimation.arRules[rule][3] && !back)) continue;

		defaultOptionsColorAnimation.arStack[animationId][rule] = {'i':i, 'element': element, 'tId':setInterval('BX.fx.colorAnimate.run("'+animationId+'","'+rule+'")', defaultOptionsColorAnimation.arRules[rule][4]),'back':Boolean(back)};
	}
}

BX.fx.colorAnimate.addRule = function (rule, startColor, finishColor, cssProp, step, delay, back)
{
	defaultOptionsColorAnimation.arRules[rule] = [
		BX.util.hex2rgb(startColor),
		BX.util.hex2rgb(finishColor),
		cssProp.replace(/\-(.)/g,function(){return arguments[1].toUpperCase();}),
		step,
		delay || 1,
		back || false
	];
};

BX.fx.colorAnimate.run = function(animationId, rule)
{
	element = defaultOptionsColorAnimation.arStack[animationId][rule].element;

    defaultOptionsColorAnimation.arStack[animationId][rule].i += defaultOptionsColorAnimation.arStack[animationId][rule].back?-1:1;
 	var finishPercent = defaultOptionsColorAnimation.arStack[animationId][rule].i/defaultOptionsColorAnimation.arRules[rule][3];
	var startPercent = 1 - finishPercent;

	var aRGBStart = defaultOptionsColorAnimation.arRules[rule][0];
	var aRGBFinish = defaultOptionsColorAnimation.arRules[rule][1];

	element.style[defaultOptionsColorAnimation.arRules[rule][2]] = 'rgb('+
	Math.floor( aRGBStart['r'] * startPercent + aRGBFinish['r'] * finishPercent ) + ','+
	Math.floor( aRGBStart['g'] * startPercent + aRGBFinish['g'] * finishPercent ) + ','+
	Math.floor( aRGBStart['b'] * startPercent + aRGBFinish['b'] * finishPercent ) +')';

	if ( defaultOptionsColorAnimation.arStack[animationId][rule].i == defaultOptionsColorAnimation.arRules[rule][3] || defaultOptionsColorAnimation.arStack[animationId][rule].i ==0)
	{
		clearInterval(defaultOptionsColorAnimation.arStack[animationId][rule].tId);
		if (defaultOptionsColorAnimation.arRules[rule][5])
			BX.fx.colorAnimate(defaultOptionsColorAnimation.arStack[animationId][rule].element, rule, true);
	}
}


/*
options = {
	delay: 100,
	duration : 3000,
	start : { scroll : document.body.scrollTop, left : 0, opacity :  100 },
	finish : { scroll : document.body.scrollHeight, left : 500, opacity : 10 },
	transition : BitrixAnimation.makeEaseOut(BitrixAnimation.transitions.quart),

	step : function(state)
	{
		document.body.scrollTop = state.scroll;
		button.style.left =  state.left + "px";
		button.style.opacity =  state.opacity / 100;
	},
	complete : function()
	{
		button.style.background = "green";
	}
}

options =
{
	delay : 20,
	duration : 4000,
	transition : BXAnimation.makeEaseOut(BXAnimation.transitions.quart),
	progress : function(progress)
	{
		document.body.scrollTop = Math.round(topMax * progress);
		button.style.left =  Math.round(leftMax * progress) + "px";
		button.style.opacity =  (100 + Math.round((opacityMin - 100) * progress)) / 100;

	},
	complete : function()
	{
		button.style.background = "green";
	}
}
*/

BX.easing = function(options)
{
	this.options = options;
	this.timer = null;
};

BX.easing.prototype.animate = function()
{
	if (!this.options || !this.options.start || !this.options.finish ||
		typeof(this.options.start) != "object" || typeof(this.options.finish) != "object"
		)
		return null;

	for (var propName in this.options.start)
	{
		if (typeof(this.options.finish[propName]) == "undefined")
		{
			delete this.options.start[propName];
		}
	}

	this.options.progress = function(progress) {
		var state = {};
		for (var propName in this.start)
			state[propName] = Math.round(this.start[propName] + (this.finish[propName] - this.start[propName]) * progress);

		if (this.step)
			this.step(state);
	};

	this.animateProgress();
};

BX.easing.prototype.stop = function(completed)
{
	if (this.timer)
	{
		clearInterval(this.timer);
		this.timer = null;

		if (completed)
			this.options.complete && this.options.complete();
	}
};

BX.easing.prototype.animateProgress = function()
{
	var start = new Date();
	var delta = this.options.transition || BX.easing.transitions.linear;
	var duration = this.options.duration || 1000;

	this.timer = setInterval(BX.proxy(function() {

		var progress = (new Date() - start) / duration;
		if (progress > 1)
			progress = 1;

		this.options.progress(delta(progress));

		if (progress == 1)
			this.stop(true);

	}, this), this.options.delay || 13);

};

BX.easing.makeEaseInOut = function(delta)
{
	return function(progress) {
		if (progress < 0.5)
			return delta( 2 * progress ) / 2;
		else
			return (2 - delta( 2 * (1-progress) ) ) / 2;
	}
};

BX.easing.makeEaseOut = function(delta)
{
	return function(progress) {
		return 1 - delta(1 - progress);
	};
};

BX.easing.transitions = {

	linear : function(progress)
	{
		return progress;
	},

	quad : function(progress)
	{
		return Math.pow(progress, 2);
	},

	cubic : function(progress) {
		return Math.pow(progress, 3);
	},

	quart : function(progress)
	{
		return Math.pow(progress, 4);
	},

	quint : function(progress)
	{
		return Math.pow(progress, 5);
	},

	circ : function(progress)
	{
		return 1 - Math.sin(Math.acos(progress));
	},

	back : function(progress)
	{
		return Math.pow(progress, 2) * ((1.5 + 1) * progress - 1.5);
	},

	elastic: function(progress)
	{
		return Math.pow(2, 10 * (progress-1)) * Math.cos(20 * Math.PI * 1.5/3 * progress);
	},

	bounce : function(progress)
	{
		for(var a = 0, b = 1; 1; a += b, b /= 2) {
			if (progress >= (7 - 4 * a) / 11) {
				return -Math.pow((11 - 6 * a - 11 * progress) / 4, 2) + Math.pow(b, 2);
			}
		}
	}};


})(window);

/* End */
;
; /* Start:/bitrix/js/main/core/core_window.js*/
;(function(window) {
if (BX.WindowManager) return;

/* windows manager */
BX.WindowManager = {
	_stack: [],
	_runtime_resize: {},
	_delta: 2,
	_delta_start: 1000,
	currently_loaded: null,

	settings_category: 'BX.WindowManager.9.5',

	register: function (w)
	{
		this.currently_loaded = null;
		var div = w.Get();

		div.style.zIndex = w.zIndex = this.GetZIndex();

		w.WM_REG_INDEX = this._stack.length;
		this._stack.push(w);

		if (this._stack.length < 2)
		{
			BX.bind(document, 'keyup', BX.proxy(this.__checkKeyPress, this));
		}
	},

	unregister: function (w)
	{
		if (null == w.WM_REG_INDEX)
			return null;

		var _current;
		if (this._stack.length > 0)
		{
			while ((_current = this.__pop_stack()) != w)
			{
				if (!_current)
				{
					_current = null;
					break;
				}
			}

			if (this._stack.length <= 0)
			{
				this.enableKeyCheck();
			}

			return _current;
		}
		else
		{
			return null;
		}
	},

	__pop_stack: function(clean)
	{
		if (this._stack.length > 0)
		{
			var _current = this._stack.pop();
			_current.WM_REG_INDEX = null;
			BX.onCustomEvent(_current, 'onWindowUnRegister', [clean === true]);

			return _current;
		}
		else
			return null;
	},

	clean: function()
	{
		while (this.__pop_stack(true)){}
		this._stack = null;
		this.disableKeyCheck();
	},

	Get: function()
	{
		if (this.currently_loaded)
			return this.currently_loaded;
		else if (this._stack.length > 0)
			return this._stack[this._stack.length-1];
		else
			return null;
	},

	setStartZIndex: function(value)
	{
		this._delta_start = value;
	},

	restoreStartZIndex: function()
	{
		this._delta_start = 1000;
	},

	GetZIndex: function()
	{
		var _current;
		return (null != (_current = this._stack[this._stack.length-1])
			? parseInt(_current.Get().style.zIndex) + this._delta
			: this._delta_start
		);
	},

	__get_check_url: function(url)
	{
		var pos = url.indexOf('?');
		return pos == -1 ? url : url.substring(0, pos);
	},

	saveWindowSize: function(url, params)
	{
		var check_url = this.__get_check_url(url);
		if (BX.userOptions)
		{
			BX.userOptions.save(this.settings_category, 'size_' + check_url, 'width', params.width);
			BX.userOptions.save(this.settings_category, 'size_' + check_url, 'height', params.height);
		}

		this._runtime_resize[check_url] = params;
	},

	saveWindowOptions: function(wnd_id, opts)
	{
		if (BX.userOptions)
		{
			for (var i in opts)
			{
				if(opts.hasOwnProperty(i))
				{
					BX.userOptions.save(this.settings_category, 'options_' + wnd_id, i, opts[i]);
				}
			}
		}
	},

	getRuntimeWindowSize: function(url)
	{
		return this._runtime_resize[this.__get_check_url(url)];
	},

	disableKeyCheck: function()
	{
		BX.unbind(document, 'keyup', BX.proxy(this.__checkKeyPress, this));
	},

	enableKeyCheck: function()
	{
		BX.bind(document, 'keyup', BX.proxy(this.__checkKeyPress, this));
	},

	__checkKeyPress: function(e)
	{
		if (null == e)
			e = window.event;

		if (e.keyCode == 27)
		{
			var wnd = BX.WindowManager.Get();
			if (wnd && !wnd.unclosable) wnd.Close();
		}
	}
};

BX.garbage(BX.WindowManager.clean, BX.WindowManager);

/* base button class */
BX.CWindowButton = function(params)
{
	if (params.btn)
	{
		this.btn = params.btn;
		this.parentWindow = params.parentWindow;

		if (/save|apply/i.test(this.btn.name))
		{
			BX.bind(this.btn, 'click', BX.delegate(this.disableUntilError, this));
		}
	}
	else
	{
		this.title = params.title; // html value attr
		this.hint = params.hint; // html title attr
		this.id = params.id; // html name and id attrs
		this.name = params.name; // html name or value attrs when id and title 're absent
		this.className = params.className; // className for button input

		this.action = params.action;
		this.onclick = params.onclick;

		// you can override button creation method
		if (params.Button && BX.type.isFunction(params.Button))
			this.Button = params.Button;

		this.btn = null;
	}
};

BX.CWindowButton.prototype.disable = function()
{
	if (this.btn)
		this.parentWindow.showWait(this.btn);
};
BX.CWindowButton.prototype.enable = function(){
	if (this.btn)
		this.parentWindow.closeWait(this.btn);
};

BX.CWindowButton.prototype.emulate = function()
{
	if (this.btn && this.btn.disabled)
		return;

	var act =
		this.action
		? BX.delegate(this.action, this)
		: (
			this.onclick
			? this.onclick
			: (
				this.btn
				? this.btn.getAttribute('onclick')
				: ''
			)
		);

	if (act)
	{
		setTimeout(act, 50);
		if (this.btn && /save|apply/i.test(this.btn.name) && !this.action)
		{
			this.disableUntilError();
		}
	}
};

BX.CWindowButton.prototype.Button = function(parentWindow)
{
	this.parentWindow = parentWindow;

	var btn = {
		props: {
			'type': 'button',
			'name': this.id ? this.id : this.name,
			'value': this.title ? this.title : this.name,
			'id': this.id
		}
	};

	if (this.hint)
		btn.props.title = this.hint;
	if (!!this.className)
		btn.props.className = this.className;

	if (this.action)
	{
		btn.events = {
			'click': BX.delegate(this.action, this)
		};
	}
	else if (this.onclick)
	{
		if (BX.browser.IsIE())
		{
			btn.events = {
				'click': BX.delegate(function() {eval(this.onclick)}, this)
			};
		}
		else
		{
			btn.attrs = {
				'onclick': this.onclick
			};
		}
	}

	this.btn = BX.create('INPUT', btn);

	return this.btn;
};

BX.CWindowButton.prototype.disableUntilError = function() {
	this.disable();
	if (!this.__window_error_handler_set)
	{
		BX.addCustomEvent(this.parentWindow, 'onWindowError', BX.delegate(this.enable, this));
		this.__window_error_handler_set = true;
	}
};

/* base window class */
BX.CWindow = function(div, type)
{
	this.DIV = div || document.createElement('DIV');

	this.SETTINGS = {
		resizable: false,
		min_height: 0,
		min_width: 0,
		top: 0,
		left: 0,
		draggable: false,
		drag_restrict: true,
		resize_restrict: true
	};

	this.ELEMENTS = {
		draggable: [],
		resizer: [],
		close: []
	};

	this.type = type == 'float' ? 'float' : 'dialog';

	BX.adjust(this.DIV, {
		props: {
			className: 'bx-core-window'
		},
		style: {
			'zIndex': 0,
			'position': 'absolute',
			'display': 'none',
			'top': this.SETTINGS.top + 'px',
			'left': this.SETTINGS.left + 'px',
			'height': '100px',
			'width': '100px'
		}
	});

	this.isOpen = false;

	BX.addCustomEvent(this, 'onWindowRegister', BX.delegate(this.onRegister, this));
	BX.addCustomEvent(this, 'onWindowUnRegister', BX.delegate(this.onUnRegister, this));

	this.MOUSEOVER = null;
	BX.bind(this.DIV, 'mouseover', BX.delegate(this.__set_msover, this));
	BX.bind(this.DIV, 'mouseout', BX.delegate(this.__unset_msover, this));

	BX.ready(BX.delegate(function() {
		document.body.appendChild(this.DIV);
	}, this));
};

BX.CWindow.prototype.Get = function () {return this.DIV};
BX.CWindow.prototype.visible = function() {return this.isOpen;};

BX.CWindow.prototype.Show = function(bNotRegister)
{
	this.DIV.style.display = 'block';

	if (!bNotRegister)
	{
		BX.WindowManager.register(this);
		BX.onCustomEvent(this, 'onWindowRegister');
	}
};

BX.CWindow.prototype.Hide = function()
{
	BX.WindowManager.unregister(this);
	this.DIV.style.display = 'none';
};

BX.CWindow.prototype.onRegister = function()
{
	this.isOpen = true;
};

BX.CWindow.prototype.onUnRegister = function(clean)
{
	this.isOpen = false;

	if (clean || (this.PARAMS && this.PARAMS.content_url))
	{
		if (clean) {BX.onCustomEvent(this, 'onWindowClose', [this, true]);}

		if (this.DIV.parentNode)
			this.DIV.parentNode.removeChild(this.DIV);
	}
	else
	{
		this.DIV.style.display = 'none';
	}
};

BX.CWindow.prototype.CloseDialog = // compatibility
BX.CWindow.prototype.Close = function(bImmediately)
{
	BX.onCustomEvent(this, 'onBeforeWindowClose', [this]);
	if (bImmediately !== true)
	{
		if (this.denyClose)
			return false;
	}

	BX.onCustomEvent(this, 'onWindowClose', [this]);

	//this crashes vis editor in ie via onWindowResizeExt event handler
	//if (this.bExpanded) this.__expand();
	// alternative version:
	if (this.bExpanded)
	{
		var pDocElement = BX.GetDocElement();
		BX.unbind(window, 'resize', BX.proxy(this.__expand_onresize, this));
		pDocElement.style.overflow = this.__expand_settings.overflow;
	}

	BX.WindowManager.unregister(this);

	return true;
};

BX.CWindow.prototype.SetResize = function(elem)
{
	elem.style.cursor = 'se-resize';
	BX.bind(elem, 'mousedown', BX.proxy(this.__startResize, this));

	this.ELEMENTS.resizer.push(elem);
	this.SETTINGS.resizable = true;
};

BX.CWindow.prototype.SetExpand = function(elem, event_name)
{
	event_name = event_name || 'click';
	BX.bind(elem, event_name, BX.proxy(this.__expand, this));
};

BX.CWindow.prototype.__expand_onresize = function()
{
	var windowSize = BX.GetWindowInnerSize();
	this.DIV.style.width = windowSize.innerWidth + "px";
	this.DIV.style.height = windowSize.innerHeight + "px";

	BX.onCustomEvent(this, 'onWindowResize');
};

BX.CWindow.prototype.__expand = function()
{
	var pDocElement = BX.GetDocElement();

	if (!this.bExpanded)
	{
		var wndScroll = BX.GetWindowScrollPos(),
			wndSize = BX.GetWindowInnerSize();

		this.__expand_settings = {
			resizable: this.SETTINGS.resizable,
			draggable: this.SETTINGS.draggable,
			width: this.DIV.style.width,
			height: this.DIV.style.height,
			left: this.DIV.style.left,
			top: this.DIV.style.top,
			scrollTop: wndScroll.scrollTop,
			scrollLeft: wndScroll.scrollLeft,
			overflow: BX.style(pDocElement, 'overflow')
		};

		this.SETTINGS.resizable = false;
		this.SETTINGS.draggable = false;

		window.scrollTo(0,0);
		pDocElement.style.overflow = 'hidden';

		this.DIV.style.top = '0px';
		this.DIV.style.left = '0px';

		this.DIV.style.width = wndSize.innerWidth + 'px';
		this.DIV.style.height = wndSize.innerHeight + 'px';

		this.bExpanded = true;

		BX.onCustomEvent(this, 'onWindowExpand');
		BX.onCustomEvent(this, 'onWindowResize');

		BX.bind(window, 'resize', BX.proxy(this.__expand_onresize, this));
	}
	else
	{
		BX.unbind(window, 'resize', BX.proxy(this.__expand_onresize, this));

		this.SETTINGS.resizable = this.__expand_settings.resizable;
		this.SETTINGS.draggable = this.__expand_settings.draggable;

		pDocElement.style.overflow = this.__expand_settings.overflow;

		this.DIV.style.top = this.__expand_settings.top;
		this.DIV.style.left = this.__expand_settings.left;
		this.DIV.style.width = this.__expand_settings.width;
		this.DIV.style.height = this.__expand_settings.height;

		window.scrollTo(this.__expand_settings.scrollLeft, this.__expand_settings.scrollTop);

		this.bExpanded = false;

		BX.onCustomEvent(this, 'onWindowNarrow');
		BX.onCustomEvent(this, 'onWindowResize');

	}
};

BX.CWindow.prototype.Resize = function(x, y)
{
	var new_width = Math.max(x - this.pos.left + this.dx, this.SETTINGS.min_width);
	var new_height = Math.max(y - this.pos.top + this.dy, this.SETTINGS.min_height);

	if (this.SETTINGS.resize_restrict)
	{
		var scrollSize = BX.GetWindowScrollSize();

		if (this.pos.left + new_width > scrollSize.scrollWidth - this.dw)
			new_width = scrollSize.scrollWidth - this.pos.left - this.dw;
	}

	this.DIV.style.width = new_width + 'px';
	this.DIV.style.height = new_height + 'px';

	BX.onCustomEvent(this, 'onWindowResize');
};

BX.CWindow.prototype.__startResize = function(e)
{
	if (!this.SETTINGS.resizable)
		return false;

	if(!e) e = window.event;

	this.wndSize = BX.GetWindowScrollPos();
	this.wndSize.innerWidth = BX.GetWindowInnerSize().innerWidth;

	this.pos = BX.pos(this.DIV);

	this.x = e.clientX + this.wndSize.scrollLeft;
	this.y = e.clientY + this.wndSize.scrollTop;

	this.dx = this.pos.left + this.pos.width - this.x;
	this.dy = this.pos.top + this.pos.height - this.y;
	this.dw = this.pos.width - parseInt(this.DIV.style.width);

	BX.bind(document, "mousemove", BX.proxy(this.__moveResize, this));
	BX.bind(document, "mouseup", BX.proxy(this.__stopResize, this));

	if(document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;

	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = 'none';
	b.style.cursor = 'se-resize';

	BX.onCustomEvent(this, 'onWindowResizeStart');

	return true;
};

BX.CWindow.prototype.__moveResize = function(e)
{
	if(!e) e = window.event;

	var windowScroll = BX.GetWindowScrollPos();

	var x = e.clientX + windowScroll.scrollLeft;
	var y = e.clientY + windowScroll.scrollTop;

	if(this.x == x && this.y == y)
		return;

	this.Resize(x, y);

	this.x = x;
	this.y = y;
};

BX.CWindow.prototype.__stopResize = function()
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this.__moveResize, this));
	BX.unbind(document, "mouseup", BX.proxy(this.__stopResize, this));

	document.onmousedown = null;

	var b = document.body;
	b.ondrag = b.onselectstart = null;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = '';
	b.style.cursor = '';

	BX.onCustomEvent(this, 'onWindowResizeFinished')
};

BX.CWindow.prototype.SetClose = function(elem)
{
	BX.bind(elem, 'click', BX.proxy(this.Close, this));
	this.ELEMENTS.close.push(elem);
};

BX.CWindow.prototype.SetDraggable = function(elem)
{
	BX.bind(elem, 'mousedown', BX.proxy(this.__startDrag, this));

	elem.style.cursor = 'move';

	this.ELEMENTS.draggable.push(elem);
	this.SETTINGS.draggable = true;
};

BX.CWindow.prototype.Move = function(x, y)
{
	var dxShadow = 1; // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	var left = parseInt(this.DIV.style.left)+x;
	var top = parseInt(this.DIV.style.top)+y;

	if (this.SETTINGS.drag_restrict)
	{
		//Left side
		if (left < 0)
			left = 0;

		//Right side
		var scrollSize = BX.GetWindowScrollSize();
		var floatWidth = this.DIV.offsetWidth;
		var floatHeight = this.DIV.offsetHeight;

		if (left > (scrollSize.scrollWidth - floatWidth - dxShadow))
			left = scrollSize.scrollWidth - floatWidth - dxShadow;

		if (top > (scrollSize.scrollHeight - floatHeight - dxShadow))
			top = scrollSize.scrollHeight - floatHeight - dxShadow;

		//Top side
		if (top < 0)
			top = 0;
	}

	this.DIV.style.left = left+'px';
	this.DIV.style.top = top+'px';

	//this.AdjustShadow(div);
};

BX.CWindow.prototype.__startDrag = function(e)
{
	if (!this.SETTINGS.draggable)
		return false;

	if(!e) e = window.event;

	this.x = e.clientX + document.body.scrollLeft;
	this.y = e.clientY + document.body.scrollTop;

	this.__bWasDragged = false;
	BX.bind(document, "mousemove", BX.proxy(this.__moveDrag, this));
	BX.bind(document, "mouseup", BX.proxy(this.__stopDrag, this));

	if(document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;

	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = 'none';
	b.style.cursor = 'move';
	return BX.PreventDefault(e);
};

BX.CWindow.prototype.__moveDrag = function(e)
{
	if(!e) e = window.event;

	var x = e.clientX + document.body.scrollLeft;
	var y = e.clientY + document.body.scrollTop;

	if(this.x == x && this.y == y)
		return;

	this.Move((x - this.x), (y - this.y));
	this.x = x;
	this.y = y;

	if (!this.__bWasDragged)
	{
		BX.onCustomEvent(this, 'onWindowDragStart');
		this.__bWasDragged = true;
		BX.bind(BX.proxy_context, "click", BX.PreventDefault);
	}

	BX.onCustomEvent(this, 'onWindowDrag');
};

BX.CWindow.prototype.__stopDrag = function(e)
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this.__moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this.__stopDrag, this));

	document.onmousedown = null;

	var b = document.body;
	b.ondrag = b.onselectstart = null;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = '';
	b.style.cursor = '';

	if (this.__bWasDragged)
	{
		BX.onCustomEvent(this, 'onWindowDragFinished');
		var _proxy_context = BX.proxy_context;
		setTimeout(function(){BX.unbind(_proxy_context, "click", BX.PreventDefault)}, 100);
		this.__bWasDragged = false;
	}
	return BX.PreventDefault(e);
};

BX.CWindow.prototype.DenyClose = function()
{
	this.denyClose = true;
};

BX.CWindow.prototype.AllowClose = function()
{
	this.denyClose = false;
};

BX.CWindow.prototype.ShowError = function(str)
{
	BX.onCustomEvent(this, 'onWindowError', [str]);

	if (this._wait)
		BX.closeWait(this._wait);

	window.alert(str);
};

BX.CWindow.prototype.__set_msover = function() {this.MOUSEOVER = true;};
BX.CWindow.prototype.__unset_msover = function() {this.MOUSEOVER = false;};

/* dialog window class extends window class */
BX.CWindowDialog = function() {
	var a = arguments;
	a[1] = 'dialog';
	BX.CWindowDialog.superclass.constructor.apply(this, a);

	this.DIV.style.top = '10px';
	this.OVERLAY = null;
};
BX.extend(BX.CWindowDialog, BX.CWindow);

BX.CWindowDialog.prototype.__resizeOverlay = function()
{
	var windowSize = BX.GetWindowScrollSize();
	this.OVERLAY.style.width = windowSize.scrollWidth + "px";
};

BX.CWindowDialog.prototype.CreateOverlay = function(zIndex)
{
	if (null == this.OVERLAY)
	{
		var windowSize = BX.GetWindowScrollSize();
		this.OVERLAY = document.body.appendChild(BX.create("DIV", {
			style: {
				position: 'absolute',
				top: '0px',
				left: '0px',
				zIndex: zIndex || (parseInt(this.DIV.style.zIndex)-2),
				width: windowSize.scrollWidth + "px",
				height: windowSize.scrollHeight + "px"
			}
		}));
	}

	return this.OVERLAY;
};

BX.CWindowDialog.prototype.Show = function()
{
	BX.CWindowDialog.superclass.Show.apply(this, arguments);

	this.CreateOverlay();

	this.OVERLAY.style.display = 'block';
	this.OVERLAY.style.zIndex = parseInt(this.DIV.style.zIndex)-2;

	BX.unbind(window, 'resize', BX.proxy(this.__resizeOverlay, this));
	BX.bind(window, 'resize', BX.proxy(this.__resizeOverlay, this));
};

BX.CWindowDialog.prototype.onUnRegister = function(clean)
{
	BX.CWindowDialog.superclass.onUnRegister.apply(this, arguments);

	if (this.clean)
	{
		if (this.OVERLAY.parentNode)
			this.OVERLAY.parentNode.removeChild(this.OVERLAY);
	}
	else
	{
		this.OVERLAY.style.display = 'none';
	}

	BX.unbind(window, 'resize', BX.proxy(this.__resizeOverlay, this));
};

/* standard bitrix dialog extends BX.CWindowDialog */
/*
	arParams = {
		(
			title: 'dialog title',
			head: 'head block html',
			content: 'dialog content',
			icon: 'head icon classname or filename',

			resize_id: 'some id to save resize information'// useless if resizable = false
		)
		or
		(
			content_url: url to content load
				loaded content scripts can use BX.WindowManager.Get() to get access to the current window object
		)

		height: window_height_in_pixels,
		width: window_width_in_pixels,

		draggable: true|false,
		resizable: true|false,

		min_height: min_window_height_in_pixels, // useless if resizable = false
		min_width: min_window_width_in_pixels, // useless if resizable = false

		buttons: [
			'html_code',
			BX.CDialog.btnSave, BX.CDialog.btnCancel, BX.CDialog.btnClose
		]
	}
*/
BX.CDialog = function(arParams)
{
	BX.CDialog.superclass.constructor.apply(this);

	this._sender = 'core_window_cdialog';

	this.PARAMS = arParams || {};

	for (var i in this.defaultParams)
	{
		if (typeof this.PARAMS[i] == 'undefined')
			this.PARAMS[i] = this.defaultParams[i];
	}

	this.PARAMS.width = (!isNaN(parseInt(this.PARAMS.width)))
		? this.PARAMS.width
		: this.defaultParams['width'];
	this.PARAMS.height = (!isNaN(parseInt(this.PARAMS.height)))
		? this.PARAMS.height
		: this.defaultParams['height'];

	if (this.PARAMS.resize_id || this.PARAMS.content_url)
	{
		var arSize = BX.WindowManager.getRuntimeWindowSize(this.PARAMS.resize_id || this.PARAMS.content_url);
		if (arSize)
		{
			this.PARAMS.width = arSize.width;
			this.PARAMS.height = arSize.height;
		}
	}

	BX.addClass(this.DIV, 'bx-core-adm-dialog');
	this.DIV.id = 'bx-admin-prefix';

	this.PARTS = {};

	this.DIV.style.height = null;
	this.DIV.style.width = null;

	this.PARTS.TITLEBAR = this.DIV.appendChild(BX.create('DIV', {props: {
			className: 'bx-core-adm-dialog-head'
		}
	}));

	this.PARTS.TITLE_CONTAINER = this.PARTS.TITLEBAR.appendChild(BX.create('SPAN', {
		props: {className: 'bx-core-adm-dialog-head-inner'},
		text: this.PARAMS.title
	}));

	this.PARTS.TITLEBAR_ICONS = this.PARTS.TITLEBAR.appendChild(BX.create('DIV', {
		props: {
			className: 'bx-core-adm-dialog-head-icons'
		},
		children: (this.PARAMS.resizable ? [
			BX.create('SPAN', {props: {className: 'bx-core-adm-icon-expand', title: BX.message('JS_CORE_WINDOW_EXPAND')}}),
			BX.create('SPAN', {props: {className: 'bx-core-adm-icon-close', title: BX.message('JS_CORE_WINDOW_CLOSE')}})
		] : [
			BX.create('SPAN', {props: {className: 'bx-core-adm-icon-close', title: BX.message('JS_CORE_WINDOW_CLOSE')}})
		])
	}));


	this.PARTS.CONTENT = this.DIV.appendChild(BX.create('DIV', {
		props: {className: 'bx-core-adm-dialog-content-wrap adm-workarea'}
	}));

	this.PARTS.CONTENT_DATA = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
		props: {className: 'bx-core-adm-dialog-content'},
		style: {
			height: this.PARAMS.height + 'px',
			width: this.PARAMS.width + 'px'
		}
	}));

	this.PARTS.HEAD = this.PARTS.CONTENT_DATA.appendChild(BX.create('DIV', {
		props: {
			className: 'bx-core-adm-dialog-head-block' + (this.PARAMS.icon ? ' ' + this.PARAMS.icon : '')
		}
	}));

	this.SetHead(this.PARAMS.head);
	this.SetContent(this.PARAMS.content);
	this.SetTitle(this.PARAMS.title);
	this.SetClose(this.PARTS.TITLEBAR_ICONS.lastChild);

	if (this.PARAMS.resizable)
	{
		this.SetExpand(this.PARTS.TITLEBAR_ICONS.firstChild);
		this.SetExpand(this.PARTS.TITLEBAR, 'dblclick');

		BX.addCustomEvent(this, 'onWindowExpand', BX.proxy(this.__onexpand, this));
		BX.addCustomEvent(this, 'onWindowNarrow', BX.proxy(this.__onexpand, this));
	}

	this.PARTS.FOOT = this.PARTS.BUTTONS_CONTAINER = this.PARTS.CONTENT.appendChild(BX.create('DIV', {
			props: {
				className: 'bx-core-adm-dialog-buttons'
			},
			//events: {
			//	'click': BX.delegateEvent({property:{type: /button|submit/}}, BX.delegate(function() {this.showWait(BX.proxy_context)}, this))
			//},
			children: this.ShowButtons()
		}
	));

	if (this.PARAMS.draggable)
		this.SetDraggable(this.PARTS.TITLEBAR);

	if (this.PARAMS.resizable)
	{
		this.PARTS.RESIZER = this.DIV.appendChild(BX.create('DIV', {
			props: {className: 'bx-core-resizer'}
		}));

		this.SetResize(this.PARTS.RESIZER);

		this.SETTINGS.min_width = this.PARAMS.min_width;
		this.SETTINGS.min_height = this.PARAMS.min_height;
	}

	this.auth_callback = BX.delegate(function(){
		this.PARAMS.content = '';
		this.hideNotify();
		this.Show();
	}, this)
};
BX.extend(BX.CDialog, BX.CWindowDialog);

BX.CDialog.prototype.defaultParams = {
	width: 700,
	height: 400,
	min_width: 500,
	min_height: 300,

	resizable: true,
	draggable: true,

	title: '',
	icon: ''
};

BX.CDialog.prototype.showWait = function(el)
{
	if (BX.type.isElementNode(el) && (el.type == 'button' || el.type == 'submit'))
	{
		BX.defer(function(){el.disabled = true})();

		var bSave = (BX.hasClass(el, 'adm-btn-save') || BX.hasClass(el, 'adm-btn-save')),
			pos = BX.pos(el, true);

		el.bxwaiter = this.PARTS.FOOT.appendChild(BX.create('DIV', {
			props: {className: 'adm-btn-load-img' + (bSave ? '-green' : '')},
			style: {
				top: parseInt((pos.bottom + pos.top)/2 - 10) + 'px',
				left: parseInt((pos.right + pos.left)/2 - 10) + 'px'
			}
		}));

		BX.addClass(el, 'adm-btn-load');

		this.lastWaitElement = el;

		return el.bxwaiter;
	}
	return null;
};

BX.CDialog.prototype.closeWait = function(el)
{
	el = el || this.lastWaitElement;

	if (BX.type.isElementNode(el))
	{
		if (el.bxwaiter)
		{
			if(el.bxwaiter.parentNode)
			{
				el.bxwaiter.parentNode.removeChild(el.bxwaiter);
			}

			el.bxwaiter = null;
		}

		el.disabled = false;
		BX.removeClass(el, 'adm-btn-load');

		if (this.lastWaitElement == el)
			this.lastWaitElement = null;
	}
};

BX.CDialog.prototype.Authorize = function(arAuthResult)
{
	this.bSkipReplaceContent = true;
	this.ShowError(BX.message('JSADM_AUTH_REQ'));

	BX.onCustomEvent(this, 'onWindowError', []);

	BX.closeWait();

	(new BX.CAuthDialog({
		content_url: this.PARAMS.content_url,
		auth_result: arAuthResult,
		callback: BX.delegate(function(){
			if (this.auth_callback)
				this.auth_callback()
		}, this)
	})).Show();
};

BX.CDialog.prototype.ShowError = function(str)
{
	BX.onCustomEvent(this, 'onWindowError', [str]);

	this.closeWait();

	if (this._wait)
		BX.closeWait(this._wait);

	this.Notify(str, true);
};


BX.CDialog.prototype.__expandGetSize = function()
{
	var pDocElement = BX.GetDocElement();
	pDocElement.style.overflow = 'hidden';

	var wndSize = BX.GetWindowInnerSize();

	pDocElement.scrollTop = 0;

	this.DIV.style.top = '-' + this.dxShadow + 'px';
	this.DIV.style.left = '-' + this.dxShadow + 'px';

	return {
		width: (wndSize.innerWidth - parseInt(BX.style(this.PARTS.CONTENT, 'padding-right')) - parseInt(BX.style(this.PARTS.CONTENT, 'padding-left'))) + this.dxShadow,
		height: (wndSize.innerHeight - this.PARTS.TITLEBAR.offsetHeight - this.PARTS.FOOT.offsetHeight - parseInt(BX.style(this.PARTS.CONTENT, 'padding-top')) - parseInt(BX.style(this.PARTS.CONTENT, 'padding-bottom'))) + this.dxShadow
	};
};

BX.CDialog.prototype.__expand = function()
{
	var pDocElement = BX.GetDocElement();
	this.dxShadow = 2;

	if (!this.bExpanded)
	{
		var wndScroll = BX.GetWindowScrollPos();

		this.__expand_settings = {
			resizable: this.SETTINGS.resizable,
			draggable: this.SETTINGS.draggable,
			width: this.PARTS.CONTENT_DATA.style.width,
			height: this.PARTS.CONTENT_DATA.style.height,
			left: this.DIV.style.left,
			top: this.DIV.style.top,
			scrollTop: wndScroll.scrollTop,
			scrollLeft: wndScroll.scrollLeft,
			overflow: BX.style(pDocElement, 'overflow')
		};

		this.SETTINGS.resizable = false;
		this.SETTINGS.draggable = false;

		var pos = this.__expandGetSize();

		this.PARTS.CONTENT_DATA.style.width = pos.width + 'px';
		this.PARTS.CONTENT_DATA.style.height = pos.height + 'px';

		window.scrollTo(0,0);
		pDocElement.style.overflow = 'hidden';

		this.bExpanded = true;

		BX.onCustomEvent(this, 'onWindowExpand');
		BX.onCustomEvent(this, 'onWindowResize');
		BX.onCustomEvent(this, 'onWindowResizeExt', [{'width': pos.width, 'height': pos.height}]);

		BX.bind(window, 'resize', BX.proxy(this.__expand_onresize, this));
	}
	else
	{
		BX.unbind(window, 'resize', BX.proxy(this.__expand_onresize, this));

		this.SETTINGS.resizable = this.__expand_settings.resizable;
		this.SETTINGS.draggable = this.__expand_settings.draggable;

		pDocElement.style.overflow = this.__expand_settings.overflow;

		this.DIV.style.top = this.__expand_settings.top;
		this.DIV.style.left = this.__expand_settings.left;
		this.PARTS.CONTENT_DATA.style.width = this.__expand_settings.width;
		this.PARTS.CONTENT_DATA.style.height = this.__expand_settings.height;
		window.scrollTo(this.__expand_settings.scrollLeft, this.__expand_settings.scrollTop);
		this.bExpanded = false;

		BX.onCustomEvent(this, 'onWindowNarrow');
		BX.onCustomEvent(this, 'onWindowResize');
		BX.onCustomEvent(this, 'onWindowResizeExt', [{'width': parseInt(this.__expand_settings.width), 'height': parseInt(this.__expand_settings.height)}]);
	}
};

BX.CDialog.prototype.__expand_onresize = function()
{
	var pos = this.__expandGetSize();

	this.PARTS.CONTENT_DATA.style.width = pos.width + 'px';
	this.PARTS.CONTENT_DATA.style.height = pos.height + 'px';

	BX.onCustomEvent(this, 'onWindowResize');
	BX.onCustomEvent(this, 'onWindowResizeExt', [pos]);
};

BX.CDialog.prototype.__onexpand = function()
{
	var ob = this.PARTS.TITLEBAR_ICONS.firstChild;
	ob.className = BX.toggle(ob.className, ['bx-core-adm-icon-expand', 'bx-core-adm-icon-narrow']);
	ob.title = BX.toggle(ob.title, [BX.message('JS_CORE_WINDOW_EXPAND'), BX.message('JS_CORE_WINDOW_NARROW')]);

	if (this.PARTS.RESIZER)
	{
		this.PARTS.RESIZER.style.display = this.bExpanded ? 'none' : 'block';
	}
};


BX.CDialog.prototype.__startResize = function(e)
{
	if (!this.SETTINGS.resizable)
		return false;

	if(!e) e = window.event;

	this.wndSize = BX.GetWindowScrollPos();
	this.wndSize.innerWidth = BX.GetWindowInnerSize().innerWidth;

	this.pos = BX.pos(this.PARTS.CONTENT_DATA);

	this.x = e.clientX + this.wndSize.scrollLeft;
	this.y = e.clientY + this.wndSize.scrollTop;

	this.dx = this.pos.left + this.pos.width - this.x;
	this.dy = this.pos.top + this.pos.height - this.y;


	// TODO: suspicious
	this.dw = this.pos.width - parseInt(this.PARTS.CONTENT_DATA.style.width) + parseInt(BX.style(this.PARTS.CONTENT, 'padding-right'));

	BX.bind(document, "mousemove", BX.proxy(this.__moveResize, this));
	BX.bind(document, "mouseup", BX.proxy(this.__stopResize, this));

	if(document.body.setCapture)
		document.body.setCapture();

	document.onmousedown = BX.False;

	var b = document.body;
	b.ondrag = b.onselectstart = BX.False;
	b.style.MozUserSelect = this.DIV.style.MozUserSelect = 'none';
	b.style.cursor = 'se-resize';

	BX.onCustomEvent(this, 'onWindowResizeStart');

	return true;
};

BX.CDialog.prototype.Resize = function(x, y)
{
	var new_width = Math.max(x - this.pos.left + this.dx, this.SETTINGS.min_width);
	var new_height = Math.max(y - this.pos.top + this.dy, this.SETTINGS.min_height);

	if (this.SETTINGS.resize_restrict)
	{
		var scrollSize = BX.GetWindowScrollSize();

		if (this.pos.left + new_width > scrollSize.scrollWidth - this.dw)
			new_width = scrollSize.scrollWidth - this.pos.left - this.dw;
	}

	this.PARTS.CONTENT_DATA.style.width = new_width + 'px';
	this.PARTS.CONTENT_DATA.style.height = new_height + 'px';

	BX.onCustomEvent(this, 'onWindowResize');
	BX.onCustomEvent(this, 'onWindowResizeExt', [{'height': new_height, 'width': new_width}]);
};

BX.CDialog.prototype.SetSize = function(obSize)
{
	this.PARTS.CONTENT_DATA.style.width = obSize.width + 'px';
	this.PARTS.CONTENT_DATA.style.height = obSize.height + 'px';

	BX.onCustomEvent(this, 'onWindowResize');
	BX.onCustomEvent(this, 'onWindowResizeExt', [obSize]);
};

BX.CDialog.prototype.GetParameters = function(form_name)
{
	var form = this.GetForm();

	if(!form)
		return "";

	var i, s = "";
	var n = form.elements.length;

	var delim = '';
	for(i=0; i<n; i++)
	{
		if (s != '') delim = '&';
		var el = form.elements[i];
		if (el.disabled)
			continue;

		switch(el.type.toLowerCase())
		{
			case 'text':
			case 'textarea':
			case 'password':
			case 'hidden':
				if (null == form_name && el.name.substr(el.name.length-4) == '_alt' && form.elements[el.name.substr(0, el.name.length-4)])
					break;
				s += delim + el.name + '=' + BX.util.urlencode(el.value);
				break;
			case 'radio':
				if(el.checked)
					s += delim + el.name + '=' + BX.util.urlencode(el.value);
				break;
			case 'checkbox':
				s += delim + el.name + '=' + BX.util.urlencode(el.checked ? 'Y':'N');
				break;
			case 'select-one':
				var val = "";
				if (null == form_name && form.elements[el.name + '_alt'] && el.selectedIndex == 0)
					val = form.elements[el.name+'_alt'].value;
				else
					val = el.value;
				s += delim + el.name + '=' + BX.util.urlencode(val);
				break;
			case 'select-multiple':
				var j, bAdded = false;
				var l = el.options.length;
				for (j=0; j<l; j++)
				{
					if (el.options[j].selected)
					{
						s += delim + el.name + '=' + BX.util.urlencode(el.options[j].value);
						bAdded = true;
					}
				}
				if (!bAdded)
					s += delim + el.name + '=';
				break;
			default:
				break;
		}
	}

	return s;
};

BX.CDialog.prototype.PostParameters = function(params)
{
	var url = this.PARAMS.content_url;

	if (null == params)
		params = "";

	params += (params == "" ? "" : "&") + "bxsender=" + this._sender;

	var index = url.indexOf('?');
	if (index == -1)
		url += '?' + params;
	else
		url = url.substring(0, index) + '?' + params + "&" + url.substring(index+1);

	BX.showWait();

	this.auth_callback = BX.delegate(function(){
		this.hideNotify();
		this.PostParameters(params);
	}, this);

	BX.ajax.Setup({skipAuthCheck:true},true);
	BX.ajax.post(url, this.GetParameters(), BX.delegate(function(result) {
		BX.closeWait();
		if (!this.bSkipReplaceContent)
		{
			this.ClearButtons(); // buttons are appended during form reload, so we should clear footer
			this.SetContent(result);
			this.Show(true);
		}

		this.bSkipReplaceContent = false;
	}, this));
};

BX.CDialog.prototype.Submit = function(params, url)
{
	var FORM = this.GetForm();
	if (FORM)
	{
		FORM.onsubmit = null;

		FORM.method = 'POST';
		if (!FORM.action || url)
		{
			url = url || this.PARAMS.content_url;
			if (null != params)
			{
				var index = url.indexOf('?');
				if (index == -1)
					url += '?' + params;
				else
					url = url.substring(0, index) + '?' + params + "&" + url.substring(index+1);
			}

			FORM.action = url;
		}

		if (!FORM._bxsender)
		{
			FORM._bxsender = FORM.appendChild(BX.create('INPUT', {
				attrs: {
					type: 'hidden',
					name: 'bxsender',
					value: this._sender
				}
			}));
		}

		this._wait = BX.showWait();

		this.auth_callback = BX.delegate(function(){
			this.hideNotify();
			this.Submit(params);
		}, this);

		BX.ajax.submit(FORM, BX.delegate(function(){this.closeWait()}, this));
	}
	else
	{
		window.alert('no form registered!');
	}
};

BX.CDialog.prototype.GetForm = function()
{
	if (null == this.__form)
	{
		var forms = this.PARTS.CONTENT_DATA.getElementsByTagName('FORM');
		this.__form = forms[0] ? forms[0] : null;
	}

	return this.__form;
};

BX.CDialog.prototype.GetRealForm = function()
{
	if (null == this.__rform)
	{
		var forms = this.PARTS.CONTENT_DATA.getElementsByTagName('FORM');
		this.__rform = forms[1] ? forms[1] : (forms[0] ? forms[0] : null);
	}

	return this.__rform;
};

BX.CDialog.prototype._checkButton = function(btn)
{
	var arCustomButtons = ['btnSave', 'btnCancel', 'btnClose'];

	for (var i = 0; i < arCustomButtons.length; i++)
	{
		if (this[arCustomButtons[i]] && (btn == this[arCustomButtons[i]]))
			return arCustomButtons[i];
	}

	return false;
};

BX.CDialog.prototype.ShowButtons = function()
{
	var result = [];
	if (this.PARAMS.buttons)
	{
		if (this.PARAMS.buttons.title) this.PARAMS.buttons = [this.PARAMS.buttons];

		for (var i=0, len=this.PARAMS.buttons.length; i<len; i++)
		{
			if (BX.type.isNotEmptyString(this.PARAMS.buttons[i]))
			{
				result.push(this.PARAMS.buttons[i]);
			}
			else if (this.PARAMS.buttons[i])
			{
				//if (!(this.PARAMS.buttons[i] instanceof BX.CWindowButton))
				if (!BX.is_subclass_of(this.PARAMS.buttons[i], BX.CWindowButton))
				{
					var b = this._checkButton(this.PARAMS.buttons[i]); // hack to set links to real CWindowButton object in btnSave etc;
					this.PARAMS.buttons[i] = new BX.CWindowButton(this.PARAMS.buttons[i]);
					if (b) this[b] = this.PARAMS.buttons[i];
				}

				result.push(this.PARAMS.buttons[i].Button(this));
			}
		}
	}

	return result;
};

BX.CDialog.prototype.setAutosave = function () {
	if (!this.bSetAutosaveDelay)
	{
		this.bSetAutosaveDelay = true;
		setTimeout(BX.proxy(this.setAutosave, this), 10);
	}
};

BX.CDialog.prototype.SetTitle = function(title)
{
	this.PARAMS.title = title;
	BX.cleanNode(this.PARTS.TITLE_CONTAINER).appendChild(document.createTextNode(this.PARAMS.title));
};

BX.CDialog.prototype.SetHead = function(head)
{
	this.PARAMS.head = BX.util.trim(head);
	this.PARTS.HEAD.innerHTML = this.PARAMS.head || "&nbsp;";
	this.PARTS.HEAD.style.display = this.PARAMS.head ? 'block' : 'none';
	this.adjustSize();
};

BX.CDialog.prototype.Notify = function(note, bError)
{
	if (!this.PARTS.NOTIFY)
	{
		this.PARTS.NOTIFY = this.DIV.insertBefore(BX.create('DIV', {
			props: {className: 'adm-warning-block'},
			children: [
				BX.create('SPAN', {
					props: {className: 'adm-warning-text'}
				}),
				BX.create('SPAN', {
					props: {className: 'adm-warning-icon'}
				}),
				BX.create('SPAN', {
					props: {className: 'adm-warning-close'},
					events: {click: BX.proxy(this.hideNotify, this)}
				})
			]
		}), this.DIV.firstChild);
	}

	if (bError)
		BX.addClass(this.PARTS.NOTIFY, 'adm-warning-block-red');
	else
		BX.removeClass(this.PARTS.NOTIFY, 'adm-warning-block-red');

	this.PARTS.NOTIFY.firstChild.innerHTML = note || '&nbsp;';
	this.PARTS.NOTIFY.firstChild.style.width = (this.PARAMS.width-50) + 'px';
	BX.removeClass(this.PARTS.NOTIFY, 'adm-warning-animate');
};

BX.CDialog.prototype.hideNotify = function()
{
	BX.addClass(this.PARTS.NOTIFY, 'adm-warning-animate');
};

BX.CDialog.prototype.__adjustHeadToIcon = function()
{
	if (!this.PARTS.HEAD.offsetHeight)
	{
		setTimeout(BX.delegate(this.__adjustHeadToIcon, this), 50);
	}
	else
	{
		if (this.icon_image && this.icon_image.height && this.icon_image.height > this.PARTS.HEAD.offsetHeight - 5)
		{
			this.PARTS.HEAD.style.height = this.icon_image.height + 5 + 'px';
			this.adjustSize();
		}

		this.icon_image.onload = null;
		this.icon_image = null;
	}
};

BX.CDialog.prototype.SetIcon = function(icon_class)
{
	if (this.PARAMS.icon != icon_class)
	{
		if (this.PARAMS.icon)
			BX.removeClass(this.PARTS.HEAD, this.PARAMS.icon);

		this.PARAMS.icon = icon_class;

		if (this.PARAMS.icon)
		{
			BX.addClass(this.PARTS.HEAD, this.PARAMS.icon);

			var icon_file = (BX.style(this.PARTS.HEAD, 'background-image') || BX.style(this.PARTS.HEAD, 'backgroundImage'));
			if (BX.type.isNotEmptyString(icon_file) && icon_file != 'none')
			{
				var match = icon_file.match(new RegExp('url\\s*\\(\\s*(\'|"|)(.+?)(\\1)\\s*\\)'));
				if(match)
				{
					icon_file = match[2];
					if (BX.type.isNotEmptyString(icon_file))
					{
						this.icon_image = new Image();
						this.icon_image.onload = BX.delegate(this.__adjustHeadToIcon, this);
						this.icon_image.src = icon_file;
					}
				}
			}
		}
	}
	this.adjustSize();
};

BX.CDialog.prototype.SetIconFile = function(icon_file)
{
	this.icon_image = new Image();
	this.icon_image.onload = BX.delegate(this.__adjustHeadToIcon, this);
	this.icon_image.src = icon_file;

	BX.adjust(this.PARTS.HEAD, {style: {backgroundImage: 'url(' + icon_file + ')', backgroundPosition: 'right 9px'/*'99% center'*/}});
	this.adjustSize();
};

/*
BUTTON: {
	title: 'title',
	'action': function executed in window object context
}
BX.CDialog.btnSave || BX.CDialog.btnCancel - standard buttons
*/

BX.CDialog.prototype.SetButtons = function(a)
{
	if (BX.type.isString(a))
	{
		if (a.length > 0)
		{
			this.PARTS.BUTTONS_CONTAINER.innerHTML += a;

			var btns = this.PARTS.BUTTONS_CONTAINER.getElementsByTagName('INPUT');
			if (btns.length > 0)
			{
				this.PARAMS.buttons = [];
				for (var i = 0; i < btns.length; i++)
				{
					this.PARAMS.buttons.push(new BX.CWindowButton({btn: btns[i], parentWindow: this}));
				}
			}
		}
	}
	else
	{
		this.PARAMS.buttons = a;
		BX.adjust(this.PARTS.BUTTONS_CONTAINER, {
			children: this.ShowButtons()
		});
	}
	this.adjustSize();
};

BX.CDialog.prototype.ClearButtons = function()
{
	BX.cleanNode(this.PARTS.BUTTONS_CONTAINER);
	this.adjustSize();
};

BX.CDialog.prototype.SetContent = function(html)
{
	this.__form = null;

	if (BX.type.isElementNode(html))
	{
		if (html.parentNode)
			html.parentNode.removeChild(html);
	}
	else if (BX.type.isString(html))
	{
		html = BX.create('DIV', {html: html});
	}

	this.PARAMS.content = html;
	BX.cleanNode(this.PARTS.CONTENT_DATA);

	BX.adjust(this.PARTS.CONTENT_DATA, {
		children: [
			this.PARTS.HEAD,
			BX.create('DIV', {
				props: {
					className: 'bx-core-adm-dialog-content-wrap-inner'
				},
				children: [this.PARAMS.content]
			})
		]
	});

	if (this.PARAMS.content_url && this.GetForm())
	{
		this.__form.submitbtn = this.__form.appendChild(BX.create('INPUT', {props:{type:'submit'},style:{display:'none'}}));
		this.__form.onsubmit = BX.delegate(this.__submit, this);
	}
};

BX.CDialog.prototype.__submit = function(e)
{
	for (var i=0,len=this.PARAMS.buttons.length; i<len; i++)
	{
		if (
			this.PARAMS.buttons[i]
			&& (
				this.PARAMS.buttons[i].name && /save|apply/i.test(this.PARAMS.buttons[i].name)
				||
				this.PARAMS.buttons[i].btn && this.PARAMS.buttons[i].btn.name && /save|apply/i.test(this.PARAMS.buttons[i].btn.name)
			)
		)
		{
			this.PARAMS.buttons[i].emulate();
			break;
		}
	}

	return BX.PreventDefault(e);
};

BX.CDialog.prototype.SwapContent = function(cont)
{
	cont = BX(cont);

	BX.cleanNode(this.PARTS.CONTENT_DATA);
	cont.parentNode.removeChild(cont);
	this.PARTS.CONTENT_DATA.appendChild(cont);
	cont.style.display = 'block';
	this.SetContent(cont.innerHTML);
};

// this method deprecated
BX.CDialog.prototype.adjustSize = function()
{
};

// this method deprecated
BX.CDialog.prototype.__adjustSize = function()
{
};

BX.CDialog.prototype.adjustSizeEx = function()
{
	BX.defer(this.__adjustSizeEx, this)();
};

BX.CDialog.prototype.__adjustSizeEx = function()
{
	var ob = this.PARTS.CONTENT_DATA.firstChild, new_height = 0;
	while (ob)
	{
		new_height += ob.offsetHeight
			+ parseInt(BX.style(ob, 'margin-top'))
			+ parseInt(BX.style(ob, 'margin-bottom'));

		ob = BX.nextSibling(ob);
	}

	if (new_height)
		this.PARTS.CONTENT_DATA.style.height = new_height + 'px';
};


BX.CDialog.prototype.__onResizeFinished = function()
{
	BX.WindowManager.saveWindowSize(
		this.PARAMS.resize_id || this.PARAMS.content_url, {height: parseInt(this.PARTS.CONTENT_DATA.style.height), width: parseInt(this.PARTS.CONTENT_DATA.style.width)}
	);
};

BX.CDialog.prototype.Show = function(bNotRegister)
{
	if ((!this.PARAMS.content) && this.PARAMS.content_url && BX.ajax && !bNotRegister)
	{
		var wait = BX.showWait();

		BX.WindowManager.currently_loaded = this;

		this.CreateOverlay(parseInt(BX.style(wait, 'z-index'))-1);
		this.OVERLAY.style.display = 'block';
		this.OVERLAY.className = 'bx-core-dialog-overlay';

		var post_data = '', method = 'GET';
		if (this.PARAMS.content_post)
		{
			post_data = this.PARAMS.content_post;
			method = 'POST';
		}

		var url = this.PARAMS.content_url
			+ (this.PARAMS.content_url.indexOf('?')<0?'?':'&')+'bxsender=' + this._sender;

		this.auth_callback = BX.delegate(function(){
			this.PARAMS.content = '';
			this.hideNotify();
			this.Show();
		}, this);

		BX.ajax({
			method: method,
			dataType: 'html',
			url: url,
			data: post_data,
			skipAuthCheck: true,
			onsuccess: BX.delegate(function(data) {
				BX.closeWait(null, wait);

				this.SetContent(data || '&nbsp;');
				this.Show();
			}, this),
			processScriptsConsecutive: true
		});
	}
	else
	{
		BX.WindowManager.currently_loaded = null;
		BX.CDialog.superclass.Show.apply(this, arguments);

		this.adjustPos();

		this.OVERLAY.className = 'bx-core-dialog-overlay';

		this.__adjustSize();

		BX.addCustomEvent(this, 'onWindowResize', BX.proxy(this.__adjustSize, this));

		if (this.PARAMS.resizable && (this.PARAMS.content_url || this.PARAMS.resize_id))
			BX.addCustomEvent(this, 'onWindowResizeFinished', BX.delegate(this.__onResizeFinished, this));
	}
};

BX.CDialog.prototype.GetInnerPos = function()
{
	return {'width': parseInt(this.PARTS.CONTENT_DATA.style.width), 'height': parseInt(this.PARTS.CONTENT_DATA.style.height)};
};

BX.CDialog.prototype.adjustPos = function()
{
	if (!this.bExpanded)
	{
		var windowSize = BX.GetWindowInnerSize();
		var windowScroll = BX.GetWindowScrollPos();

		BX.adjust(this.DIV, {
			style: {
				left: parseInt(windowScroll.scrollLeft + windowSize.innerWidth / 2 - parseInt(this.DIV.offsetWidth) / 2) + 'px',
				top: Math.max(parseInt(windowScroll.scrollTop + windowSize.innerHeight / 2 - parseInt(this.DIV.offsetHeight) / 2), 0) + 'px'
			}
		});
	}
};

BX.CDialog.prototype.GetContent = function () {return this.PARTS.CONTENT_DATA};

BX.CDialog.prototype.btnSave = BX.CDialog.btnSave = {
	title: BX.message('JS_CORE_WINDOW_SAVE'),
	id: 'savebtn',
	name: 'savebtn',
	className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
	action: function () {
		this.disableUntilError();
		this.parentWindow.PostParameters();
	}
};

BX.CDialog.prototype.btnCancel = BX.CDialog.btnCancel = {
	title: BX.message('JS_CORE_WINDOW_CANCEL'),
	id: 'cancel',
	name: 'cancel',
	action: function () {
		this.parentWindow.Close();
	}
};

BX.CDialog.prototype.btnClose = BX.CDialog.btnClose = {
	title: BX.message('JS_CORE_WINDOW_CLOSE'),
	id: 'close',
	name: 'close',
	action: function () {
		this.parentWindow.Close();
	}
};

/* special child for admin forms loaded into public page */
BX.CAdminDialog = function(arParams)
{
	BX.CAdminDialog.superclass.constructor.apply(this, arguments);

	this._sender = 'core_window_cadmindialog';

	BX.addClass(this.DIV, 'bx-core-adm-admin-dialog');

	this.PARTS.CONTENT.insertBefore(this.PARTS.HEAD, this.PARTS.CONTENT.firstChild);
	this.PARTS.HEAD.className = 'bx-core-adm-dialog-tabs';
};
BX.extend(BX.CAdminDialog, BX.CDialog);

BX.CAdminDialog.prototype.SetHead = function()
{
	BX.CAdminDialog.superclass.SetHead.apply(this, arguments);

	if (this.PARTS.HEAD.firstChild && BX.type.isElementNode(this.PARTS.HEAD.firstChild))
	{
		var ob = this.PARTS.HEAD.firstChild, new_width = 0, marginLeft = 0, marginRight = 0;

		while (ob)
		{
			marginLeft = parseInt(BX.style(ob, 'margin-left'), 10);
			if (isNaN(marginLeft))
			{
				marginLeft = 0;
			}
			marginRight = parseInt(BX.style(ob, 'margin-right'), 10);
			if (isNaN(marginRight))
			{
				marginRight = 0;
			}
			new_width += ob.offsetWidth + marginLeft + marginRight;
			ob = BX.nextSibling(ob);
		}

		this.SETTINGS.min_width = Math.max(new_width, this.SETTINGS.min_width) - 2;
		if (this.PARAMS.width < this.SETTINGS.min_width)
		{
			BX.adjust(this.PARTS.CONTENT_DATA, {
				style: {
					width: this.SETTINGS.min_width + 'px'
				}
			});
		}
	}
};

BX.CAdminDialog.prototype.SetContent = function(html)
{
	this.__form = null;

	if (BX.type.isElementNode(html))
	{
		if (html.parentNode)
			html.parentNode.removeChild(html);
	}

	this.PARAMS.content = html;
	BX.cleanNode(this.PARTS.CONTENT_DATA);

	BX.adjust(this.PARTS.CONTENT_DATA, {
		children: [
			this.PARAMS.content || '&nbsp;'
		]
	});

	if (this.PARAMS.content_url && this.GetForm())
	{
		this.__form.appendChild(BX.create('INPUT', {props:{type:'submit'},style:{display:'none'}}));
		this.__form.onsubmit = BX.delegate(this.__submit, this);
	}
};

BX.CAdminDialog.prototype.__adjustSizeEx = function()
{
	var new_height = BX.firstChild(this.PARTS.CONTENT_DATA).offsetHeight;
	if (new_height)
		this.PARTS.CONTENT_DATA.style.height = new_height + 'px';
};

BX.CAdminDialog.prototype.__expandGetSize = function()
{
	var res = BX.CAdminDialog.superclass.__expandGetSize.apply(this, arguments);

	res.width -= parseInt(BX.style(this.PARTS.CONTENT_DATA, 'padding-right')) + parseInt(BX.style(this.PARTS.CONTENT_DATA, 'padding-left'));
	res.height -= parseInt(BX.style(this.PARTS.CONTENT_DATA, 'padding-top')) + parseInt(BX.style(this.PARTS.CONTENT_DATA, 'padding-bottom'));

	res.height -= this.PARTS.HEAD.offsetHeight;

	return res;
};

BX.CAdminDialog.prototype.Submit = function()
{
	var FORM = this.GetForm();
	if (FORM && !FORM['bxpublic'] && !/bxpublic=/.test(FORM.action))
	{
		FORM.appendChild(BX.create('INPUT', {
			props: {
				type: 'hidden',
				name: 'bxpublic',
				value: 'Y'
			}
		}));
	}

	return BX.CAdminDialog.superclass.Submit.apply(this, arguments);
};

BX.CAdminDialog.prototype.btnSave = BX.CAdminDialog.btnSave = {
	title: BX.message('JS_CORE_WINDOW_SAVE'),
	id: 'savebtn',
	name: 'savebtn',
	className: 'adm-btn-save',
	action: function () {
		this.disableUntilError();
		this.parentWindow.Submit();
	}
};

BX.CAdminDialog.btnCancel = BX.CAdminDialog.superclass.btnCancel;
BX.CAdminDialog.btnClose = BX.CAdminDialog.superclass.btnClose;

BX.CDebugDialog = function(arParams)
{
	BX.CDebugDialog.superclass.constructor.apply(this, arguments);
};
BX.extend(BX.CDebugDialog, BX.CDialog);

BX.CDebugDialog.prototype.ShowDetails = function(div_id)
{
	var div = BX(div_id);
	if (div)
	{
		if (this.div_detail_current)
			this.div_detail_current.style.display = 'none';

		div.style.display = 'block';
		this.div_detail_current = div;
	}
};

BX.CDebugDialog.prototype.SetContent = function(html)
{
	if (!html)
		return;

	var arHtml = html.split('#DIVIDER#');
	if (arHtml.length > 1)
	{
		this.PARAMS.content = arHtml[1];

		this.PARTS.CONTENT_DATA.style.overflow = 'hidden';

		BX.CDebugDialog.superclass.SetContent.apply(this, [arHtml[1]]);

		this.PARTS.CONTENT_INNER = this.PARTS.CONTENT_DATA.firstChild.nextSibling;
		this.PARTS.CONTENT_TOP = this.PARTS.CONTENT_DATA.insertBefore(BX.create('DIV', {
			props: {
				className: 'bx-debug-content-top'
			},
			html: arHtml[0]
		}), this.PARTS.CONTENT_INNER);
		this.PARTS.CONTENT_INNER.style.overflow = 'auto';
	}
	else
	{
		BX.CDebugDialog.superclass.SetContent.apply(this, arguments);
	}
};

BX.CDebugDialog.prototype.__adjustSize = function()
{
	BX.CDebugDialog.superclass.__adjustSize.apply(this, arguments);

	if (this.PARTS.CONTENT_TOP)
	{
		var new_height = this.PARTS.CONTENT_DATA.offsetHeight - this.PARTS.HEAD.offsetHeight - this.PARTS.CONTENT_TOP.offsetHeight - 38;

		if (new_height > 0)
		{
			this.PARTS.CONTENT_INNER.style.height = new_height + 'px';
		}
	}
};


/* class for dialog window with editors */

BX.CEditorDialog = function(arParams)
{
	BX.CEditorDialog.superclass.constructor.apply(this, arguments);

	BX.removeClass(this.PARTS.CONTENT, 'bx-core-adm-dialog-content-wrap');
	BX.removeClass(this.PARTS.CONTENT_DATA, 'bx-core-adm-dialog-content');

	BX.removeClass(this.PARTS.CONTENT_DATA.lastChild, 'bx-core-adm-dialog-content-wrap-inner');
	BX.removeClass(this.PARTS.BUTTONS_CONTAINER, 'bx-core-adm-dialog-buttons');

	BX.addClass(this.PARTS.CONTENT, 'bx-core-editor-dialog-content-wrap');
	BX.addClass(this.PARTS.CONTENT_DATA, 'bx-core-editor-dialog-content');
	BX.addClass(this.PARTS.BUTTONS_CONTAINER, 'bx-core-editor-dialog-buttons');
};
BX.extend(BX.CEditorDialog, BX.CDialog);

BX.CEditorDialog.prototype.SetContent  = function()
{
	BX.CEditorDialog.superclass.SetContent.apply(this, arguments);

	BX.removeClass(this.PARTS.CONTENT_DATA.lastChild, 'bx-core-adm-dialog-content-wrap-inner');
};

/* class for wizards in admin section */
BX.CWizardDialog = function(arParams)
{
	BX.CWizardDialog.superclass.constructor.apply(this, arguments);

	BX.removeClass(this.PARTS.CONTENT, 'bx-core-adm-dialog-content-wrap');
	BX.removeClass(this.PARTS.CONTENT_DATA, 'bx-core-adm-dialog-content');
	BX.removeClass(this.PARTS.CONTENT_DATA.lastChild, 'bx-core-adm-dialog-content-wrap-inner');
	BX.removeClass(this.PARTS.BUTTONS_CONTAINER, 'bx-core-adm-dialog-buttons');

	BX.addClass(this.PARTS.CONTENT, 'bx-core-wizard-dialog-content-wrap');
};

BX.extend(BX.CWizardDialog, BX.CDialog);

/* class for auth dialog */
BX.CAuthDialog = function(arParams)
{
	arParams.resizable = false;
	arParams.width = 350;
	arParams.height = 200;

	arParams.buttons = [this.btnSave];

	BX.CAuthDialog.superclass.constructor.apply(this, arguments);
	this._sender = 'core_window_cauthdialog';

	BX.addClass(this.DIV, 'bx-core-auth-dialog');

	BX.AUTHAGENT = this;
};
BX.extend(BX.CAuthDialog, BX.CDialog);

BX.CAuthDialog.prototype.btnSave = BX.CAuthDialog.btnSave = {
	title: BX.message('JS_CORE_WINDOW_AUTH'),
	id: 'savebtn',
	name: 'savebtn',
	className: 'adm-btn-save',
	action: function () {
		this.disableUntilError();
		this.parentWindow.Submit('', this.parentWindow.PARAMS.content_url);
	}
};

BX.CAuthDialog.prototype.SetError = function(error)
{
	BX.closeWait();

	if (!!error)
		this.ShowError(error.MESSAGE || error);
};

BX.CAuthDialog.prototype.setAuthResult = function(result)
{
	BX.closeWait();

	if (result === false)
	{
		this.Close();
		if (this.PARAMS.callback)
			this.PARAMS.callback();
	}
	else
	{
		this.SetError(result);
	}
};

/* MENU CLASSES */

BX.CWindowFloat = function(node)
{
	BX.CWindowFloat.superclass.constructor.apply(this, [node, 'float']);

	this.SETTINGS.resizable = false;
};
BX.extend(BX.CWindowFloat, BX.CWindow);

BX.CWindowFloat.prototype.adjustPos = function()
{
	if (this.PARAMS.parent)
		this.adjustToNode();
	else if (this.PARAMS.x && this.PARAMS.y)
		this.adjustToPos([this.PARAMS.x, this.PARAMS.y]);
};

BX.CWindowFloat.prototype.adjustToPos = function(pos)
{
	this.DIV.style.left = parseInt(pos[0]) + 'px';
	this.DIV.style.top = parseInt(pos[1]) + 'px';
};

BX.CWindowFloat.prototype.adjustToNodeGetPos = function()
{
	return BX.pos(this.PARAMS.parent);
};

BX.CWindowFloat.prototype.adjustToNode = function(el)
{
	el = el || this.PARAMS.parent;

	this.PARAMS.parent = BX(el);

	if (this.PARAMS.parent)
	{
		var pos = this.adjustToNodeGetPos();

		this.DIV.style.top = pos.top + 'px';//(pos.top - 26) + 'px';
		this.DIV.style.left = pos.left + 'px';

		this.PARAMS.parent.OPENER = this;
	}
};

BX.CWindowFloat.prototype.Show = function()
{
	this.adjustToPos([-1000, -1000]);
	BX.CWindowFloat.superclass.Show.apply(this, arguments);
	this.adjustPos();
};

/* menu opener class */
/*
{
	DOMNode DIV,
	BX.CMenu or Array MENU,
	TYPE = 'hover' | 'click',
	TIMEOUT: 1000
	ATTACH_MODE: 'top' | 'right'
	ACTIVE_CLASS: className for opener element when menu is opened
}
*/
BX.COpener = function(arParams)
{
	this.PARAMS = arParams || {};

	this.MENU = arParams.MENU || [];

	this.DIV = arParams.DIV;
	this.ATTACH = arParams.ATTACH || arParams.DIV;
	this.ATTACH_MODE = arParams.ATTACH_MODE || 'bottom';

	this.ACTIVE_CLASS = arParams.ACTIVE_CLASS || '';
	this.LEVEL = arParams.LEVEL || 0;

	this.CLOSE_ON_CLICK = typeof arParams.CLOSE_ON_CLICK != 'undefined' ? !!arParams.CLOSE_ON_CLICK : true;
	this.ADJUST_ON_CLICK = typeof arParams.ADJUST_ON_CLICK != 'undefined' ? !!arParams.ADJUST_ON_CLICK : true;

	this.TYPE = this.PARAMS.TYPE == 'hover' ? 'hover' : 'click';

	this._openTimeout = null;

	if (this.PARAMS.TYPE == 'hover' && arParams.TIMEOUT !== 0)
		this.TIMEOUT = arParams.TIMEOUT || 1000;
	else
		this.TIMEOUT = 0;

	if (!!this.PARAMS.MENU_URL)
	{
		this.bMenuLoaded = false;
		this.bMenuLoading = false;

		this.MENU = [{
			TEXT: BX.message('JS_CORE_LOADING'),
			CLOSE_ON_CLICK: false
		}];

		if (this.PARAMS.MENU_PRELOAD)
		{
			BX.defer(this.Load, this)();
		}
	}

	BX.ready(BX.defer(this.Init, this));
};

BX.COpener.prototype.Init = function()
{
	this.DIV = BX(this.DIV);

	switch (this.TYPE)
	{
		case 'hover':
			BX.bind(this.DIV, 'mouseover', BX.proxy(this.Open, this));
			BX.bind(this.DIV, 'click', BX.proxy(this.Toggle, this));
		break;

		case 'click':
			BX.bind(this.DIV, 'click', BX.proxy(this.Toggle, this));
		break;
	}

	//BX.bind(window, 'scroll', BX.delegate(this.__close_immediately, this));

	this.bMenuInit = false;
};

BX.COpener.prototype.Load = function()
{
	if (this.PARAMS.MENU_URL && !this.bMenuLoaded)
	{
		if (!this.bMenuLoading)
		{
			var url = this.PARAMS.MENU_URL;
			if (url.indexOf('sessid=') <= 0)
				url += (url.indexOf('?') > 0 ? '&' : '?') + 'sessid=' + BX.bitrix_sessid();

			this.bMenuLoading = true;
			BX.ajax.loadJSON(url, BX.proxy(this.SetMenu, this), BX.proxy(this.LoadFailed, this));
		}
	}
};

BX.COpener.prototype.SetMenu = function(menu)
{
	this.bMenuLoaded = true;
	this.bMenuLoading = false;
	if (this.bMenuInit)
	{
		this.MENU.setItems(menu);
	}
	else
	{
		this.MENU = menu;
	}
};

BX.COpener.prototype.LoadFailed = function(type, error)
{
	this.bMenuLoading = false;
	this.SetMenu([{
		TEXT: BX.message('JS_CORE_NO_DATA'),
		CLOSE_ON_CLICK: true
	}]);
	BX.debug(arguments);
};

BX.COpener.prototype.checkAdminMenu = function()
{
	if (document.documentElement.id == 'bx-admin-prefix')
		return true;

	return !!BX.findParent(this.DIV, {property: {id: 'bx-admin-prefix'}});
};

BX.COpener.prototype.Toggle = function(e)
{
	this.__clear_timeout();

	if (!this.bMenuInit || !this.MENU.visible())
	{
		var t = this.TIMEOUT;
		this.TIMEOUT = 0;
		this.Open(e);
		this.TIMEOUT = t;
	}
	else
	{
		this.MENU.Close();
	}

	return !!(e||window.event) && BX.PreventDefault(e);
};

BX.COpener.prototype.GetMenu = function()
{
	if (!this.bMenuInit)
	{
		if (BX.type.isArray(this.MENU))
		{
			this.MENU = new BX.CMenu({
				ITEMS: this.MENU,
				ATTACH_MODE: this.ATTACH_MODE,
				SET_ID: this.checkAdminMenu() ? 'bx-admin-prefix' : '',
				CLOSE_ON_CLICK: !!this.CLOSE_ON_CLICK,
				ADJUST_ON_CLICK: !!this.ADJUST_ON_CLICK,
				LEVEL: this.LEVEL,
				parent: BX(this.DIV),
				parent_attach: BX(this.ATTACH)
			});

			if (this.LEVEL > 0)
			{
				BX.bind(this.MENU.DIV, 'mouseover', BX.proxy(this._on_menu_hover, this));
				BX.bind(this.MENU.DIV, 'mouseout', BX.proxy(this._on_menu_hout, this));
			}
		}

		BX.addCustomEvent(this.MENU, 'onMenuOpen', BX.proxy(this.handler_onopen, this));
		BX.addCustomEvent(this.MENU, 'onMenuClose', BX.proxy(this.handler_onclose, this));

		BX.addCustomEvent('onMenuItemHover', BX.proxy(this.handler_onover, this));

		this.bMenuInit = true;
	}

	return this.MENU;
};

BX.COpener.prototype.Open = function()
{
	this.GetMenu();

	this.bOpen = true;

	this.__clear_timeout();

	if (this.TIMEOUT > 0)
	{
		BX.bind(this.DIV, 'mouseout', BX.proxy(this.__clear_timeout, this));
		this._openTimeout = setTimeout(BX.proxy(this.__open, this), this.TIMEOUT);
	}
	else
	{
		this.__open();
	}

	if (!!this.PARAMS.MENU_URL && !this.bMenuLoaded)
	{
		this._loadTimeout = setTimeout(BX.proxy(this.Load, this), parseInt(this.TIMEOUT/2));
	}

	return true;
};

BX.COpener.prototype.__clear_timeout = function()
{
	if (!!this._openTimeout)
		clearTimeout(this._openTimeout);
	if (!!this._loadTimeout)
		clearTimeout(this._loadTimeout);

	BX.unbind(this.DIV, 'mouseout', BX.proxy(this.__clear_timeout, this));
};

BX.COpener.prototype._on_menu_hover = function()
{
	this.bMenuHover = true;

	this.__clear_timeout();

	if (this.ACTIVE_CLASS)
		BX.addClass(this.DIV, this.ACTIVE_CLASS);

};

BX.COpener.prototype._on_menu_hout = function()
{
	this.bMenuHover = false;
};

BX.COpener.prototype.handler_onover = function(level, opener)
{
	if (this.bMenuHover)
		return;

	if (opener != this && level == this.LEVEL-1 && this.ACTIVE_CLASS)
	{
		BX.removeClass(this.DIV, this.ACTIVE_CLASS);
	}

	if (this.bMenuInit && level <= this.LEVEL-1 && this.MENU.visible())
	{
		if (opener != this)
		{
			this.__clear_timeout();
			this._openTimeout = setTimeout(BX.proxy(this.Close, this), this.TIMEOUT);
		}
	}
};

BX.COpener.prototype.handler_onopen = function()
{
	this.bOpen = true;

	if (this.ACTIVE_CLASS)
		BX.addClass(this.DIV, this.ACTIVE_CLASS);

	BX.defer(function() {
		BX.onCustomEvent(this, 'onOpenerMenuOpen');
	}, this)();
};

BX.COpener.prototype.handler_onclose = function()
{
	this.bOpen = false;
	BX.onCustomEvent(this, 'onOpenerMenuClose');

	if (this.ACTIVE_CLASS)
		BX.removeClass(this.DIV, this.ACTIVE_CLASS);
};

BX.COpener.prototype.Close = function()
{
	if (!this.bMenuInit)
		return;

	if (!!this._openTimeout)
		clearTimeout(this._openTimeout);

	this.bOpen = false;

	this.__close();
};

BX.COpener.prototype.__open = function()
{
	this.__clear_timeout();

	if (this.bMenuInit && this.bOpen && !this.MENU.visible())
		this.MENU.Show();
};

BX.COpener.prototype.__close = function()
{
	if (this.bMenuInit && !this.bOpen && this.MENU.visible())
		this.MENU.Hide();
};

BX.COpener.prototype.__close_immediately = function() {
	this.bOpen = false; this.__close();
};

BX.COpener.prototype.isMenuVisible = function() {
	return null != this.MENU.visible && this.MENU.visible()
};

/* common menu class */

BX.CMenu = function(arParams)
{
	BX.CMenu.superclass.constructor.apply(this);

	this.DIV.style.width = 'auto';//this.DIV.firstChild.offsetWidth + 'px';
	this.DIV.style.height = 'auto';//this.DIV.firstChild.offsetHeight + 'px';

	this.PARAMS = arParams || {};
	this.PARTS = {};

	this.PARAMS.ATTACH_MODE = this.PARAMS.ATTACH_MODE || 'bottom';
	this.PARAMS.CLOSE_ON_CLICK = typeof this.PARAMS.CLOSE_ON_CLICK == 'undefined' ? true : this.PARAMS.CLOSE_ON_CLICK;
	this.PARAMS.ADJUST_ON_CLICK = typeof this.PARAMS.ADJUST_ON_CLICK == 'undefined' ? true : this.PARAMS.ADJUST_ON_CLICK;
	this.PARAMS.LEVEL = this.PARAMS.LEVEL || 0;

	this.DIV.className = 'bx-core-popup-menu bx-core-popup-menu-' + this.PARAMS.ATTACH_MODE + ' bx-core-popup-menu-level' + this.PARAMS.LEVEL + (typeof this.PARAMS.ADDITIONAL_CLASS != 'undefined' ? ' ' + this.PARAMS.ADDITIONAL_CLASS : '');
	if (!!this.PARAMS.SET_ID)
		this.DIV.id = this.PARAMS.SET_ID;

	if (this.PARAMS.LEVEL == 0)
	{
		this.ARROW = this.DIV.appendChild(BX.create('SPAN', {props: {className: 'bx-core-popup-menu-angle'}, style: {left:'15px'}}));
	}

	if (!!this.PARAMS.CLASS_NAME)
		this.DIV.className += ' ' + this.PARAMS.CLASS_NAME;

	BX.bind(this.DIV, 'click', BX.eventCancelBubble);

	this.ITEMS = [];

	this.setItems(this.PARAMS.ITEMS);

	BX.addCustomEvent('onMenuOpen', BX.proxy(this._onMenuOpen, this));
	BX.addCustomEvent('onMenuItemSelected', BX.proxy(this.Hide, this));
};
BX.extend(BX.CMenu, BX.CWindowFloat);

BX.CMenu.broadcastCloseEvent = function()
{
	BX.onCustomEvent("onMenuItemSelected");
};

BX.CMenu._toggleChecked = function()
{
	BX.toggleClass(this, 'bx-core-popup-menu-item-checked');
};

BX.CMenu._itemDblClick = function()
{
	window.location.href = this.href;
};

BX.CMenu.prototype.toggleArrow = function(v)
{
	if (!!this.ARROW)
	{
		if (typeof v == 'undefined')
		{
			v = this.ARROW.style.visibility == 'hidden';
		}

		this.ARROW.style.visibility = !!v ? 'visible' : 'hidden';
	}
};

BX.CMenu.prototype.visible = function()
{
	return this.DIV.style.display !== 'none';
};

BX.CMenu.prototype._onMenuOpen = function(menu, menu_level)
{
	if (this.visible())
	{
		if (menu_level == this.PARAMS.LEVEL && menu != this)
		{
			this.Hide();
		}
	}
};

BX.CMenu.prototype.onUnRegister = function()
{
	if (!this.visible())
		return;

	this.Hide();
};

BX.CMenu.prototype.setItems = function(items)
{
	this.PARAMS.ITEMS = items;

	BX.cleanNode(this.DIV);

	if (!!this.ARROW)
		this.DIV.appendChild(this.ARROW);

	if (this.PARAMS.ITEMS)
	{
		this.PARAMS.ITEMS = BX.util.array_values(this.PARAMS.ITEMS);

		var bIcons = false;
		var cnt = 0;
		for (var i = 0, len = this.PARAMS.ITEMS.length; i < len; i++)
		{
			if ((i == 0 || i == len-1) && this.PARAMS.ITEMS[i].SEPARATOR)
				continue;

			cnt++;

			if (!bIcons)
				bIcons = !!this.PARAMS.ITEMS[i].GLOBAL_ICON;

			this.addItem(this.PARAMS.ITEMS[i], i);
		}

		// Occam turning in his grave
		if (cnt === 1)
			BX.addClass(this.DIV, 'bx-core-popup-menu-single-item');
		else
			BX.removeClass(this.DIV, 'bx-core-popup-menu-single-item');

		if (!bIcons)
			BX.addClass(this.DIV, 'bx-core-popup-menu-no-icons');
		else
			BX.removeClass(this.DIV, 'bx-core-popup-menu-no-icons');

	}
};

BX.CMenu.prototype.addItem = function(item)
{
	this.ITEMS.push(item);

	if (item.SEPARATOR)
	{
		item.NODE = BX.create(
			'DIV', {props: {className: 'bx-core-popup-menu-separator'}}
		);
	}
	else
	{
		var bHasMenu = (!!item.MENU
			&& (
				(BX.type.isArray(item.MENU) && item.MENU.length > 0)
				|| item.MENU instanceof BX.CMenu
			) || !!item.MENU_URL
		);

		if (item.DISABLED)
		{
			item.CLOSE_ON_CLICK = false;
			item.LINK = null;
			item.ONCLICK = null;
			item.ACTION = null;
		}

		item.NODE = BX.create(!!item.LINK || BX.browser.IsIE() && !BX.browser.IsDoctype() ? 'A' : 'SPAN', {
			props: {
				className: 'bx-core-popup-menu-item'
					+ (bHasMenu ? ' bx-core-popup-menu-item-opener' : '')
					+ (!!item.DEFAULT ? ' bx-core-popup-menu-item-default' : '')
					+ (!!item.DISABLED ? ' bx-core-popup-menu-item-disabled' : '')
					+ (!!item.CHECKED ? ' bx-core-popup-menu-item-checked' : ''),
					title: !!BX.message['MENU_ENABLE_TOOLTIP'] || !!item.SHOW_TITLE ? item.TITLE || '' : '',
				BXMENULEVEL: this.PARAMS.LEVEL
			},
			attrs: !!item.LINK || BX.browser.IsIE() && !BX.browser.IsDoctype() ? {href: item.LINK || 'javascript:void(0)'} : {},
			events: {
				mouseover: function()
				{
					BX.onCustomEvent('onMenuItemHover', [this.BXMENULEVEL, this.OPENER])
				}
			},
			html: '<span class="bx-core-popup-menu-item-icon' + (item.GLOBAL_ICON ? ' '+item.GLOBAL_ICON : '') + '"></span><span class="bx-core-popup-menu-item-text">'+item.TEXT+'</span>'
		});

		if (bHasMenu && !item.DISABLED)
		{
			item.NODE.OPENER = new BX.COpener({
				DIV: item.NODE,
				ACTIVE_CLASS: 'bx-core-popup-menu-item-opened',
				TYPE: 'hover',
				MENU: item.MENU,
				MENU_URL: item.MENU_URL,
				MENU_PRELOAD: !!item.MENU_PRELOAD,
				LEVEL: this.PARAMS.LEVEL + 1,
				ATTACH_MODE:'right',
				TIMEOUT: 500
			});
		}
		else if (this.PARAMS.CLOSE_ON_CLICK && (typeof item.CLOSE_ON_CLICK == 'undefined' || !!item.CLOSE_ON_CLICK))
		{
			BX.bind(item.NODE, 'click', BX.CMenu.broadcastCloseEvent);
		}
		else if (this.PARAMS.ADJUST_ON_CLICK && (typeof item.ADJUST_ON_CLICK == 'undefined' || !!item.ADJUST_ON_CLICK))
		{
			BX.bind(item.NODE, 'click', BX.defer(this.adjustPos, this));
		}

		if (bHasMenu && !!item.LINK)
		{
			BX.bind(item.NODE, 'dblclick', BX.CMenu._itemDblClick);
		}

		if (typeof item.CHECKED != 'undefined')
		{
			BX.bind(item.NODE, 'click', BX.CMenu._toggleChecked);
		}

		item.ONCLICK = item.ACTION || item.ONCLICK;
		if (!!item.ONCLICK)
		{
			if (BX.type.isString(item.ONCLICK))
			{
				item.ONCLICK = new Function("event", item.ONCLICK);
			}

			BX.bind(item.NODE, 'click', item.ONCLICK);
		}
	}

	this.DIV.appendChild(item.NODE);
};

BX.CMenu.prototype._documentClickBind = function()
{
	this._documentClickUnBind();
	BX.bind(document, 'click', BX.proxy(this._documentClick, this));
};

BX.CMenu.prototype._documentClickUnBind = function()
{
	BX.unbind(document, 'click', BX.proxy(this._documentClick, this));
};

BX.CMenu.prototype._documentClick = function(e)
{
	e = e||window.event;
	if(!!e && !(BX.getEventButton(e) & BX.MSLEFT))
		return;

	this.Close();
};

BX.CMenu.prototype.Show = function()
{
	BX.onCustomEvent(this, 'onMenuOpen', [this, this.PARAMS.LEVEL]);
	BX.CMenu.superclass.Show.apply(this, []);

	this.bCloseEventFired = false;

	BX.addCustomEvent(this.PARAMS.parent_attach, 'onChangeNodePosition', BX.proxy(this.adjustToNode, this));

	(BX.defer(this._documentClickBind, this))();
};

BX.CMenu.prototype.Close = // we shouldn't 'Close' window - only hide
BX.CMenu.prototype.Hide = function()
{
	if (!this.visible())
		return;

	BX.removeCustomEvent(this.PARAMS.parent_attach, 'onChangeNodePosition', BX.proxy(this.adjustToNode, this));

	this._documentClickUnBind();

	if (!this.bCloseEventFired)
	{
		BX.onCustomEvent(this, 'onMenuClose', [this, this.PARAMS.LEVEL]);
		this.bCloseEventFired = true;
	}
	BX.CMenu.superclass.Hide.apply(this, arguments);


//	this.DIV.onclick = null;
	//this.PARAMS.parent.onclick = null;
};

BX.CMenu.prototype.__adjustMenuToNode = function()
{
	var pos = BX.pos(this.PARAMS.parent_attach),
		bFixed = !!BX.findParent(this.PARAMS.parent_attach, BX.is_fixed);

	if (bFixed)
		this.DIV.style.position = 'fixed';
	else
		this.DIV.style.position = 'absolute';

	if (!pos.top)
	{
		this.DIV.style.top = '-1000px';
		this.DIV.style.left = '-1000px';
	}

	if (this.bTimeoutSet) return;

	var floatWidth = this.DIV.offsetWidth, floatHeight = this.DIV.offsetHeight;
	if (!floatWidth)
	{
		setTimeout(BX.delegate(function(){
			this.bTimeoutSet = false; this.__adjustMenuToNode();
		}, this), 100);

		this.bTimeoutSet = true;
		return;
	}

	var menu_pos = {},
		wndSize = BX.GetWindowSize();

/*
	if (BX.browser.IsIE() && !BX.browser.IsDoctype())
	{
		pos.top -= 4; pos.bottom -= 4;
		pos.left -= 2; pos.right -= 2;
	}
*/

	switch (this.PARAMS.ATTACH_MODE)
	{
		case 'bottom':
			menu_pos.top = pos.bottom + 9;
			menu_pos.left = pos.left;

			var arrowPos = 0;
			if (!!this.ARROW)
			{
				if (pos.width > floatWidth)
					arrowPos = parseInt(floatWidth/2 - 7);
				else
					arrowPos = parseInt(Math.min(floatWidth, pos.width)/2 - 7);

				if (arrowPos < 7)
				{
					menu_pos.left -= 15;
					arrowPos += 15;
				}
			}

			if (menu_pos.left > wndSize.scrollWidth - floatWidth - 10)
			{
				var orig_menu_pos = menu_pos.left;
				menu_pos.left = wndSize.scrollWidth - floatWidth - 10;

				if (!!this.ARROW)
					arrowPos += orig_menu_pos - menu_pos.left;
			}

			if (bFixed)
			{
				menu_pos.left -= wndSize.scrollLeft;
			}

			if (!!this.ARROW)
				this.ARROW.style.left = arrowPos + 'px';
		break;
		case 'right':
			menu_pos.top = pos.top-1;
			menu_pos.left = pos.right;

			if (menu_pos.left > wndSize.scrollWidth - floatWidth - 10)
			{
				menu_pos.left = pos.left - floatWidth - 1;
			}
		break;
	}

	if (bFixed)
	{
		menu_pos.top -= wndSize.scrollTop;
	}

	if (!!this.ARROW)
		this.ARROW.className = 'bx-core-popup-menu-angle';

	if((menu_pos.top + floatHeight > wndSize.scrollTop + wndSize.innerHeight)
		|| (menu_pos.top + floatHeight > wndSize.scrollHeight))
	{
		var new_top = this.PARAMS.ATTACH_MODE == 'bottom'
			? pos.top - floatHeight - 9
			: pos.bottom - floatHeight + 1;

		if((new_top > wndSize.scrollTop)
			|| (menu_pos.top + floatHeight > wndSize.scrollHeight))
		{
			if ((menu_pos.top + floatHeight > wndSize.scrollHeight))
			{
				menu_pos.top = Math.max(0, wndSize.scrollHeight-floatHeight);
				this.toggleArrow(false);
			}
			else
			{
				menu_pos.top = new_top;

				if (!!this.ARROW)
					this.ARROW.className = 'bx-core-popup-menu-angle-bottom';
			}
		}
	}

	if (menu_pos.top + menu_pos.left == 0)
	{
		this.Hide();
	}
	else
	{
		this.DIV.style.top = menu_pos.top + 'px';
		this.DIV.style.left = menu_pos.left + 'px';
	}
};

BX.CMenu.prototype.adjustToNode = function(el)
{
	this.PARAMS.parent_attach = BX(el) || this.PARAMS.parent_attach || this.PARAMS.parent;
	this.__adjustMenuToNode();
};


/* components toolbar class */

BX.CMenuOpener = function(arParams)
{
	BX.CMenuOpener.superclass.constructor.apply(this);

	this.PARAMS = arParams || {};
	this.setParent(this.PARAMS.parent);
	this.PARTS = {};

	this.SETTINGS.drag_restrict = true;

	this.defaultAction = null;

	this.timeout = 500;

	this.DIV.className = 'bx-component-opener';
	this.DIV.ondblclick = BX.PreventDefault;

	if (this.PARAMS.component_id)
	{
		this.PARAMS.transform = !!this.PARAMS.transform;
	}

	this.OPENERS = [];

	this.DIV.appendChild(BX.create('SPAN', {
		props: {className: 'bx-context-toolbar' + (this.PARAMS.transform ? ' bx-context-toolbar-vertical-mode' : '')}
	}));

	//set internal structure and register draggable element
	this.PARTS.INNER = this.DIV.firstChild.appendChild(BX.create('SPAN', {
		props: {className: 'bx-context-toolbar-inner'},
		html: '<span class="bx-context-toolbar-drag-icon"></span><span class="bx-context-toolbar-vertical-line"></span><br>'
	}));

	this.EXTRA_BUTTONS = {};

	var btnCount = 0;
	for (var i = 0, len = this.PARAMS.menu.length; i < len; i++)
	{
		var item = this.addItem(this.PARAMS.menu[i]);
		if (null != item)
		{
			btnCount++;
			this.PARTS.INNER.appendChild(item);
			this.PARTS.INNER.appendChild(BX.create('BR'));
		}
	}
	var bHasButtons = btnCount > 0;

	//menu items will be attached here

	this.PARTS.ICONS = this.PARTS.INNER.appendChild(BX.create('SPAN', {
		props: {className: 'bx-context-toolbar-icons'}
	}));

	if (this.PARAMS.component_id)
	{
		this.PARAMS.pin = !!this.PARAMS.pin;

		if (bHasButtons)
			this.PARTS.ICONS.appendChild(BX.create('SPAN', {props: {className: 'bx-context-toolbar-separator'}}));

		this.PARTS.ICON_PIN = this.PARTS.ICONS.appendChild(BX.create('A', {
			attrs: {
				href: 'javascript:void(0)'
			},
			props: {
				className: this.PARAMS.pin
							? 'bx-context-toolbar-pin-fixed'
							: 'bx-context-toolbar-pin'
			},
			events: {
				click: BX.delegate(this.__pin_btn_clicked, this)
			}
		}));
	}


	if (this.EXTRA_BUTTONS['components2_props'])
	{
		var btn = this.EXTRA_BUTTONS['components2_props'] || {URL: 'javascript:void(0)'};
		if (null == this.defaultAction)
		{
			this.defaultAction = btn.ONCLICK;
			this.defaultActionTitle = btn.TITLE || btn.TEXT;
		}

		btn.URL = 'javascript:' + BX.util.urlencode(btn.ONCLICK);

		this.ATTACH = this.PARTS.ICONS.appendChild(BX.create('SPAN', {
			props: {className: 'bx-context-toolbar-button bx-context-toolbar-button-settings' },
			children:
			[
				BX.create('SPAN',
				{
					props:{className: 'bx-context-toolbar-button-inner'},
					children:
					[
						BX.create('A', {
							attrs: {href: btn.URL},
							events: {
								mouseover: BX.proxy(this.__msover_text, this),
								mouseout: BX.proxy(this.__msout_text, this),
								mousedown: BX.proxy(this.__msdown_text, this)
							},
							html: '<span class="bx-context-toolbar-button-icon bx-context-toolbar-settings-icon"></span>'
						}),
						BX.create('A', {
							attrs: {href: 'javascript: void(0)'},
							props: {className: 'bx-context-toolbar-button-arrow'},
							events: {
								mouseover: BX.proxy(this.__msover_arrow, this),
								mouseout: BX.proxy(this.__msout_arrow, this),
								mousedown: BX.proxy(this.__msdown_arrow, this)
							},
							html: '<span class="bx-context-toolbar-button-arrow"></span>'
						})
					]
				})
			]
		}));

		this.OPENER = this.ATTACH.firstChild.lastChild;

		var opener = this.attachMenu(this.EXTRA_BUTTONS['components2_submenu']['MENU']);

		BX.addCustomEvent(opener, 'onOpenerMenuOpen', BX.proxy(this.__menu_open, this));
		BX.addCustomEvent(opener, 'onOpenerMenuClose', BX.proxy(this.__menu_close, this));
	}

	if (btnCount > 1)
	{
		this.PARTS.ICONS.appendChild(BX.create('span', { props: {className: 'bx-context-toolbar-separator bx-context-toolbar-separator-switcher'}}));

		this.ICON_TRANSFORM = this.PARTS.ICONS.appendChild(BX.create('A', {
			attrs: {href: 'javascript: void(0)'},
			props: {className: 'bx-context-toolbar-switcher'},
			events: {
				click: BX.delegate(this.__trf_btn_clicked, this)
			}
		}));
	}

	if (this.PARAMS.HINT)
	{
		this.DIV.BXHINT = this.HINT = new BX.CHint({
			parent: this.DIV,
			hint:this.PARAMS.HINT.TEXT || '',
			title: this.PARAMS.HINT.TITLE || '',
			hide_timeout: this.timeout/2,
			preventHide: false
		});
	}

	BX.addCustomEvent(this, 'onWindowDragFinished', BX.delegate(this.__onMoveFinished, this));
	BX.addCustomEvent('onDynamicModeChange', BX.delegate(this.__onDynamicModeChange, this));
	BX.addCustomEvent('onTopPanelCollapse', BX.delegate(this.__onPanelCollapse, this));

	BX.addCustomEvent('onMenuOpenerMoved', BX.delegate(this.checkPosition, this));
	BX.addCustomEvent('onMenuOpenerUnhide', BX.delegate(this.checkPosition, this));

	if (this.OPENERS)
	{
		for (i=0,len=this.OPENERS.length; i<len; i++)
		{
			BX.addCustomEvent(this.OPENERS[i], 'onOpenerMenuOpen', BX.proxy(this.__hide_hint, this));
		}
	}
};
BX.extend(BX.CMenuOpener, BX.CWindowFloat);

BX.CMenuOpener.prototype.setParent = function(new_parent)
{
	new_parent = BX(new_parent);
	if(new_parent.OPENER && new_parent.OPENER != this)
	{
		new_parent.OPENER.Close();
		new_parent.OPENER.clearHoverHoutEvents();
	}

	if(this.PARAMS.parent && this.PARAMS.parent != new_parent)
	{
		this.clearHoverHoutEvents();
		this.PARAMS.parent.OPENER = null;
	}

	this.PARAMS.parent = new_parent;
	this.PARAMS.parent.OPENER = this;
};

BX.CMenuOpener.prototype.setHoverHoutEvents = function(hover, hout)
{
	if(!this.__opener_events_set)
	{
		BX.bind(this.Get(), 'mouseover', hover);
		BX.bind(this.Get(), 'mouseout', hout);
		this.__opener_events_set = true;
	}
};

BX.CMenuOpener.prototype.clearHoverHoutEvents = function()
{
	if(this.Get())
	{
		BX.unbindAll(this.Get());
		this.__opener_events_set = false;
	}
};


BX.CMenuOpener.prototype.unclosable = true;

BX.CMenuOpener.prototype.__check_intersection = function(pos_self, pos_other)
{
	return !(pos_other.right <= pos_self.left || pos_other.left >= pos_self.right
			|| pos_other.bottom <= pos_self.top || pos_other.top >= pos_self.bottom);
};


BX.CMenuOpener.prototype.__msover_text = function() {
	this.bx_hover = true;
	if (!this._menu_open)
		BX.addClass(this.ATTACH, 'bx-context-toolbar-button-text-hover');
};

BX.CMenuOpener.prototype.__msout_text = function() {
	this.bx_hover = false;
	if (!this._menu_open)
		BX.removeClass(this.ATTACH, 'bx-context-toolbar-button-text-hover bx-context-toolbar-button-text-active');
};

BX.CMenuOpener.prototype.__msover_arrow = function() {
	this.bx_hover = true;
	if (!this._menu_open)
		BX.addClass(this.ATTACH, 'bx-context-toolbar-button-arrow-hover');
};

BX.CMenuOpener.prototype.__msout_arrow = function() {
	this.bx_hover = false;
	if (!this._menu_open)
		BX.removeClass(this.ATTACH, 'bx-context-toolbar-button-arrow-hover bx-context-toolbar-button-arrow-active');
};

BX.CMenuOpener.prototype.__msdown_text = function() {
	this.bx_active = true;
	if (!this._menu_open)
		BX.addClass(this.ATTACH, 'bx-context-toolbar-button-text-active');
};

BX.CMenuOpener.prototype.__msdown_arrow = function() {
	this.bx_active = true;
	if (!this._menu_open)
		BX.addClass(this.ATTACH, 'bx-context-toolbar-button-arrow-active');
};

BX.CMenuOpener.prototype.__menu_close = function() {
	this._menu_open = false;
	this.bx_active = false;
	BX.removeClass(this.ATTACH, 'bx-context-toolbar-button-active bx-context-toolbar-button-text-active bx-context-toolbar-button-arrow-active');
	if (!this.bx_hover)
	{
		BX.removeClass(this.ATTACH, 'bx-context-toolbar-button-hover bx-context-toolbar-button-text-hover bx-context-toolbar-button-arrow-hover');
		this.bx_hover = false;
	}
};

BX.CMenuOpener.prototype.__menu_open = function() {
	this._menu_open = true;
};

BX.CMenuOpener.prototype.checkPosition = function()
{
	if (this.isMenuVisible() || this.DIV.style.display == 'none'
		|| this == BX.proxy_context || BX.proxy_context.zIndex > this.zIndex)
		return;

	this.correctPosition(BX.proxy_context);
};

BX.CMenuOpener.prototype.correctPosition = function(opener)
{
	var pos_self = BX.pos(this.DIV), pos_other = BX.pos(opener.Get());
	if (this.__check_intersection(pos_self, pos_other))
	{
		var new_top = pos_other.top - pos_self.height;
		if (new_top < 0)
			new_top = pos_other.bottom;

		this.DIV.style.top = new_top + 'px';

		BX.addCustomEvent(opener, 'onMenuOpenerHide', BX.proxy(this.restorePosition, this));
		BX.onCustomEvent(this, 'onMenuOpenerMoved');
	}
};

BX.CMenuOpener.prototype.restorePosition = function()
{
	if (!this.MOUSEOVER && !this.isMenuVisible())
	{
		if (this.originalPos)
			this.DIV.style.top = this.originalPos.top + 'px';

		BX.removeCustomEvent(BX.proxy_context, 'onMenuOpenerHide', BX.proxy(this.restorePosition, this));
		if (this.restore_pos_timeout) clearTimeout(this.restore_pos_timeout);
	}
	else
	{
		this.restore_pos_timeout = setTimeout(BX.proxy(this.restorePosition, this), this.timeout);
	}
};


BX.CMenuOpener.prototype.Show = function()
{
	BX.CMenuOpener.superclass.Show.apply(this, arguments);

	this.SetDraggable(this.PARTS.INNER.firstChild);

	this.DIV.style.width = 'auto';
	this.DIV.style.height = 'auto';

	if (!this.PARAMS.pin)
	{
		this.DIV.style.left = '-1000px';
		this.DIV.style.top = '-1000px';

		this.Hide();
	}
	else
	{
		this.bPosAdjusted = true;
		this.bMoved = true;

		if (this.PARAMS.top) this.DIV.style.top = this.PARAMS.top + 'px';
		if (this.PARAMS.left) this.DIV.style.left = this.PARAMS.left + 'px';

		this.DIV.style.display = (!BX.admin.dynamic_mode || BX.admin.dynamic_mode_show_borders) ? 'block' : 'none';

		if (this.DIV.style.display == 'block')
		{
			setTimeout(BX.delegate(function() {BX.onCustomEvent(this, 'onMenuOpenerUnhide')}, this), 50);
		}
	}
};

BX.CMenuOpener.prototype.executeDefaultAction = function()
{
	if (this.defaultAction)
	{
		if (BX.type.isFunction(this.defaultAction))
			this.defaultAction();
		else if(BX.type.isString(this.defaultAction))
			BX.evalGlobal(this.defaultAction);
	}
};

BX.CMenuOpener.prototype.__onDynamicModeChange = function(val)
{
	this.DIV.style.display = val ? 'block' : 'none';
};

BX.CMenuOpener.prototype.__onPanelCollapse = function(bCollapsed, dy)
{
	this.DIV.style.top = (parseInt(this.DIV.style.top) + dy) + 'px';
	if (this.PARAMS.pin)
	{
		this.__savePosition();
	}
};

BX.CMenuOpener.prototype.__onMoveFinished = function()
{
	BX.onCustomEvent(this, 'onMenuOpenerMoved');

	this.bMoved = true;

	if (this.PARAMS.pin)
		this.__savePosition();
};

BX.CMenuOpener.prototype.__savePosition = function()
{
	var arOpts = {};

	arOpts.pin = this.PARAMS.pin;
	if (!this.PARAMS.pin)
	{
		arOpts.top = false; arOpts.left = false; arOpts.transform = false;
	}
	else
	{
		arOpts.transform = this.PARAMS.transform;
		if (this.bMoved)
		{
			arOpts.left = parseInt(this.DIV.style.left);
			arOpts.top = parseInt(this.DIV.style.top);
		}
	}

	BX.WindowManager.saveWindowOptions(this.PARAMS.component_id, arOpts);
};

BX.CMenuOpener.prototype.__pin_btn_clicked = function() {this.Pin()};
BX.CMenuOpener.prototype.Pin = function(val)
{
	if (null == val)
		this.PARAMS.pin = !this.PARAMS.pin;
	else
		this.PARAMS.pin = !!val;

	this.PARTS.ICON_PIN.className = (this.PARAMS.pin ? 'bx-context-toolbar-pin-fixed' : 'bx-context-toolbar-pin');

	this.__savePosition();
};

BX.CMenuOpener.prototype.__trf_btn_clicked = function() {this.Transform()};
BX.CMenuOpener.prototype.Transform = function(val)
{
	var pos = {};

	if (null == val)
		this.PARAMS.transform = !this.PARAMS.transform;
	else
		this.PARAMS.transform = !!val;

	if (this.bMoved)
	{
		pos = BX.pos(this.DIV);
	}

	if (this.PARAMS.transform)
		BX.addClass(this.DIV.firstChild, 'bx-context-toolbar-vertical-mode');
	else
		BX.removeClass(this.DIV.firstChild, 'bx-context-toolbar-vertical-mode');

	if (!this.bMoved)
	{
		this.adjustPos();
	}
	else
	{
		this.DIV.style.left = (pos.right - this.DIV.offsetWidth - (BX.browser.IsIE() && !BX.browser.IsDoctype() ? 2 : 0)) + 'px';
	}

	this.__savePosition();
};

BX.CMenuOpener.prototype.adjustToNodeGetPos = function()
{
	var pos = BX.pos(this.PARAMS.parent/*, true*/);

	var scrollSize = BX.GetWindowScrollSize();
	var floatWidth = this.DIV.offsetWidth;

	pos.left -= BX.admin.__border_dx;
	pos.top -= BX.admin.__border_dx;

	if (true || !this.PARAMS.transform)
	{
		pos.top -= 45;
	}

	if (pos.left > scrollSize.scrollWidth - floatWidth)
	{
		pos.left = scrollSize.scrollWidth - floatWidth;
	}

	return pos;
};

BX.CMenuOpener.prototype.addItem = function(item)
{
	if (item.TYPE)
	{
		this.EXTRA_BUTTONS[item.TYPE] = item;
		return null;
	}
	else
	{
		var q = new BX.CMenuOpenerItem(item);
		if (null == this.defaultAction)
		{
			if (q.item.ONCLICK)
			{
				this.defaultAction = item.ONCLICK;
			}
			else if (q.item.MENU)
			{
				this.defaultAction = BX.delegate(function() {this.Open()}, q.item.OPENER);
			}

			this.defaultActionTitle = item.TITLE || item.TEXT;

			BX.addClass(q.Get(), 'bx-content-toolbar-default');
		}
		if (q.item.OPENER) this.OPENERS[this.OPENERS.length] = q.item.OPENER;
		return q.Get();
	}
};

BX.CMenuOpener.prototype.attachMenu = function(menu)
{
	var opener = new BX.COpener({
		'DIV':  this.OPENER,
		'ATTACH': this.ATTACH,
		'MENU': menu,
		'TYPE': 'click'
	});

	this.OPENERS[this.OPENERS.length] = opener;

	return opener;
};

BX.CMenuOpener.prototype.__hide_hint = function()
{
	if (this.HINT) this.HINT.__hide_immediately();
};

BX.CMenuOpener.prototype.isMenuVisible = function()
{
	for (var i=0,len=this.OPENERS.length; i<len; i++)
	{
		if (this.OPENERS[i].isMenuVisible())
			return true;
	}

	return false;
};

BX.CMenuOpener.prototype.Hide = function()
{
	if (!this.PARAMS.pin)
	{
		this.DIV.style.display = 'none';
		BX.onCustomEvent(this, 'onMenuOpenerHide');
	}
};
BX.CMenuOpener.prototype.UnHide = function()
{
	this.DIV.style.display = 'block';
	if (!this.bPosAdjusted && !this.PARAMS.pin)
	{
		this.adjustPos();
		this.bPosAdjusted = true;
	}

	if (null == this.originalPos && !this.bMoved)
	{
		this.originalPos = BX.pos(this.DIV);
	}

	BX.onCustomEvent(this, 'onMenuOpenerUnhide');
};

BX.CMenuOpenerItem = function(item)
{
	this.item = item;

	if (this.item.ACTION && !this.item.ONCLICK)
	{
		this.item.ONCLICK = this.item.ACTION;
	}

	this.DIV = BX.create('SPAN');
	this.DIV.appendChild(BX.create('SPAN', {props: {className: 'bx-context-toolbar-button-underlay'}}));

	this.WRAPPER = this.DIV.appendChild(BX.create('SPAN', {
		props: {className: 'bx-context-toolbar-button-wrapper'},
		children: [
			BX.create('SPAN', {
				props: {className: 'bx-context-toolbar-button', title: item.TITLE},
				children: [
					BX.create('SPAN', {
						props: {className: 'bx-context-toolbar-button-inner'}
					})
				]
			})
		]
	}));

	var btn_icon = BX.create('SPAN', {
		props: {className: 'bx-context-toolbar-button-icon' + (this.item.ICON || this.item.ICONCLASS ? ' ' + (this.item.ICON || this.item.ICONCLASS) : '')},
		attrs: (
				!(this.item.ICON || this.item.ICONCLASS)
				&&
				(this.item.SRC || this.item.IMAGE)
			)
			? {
				style: 'background: scroll transparent url(' + (this.item.SRC || this.item.IMAGE) + ') no-repeat center center !important;'
			}
			: {}
	}), btn_text = BX.create('SPAN', {
		props: {className: 'bx-context-toolbar-button-text'},
		text: this.item.TEXT
	});

	if (this.item.ACTION && !this.item.ONCLICK)
	{
		this.item.ONCLICK = this.item.ACTION;
	}

	this.bHasMenu = !!this.item.MENU;
	this.bHasAction = !!this.item.ONCLICK;

	if (this.bHasAction)
	{
		this.LINK = this.WRAPPER.firstChild.firstChild.appendChild(BX.create('A', {
			attrs: {
				'href': 'javascript: void(0)'
			},
			events: {
				mouseover: this.bHasMenu ? BX.proxy(this.__msover_text, this) : BX.proxy(this.__msover, this),
				mouseout: this.bHasMenu ? BX.proxy(this.__msout_text, this) : BX.proxy(this.__msout, this),
				mousedown: this.bHasMenu ? BX.proxy(this.__msdown_text, this) : BX.proxy(this.__msdown, this)
			},
			children: [btn_icon, btn_text]
		}));

		if (this.bHasMenu)
		{
			this.LINK_MENU = this.WRAPPER.firstChild.firstChild.appendChild(BX.create('A', {
				props: {className: 'bx-context-toolbar-button-arrow'},
				attrs: {
					'href': 'javascript: void(0)'
				},
				events: {
					mouseover: BX.proxy(this.__msover_arrow, this),
					mouseout: BX.proxy(this.__msout_arrow, this),
					mousedown: BX.proxy(this.__msdown_arrow, this)
				},
				children: [
					BX.create('SPAN', {props: {className: 'bx-context-toolbar-button-arrow'}})
				]
			}));
		}

	}
	else if (this.bHasMenu)
	{
		this.item.ONCLICK = null;

		this.LINK = this.LINK_MENU = this.WRAPPER.firstChild.firstChild.appendChild(BX.create('A', {
			attrs: {
				'href': 'javascript: void(0)'
			},
			events: {
				mouseover: BX.proxy(this.__msover, this),
				mouseout: BX.proxy(this.__msout, this),
				mousedown: BX.proxy(this.__msdown, this)
			},
			children: [
				btn_icon,
				btn_text
			]
		}));

		this.LINK.appendChild(BX.create('SPAN', {props: {className: 'bx-context-toolbar-single-button-arrow'}}));

	}

	if (this.bHasMenu)
	{
		this.item.SUBMENU = new BX.CMenu({
			ATTACH_MODE:'bottom',
			ITEMS:this.item['MENU'],
			//PARENT_MENU:this.parentMenu,
			parent: this.LINK_MENU,
			parent_attach: this.WRAPPER.firstChild
		});

		this.item.OPENER = new BX.COpener({
			DIV: this.LINK_MENU,
			TYPE: 'click',
			MENU: this.item.SUBMENU
		});

		BX.addCustomEvent(this.item.OPENER, 'onOpenerMenuOpen', BX.proxy(this.__menu_open, this));
		BX.addCustomEvent(this.item.OPENER, 'onOpenerMenuClose', BX.proxy(this.__menu_close, this));
	}

	if (this.bHasAction)
	{
		BX.bind(this.LINK, 'click', BX.delegate(this.__click, this));
	}
};

BX.CMenuOpenerItem.prototype.Get = function() {return this.DIV;};
BX.CMenuOpenerItem.prototype.__msover = function() {
	this.bx_hover = true;
	if (!this._menu_open)
		BX.addClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-hover');
};
BX.CMenuOpenerItem.prototype.__msout = function() {
	this.bx_hover = false;
	if (!this._menu_open)
		BX.removeClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-hover bx-context-toolbar-button-active');
};
BX.CMenuOpenerItem.prototype.__msover_text = function() {
	this.bx_hover = true;
	if (!this._menu_open)
		BX.addClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-text-hover');
};
BX.CMenuOpenerItem.prototype.__msout_text = function() {
	this.bx_hover = false;
	if (!this._menu_open)
		BX.removeClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-text-hover bx-context-toolbar-button-text-active');
};
BX.CMenuOpenerItem.prototype.__msover_arrow = function() {
	this.bx_hover = true;
	if (!this._menu_open)
		BX.addClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-arrow-hover');
};
BX.CMenuOpenerItem.prototype.__msout_arrow = function() {
	this.bx_hover = false;
	if (!this._menu_open)
		BX.removeClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-arrow-hover bx-context-toolbar-button-arrow-active');
};
BX.CMenuOpenerItem.prototype.__msdown = function() {
	this.bx_active = true;
	if (!this._menu_open)
		BX.addClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-active');
};
BX.CMenuOpenerItem.prototype.__msdown_text = function() {
	this.bx_active = true;
	if (!this._menu_open)
		BX.addClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-text-active');
};
BX.CMenuOpenerItem.prototype.__msdown_arrow = function() {
	this.bx_active = true;
	if (!this._menu_open)
		BX.addClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-arrow-active');
};
BX.CMenuOpenerItem.prototype.__menu_close = function() {

	this._menu_open = false;
	this.bx_active = false;
	BX.removeClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-active bx-context-toolbar-button-text-active bx-context-toolbar-button-arrow-active');
	if (!this.bx_hover)
	{
		BX.removeClass(this.LINK.parentNode.parentNode, 'bx-context-toolbar-button-hover bx-context-toolbar-button-text-hover bx-context-toolbar-button-arrow-hover');
		this.bx_hover = false;
	}
};
BX.CMenuOpenerItem.prototype.__menu_open = function() {
	this._menu_open = true;
};

BX.CMenuOpenerItem.prototype.__click = function() {BX.evalGlobal(this.item.ONCLICK)};

/* global page opener class */
BX.CPageOpener = function(arParams)
{
	//if (null == arParams.pin) arParams.pin = true;
	BX.CPageOpener.superclass.constructor.apply(this, arguments);

	this.timeout = 505;

	window.PAGE_EDIT_CONTROL = this;
};
BX.extend(BX.CPageOpener, BX.CMenuOpener);

BX.CPageOpener.prototype.checkPosition = function()
{
	if (/*this.isMenuVisible() || this.DIV.style.display == 'none' || */this == BX.proxy_context)
		return;

	this.correctPosition(BX.proxy_context);
};

BX.CPageOpener.prototype.correctPosition = function(opener)
{
	if (this.bPosCorrected) return;
	var pos_self;
	if (this.DIV.style.display == 'none')
	{
		pos_self = this.adjustToNodeGetPos();
		pos_self.bottom = pos_self.top + 30;
		pos_self.right = pos_self.left + 300;
	}
	else
	{
		pos_self = BX.pos(this.DIV);
	}

	var pos_other = BX.pos(opener.Get());
	if (this.__check_intersection(pos_self, pos_other))
	{
		this.DIV.style.display = 'none';
		BX.addCustomEvent(opener, 'onMenuOpenerHide', BX.proxy(this.restorePosition, this));

		this.bPosCorrected = true;
	}
};

BX.CPageOpener.prototype.restorePosition = function()
{
	if (BX.proxy_context && BX.proxy_context.Get().style.display == 'none')
	{
		this.bPosCorrected = false;

		if (this.PARAMS.parent.bx_over || this.PARAMS.pin)
			this.UnHide();

		BX.removeCustomEvent('onMenuOpenerHide', BX.proxy(this.restorePosition, this));
	}
};

BX.CPageOpener.prototype.UnHide = function()
{
	if (!this.bPosCorrected)
		BX.CPageOpener.superclass.UnHide.apply(this, arguments);
};

BX.CPageOpener.prototype.Remove = function()
{
	BX.admin.removeComponentBorder(this.PARAMS.parent);
	BX.userOptions.save('global', 'settings', 'page_edit_control_enable', 'N');
	this.DIV.style.display = 'none';
};

/******* HINT ***************/
BX.CHintSimple = function()
{
	BX.CHintSimple.superclass.constructor.apply(this, arguments);
};
BX.extend(BX.CHintSimple, BX.CHint);

BX.CHintSimple.prototype.Init = function()
{
	this.DIV = document.body.appendChild(BX.create('DIV', {props: {className: 'bx-tooltip-simple'}, style: {display: 'none'}, children: [(this.CONTENT = BX.create('DIV'))]}));

	if (this.HINT_TITLE)
		this.CONTENT.appendChild(BX.create('B', {text: this.HINT_TITLE}));

	if (this.HINT)
		this.CONTENT_TEXT = this.CONTENT.appendChild(BX.create('DIV')).appendChild(BX.create('SPAN', {html: this.HINT}));

	if (this.PARAMS.preventHide)
	{
		BX.bind(this.DIV, 'mouseout', BX.proxy(this.Hide, this));
		BX.bind(this.DIV, 'mouseover', BX.proxy(this.Show, this));
	}

	this.bInited = true;
};

/*************************** admin informer **********************************/
BX.adminInformer = {

	itemsShow: 3,

	Init: function (itemsShow)
	{
		if(itemsShow)
			BX.adminInformer.itemsShow = itemsShow;

		var informer = BX("admin-informer");

		if(informer)
			document.body.appendChild(informer);

		BX.addCustomEvent("onTopPanelCollapse", BX.proxy(BX.adminInformer.Close, BX.adminInformer));
	},

	Toggle: function(notifyBlock)
	{
		var informer = BX("admin-informer");

		if(!informer)
			return false;

		var pos = BX.pos(notifyBlock);

		informer.style.top = (parseInt(pos.top)+parseInt(pos.height)+7)+'px';
		informer.style.left = pos.left+'px';

		if(!BX.hasClass(informer, "adm-informer-active"))
			BX.adminInformer.Show(informer);
		else
			BX.adminInformer.Hide(informer);

		return false;
	},

	Close: function()
	{
		BX.adminInformer.Hide(BX("admin-informer"));
	},

	OnInnerClick: function(event)
	{
		var target = event.target || event.srcElement;

		if(target.nodeName.toLowerCase() != 'a' || BX.hasClass(target,"adm-informer-footer"))
		{
			return BX.PreventDefault(event);
		}

		return true;
	},

	ToggleExtra : function()
	{
		var footerLink = BX("adm-informer-footer");

		if (BX.hasClass(footerLink, "adm-informer-footer-collapsed"))
			this.ShowAll();
		else
			this.HideExtra();

		return false;
	},

	ShowAll: function()
	{
		var informer = BX("admin-informer");
		for(var i=0; i<informer.children.length; i++)

			if(BX.hasClass(informer.children[i], "adm-informer-item") && informer.children[i].style.display == "none") {
				informer.children[i].style.display = "block";
			}

		var footerLink = BX("adm-informer-footer");

		if(footerLink.textContent !== undefined)
			footerLink.textContent = BX.message('JSADM_AI_HIDE_EXTRA');
		else
			footerLink.innerText = BX.message('JSADM_AI_HIDE_EXTRA');

		BX.removeClass(footerLink, "adm-informer-footer-collapsed");

		return false;
	},

	HideExtra: function()
	{
		var informer = BX("admin-informer");
		var hided = 0;

		for(var i=BX.adminInformer.itemsShow+1; i<informer.children.length; i++)
		{
			if (BX.hasClass(informer.children[i], "adm-informer-item") && informer.children[i].style.display == "block") {
				informer.children[i].style.display = "none";
				hided++;
			}
		}

		var footerLink = BX("adm-informer-footer");

		var linkText = BX.message('JSADM_AI_ALL_NOTIF')+" ("+(BX.adminInformer.itemsShow+parseInt(hided))+")";

		if(footerLink.textContent !== undefined)
			footerLink.textContent = linkText;
		else
			footerLink.innerText = linkText;

		BX.addClass(footerLink, "adm-informer-footer-collapsed");

		return false;
	},

	Show: function(informer)
	{
		var notifButton = BX("adm-header-notif-block");
		if (notifButton)
			BX.addClass(notifButton, "adm-header-notif-block-active");

		BX.onCustomEvent(informer, 'onBeforeAdminInformerShow');
		setTimeout(
			BX.proxy(function() {
					BX.bind(document, "click", BX.proxy(BX.adminInformer.Close, BX.adminInformer));
				},
				BX.adminInformer
			),0
		);
		BX.addClass(informer, "adm-informer-active");
		setTimeout(function() {BX.addClass(informer, "adm-informer-animate");},0);
	},

	Hide: function(informer)
	{
		var notifButton = BX("adm-header-notif-block");
		if (notifButton)
			BX.removeClass(notifButton, "adm-header-notif-block-active");

		BX.unbind(document, "click", BX.proxy(BX.adminInformer.Close, BX.adminInformer));

		BX.removeClass(informer, "adm-informer-animate");

		if (this.IsAnimationSupported())
			setTimeout(function() {BX.removeClass(informer, "adm-informer-active");}, 300);
		else
			BX.removeClass(informer, "adm-informer-active");

		BX.onCustomEvent(informer, 'onAdminInformerHide');
		//setTimeout(function() {BX.adminInformer.HideExtra();},500);
	},

	IsAnimationSupported : function()
	{
		var d = document.body || document.documentElement;
		if (typeof(d.style.transition) == "string")
			return true;
		else if (typeof(d.style.MozTransition) == "string")
			return true;
		else if (typeof(d.style.OTransition) == "string")
			return true;
		else if (typeof(d.style.WebkitTransition) == "string")
			return true;
		else if (typeof(d.style.msTransition) == "string")
			return true;

		return false;
	},


	SetItemHtml: function(itemIdx, html)
	{
		var itemHtmlDiv = BX("adm-informer-item-html-"+itemIdx);

		if(!itemHtmlDiv)
			return false;

		itemHtmlDiv.innerHTML = html;

		return true;
	},

	SetItemFooter: function(itemIdx, html)
	{
		var itemFooterDiv = BX("adm-informer-item-footer-"+itemIdx);

		if(!itemFooterDiv)
			return false;

		itemFooterDiv.innerHTML = html;

		if(html)
			itemFooterDiv.style.display = "block";
		else
			itemFooterDiv.style.display = "none";

		return true;
	}

};

})(window);

/* End */
;
; /* Start:/bitrix/js/main/utils.js*/
var phpVars;
if(!phpVars)
{
	phpVars = {
		ADMIN_THEME_ID: '.default',
		LANGUAGE_ID: 'en',
		FORMAT_DATE: 'DD.MM.YYYY',
		FORMAT_DATETIME: 'DD.MM.YYYY HH:MI:SS',
		opt_context_ctrl: false,
		cookiePrefix: 'BITRIX_SM',
		titlePrefix: '',
		bitrix_sessid: '',
		messHideMenu: '',
		messShowMenu: '',
		messHideButtons: '',
		messShowButtons: '',
		messFilterInactive: '',
		messFilterActive: '',
		messFilterLess: '',
		messLoading: 'Loading...',
		messMenuLoading: '',
		messMenuLoadingTitle: '',
		messNoData: '',
		messExpandTabs: '',
		messCollapseTabs: '',
		messPanelFixOn: '',
		messPanelFixOff: '',
		messPanelCollapse: '',
		messPanelExpand: ''
	};
}

var jsUtils =
{
	arEvents: Array(),

	addEvent: function(el, evname, func, capture)
	{
		if(el.attachEvent) // IE
			el.attachEvent("on" + evname, func);
		else if(el.addEventListener) // Gecko / W3C
			el.addEventListener(evname, func, false);
		else
			el["on" + evname] = func;
		this.arEvents[this.arEvents.length] = {'element': el, 'event': evname, 'fn': func};
	},

	removeEvent: function(el, evname, func)
	{
		if(el.detachEvent) // IE
			el.detachEvent("on" + evname, func);
		else if(el.removeEventListener) // Gecko / W3C
			el.removeEventListener(evname, func, false);
		else
			el["on" + evname] = null;
	},

	removeAllEvents: function(el)
	{
		var i;
		for(i=0; i<this.arEvents.length; i++)
		{
			if(this.arEvents[i] && (el==false || el==this.arEvents[i].element))
			{
				jsUtils.removeEvent(this.arEvents[i].element, this.arEvents[i].event, this.arEvents[i].fn);
				this.arEvents[i] = null;
			}
		}
		if(el==false)
			this.arEvents.length = 0;
	},

	IsDoctype: function()
	{
		if (document.compatMode)
			return (document.compatMode == "CSS1Compat");

		if (document.documentElement && document.documentElement.clientHeight)
			return true;

		return false;
	},

	GetRealPos: function(el)
	{
		if(window.BX)
			return BX.pos(el);

		if(!el || !el.offsetParent)
			return false;

		var res = Array();
		res["left"] = el.offsetLeft;
		res["top"] = el.offsetTop;
		var objParent = el.offsetParent;

		while(objParent && objParent.tagName != "BODY")
		{
			res["left"] += objParent.offsetLeft;
			res["top"] += objParent.offsetTop;
			objParent = objParent.offsetParent;
		}
		res["right"] = res["left"] + el.offsetWidth;
		res["bottom"] = res["top"] + el.offsetHeight;

		return res;
	},

	FindChildObject: function(obj, tag_name, class_name, recursive)
	{
		if(!obj)
			return null;
		var tag = tag_name.toUpperCase();
		var cl = (class_name? class_name.toLowerCase() : null);
		var n = obj.childNodes.length;
		for(var j=0; j<n; j++)
		{
			var child = obj.childNodes[j];
			if(child.tagName && child.tagName.toUpperCase() == tag)
				if(!class_name || child.className.toLowerCase() == cl)
					return child;
			if(recursive == true)
			{
				var deepChild;
				if((deepChild = jsUtils.FindChildObject(child, tag_name, class_name, true)))
					return deepChild;
			}
		}
		return null;
	},

	FindParentObject: function(obj, tag_name, class_name)
	{
		if(!obj)
			return null;
		var o = obj;
		var tag = tag_name.toUpperCase();
		var cl = (class_name? class_name.toLowerCase() : null);
		while(o.parentNode)
		{
			var parent = o.parentNode;
			if(parent.tagName && parent.tagName.toUpperCase() == tag)
				if(!class_name || parent.className.toLowerCase() == cl)
					return parent;
			o = parent;
		}
		return null;
	},

	FindNextSibling: function(obj, tag_name)
	{
		if(!obj)
			return null;
		var o = obj;
		var tag = tag_name.toUpperCase();
		while(o.nextSibling)
		{
			var sibling = o.nextSibling;
			if(sibling.tagName && sibling.tagName.toUpperCase() == tag)
				return sibling;
			o = sibling;
		}
		return null;
	},

	FindPreviousSibling: function(obj, tag_name)
	{
		if(!obj)
			return null;
		var o = obj;
		var tag = tag_name.toUpperCase();
		while(o.previousSibling)
		{
			var sibling = o.previousSibling;
			if(sibling.tagName && sibling.tagName.toUpperCase() == tag)
				return sibling;
			o = sibling;
		}
		return null;
	},

	bOpera : navigator.userAgent.toLowerCase().indexOf('opera') != -1,
	bIsIE : document.attachEvent && navigator.userAgent.toLowerCase().indexOf('opera') == -1,

	IsIE: function()
	{
		return this.bIsIE;
	},

	IsOpera: function()
	{
		return this.bOpera;
	},

	IsSafari: function()
	{
		var userAgent = navigator.userAgent.toLowerCase();
		return (/webkit/.test(userAgent));
	},

	IsEditor: function()
	{
		var userAgent = navigator.userAgent.toLowerCase();
		var version = (userAgent.match( /.+(msie)[\/: ]([\d.]+)/ ) || [])[2];
		var safari = /webkit/.test(userAgent);

		if (this.IsOpera() || (document.all && !document.compatMode && version < 6) || safari)
			return false;

		return true;
	},

	ToggleDiv: function(div)
	{
		var style = document.getElementById(div).style;
		if(style.display!="none")
			style.display = "none";
		else
			style.display = "block";
		return (style.display != "none");
	},

	urlencode: function(s)
	{
		return escape(s).replace(new RegExp('\\+','g'), '%2B');
	},

	OpenWindow: function(url, width, height)
	{
		var w = screen.width, h = screen.height;
		if(this.IsOpera())
		{
			w = document.body.offsetWidth;
			h = document.body.offsetHeight;
		}
		window.open(url, '', 'status=no,scrollbars=yes,resizable=yes,width='+width+',height='+height+',top='+Math.floor((h - height)/2-14)+',left='+Math.floor((w - width)/2-5));
	},

	SetPageTitle: function(s)
	{
		document.title = phpVars.titlePrefix+s;
		var h1 = document.getElementsByTagName("H1");
		if(h1)
			h1[0].innerHTML = s;
	},

	LoadPageToDiv: function(url, div_id)
	{
		var div = document.getElementById(div_id);
		if(!div)
			return;
		CHttpRequest.Action = function(result)
		{
			CloseWaitWindow();
			document.getElementById(div_id).innerHTML = result;
		}
		ShowWaitWindow();
		CHttpRequest.Send(url);
	},

	trim: function(s)
	{
		if (typeof s == 'string' || typeof s == 'object' && s.constructor == String)
		{
			var r, re;

			re = /^[\s\r\n]+/g;
			r = s.replace(re, "");
			re = /[\s\r\n]+$/g;
			r = r.replace(re, "");
			return r;
		}
		else
			return s;
	},

	Redirect: function(args, url)
	{
		var e = null, bShift = false;
		if(args && args.length > 0)
			e = args[0];
		if(!e)
			e = window.event;
		if(e)
			bShift = e.shiftKey;

		if(bShift)
			window.open(url);
		else
		{
			window.location.href=url;
		}
	},

	False: function(){return false;},

	AlignToPos: function(pos, w, h)
	{
		var x = pos["left"], y = pos["bottom"];

		var scroll = jsUtils.GetWindowScrollPos();
		var size = jsUtils.GetWindowInnerSize();

		if((size.innerWidth + scroll.scrollLeft) - (pos["left"] + w) < 0)
		{
			if(pos["right"] - w >= 0 )
				x = pos["right"] - w;
			else
				x = scroll.scrollLeft;
		}

		if((size.innerHeight + scroll.scrollTop) - (pos["bottom"] + h) < 0)
		{
			if(pos["top"] - h >= 0)
				y = pos["top"] - h;
			else
				y = scroll.scrollTop;
		}

		return {'left':x, 'top':y};
	},

	// evaluate js string in window scope
	EvalGlobal: function(script)
	{
		try {
		if (window.execScript)
			window.execScript(script, 'javascript');
		else if (jsUtils.IsSafari())
			window.setTimeout(script, 0);
		else
			window.eval(script);
		} catch (e) {/*alert("Error! jsUtils.EvalGlobal");*/}
	},

	GetStyleValue: function(el, styleProp)
	{
		var res;
		if(el.currentStyle)
			res = el.currentStyle[styleProp];
		else if(window.getComputedStyle)
			res = document.defaultView.getComputedStyle(el, null).getPropertyValue(styleProp);
		if(!res)
			res = '';
		return res;
	},

	GetWindowInnerSize: function(pDoc)
	{
		var width, height;
		if (!pDoc)
			pDoc = document;

		if (self.innerHeight) // all except Explorer
		{
			width = self.innerWidth;
			height = self.innerHeight;
		}
		else if (pDoc.documentElement && (pDoc.documentElement.clientHeight || pDoc.documentElement.clientWidth)) // Explorer 6 Strict Mode
		{
			width = pDoc.documentElement.clientWidth;
			height = pDoc.documentElement.clientHeight;
		}
		else if (pDoc.body) // other Explorers
		{
			width = pDoc.body.clientWidth;
			height = pDoc.body.clientHeight;
		}
		return {innerWidth : width, innerHeight : height};
	},

	GetWindowScrollPos: function(pDoc)
	{
		var left, top;
		if (!pDoc)
			pDoc = document;

		if (self.pageYOffset) // all except Explorer
		{
			left = self.pageXOffset;
			top = self.pageYOffset;
		}
		else if (pDoc.documentElement && (pDoc.documentElement.scrollTop || pDoc.documentElement.scrollLeft)) // Explorer 6 Strict
		{
			left = document.documentElement.scrollLeft;
			top = document.documentElement.scrollTop;
		}
		else if (pDoc.body) // all other Explorers
		{
			left = pDoc.body.scrollLeft;
			top = pDoc.body.scrollTop;
		}
		return {scrollLeft : left, scrollTop : top};
	},

	GetWindowScrollSize: function(pDoc)
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
	},

	GetWindowSize: function()
	{
		var innerSize = jsUtils.GetWindowInnerSize();
		var scrollPos = jsUtils.GetWindowScrollPos();
		var scrollSize = jsUtils.GetWindowScrollSize();

		return  {
			innerWidth : innerSize.innerWidth, innerHeight : innerSize.innerHeight,
			scrollLeft : scrollPos.scrollLeft, scrollTop : scrollPos.scrollTop,
			scrollWidth : scrollSize.scrollWidth, scrollHeight : scrollSize.scrollHeight
		};
	},


	arCustomEvents: {},

	addCustomEvent: function(eventName, eventHandler, arParams, handlerContextObject)
	{
		if (!this.arCustomEvents[eventName])
			this.arCustomEvents[eventName] = [];

		if (!arParams)
			arParams = [];
		if (!handlerContextObject)
			handlerContextObject = false;

		this.arCustomEvents[eventName].push(
			{
				handler: eventHandler,
				arParams: arParams,
				obj: handlerContextObject
			}
		);
	},

	removeCustomEvent: function(eventName, eventHandler)
	{
		if (!this.arCustomEvents[eventName])
			return;

		var l = this.arCustomEvents[eventName].length;
		if (l == 1)
		{
			delete this.arCustomEvents[eventName];
			return;
		}

		for (var i = 0; i < l; i++)
		{
			if (!this.arCustomEvents[eventName][i])
				continue;
			if (this.arCustomEvents[eventName][i].handler == eventHandler)
			{
				delete this.arCustomEvents[eventName][i];
				return;
			}
		}
	},

	onCustomEvent: function(eventName, arEventParams)
	{
		if (!this.arCustomEvents[eventName])
			return;

		if (!arEventParams)
			arEventParams = [];

		var h;
		for (var i = 0, l = this.arCustomEvents[eventName].length; i < l; i++)
		{
			h = this.arCustomEvents[eventName][i];
			if (!h || !h.handler)
				continue;

			if (h.obj)
				h.handler.call(h.obj, h.arParams, arEventParams);
			else
				h.handler(h.arParams, arEventParams);
		}
	},

	loadJSFile: function(arJs, oCallBack, pDoc)
	{
		if (!pDoc)
			pDoc = document;
		if (typeof arJs == 'string')
			arJs = [arJs];
		var callback = function()
		{
			if (!oCallBack)
				return;
			if (typeof oCallBack == 'function')
				return oCallBack();
			if (typeof oCallBack != 'object' || !oCallBack.func)
				return;
			var p = oCallBack.params || {};
			if (oCallBack.obj)
				oCallBack.func.apply(oCallBack.obj, p);
			else
				oCallBack.func(p);
		};
		var load_js = function(ind)
		{
			if (ind >= arJs.length)
				return callback();
			var oSript = pDoc.body.appendChild(pDoc.createElement('script'));
			oSript.src = arJs[ind];
			var bLoaded = false;
			oSript.onload = oSript.onreadystatechange = function()
			{
				if (!bLoaded && (!oSript.readyState || oSript.readyState == "loaded" || oSript.readyState == "complete"))
				{
					bLoaded = true;
					setTimeout(function (){load_js(++ind);}, 50);
				}
			};
		};
		load_js(0);
	},

	loadCSSFile: function(arCSS, pDoc, pWin)
	{
		if (typeof arCSS == 'string')
		{
			var bSingle = true;
			arCSS = [arCSS];
		}
		var i, l = arCSS.length, pLnk = [];
		if (l == 0)
			return;
		if (!pDoc)
			pDoc = document;
		if (!pWin)
			pWin = window;
		if (!pWin.bxhead)
		{
			var heads = pDoc.getElementsByTagName('HEAD');
			pWin.bxhead = heads[0];
		}
		if (!pWin.bxhead)
			return;
		for (i = 0; i < l; i++)
		{
			var lnk = document.createElement('LINK');
			lnk.href = arCSS[i];
			lnk.rel = 'stylesheet';
			lnk.type = 'text/css';
			pWin.bxhead.appendChild(lnk);
			pLnk.push(lnk);
		}
		if (bSingle)
			return lnk;
		return pLnk;
	},

	appendBXHint : function(node, html)
	{
		if (!node || !node.parentNode || !html)
			return;
		var oBXHint = new BXHint(html);
		node.parentNode.insertBefore(oBXHint.oIcon, node);
		node.parentNode.removeChild(node);
		oBXHint.oIcon.style.marginLeft = "5px";
	},

	PreventDefault : function(e)
	{
		if(!e) e = window.event;
		if(e.stopPropagation)
		{
			e.preventDefault();
			e.stopPropagation();
		}
		else
		{
			e.cancelBubble = true;
			e.returnValue = false;
		}
		return false;
	},

	CreateElement: function(tag, arAttr, arStyles, pDoc)
	{
		if (!pDoc)
			pDoc = document;
		var pEl = pDoc.createElement(tag), p;
		if(arAttr)
		{
			for(p in arAttr)
			{
				if(p == 'className' || p == 'class')
				{
					pEl.setAttribute('class', arAttr[p]);
					if (jsUtils.IsIE())
						pEl.setAttribute('className', arAttr[p]);
					continue;
				}

				if (arAttr[p] != undefined && arAttr[p] != null)
					pEl.setAttribute(p, arAttr[p]);
			}
		}
		if(arStyles)
		{
			for(p in arStyles)
				pEl.style[p] = arStyles[p];
		}
		return pEl;
	},

	in_array: function(needle, haystack)
	{
		for(var i=0; i<haystack.length; i++)
		{
			if(haystack[i] == needle)
				return true;
		}
		return false;
	},

	htmlspecialchars: function(str)
	{
		if(!str.replace)
			return str;

		return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	}
}

/************************************************/

function JCFloatDiv()
{
	var _this = this;
	this.floatDiv = null;
	this.x = this.y = 0;

	this.Create = function(arParams)
	{
		var div = document.body.appendChild(document.createElement("DIV"));
		div.id = arParams.id;
		div.style.position = 'absolute';
		div.style.left = '-10000px';
		div.style.top = '-10000px';
		if(arParams.className)
			div.className = arParams.className;
		if(arParams.zIndex)
			div.style.zIndex = arParams.zIndex;
		if(arParams.width)
			div.style.width = arParams.width+'px';
		if(arParams.height)
			div.style.height = arParams.height+'px';
		return div;
	}

	this.Show = function(div, left, top, dxShadow, restrictDrag, showSubFrame)
	{
		if (showSubFrame !== false)
			showSubFrame = true;
		var zIndex = parseInt(div.style.zIndex);
		if(zIndex <= 0 || isNaN(zIndex))
			zIndex = 100;

		//document.title = 'zIndex = ' + zIndex;
		div.style.zIndex = zIndex;

		if (left < 0)
			left = 0;

		if (top < 0)
			top = 0;

		div.style.left = parseInt(left) + "px";
		div.style.top = parseInt(top) + "px";

		if(jsUtils.IsIE() && showSubFrame === true)
		{
			var frame = document.getElementById(div.id+"_frame");
			if(!frame)
			{
				frame = document.createElement("IFRAME");
				frame.src = "javascript:''";
				frame.id = div.id+"_frame";
				frame.style.position = 'absolute';
				frame.style.borderWidth = '0px';
				frame.style.zIndex = zIndex-1;
				document.body.appendChild(frame);
			}
			frame.style.width = div.offsetWidth + "px";
			frame.style.height = div.offsetHeight + "px";
			frame.style.left = div.style.left;
			frame.style.top = div.style.top;
			frame.style.visibility = 'visible';
		}

		/*Restrict drag*/
		div.restrictDrag = restrictDrag || false;

		/*shadow*/
		if(isNaN(dxShadow))
			dxShadow = 5;

		if(dxShadow > 0)
		{
			var img = document.getElementById(div.id+'_shadow');
			if(!img)
			{
				if(jsUtils.IsIE())
				{
		 			img = document.createElement("DIV");
		 			img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/bitrix/themes/"+phpVars.ADMIN_THEME_ID+"/images/shadow.png',sizingMethod='scale')";
				}
				else
				{
		 			img = document.createElement("IMG");
					img.src = '/bitrix/themes/' + phpVars.ADMIN_THEME_ID+'/images/shadow.png';
				}
				img.id = div.id+'_shadow';
				img.style.position = 'absolute';
				img.style.zIndex = zIndex-2;
				img.style.left = '-1000px';
				img.style.top = '-1000px';
				img.style.lineHeight = 'normal';
				img.className = "bx-js-float-shadow";
				document.body.appendChild(img);
			}
			img.style.width = div.offsetWidth+'px';
			img.style.height = div.offsetHeight+'px';
			img.style.left = parseInt(div.style.left)+dxShadow+'px';
			img.style.top = parseInt(div.style.top)+dxShadow+'px';
			img.style.visibility = 'visible';
		}
		div.dxShadow = dxShadow;
	}

	this.Close = function(div)
	{
		if(!div)
			return;
		var sh = document.getElementById(div.id+"_shadow");
		if(sh)
			sh.style.visibility = 'hidden';

		var frame = document.getElementById(div.id+"_frame");
		if(frame)
			frame.style.visibility = 'hidden';
	}

	this.Move = function(div, x, y)
	{
		if(!div)
			return;

		var dxShadow = div.dxShadow;
		var left = parseInt(div.style.left)+x;
		var top = parseInt(div.style.top)+y;

		if (div.restrictDrag)
		{
			//Left side
			if (left < 0)
				left = 0;

			//Right side
			if ( (document.compatMode && document.compatMode == "CSS1Compat"))
				windowWidth = document.documentElement.scrollWidth;
			else
			{
				if (document.body.scrollWidth > document.body.offsetWidth ||
					(document.compatMode && document.compatMode == "BackCompat") ||
					(document.documentElement && !document.documentElement.clientWidth)
				)
					windowWidth = document.body.scrollWidth;
				else
					windowWidth = document.body.offsetWidth;
			}

			var floatWidth = div.offsetWidth;
			if (left > (windowWidth - floatWidth - dxShadow))
				left = windowWidth - floatWidth - dxShadow;

			//Top side
			if (top < 0)
				top = 0;
		}

		div.style.left = left+'px';
		div.style.top = top+'px';

		this.AdjustShadow(div);
	}

	this.HideShadow = function(div)
	{
		var sh = document.getElementById(div.id + "_shadow");
		sh.style.visibility = 'hidden';
	}

	this.UnhideShadow = function(div)
	{
		var sh = document.getElementById(div.id + "_shadow");
		sh.style.visibility = 'visible';
	}

	this.AdjustShadow = function(div)
	{
		var sh = document.getElementById(div.id + "_shadow");
		if(sh && sh.style.visibility != 'hidden')
		{
			var dxShadow = div.dxShadow;

			sh.style.width = div.offsetWidth+'px';
			sh.style.height = div.offsetHeight+'px';
			sh.style.left = parseInt(div.style.left)+dxShadow+'px';
			sh.style.top = parseInt(div.style.top)+dxShadow+'px';
		}

		var frame = document.getElementById(div.id+"_frame");
		if(frame)
		{
			frame.style.width = div.offsetWidth + "px";
			frame.style.height = div.offsetHeight + "px";
			frame.style.left = div.style.left;
			frame.style.top = div.style.top;
		}
	}

	this.StartDrag = function(e, div)
	{
		if(!e)
			e = window.event;
		this.x = e.clientX + document.body.scrollLeft;
		this.y = e.clientY + document.body.scrollTop;
		this.floatDiv = div;

		jsUtils.addEvent(document, "mousemove", this.MoveDrag);
		document.onmouseup = this.StopDrag;
		if(document.body.setCapture)
			document.body.setCapture();

		document.onmousedown = jsUtils.False;
		var b = document.body;
		b.ondrag = jsUtils.False;
		b.onselectstart = jsUtils.False;
		b.style.MozUserSelect = _this.floatDiv.style.MozUserSelect = 'none';
		b.style.cursor = 'move';
	}

	this.StopDrag = function(e)
	{
		if(document.body.releaseCapture)
			document.body.releaseCapture();

		jsUtils.removeEvent(document, "mousemove", _this.MoveDrag);
		document.onmouseup = null;

		this.floatDiv = null;

		document.onmousedown = null;
		var b = document.body;
		b.ondrag = null;
		b.onselectstart = null;
		b.style.MozUserSelect = _this.floatDiv.style.MozUserSelect = '';
		b.style.cursor = '';
	}

	this.MoveDrag = function(e)
	{
		var x = e.clientX + document.body.scrollLeft;
		var y = e.clientY + document.body.scrollTop;

		if(_this.x == x && _this.y == y)
			return;

		_this.Move(_this.floatDiv, (x - _this.x), (y - _this.y));
		_this.x = x;
		_this.y = y;
	}
}
var jsFloatDiv = new JCFloatDiv();

/************************************************/

var BXHint = function(innerHTML, element, addParams)
{
	this.oDivOver = false;
	this.timeOutID = null;
	this.oIcon = null;
	this.freeze = false;
	this.x = 0;
	this.y = 0;
	this.time = 700;

	if (!innerHTML)
		innerHTML = "";
	this.Create(innerHTML, element, addParams);
}

BXHint.prototype.Create = function(innerHTML, element, addParams)
{
	var
		_this = this,
		width = 0,
		height = 0,
		className = null,
		type = "icon";
	this.bWidth = true;

	if (addParams)
	{
		if (addParams.width === false)
			this.bWidth = false;
		else if (addParams.width)
			width = addParams.width;

		if (addParams.height)
			height = addParams.height;

		if (addParams.className)
			className = addParams.className;

		if (addParams.type && (addParams.type == "link" || addParams.type == "icon"))
			type = addParams.type;
		if (addParams.time > 0)
			this.time = addParams.time;
	}

	if (element)
		type = "element";

	if (type == "icon")
	{
		var element = document.createElement("IMG");
		element.src = (addParams && addParams.iconSrc) ? addParams.iconSrc : "/bitrix/themes/"+phpVars.ADMIN_THEME_ID+"/public/popup/hint.gif";
		element.ondrag = jsUtils.False;
	}
	else if (type == "link")
	{
		var element = document.createElement("A");
		element.href = "";
		element.onclick = function(e){return false;}
		element.innerHTML = "[?]";
	}

	this.element = element;
	if (type == "element")
	{
		if(addParams && addParams.show_on_click)
		{
			jsUtils.addEvent(
				element,
				"click",
				function (event)
				{
					if (!event)
						event = window.event;
					_this.GetMouseXY(event);
					_this.timeOutID = setTimeout(function () {_this.Show(innerHTML,width,height,className) }, 10);
				}
			);
		}
		else
		{
			jsUtils.addEvent(
				element,
				"mouseover",
				function (event)
				{
					if (!event)
						event = window.event;
					_this.GetMouseXY(event);
					_this.timeOutID = setTimeout(function () {_this.Show(innerHTML,width,height,className) }, 750);
				}
			);
		}

		jsUtils.addEvent(
			element,
			"mouseout",
			function(event)
			{
				if (_this.timeOutID)
					clearTimeout(_this.timeOutID);
				_this.SmartHide(_this);
			}
		);
	}
	else
	{
		this.oIcon = element;
		element.onmouseover = function(event) {if (!event) event = window.event; _this.GetMouseXY(event); _this.Show(innerHTML,width,height,className)};
		element.onmouseout = function() {_this.SmartHide(_this);};
	}
}

BXHint.prototype.IsFrozen = function()
{
	return this.freeze;
}

BXHint.prototype.Freeze = function()
{
	this.freeze = true;
	this.Hide();
}

BXHint.prototype.UnFreeze = function()
{
	this.freeze = false;
}

BXHint.prototype.GetMouseXY = function(event)
{
	if (event.pageX || event.pageY)
	{
		this.x = event.pageX;
		this.y = event.pageY;
	}
	else if (event.clientX || event.clientY)
	{
		this.x = event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft;
		this.y = event.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop;
	}
}

BXHint.prototype.Show = function(innerHTML, width, height, className)
{
	//Delete previous hint
	var old = document.getElementById("__BXHint_div");
	if (old)
		this.Hide();

	if (this.freeze)
		return;

	var _this = this;
	var oDiv = document.body.appendChild(document.createElement("DIV"));
	oDiv.onmouseover = function(){_this.oDivOver = true};
	oDiv.onmouseout = function(){_this.oDivOver = false; _this.SmartHide(_this);}
	oDiv.id = "__BXHint_div";
	oDiv.className = (className) ? className : "bxhint";
	oDiv.style.position = 'absolute';
	if (width && this.bWidth)
		oDiv.style.width = width + "px";

	if (height)
		oDiv.style.height = height + "px";
	oDiv.innerHTML = innerHTML;

	var w = oDiv.offsetWidth;
	var h = oDiv.offsetHeight;
	if (this.bWidth)
	{
		if (!width && w>200)
			w = Math.round(Math.sqrt(1.618*w*h));
		oDiv.style.width = w + "px";
		h = oDiv.offsetHeight;
	}

	var pos = {left : this.x + 10, right : this.x + w, top : this.y, bottom : this.y + h};

	pos = this.AlignToPos(pos, w, h);

	oDiv.style.zIndex = 2100;

	jsFloatDiv.Show(oDiv, pos.left, pos.top,3);

//	oDiv.ondrag = jsUtils.False;
//	oDiv.onselectstart = jsUtils.False;
//	oDiv.style.MozUserSelect = 'none';
	oDiv = null;
}

BXHint.prototype.AlignToPos = function(pos, w, h)
{
	var body = document.body;
	if((body.clientWidth + body.scrollLeft) < (pos.left + w))
		pos.left = (pos.left - w >= 0) ? (pos.left - w) : body.scrollLeft;

	if((body.clientHeight + body.scrollTop) - (pos["bottom"]) < 0)
		pos.top = (pos.top - h >= 0) ? (pos.top - h) : body.scrollTop;

	return pos;
}

BXHint.prototype.Hide = function()
{
	var oDiv = document.getElementById("__BXHint_div");

	if (!oDiv)
		return;

	jsFloatDiv.Close(oDiv);
	oDiv.parentNode.removeChild(oDiv);
	oDiv = null;
}

BXHint.prototype.SmartHide = function(_this)
{
	setTimeout(function ()
		{
			if (!_this.oDivOver)
				_this.Hide();
		}, 100
	);
}

/************************************************/

function WaitOnKeyPress(e)
{
	if(!e) e = window.event
	if(!e) return;
	if(e.keyCode == 27)
		CloseWaitWindow();
}

function ShowWaitWindow()
{
	CloseWaitWindow();

	var obWndSize = jsUtils.GetWindowSize();

	var div = document.body.appendChild(document.createElement("DIV"));
	div.id = "wait_window_div";
	div.innerHTML = phpVars.messLoading;
	div.className = "waitwindow";
	//div.style.left = obWndSize.scrollLeft + (obWndSize.innerWidth - div.offsetWidth) - (jsUtils.IsIE() ? 5 : 20) + "px";
	div.style.right = (5 - obWndSize.scrollLeft) + 'px';
	div.style.top = obWndSize.scrollTop + 5 + "px";

	if(jsUtils.IsIE())
	{
		var frame = document.createElement("IFRAME");
		frame.src = "javascript:''";
		frame.id = "wait_window_frame";
		frame.className = "waitwindow";
		frame.style.width = div.offsetWidth + "px";
		frame.style.height = div.offsetHeight + "px";
		frame.style.right = div.style.right;
		frame.style.top = div.style.top;
		document.body.appendChild(frame);
	}
	jsUtils.addEvent(document, "keypress", WaitOnKeyPress);
}

function CloseWaitWindow()
{
	jsUtils.removeEvent(document, "keypress", WaitOnKeyPress);

	var frame = document.getElementById("wait_window_frame");
	if(frame)
		frame.parentNode.removeChild(frame);

	var div = document.getElementById("wait_window_div");
	if(div)
		div.parentNode.removeChild(div);
}

/************************************************/

var jsSelectUtils =
{
	addNewOption: function(select_id, opt_value, opt_name, do_sort, check_unique)
	{
		var oSelect = (typeof(select_id) == 'string' || select_id instanceof String? document.getElementById(select_id) : select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			if(check_unique !== false)
			{
				for(var i=0;i<n;i++)
					if(oSelect[i].value==opt_value)
						return;
			}
			var newoption = new Option(opt_name, opt_value, false, false);
			oSelect.options[n]=newoption;
		}
		if(do_sort === true)
			this.sortSelect(select_id);
	},

	deleteOption: function(select_id, opt_value)
	{
		var oSelect = (typeof(select_id) == 'string' || select_id instanceof String? document.getElementById(select_id) : select_id);
		if(oSelect)
		{
			for(var i=0;i<oSelect.length;i++)
				if(oSelect[i].value==opt_value)
				{
					oSelect.remove(i);
					break;
				}
		}
	},

	deleteSelectedOptions: function(select_id)
	{
		var oSelect = (typeof(select_id) == 'string' || select_id instanceof String? document.getElementById(select_id) : select_id);
		if(oSelect)
		{
			var i=0;
			while(i<oSelect.length)
				if(oSelect[i].selected)
				{
					oSelect[i].selected=false;
					oSelect.remove(i);
				}
				else
					i++;
		}
	},

	deleteAllOptions: function(oSelect)
	{
		if(oSelect)
		{
			for(var i=oSelect.length-1; i>=0; i--)
				oSelect.remove(i);
		}
	},

	optionCompare: function(record1, record2)
	{
		var value1 = record1.optText.toLowerCase();
		var value2 = record2.optText.toLowerCase();
		if (value1 > value2) return(1);
		if (value1 < value2) return(-1);
		return(0);
	},

	sortSelect: function(select_id)
	{
		var oSelect = (typeof(select_id) == 'string' || select_id instanceof String? document.getElementById(select_id) : select_id);
		if(oSelect)
		{
			var myOptions = [];
			var n = oSelect.options.length;
			for (var i=0;i<n;i++)
			{
				myOptions[i] = {
					optText:oSelect[i].text,
					optValue:oSelect[i].value
				};
			}
			myOptions.sort(this.optionCompare);
			oSelect.length=0;
			n = myOptions.length;
			for(var i=0;i<n;i++)
			{
				var newoption = new Option(myOptions[i].optText, myOptions[i].optValue, false, false);
				oSelect[i]=newoption;
			}
		}
	},

	selectAllOptions: function(select_id)
	{
		var oSelect = (typeof(select_id) == 'string' || select_id instanceof String? document.getElementById(select_id) : select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
				oSelect[i].selected=true;
		}
	},

	selectOption: function(select_id, opt_value)
	{
		var oSelect = (typeof(select_id) == 'string' || select_id instanceof String? document.getElementById(select_id) : select_id);
		if(oSelect)
		{
			var n = oSelect.length;
			for(var i=0;i<n;i++)
				oSelect[i].selected = (oSelect[i].value == opt_value);
		}
	},

	addSelectedOptions: function(oSelect, to_select_id, check_unique, do_sort)
	{
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
			if(oSelect[i].selected)
				this.addNewOption(to_select_id, oSelect[i].value, oSelect[i].text, do_sort, check_unique);
	},

	moveOptionsUp: function(oSelect)
	{
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=0; i<n; i++)
		{
			if(oSelect[i].selected && i>0 && oSelect[i-1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i-1].text, oSelect[i-1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i-1] = option1;
				oSelect[i-1].selected = true;
			}
		}
	},

	moveOptionsDown: function(oSelect)
	{
		if(!oSelect)
			return;
		var n = oSelect.length;
		for(var i=n-1; i>=0; i--)
		{
			if(oSelect[i].selected && i<n-1 && oSelect[i+1].selected == false)
			{
				var option1 = new Option(oSelect[i].text, oSelect[i].value);
				var option2 = new Option(oSelect[i+1].text, oSelect[i+1].value);
				oSelect[i] = option2;
				oSelect[i].selected = false;
				oSelect[i+1] = option1;
				oSelect[i+1].selected = true;
			}
		}
	}

}

/************************************************/
/* End */
;
; /* Start:/bitrix/js/main/rating_like.js*/
if (!BXRL)
{
	var BXRL = {};
	var BXRLW = null;
}

RatingLike = function(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile, pathToAjax)
{
	this.enabled = true;
	this.likeId = likeId;
	this.entityTypeId = entityTypeId;
	this.entityId = entityId;
	this.available = available == 'Y'? true: false;
	this.userId = userId;
	this.localize = localize;
	this.template = template;
	this.pathToUserProfile = pathToUserProfile;
	this.pathToAjax = typeof(pathToAjax) == "string"? pathToAjax: '/bitrix/components/bitrix/rating.vote/vote.ajax.php';

	this.box = BX('bx-ilike-button-'+likeId);
	if (this.box === null)
	{
		this.enabled = false;
		return false;
	}

	this.button = BX.findChild(this.box, {className:'bx-ilike-left-wrap'}, true, false);
	this.buttonText = BX.findChild(this.button, {className:'bx-ilike-text'}, true, false);
	this.count = BX.findChild(this.box,  {tagName:'span', className:'bx-ilike-right-wrap'}, true, false);
	this.countText	= BX.findChild(this.count, {tagName:'span', className:'bx-ilike-right'}, true, false);
	this.popup = null;
	this.popupId = null;
	this.popupOpenId = null;
	this.popupTimeoutId = null;
	this.popupContent = BX.findChild(BX('bx-ilike-popup-cont-'+likeId), {tagName:'span', className:'bx-ilike-popup'}, true, false);
	this.popupContentPage = 1;
	this.popupListProcess = false;
	this.popupTimeout = false;
	this.likeTimeout = false;

	this.lastVote = BX.hasClass(template == 'standart'? this.button: this.count, 'bx-you-like')? 'plus': 'cancel';
}

RatingLike.LiveUpdate = function(params)
{
	if (params.USER_ID == BX.message('USER_ID'))
		return false;

	for(var i in BXRL)
	{
		if (BXRL[i].entityTypeId == params.ENTITY_TYPE_ID & BXRL[i].entityId == params.ENTITY_ID)
		{
			var element = BXRL[i];
			element.countText.innerHTML = parseInt(params.TOTAL_POSITIVE_VOTES);
			element.count.insertBefore(
				BX.create("span", { props : { className : "bx-ilike-plus-one" }, style: {width: (element.countText.clientWidth-8)+'px', height: (element.countText.clientHeight-8)+'px'}, html: (params.TYPE == 'ADD'? '+1': '-1')})
			, element.count.firstChild);

			if (element.popup)
			{
				element.popup.close();
				element.popupContentPage = 1;
			}
		}
	}
}

RatingLike.Set = function(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile, pathToAjax)
{
	if (template === undefined)
		template = 'standart';

	if (!BXRL[likeId] || BXRL[likeId].tryToSet <= 5)
	{
		var tryToSend = BXRL[likeId] && BXRL[likeId].tryToSet? BXRL[likeId].tryToSet: 1;
		BXRL[likeId] = new RatingLike(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile, pathToAjax);
		if (BXRL[likeId].enabled)
		{
			RatingLike.Init(likeId);
		}
		else
		{
			setTimeout(function(){
				BXRL[likeId].tryToSet = tryToSend+1;
				RatingLike.Set(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile, pathToAjax);
			}, 500);
		}
	}
};

RatingLike.Init = function(likeId)
{
	// like/unlike button
	if (BXRL[likeId].available)
	{

		BX.bind(BXRL[likeId].template == 'standart'? BXRL[likeId].button: BXRL[likeId].buttonText, 'click' ,function(e) {
			clearTimeout(BXRL[likeId].likeTimeout);
			if (BX.hasClass(BXRL[likeId].template == 'standart'? this: BXRL[likeId].count, 'bx-you-like'))
			{
				BXRL[likeId].buttonText.innerHTML	=	BXRL[likeId].localize['LIKE_N'];
				BXRL[likeId].countText.innerHTML		= 	parseInt(BXRL[likeId].countText.innerHTML)-1;
				BX.removeClass(BXRL[likeId].template == 'standart'? this: BXRL[likeId].count, 'bx-you-like');

				BXRL[likeId].likeTimeout = setTimeout(function(){
					if (BXRL[likeId].lastVote != 'cancel')
						RatingLike.Vote(likeId, 'cancel');
				}, 1000);
			}
			else
			{
				BXRL[likeId].buttonText.innerHTML	=	BXRL[likeId].localize['LIKE_Y'];
				BXRL[likeId].countText.innerHTML 	= 	parseInt(BXRL[likeId].countText.innerHTML)+1;
				BX.addClass(BXRL[likeId].template == 'standart'? this: BXRL[likeId].count, 'bx-you-like');

				BXRL[likeId].likeTimeout = setTimeout(function(){
					if (BXRL[likeId].lastVote != 'plus')
						RatingLike.Vote(likeId, 'plus');
				}, 1000);
			}
			BX.removeClass(this.box, 'bx-ilike-button-hover');
			BX.PreventDefault(e);
		});
		// Hover/unHover like-button
		BX.bind(BXRL[likeId].box, 'mouseover', function() {BX.addClass(this, 'bx-ilike-button-hover')});
		BX.bind(BXRL[likeId].box, 'mouseout', function() {BX.removeClass(this, 'bx-ilike-button-hover')});

	}
	else
	{
		if (BXRL[likeId].buttonText != undefined)
			BXRL[likeId].buttonText.innerHTML	=	BXRL[likeId].localize['LIKE_D'];
	}
	// get like-user-list
	RatingLike.PopupScroll(likeId);

	BX.bind(BXRL[likeId].count, 'mouseover' , function() {
		clearTimeout(BXRL[likeId].popupTimeoutId);
		BXRL[likeId].popupTimeoutId = setTimeout(function(){
			if (BXRLW == likeId)
				return false;
			if (BXRL[likeId].popupContentPage == 1)
				RatingLike.List(likeId, 1);
			BXRL[likeId].popupTimeoutId = setTimeout(function() {
				RatingLike.OpenWindow(likeId);
			}, 400);
		}, 400);
	});
	BX.bind(BXRL[likeId].count, 'mouseout' , function() {
		clearTimeout(BXRL[likeId].popupTimeoutId);
	});
	BX.bind(BXRL[likeId].count, 'click' , function() {
		clearTimeout(BXRL[likeId].popupTimeoutId);
		if (BXRL[likeId].popupContentPage == 1)
			RatingLike.List(likeId, 1);
		RatingLike.OpenWindow(likeId);
	});

	BX.bind(BXRL[likeId].box, 'mouseout' , function() {
		clearTimeout(BXRL[likeId].popupTimeout);
		BXRL[likeId].popupTimeout = setTimeout(function(){
			if (BXRL[likeId].popup !== null)
			{
				BXRL[likeId].popup.close();
				BXRLW = null;
			}
		}, 1000);
	});
	BX.bind(BXRL[likeId].box, 'mouseover' , function() {
		clearTimeout(BXRL[likeId].popupTimeout);
	});
}

RatingLike.OpenWindow = function(likeId)
{
	if (parseInt(BXRL[likeId].countText.innerHTML) == 0)
		return false;

	if (BXRL[likeId].popup == null)
	{
		BXRL[likeId].popup = new BX.PopupWindow('ilike-popup-'+likeId, (BXRL[likeId].template == 'standart'? BXRL[likeId].count: BXRL[likeId].box), {
			lightShadow : true,
			offsetLeft: 5,
			autoHide: true,
			closeByEsc: true,
			zIndex: 2005,
			bindOptions: {position: "top"},
			events : {
				onPopupClose : function() { BXRLW = null; },
				onPopupDestroy : function() {  }
			},
			content : BX('bx-ilike-popup-cont-'+likeId)
		});
		BXRL[likeId].popup.setAngle({});

		BX.bind(BX('ilike-popup-'+likeId), 'mouseout' , function() {
			clearTimeout(BXRL[likeId].popupTimeout);
			BXRL[likeId].popupTimeout = setTimeout(function(){
				BXRL[likeId].popup.close();
			}, 1000);
		});

		BX.bind(BX('ilike-popup-'+likeId), 'mouseover' , function() {
			clearTimeout(BXRL[likeId].popupTimeout);
		});
	}

	if (BXRLW != null)
		BXRL[BXRLW].popup.close();

	BXRLW = likeId;
	BXRL[likeId].popup.show();

	RatingLike.AdjustWindow(likeId);
}

RatingLike.Vote = function(likeId, voteAction)
{
	BX.ajax({
		url: BXRL[likeId].pathToAjax,
		method: 'POST',
		dataType: 'json',
		data: {'RATING_VOTE' : 'Y', 'RATING_VOTE_TYPE_ID' : BXRL[likeId].entityTypeId, 'RATING_VOTE_ENTITY_ID' : BXRL[likeId].entityId, 'RATING_VOTE_ACTION' : voteAction, 'sessid': BX.bitrix_sessid()},
		onsuccess: function(data)	{
			BXRL[likeId].lastVote = data.action;
			BXRL[likeId].countText.innerHTML = data.items_all;
			BXRL[likeId].popupContentPage = 1;

			BXRL[likeId].popupContent.innerHTML = '';
			spanTag0 = document.createElement("span");
			spanTag0.className = "bx-ilike-wait";
			BXRL[likeId].popupContent.appendChild(spanTag0);
			RatingLike.AdjustWindow(likeId);

			if(BX('ilike-popup-'+likeId) && BX('ilike-popup-'+likeId).style.display == "block")
				RatingLike.List(likeId, null);
		},
		onfailure: function(data)	{}
	});
	return false;
}

RatingLike.List = function(likeId, page)
{
	if (parseInt(BXRL[likeId].countText.innerHTML) == 0)
		return false;

	if (page == null)
		page = BXRL[likeId].popupContentPage;
	BXRL[likeId].popupListProcess = true;
	BX.ajax({
		url: BXRL[likeId].pathToAjax,
		method: 'POST',
		dataType: 'json',
		data: {'RATING_VOTE_LIST' : 'Y', 'RATING_VOTE_TYPE_ID' : BXRL[likeId].entityTypeId, 'RATING_VOTE_ENTITY_ID' : BXRL[likeId].entityId, 'RATING_VOTE_LIST_PAGE' : page, 'PATH_TO_USER_PROFILE' : BXRL[likeId].pathToUserProfile, 'sessid': BX.bitrix_sessid()},
		onsuccess: function(data)
		{
			BXRL[likeId].countText.innerHTML = data.items_all;

			if ( parseInt(data.items_page) == 0 )
				return false;

			if (page == 1)
			{
				BXRL[likeId].popupContent.innerHTML = '';
				spanTag0 = document.createElement("span");
				spanTag0.className = "bx-ilike-bottom_scroll";
				BXRL[likeId].popupContent.appendChild(spanTag0);
			}
			BXRL[likeId].popupContentPage += 1;

			for (var i = 0; i < data.items.length; i++)
			{
				BXRL[likeId].popupContent.appendChild(
					BX.create("a", { attrs: { href: data.items[i]['URL'], target: '_blank' }, props : { className : "bx-ilike-popup-img"}, children:[
						BX.create("span", { props : { className : "bx-ilike-popup-avatar"}, html: data.items[i]['PHOTO']}),
						BX.create("span", { props : { className : "bx-ilike-popup-name"}, html: data.items[i]['FULL_NAME']})
					]})
				);
			}

			RatingLike.AdjustWindow(likeId);
			RatingLike.PopupScroll(likeId);

			BXRL[likeId].popupListProcess = false;
		},
		onfailure: function(data)	{}
	});
	return false;
}

RatingLike.AdjustWindow = function(likeId)
{
	if (BXRL[likeId].popup != null)
	{
		BXRL[likeId].popup.bindOptions.forceBindPosition = true;
		BXRL[likeId].popup.adjustPosition();
		BXRL[likeId].popup.bindOptions.forceBindPosition = false;
	}
}

RatingLike.PopupScroll = function(likeId)
{
	BX.bind(BXRL[likeId].popupContent, 'scroll' , function() {
		if (this.scrollTop > (this.scrollHeight - this.offsetHeight) / 1.5)
		{
			RatingLike.List(likeId, null);
			BX.unbindAll(this);
		}
	});
}
/* End */
;
; /* Start:/bitrix/js/main/core/core_tooltip.js*/
(function(window) {
if (BX.tooltip) return;

var arTooltipIndex = {},
	bDisable = false;

BX.tooltip = function(user_id, anchor_name, loader, rootClassName, bForceUseLoader)
{
	if (BX.message('TOOLTIP_ENABLED') != "Y")
		return;

	if(BX.browser.IsAndroid() || BX.browser.IsIOS())
	{
		return;
	}

	BX.ready(function() {
		var anchor = BX(anchor_name);
		if (null == anchor)
			return;

		var tooltipId = user_id;
		if(bForceUseLoader && BX.type.isNotEmptyString(loader))
		{
			// prepare tooltip ID from custom loader
			var loaderHash = 0;
			for(var i = 0, len = loader.length; i < len; i++)
			{
				loaderHash = (31 * loaderHash + loader.charCodeAt(i)) << 0;
			}

			tooltipId = loaderHash + user_id;
		}

		if (null == arTooltipIndex[tooltipId])
			arTooltipIndex[tooltipId] = new BX.CTooltip(user_id, anchor, loader, rootClassName, bForceUseLoader);
		else
		{
			arTooltipIndex[tooltipId].ANCHOR = anchor;
			arTooltipIndex[tooltipId].rootClassName = rootClassName;
			arTooltipIndex[tooltipId].LOADER = (bForceUseLoader && BX.type.isNotEmptyString(loader)) ? loader : '/bitrix/tools/tooltip.php';
			arTooltipIndex[tooltipId].Create();
		}
	});
};

BX.tooltip.disable = function(){ bDisable = true; };
BX.tooltip.enable = function(){ bDisable = false; };

BX.CTooltip = function(user_id, anchor, loader, rootClassName, bForceUseLoader)
{
	this.LOADER = (bForceUseLoader && BX.type.isNotEmptyString(loader)) ? loader : '/bitrix/tools/tooltip.php';
	this.USER_ID = user_id;
	this.ANCHOR = anchor;
	this.rootClassName = '';

	if (
		rootClassName != 'undefined'
		&& rootClassName != null
		&& rootClassName.length > 0
	)
		this.rootClassName = rootClassName;

	var old = document.getElementById('user_info_' + this.USER_ID);
	if (null != old)
	{
		if (null != old.parentNode)
			old.parentNode.removeChild(old);

		old = null;
	}

	var _this = this;

	this.INFO = null;

	this.width = 393;
	this.height = 302;

	this.CoordsLeft = 0;
	this.CoordsTop = 0;
	this.AnchorRight = 0;
	this.AnchorBottom = 0;

	this.DIV = null;
	this.ROOT_DIV = null;

	if (BX.browser.IsIE())
		this.IFRAME = null;

	this.v_delta = 0;
	this.classNameAnim = false;
	this.classNameFixed = false;

	this.left = 0;
	this.top = 0;

	this.tracking = false;
	this.active = false;
	this.showed = false;

	this.Create = function()
	{
		_this.ANCHOR.onmouseover = function() {
			if (!bDisable)
			{
				_this.StartTrackMouse(this);
			}
		};

		_this.ANCHOR.onmouseout = function() {
			_this.StopTrackMouse(this);
		}
	};

	this.Create();

	this.TrackMouse = function(e)
	{
		if(!_this.tracking)
			return;

		var current;
		if(e && e.pageX)
			current = {x: e.pageX, y: e.pageY};
		else
			current = {x: e.clientX + document.body.scrollLeft, y: e.clientY + document.body.scrollTop};

		if(current.x < 0)
			current.x = 0;
		if(current.y < 0)
			current.y = 0;

		current.time = _this.tracking;

		if(!_this.active)
			_this.active = current;
		else
		{
			if(
				_this.active.x >= (current.x - 1) && _this.active.x <= (current.x + 1)
				&& _this.active.y >= (current.y - 1) && _this.active.y <= (current.y + 1)
			)
			{
				if((_this.active.time + 20/*2sec*/) <= current.time)
					_this.ShowTooltip();
			}
			else
				_this.active = current;
		}
	};

	this.ShowTooltip = function()
	{
		var old = document.getElementById('user_info_' + _this.USER_ID);
		if(bDisable || old && old.style.display == 'block')
			return;

		var bIE = (BX.browser.IsIE() && !BX.browser.IsIE10());

		if (null == _this.DIV && null == _this.ROOT_DIV)
		{
			_this.ROOT_DIV = document.body.appendChild(document.createElement('DIV'));
			_this.ROOT_DIV.style.position = 'absolute';

			_this.DIV = _this.ROOT_DIV.appendChild(document.createElement('DIV'));
			if (bIE)
				_this.DIV.className = 'bx-user-info-shadow-ie';
			else
				_this.DIV.className = 'bx-user-info-shadow';

			_this.DIV.style.width = _this.width + 'px';
			_this.DIV.style.height = _this.height + 'px';
		}

		var left = _this.CoordsLeft;
		var top = _this.CoordsTop + 30;
		var arScroll = jsUtils.GetWindowScrollPos();
		var body = document.body;

		var h_mirror = false;
		var v_mirror = false;

		if((body.clientWidth + arScroll.scrollLeft) < (left + _this.width))
		{
			left = _this.AnchorRight - _this.width;
			h_mirror = true;
		}

		if((top - arScroll.scrollTop) < 0)
		{
			top = _this.AnchorBottom - 5;
			v_mirror = true;
			_this.v_delta = 40;
		}
		else
			_this.v_delta = 0;

		_this.ROOT_DIV.style.left = parseInt(left) + "px";
		_this.ROOT_DIV.style.top = parseInt(top) + "px";
		_this.ROOT_DIV.style.zIndex = 1200;

		BX.bind(BX(_this.ROOT_DIV), "click", BX.eventCancelBubble);

		if (
			this.rootClassName != 'undefined'
			&& this.rootClassName != null
			&& this.rootClassName.length > 0
		)
			_this.ROOT_DIV.className = this.rootClassName;

		if ('' == _this.DIV.innerHTML)
		{
			var url;
			if (_this.LOADER.indexOf('?') >= 0)
			{
				url = _this.LOADER + '&MUL_MODE=INFO&USER_ID=' + _this.USER_ID + '&site=' + (BX.message('SITE_ID') || '');
			}
			else
			{
				url = _this.LOADER + '?MUL_MODE=INFO&USER_ID=' + _this.USER_ID + '&site=' + (BX.message('SITE_ID') || '');
			}

			BX.ajax.get(url, _this.InsertData);
			_this.DIV.id = 'user_info_' + _this.USER_ID;

			_this.DIV.innerHTML = '<div class="bx-user-info-wrap">'
				+ '<div class="bx-user-info-leftcolumn">'
					+ '<div class="bx-user-photo" id="user-info-photo-' + _this.USER_ID + '"><div class="bx-user-info-data-loading">' + BX.message('JS_CORE_LOADING') + '</div></div>'
					+ '<div class="bx-user-tb-control bx-user-tb-control-left" id="user-info-toolbar-' + _this.USER_ID + '"></div>'
				+ '</div>'
				+ '<div class="bx-user-info-data">'
					+ '<div id="user-info-data-card-' + _this.USER_ID + '"></div>'
					+ '<div class="bx-user-info-data-tools">'
						+ '<div class="bx-user-tb-control bx-user-tb-control-right" id="user-info-toolbar2-' + _this.USER_ID + '"></div>'
						+ '<div class="bx-user-info-data-clear"></div>'
					+ '</div>'
				+ '</div>'
				+ '</div><div class="bx-user-info-bottomarea"></div>';
		}

		if (bIE)
		{
			_this.DIV.className = 'bx-user-info-shadow-ie';
			_this.classNameAnim = 'bx-user-info-shadow-anim-ie';
			_this.classNameFixed = 'bx-user-info-shadow-ie';
		}
		else
		{
			_this.DIV.className = 'bx-user-info-shadow';
			_this.classNameAnim = 'bx-user-info-shadow-anim';
			_this.classNameFixed = 'bx-user-info-shadow';
		}

		_this.filterFixed = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/bitrix/components/bitrix/main.user.link/templates/.default/images/cloud-left-top.png', sizingMethod = 'crop' );";

		if (h_mirror && v_mirror)
		{
			if (BX.browser.IsIE6())
			{
				_this.DIV.className = 'bx-user-info-shadow-hv-ie6';
				_this.classNameAnim = 'bx-user-info-shadow-hv-anim-ie6';
				_this.classNameFixed = 'bx-user-info-shadow-hv-ie6';
			}
			else if (bIE)
			{
				_this.DIV.className = 'bx-user-info-shadow-hv-ie';
				_this.classNameAnim = 'bx-user-info-shadow-hv-anim-ie';
				_this.classNameFixed = 'bx-user-info-shadow-hv-ie';
			}
			else
			{
				_this.DIV.className = 'bx-user-info-shadow-hv';
				_this.classNameAnim = 'bx-user-info-shadow-hv-anim';
				_this.classNameFixed = 'bx-user-info-shadow-hv';
			}

			_this.filterFixed = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/bitrix/components/bitrix/main.user.link/templates/.default/images/cloud-right-bottom.png', sizingMethod = 'crop' );";
		}
		else
		{
			if (h_mirror)
			{
				if (bIE)
				{
					_this.DIV.className = 'bx-user-info-shadow-h-ie';
					_this.classNameAnim = 'bx-user-info-shadow-h-anim-ie';
					_this.classNameFixed = 'bx-user-info-shadow-h-ie';
				}
				else
				{
					_this.DIV.className = 'bx-user-info-shadow-h';
					_this.classNameAnim = 'bx-user-info-shadow-h-anim';
					_this.classNameFixed = 'bx-user-info-shadow-h';
				}

				_this.filterFixed = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/bitrix/components/bitrix/main.user.link/templates/.default/images/cloud-right-top.png', sizingMethod = 'crop' );";
			}

			if (v_mirror)
			{
				if (BX.browser.IsIE6())
				{
					_this.DIV.className = 'bx-user-info-shadow-v-ie6';
					_this.classNameAnim = 'bx-user-info-shadow-v-anim-ie6';
					_this.classNameFixed = 'bx-user-info-shadow-v-ie6';
				}
				else if (bIE)
				{
					_this.DIV.className = 'bx-user-info-shadow-v-ie';
					_this.classNameAnim = 'bx-user-info-shadow-v-anim-ie';
					_this.classNameFixed = 'bx-user-info-shadow-v-ie';
				}
				else
				{
					_this.DIV.className = 'bx-user-info-shadow-v';
					_this.classNameAnim = 'bx-user-info-shadow-v-anim';
					_this.classNameFixed = 'bx-user-info-shadow-v';
				}

				_this.filterFixed = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='/bitrix/components/bitrix/main.user.link/templates/.default/images/cloud-left-bottom.png', sizingMethod = 'crop' );";
			}
		}


		if (BX.browser.IsIE() && null == _this.IFRAME)
		{
			_this.IFRAME = document.body.appendChild(document.createElement('IFRAME'));
			_this.IFRAME.id = _this.DIV.id + "_frame";
			_this.IFRAME.style.position = 'absolute';
			_this.IFRAME.style.width = (_this.width - 60) + 'px';
			_this.IFRAME.style.height = (_this.height - 100) + 'px';
			_this.IFRAME.style.borderStyle = 'solid';
			_this.IFRAME.style.borderWidth = '0px';
			_this.IFRAME.style.zIndex = 550;
			_this.IFRAME.style.display = 'none';
		}
		if (BX.browser.IsIE())
		{
			_this.IFRAME.style.left = (parseInt(left) + 25) + "px";
			_this.IFRAME.style.top = (parseInt(top) + 30 + _this.v_delta) + "px";
		}

		_this.DIV.style.display = 'none';
		_this.ShowOpacityEffect({func: _this.SetVisible, obj: _this.DIV, arParams: []}, 0);

		document.getElementById('user_info_' + _this.USER_ID).onmouseover = function() {
			_this.StartTrackMouse(this);
		};

		document.getElementById('user_info_' + _this.USER_ID).onmouseout = function() {
			_this.StopTrackMouse(this);
		}
	};

	this.InsertData = function(data)
	{
		if (null != data && data.length > 0)
		{
			eval('_this.INFO = ' + data);

			var cardEl = document.getElementById('user-info-data-card-' + _this.USER_ID);
			cardEl.innerHTML = _this.INFO.RESULT.Card;

			var photoEl = document.getElementById('user-info-photo-' + _this.USER_ID);
			photoEl.innerHTML = _this.INFO.RESULT.Photo;

			var toolbarEl = document.getElementById('user-info-toolbar-' + _this.USER_ID);
			toolbarEl.innerHTML = _this.INFO.RESULT.Toolbar;

			var toolbar2El = document.getElementById('user-info-toolbar2-' + _this.USER_ID);
			toolbar2El.innerHTML = _this.INFO.RESULT.Toolbar2;
		}
	}

};
BX.CTooltip.prototype.StartTrackMouse = function(ob)
{
	var _this = this;

	if(!this.tracking)
	{
		var elCoords = jsUtils.GetRealPos(ob);
		this.CoordsLeft = elCoords.left + 0;
		this.CoordsTop = elCoords.top - 325;
		this.AnchorRight = elCoords.right;
		this.AnchorBottom = elCoords.bottom;

		this.tracking = 1;
		jsUtils.addEvent(document, "mousemove", _this.TrackMouse);
		setTimeout(function() {_this.tickTimer()}, 500);
	}
};

BX.CTooltip.prototype.StopTrackMouse = function()
{
	var _this = this;
	if(this.tracking)
	{
		jsUtils.removeEvent(document, "mousemove", _this.TrackMouse);
		this.active = false;
		setTimeout(function() {_this.HideTooltip()}, 500);
		this.tracking = false;
	}
};

BX.CTooltip.prototype.tickTimer = function()
{
	var _this = this;

	if(this.tracking)
	{
		this.tracking++;
		if(this.active)
		{
			if( (this.active.time + 5/*0.5sec*/)  <= this.tracking)
				this.ShowTooltip();
		}
		setTimeout(function() {_this.tickTimer()}, 100);
	}
};

BX.CTooltip.prototype.HideTooltip = function()
{
	if(!this.tracking)
		this.ShowOpacityEffect({func: this.SetInVisible, obj: this.DIV, arParams: []}, 1);
};

BX.CTooltip.prototype.ShowOpacityEffect = function(oCallback, bFade)
{
	var steps = 3;
	var period = 1;
	var delta = 1 / steps;
	var i = 0, op, _this = this;

	if(BX.browser.IsIE() && _this.DIV)
		_this.DIV.className = _this.classNameAnim;

	var show = function()
	{
		i++;
		if (i > steps)
		{
			clearInterval(intId);
			if (!oCallback.arParams)
				oCallback.arParams = [];
			if (oCallback.func && oCallback.obj)
				oCallback.func.apply(oCallback.obj, oCallback.arParams);
			return;
		}
		op = bFade ? 1 - i * delta : i * delta;

		if (_this.DIV != null)
		{
			try{
				_this.DIV.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + (op * 100) + ')';
				_this.DIV.style.opacity = op;
				_this.DIV.style.MozOpacity = op;
				_this.DIV.style.KhtmlOpacity = op;
			}
			catch(e){
			}
			finally{
				if (!bFade && i == 1)
					_this.DIV.style.display = 'block';

				if (bFade && i == steps && _this.DIV)
					_this.DIV.style.display = 'none';


				if (jsUtils.IsIE() && i == 1 && bFade && _this.IFRAME)
					_this.IFRAME.style.display = 'none';


				if (jsUtils.IsIE() && i == steps && _this.DIV)
				{
					if (!bFade)
						_this.IFRAME.style.display = 'block';

					_this.DIV.style.filter = _this.filterFixed;
					_this.DIV.className = _this.classNameFixed;
					_this.DIV.innerHTML = ''+_this.DIV.innerHTML;
				}
			}
		}

	};
	var intId = setInterval(show, period);

}

})(window);
/* End */
;
; /* Start:/bitrix/js/main/core/core_dd.js*/
(function(window){
BX.DD = function(params)
{
	return new BX.DD.dragdrop(params);
}

BX.DD.allowSelection = function()
{
	document.onmousedown = null;
	var b = document.body;
	b.ondrag = null;
	b.onselectstart = null;
	b.style.MozUserSelect = '';
	
	// if (jsDD.current_node)
	// {
		// jsDD.current_node.ondrag = null;
		// jsDD.current_node.onselectstart = null;
		// jsDD.current_node.style.MozUserSelect = '';
	// }
}
	
BX.DD.denySelection = function()
{
	document.onmousedown = BX.False;
	var b = document.body;
	b.ondrag = BX.False;
	b.onselectstart = BX.False;
	b.style.MozUserSelect = 'none';
	// if (jsDD.current_node) 
	// {
		// jsDD.current_node.ondrag = jsUtils.False;
		// jsDD.current_node.onselectstart = jsUtils.False;
		// jsDD.current_node.style.MozUserSelect = 'none';
	// }
}

BX.DD.dragdrop = function(params)
{


}

/*
 * BX.DD.dropFiles - for html5 drag and drop files
 *
 * example:
 *
 * BX(function() {
 *	  var dropBoxNode = BX('WebDAV23');
 *    var dropbox = new BX.DD.dropFiles(dropBoxNode);
 *    if (dropbox && dropbox.supported())
 *    {
 *        BX.addCustomEvent(dropbox, 'dropFiles', function(files) { WDUploadDroppedFiles(files);});
 *        BX.addCustomEvent(dropbox, 'dragEnter', function() {BX.addClass( dropBoxNode, 'droptarget');});
 *        BX.addCustomEvent(dropbox, 'dragLeave', function() {BX.removeClass( dropBoxNode, 'droptarget');});
 *    }
 * });
 *
 * to save files use BX.ajax.FormData
 */
BX.DD.dropFiles = function(div)
{
	if (BX.type.isElementNode(div)
		&& this.supported())
	{
		div.setAttribute('dropzone', 'copy f:*/*');
		this.DIV = div;
		this._timer = null;
		this._initEvents();

		this._cancelLeave = function()
		{
			if (this._timer != null)
			{
				clearTimeout(this._timer);
				this._timer = null;
			}
		}
		this._prepareLeave = function()
		{
			this._cancelLeave();
			this._timer = setTimeout( BX.delegate(function() {
				BX.onCustomEvent(this, 'dragLeave')
			}, this), 100);
		}

		return this;
	}
	return false;
}

BX.DD.dropFiles.prototype._initEvents = function()
{
	BX.bind(this.DIV, 'dragover', BX.proxy(this._dragOver, this));
	BX.bind(this.DIV, 'dragenter', BX.proxy(this._dragEnter, this));
	BX.bind(this.DIV, 'dragleave', BX.proxy(this._dragLeave, this));
	BX.bind(this.DIV, 'dragexit', BX.proxy(this._dragExit, this));
	BX.bind(this.DIV, 'drop', BX.proxy(this._drop, this));
}

BX.DD.dropFiles.prototype._dragEnter = function(e)
{
	BX.PreventDefault(e);
	this._cancelLeave();
	BX.onCustomEvent(this, 'dragEnter');
	return true;
}

BX.DD.dropFiles.prototype._dragExit = function(e)
{
	BX.PreventDefault(e);
	this._prepareLeave();
	return false;
}


BX.DD.dropFiles.prototype._dragLeave = function(e)
{
	BX.PreventDefault(e);
	this._prepareLeave();
	return false;
}

BX.DD.dropFiles.prototype._dragOver = function(e)
{
	BX.PreventDefault(e);
	this._cancelLeave();
	return true;
}

BX.DD.dropFiles.prototype._drop = function(e)
{
	BX.PreventDefault(e);
	var dt = e.dataTransfer;
	var files = dt.files;
	BX.onCustomEvent(this, 'dropFiles', [files]);
	BX.onCustomEvent(this, 'dragLeave')
	return false;
}

BX.DD.dropFiles.prototype.isEventSupported = function(event)
{
	var div = BX.create('DIV');
	var eventName = 'on'+event;
	var result = (eventName in div);
	
	if (!result && div.setAttribute && div.removeAttribute)
	{
		div.setAttribute(eventName, '');
		result = (typeof div[eventName] === 'function');
	}

	div = null;
	return result;
}

BX.DD.dropFiles.prototype.supported = function()
{
	return ( (!!window.FileReader) && this.isEventSupported('dragstart') && this.isEventSupported('drop') );
}

})(window)

/* End */
;
; /* Start:/bitrix/js/main/core/core_autosave.js*/
(function(window){
if (BX.CAutoSave && top.BX.CAutoSave) return;
/******************************* AUTOSAVE *********************************/

BX.CAutoSave = function(params)
{
	this.FORM_NAME = params.form;
	this.FORM_MARKER = params.form_marker;
	this.FORM_ID = params.form_id;

	this.PERIOD = params.period || [4001, 20990];

	this.RESTORE_DATA = null;
	this.TIMERS = [null, null];

	this.bInited = false;
	this.bRestoreInProgress = false;

	this.DISABLE_STANDARD_NOTIFY = params.DISABLE_STANDARD_NOTIFY;
	this.NOTIFY_CONTEXT = null;

	BX.ready(BX.defer(this.Prepare, this));
	BX.garbage(BX.delegate(this.Clear, this));
};

BX.CAutoSave.prototype.Prepare = function()
{
	var i;

	if (this.FORM_NAME && BX.type.isString(this.FORM_NAME))
		this.FORM = document.forms[this.FORM_NAME];
	else if (this.FORM_MARKER && BX.type.isString(this.FORM_MARKER))
		this.FORM = (BX(this.FORM_MARKER)||{form:null}).form;

	if (!BX.type.isDomNode(this.FORM))
		return;

	this.FORM.BXAUTOSAVE = this;
	BX.bind(this.FORM, 'submit', BX.proxy(this.ClearTimers, this));

	for (i=0; i<this.FORM.elements.length; i++)
	{
		this.RegisterInput(this.FORM.elements[i]);
	}

	setTimeout(BX.delegate(this._PrepareAfter, this), 10);
};

BX.CAutoSave.prototype.RegisterInput = function(inp)
{
	if (BX.type.isString(inp))
	{
		setTimeout(BX.delegate(function(){this.RegisterInput(this.FORM[inp] || BX(inp))}, this), 10);
	}
	else if (BX.type.isDomNode(inp))
	{
		if (
			inp.type != 'button'
			&& inp.type != 'submit'
			&& inp.type != 'reset'
			&& inp.type != 'image'
			&& inp.type != 'hidden'
		)
		{
			BX.bind(inp, 'change', BX.proxy(this.Init, this));

			if (inp.type == 'text' || inp.type == 'textarea')
			{
				BX.bind(inp, 'keyup', BX.proxy(this.Init, this));
			}

			if (inp.type == 'checkbox' || inp.type == 'radio')
			{
				BX.bind(inp, 'click', BX.proxy(this.Init, this));
			}
		}
	}
};

BX.CAutoSave.prototype.UnRegisterInput = function(inp)
{
	if (BX.type.isString(inp))
		inp = this.FORM[inp] || BX(inp);
	if (BX.type.isDomNode(inp))
	{
		BX.unbind(inp, 'change', BX.proxy(this.Init, this));
		BX.unbind(inp, 'keyup', BX.proxy(this.Init, this));
		BX.unbind(inp, 'click', BX.proxy(this.Init, this));
	}
};

BX.CAutoSave.prototype._PrepareAfter = function()
{
	// we can set other "target events" here
	BX.onCustomEvent(this.FORM, 'onAutoSavePrepare', [this, BX.proxy(this.Init, this)]);

	if (this.RESTORE_DATA)
	{
		var id = this.FORM.name || Math.random();
		BX.addCustomEvent('onExtAutoSaveRestoreClick_' + id, BX.proxy(this.Restore, this));

		var o = this._NotifyContext();
		if (o)
		{
			o.Notify(BX.message('AUTOSAVE') + ' <a href="javascript:void(0)" onclick="BX.CAutoSave.Restore(\'' + BX.util.urlencode(id) + '\', this); return false;">' + BX.message('AUTOSAVE_R') + '</a>');
		}

		// may be useful sometimes
		BX.onCustomEvent(this.FORM, 'onAutoSaveRestoreFound', [this, this.RESTORE_DATA]);
	}
};

BX.CAutoSave.prototype.Init = function()
{
	// if (this.bRestoreInProgress)
		// return;

	if (this.TIMERS[0])
	{
		clearTimeout(this.TIMERS[0]);
		this.TIMERS[0] = null;
	}

	this.TIMERS[0] = setTimeout(BX.proxy(this.TimerHandler, this), this.PERIOD[0]);

	if (!this.TIMERS[1])
	{
		this.TIMERS[1] = setInterval(BX.proxy(this.Save, this), this.PERIOD[1]);
	}

	// may also be useful
	BX.onCustomEvent(this.FORM, 'onAutoSaveInit', [this]);
};

BX.CAutoSave.prototype.TimerHandler = function()
{
	if (this.TIMERS[1])
	{
		clearInterval(this.TIMERS[1]);
		this.TIMERS[1] = null;
	}
	this.Save();
};

BX.CAutoSave.prototype.Save = function()
{
	if (this.FORM && BX.isNodeInDom(this.FORM))
	{
		var i, j, el, data = {autosave_id: this.FORM_ID, form_data: {}};

		for (i=0; i<this.FORM.elements.length; i++)
		{
			el = this.FORM.elements[i];

			if (el.name && el.name != 'sessid' && el.name != 'lang' && el.name != 'autosave_id')
			{
				var n = el.name, v = '', t = el.type.toLowerCase();

				switch (t)
				{
					case 'button':
					case 'submit':
					case 'reset':
					case 'image':
					case 'file':
					case 'password':
						break;

					case 'radio':
					case 'checkbox':
						if (el.checked)
							v = el.value || 'on';
					break;

					case 'select-multiple':
						n = n.substring(0, n.length-2);
						v = [];
						for (j=0;j<el.options.length;j++)
						{
							if (el.options[j].selected)
							{
								v.push(el.options[j].value);
							}
						}
					break;

					default:
						v = el.value;
				}

				if (n.indexOf('[]') > 0)
				{
					n = _encodeName(n);
					if (typeof(data.form_data[n]) == 'undefined')
						data.form_data[n] = [v];
					else
						data.form_data[n].push(v);
				}
				else
					data.form_data[_encodeName(n)] = v;
			}
		}

		// we can adjust form_data before autosaving
		BX.onCustomEvent(this.FORM, 'onAutoSave', [this, data.form_data]);
		BX.ajax.post(
			'/bitrix/tools/autosave.php?bxsender=core_autosave&sessid=' + BX.bitrix_sessid(), data, BX.proxy(this._Save, this)
		);
	}
	else
	{
		this.Clear();
	}
};

BX.CAutoSave.prototype._Save = function(data)
{
	BX.onCustomEvent(this.FORM, 'onAutoSaveFinished', [this, data]);
};

BX.CAutoSave.prototype.Restore = function(data, clicker)
{
	if (data)
	{
		this.RESTORE_DATA = _decodeData(data);
	}
	else if (this.FORM && this.RESTORE_DATA)
	{
		// we can change restore data or make some unusual actions here
		BX.onCustomEvent(this.FORM, 'onAutoSaveRestore', [this, this.RESTORE_DATA]);

		this.bRestoreInProgress = true;

		for (var i=0; i<this.FORM.elements.length; i++)
		{
			var el = this.FORM.elements[i];
			if (el && BX.type.isDomNode(el) && el.name)
			{
				var value = undefined, n = el.name;

				if (el.type == 'select-multiple')
					n = el.name.substring(0, el.name.length-2);

				value = this.RESTORE_DATA[n];

				if (n.indexOf('[]') > 0 && BX.type.isArray(value))
					value = this.RESTORE_DATA[n].shift();

				if (el.type != 'checkbox' && typeof value == 'undefined')
					continue;

				var bChange = false;

				switch(el.type)
				{
					case 'radio':
						if (!el.checked && !!(value == el.value))
						{
							bChange = true;
							BX.fireEvent(el, 'click');
						}
					break;
					case 'checkbox':
						if (el.checked != !!(value == el.value))
						{
							bChange = true;
							BX.fireEvent(el, 'click');
						}
					break;

					case 'select-one':
						for (var j = 0; j < el.options.length; j++)
						{
							var q = el.options[j].selected;
							el.options[j].selected = !!(value == el.options[j].value);
							bChange |= el.options[j].selected != q;
						}

						break;

					case 'select-multiple':
						value = this.RESTORE_DATA[el.name.substring(0, el.name.length-2)];
						for (j = 0; j < el.options.length; j++)
						{
							q = el.options[j].selected;
							el.options[j].selected = !!(BX.type.isArray(value) && BX.util.in_array(el.options[j].value, value));
							bChange |= el.options[j].selected != q;
						}
						break;

					case 'file':
					case 'button':
					case 'image':
					case 'submit':
					case 'reset':
					case 'password':
						break;

					default:
						bChange = value != el.value;
						el.value = value;
				}

				if (bChange)
					BX.fireEvent(el, 'change');
			}
		}

		var o = this._NotifyContext();
		if (o)
			o.hideNotify(clicker.parentNode.parentNode);

		this.bRestoreInProgress = false;

		BX.onCustomEvent(this.FORM, 'onAutoSaveRestoreFinished', [this, this.RESTORE_DATA]);
	}
};

BX.CAutoSave.prototype._NotifyContext = function()
{
	var o = null;

	if (!this.DISABLE_STANDARD_NOTIFY)
	{
		if (this.NOTIFY_CONTEXT)
			o = this.NOTIFY_CONTEXT;
		else if (BX.WindowManager && BX.WindowManager.Get())
			o = BX.WindowManager.Get();
		else if (BX.adminPanel)
			o = BX.adminPanel;
		else if (BX.admin && BX.admin.panel)
			o = BX.admin.panel;

		this.NOTIFY_CONTEXT = o;
	}

	return o;
};

BX.CAutoSave.prototype.ClearTimers = function()
{
	if (this.TIMERS)
	{
		clearTimeout(this.TIMERS[0]);
		clearInterval(this.TIMERS[1]);
	}
};

BX.CAutoSave.prototype.Clear = function()
{
	if (this.FORM)
	{
		this.FORM.BXAUTOSAVE = null;

		for (var i=0; i<this.FORM.elements.length; i++)
		{
			this.UnRegisterInput(this.FORM.elements[i]);
		}
	}

	this.ClearTimers();

	// we should unset any additional "target events" here
	BX.onCustomEvent(this.FORM, 'onAutoSaveClear', [this]);

	this.FORM = null;
	this.TIMERS = null;
};

BX.CAutoSave.Restore = function(id, el)
{
	BX.onCustomEvent('onExtAutoSaveRestoreClick_' + id, [null, el]);
};

function _encodeName(n)
{
	var q;
	while (q = /[^a-zA-Z0-9_\-]/.exec(n))
	{
		n = n.replace(q[0], 'X' + BX.util.str_pad_left(q[0].charCodeAt(0).toString(), 6, '0') + 'X');
	}
	return n;
}

function _decodeName(n)
{
	var q;
	while (q = /X[\d]{6}X/.exec(n))
	{
		n = n.replace(q[0], String.fromCharCode(parseInt(q[0].replace(/(^X[0]*)|(X$)/g, ''))))
	}
	return n;
}

function _decodeData(data)
{
	var d = {};
	for (var i in data)
	{
		d[_decodeName(i)] = data[i];
	}
	return d;
}
	top.BX.CAutoSave = BX.CAutoSave;
})(window);

/* End */
;
; /* Start:/bitrix/js/main/core/core_popup.js*/
;(function(window) {

if (BX.PopupWindowManager)
	return;

BX.PopupWindowManager =
{
	_popups : [],
	_currentPopup : null,

	create : function(uniquePopupId, bindElement, params)
	{
		var index = -1;
		if ( (index = this._getPopupIndex(uniquePopupId)) !== -1)
			return this._popups[index];

		var popupWindow = new BX.PopupWindow(uniquePopupId, bindElement, params);

		BX.addCustomEvent(popupWindow, "onPopupShow", BX.delegate(this.onPopupShow, this));
		BX.addCustomEvent(popupWindow, "onPopupClose", BX.delegate(this.onPopupClose, this));
		BX.addCustomEvent(popupWindow, "onPopupDestroy", BX.delegate(this.onPopupDestroy, this));

		this._popups.push(popupWindow);

		return popupWindow;
	},

	onPopupShow : function(popupWindow)
	{
		if (this._currentPopup !== null)
			this._currentPopup.close();

		this._currentPopup = popupWindow;
	},

	onPopupClose : function(popupWindow)
	{
		this._currentPopup = null;
	},

	onPopupDestroy : function(popupWindow)
	{
		var index = -1;
		if ( (index = this._getPopupIndex(popupWindow.uniquePopupId)) !== -1)
			this._popups = BX.util.deleteFromArray(this._popups, index);
	},

	getCurrentPopup : function()
	{
		return this._currentPopup;
	},

	isPopupExists : function(uniquePopupId)
	{
		return this._getPopupIndex(uniquePopupId) !== -1
	},

	_getPopupIndex : function(uniquePopupId)
	{
		var index = -1;

		for (var i = 0; i < this._popups.length; i++)
			if (this._popups[i].uniquePopupId == uniquePopupId)
				return i;

		return index;
	}
};

BX.PopupWindow = function(uniquePopupId, bindElement, params)
{
	BX.onCustomEvent("onPopupWindowInit", [uniquePopupId, bindElement, params ]);

	this.uniquePopupId = uniquePopupId;
	this.params = params || {};
	this.params.zIndex = parseInt(this.params.zIndex);
	this.params.zIndex = isNaN(this.params.zIndex) ? 0 : this.params.zIndex;
	this.buttons = this.params.buttons && BX.type.isArray(this.params.buttons) ? this.params.buttons : [];
	this.offsetTop = BX.PopupWindow.getOption("offsetTop");
	this.offsetLeft = BX.PopupWindow.getOption("offsetLeft");
	this.firstShow = false;
	this.bordersWidth = 20;
	this.bindElementPos = null;
	this.closeIcon = null;
	this.angle = null;
	this.overlay = null;
	this.titleBar = null;
	this.bindOptions = typeof(this.params.bindOptions) == "object" ? this.params.bindOptions : {};
	this.isAutoHideBinded = false;
	this.closeByEsc = !!this.params.closeByEsc;
	this.isCloseByEscBinded = false;

	this.dragged = false;
	this.dragPageX = 0;
	this.dragPageY = 0;

	if (this.params.events)
	{
		for (var eventName in this.params.events)
			BX.addCustomEvent(this, eventName, this.params.events[eventName]);
	}

	this.popupContainer = document.createElement("DIV");

	BX.adjust(this.popupContainer, {
		props : {
			id : uniquePopupId
		},
		style : {
			zIndex: this.getZindex(),
			position: "absolute",
			display: "none",
			top: "0px",
			left: "0px"
		}
	});

	var tableClassName = "popup-window";
	if (params.lightShadow)
		tableClassName += " popup-window-light";
	if (params.titleBar)
		tableClassName += params.lightShadow ? " popup-window-titlebar-light" : " popup-window-titlebar";
	if (params.className && BX.type.isNotEmptyString(params.className))
		tableClassName += " " + params.className;

	this.popupContainer.innerHTML = ['<table class="', tableClassName,'" cellspacing="0"> \
		<tr class="popup-window-top-row"> \
			<td class="popup-window-left-column"><div class="popup-window-left-spacer"></div></td> \
			<td class="popup-window-center-column">', (params.titleBar ? '<div class="popup-window-titlebar" id="popup-window-titlebar-' + uniquePopupId + '"></div>' : ""),'</td> \
			<td class="popup-window-right-column"><div class="popup-window-right-spacer"></div></td> \
		</tr> \
		<tr class="popup-window-content-row"> \
			<td class="popup-window-left-column"></td> \
			<td class="popup-window-center-column"><div class="popup-window-content" id="popup-window-content-', uniquePopupId ,'"> \
			</div></td> \
			<td class="popup-window-right-column"></td> \
		</tr> \
		<tr class="popup-window-bottom-row"> \
			<td class="popup-window-left-column"></td> \
			<td class="popup-window-center-column"></td> \
			<td class="popup-window-right-column"></td> \
		</tr> \
	</table>'].join("");
	document.body.appendChild(this.popupContainer);

	if (params.closeIcon)
	{
		this.popupContainer.appendChild(
			(this.closeIcon = BX.create("a", {
				props : { className: "popup-window-close-icon" + (params.titleBar ? " popup-window-titlebar-close-icon" : ""), href : ""},
				style : (typeof(params.closeIcon) == "object" ? params.closeIcon : {} ),
				events : { click : BX.proxy(this._onCloseIconClick, this) } } )
			)
		);

		if (BX.browser.IsIE())
			BX.adjust(this.closeIcon, { attrs: { hidefocus: "true" } });
	}

	this.contentContainer = BX("popup-window-content-" +  uniquePopupId);
	this.titleBar = BX("popup-window-titlebar-" +  uniquePopupId);
	this.buttonsContainer = this.buttonsHr = null;

	if (params.angle)
		this.setAngle(params.angle);

	if (params.overlay)
		this.setOverlay(params.overlay);

	this.setOffset(this.params);
	this.setBindElement(bindElement);
	this.setTitleBar(this.params.titleBar);
	this.setContent(this.params.content);
	this.setButtons(this.params.buttons);

	if (this.params.bindOnResize !== false)
	{
		BX.bind(window, "resize", BX.proxy(this._onResizeWindow, this));
	}
};

BX.PopupWindow.prototype.setContent = function(content)
{
	if (!this.contentContainer || !content)
		return;

	if (BX.type.isElementNode(content))
	{
		BX.cleanNode(this.contentContainer);
		this.contentContainer.appendChild(content.parentNode ? content.parentNode.removeChild(content) : content );
		content.style.display = "block";
	}
	else if (BX.type.isString(content))
	{
		this.contentContainer.innerHTML = content;
	}
	else
		this.contentContainer.innerHTML = "&nbsp;";

};

BX.PopupWindow.prototype.setButtons = function(buttons)
{
	this.buttons = buttons && BX.type.isArray(buttons) ? buttons : [];

	if (this.buttonsHr)
		BX.remove(this.buttonsHr);
	if (this.buttonsContainer)
		BX.remove(this.buttonsContainer);

	if (this.buttons.length > 0 && this.contentContainer)
	{
		var newButtons = [];
		for (var i = 0; i < this.buttons.length; i++)
		{
			var button = this.buttons[i];
			if (button == null || !BX.is_subclass_of(button, BX.PopupWindowButton))
				continue;

			button.popupWindow = this;
			newButtons.push(button.render());
		}

		this.buttonsHr = this.contentContainer.parentNode.appendChild(
			BX.create("div",{
				props : { className : "popup-window-hr popup-window-buttons-hr" },
				children : [ BX.create("i", {}) ]
			})
		);

		this.buttonsContainer = this.contentContainer.parentNode.appendChild(
			BX.create("div",{
				props : { className : "popup-window-buttons" },
				children : newButtons
			})
		);
	}
};

BX.PopupWindow.prototype.setBindElement = function(bindElement)
{
	if (!bindElement || typeof(bindElement) != "object")
		return;

	if (BX.type.isDomNode(bindElement) || (BX.type.isNumber(bindElement.top) && BX.type.isNumber(bindElement.left)))
		this.bindElement = bindElement;
	else if (BX.type.isNumber(bindElement.clientX) && BX.type.isNumber(bindElement.clientY))
	{
		BX.fixEventPageXY(bindElement);
		this.bindElement = { left : bindElement.pageX, top : bindElement.pageY, bottom : bindElement.pageY };
	}
};

BX.PopupWindow.prototype.getBindElementPos = function(bindElement)
{
	if (BX.type.isDomNode(bindElement))
	{
		return BX.pos(bindElement, false);
	}
	else if (bindElement && typeof(bindElement) == "object")
	{
		if (!BX.type.isNumber(bindElement.bottom))
			bindElement.bottom = bindElement.top;
		return bindElement;
	}
	else
	{
		var windowSize =  BX.GetWindowInnerSize();
		var windowScroll = BX.GetWindowScrollPos();
		var popupWidth = this.popupContainer.offsetWidth;
		var popupHeight = this.popupContainer.offsetHeight;

		return {
			left : windowSize.innerWidth/2 - popupWidth/2 + windowScroll.scrollLeft,
			top : windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop,
			bottom : windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop,

			//for optimisation purposes
			windowSize : windowSize,
			windowScroll : windowScroll,
			popupWidth : popupWidth,
			popupHeight : popupHeight
		};
	}
};

BX.PopupWindow.prototype.setAngle = function(params)
{
	var className = this.params.lightShadow ? "popup-window-light-angly" : "popup-window-angly";
	if (this.angle == null)
	{
		var position = this.bindOptions.position && this.bindOptions.position == "top" ? "bottom" : "top";
		var angleMinLeft = BX.PopupWindow.getOption(position == "top" ? "angleMinTop" : "angleMinBottom");
		var defaultOffset = BX.type.isNumber(params.offset) ? params.offset : 0;

		var angleLeftOffset = BX.PopupWindow.getOption("angleLeftOffset", null);
		if (defaultOffset > 0 && BX.type.isNumber(angleLeftOffset))
			defaultOffset += angleLeftOffset - BX.PopupWindow.defaultOptions.angleLeftOffset;

		this.angle = {
			element : BX.create("div", { props : { className: className + " " + className +"-" + position }}),
			position : position,
			offset : 0,
			defaultOffset : Math.max(defaultOffset, angleMinLeft)
			//Math.max(BX.type.isNumber(params.offset) ? params.offset : 0, angleMinLeft)
		};
		this.popupContainer.appendChild(this.angle.element);
	}

	if (typeof(params) == "object" && params.position && BX.util.in_array(params.position, ["top", "right", "bottom", "left", "hide"]))
	{
		BX.removeClass(this.angle.element, className + "-" +  this.angle.position);
		BX.addClass(this.angle.element, className + "-" +  params.position);
		this.angle.position = params.position;
	}

	if (typeof(params) == "object" && BX.type.isNumber(params.offset))
	{
		var offset = params.offset;
		var minOffset, maxOffset;
		if (this.angle.position == "top")
		{
			minOffset = BX.PopupWindow.getOption("angleMinTop");
			maxOffset = this.popupContainer.offsetWidth - BX.PopupWindow.getOption("angleMaxTop");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.left = this.angle.offset + "px";
			this.angle.element.style.marginLeft = "auto";
		}
		else if (this.angle.position == "bottom")
		{
			minOffset = BX.PopupWindow.getOption("angleMinBottom");
			maxOffset = this.popupContainer.offsetWidth - BX.PopupWindow.getOption("angleMaxBottom");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.marginLeft = this.angle.offset + "px";
			this.angle.element.style.left = "auto";
		}
		else if (this.angle.position == "right")
		{
			minOffset = BX.PopupWindow.getOption("angleMinRight");
			maxOffset = this.popupContainer.offsetHeight - BX.PopupWindow.getOption("angleMaxRight");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.top = this.angle.offset + "px";
		}
		else if (this.angle.position == "left")
		{
			minOffset = BX.PopupWindow.getOption("angleMinLeft");
			maxOffset = this.popupContainer.offsetHeight - BX.PopupWindow.getOption("angleMaxLeft");
			maxOffset = maxOffset < minOffset ? Math.max(minOffset, offset) : maxOffset;

			this.angle.offset = Math.min(Math.max(minOffset, offset), maxOffset);
			this.angle.element.style.top = this.angle.offset + "px";
		}
	}
};

BX.PopupWindow.prototype.isTopAngle = function()
{
	return this.angle != null && this.angle.position == "top";
};

BX.PopupWindow.prototype.isBottomAngle = function()
{
	return this.angle != null && this.angle.position == "bottom";
};

BX.PopupWindow.prototype.isTopOrBottomAngle = function()
{
	return this.angle != null && BX.util.in_array(this.angle.position, ["top", "bottom"]);
};

BX.PopupWindow.prototype.getAngleHeight = function()
{
	return (this.isTopOrBottomAngle() ? BX.PopupWindow.getOption("angleTopOffset") : 0);
};

BX.PopupWindow.prototype.setOffset = function(params)
{
	if (typeof(params) != "object")
		return;

	if (params.offsetLeft && BX.type.isNumber(params.offsetLeft))
		this.offsetLeft = params.offsetLeft + BX.PopupWindow.getOption("offsetLeft");

	if (params.offsetTop && BX.type.isNumber(params.offsetTop))
		this.offsetTop = params.offsetTop + BX.PopupWindow.getOption("offsetTop");
};

BX.PopupWindow.prototype.setTitleBar = function(params)
{
	if (!this.titleBar || typeof(params) != "object" || !BX.type.isDomNode(params.content))
		return;

	this.titleBar.innerHTML = "";
	this.titleBar.appendChild(params.content);

	if (this.params.draggable)
	{
		this.titleBar.parentNode.style.cursor = "move";
		BX.bind(this.titleBar.parentNode, "mousedown", BX.proxy(this._startDrag, this));
	}
};

BX.PopupWindow.prototype.setClosingByEsc = function(enable)
{
	enable = !!enable;
	if (enable)
	{
		this.closeByEsc = true;
		if (!this.isCloseByEscBinded)
		{
			BX.bind(document, "keyup", BX.proxy(this._onKeyUp, this));
			this.isCloseByEscBinded = true;
		}
	}
	else
	{
		this.closeByEsc = false;
		if (this.isCloseByEscBinded)
		{
			BX.unbind(document, "keyup", BX.proxy(this._onKeyUp, this));
			this.isCloseByEscBinded = false;
		}
	}
};

BX.PopupWindow.prototype.setOverlay = function(params)
{
	if (this.overlay == null)
	{
		this.overlay = {
			element : BX.create("div", { props : { className: "popup-window-overlay", id : "popup-window-overlay-" + this.uniquePopupId } })
		};

		this.adjustOverlayZindex();
		this.resizeOverlay();
		document.body.appendChild(this.overlay.element);
	}

	if (params && params.opacity && BX.type.isNumber(params.opacity) && params.opacity >= 0 && params.opacity <= 100)
	{
		if (BX.browser.IsIE() && !BX.browser.IsIE9())
			this.overlay.element.style.filter =  "alpha(opacity=" + params.opacity +")";
		else
		{
			this.overlay.element.style.filter = "none";
			this.overlay.element.style.opacity = parseFloat(params.opacity/100).toPrecision(3);
		}
	}

	if (params && params.backgroundColor)
		this.overlay.element.style.backgroundColor = params.backgroundColor;
};

BX.PopupWindow.prototype.removeOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
		BX.remove(this.overlay.element);

	this.overlay = null;
};

BX.PopupWindow.prototype.hideOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
		this.overlay.element.style.display = "none";
};

BX.PopupWindow.prototype.showOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
		this.overlay.element.style.display = "block";
};

BX.PopupWindow.prototype.resizeOverlay = function()
{
	if (this.overlay != null && this.overlay.element != null)
	{
		var windowSize = BX.GetWindowScrollSize();
		this.overlay.element.style.width = windowSize.scrollWidth + "px";
		this.overlay.element.style.height = windowSize.scrollHeight + "px";
	}
};

BX.PopupWindow.prototype.getZindex = function()
{
	if (this.overlay != null)
		return BX.PopupWindow.getOption("popupOverlayZindex") + this.params.zIndex;
	else
		return BX.PopupWindow.getOption("popupZindex") + this.params.zIndex;
};


BX.PopupWindow.prototype.adjustOverlayZindex = function()
{
	if (this.overlay != null && this.overlay.element != null)
	{
		this.overlay.element.style.zIndex = parseInt(this.popupContainer.style.zIndex) - 1;
	}
};


BX.PopupWindow.prototype.show = function()
{
	if (!this.firstShow)
	{
		BX.onCustomEvent(this, "onPopupFirstShow", [this]);
		this.firstShow = true;
	}
	BX.onCustomEvent(this, "onPopupShow", [this]);

	this.showOverlay();
	this.popupContainer.style.display = "block";

	this.adjustPosition();

	BX.onCustomEvent(this, "onAfterPopupShow", [this]);

	if (this.closeByEsc && !this.isCloseByEscBinded)
	{
		BX.bind(document, "keyup", BX.proxy(this._onKeyUp, this));
		this.isCloseByEscBinded = true;
	}

	if (this.params.autoHide && !this.isAutoHideBinded)
	{
		setTimeout(
			BX.proxy(function() {
				this.isAutoHideBinded = true;
				BX.bind(this.popupContainer, "click", this.cancelBubble);
				BX.bind(document, "click", BX.proxy(this.close, this));
			}, this), 0
		);
	}
};

BX.PopupWindow.prototype.isShown = function()
{
   return this.popupContainer.style.display == "block";
};

BX.PopupWindow.prototype.cancelBubble = function(event)
{
	if(!event)
		event = window.event;

	if (event.stopPropagation)
		event.stopPropagation();
	else
		event.cancelBubble = true;
};

BX.PopupWindow.prototype.close = function(event)
{
	if (!this.isShown())
		return;

	if (event && !(BX.getEventButton(event)&BX.MSLEFT))
		return true;

	BX.onCustomEvent(this, "onPopupClose", [this, event]);

	this.hideOverlay();
	this.popupContainer.style.display = "none";

	if (this.isCloseByEscBinded)
	{
		BX.unbind(document, "keyup", BX.proxy(this._onKeyUp, this));
		this.isCloseByEscBinded = false;
	}

	setTimeout(BX.proxy(this._close, this), 0);
};

BX.PopupWindow.prototype._close = function()
{
	if (this.params.autoHide && this.isAutoHideBinded)
	{
		this.isAutoHideBinded = false;
		BX.unbind(this.popupContainer, "click", this.cancelBubble);
		BX.unbind(document, "click", BX.proxy(this.close, this));
	}
};

BX.PopupWindow.prototype._onCloseIconClick = function(event)
{
	event = event || window.event;
	this.close(event);
	BX.PreventDefault(event);
};

BX.PopupWindow.prototype._onKeyUp = function(event)
{
	event = event || window.event;
	if (event.keyCode == 27)
	{
		_checkEscPressed(this.getZindex(), BX.proxy(this.close, this));
	}
};

BX.PopupWindow.prototype.destroy = function()
{
	BX.onCustomEvent(this, "onPopupDestroy", [this]);
	BX.unbindAll(this);
	BX.unbind(document, "keyup", BX.proxy(this._onKeyUp, this));
	BX.unbind(document, "click", BX.proxy(this.close, this));
	BX.unbind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this._stopDrag, this));
	BX.unbind(window, "resize", BX.proxy(this._onResizeWindow, this));
	BX.remove(this.popupContainer);
	this.removeOverlay();
};

BX.PopupWindow.prototype.adjustPosition = function(bindOptions)
{
	if (bindOptions && typeof(bindOptions) == "object")
		this.bindOptions = bindOptions;

	var bindElementPos = this.getBindElementPos(this.bindElement);

	if (!this.bindOptions.forceBindPosition && this.bindElementPos != null &&
		 bindElementPos.top == this.bindElementPos.top &&
		 bindElementPos.left == this.bindElementPos.left
	)
		return;

	this.bindElementPos = bindElementPos;

	var windowSize = bindElementPos.windowSize ? bindElementPos.windowSize : BX.GetWindowInnerSize();
	var windowScroll = bindElementPos.windowScroll ? bindElementPos.windowScroll : BX.GetWindowScrollPos();
	var popupWidth = bindElementPos.popupWidth ? bindElementPos.popupWidth : this.popupContainer.offsetWidth;
	var popupHeight = bindElementPos.popupHeight ? bindElementPos.popupHeight : this.popupContainer.offsetHeight;

	var angleTopOffset = BX.PopupWindow.getOption("angleTopOffset");

	var left = this.bindElementPos.left + this.offsetLeft -
				(this.isTopOrBottomAngle() ? BX.PopupWindow.getOption("angleLeftOffset") : 0);

	if ( !this.bindOptions.forceLeft &&
		(left + popupWidth + this.bordersWidth) >= (windowSize.innerWidth + windowScroll.scrollLeft) &&
		(windowSize.innerWidth + windowScroll.scrollLeft - popupWidth - this.bordersWidth) > 0)
	{
			var bindLeft = left;
			left = windowSize.innerWidth + windowScroll.scrollLeft - popupWidth - this.bordersWidth;
			if (this.isTopOrBottomAngle())
			{
				this.setAngle({ offset : bindLeft - left + this.angle.defaultOffset});
			}
	}
	else if (this.isTopOrBottomAngle())
	{
		this.setAngle({ offset : this.angle.defaultOffset + (left < 0 ? left : 0) });
	}

	if (left < 0)
		left = 0;

	var top = 0;

	if (this.bindOptions.position && this.bindOptions.position == "top")
	{
		top = this.bindElementPos.top - popupHeight - this.offsetTop - (this.isBottomAngle() ? angleTopOffset : 0);
		if (top < 0 || (!this.bindOptions.forceTop && top < windowScroll.scrollTop))
		{
			top = this.bindElementPos.bottom + this.offsetTop;
			if (this.angle != null)
			{
				top += angleTopOffset;
				this.setAngle({ position: "top"});
			}
		}
		else if (this.isTopAngle())
		{
			top = top - angleTopOffset + BX.PopupWindow.getOption("positionTopXOffset");
			this.setAngle({ position: "bottom"});
		}
		else
		{
			top += BX.PopupWindow.getOption("positionTopXOffset");
		}
	}
	else
	{
		top = this.bindElementPos.bottom + this.offsetTop + this.getAngleHeight();

		if ( !this.bindOptions.forceTop &&
			(top + popupHeight) > (windowSize.innerHeight + windowScroll.scrollTop) &&
			(this.bindElementPos.top - popupHeight - this.getAngleHeight()) >= 0) //Can we place the PopupWindow above the bindElement?
		{
			//The PopupWindow doesn't place below the bindElement. We should place it above.
			top = this.bindElementPos.top - popupHeight;
			if (this.isTopOrBottomAngle())
			{
				top -= angleTopOffset;
				this.setAngle({ position: "bottom"});
			}

			top += BX.PopupWindow.getOption("positionTopXOffset");
		}
		else if (this.isBottomAngle())
		{
			top += angleTopOffset;
			this.setAngle({ position: "top"});
		}
	}

	if (top < 0)
		top = 0;

	BX.adjust(this.popupContainer, { style: {
		top: top + "px",
		left: left + "px",
		zIndex: this.getZindex()
	}});

	this.adjustOverlayZindex();
};

BX.PopupWindow.prototype._onResizeWindow = function(event)
{
	if (this.isShown())
	{
		this.adjustPosition();
		if (this.overlay != null)
			this.resizeOverlay();
	}
};

BX.PopupWindow.prototype.move = function(offsetX, offsetY)
{
	var left = parseInt(this.popupContainer.style.left) + offsetX;
	var top = parseInt(this.popupContainer.style.top) + offsetY;

	if (typeof(this.params.draggable) == "object" && this.params.draggable.restrict)
	{
		//Left side
		if (left < 0)
			left = 0;

		//Right side
		var scrollSize = BX.GetWindowScrollSize();
		var floatWidth = this.popupContainer.offsetWidth;
		var floatHeight = this.popupContainer.offsetHeight;

		if (left > (scrollSize.scrollWidth - floatWidth))
			left = scrollSize.scrollWidth - floatWidth;

		if (top > (scrollSize.scrollHeight - floatHeight))
			top = scrollSize.scrollHeight - floatHeight;

		//Top side
		if (top < 0)
			top = 0;
	}

	this.popupContainer.style.left = left + "px";
	this.popupContainer.style.top = top + "px";
};

BX.PopupWindow.prototype._startDrag = function(event)
{
	event = event || window.event;
	BX.fixEventPageXY(event);

	this.dragPageX = event.pageX;
	this.dragPageY = event.pageY;
	this.dragged = false;

	BX.bind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.bind(document, "mouseup", BX.proxy(this._stopDrag, this));

	if (document.body.setCapture)
		document.body.setCapture();

	//document.onmousedown = BX.False;
	document.body.ondrag = BX.False;
	document.body.onselectstart = BX.False;
	document.body.style.cursor = "move";
	document.body.style.MozUserSelect = "none";
	this.popupContainer.style.MozUserSelect = "none";

	return BX.PreventDefault(event);
};

BX.PopupWindow.prototype._moveDrag = function(event)
{
	event = event || window.event;
	BX.fixEventPageXY(event);

	if(this.dragPageX == event.pageX && this.dragPageY == event.pageY)
		return;

	this.move((event.pageX - this.dragPageX), (event.pageY - this.dragPageY));
	this.dragPageX = event.pageX;
	this.dragPageY = event.pageY;

	if (!this.dragged)
	{
		BX.onCustomEvent(this, "onPopupDragStart");
		this.dragged = true;
	}

	BX.onCustomEvent(this, "onPopupDrag");
};

BX.PopupWindow.prototype._stopDrag = function(event)
{
	if(document.body.releaseCapture)
		document.body.releaseCapture();

	BX.unbind(document, "mousemove", BX.proxy(this._moveDrag, this));
	BX.unbind(document, "mouseup", BX.proxy(this._stopDrag, this));

	//document.onmousedown = null;
	document.body.ondrag = null;
	document.body.onselectstart = null;
	document.body.style.cursor = "";
	document.body.style.MozUserSelect = "";
	this.popupContainer.style.MozUserSelect = "";

	BX.onCustomEvent(this, "onPopupDragEnd");
	this.dragged = false;

	return BX.PreventDefault(event);
};

BX.PopupWindow.options = {};
BX.PopupWindow.defaultOptions = {

	angleLeftOffset : 15,

	positionTopXOffset : 0,
	angleTopOffset : 8,

	popupZindex : 1000,
	popupOverlayZindex : 1100,

	angleMinLeft : 10,
	angleMaxLeft : 10,

	angleMinRight : 10,
	angleMaxRight : 10,

	angleMinBottom : 7,
	angleMaxBottom : 25,

	angleMinTop : 7,
	angleMaxTop : 25,

	offsetLeft : 0,
	offsetTop: 0
};
BX.PopupWindow.setOptions = function(options)
{
	if (!options || typeof(options) != "object")
		return;

	for (var option in options)
		BX.PopupWindow.options[option] = options[option];
};

BX.PopupWindow.getOption = function(option, defaultValue)
{
	if (typeof(BX.PopupWindow.options[option]) != "undefined")
		return BX.PopupWindow.options[option];
	else if (typeof(defaultValue) != "undefined")
		return defaultValue;
	else
		return BX.PopupWindow.defaultOptions[option];
};


/*========================================Buttons===========================================*/

BX.PopupWindowButton = function(params)
{
	this.popupWindow = null;

	this.params = params || {};

	this.text = this.params.text || "";
	this.id = this.params.id || "";
	this.className = this.params.className || "";
	this.events = this.params.events || {};

	this.contextEvents = {};
	for (var eventName in this.events)
		this.contextEvents[eventName] = BX.proxy(this.events[eventName], this);

	this.nameNode = BX.create("span", { props : { className : "popup-window-button-text"}, text : this.text } );
	this.buttonNode = BX.create(
		"span",
		{
			props : { className : "popup-window-button" + (this.className.length > 0 ? " " + this.className : ""), id : this.id },
			children : [
				BX.create("span", { props : { className : "popup-window-button-left"} } ),
				this.nameNode,
				BX.create("span", { props : { className : "popup-window-button-right"} } )
			],
			events : this.contextEvents
		}
	);
};

BX.PopupWindowButton.prototype.render = function()
{
	return this.buttonNode;
};

BX.PopupWindowButton.prototype.setName = function(name)
{
	this.text = name || "";
	if (this.nameNode)
	{
		BX.cleanNode(this.nameNode);
		BX.adjust(this.nameNode, { text : this.text} );
	}
};

BX.PopupWindowButton.prototype.setClassName = function(className)
{
	if (this.buttonNode)
	{
		if (BX.type.isString(this.className) && (this.className != ''))
			BX.removeClass(this.buttonNode, this.className);

		BX.addClass(this.buttonNode, className)
	}

	this.className = className;
};

BX.PopupWindowButtonLink = function(params)
{
	BX.PopupWindowButtonLink.superclass.constructor.apply(this, arguments);

	this.nameNode = BX.create("span", { props : { className : "popup-window-button-link-text" }, text : this.text, events : this.contextEvents });
	this.buttonNode = BX.create(
		"span",
		{
			props : { className : "popup-window-button popup-window-button-link" + (this.className.length > 0 ? " " + this.className : ""), id : this.id },
			children : [this.nameNode]
		}
	);

};

BX.extend(BX.PopupWindowButtonLink, BX.PopupWindowButton);

BX.PopupMenu = {

	Data : {},
	currentItem : null,

	show : function(id, bindElement, menuItems, params)
	{
		if (this.currentItem !== null)
		{
			this.currentItem.popupWindow.close();
		}

		this.currentItem = this.create(id, bindElement, menuItems, params);
		this.currentItem.popupWindow.show();
	},

	create : function(id, bindElement, menuItems, params)
	{
		if (!this.Data[id])
		{
			this.Data[id] = new BX.PopupMenuWindow(id, bindElement, menuItems, params);
		}

		return this.Data[id];
	},

	getCurrentMenu : function()
	{
		return this.currentItem;
	},

	getMenuById : function(id)
	{
		return this.Data[id] ? this.Data[id] : null;
	},

	destroy : function(id)
	{
		var menu = this.getMenuById(id);
		if (menu)
		{
			if (this.currentItem == menu)
			{
				this.currentItem = null;
			}
			menu.popupWindow.destroy();
			delete this.Data[id];
		}
	}
};

BX.PopupMenuWindow = function(id, bindElement, menuItems, params)
{
	this.id = id;
	this.bindElement = bindElement;
	this.menuItems = [];
	this.itemsContainer = null;

	if (menuItems && BX.type.isArray(menuItems))
	{
		for (var i = 0; i < menuItems.length; i++)
		{
			this.__addMenuItem(menuItems[i], null);
		}
	}

	this.params = params && typeof(params) == "object" ? params : {};
	this.popupWindow = this.__createPopup();
};

BX.PopupMenuWindow.prototype.__createItem = function(item, position)
{
	if (position > 0)
	{
		item.layout.hr = BX.create("div", { props : { className : "popup-window-hr" }, children : [ BX.create("i", {}) ]});
	}

	if (item.delimiter)
	{
		item.layout.item = BX.create("span", { props: { className: "popup-window-delimiter" },  html: "<i></i>" });
	}
	else
	{
		item.layout.item = BX.create(!!item.href ? "a" : "span", {
			props : { className: "menu-popup-item" +  (BX.type.isNotEmptyString(item.className) ? " " + item.className : " menu-popup-no-icon")},
			attrs : { title : item.title ? item.title : "", onclick: item.onclick && BX.type.isString(item.onclick) ? item.onclick : "" },
			events : item.onclick && BX.type.isFunction(item.onclick) ? { click : BX.proxy(this.onItemClick, {obj : this, item : item }) } : null,
			children : [
				BX.create("span", { props : { className: "menu-popup-item-left"} }),
				BX.create("span", { props : { className: "menu-popup-item-icon"} }),
				(item.layout.text = BX.create("span", { props : { className: "menu-popup-item-text"}, html : item.text })),
				BX.create("span", { props : { className: "menu-popup-item-right"} })
			]
		});

		if (item.href)
			item.layout.item.href = item.href;
	}

	return item;
};

BX.PopupMenuWindow.prototype.__createPopup = function()
{
	var domItems = [];
	for (var i = 0; i < this.menuItems.length; i++)
	{
		this.__createItem(this.menuItems[i], i);
		if (this.menuItems[i].layout.hr != null)
		{
			domItems.push(this.menuItems[i].layout.hr);
		}

		domItems.push(this.menuItems[i].layout.item);
	}

	var popupWindow = new BX.PopupWindow("menu-popup-" + this.id, this.bindElement, {
		closeByEsc : typeof(this.params.closeByEsc) != "undefined" ? this.params.closeByEsc: false,
		bindOptions : typeof(this.params.bindOptions) == "object" ? this.params.bindOptions : {},
		angle : typeof(this.params.angle) != "undefined" ? this.params.angle : false,
		zIndex : this.params.zIndex ? this.params.zIndex : 0,
		overlay: typeof(this.params.overlay) == "object" ? this.params.overlay : null,
		autoHide : typeof(this.params.autoHide) != "undefined" ? this.params.autoHide : true,
		offsetTop : this.params.offsetTop ? this.params.offsetTop : 1,
		offsetLeft : this.params.offsetLeft ? this.params.offsetLeft : 0,

		lightShadow : typeof(this.params.lightShadow) != "undefined" ? this.params.lightShadow : true,

		content : BX.create("div", { props : { className : "menu-popup" }, children: [
			(this.itemsContainer = BX.create("div", { props : { className : "menu-popup-items" }, children: domItems}))
		]})
	});

	if (this.params && this.params.events)
	{
		for (var eventName in this.params.events)
		{
			if (this.params.events.hasOwnProperty(eventName))
			{
				BX.addCustomEvent(popupWindow, eventName, this.params.events[eventName]);
			}
		}
	}

	return popupWindow;
};

BX.PopupMenuWindow.prototype.onItemClick = function(event)
{
	event = event || window.event;
	this.item.onclick.call(this.obj, event, this.item);
};

BX.PopupMenuWindow.prototype.addMenuItem = function(menuItem, refItemId)
{
	var position = this.__addMenuItem(menuItem, refItemId);
	if (position < 0)
	{
		return false;
	}

	this.__createItem(menuItem, position);
	var refItem = this.getMenuItem(refItemId);
	if (refItem != null)
	{
		if (refItem.layout.hr == null)
		{
			refItem.layout.hr = BX.create("div", { props : { className : "popup-window-hr" }, children : [ BX.create("i", {}) ]});
			this.itemsContainer.insertBefore(refItem.layout.hr, refItem.layout.item);
		}

		if (menuItem.layout.hr != null)
		{
			this.itemsContainer.insertBefore(menuItem.layout.hr, refItem.layout.hr);
		}

		this.itemsContainer.insertBefore(menuItem.layout.item, refItem.layout.hr);
	}
	else
	{
		if (menuItem.layout.hr != null)
		{
			this.itemsContainer.appendChild(menuItem.layout.hr);
		}

		this.itemsContainer.appendChild(menuItem.layout.item);
	}

	return true;
};

BX.PopupMenuWindow.prototype.__addMenuItem = function(menuItem, refItemId)
{
	if (!menuItem || (!menuItem.delimiter && !BX.type.isNotEmptyString(menuItem.text)) || (menuItem.id && this.getMenuItem(menuItem.id) != null))
	{
		return -1;
	}

	menuItem.layout = { item : null, text : null, hr : null };

	var position = this.getMenuItemPosition(refItemId);
	if (position >= 0)
	{
		this.menuItems = BX.util.insertIntoArray(this.menuItems, position, menuItem);
	}
	else
	{
		this.menuItems.push(menuItem);
		position = this.menuItems.length - 1;
	}

	return position;
};

BX.PopupMenuWindow.prototype.removeMenuItem = function(itemId)
{
	var item = this.getMenuItem(itemId);
	if (!item)
	{
		return;
	}

	for (var position = 0; position < this.menuItems.length; position++)
	{
		if (this.menuItems[position] == item)
		{
			this.menuItems = BX.util.deleteFromArray(this.menuItems, position);
			break;
		}
	}

	if (position == 0)
	{
		if (this.menuItems[0])
		{
			this.menuItems[0].layout.hr.parentNode.removeChild(this.menuItems[0].layout.hr);
			this.menuItems[0].layout.hr = null;
		}
	}
	else
	{
		item.layout.hr.parentNode.removeChild(item.layout.hr);
	}

	item.layout.item.parentNode.removeChild(item.layout.item);
	item.layout.item = null;
};

BX.PopupMenuWindow.prototype.getMenuItem = function(itemId)
{
	for (var i = 0; i < this.menuItems.length; i++)
	{
		if (this.menuItems[i].id && this.menuItems[i].id == itemId)
		{
			return this.menuItems[i];
		}
	}

	return null;
};

BX.PopupMenuWindow.prototype.getMenuItemPosition = function(itemId)
{
	if (itemId)
	{
		for (var i = 0; i < this.menuItems.length; i++)
		{
			if (this.menuItems[i].id && this.menuItems[i].id == itemId)
			{
				return i;
			}
		}
	}

	return -1;
};

// TODO: copypaste/update/enhance CSS and images from calendar to MAIN CORE
// this.values = [{ID: 1, NAME : '111', DESCRIPTION: '111', URL: 'href://...'}]

window.BXInputPopup = function(params)
{
	this.id = params.id || 'bx-inp-popup-' + Math.round(Math.random() * 1000000);
	this.handler = params.handler || false;
	this.values = params.values || false;
	this.pInput = params.input;
	this.bValues = !!this.values;
	this.defaultValue = params.defaultValue || '';
	this.openTitle = params.openTitle || '';
	this.className = params.className || '';
	this.noMRclassName = params.noMRclassName || 'ec-no-rm';
	this.emptyClassName = params.noMRclassName || 'ec-label';

	var _this = this;
	this.curInd = false;

	if (this.bValues)
	{
		this.pInput.onfocus = this.pInput.onclick = function(e)
		{
			if (this.value == _this.defaultValue)
			{
				this.value = '';
				this.className = _this.className;
			}
			_this.ShowPopup();
			return BX.PreventDefault(e);
		};
		this.pInput.onblur = function()
		{
			if (_this.bShowed)
				setTimeout(function(){_this.ClosePopup(true);}, 200);
			_this.OnChange();
		};
	}
	else
	{
		this.pInput.className = this.noMRclassName;
		this.pInput.onblur = BX.proxy(this.OnChange, this);
	}
}

BXInputPopup.prototype = {
ShowPopup: function()
{
	if (this.bShowed)
		return;

	var _this = this;
	if (!this.oPopup)
	{
		var
			pRow,
			pWnd = BX.create("DIV", {props:{className: "bxecpl-loc-popup " + this.className}});

		for (var i = 0, l = this.values.length; i < l; i++)
		{
			pRow = pWnd.appendChild(BX.create("DIV", {
				props: {id: 'bxecmr_' + i},
				text: this.values[i].NAME,
				events: {
					mouseover: function(){BX.addClass(this, 'bxecplloc-over');},
					mouseout: function(){BX.removeClass(this, 'bxecplloc-over');},
					click: function()
					{
						var ind = this.id.substr('bxecmr_'.length);
						_this.pInput.value = _this.values[ind].NAME;
						_this.curInd = ind;
						_this.OnChange();
						_this.ClosePopup(true);
					}
				}
			}));

			if (this.values[i].DESCRIPTION)
				pRow.title = this.values[i].DESCRIPTION;
			if (this.values[i].CLASS_NAME)
				BX.addClass(pRow, this.values[i].CLASS_NAME);

			if (this.values[i].URL)
				pRow.appendChild(BX.create('A', {props: {href: this.values[i].URL, className: 'bxecplloc-view', target: '_blank', title: this.openTitle}}));
		}

		this.oPopup = new BX.PopupWindow(this.id, this.pInput, {
			autoHide : true,
			offsetTop : 1,
			offsetLeft : 0,
			lightShadow : true,
			closeByEsc : true,
			content : pWnd
		});

		BX.addCustomEvent(this.oPopup, 'onPopupClose', BX.proxy(this.ClosePopup, this));
	}

	this.oPopup.show();
	this.pInput.select();

	this.bShowed = true;
	BX.onCustomEvent(this, 'onInputPopupShow', [this]);
},

ClosePopup: function(bClosePopup)
{
	this.bShowed = false;

	if (this.pInput.value == '')
		this.OnChange();

	BX.onCustomEvent(this, 'onInputPopupClose', [this]);

	if (bClosePopup === true)
		this.oPopup.close();
},

OnChange: function()
{
	var val = this.pInput.value;
	if (this.bValues)
	{
		if (this.pInput.value == '' || this.pInput.value == this.defaultValue)
		{
			this.pInput.value = this.defaultValue;
			this.pInput.className = this.emptyClassName;
			val = '';
		}
		else
		{
			this.pInput.className = '';
		}
	}

	if (isNaN(parseInt(this.curInd)) || this.curInd !==false && val != this.values[this.curInd].NAME)
		this.curInd = false;
	else
		this.curInd = parseInt(this.curInd);

	BX.onCustomEvent(this, 'onInputPopupChanged', [this, this.curInd, val]);
	if (this.handler && typeof this.handler == 'function')
		this.handler({ind: this.curInd, value: val});
},

Set: function(ind, val, bOnChange)
{
	this.curInd = ind;
	if (this.curInd !== false)
		this.pInput.value = this.values[this.curInd].NAME;
	else
		this.pInput.value = val;

	if (bOnChange !== false)
		this.OnChange();
},

Get: function(ind)
{
	var
		id = false;
	if (typeof ind == 'undefined')
		ind = this.curInd;

	if (ind !== false && this.values[ind])
		id = this.values[ind].ID;
	return id;
},

GetIndex: function(id)
{
	for (var i = 0, l = this.values.length; i < l; i++)
		if (this.values[i].ID == id)
			return i;
	return false;
},

Deactivate: function(bDeactivate)
{
	if (this.pInput.value == '' || this.pInput.value == this.defaultValue)
	{
		if (bDeactivate)
		{
			this.pInput.value = '';
			this.pInput.className = this.noMRclassName;
		}
		else if (this.oEC.bUseMR)
		{
			this.pInput.value = this.defaultValue;
			this.pInput.className = this.emptyClassName;
		}
	}
	this.pInput.disabled = bDeactivate;
}
};

/************** utility *************/

var _escCallbackIndex = -1,
	_escCallback = null;

function _checkEscPressed(zIndex, callback)
{
	if(zIndex === false)
	{
		if(_escCallback && _escCallback.length > 0)
		{
			for(var i=0;i<_escCallback.length; i++)
			{
				_escCallback[i]();
			}

			_escCallback = null;
			_escCallbackIndex = -1;
		}
	}
	else
	{
		if(_escCallback === null)
		{
			_escCallback = [];
			_escCallbackIndex = -1;
			BX.defer(_checkEscPressed)(false);
		}

		if(zIndex > _escCallbackIndex)
		{
			_escCallbackIndex = zIndex;
			_escCallback = [callback];
		}
		else if(zIndex == _escCallbackIndex)
		{
			_escCallback.push(callback)
		}
	}
}


})(window);

/* End */
;
; /* Start:/bitrix/js/main/core/core_date.js*/
;(function(){

if (BX.date)
	return;

BX.date = {};


BX.date.format = function(format, timestamp, now, utc)
{
	/*
	PHP to Javascript:
		time() = new Date()
		mktime(...) = new Date(...)
		gmmktime(...) = new Date(Date.UTC(...))
		mktime(0,0,0, 1, 1, 1970) != 0          new Date(1970,0,1).getTime() != 0
		gmmktime(0,0,0, 1, 1, 1970) == 0        new Date(Date.UTC(1970,0,1)).getTime() == 0
		date("d.m.Y H:i:s") = BX.date.format("d.m.Y H:i:s")
		gmdate("d.m.Y H:i:s") = BX.date.format("d.m.Y H:i:s", null, null, true);
	*/
	var date = BX.type.isDate(timestamp) ? new Date(timestamp.getTime()) : BX.type.isNumber(timestamp) ? new Date(timestamp * 1000) : new Date();
	var nowDate = BX.type.isDate(now) ? new Date(now.getTime()) : BX.type.isNumber(now) ? new Date(now * 1000) : new Date();
	var isUTC = !!utc;

	if (BX.type.isArray(format))
		return _formatDateInterval(format, date, nowDate, isUTC);
	else if (!BX.type.isNotEmptyString(format))
		return "";

	var formatRegex = /\\?(sago|iago|isago|Hago|dago|mago|Yago|sdiff|idiff|Hdiff|ddiff|mdiff|Ydiff|yesterday|today|tommorow|[a-z])/gi;

	var dateFormats = {
		d : function() {
			// Day of the month 01 to 31
			return BX.util.str_pad_left(getDate(date).toString(), 2, "0");
		},

		D : function() {
			//Mon through Sun
			return BX.message("DOW_" + getDay(date));
		},

		j : function() {
			//Day of the month 1 to 31
			return getDate(date);
		},

		l : function() {
			//Sunday through Saturday
			return BX.message("DAY_OF_WEEK_" + getDay(date));
		},

		N : function() {
			//1 (for Monday) through 7 (for Sunday)
			return getDay(date) || 7;
		},

		S : function() {
			//st, nd, rd or th. Works well with j
			if (getDate(date) % 10 == 1 && getDate(date) != 11)
				return "st";
			else if (getDate(date) % 10 == 2 && getDate(date) != 12)
				return "nd";
			else if (getDate(date) % 10 == 3 && getDate(date) != 13)
				return "rd";
			else
				return "th";
		},

		w : function() {
			//0 (for Sunday) through 6 (for Saturday)
			return getDay(date);
		},

		z : function() {
			//0 through 365
			var firstDay = new Date(getFullYear(date), 0, 1);
			var currentDay = new Date(getFullYear(date), getMonth(date), getDate(date));
			return Math.ceil( (currentDay - firstDay) / (24 * 3600 * 1000) );
		},

		W : function() {
			//ISO-8601 week number of year
			var newDate  = new Date(date.getTime());
		    var dayNumber   = (getDay(date) + 6) % 7;
			setDate(newDate, getDate(newDate) - dayNumber + 3);
		    var firstThursday = newDate.getTime();
			setMonth(newDate, 0, 1);
		    if (getDay(newDate) != 4)
				setMonth(newDate, 0, 1 + ((4 - getDay(newDate)) + 7) % 7);
			var weekNumber = 1 + Math.ceil((firstThursday - newDate) / (7 * 24 * 3600 * 1000));
		    return BX.util.str_pad_left(weekNumber.toString(), 2, "0");
		},

		F : function() {
			//January through December
			return BX.message("MONTH_" + (getMonth(date) + 1) + "_S");
		},

		f : function() {
			//January through December
			return BX.message("MONTH_" + (getMonth(date) + 1));
		},

		m : function() {
			//Numeric representation of a month 01 through 12
			return BX.util.str_pad_left((getMonth(date) + 1).toString(), 2, "0");
		},

		M : function() {
			//A short textual representation of a month, three letters Jan through Dec
			return BX.message("MON_" + (getMonth(date) + 1));
		},

		n : function() {
			//Numeric representation of a month 1 through 12
			return getMonth(date) + 1;
		},

		t : function() {
			//Number of days in the given month 28 through 31
			var lastMonthDay = isUTC ? new Date(Date.UTC(getFullYear(date), getMonth(date) + 1, 0)) : new Date(getFullYear(date), getMonth(date) + 1, 0);
			return getDate(lastMonthDay);
		},

		L : function() {
			//1 if it is a leap year, 0 otherwise.
			var year = getFullYear(date);
			return (year % 4 == 0 && year % 100 != 0 || year % 400 == 0 ? 1 : 0);
		},

		o : function() {
			//ISO-8601 year number
			var correctDate  = new Date(date.getTime());
			setDate(correctDate, getDate(correctDate) - ((getDay(date) + 6) % 7) + 3);
   			return getFullYear(correctDate);
		},

		Y : function() {
			//A full numeric representation of a year, 4 digits
			return getFullYear(date);
		},

		y : function() {
			//A two digit representation of a year
			return getFullYear(date).toString().slice(2);
		},

		a : function() {
			//am or pm
			return getHours(date) > 11 ? "pm" : "am";
		},

		A : function() {
			//AM or PM
			return getHours(date) > 11 ? "PM" : "AM";
		},

		B : function() {
			//000 through 999
			var swatch = ((date.getUTCHours() + 1) % 24) + date.getUTCMinutes() / 60 + date.getUTCSeconds() / 3600;
			return BX.util.str_pad_left(Math.floor(swatch * 1000 / 24).toString(), 3, "0");
		},

		g : function() {
			//12-hour format of an hour without leading zeros 1 through 12
			return getHours(date) % 12 || 12;
		},

		G : function() {
			//24-hour format of an hour without leading zeros 0 through 23
			return getHours(date);
		},

		h : function() {
			//12-hour format of an hour with leading zeros 01 through 12
			return BX.util.str_pad_left((getHours(date) % 12 || 12).toString(), 2, "0");
		},

		H : function() {
			//24-hour format of an hour with leading zeros 00 through 23
			return BX.util.str_pad_left(getHours(date).toString(), 2, "0");
		},

		i : function() {
			//Minutes with leading zeros 00 to 59
			return BX.util.str_pad_left(getMinutes(date).toString(), 2, "0");
		},

		s : function() {
			//Seconds, with leading zeros 00 through 59
			return BX.util.str_pad_left(getSeconds(date).toString(), 2, "0");
		},

		u : function() {
			//Microseconds
			return BX.util.str_pad_left((getMilliseconds(date) * 1000).toString(), 6, "0");
		},

		e : function() {
			if (isUTC)
				return "UTC";
			return "";
		},

		I : function() {
			if (isUTC)
				return 0;

			//Whether or not the date is in daylight saving time 1 if Daylight Saving Time, 0 otherwise
			var firstJanuary = new Date(getFullYear(date), 0, 1);
			var firstJanuaryUTC = Date.UTC(getFullYear(date), 0, 1);
			var firstJuly = new Date(getFullYear(date), 6, 0);
			var firstJulyUTC = Date.UTC(getFullYear(date), 6, 0);
			return 0 + ((firstJanuary - firstJanuaryUTC) !== (firstJuly - firstJulyUTC));
		},

		O : function() {
			if (isUTC)
				return "+0000";

			//Difference to Greenwich time (GMT) in hours +0200
			var timezoneOffset = date.getTimezoneOffset();
			var timezoneOffsetAbs = Math.abs(timezoneOffset);
			return (timezoneOffset > 0 ? "-" : "+") + BX.util.str_pad_left((Math.floor(timezoneOffsetAbs / 60) * 100 + timezoneOffsetAbs % 60).toString(), 4, "0");
		},

		P : function() {
			if (isUTC)
				return "+00:00";

			//Difference to Greenwich time (GMT) with colon between hours and minutes +02:00
			var difference = this.O();
			return difference.substr(0, 3) + ":" + difference.substr(3);
		},

		Z : function() {
			if (isUTC)
				return 0;
			//Timezone offset in seconds. The offset for timezones west of UTC is always negative,
			//and for those east of UTC is always positive.
			return -date.getTimezoneOffset() * 60;
		},

		c : function() {
			//ISO 8601 date
			return "Y-m-d\\TH:i:sP".replace(formatRegex, _replaceDateFormat);
		},

		r : function() {
			//RFC 2822 formatted date
			return "D, d M Y H:i:s O".replace(formatRegex, _replaceDateFormat);
		},

		U : function() {
			//Seconds since the Unix Epoch
			return Math.floor(date.getTime() / 1000);
		},

		sago : function() {
			return _formatDateMessage(intval((nowDate - date) / 1000), {
				"0" : "FD_SECOND_AGO_0",
				"1" : "FD_SECOND_AGO_1",
				"10_20" : "FD_SECOND_AGO_10_20",
				"MOD_1" : "FD_SECOND_AGO_MOD_1",
				"MOD_2_4" : "FD_SECOND_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_SECOND_AGO_MOD_OTHER"
			});
		},

		sdiff : function() {
			return _formatDateMessage(intval((nowDate - date) / 1000), {
				"0" : "FD_SECOND_DIFF_0",
				"1" : "FD_SECOND_DIFF_1",
				"10_20" : "FD_SECOND_DIFF_10_20",
				"MOD_1" : "FD_SECOND_DIFF_MOD_1",
				"MOD_2_4" : "FD_SECOND_DIFF_MOD_2_4",
				"MOD_OTHER" : "FD_SECOND_DIFF_MOD_OTHER"
			});
		},

		iago : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 1000), {
				"0" : "FD_MINUTE_AGO_0",
				"1" : "FD_MINUTE_AGO_1",
				"10_20" : "FD_MINUTE_AGO_10_20",
				"MOD_1" : "FD_MINUTE_AGO_MOD_1",
				"MOD_2_4" : "FD_MINUTE_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_MINUTE_AGO_MOD_OTHER"
			});
		},

		idiff : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 1000), {
				"0" : "FD_MINUTE_DIFF_0",
				"1" : "FD_MINUTE_DIFF_1",
				"10_20" : "FD_MINUTE_DIFF_10_20",
				"MOD_1" : "FD_MINUTE_DIFF_MOD_1",
				"MOD_2_4" : "FD_MINUTE_DIFF_MOD_2_4",
				"MOD_OTHER" : "FD_MINUTE_DIFF_MOD_OTHER"
			});
		},

		isago : function() {
			var minutesAgo = intval((nowDate - date) / 60 / 1000);
			var result = _formatDateMessage(minutesAgo, {
				"0" : "FD_MINUTE_0",
				"1" : "FD_MINUTE_1",
				"10_20" : "FD_MINUTE_10_20",
				"MOD_1" : "FD_MINUTE_MOD_1",
				"MOD_2_4" : "FD_MINUTE_MOD_2_4",
				"MOD_OTHER" : "FD_MINUTE_MOD_OTHER"
			});

			result += " ";

			var secondsAgo = intval((nowDate - date) / 1000) - (minutesAgo * 60);
			result += _formatDateMessage(secondsAgo, {
				"0" : "FD_SECOND_AGO_0",
				"1" : "FD_SECOND_AGO_1",
				"10_20" : "FD_SECOND_AGO_10_20",
				"MOD_1" : "FD_SECOND_AGO_MOD_1",
				"MOD_2_4" : "FD_SECOND_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_SECOND_AGO_MOD_OTHER"
			});
			return result;
		},

		Hago : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 1000), {
				"0" : "FD_HOUR_AGO_0",
				"1" : "FD_HOUR_AGO_1",
				"10_20" : "FD_HOUR_AGO_10_20",
				"MOD_1" : "FD_HOUR_AGO_MOD_1",
				"MOD_2_4" : "FD_HOUR_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_HOUR_AGO_MOD_OTHER"
			});
		},

		Hdiff : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 1000), {
				"0" : "FD_HOUR_DIFF_0",
				"1" : "FD_HOUR_DIFF_1",
				"10_20" : "FD_HOUR_DIFF_10_20",
				"MOD_1" : "FD_HOUR_DIFF_MOD_1",
				"MOD_2_4" : "FD_HOUR_DIFF_MOD_2_4",
				"MOD_OTHER" : "FD_HOUR_DIFF_MOD_OTHER"
			});
		},

		yesterday : function() {
			return BX.message("FD_YESTERDAY");
		},

		today : function() {
			return BX.message("FD_TODAY");
		},

		tommorow : function() {
			return BX.message("FD_TOMORROW");
		},

		dago : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 24 / 1000), {
				"0" : "FD_DAY_AGO_0",
				"1" : "FD_DAY_AGO_1",
				"10_20" : "FD_DAY_AGO_10_20",
				"MOD_1" : "FD_DAY_AGO_MOD_1",
				"MOD_2_4" : "FD_DAY_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_DAY_AGO_MOD_OTHER"
			});
		},

		ddiff : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 24 / 1000), {
				"0" : "FD_DAY_DIFF_0",
				"1" : "FD_DAY_DIFF_1",
				"10_20" : "FD_DAY_DIFF_10_20",
				"MOD_1" : "FD_DAY_DIFF_MOD_1",
				"MOD_2_4" : "FD_DAY_DIFF_MOD_2_4",
				"MOD_OTHER" : "FD_DAY_DIFF_MOD_OTHER"
			});
		},

		mago : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 24 / 31 / 1000), {
				"0" : "FD_MONTH_AGO_0",
				"1" : "FD_MONTH_AGO_1",
				"10_20" : "FD_MONTH_AGO_10_20",
				"MOD_1" : "FD_MONTH_AGO_MOD_1",
				"MOD_2_4" : "FD_MONTH_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_MONTH_AGO_MOD_OTHER"
			});
		},

		mdiff : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 24 / 31 / 1000), {
				"0" : "FD_MONTH_DIFF_0",
				"1" : "FD_MONTH_DIFF_1",
				"10_20" : "FD_MONTH_DIFF_10_20",
				"MOD_1" : "FD_MONTH_DIFF_MOD_1",
				"MOD_2_4" : "FD_MONTH_DIFF_MOD_2_4",
				"MOD_OTHER" : "FD_MONTH_DIFF_MOD_OTHER"
			});
		},

		Yago : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 24 / 365 / 1000), {
				"0" : "FD_YEARS_AGO_0",
				"1" : "FD_YEARS_AGO_1",
				"10_20" : "FD_YEARS_AGO_10_20",
				"MOD_1" : "FD_YEARS_AGO_MOD_1",
				"MOD_2_4" : "FD_YEARS_AGO_MOD_2_4",
				"MOD_OTHER" : "FD_YEARS_AGO_MOD_OTHER"
			});
		},

		Ydiff : function() {
			return _formatDateMessage(intval((nowDate - date) / 60 / 60 / 24 / 365 / 1000), {
				"0" : "FD_YEARS_DIFF_0",
				"1" : "FD_YEARS_DIFF_1",
				"10_20" : "FD_YEARS_DIFF_10_20",
				"MOD_1" : "FD_YEARS_DIFF_MOD_1",
				"MOD_2_4" : "FD_YEARS_DIFF_MOD_2_4",
				"MOD_OTHER" : "FD_YEARS_DIFF_MOD_OTHER"
			});
		},

		x : function() {
			return BX.date.format([
				["tommorow", "tommorow, H:i"],
				["-", BX.date.convertBitrixFormat(BX.message("FORMAT_DATETIME")).replace(/:s$/g, "")],
				["s", "sago"],
				["i", "iago"],
				["today", "today, H:i"],
				["yesterday", "yesterday, H:i"],
				["", BX.date.convertBitrixFormat(BX.message("FORMAT_DATETIME")).replace(/:s$/g, "")]
			], date, nowDate, isUTC);
		},

		X : function() {
			var day = BX.date.format([
				["tommorow", "tommorow"],
				["-", BX.date.convertBitrixFormat(BX.message("FORMAT_DATE"))],
				["today", "today"],
				["yesterday", "yesterday"],
				["", BX.date.convertBitrixFormat(BX.message("FORMAT_DATE"))]
			], date, nowDate, isUTC);

			var time = BX.date.format([
				["tommorow", "H:i"],
				["today", "H:i"],
				["yesterday", "H:i"],
				["", ""]
			], date, nowDate, isUTC);

			if (time.length > 0)
				return BX.message("FD_DAY_AT_TIME").replace(/#DAY#/g, day).replace(/#TIME#/g, time);
			else
				return day;
		},

		Q : function() {
			var daysAgo = intval((nowDate - date) / 60 / 60 / 24 / 1000);
			if(daysAgo == 0)
				return BX.message("FD_DAY_DIFF_1").replace(/#VALUE#/g, 1);
			else
				return BX.date.format([ ["d", "ddiff"], ["m", "mdiff"], ["", "Ydiff"] ], date, nowDate);
		}
	};

	var cutZeroTime = false;
	if (format[0] && format[0] == "^")
	{
		cutZeroTime = true;
		format = format.substr(1);
	}

	var result = format.replace(formatRegex, _replaceDateFormat);

	if (cutZeroTime)
	{
		/* 	15.04.12 13:00:00 => 15.04.12 13:00
			00:01:00 => 00:01
			4 may 00:00:00 => 4 may
			01-01-12 00:00 => 01-01-12
		*/

		result = result.replace(/\s*00:00:00\s*/g, "").
						replace(/(\d\d:\d\d)(:00)/g, "$1").
						replace(/(\s*00:00\s*)(?!:)/g, "");
	}

	return result;

	function _formatDateInterval(formats, date, nowDate, isUTC)
	{
		var secondsAgo = intval((nowDate - date) / 1000);
		for (var i = 0; i < formats.length; i++)
		{
			var formatInterval = formats[i][0];
			var formatValue = formats[i][1];
			var match = null;
			if (formatInterval == "s")
			{
				if (secondsAgo < 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if ((match = /^s(\d+)/.exec(formatInterval)) != null)
			{
				if (secondsAgo < match[1])
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "i")
			{
				if (secondsAgo < 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if ((match = /^i(\d+)/.exec(formatInterval)) != null)
			{
				if (secondsAgo < match[1]*60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "H")
			{
				if (secondsAgo < 24 * 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if ((match = /^H(\d+)/.exec(formatInterval)) != null)
			{
				if (secondsAgo < match[1] * 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "d")
			{
				if (secondsAgo < 31 *24 * 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if ((match = /^d(\d+)/.exec(formatInterval)) != null)
			{
				if (secondsAgo < match[1] * 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "m")
			{
				if (secondsAgo < 365 * 24 * 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if ((match = /^m(\d+)/.exec(formatInterval)) != null)
			{
				if (secondsAgo < match[1] * 31 * 24 * 60 * 60)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "today")
			{
				var year = getFullYear(nowDate), month = getMonth(nowDate), day = getDate(nowDate);
				var todayStart = isUTC ? new Date(Date.UTC(year, month, day, 0, 0, 0, 0)) : new Date(year, month, day, 0, 0, 0, 0);
				var todayEnd = isUTC ? new Date(Date.UTC(year, month, day+1, 0, 0, 0, 0)) : new Date(year, month, day+1, 0, 0, 0, 0);
				if (date >= todayStart && date < todayEnd)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "yesterday")
			{
				year = getFullYear(nowDate); month = getMonth(nowDate); day = getDate(nowDate);
				var yesterdayStart = isUTC ? new Date(Date.UTC(year, month, day-1, 0, 0, 0, 0)) : new Date(year, month, day-1, 0, 0, 0, 0);
				var yesterdayEnd = isUTC ? new Date(Date.UTC(year, month, day, 0, 0, 0, 0)) : new Date(year, month, day, 0, 0, 0, 0);
				if (date >= yesterdayStart && date < yesterdayEnd)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "tommorow")
			{
				year = getFullYear(nowDate); month = getMonth(nowDate); day = getDate(nowDate);
				var tommorowStart = isUTC ? new Date(Date.UTC(year, month, day+1, 0, 0, 0, 0)) : new Date(year, month, day+1, 0, 0, 0, 0);
				var tommorowEnd = isUTC ? new Date(Date.UTC(year, month, day+2, 0, 0, 0, 0)) : new Date(year, month, day+2, 0, 0, 0, 0);
				if (date >= tommorowStart && date < tommorowEnd)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
			else if (formatInterval == "-")
			{
				if (secondsAgo < 0)
					return BX.date.format(formatValue, date, nowDate, isUTC);
			}
		}

		return formats.length > 0 ? BX.date.format(formats.pop()[1], date, nowDate, isUTC) : "";
	}


	function getFullYear(date) { return isUTC ? date.getUTCFullYear() : date.getFullYear(); }
	function getDate(date) { return isUTC ? date.getUTCDate() : date.getDate(); }
	function getMonth(date) { return isUTC ? date.getUTCMonth() : date.getMonth(); }
	function getHours(date) { return isUTC ? date.getUTCHours() : date.getHours(); }
	function getMinutes(date) { return isUTC ? date.getUTCMinutes() : date.getMinutes(); }
	function getSeconds(date) { return isUTC ? date.getUTCSeconds() : date.getSeconds(); }
	function getMilliseconds(date) { return isUTC ? date.getUTCMilliseconds() : date.getMilliseconds(); }
	function getDay(date) { return isUTC ? date.getUTCDay() : date.getDay(); }
	function setDate(date, dayValue) { return isUTC ? date.setUTCDate(dayValue) : date.setDate(dayValue); }
	function setMonth(date, monthValue, dayValue) { return isUTC ? date.setUTCMonth(monthValue, dayValue) : date.setMonth(monthValue, dayValue); }

	function _formatDateMessage(value, messages)
	{
		var val = value < 100 ? Math.abs(value) : Math.abs(value % 100);
		var dec = val % 10;
		var message = "";

		if(val == 0)
			message = BX.message(messages["0"]);
		else if (val == 1)
			message = BX.message(messages["1"]);
		else if (val >= 10 && val <= 20)
			message = BX.message(messages["10_20"]);
		else if (dec == 1)
			message = BX.message(messages["MOD_1"]);
		else if (2 <= dec && dec <= 4)
			message = BX.message(messages["MOD_2_4"]);
		else
			message = BX.message(messages["MOD_OTHER"]);

		return message.replace(/#VALUE#/g, value);
	}

	function _replaceDateFormat(match, matchFull)
	{
		if (dateFormats[match])
			return dateFormats[match]();
		else
			return matchFull;
	}

	function intval(number)
	{
		return number >= 0 ? Math.floor(number) : Math.ceil(number);
	}
};

BX.date.convertBitrixFormat = function(format)
{
	if (!BX.type.isNotEmptyString(format))
		return "";

	return format.replace("YYYY", "Y")	// 1999
				 .replace("MMMM", "F")	// January - December
				 .replace("MM", "m")	// 01 - 12
				 .replace("M", "M")	// Jan - Dec
				 .replace("DD", "d")	// 01 - 31
				 .replace("G", "g")	//  1 - 12
				 .replace(/GG/i, "G")	//  0 - 23
				 .replace("H", "h")	// 01 - 12
				 .replace(/HH/i, "H")	// 00 - 24
				 .replace("MI", "i")	// 00 - 59
				 .replace("SS", "s")	// 00 - 59
				 .replace("TT", "A")	// AM - PM
				 .replace("T", "a");	// am - pm
};

BX.date.convertToUTC = function(date)
{
	if (!BX.type.isDate(date))
		return null;
	return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds(), date.getMilliseconds()));
};

/*
 function creates and returns Javascript Date() object from server timestamp regardless of local browser (system) timezone.
 For example can be used to convert timestamp from some exact date on server to the JS Date object with the same value.

 params: {
 timestamp: timestamp in seconds
 }
 */
BX.date.getNewDate = function(timestamp)
{
	return new Date(BX.date.getBrowserTimestamp(timestamp));
};

/*
 function transforms server timestamp (in sec) to javascript timestamp (calculated depend on local browser timezone offset). Returns timestamp in milliseconds.
 Also see BX.date.getNewDate description.

 params: {
 timestamp: timestamp in seconds
 }
 */
BX.date.getBrowserTimestamp = function(timestamp)
{
	timestamp = parseInt(timestamp, 10);
	var browserOffset = new Date(timestamp * 1000).getTimezoneOffset() * 60;
	return (parseInt(timestamp, 10) + parseInt(BX.message('SERVER_TZ_OFFSET')) + browserOffset) * 1000;
};

/*
 function transforms local browser timestamp (in ms) to server timestamp (calculated depend on local browser timezone offset). Returns timestamp in seconds.

 params: {
 timestamp: timestamp in milliseconds
 }
 */
BX.date.getServerTimestamp = function(timestamp)
{
	timestamp = parseInt(timestamp, 10);
	var browserOffset = new Date(timestamp).getTimezoneOffset() * 60;
	return Math.round(timestamp / 1000 - (parseInt(BX.message('SERVER_TZ_OFFSET'), 10) + parseInt(browserOffset, 10)));
};

/************************************** calendar class **********************************/

var obCalendarSingleton = null;

/*
params: {
	node: bind element || document.body

	value - start value in site format (using 'field' param if 'value' does not exist)
	callback - date check handler. can return false to prevent calendar closing.
	callback_after - another handler, called after date picking

	field - field to read/write data

	bTime = true - whether to enable time control
	bHideTime = false - whether to hide time control by default

	currentTime - current UTC time()

}
*/


BX.calendar = function(params)
{
	return BX.calendar.get().Show(params);
}

BX.calendar.get = function()
{
	if (!obCalendarSingleton)
		obCalendarSingleton = new BX.JCCalendar();

	return obCalendarSingleton;
}

// simple func for compatibility with the oldies
BX.calendar.InsertDaysBack = function(input, days)
{
	if (days != '')
	{
		var d = new Date();
		if(days > 0)
		{
			d.setTime(d.valueOf() - days*86400000);
		}

		input.value = BX.date.format(BX.date.convertBitrixFormat(BX.message('FORMAT_DATE')), d, null);
	}
	else
	{
		input.value = '';
	}
}

BX.calendar.ValueToString = function(value, bTime, bUTC)
{
	return BX.date.format(
		BX.date.convertBitrixFormat(BX.message(bTime ? 'FORMAT_DATETIME' : 'FORMAT_DATE')),
		value,
		null,
		!!bUTC
	);
}


BX.CalendarPeriod =
{
	Init: function(inputFrom, inputTo, selPeriod)
	{
		if((inputFrom.value != "" || inputTo.value != "") && selPeriod.value == "")
			selPeriod.value = "interval";

		selPeriod.onchange();
	},

	ChangeDirectOpts: function(peroidValue, selPParent) // "week" || "others"
	{
		var selDirect = BX.findChild(selPParent, {'className':'adm-select adm-calendar-direction'}, true);

		if(peroidValue == "week")
		{
			selDirect.options[0].text = BX.message('JSADM_CALEND_PREV_WEEK');
			selDirect.options[1].text = BX.message('JSADM_CALEND_CURR_WEEK');
			selDirect.options[2].text = BX.message('JSADM_CALEND_NEXT_WEEK');
		}
		else
		{
			selDirect.options[0].text = BX.message('JSADM_CALEND_PREV');
			selDirect.options[1].text = BX.message('JSADM_CALEND_CURR');
			selDirect.options[2].text = BX.message('JSADM_CALEND_NEXT');
		}
	},

	SaveAndClearInput: function(oInput)
	{
		if(!window.SavedPeriodValues)
			window.SavedPeriodValues = {};

		window.SavedPeriodValues[oInput.id] = oInput.value;
		oInput.value="";
	},

	RestoreInput: function(oInput)
	{
		if(!window.SavedPeriodValues || !window.SavedPeriodValues[oInput.id])
			return;

		oInput.value = window.SavedPeriodValues[oInput.id];
		delete(window.SavedPeriodValues[oInput.id]);
	},

	OnChangeP: function(sel)
	{
		var selPParent = sel.parentNode.parentNode;
		var bShowFrom = bShowTo = bShowDirect = bShowSeparate = false;

		var inputFromWrap = BX.findChild(selPParent, {'className':'adm-input-wrap adm-calendar-inp adm-calendar-first'});
		var inputToWrap = BX.findChild(selPParent, {'className':'adm-input-wrap adm-calendar-second'});
		var selDirectWrap = BX.findChild(selPParent, {'className':'adm-select-wrap adm-calendar-direction'});
		var separator = BX.findChild(selPParent, {'className':'adm-calendar-separate'});
		var inputFrom = BX.findChild(selPParent, {'className':'adm-input adm-calendar-from'},true);
		var inputTo = BX.findChild(selPParent, {'className':'adm-input adm-calendar-to'},true);

		// define who must be shown
		switch (sel.value)
		{
			case "day":
			case "week":
			case "month":
			case "quarter":
			case "year":
				bShowDirect=true;
				BX.CalendarPeriod.OnChangeD(selDirectWrap.children[0]);
				break;

			case "before":
				bShowTo = true;
				break;

			case "after":
				bShowFrom = true;
				break;

			case "exact":
				bShowFrom= true;
				break;

			case "interval":
				bShowFrom = bShowTo = bShowSeparate = true;
				BX.CalendarPeriod.RestoreInput(inputFrom);
				BX.CalendarPeriod.RestoreInput(inputTo);

				break;

			case "":
				BX.CalendarPeriod.SaveAndClearInput(inputFrom);
				BX.CalendarPeriod.SaveAndClearInput(inputTo);
				break;

			default:
				break;

		}

		BX.CalendarPeriod.ChangeDirectOpts(sel.value, selPParent);

		inputFromWrap.style.display = (bShowFrom? 'inline-block':'none');
		inputToWrap.style.display = (bShowTo? 'inline-block':'none');
		selDirectWrap.style.display = (bShowDirect? 'inline-block':'none');
		separator.style.display = (bShowSeparate? 'inline-block':'none');
	},


	OnChangeD: function(sel)
	{
		var selPParent = sel.parentNode.parentNode;
		var inputFrom = BX.findChild(selPParent, {'className':'adm-input adm-calendar-from'},true);
		var inputTo = BX.findChild(selPParent, {'className':'adm-input adm-calendar-to'},true);
		var selPeriod = BX.findChild(selPParent, {'className':'adm-select adm-calendar-period'},true);

		var offset=0;

		switch (sel.value)
		{
			case "previous":
				offset = -1;
				break;

			case "next":
				offset = 1;
				break;

			case "current":
			default:
				break;

		}

		var from = false;
		var to = false;

		var today = new Date();
		var year = today.getFullYear();
		var month = today.getMonth();
		var day = today.getDate();
		var dayW = today.getDay();

		if (dayW == 0)
				dayW = 7;

		switch (selPeriod.value)
		{
			case "day":
				from = new Date(year, month, day+offset, 0, 0, 0);
				to = new Date(year, month, day+offset, 23, 59, 59);
				break;

			case "week":
				from = new Date(year, month, day-dayW+1+offset*7, 0, 0, 0);
				to = new Date(year, month, day+(7-dayW)+offset*7, 23, 59, 59);
				break;

			case "month":
				from = new Date(year, month+offset, 1, 0, 0, 0);
				to = new Date(year, month+1+offset, 0, 23, 59, 59);
				break;

			case "quarter":
				var quarterNum = Math.floor((month/3))+offset;
				from = new Date(year, 3*(quarterNum), 1, 0, 0, 0);
				to = new Date(year, 3*(quarterNum+1), 0, 23, 59, 59);
				break;

			case "year":
				from = new Date(year+offset, 0, 1, 0, 0, 0);
				to = new Date(year+1+offset, 0, 0, 23, 59, 59);
				break;

			default:
				break;
		}

		var format = window[inputFrom.name+"_bTime"] ? BX.message('FORMAT_DATETIME') : BX.message('FORMAT_DATE');

		if(from)
		{
			inputFrom.value = BX.formatDate(from, format);
			BX.addClass(inputFrom,"adm-calendar-inp-setted");
		}

		if(to)
		{
			inputTo.value = BX.formatDate(to, format);
			BX.addClass(inputTo,"adm-calendar-inp-setted");
		}
	}
}


BX.JCCalendar = function()
{
	this.params = {};

	this.bAmPm = BX.isAmPmMode();

	this.popup = null;
	this.popup_month = null;
	this.popup_year = null;

	this.value = null;

	this.control_id = Math.random();

	this._layers = {};
	this._current_layer = null;

	this.DIV = null;
	this.PARTS = {};

	this.weekStart = 0;
	this.numRows = 6;

	this._create = function(params)
	{
		this.popup = new BX.PopupWindow('calendar_popup_' + this.control_id, params.node, {
			closeByEsc: true,
			autoHide: false,
			content: this._get_content(),
			zIndex: 3000,
			bindOptions: {forceBindPosition: true}
		});

		BX.bind(this.popup.popupContainer, 'click', this.popup.cancelBubble);
	};

	this._auto_hide_disable = function()
	{
		BX.unbind(document, 'click', BX.proxy(this._auto_hide, this));
	}

	this._auto_hide_enable = function()
	{
		BX.bind(document, 'click', BX.proxy(this._auto_hide, this));
	}

	this._auto_hide = function(e)
	{
		this._auto_hide_disable();
		this.popup.close();
	}

	this._get_content = function()
	{
		var _layer_onclick = BX.delegate(function(e) {
			e = e||window.event;
			this.SetDate(new Date(parseInt(BX.proxy_context.getAttribute('data-date'))), e.type=='dblclick')
		}, this);

		this.DIV = BX.create('DIV', {
			props: {className: 'bx-calendar'},
			children: [
				BX.create('DIV', {
					props: {
						className: 'bx-calendar-header'
					},
					children: [
						BX.create('A', {
							attrs: {href: 'javascript:void(0)'},
							props: {className: 'bx-calendar-left-arrow'},
							events: {click: BX.proxy(this._prev, this)}
						}),

						BX.create('SPAN', {
							props: {className: 'bx-calendar-header-content'},
							children: [
								(this.PARTS.MONTH = BX.create('A', {
									attrs: {href: 'javascript:void(0)'},
									props: {className: 'bx-calendar-top-month'},
									events: {click: BX.proxy(this._menu_month, this)}
								})),

								(this.PARTS.YEAR = BX.create('A', {
									attrs: {href: 'javascript:void(0)'},
									props: {className: 'bx-calendar-top-year'},
									events: {click: BX.proxy(this._menu_year, this)}
								}))
							]
						}),

						BX.create('A', {
							attrs: {href: 'javascript:void(0)'},
							props: {className: 'bx-calendar-right-arrow'},
							events: {click: BX.proxy(this._next, this)}
						})
					]
				}),

				(this.PARTS.WEEK = BX.create('DIV', {
					props: {
						className: 'bx-calendar-name-day-wrap'
					}
				})),

				(this.PARTS.LAYERS = BX.create('DIV', {
					props: {
						className: 'bx-calendar-cell-block'
					},
					events: {
						click: BX.delegateEvent({className: 'bx-calendar-cell'}, _layer_onclick),
						dblclick: BX.delegateEvent({className: 'bx-calendar-cell'}, _layer_onclick)
					}
				})),

				(this.PARTS.TIME = BX.create('DIV', {
					props: {
						className: 'bx-calendar-set-time-wrap'
					},
					events: {
						click: BX.delegateEvent(
							{attr: 'data-action'},
							BX.delegate(this._time_actions, this)
						)
					},
					html: '<a href="javascript:void(0)" data-action="time_show" class="bx-calendar-set-time"><i></i>'+BX.message('CAL_TIME_SET')+'</a><div class="bx-calendar-form-block"><span class="bx-calendar-form-text">'+BX.message('CAL_TIME')+'</span><span class="bx-calendar-form"><input type="text" class="bx-calendar-form-input" maxwidth="2" onkeyup="BX.calendar.get()._check_time()" /><span class="bx-calendar-form-separator"></span><input type="text" class="bx-calendar-form-input" maxwidth="2" onkeyup="BX.calendar.get()._check_time()" />'+(this.bAmPm?'<span class="bx-calendar-AM-PM-block"><span class="bx-calendar-AM-PM-text" data-action="time_ampm"></span><span class="bx-calendar-form-arrow-r"><a href="javascript:void(0)" class="bx-calendar-form-arrow-top" data-action="time_ampm_up"><i></i></a><a href="javascript:void(0)" class="bx-calendar-form-arrow-bottom" data-action="time_ampm_down"><i></i></a></span></span>':'')+'</span><a href="javascript:void(0)" data-action="time_hide" class="bx-calendar-form-close"><i></i></a></div>'
				})),

				BX.create('DIV', {
					props: {className: 'bx-calendar-button-block'},
					events: {
						click: BX.delegateEvent(
							{attr: 'data-action'},
							BX.delegate(this._button_actions, this)
						)
					},
					html: '<a href="javascript:void(0)" class="bx-calendar-button bx-calendar-button-select" data-action="submit"><span class="bx-calendar-button-left"></span><span class="bx-calendar-button-text">'+BX.message('CAL_BUTTON')+'</span><span class="bx-calendar-button-right"></span></a><a href="javascript:void(0)" class="bx-calendar-button bx-calendar-button-cancel" data-action="cancel"><span class="bx-calendar-button-left"></span><span class="bx-calendar-button-text">'+BX.message('JS_CORE_WINDOW_CLOSE')+'</span><span class="bx-calendar-button-right"></span></a>'
				})
			]
		});

		this.PARTS.TIME_INPUT_H = BX.findChild(this.PARTS.TIME, {tag: 'INPUT'}, true);
		this.PARTS.TIME_INPUT_M = this.PARTS.TIME_INPUT_H.nextSibling.nextSibling;

		if (this.bAmPm)
			this.PARTS.TIME_AMPM = this.PARTS.TIME_INPUT_M.nextSibling.firstChild;

		var spinner = (new BX.JCSpinner({
			input: this.PARTS.TIME_INPUT_H,
			callback_change: BX.proxy(this._check_time, this),
			bSaveValue: false
		})).Show();
		spinner.className = 'bx-calendar-form-arrow-l';
		this.PARTS.TIME_INPUT_H.parentNode.insertBefore(spinner, this.PARTS.TIME_INPUT_H);

		spinner = (new BX.JCSpinner({
			input: this.PARTS.TIME_INPUT_M,
			callback_change: BX.proxy(this._check_time, this),
			bSaveValue: true
		})).Show();
		spinner.className = 'bx-calendar-form-arrow-r';
		if (!this.PARTS.TIME_INPUT_M.nextSibling)
			this.PARTS.TIME_INPUT_M.parentNode.appendChild(spinner);
		else
			this.PARTS.TIME_INPUT_M.parentNode.insertBefore(spinner, this.PARTS.TIME_INPUT_M.nextSibling);

		for (var i = 0; i < 7; i++)
		{
			this.PARTS.WEEK.appendChild(BX.create('SPAN', {
				props: {
					className: 'bx-calendar-name-day'
				},
				text: BX.message('DOW_' + ((i + this.weekStart) % 7))
			}));
		}

		return this.DIV;
	};

	this._time_actions = function()
	{
		var v;
		switch (BX.proxy_context.getAttribute('data-action'))
		{
			case 'time_show':
				BX.addClass(this.PARTS.TIME, 'bx-calendar-set-time-opened');
				this.popup.adjustPosition();
			break;
			case 'time_hide':
				BX.removeClass(this.PARTS.TIME, 'bx-calendar-set-time-opened');
				this.popup.adjustPosition();
			break;
			case 'time_ampm':
				this.PARTS.TIME_AMPM.innerHTML = this.PARTS.TIME_AMPM.innerHTML == 'AM' ? 'PM' : 'AM';
			break;
			case 'time_ampm_up':
				this._check_time({bSaveValue: false}, null, 12);
				return;
			break;
			case 'time_ampm_down':
				this._check_time({bSaveValue: false}, null, -12);
				return;
			break;
		}

		this._check_time();
	};

	this._button_actions = function()
	{
		switch (BX.proxy_context.getAttribute('data-action'))
		{
			case 'submit':
				this.SaveValue();
			break;
			case 'cancel':
				this.Close();
			break;
		}
	};

	this._check_time = function(params, value, direction)
	{
		var h = parseInt(this.PARTS.TIME_INPUT_H.value.substring(0,5),10)||0,
			m = parseInt(this.PARTS.TIME_INPUT_M.value.substring(0,5),10)||0,
			bChanged = false;

		if (!!params && !params.bSaveValue)
		{
			this.value.setHours(this.value.getHours() + direction);
		}
		else if (!isNaN(h))
		{
			if (this.bAmPm)
			{
				if (h != 12 && this.PARTS.TIME_AMPM.innerHTML == 'PM')
				{
					h += 12;
				}
			}

			bChanged = true;
			this.value.setHours(h % 24);
		}

		if (!isNaN(m))
		{
			bChanged = true;
			this.value.setMinutes(m % 60);
		}

		if (bChanged)
		{
			this.SetValue(this.value);
		}
	};

	this._set_layer = function()
	{
		var layerId = parseInt(this.value.getFullYear() + '' + BX.util.str_pad_left(this.value.getMonth()+'', 2, "0"));

		if (!this._layers[layerId])
		{
			this._layers[layerId] = this._create_layer();
			this._layers[layerId].BXLAYERID = layerId;
		}

		if (this._current_layer)
		{
			var v = new Date(this.value.valueOf());
			v.setHours(0); v.setMinutes(0);

			var cur_value = BX.findChild(this._layers[layerId], {
					tag: 'A',
					className: 'bx-calendar-active'
				}, true),
				new_value = BX.findChild(this._layers[layerId], {
					tag: 'A',
					attr: {
						'data-date' : v.valueOf() + ''
					}
				}, true);

			if (cur_value)
			{
				BX.removeClass(cur_value, 'bx-calendar-active');
			}

			if (new_value)
			{
				BX.addClass(new_value, 'bx-calendar-active');
			}

			this._replace_layer(this._current_layer, this._layers[layerId]);
		}
		else
		{
			this.PARTS.LAYERS.appendChild(this._layers[layerId]);
		}

		this._current_layer = this._layers[layerId];
	};

	this._replace_layer = function(old_layer, new_layer)
	{
		if (old_layer != new_layer)
		{
			if (!BX.browser.IsIE() || BX.browser.IsDoctype())
			{
				var dir = old_layer.BXLAYERID > new_layer.BXLAYERID ? 1 : -1;

				var old_top = 0;
					new_top = -dir * old_layer.offsetHeight;

				old_layer.style.position = 'relative';
				old_layer.style.top = "0px";
				old_layer.style.zIndex = 5;

				new_layer.style.position = 'absolute';
				new_layer.style.top = new_top + 'px';
				new_layer.style.zIndex = 6;

				this.PARTS.LAYERS.appendChild(new_layer);

				var delta = 15;

				var f
				(f = function() {
					new_top += dir * delta;
					old_top += dir * delta;

					if (dir * new_top < 0)
					{
						old_layer.style.top = old_top + 'px';
						new_layer.style.top = new_top + 'px';
						setTimeout(f, 10);
					}
					else
					{
						old_layer.parentNode.removeChild(old_layer);

						new_layer.style.top = "0px";
						new_layer.style.position = 'static';
						new_layer.style.zIndex = 0;
					}
				})();
			}
			else
			{
				this.PARTS.LAYERS.replaceChild(new_layer, old_layer);
			}
		}
	};

	this._create_layer = function()
	{
		var l = BX.create('DIV', {
			props: {
				className: 'bx-calendar-layer'
			}
		});

		var month_start = new Date(this.value);
		month_start.setHours(0);
		month_start.setMinutes(0);

		month_start.setDate(1);

		if (month_start.getDay() != this.weekStart)
		{
			var d = month_start.getDay() - this.weekStart;
			d += d < 0 ? 7 : 0;
			month_start.setDate(month_start.getDate()-d);
		}

		var cur_month = this.value.getMonth(),
			cur_day = this.value.getDate(),
			s = '';
		for (var i = 0; i < this.numRows; i++)
		{
			s += '<div class="bx-calendar-range'
				+(i == this.numRows-1 ? ' bx-calendar-range-noline' : '')
				+'">';

			for (var j = 0; j < 7; j++)
			{
				var d = month_start.getDate(),
					wd = month_start.getDay(),
					className = 'bx-calendar-cell';
				if (cur_month != month_start.getMonth())
					className += ' bx-calendar-date-hidden';
				else if (cur_day == d)
					className += ' bx-calendar-active';


				if (wd == 0 || wd == 6)
					className += ' bx-calendar-weekend';

				s += '<a href="javascript:void(0)" class="'+className+'" data-date="' + month_start.valueOf() + '">' + d + '</a>';

				month_start.setDate(month_start.getDate()+1);
			}
			s += '</div>';
		}

		l.innerHTML = s;

		return l;
	}

	this._prev = function()
	{
		this.SetMonth(this.value.getMonth()-1);
	};

	this._next = function()
	{
		this.SetMonth(this.value.getMonth()+1);
	};

	this._menu_month_content = function()
	{
		var months = '', cur_month = this.value.getMonth(), i;
		for (i=0; i<12; i++)
		{
			months += '<a href="javascript:void(0)" class="bx-calendar-month'+(i == cur_month ? ' bx-calendar-month-active' : '')+'" onclick="BX.calendar.get().SetMonth('+i+')">'+BX.message('MONTH_' + (i+1))+'</a>';
		}

		return '<div class="bx-calendar-month-popup"><div class="bx-calendar-month-title" onclick="BX.calendar.get().popup_month.close();">'+BX.message('MONTH_' + (this.value.getMonth()+1))+'</div><div class="bx-calendar-month-content">'+months+'</div></div>';
	};

	this._menu_month = function()
	{
		if (!this.popup_month)
		{
			this.popup_month = BX.PopupWindowManager.create(
				'calendar_popup_month_' + this.control_id, this.PARTS.MONTH,
				{
					content: this._menu_month_content(),
					zIndex: 3001,
					closeByEsc: true,
					autoHide: true,
					offsetTop: -29,
					offsetLeft: -1
				}
			);

			this.popup_month.BXMONTH = this.value.getMonth();
		}
		else if (this.popup_month.BXMONTH != this.value.getMonth())
		{
			this.popup_month.setContent(this._menu_month_content());
			this.popup_month.BXMONTH = this.value.getMonth();
		}

		this.popup_month.show();
	};

	this._menu_year_content = function()
	{
		var s = '<div class="bx-calendar-year-popup"><div class="bx-calendar-year-title" onclick="BX.calendar.get().popup_year.close();">'+this.value.getFullYear()+'</div><div class="bx-calendar-year-content" id="bx-calendar-year-content">'

			for (var i=-3; i <= 3; i++)
			{
				s += '<a href="javascript:void(0)" class="bx-calendar-year-number'+(i==0?' bx-calendar-year-active':'')+'" onclick="BX.calendar.get().SetYear('+(this.value.getFullYear()-i)+')">'+(this.value.getFullYear()-i)+'</a>'
			}

			s += '</div><input type="text" class="bx-calendar-year-input" onkeyup="if(this.value>=1900&&this.value<=2100)BX.calendar.get().SetYear(this.value);" maxlength="4" /></div>';

		return s;
	};

	this._menu_year = function()
	{
		if (!this.popup_year)
		{
			this.popup_year = BX.PopupWindowManager.create(
				'calendar_popup_year_' + this.control_id, this.PARTS.YEAR,
				{
					content: this._menu_year_content(),
					zIndex: 3001,
					closeByEsc: true,
					autoHide: true,
					offsetTop: -29,
					offsetLeft: -1
				}
			);

			this.popup_year.BXYEAR = this.value.getFullYear();
		}
		else if (this.popup_year.BXYEAR != this.value.getFullYear())
		{
			this.popup_year.setContent(this._menu_year_content());
			this.popup_year.BXYEAR = this.value.getFullYear();
		}

		this.popup_year.show();
	};

	this._check_date = function(v)
	{
		if (BX.type.isString(v))
			v = BX.parseDate(v);

		if (!BX.type.isDate(v) || isNaN(v.valueOf()))
		{
			v = new Date();
			if (this.params.bHideTime)
			{
				v.setHours(0);
				v.setMinutes(0);
			}
		}

		//v = BX.date.convertToUTC(v);
		v.setMilliseconds(0);
		v.setSeconds(0);

		v.BXCHECKED = true;

		return v;
	};
};

BX.JCCalendar.prototype.Show = function(params)
{
	if (!BX.isReady)
	{
		BX.ready(BX.delegate(function() {this.Show(params)}, this));
		return;
	}

	params.node = params.node||document.body;

	if (BX.type.isNotEmptyString(params.node))
	{
		var n = BX(params.node);
		if (!n)
		{
			n = document.getElementsByName(params.node);
			if (n && n.length > 0)
			{
				n = n[0]
			}
		}
		params.node = n;
	}

	if (!params.node)
		return;

	if (!!params.field)
	{
		if (BX.type.isString(params.field))
		{
			var n = BX(params.field);
			if (!!n)
			{
				params.field = n;
			}
			else
			{
				if (params.form)
				{
					if (BX.type.isString(params.form))
					{
						params.form = document.forms[params.form];
					}
				}

				if (BX.type.isDomNode(params.form) && !!params.form[params.field])
				{
					params.field = params.form[params.field];
				}
				else
				{
					var n = document.getElementsByName(params.field);
					if (n && n.length > 0)
					{
						n = n[0];
						params.field = n;
					}
				}
			}

			if (BX.type.isString(params.field))
			{
				params.field = BX(params.field);
			}
		}
	}

	var bShow = !this.popup || !this.popup.isShown() || this.params.node != params.node;

	this.params = params;

	this.params.bTime = typeof this.params.bTime == 'undefined' ? true : !!this.params.bTime;
	this.params.bHideTime = typeof this.params.bHideTime == 'undefined' ? true : !!this.params.bHideTime;

	this.weekStart = parseInt(this.params.weekStart || this.params.weekStart || BX.message('WEEK_START'));
	if (isNaN(this.weekStart))
		this.weekStart = 1;

	if (!this.popup)
	{
		this._create(this.params);
	}
	else
	{
		this.popup.setBindElement(this.params.node);
	}

	var bHideTime = !!this.params.bHideTime;
	if (this.params.value)
	{
		this.SetValue(this.params.value);
		bHideTime = this.value.getHours() <= 0 && this.value.getMinutes() <= 0;
	}
	else if (this.params.field)
	{
		this.SetValue(this.params.field.value);
		bHideTime = this.value.getHours() <= 0 && this.value.getMinutes() <= 0;
	}
	else if (!!this.params.currentTime)
	{
		this.SetValue(this.params.currentTime);
	}
	else
	{
		this.SetValue(new Date());
	}

	if (!!this.params.bTime)
		BX.removeClass(this.DIV, 'bx-calendar-time-disabled');
	else
		BX.addClass(this.DIV, 'bx-calendar-time-disabled');

	if (!!bHideTime)
		BX.removeClass(this.PARTS.TIME, 'bx-calendar-set-time-opened');
	else
		BX.addClass(this.PARTS.TIME, 'bx-calendar-set-time-opened');

	if (bShow)
	{
		this._auto_hide_disable();
		this.popup.show();
		setTimeout(BX.proxy(this._auto_hide_enable, this))
	}

	this.params.bSetFocus = typeof this.params.bSetFocus == 'undefined' ? true : !!this.params.bSetFocus;
	if(this.params.bSetFocus)
		params.node.blur();

	return this;
};

BX.JCCalendar.prototype.SetDay = function(d)
{
	this.value.setDate(d);
	return this.SetValue(this.value);
};

BX.JCCalendar.prototype.SetMonth = function(m)
{
	if (this.popup_month)
		this.popup_month.close();

	this.value.setMonth(m);

	if(m < 0)
		m += 12;
	else if (m >= 12)
		m -= 12;

	while(this.value.getMonth(m) > m)
	{
		this.value.setDate(this.value.getDate()-1);
	}

	return this.SetValue(this.value);
};

BX.JCCalendar.prototype.SetYear = function(y)
{
	if (this.popup_year)
		this.popup_year.close();
	this.value.setFullYear(y);
	return this.SetValue(this.value);
};

BX.JCCalendar.prototype.SetDate = function(v, bSet)
{
	v = this._check_date(v);
	v.setHours(this.value.getHours());
	v.setMinutes(this.value.getMinutes());
	v.setSeconds(this.value.getSeconds());

	if (this.params.bTime && !bSet)
	{
		return this.SetValue(v);
	}
	else
	{
		this.SetValue(v);
		this.SaveValue();
	}
};

BX.JCCalendar.prototype.SetValue = function(v)
{
	this.value = (v && v.BXCHECKED) ? v : this._check_date(v);

	this.PARTS.MONTH.innerHTML = BX.message('MONTH_' + (this.value.getMonth()+1));
	this.PARTS.YEAR.innerHTML = this.value.getFullYear();

	if (!!this.params.bTime)
	{
		var h = this.value.getHours();
		if (this.bAmPm)
		{
			if (h >= 12)
			{
				this.PARTS.TIME_AMPM.innerHTML = 'PM';

				if (h != 12)
					h -= 12;
			}
			else
			{
				this.PARTS.TIME_AMPM.innerHTML = 'AM'

				if (h == 0)
					h = 12;
			}
		}

		this.PARTS.TIME_INPUT_H.value = BX.util.str_pad_left(h.toString(), 2, "0");
		this.PARTS.TIME_INPUT_M.value = BX.util.str_pad_left(this.value.getMinutes().toString(), 2, "0");
	}

	this._set_layer();

	return this;
};

BX.JCCalendar.prototype.SaveValue = function()
{
	if (this.popup_month)
		this.popup_month.close();
	if (this.popup_year)
		this.popup_year.close();

	var bSetValue = true;
	if (!!this.params.callback)
	{
		var res = this.params.callback.apply(this, [this.value]);
		if (res === false)
			bSetValue = false;
	}

	if (bSetValue)
	{
		var bTime = !!this.params.bTime && BX.hasClass(this.PARTS.TIME, 'bx-calendar-set-time-opened');

		if (this.params.field)
		{
			this.params.field.value = BX.calendar.ValueToString(this.value, bTime);
			BX.fireEvent(this.params.field, 'change');
		}

		this.popup.close();

		if (!!this.params.callback_after)
		{
			this.params.callback_after.apply(this, [this.value, bTime]);
		}
	}

	return this;
};

BX.JCCalendar.prototype.Close = function()
{
	if (!!this.popup)
		this.popup.close();

	return this;
};

BX.JCSpinner = function(params)
{
	params = params || {};
	this.params = {
		input: params.input || null,

		delta: params.delta || 1,

		timeout_start: params.timeout_start || 1000,
		timeout_cont: params.timeout_cont || 150,

		callback_start: params.callback_start || null,
		callback_change: params.callback_change || null,
		callback_finish: params.callback_finish || null,

		bSaveValue: typeof params.bSaveValue == 'undefined' ? !!params.input : !!params.bSaveValue
	}

	this.mousedown = false;
	this.direction = 1;
}

BX.JCSpinner.prototype.Show = function()
{
	this.node = BX.create('span', {
		events: {
			mousedown: BX.delegateEvent(
				{attr: 'data-dir'},
				BX.delegate(this.Start, this)
			)
		},
		html: '<a href="javascript:void(0)" class="bx-calendar-form-arrow bx-calendar-form-arrow-top" data-dir="1"><i></i></a><a href="javascript:void(0)" class="bx-calendar-form-arrow bx-calendar-form-arrow-bottom" data-dir="-1"><i></i></a>'
	});
	return this.node;
}

BX.JCSpinner.prototype.Start = function()
{
	this.mousedown = true;
	this.direction = BX.proxy_context.getAttribute('data-dir') > 0 ? 1 : -1;
	BX.bind(document, "mouseup", BX.proxy(this.MouseUp, this));
	this.ChangeValue(true);
}

BX.JCSpinner.prototype.ChangeValue = function(bFirst)
{
	if(!this.mousedown)
		return;

	if (this.params.input)
	{
		var v = parseInt(this.params.input.value, 10) + this.params.delta * this.direction;

		if (this.params.bSaveValue)
			this.params.input.value = v;

		if (!!bFirst && this.params.callback_start)
			this.params.callback_start(this.params, v, this.direction);

		if (this.params.callback_change)
			this.params.callback_change(this.params, v, this.direction);

		setTimeout(
			BX.proxy(this.ChangeValue, this),
			!!bFirst ? this.params.timeout_start : this.params.timeout_cont
		);
	}
}

BX.JCSpinner.prototype.MouseUp = function()
{
	this.mousedown = false;
	BX.unbind(document, "mouseup", BX.proxy(this.MouseUp, this));

	if (this.params.callback_finish)
		this.params.callback_finish(this.params, this.params.input.value);
}

/**************** compatibility hacks ***************************/

window.jsCalendar = {
	Show: function(obj, field, fieldFrom, fieldTo, bTime, serverTime, form_name, bHideTimebar)
	{
		return BX.calendar({
			node: obj, field: field, form: form_name, bTime: !!bTime, currentTime: serverTime, bHideTimebar: !!bHideTimebar
		});
	},

	ValueToString: BX.calendar.ValueToString
}


/************ clock popup transferred from timeman **************/

BX.CClockSelector = function(params)
{
	this.params = params;

	this.params.popup_buttons = this.params.popup_buttons || [
		new BX.PopupWindowButton({
			text : BX.message('CAL_BUTTON'),
			className : "popup-window-button-create",
			events : {click : BX.proxy(this.setValue, this)}
		})
	];

	this.isReady = false;

	this.WND = new BX.PopupWindow(
		this.params.popup_id || 'clock_selector_popup',
		this.params.node,
		this.params.popup_config || {
			titleBar: {content: BX.create('SPAN', {text: BX.message('CAL_TIME')})},
			offsetLeft: -45,
			offsetTop: -135,
			autoHide: true,
			closeIcon: true,
			closeByEsc: true,
			zIndex: this.params.zIndex
		}
	);

	this.SHOW = false;
	BX.addCustomEvent(this.WND, "onPopupClose", BX.delegate(this.onPopupClose, this));

	this.obClocks = {};
	this.CLOCK_ID = this.params.clock_id || 'clock_selector';
};

BX.CClockSelector.prototype.Show = function()
{
	if (!this.isReady)
	{
		//BX.timeman.showWait(this.parent.DIV);

		BX.addCustomEvent('onClockRegister', BX.proxy(this.onClockRegister, this));
		return BX.ajax.get('/bitrix/tools/clock_selector.php', {start_time: this.params.start_time, clock_id: this.CLOCK_ID, sessid: BX.bitrix_sessid()}, BX.delegate(this.Ready, this));
	}

	this.WND.setButtons(this.params.popup_buttons);
	this.WND.show();

	this.SHOW = true;

	if (window['bxClock_' + this.obClocks[this.CLOCK_ID]])
	{
		setTimeout("window['bxClock_" + this.obClocks[this.CLOCK_ID] + "'].CalculateCoordinates()", 40);
	}

	return true;
};

BX.CClockSelector.prototype.onClockRegister = function(obClocks)
{
	if (obClocks[this.CLOCK_ID])
	{
		this.obClocks[this.CLOCK_ID] = obClocks[this.CLOCK_ID];
		BX.removeCustomEvent('onClockRegister', BX.proxy(this.onClockRegister, this));
	}
};

BX.CClockSelector.prototype.Ready = function(data)
{
	this.content = this.CreateContent(data);
	this.WND.setContent(this.content);

	this.isReady = true;
	//BX.timeman.closeWait();

	setTimeout(BX.proxy(this.Show, this), 30);
};

BX.CClockSelector.prototype.CreateContent = function(data)
{
	return BX.create('DIV', {
		events: {click: BX.PreventDefault},
		html:
			'<div class="bx-tm-popup-clock">' + data + '</div>'
	});
};

BX.CClockSelector.prototype.setValue = function(e)
{
	if (this.params.callback)
	{
		var input = BX.findChild(this.content, {tagName: 'INPUT'}, true);
		this.params.callback.apply(this.params.node, [input.value]);
	}

	return BX.PreventDefault(e);
};

BX.CClockSelector.prototype.closeWnd = function(e)
{
	this.WND.close();
	return (e || window.event) ? BX.PreventDefault(e) : true;
};

BX.CClockSelector.prototype.setNode = function(node)
{
	this.WND.setBindElement(node);
};

BX.CClockSelector.prototype.setTime = function(timestamp)
{
	this.params.start_time = timestamp;
	if (window['bxClock_' + this.obClocks[this.CLOCK_ID]])
	{
		window['bxClock_' +  this.obClocks[this.CLOCK_ID]].SetTime(parseInt(timestamp/3600), parseInt((timestamp%3600)/60));
	}
};

BX.CClockSelector.prototype.setCallback = function(cb)
{
	this.params.callback = cb;
};

BX.CClockSelector.prototype.onPopupClose = function()
{
	this.SHOW = false;
};

})();
/* End */
;