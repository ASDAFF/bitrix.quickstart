function JCSmartFilter(ajaxURL)
{
	this.ajaxURL = ajaxURL;
	this.form = null;
	this.timer = null;
}

JCSmartFilter.prototype.keyup = function(input)
{
	if(this.timer)
		clearTimeout(this.timer);
	this.timer = setTimeout(BX.delegate(function(){
		this.reload(input);
	}, this), 1000);
}

JCSmartFilter.prototype.click = function(checkbox)
{
	if(this.timer)
		clearTimeout(this.timer);
	this.timer = setTimeout(BX.delegate(function(){
		this.reload(checkbox);
	}, this), 1000);
}

JCSmartFilter.prototype.reload = function(input)
{
	this.position = BX.pos(input, true);
	this.form = BX.findParent(input, {'tag':'form'});
	if(this.form)
	{
		var values = new Array;
		values[0] = {name: 'ajax', value: 'y'};
		this.gatherInputsValues(values, BX.findChildren(this.form, {'tag':'input'}, true));
		BX.ajax.loadJSON(
			this.ajaxURL,
			this.values2post(values),
			BX.delegate(this.postHandler, this)
		);
	}
}

JCSmartFilter.prototype.postHandler = function (result)
{
	if(!!result && !!result.ITEMS)
	{
		for(var PID in result.ITEMS)
		{
			var arItem = result.ITEMS[PID];
			if(arItem.PROPERTY_TYPE == 'N' || arItem.PRICE)
			{
			}
			else if(arItem.VALUES)
			{
				for(var i in arItem.VALUES)
				{
					var ar = arItem.VALUES[i];
					var control = BX(ar.CONTROL_ID);
					if(control)
					{
						control.parentNode.className = ar.DISABLED ? 'disabled': '';
					}
				}
			}
		}
		var modef = BX('modef');
		var modef_num = BX('modef_num');
		if(modef && modef_num)
		{
			modef_num.innerHTML = result.ELEMENT_COUNT;
			var hrefFILTER = BX.findChildren(modef, {tag: 'A'}, true);

			if(result.FILTER_URL && hrefFILTER)
				hrefFILTER[0].href = BX.util.htmlspecialcharsback(result.FILTER_URL);

			if(result.FILTER_AJAX_URL && result.COMPONENT_CONTAINER_ID)
			{
				BX.bind(hrefFILTER[0], 'click', function(e)
				{
					var url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
					BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
					return BX.PreventDefault(e);
				});
			}

			if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID)
			{
				var url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
				BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
			}
			else
			{
				if(modef.style.display == 'none')
					modef.style.display = 'block';
				modef.style.top = this.position.top + 'px';
			}
		}
	}
}

JCSmartFilter.prototype.gatherInputsValues = function (values, elements)
{
	if(elements)
	{
		for(var i = 0; i < elements.length; i++)
		{
			var el = elements[i];
			if (el.disabled || !el.type)
				continue;

			switch(el.type.toLowerCase())
			{
				case 'text':
				case 'textarea':
				case 'password':
				case 'hidden':
				case 'select-one':
					if(el.value.length)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'select-multiple':
					for (var j = 0; j < el.options.length; j++)
					{
						if (el.options[j].selected)
							values[values.length] = {name : el.name, value : el.options[j].value};
					}
					break;
				default:
					break;
			}
		}
	}
}

JCSmartFilter.prototype.values2post = function (values)
{
	var post = new Array;
	var current = post;
	var i = 0;
	while(i < values.length)
	{
		var p = values[i].name.indexOf('[');
		if(p == -1)
		{
			current[values[i].name] = values[i].value;
			current = post;
			i++;
		}
		else
		{
			var name = values[i].name.substring(0, p);
			var rest = values[i].name.substring(p+1);
			if(!current[name])
				current[name] = new Array;

			var pp = rest.indexOf(']');
			if(pp == -1)
			{
				//Error - not balanced brackets
				current = post;
				i++;
			}
			else if(pp == 0)
			{
				//No index specified - so take the next integer
				current = current[name];
				values[i].name = '' + current.length;
			}
			else
			{
				//Now index name becomes and name and we go deeper into the array
				current = current[name];
				values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
			}
		}
	}
	return post;
}

