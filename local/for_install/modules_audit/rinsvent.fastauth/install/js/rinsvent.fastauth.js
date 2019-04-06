;(function($, window, document, undefined) {
	"use strict";

	function Fastauth(options) {
		/**
		 * Current options set by the caller including defaults.
		 * @public
		 */
		this.options = $.extend({}, Fastauth.Defaults, options);

		this.arPointsAll = [];
		this.arPointsObjAll = [];
		this.arPointsTotal = [];

		this.svg = false;

		this.typeAjax = "CHECK";

		if(this.options.condition == "DONTAUTH"){
			this.initialize();
		}else{
			this.rinsventFastauth();
		}

		this.buildMenu();
	}

	/**
	 * Default options for the carousel.
	 * @public
	 */
	Fastauth.Defaults = {
		size: 5,
		url: '/bitrix/tools/rinsvent_fastauth.php',
		scale: 6,
		rExt: 25,
		condition:"DONTAUTH",
	};

	Fastauth.prototype.buildMenu = function () {
		var _this = this;
		$("body").append("<div id='rinsvent_menu_settings'>RFA <div class='settings bx-context-toolbar-button-icon bx-context-toolbar-settings-icon'></div></div>");
		$("#rinsvent_menu_settings .settings").on("click", function(){
			_this.typeAjax = "UPDATEPASSWORD";
			_this.initialize();
		});
	}

	/**
	 * Initializes the .
	 * @protected
	 */
	Fastauth.prototype.initialize = function() {
		//добавл€ем оболочку окна
		$("body").append('<div id="rinsvent_fastauth_overlay"></div><div id="rinsvent_fastauth_wrap"><svg></svg></div>');
		$("#rinsvent_fastauth_overlay").on("click", this.close);
		this.svg = $("#rinsvent_fastauth_wrap svg");

		//инициализируем параметры
		var size = this.options.size;
		var scale = this.options.scale;
		var rExt = this.options.rExt;

		//расчитываем отступы
		var winWidth = $(window).width();
		var winHeight = $(window).height();
		var elWidth = Math.min(winHeight,winWidth)/scale;
		var elWidthX = winWidth>winHeight ? (winWidth-(size*elWidth))/2 : elWidth/2;
		var elHeightY = winWidth<winHeight ? (winHeight-(size*elWidth))/2 : elWidth/2;
		var rExternal = rExt*elWidth/150;
		var rf_paddinf_left = elWidth/2;

		//устанавливаем размеры и отступы
		$("#rinsvent_fastauth_wrap").offset({ top: elHeightY, left: elWidthX });
		this.svg.width((size*elWidth));
		this.svg.height((size*elWidth));

		//—троим точки
		for (var i=0;i<size;i++){
			for(var j=0;j<size;j++){
				var x = i*elWidth+rf_paddinf_left;
				var y = j*elWidth+rf_paddinf_left
				this.addPoint(x,y,5,i,j, rExternal);
			}
		}

	};

	/**/
	Fastauth.prototype.addPoint = function(x,y,r,n,m, rExternal){
		var _this = this;
		var circle= this.makeSVG('circle',
			{
				cx: x,
				cy: y,
				r:r,
				stroke: 'green',
				'stroke-width': 1,
				fill: 'green',
				n:n,
				m:m
			}
		);
		$(circle).data("index",_this.arPointsAll.length);
		_this.arPointsObjAll.push(circle);
		_this.arPointsAll.push({x:x,y:y});
		_this.svg.append(circle);

		$(circle).on("mousedown.fp",function(){
			_this.svg.attr("class", "move");
			if(_this.arPointsTotal.length == 0){
				_this.addPolyline($(this).attr("cx")+","+$(this).attr("cy")+","+$(this).attr("cx")+","+$(this).attr("cy"),rExternal);
				var circleActive = _this.makeSVG('circle',
					{
						cx: $(this).attr("cx"),
						cy: $(this).attr("cy"),
						r:rExternal,
						stroke: 'yellow',
						'stroke-width': 5,
						fill: 'none'
					}
				);
				_this.svg.append(circleActive);
			}
		});

	}
	Fastauth.prototype.makeSVG = function(tag, attrs) {
		var el= document.createElementNS('http://www.w3.org/2000/svg', tag);
		for (var k in attrs)
			el.setAttribute(k, attrs[k]);
		return el;
	}
	Fastauth.prototype.addLine = function(x1,y1,x2,y2){
		var line=this.makeSVG('line', {x1: x1, y1: y1, x2: x2, y2: y2, stroke: 'green', 'stroke-width': 1, fill: 'green'});
		this.svg.append(line);
	}
	Fastauth.prototype.addPolyline = function(points,rExternal){
		var _this = this;
		var polyLine=this.makeSVG('polyline', {points: points, stroke: 'green', 'stroke-width': 3, fill: 'none'});
		this.svg.append(polyLine);

		this.svg.on("mousemove.fp",function(e){
			var arPoints = $(polyLine).attr("points").split(",");
			arPoints[arPoints.length-2]=e.offsetX;
			arPoints[arPoints.length-1]=e.offsetY;

			for(var i=0;i<_this.arPointsAll.length;i++){
				if(Math.abs(_this.arPointsAll[i].x-e.offsetX)<15 && Math.abs(_this.arPointsAll[i].y-e.offsetY)<15){
					_this.arPointsTotal.push($(_this.arPointsObjAll[i]).data("index"));
					var circleActive = _this.makeSVG('circle',
						{
							cx: _this.arPointsAll[i].x,
							cy: _this.arPointsAll[i].y,
							r:rExternal,
							stroke: 'yellow',
							'stroke-width': 5,
							fill: 'none'
						}
					);
					_this.svg.append(circleActive);
					arPoints[arPoints.length-2]=_this.arPointsAll[i].x;
					arPoints[arPoints.length-1]=_this.arPointsAll[i].y;
					arPoints.push(_this.arPointsAll[i].x);
					arPoints.push(_this.arPointsAll[i].y);
					_this.arPointsAll.splice(i,1);
					_this.arPointsObjAll.splice(i,1);
				}
			}

			$(polyLine).attr("points",arPoints.join(","));

			if(_this.arPointsTotal.length == 25) _this.svg.unbind("mousemove.fp");
		});

		this.svg.on("mouseup.fp",function(e){
			_this.svg.removeAttr("class");
			_this.svg.unbind("mousemove.fp");
			_this.svg.unbind("mouseup.fp");

			var dataPost = {
				"TYPE_AJAX" : _this.typeAjax,
				"POINTS" :_this.arPointsTotal,
			};
			var config = {
				'method': 'POST',
				'dataType': 'json',
				'url': _this.options.url,
				'data': dataPost ,
				'onsuccess': function(data){
					if(data.STATUS == "SUCCESSAUTH"){
						document.location.reload();
					}
					if(data.STATUS == "FAILEDAUTH"){
						_this.refresh();
					}
					if(data.STATUS == "REPEATPASSWORD"){
						_this.typeAjax = "REPEATPASSWORD";
						_this.refresh();
					}
					if(data.STATUS == "SUCCESSPASSWORD"){
						_this.typeAjax = "CHECK";
						_this.close();
					}
				},
			};
			BX.ajax(config);
		});

	}

	Fastauth.prototype.close = function () {
		this.arPointsAll = [];
		this.arPointsObjAll = [];
		this.arPointsTotal = [];
		$("#rinsvent_fastauth_overlay").remove();
		$("#rinsvent_fastauth_wrap").remove();
		this.svg = false;
	}

	Fastauth.prototype.refresh = function () {
		this.arPointsAll = [];
		this.arPointsObjAll = [];
		this.arPointsTotal = [];
		$("#rinsvent_fastauth_overlay").remove();
		$("#rinsvent_fastauth_wrap").remove();
		this.svg = false;
		this.initialize();
		this.rinsventFastauth();
	}

	Fastauth.prototype.rinsventFastauthSessionCustom = function(data) {
		if(data == 'SESSION_EXPIRED')
		{
			this.refresh();
		}
	}
	//переопредел€ем штатную функцию
	Fastauth.prototype.rinsventFastauth = function () {
		var _this = this;
		if (typeof bxSession == "object") {
			if (typeof bxSession.CheckResult == "function") {
				var rinsventFastauthBxSession = bxSession.CheckResult;
				bxSession.CheckResult = function (data) {
					rinsventFastauthBxSession(data);
					_this.rinsventFastauthSessionCustom(data);
				}
			}
		}
	}
	/**
	 * The jQuery Plugin for the Rinsvent Fastauth
	 * @todo Navigation plugin `next` and `prev`
	 * @public
	 */
	$.fn.rinsventFastauth = function(option) {
		var args = Array.prototype.slice.call(arguments, 1);

		return this.each(function() {
			var $this = $(this),
				data = $this.data('rinsvent.fastauth');

			if (!data) {
				data = new Fastauth(typeof option == 'object' && option);
				$this.data('rinsvent.fastauth', data);
			}

			if (typeof option == 'string' && option.charAt(0) !== '_') {
				data[option].apply(data, args);
			}
		});
	};

	/**
	 * The constructor for the jQuery Plugin
	 * @public
	 */
	$.fn.rinsventFastauth.Constructor = Fastauth;

})(window.Zepto || window.jQuery, window, document);