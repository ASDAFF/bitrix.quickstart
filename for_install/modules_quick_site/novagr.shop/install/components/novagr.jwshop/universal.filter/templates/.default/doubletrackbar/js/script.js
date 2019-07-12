/*
*	Track — id of parent elenemt
*	Tracker — id of tracked element
*	OnUpdate — function whitch calls on each value change
*	OnComplete — function whitch calls on end of the drag
*	FingerOffset — distance between mouse pointer and corner tracker's edge
*	FormatNumbers — lead numders in hairlines with spaces
*	Min & Max — range of vaues
*	MinSpace — minimum difference between Min & Max
*	RoundTo — values will be rounded to this value
*	Margins — indent between Track & Tracker
*	AllowedValues — force Tracker to stick to the values
*
*	OnUpdate — function whitch called each time, when Tracker moved
*	OnComplete — function whitch called when user stop draging
*/

function cDoubleTrackBar(Track, Tracker, Settings) {
	switch(typeof Track){
		case 'string': this.Track = document.getElementById(Track); break;
		case 'object': this.Track = Track; break;
	}
	switch(typeof Tracker){
		case 'string': this.Tracker = document.getElementById(Tracker); break;
		case 'object': this.Tracker = Tracker; break;
	}
	if (!Track || !Tracker)
		return false;
	this.OnUpdate = Settings.OnUpdate;
	this.OnComplete = Settings.OnComplete;
	this.FingerOffset = Settings.FingerOffset || 0;
	this.FormatNumbers = Settings.FormatNumbers || false;
	this.Min = Settings.Min || 0;
	this.Max = Settings.Max || 100;
	this.MinSpace = Settings.MinSpace || 0;
	this.RoundTo = Settings.RoundTo || 1;
	this.Margins = Settings.Margins || 0;
	this.AllowedValues = Settings.AllowedValues || false;
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


	this.Track.style.width = (this.Track.clientWidth || this.Track.offsetWidth) + 'px';
	this.OnTrackMouseDown = this.bindAsEventListener(this.TrackMouseDown);
	this.OnDocumentMouseMove = this.bindAsEventListener(this.DocumentMouseMove);
	this.OnDocumentMouseUp = this.bindAsEventListener(this.DocumentMouseUp);

	this.bindEvent(this.Track, 'mousedown', this.OnTrackMouseDown);

	this.TrackerLeft = 0;
	this.UpdateTracker(this.Track.offsetWidth + this.FingerOffset);
	if (typeof this.OnUpdate == 'function') {
		this.OnUpdate.call(this);
	}
}
cDoubleTrackBar.prototype = {

	TrackMouseDown: function(event) {
		this.TrackerLeft = this.Tracker.offsetLeft - this.Margins;
		this.TrackerRight = this.TrackerLeft + this.Tracker.offsetWidth;

		this.TrackerOffsets = this.getOffsets(this.Track);

		var X = event.clientX + document.documentElement.scrollLeft;
		X -= this.TrackerOffsets[0];

		this.Left = Math.abs(this.TrackerLeft-X+this.Margins) <= Math.abs(this.TrackerRight-X+this.Margins);

		if (typeof this.Disabled == 'function') {
			if ( this.Disabled.call(this) )
				return true;
		} else if ( this.Disabled )
			return true;
		
		this.UpdateTracker(X);

		this.bindEvent(document, 'mousemove', this.OnDocumentMouseMove);
		this.bindEvent(document, 'mouseup', this.OnDocumentMouseUp);

		return this.stopEvent(event);
	},
	DocumentMouseMove: function(event) {
		this.UpdateTracker(event.clientX + document.documentElement.scrollLeft - this.TrackerOffsets[0]);
		return this.stopEvent(event);
	},
	DocumentMouseUp: function(event) {
		this.unbindEvent(document, 'mousemove', this.OnDocumentMouseMove);
		this.unbindEvent(document, 'mouseup', this.OnDocumentMouseUp);

		if (typeof this.OnComplete == 'function') {
			this.OnComplete.call(this);
		}
		return this.stopEvent(event);
	},
	UpdateTracker: function(X){
		var _LogicWidth = this.Track.offsetWidth - this.Margins*2 - 1;
		var _minSpace = Math.floor(_LogicWidth*this.MinSpace/(this.Max-this.Min));
		var _oldMin = this.MinPos;
		var _oldMax = this.MaxPos;

		X -= this.Margins;
		if (this.Left) {
			X += this.FingerOffset;
			this.TrackerLeft = Math.max(0, Math.min(this.TrackerRight - _minSpace - 1, X));
			this.MinPos = Math.round((this.Min + this.TrackerLeft*(this.Max-this.Min)/_LogicWidth) / this.RoundTo) * this.RoundTo;
			if (this.MinSpace >= this.MaxPos - this.MinPos) {
				this.MinPos = this.MaxPos - this.MinSpace;
			}
			if (this.AllowedValues) {
				this.TrackerLeft = Math.round(_LogicWidth*(this.MinPos - this.Min)/(this.Max - this.Min));
			}
		} else {
			X -= this.FingerOffset;
			this.TrackerRight = Math.max(this.TrackerLeft + _minSpace + 1 , Math.min(_LogicWidth + 1, X));
			this.MaxPos = Math.round((this.Min + (this.TrackerRight-1)*(this.Max-this.Min)/_LogicWidth) / this.RoundTo) * this.RoundTo;
			if (this.MinSpace >= this.MaxPos - this.MinPos) {
				this.MaxPos = this.MinPos + this.MinSpace;
			}
			if (this.AllowedValues) {
				this.TrackerRight = Math.round(_LogicWidth*(this.MaxPos - this.Min)/(this.Max - this.Min))+1;
			}
		}
		this.Tracker.style.width = (this.TrackerRight - this.TrackerLeft) + 'px';
		this.Tracker.style.left = (this.Margins + this.TrackerLeft) + 'px';

		if (typeof this.OnUpdate == 'function')
			if ( !this.AllowedValues || (this.AllowedValues && (_oldMax!=this.MaxPos || _oldMin!=this.MinPos)) )
				this.OnUpdate.call(this);
	},
	
	AddHairline: function (pos) {
		var _Touch = this.Track.appendChild( document.createElement('div') );
		var _LogicWidth = this.Track.offsetWidth - this.Margins*2 - 1;
		
		_Touch.style.left = this.Margins + _LogicWidth/(this.Max-this.Min)*(pos-this.Min) + 'px';
		_Touch.className = 'touch';
		_Touch.innerHTML = "<span>" + (this.FormatNumbers ? this.leadSpaces(pos) : pos) + "</span>";
	},
	
	AutoHairline: function(num) {
		if (num >= 1)
			this.AddHairline(this.Min);
		if (num >= 2)
			this.AddHairline(this.Max);
		if (num >= 3) {
			num--;
			var diff = this.Max - this.Min;
			var roundTo = [10, 20, 50, 100, 250, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000, 250000, 500000, 1000000];
			var DoRound = 1;
			for (var i=0; roundTo[i]; i++) {
				DoRound = roundTo[i]/10;
				if (roundTo[i]>diff)
					break;
			}
			for (var i=1; i<num; i++) {
				var val = this.Min + diff/num*i;
				val = Math.round(val/DoRound)*DoRound;
				this.AddHairline(val);
			}
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
	leadSpaces: function(numb) {
		var res = '';
		numb = numb.toString();
		var l = numb.length;
		for (var i=l; i>0; i--)
			if ((l-i)%3==2)
				res = '&nbsp;'+numb.charAt(i-1)+res;
			else
				res = numb.charAt(i-1)+res;
		return res;
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
	}
}

/*
* перенесено из template
*/
function classFilter(r,m,not){
	m = " " + m + " ";
	var tmp = [];
	for ( var i = 0; r[i]; i++ ) {
		var pass = (" " + r[i].className + " ").indexOf( m ) >= 0;
		if ( not ^ pass )
			tmp.push( r[i] );
	}
	return tmp;
}