function cDoubleTrackBar(Track, Tracker,LeftDrag, RightDrag, Settings)
{
	switch(typeof Track){
		case 'string': this.Track = document.getElementById(Track); break;
		case 'object': this.Track = Track; break;
	}
	switch(typeof Tracker){
		case 'string': this.Tracker = document.getElementById(Tracker); break;
		case 'object': this.Tracker = Tracker; break;
	}
	switch(typeof LeftDrag){
		case 'string': this.LeftDrag = document.getElementById(LeftDrag); break;
		case 'object': this.LeftDrag = LeftDrag; break;
	}
	switch(typeof RightDrag){
		case 'string': this.RightDrag = document.getElementById(RightDrag); break;
		case 'object': this.RightDrag = RightDrag; break;
	}
	if (!Track || !Tracker)
		return false;
	this.OnUpdate = Settings.OnUpdate;
	this.OnComplete = Settings.OnComplete;
	this.FingerOffset = Settings.FingerOffset || 0;
	this.Min = Settings.Min || 0;
	this.Max = Settings.Max || 100;
	this.MinSpace = Settings.MinSpace || 0;
	this.RoundTo = Settings.RoundTo || 1;
	if (this.RoundTo < 1)
	{
		this.Precision = parseInt(Settings.Precision, 10) || 0;
		if (isNaN(this.Precision))
		{
			this.Precision = 0;
		}
	}
	else
	{
		this.Precision = 0;
	}
	this.PrecisionFactor = Math.pow(10,this.Precision);

	this.Disabled = (typeof Settings.Disabled != 'undefined') ? Settings.Disabled : false;

	if (this.Min >= this.Max)
		this.Max = this.Min +1;
	this.MinPos = this.Min;
	this.MaxPos = this.Max;
	if (this.Max - this.Min < this.MinSpace)
		this.MinSpace =  this.Max - this.Min;
	if (this.Max - this.Min < this.RoundTo)
		this.RoundTo =  this.Max - this.Min;
	this.MinSpace = Math.ceil(this.MinSpace/this.RoundTo)*this.RoundTo;

	//this.Track.style.width = (this.Track.clientWidth || this.Track.offsetWidth) + 'px';
	this.OnTrackMouseDown = this.bindAsEventListener(this.TrackMouseDown);
	this.OnDocumentMouseMove = this.bindAsEventListener(this.DocumentMouseMove);
	this.OnDocumentMouseUp = this.bindAsEventListener(this.DocumentMouseUp);

	if ('ontouchstart' in document.documentElement)
	{
		this.bindEvent(this.Track, 'touchstart', this.OnTrackMouseDown);
	}
	else
		this.bindEvent(this.Track, 'mousedown', this.OnTrackMouseDown);

	this.TrackerLeft = 0;
//	this.UpdateTracker(this.Track.offsetWidth + this.FingerOffset);
	/*	if (typeof this.OnUpdate == 'function') {
	 this.OnUpdate.call(this);
	 }*/

	this.MinInputId = Settings.MinInputId || 0;
	this.MaxInputId = Settings.MaxInputId || 1000;

	BX.defer(BX.proxy(this.startPosition, this))();
}
cDoubleTrackBar.prototype = {

	TrackMouseDown: function(event) {
		this.TrackerLeft = this.Tracker.offsetLeft;
		this.TrackerRight = this.TrackerLeft + this.Tracker.offsetWidth;

		this.TrackerOffsets = this.getOffsets(this.Track);

		var currentX = ('ontouchmove' in document.documentElement) ? event.targetTouches[0].pageX : event.clientX;
		var X = currentX + document.documentElement.scrollLeft;
		X -= this.TrackerOffsets[0];

		var diff = Math.abs(this.TrackerLeft-X) - Math.abs(this.TrackerRight-X);
		if (diff == 0 && this.TrackerLeft == 0)
			this.Left = false;
		else
			this.Left = (diff <= 0);

		if (typeof this.Disabled == 'function') {
			if ( this.Disabled.call(this) )
				return true;
		} else if ( this.Disabled )
			return true;

		this.UpdateTracker(X);

		if ('ontouchmove' in document.documentElement)
		{
			this.bindEvent(document, 'touchmove', this.OnDocumentMouseMove);
			this.bindEvent(document, 'touchend', this.OnDocumentMouseUp);
		}
		else
		{
			this.bindEvent(document, 'mousemove', this.OnDocumentMouseMove);
			this.bindEvent(document, 'mouseup', this.OnDocumentMouseUp);
		}
		return this.stopEvent(event);
	},
	DocumentMouseMove: function(event) {
		var currentX = ('ontouchmove' in document.documentElement) ? event.targetTouches[0].pageX : event.clientX;
		this.UpdateTracker(currentX + document.documentElement.scrollLeft - this.TrackerOffsets[0]);
		return this.stopEvent(event);
	},
	DocumentMouseUp: function(event) {
		if ('ontouchmove' in document.documentElement)
		{
			this.unbindEvent(document, 'touchmove', this.OnDocumentMouseMove);
			this.unbindEvent(document, 'touchend', this.OnDocumentMouseUp);
		}
		else
		{
			this.unbindEvent(document, 'mousemove', this.OnDocumentMouseMove);
			this.unbindEvent(document, 'mouseup', this.OnDocumentMouseUp);
		}

		if (typeof this.OnComplete == 'function') {
			this.OnComplete.call(this);
		}
		return this.stopEvent(event);
	},
	UpdateTracker: function(X)
	{
		var _LogicWidth = this.Track.clientWidth;
		var _minSpace = Math.floor(_LogicWidth*this.MinSpace/(this.Max-this.Min));
		var _oldMin = this.MinPos;
		var _oldMax = this.MaxPos;

		if (this.Left)
		{
			X += this.FingerOffset;
			this.TrackerLeft = Math.max(0, Math.min(this.TrackerRight - _minSpace - 1, X));
			this.MinPos = Math.round((this.Min + this.TrackerLeft*(this.Max-this.Min)/_LogicWidth) / this.RoundTo) * this.RoundTo;
			if (this.MinSpace >= this.MaxPos - this.MinPos)
			{
				this.MinPos = this.MaxPos - this.MinSpace;
			}
			if (this.Precision > 0)
			{
				this.MinPos = Math.round(this.MinPos*this.PrecisionFactor)/this.PrecisionFactor;
			}

			this.TrackerLeft = this.price2px(this.Track, this.MinPos - this.Min);

			this.LeftDrag.style.left = this.px2percent(this.Track, this.TrackerLeft) + '%';
			this.Tracker.style.width = this.px2percent(this.Track, this.TrackerRight - this.TrackerLeft) + '%';
			this.Tracker.style.left = this.px2percent(this.Track, this.TrackerLeft) + '%';
			this.MinInputId.value = this.MinPos;
			smartFilter.keyup(this.MinInputId);
		}
		else
		{
			X -= this.FingerOffset;
			this.TrackerRight = Math.max(this.TrackerLeft + _minSpace + 1 , Math.min(_LogicWidth + 1, X));
			this.MaxPos = Math.round((this.Min + (this.TrackerRight-1)*(this.Max-this.Min)/_LogicWidth) / this.RoundTo) * this.RoundTo;
			if (this.MinSpace >= this.MaxPos - this.MinPos)
			{
				this.MaxPos = this.MinPos + this.MinSpace;
			}
			if (this.Precision > 0)
			{
				this.MaxPos = Math.round(this.MaxPos*this.PrecisionFactor)/this.PrecisionFactor;
			}

			this.TrackerRight = this.price2px(this.Track, this.MaxPos - this.Min);

			this.Tracker.style.left = this.px2percent(this.Track, this.TrackerLeft) + '%';
			this.Tracker.style.width = this.px2percent(this.Track, this.TrackerRight - this.TrackerLeft) + '%';
			this.RightDrag.style.left = this.px2percent(this.Track, this.TrackerRight) + '%';
			this.MaxInputId.value = this.MaxPos;
			smartFilter.keyup(this.MaxInputId);
		}
	},
	getOffsets: function(element) {
		var valueT = 0, valueL = 0;
		do {
			valueT += element.offsetTop  || 0;
			valueL += element.offsetLeft || 0;
			element = element.offsetParent;
		} while (element);
		return [valueL, valueT];
	},
	bindEvent: function(element, event, callBack){
		if (element.addEventListener) {
			element.addEventListener(event, callBack, false);
		} else {
			element.attachEvent('on' + event, callBack);
		}
	},
	unbindEvent: function(element, event, callBack){
		if (element.removeEventListener) {
			element.removeEventListener(event, callBack, false);
		} else if (element.detachEvent) {
			element.detachEvent('on' + event, callBack);
		}
	},
	bindAsEventListener: function (callBack) {
		var _object = this;
		return function(event) {
			return callBack.call(_object, event || window.event);
		}
	},
	stopEvent: function (event){
		if (event.preventDefault) {
			event.preventDefault();
			event.stopPropagation();
		} else {
			event.returnValue = false;
			event.cancelBubble = true;
		}
		return false;
	},
	startPosition: function ()
	{
		var curMinPrice = this.MinInputId.value || 0,
			curMaxPrice = this.MaxInputId.value || 0,
			curLeft = 0,
			curRight = 0;

		if (curMinPrice || curMaxPrice)
		{
			if (!curMinPrice || curMinPrice < this.Min|| curMinPrice > this.Max)
				curMinPrice = this.Min;
			if (!curMaxPrice || curMaxPrice > this.Max || curMaxPrice < this.Min)
				curMaxPrice = this.Max;

			if (curMinPrice)
				curLeft = this.price2px(this.Track, curMinPrice - this.Min);
			if (curMaxPrice)
				curRight = this.price2px(this.Track, curMaxPrice - this.Min);

			this.LeftDrag.style.left = this.px2percent(this.Track, curLeft) + "%";
			this.Tracker.style.left = this.px2percent(this.Track, curLeft) + "%";
			this.Tracker.style.width = this.px2percent(this.Track, curRight - curLeft) + "%";
			if (Math.round(this.px2percent(this.Track, curRight)) < 100)
				this.RightDrag.style.left = this.px2percent(this.Track, curRight)  + "%";
		}
	},
	px2percent: function (control, px)
	{
		return px / control.clientWidth * 100;
	},
	price2px: function (control, price)
	{
		var scale = (this.Max - this.Min) / control.clientWidth;
		return Math.round(price / scale);
	}
}

