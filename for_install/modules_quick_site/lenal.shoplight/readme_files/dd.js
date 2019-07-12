;(function(){

if (window.jsDD)
	return;

jsDD = {
	arObjects: [],
	arDestinations: [],
	arDestinationsPriority: [],

	arContainers: [],
	arContainersPos: [],

	current_dest_index: false,
	current_node: null,

	wndSize: null,

	bStarted: false,
	bDisable: false,
	bDisableDestRefresh: false,

	bEscPressed: false,

	bScrollWindow: false,
	scrollViewTimer: null,
	scrollViewConfig: {
		checkerTimeout: 30,
		scrollZone: 25,
		scrollBy: 25,
		scrollContainer: null,
		bScrollH: true,
		bScrollV: true,
		pos: null
	},

	setScrollWindow: function(val)
	{
		jsDD.bScrollWindow = !!val;
		if (BX.type.isDomNode(val))
		{
			jsDD.scrollViewConfig.scrollContainer = val;
			jsDD.scrollViewConfig.pos = BX.pos(val);

			var s = BX.style(val, 'overflow') || 'visible',
				s1 = BX.style(val, 'overflow-x') || 'visible',
				s2 = BX.style(val, 'overflow-y') || 'visible';

			jsDD.scrollViewConfig.bScrollH = s != 'visible' || s1 != 'visible';
			jsDD.scrollViewConfig.bScrollV = s != 'visible' || s2 != 'visible';
		}
	},

	Reset: function()
	{
		jsDD.arObjects = [];
		jsDD.arDestinations = [];
		arDestinationsPriority = [];
		jsDD.bStarted = false;
		jsDD.current_node = null;
		jsDD.current_dest_index = false;
		jsDD.bDisableDestRefresh = false;
		jsDD.bDisable = false;
		jsDD.x = null;
		jsDD.y = null;
		jsDD.start_x = null;
		jsDD.start_y = null;
		jsDD.wndSize = null;

		jsDD.bEscPressed = false;

		clearInterval(jsDD.scrollViewTimer)
		jsDD.bScrollWindow = false;
		jsDD.scrollViewTimer = null;
		jsDD.scrollViewConfig.scrollContainer = null;
	},

	registerObject: function (obNode)
	{
		obNode.onmousedown = jsDD.startDrag;
		obNode.__bxddid = jsDD.arObjects.length;

		jsDD.arObjects[obNode.__bxddid] = obNode;
	},

	registerDest: function (obDest, priority)
	{
		if (!priority)
			priority = 100;

		obDest.__bxddeid = jsDD.arDestinations.length;
		obDest.__bxddpriority = priority;

		jsDD.arDestinations[obDest.__bxddeid] = obDest;
		if (!jsDD.arDestinationsPriority[priority])
			jsDD.arDestinationsPriority[priority] = [obDest.__bxddeid]
		else
			jsDD.arDestinationsPriority[priority].push(obDest.__bxddeid);

		jsDD.refreshDestArea(obDest.__bxddeid);
	},

	disableDest: function(obDest)
	{
		if (obDest.__bxddeid)
		{
			obDest.__bxdddisabled = true;
		}
	},

	enableDest: function(obDest)
	{
		if (obDest.__bxddeid)
		{
			obDest.__bxdddisabled = false;
		}
	},

	registerContainer: function (obCont)
	{
		jsDD.arContainers[jsDD.arContainers.length] = obCont;
	},

	getContainersScrollPos: function(x, y)
	{
		var pos = {'left':0, 'top':0};
		for(var i=0, n=jsDD.arContainers.length; i<n; i++)
		{
			if(jsDD.arContainers[i] && x >= jsDD.arContainersPos[i]["left"] && x <= jsDD.arContainersPos[i]["right"] && y >= jsDD.arContainersPos[i]["top"] && y <= jsDD.arContainersPos[i]["bottom"])
			{
				pos.left = jsDD.arContainers[i].scrollLeft;
				pos.top = jsDD.arContainers[i].scrollTop;
			}
		}
		return pos;
	},

	setContainersPos: function()
	{
		for(var i=0, n=jsDD.arContainers.length; i<n; i++)
		{
			if(jsDD.arContainers[i])
				jsDD.arContainersPos[i] = BX.pos(jsDD.arContainers[i]);
		}
	},

	refreshDestArea: function(id)
	{
		if (id && typeof (id) == "object" && typeof (id.__bxddeid) != 'undefined')
		{
			id = id.__bxddeid;
		}

		if (typeof id == 'undefined')
		{
			for (var i = 0, cnt = jsDD.arDestinations.length; i < cnt; i++)
			{
				jsDD.refreshDestArea(i);
			}
		}
		else
		{
			if (null == jsDD.arDestinations[id])
				return;

			var arPos = BX.pos(jsDD.arDestinations[id]);
			jsDD.arDestinations[id].__bxpos = [arPos.left, arPos.top, arPos.right, arPos.bottom];
		}
	},

	_checkEsc: function(e)
	{
		e = e||window.event;
		if (jsDD.bStarted && e.keyCode == 27)
		{
			jsDD.stopCurrentDrag();
		}
	},

	stopCurrentDrag: function()
	{
		if (jsDD.bStarted)
		{
			jsDD.bEscPressed = true;
			jsDD.stopDrag();
		}
	},

	/* scroll checkers */

	_onscroll: function() {
		jsDD.wndSize = BX.GetWindowSize();
	},

	_checkScroll: function()
	{
		if (jsDD.bScrollWindow)
		{
			var pseudo_e = {
					clientX: jsDD.x - jsDD.wndSize.scrollLeft,
					clientY: jsDD.y - jsDD.wndSize.scrollTop
				},
				bChange = false,
				d = jsDD.scrollViewConfig.scrollZone;

			// check whether window scroll needed
			if (pseudo_e.clientY < d && jsDD.wndSize.scrollTop > 0)
			{
				window.scrollBy(0, -jsDD.scrollViewConfig.scrollBy);
				bChange = true;
			}

			if (pseudo_e.clientY > jsDD.wndSize.innerHeight - d && jsDD.wndSize.scrollTop < jsDD.wndSize.scrollHeight - jsDD.wndSize.innerHeight)
			{
				window.scrollBy(0, jsDD.scrollViewConfig.scrollBy);
				bChange = true;
			}

			if (pseudo_e.clientX < d && jsDD.wndSize.scrollLeft > 0)
			{
				window.scrollBy(-jsDD.scrollViewConfig.scrollBy, 0);
				bChange = true;
			}

			if (pseudo_e.clientX > jsDD.wndSize.innerWidth - d && jsDD.wndSize.scrollLeft < jsDD.wndSize.scrollWidth - jsDD.wndSize.innerWidth)
			{
				window.scrollBy(jsDD.scrollViewConfig.scrollBy, 0);
				bChange = true;
			}

			// check whether container scroll needed

			if (jsDD.scrollViewConfig.scrollContainer)
			{
				var c = jsDD.scrollViewConfig.scrollContainer;

				if (jsDD.scrollViewConfig.bScrollH)
				{
					if (pseudo_e.clientX + jsDD.wndSize.scrollLeft < jsDD.scrollViewConfig.pos.left + d && c.scrollLeft > 0)
					{
						c.scrollLeft -= jsDD.scrollViewConfig.scrollBy;
						bChange = true;
					}

					if (pseudo_e.clientX + jsDD.wndSize.scrollLeft > jsDD.scrollViewConfig.pos.right - d
						&& c.scrollLeft < c.scrollWidth - c.offsetWidth)
					{
						c.scrollLeft += jsDD.scrollViewConfig.scrollBy;
						bChange = true;
					}
				}

				if (jsDD.scrollViewConfig.bScrollV)
				{
					if (pseudo_e.clientY + jsDD.wndSize.scrollTop < jsDD.scrollViewConfig.pos.top + d && c.scrollTop > 0)
					{
						c.scrollTop -= jsDD.scrollViewConfig.scrollBy;
						bChange = true;
					}

					if (pseudo_e.clientY + jsDD.wndSize.scrollTop > jsDD.scrollViewConfig.pos.bottom - d
						&& c.scrollTop < c.scrollHeight - c.offsetHeight)
					{
						c.scrollTop += jsDD.scrollViewConfig.scrollBy;
						bChange = true;
					}
				}
			}

			if (bChange)
			{
				jsDD._onscroll();
				jsDD.drag(pseudo_e);
			}
		}
	},

	/* DD process */

	startDrag: function(e)
	{
		if (jsDD.bDisable)
			return true;

		e = e || window.event;

		if (!(BX.getEventButton(e)&BX.MSLEFT))
			return true;

		jsDD.current_node = null;
		if (e.currentTarget)
		{
			jsDD.current_node = e.currentTarget;
			if (null == jsDD.current_node || null == jsDD.current_node.__bxddid)
			{
				jsDD.current_node = null;
				return;
			}
		}
		else
		{
			jsDD.current_node = e.srcElement;
			if (null == jsDD.current_node)
				return;

			while (null == jsDD.current_node.__bxddid)
			{
				jsDD.current_node = jsDD.current_node.parentNode;
				if (jsDD.current_node.tagName == 'BODY')
					return;
			}
		}

		jsDD.bStarted = false;
		jsDD.bPreStarted = true;

		jsDD.wndSize = BX.GetWindowSize();

		jsDD.start_x = e.clientX + jsDD.wndSize.scrollLeft;
		jsDD.start_y = e.clientY + jsDD.wndSize.scrollTop;

		BX.bind(document, "mouseup", jsDD.stopDrag);
		BX.bind(document, "mousemove", jsDD.drag);
		BX.bind(window, 'scroll', jsDD._onscroll);

		if(document.body.setCapture)
			document.body.setCapture();

		jsDD.denySelection();

		if (!jsDD.bDisableDestRefresh)
			jsDD.refreshDestArea();

		jsDD.setContainersPos();

		return BX.PreventDefault(e);
	},

	start: function()
	{
		if (jsDD.bDisable)
			return true;

		document.body.style.cursor = 'move';

		if (jsDD.current_node.onbxdragstart)
			jsDD.current_node.onbxdragstart();

		for (var i = 0, cnt = jsDD.arDestinations.length; i < cnt; i++)
		{
			if (jsDD.arDestinations[i].onbxdestdragstart)
				jsDD.arDestinations[i].onbxdestdragstart(jsDD.current_node);
		}

		jsDD.bStarted = true;
		jsDD.bPreStarted = false;

		if (jsDD.bScrollWindow)
		{
			if (jsDD.scrollViewTimer)
				clearInterval(jsDD.scrollViewTimer);

			jsDD.scrollViewTimer = setInterval(jsDD._checkScroll, jsDD.scrollViewConfig.checkerTimeout);
		}

		BX.bind(document, 'keypress', this._checkEsc);
	},

	drag: function(e)
	{
		if (jsDD.bDisable)
			return true;

		e = e || window.event;

		jsDD.x = e.clientX + jsDD.wndSize.scrollLeft;
		jsDD.y = e.clientY + jsDD.wndSize.scrollTop;

		var delta = 5;
		if(jsDD.x >= jsDD.start_x-delta && jsDD.x <= jsDD.start_x+delta && jsDD.y >= jsDD.start_y-delta && jsDD.y <= jsDD.start_y+delta)
			return true;

		if (!jsDD.bStarted)
			jsDD.start();

		if (jsDD.current_node.onbxdrag)
		{
			jsDD.current_node.onbxdrag(jsDD.x, jsDD.y);
		}

		var containersScroll = jsDD.getContainersScrollPos(jsDD.x, jsDD.y);
		var current_dest_index = jsDD.searchDest(jsDD.x+containersScroll.left, jsDD.y+containersScroll.top);

		if (current_dest_index !== jsDD.current_dest_index)
		{
			if (jsDD.current_dest_index !== false)
			{
				if (jsDD.current_node.onbxdraghout)
					jsDD.current_node.onbxdraghout(jsDD.arDestinations[jsDD.current_dest_index], jsDD.x, jsDD.y);

				if (jsDD.arDestinations[jsDD.current_dest_index].onbxdestdraghout)
					jsDD.arDestinations[jsDD.current_dest_index].onbxdestdraghout(jsDD.current_node, jsDD.x, jsDD.y);
			}

			if (current_dest_index !== false)
			{
				if (jsDD.current_node.onbxdraghover)
					jsDD.current_node.onbxdraghover(jsDD.arDestinations[current_dest_index], jsDD.x, jsDD.y);

				if (jsDD.arDestinations[current_dest_index].onbxdestdraghover)
					jsDD.arDestinations[current_dest_index].onbxdestdraghover(jsDD.current_node, jsDD.x, jsDD.y);
			}
		}

		jsDD.current_dest_index = current_dest_index;
	},

	stopDrag: function(e)
	{
		BX.unbind(document, 'keypress', jsDD._checkEsc);

		e = e || window.event;

		jsDD.bPreStarted = false;

		if (jsDD.bStarted)
		{
			if (!jsDD.bEscPressed)
			{
				jsDD.x = e.clientX + jsDD.wndSize.scrollLeft;
				jsDD.y = e.clientY + jsDD.wndSize.scrollTop;
			}

			if (null != jsDD.current_node.onbxdragstop)
				jsDD.current_node.onbxdragstop(jsDD.x, jsDD.y);

			var containersScroll = jsDD.getContainersScrollPos(jsDD.x, jsDD.y);
			var dest_index = jsDD.searchDest(jsDD.x+containersScroll.left, jsDD.y+containersScroll.top);

			if (false !== dest_index)
			{
				if (jsDD.bEscPressed)
				{
					if (null != jsDD.arDestinations[dest_index].onbxdestdraghout)
					{
						if (!jsDD.arDestinations[dest_index].onbxdestdraghout(jsDD.current_node, jsDD.x, jsDD.y))
							dest_index = false;
						else
						{
							if (null != jsDD.current_node.onbxdragfinish)
								jsDD.current_node.onbxdragfinish(jsDD.arDestinations[dest_index], jsDD.x, jsDD.y);
						}
					}

				}
				else
				{
					if (null != jsDD.arDestinations[dest_index].onbxdestdragfinish)
					{
						if (!jsDD.arDestinations[dest_index].onbxdestdragfinish(jsDD.current_node, jsDD.x, jsDD.y, e))
							dest_index = false;
						else
						{
							if (null != jsDD.current_node.onbxdragfinish)
								jsDD.current_node.onbxdragfinish(jsDD.arDestinations[dest_index], jsDD.x, jsDD.y);
						}
					}
				}
			}

			if (false === dest_index)
			{
				if (null != jsDD.current_node.onbxdragrelease)
					jsDD.current_node.onbxdragrelease(jsDD.x, jsDD.y);
			}
			else
			{
				for (var i = 0, cnt = jsDD.arDestinations.length; i < cnt; i++)
				{
					if (i != dest_index && null != jsDD.arDestinations[i].onbxdestdragrelease)
						jsDD.arDestinations[i].onbxdestdragrelease(jsDD.current_node, jsDD.x, jsDD.y);
				}
			}

			for (var i = 0, cnt = jsDD.arDestinations.length; i < cnt; i++)
			{
				if (null != jsDD.arDestinations[i].onbxdestdragstop)
					jsDD.arDestinations[i].onbxdestdragstop(jsDD.current_node, jsDD.x, jsDD.y);
			}
		}

		if(document.body.releaseCapture)
			document.body.releaseCapture();

		BX.unbind(window, 'scroll', jsDD._onscroll);
		BX.unbind(document, "mousemove", jsDD.drag);
		BX.unbind(document, "keypress", jsDD._checkEsc);
		BX.unbind(document, "mouseup", jsDD.stopDrag);

		jsDD.allowSelection();
		document.body.style.cursor = '';

		jsDD.current_node = null;

		if (jsDD.bScrollWindow)
		{
			if (jsDD.scrollViewTimer)
				clearInterval(jsDD.scrollViewTimer);
		}

		if (jsDD.bStarted && !jsDD.bDisableDestRefresh)
			jsDD.refreshDestArea();

		jsDD.bStarted = false;
		jsDD.bEscPressed = false;
	},

	searchDest: function(x, y)
	{
		var p, len, p1, len1, i;
		for (p = 0, len = jsDD.arDestinationsPriority.length; p < len; p++)
		{
			if (jsDD.arDestinationsPriority[p] && BX.type.isArray(jsDD.arDestinationsPriority[p]))
			{
				for (p1 = 0, len1 = jsDD.arDestinationsPriority[p].length; p1 < len; p1++)
				{
					i = jsDD.arDestinationsPriority[p][p1];
					if (jsDD.arDestinations[i] && !jsDD.arDestinations[i].__bxdddisabled)
					{
						if (
							jsDD.arDestinations[i].__bxpos[0] <= x &&
							jsDD.arDestinations[i].__bxpos[2] >= x &&

							jsDD.arDestinations[i].__bxpos[1] <= y &&
							jsDD.arDestinations[i].__bxpos[3] >= y
							)
						{
							return i;
						}
					}
				}
			}
		}

		return false;
	},

	allowSelection: function()
	{
		document.onmousedown = null;
		var b = document.body;
		b.ondrag = null;
		b.onselectstart = null;
		b.style.MozUserSelect = '';

		if (jsDD.current_node)
		{
			jsDD.current_node.ondrag = null;
			jsDD.current_node.onselectstart = null;
			jsDD.current_node.style.MozUserSelect = '';
		}
	},

	denySelection: function()
	{
		document.onmousedown = BX.False;
		var b = document.body;
		b.ondrag = BX.False;
		b.onselectstart = BX.False;
		b.style.MozUserSelect = 'none';
		if (jsDD.current_node)
		{
			jsDD.current_node.ondrag = BX.False;
			jsDD.current_node.onselectstart = BX.False;
			jsDD.current_node.style.MozUserSelect = 'none';
		}
	},

	Disable: function() {jsDD.bDisable = true;},
	Enable: function() {jsDD.bDisable = false;}
}

})();