function hideFilterProps(element)
{
	var obj = element.parentNode;

	var filterBlock = BX.findChild(obj, {className:"bx_filter_block"}, true, false);

	if(BX.hasClass(obj, "active"))
	{
		var easing = new BX.easing({
			duration : 300,
			start : { opacity: 1,  height: filterBlock.offsetHeight },
			finish : { opacity: 0, height:0 },
			transition : BX.easing.transitions.quart,
			step : function(state){
				filterBlock.style.opacity = state.opacity;
				filterBlock.style.height = state.height + "px";
			},
			complete : function() {
				BX.removeClass(obj, "active");
				filterBlock.style.overflow = "hidden";
			}
		});
		easing.animate();
	}
	else
	{
		filterBlock.style.display = "block";
		filterBlock.style.opacity = 0;
		filterBlock.style.height = "auto";

		var obj_children_height = filterBlock.offsetHeight;
		filterBlock.style.height = 0;

		var easing = new BX.easing({
			duration : 300,
			start : { opacity: 0,  height: 0 },
			finish : { opacity: 1, height: obj_children_height },
			transition : BX.easing.transitions.quart,
			step : function(state){
				filterBlock.style.opacity = state.opacity;
				filterBlock.style.height = state.height + "px";
			},
			complete : function() {
				filterBlock.style.overflow = "auto";
			}
		});
		easing.animate();
		BX.addClass(obj, "active")
	}
}