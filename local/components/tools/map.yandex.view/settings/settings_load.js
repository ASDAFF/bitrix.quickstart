/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

/* CBXYandexPoint definition */
function CBXYandexPoint(arData)
{
	var _this = this;
	
	/* fields */
	this.DATA = {LON:0,LAT:0,TEXT:''};
	this.PLACEMARK = null;
	this.VIEW = null;
	this.EDIT_CONTROL = null;
	
	/* events */
	this.onCreate = null;
	this.onDelete = null;

	/* methods */
	this.__updatePointPosition = function()
	{
		var obPoint = this.getGeoPoint();
		
		_this.DATA.LON = obPoint.getLng();
		_this.DATA.LAT = obPoint.getLat();
	};

	
	this.Delete = function (e)
	{
		if (null != _this.VIEW && null != _this.VIEW.parentNode)
			_this.VIEW.parentNode.removeChild(_this.VIEW);
		
		jsYandexCE.map.removeOverlay(_this.PLACEMARK);
		_this.PLACEMARK = null;
		
		if (null != _this.onDelete)
			_this.onDelete(_this);
			
		return BX.PreventDefault(e);
	};
	
	this.Edit = function(e)
	{
		if (null == e)
			e = BX.GetContext(this).event;
		
		if (_this.PLACEMARK._balloonVisible)
			_this.PLACEMARK.closeBalloon();
		else
		{
			_this.PLACEMARK.openBalloon();
			_this.EDIT_CONTROL.focus();
		}
		
		if (null != e)
			return BX.PreventDefault(e);
	};
	
	this.__updateView = function(e)
	{
		if (null == e)
			e = BX.GetContext(this).event;

		value = this.value;
		
		_this.DATA.TEXT = value;
		
		var rnpos = value.indexOf("\n");

		var value_view = '';
		if (value.length > 0)
			value_view = rnpos <= 0 ? value : value.substring(0, rnpos);

		value_view = value_view.replace(/</g, '&lt;');
		value_view = value_view.replace(/>/g, '&gt;');
			
		_this.__updateViewText(value_view ? value_view : window.jsYandexMess.noname);
		_this.PLACEMARK.setIconContent(value_view);
		
		if (e.type == 'blur')
			_this.PLACEMARK.closeBalloon();
			
		return BX.PreventDefault(e);
	};
	
	/* constructor */
	if (null != arData)
	{
		this.Create(arData)
	}
}

CBXYandexPoint.prototype.__point_link_hover = function() {this.style.backgroundColor = "#E3E8F7"; this.firstChild.style.display = 'block';}
CBXYandexPoint.prototype.__point_link_hout = function() {this.style.backgroundColor = "#FFFFFF"; this.firstChild.style.display = 'none';}
CBXYandexPoint.prototype.__updateViewText = function(str) {this.VIEW.firstChild.nextSibling.innerHTML = str;}
CBXYandexPoint.prototype.getData = function() {return this.DATA;}

CBXYandexPoint.prototype.Create = function(arPlacemark)
{
	this.DATA.TEXT = arPlacemark.TEXT; 
	this.DATA.LAT = arPlacemark.LAT;
	this.DATA.LON = arPlacemark.LON;

	this.PLACEMARK = new jsYandexCE.context.YMaps.Placemark(new jsYandexCE.context.YMaps.GeoPoint(this.DATA.LON, this.DATA.LAT), {draggable: 1});
	this.PLACEMARK.setBalloonContent(this.__createEditForm());

	this.__createView();

	var value_view = '';
	if (this.DATA.TEXT.length > 0)
	{
		var rnpos = this.DATA.TEXT.indexOf("\n");
		value_view = rnpos <= 0 ? this.DATA.TEXT : this.DATA.TEXT.substring(0, rnpos);
		value_view = value_view.replace(/>/g, '&gt;');
		value_view = value_view.replace(/</g, '&lt;');
	}

	this.__updateViewText(value_view ? value_view : window.jsYandexMess.noname);
	this.PLACEMARK.setIconContent(value_view);	

	jsYandexCE.context.YMaps.Events.observe(this.PLACEMARK, this.PLACEMARK.Events.DragEnd, this.__updatePointPosition);
	jsYandexCE.map.addOverlay(this.PLACEMARK);
	
	if (null !== this.onCreate)
		this.onCreate(this);

	return this.PLACEMARK;
}
	
CBXYandexPoint.prototype.__createEditForm = function()
{
	this.EDIT_CONTROL = jsYandexCE.context.document.createElement('TEXTAREA');
	this.EDIT_CONTROL.value = this.DATA.TEXT;
	
	this.EDIT_CONTROL.onkeyup = this.__updateView;
	this.EDIT_CONTROL.onblur = this.__updateView;
	
	return this.EDIT_CONTROL;
}

CBXYandexPoint.prototype.__createView = function()
{
	this.VIEW = document.getElementById('bx_yandex_points').appendChild(document.createElement('LI'));
	
	var obDeleteLink = this.VIEW.appendChild(document.createElement('A'));

	obDeleteLink.href = "javascript: void(0)";
	obDeleteLink.className = 'bx-yandex-delete';
	obDeleteLink.onclick = this.Delete;
	obDeleteLink.style.display = 'none';

	var obLink = this.VIEW.appendChild(document.createElement('A'));
	obLink.className = 'bx-yandex-point';
	obLink.href = 'javascript:void(0)';
	obLink.onclick = this.Edit;
	obLink.innerHTML = window.jsYandexMess.noname;
	
	this.VIEW.onmouseover = this.__point_link_hover;
	this.VIEW.onmouseout = this.__point_link_hout;
};
/* /CBXYandexPoint definition */
/* CBXYandexPoly definition */
function CBXYandexPoly(arData)
{
	var _this = this;
	
	/* fields */
	this.DATA = {POINTS:[],TITLE:'',STYLE:{}};
	this.POLYLINE = null;
	this.VIEW = null;
	this.EDIT_CONTROL = null;
	this.STYLE = null;
	this.STYLE_ID = null;
	
	this.START_POINT = null;
	this.END_POINT = null;
	
	this.ClickObserver = null;
	this.bFinished = false;
	
	/* events */
	this.onFinish = null;
	this.onDelete = null;
	
	/* methods */
	this.Finish = function(obEvent)
	{
		if (!jsYandexCE.bAddPolyMode)
			return;
		
		_this.bFinished = true;
                if (null != _this.ClickObserver)
                    _this.ClickObserver.cleanup();
		
		jsYandexCE.map.removeOverlay(_this.START_POINT);
		
		if (null == _this.POLYLINE)
		{
			_this.Delete();
		}
		else
		{
			//jsYandexCE.context.YMaps.Events.observe(_this.POLYLINE, _this.POLYLINE.Events.Click, _this.showSettingsForm);
			_this.END_POINT.setIconContent(jsYandexMess.poly_settings);
		}
		
		if (null != _this.onFinish)
		{
			_this.onFinish(_this);
		}
	}
	
	this.__addPoint = function(obEvent)
	{
		if (!jsYandexCE.bAddPolyMode)
			return;

		// set line finish flag
		if (null == _this.END_POINT)
		{
			_this.END_POINT = new jsYandexCE.context.YMaps.Placemark(obEvent.getGeoPoint());
			_this.END_POINT.setIconContent(jsYandexMess.poly_finish);
			jsYandexCE.context.YMaps.Events.observe(_this.END_POINT, _this.END_POINT.Events.Click, _this.Finish);
			
			jsYandexCE.map.addOverlay(_this.END_POINT);
		}
		else
		{
			_this.END_POINT.setGeoPoint(obEvent.getGeoPoint());
		}
		
		// initate points array
		if (_this.DATA.POINTS.length <= 0)
		{
			_this.DATA.POINTS = [_this.START_POINT.getGeoPoint(), obEvent.getGeoPoint()];
		}

		if (null !== _this.POLYLINE)
		{
			// _this.DATA.POINTS is updated via reference
			_this.POLYLINE.splice(
				_this.DATA.POINTS.length, 0, obEvent.getGeoPoint()
			);

			_this.__updateViewText(jsYandexMess.noname);
		}
		else
		{
			_this.setStyle();
			_this.prepareSettingsForm();
			
			_this.POLYLINE = new jsYandexCE.context.YMaps.Polyline(
				_this.DATA.POINTS, // reference!
				{style: _this.STYLE_ID, clickable: false}
			);
			jsYandexCE.map.addOverlay(_this.POLYLINE);
			
			_this.__createView();
		}
	}
	
	this.Start = function(obPoint)
	{
		_this.START_POINT = new jsYandexCE.context.YMaps.Placemark(obPoint);
		_this.START_POINT.setIconContent(jsYandexMess.poly_start_point);
			
		jsYandexCE.map.addOverlay(_this.START_POINT);

		_this.ClickObserver = jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.Click, _this.__addPoint);
	}
	
	this.showSettingsForm = function(e)
	{
		if (!_this.bFinished)
			_this.Finish();
	
		if (_this.END_POINT._balloonVisible)
			_this.END_POINT.closeBalloon();
		else
		{
			jsYandexCE.map.addOverlay(_this.END_POINT);
			_this.END_POINT.openBalloon();
		}
		
		return BX.PreventDefault(e);
	}
	
	this.Delete = function (e)
	{
		if (null != _this.VIEW && null != _this.VIEW.parentNode)
			_this.VIEW.parentNode.removeChild(_this.VIEW);
		
		if (null != _this.POLYLINE)
			jsYandexCE.map.removeOverlay(_this.POLYLINE);
		
		jsYandexCE.map.removeOverlay(_this.START_POINT);
		
		if (null != _this.END_POINT)
			jsYandexCE.map.removeOverlay(_this.END_POINT);
		
		_this.POLYLINE = null;
		
		if (!_this.bFinished)
			_this.Finish();
		
		if (null != _this.onDelete)
			_this.onDelete(_this);
			
		return BX.PreventDefault(e);
	};
	
	/* constructor */
	if (null != arData)
	{
		this.Create(arData)
	}
}
CBXYandexPoly.prototype.__poly_link_hover = function() {this.style.backgroundColor = "#E3E8F7"; this.firstChild.style.display = 'block';}
CBXYandexPoly.prototype.__poly_link_hout = function() {this.style.backgroundColor = "#FFFFFF"; this.firstChild.style.display = 'none';}
CBXYandexPoly.prototype.__updateViewText = function(str) {this.VIEW.firstChild.nextSibling.innerHTML = str + ' (' + this.DATA.POINTS.length + ')';}
CBXYandexPoly.prototype.getData = function()
{
	var obReturn = {POINTS:[],TITLE:'',STYLE:{}};
	
	for (var i = 0,len = this.DATA.POINTS.length; i < len; i++)
	{
		obReturn.POINTS[i] = {LAT:this.DATA.POINTS[i].getLat(),LON:this.DATA.POINTS[i].getLng()};
	}

	obReturn.TITLE = this.DATA.TITLE;
	
	obReturn.STYLE = {lineStyle:{strokeColor:this.STYLE.lineStyle.strokeColor,strokeWidth:this.STYLE.lineStyle.strokeWidth}};
	
	return obReturn;
}

CBXYandexPoly.prototype.Create = function(arPolyline)
{
	this.DATA.POINTS = [];
	
	for(var i = 0,cnt = arPolyline.POINTS.length; i < cnt; i++)
	{
		this.DATA.POINTS[i] = new jsYandexCE.context.YMaps.GeoPoint(arPolyline.POINTS[i].LON, arPolyline.POINTS[i].LAT);
	}
	
	if (null != arPolyline.STYLE && null != arPolyline.STYLE.lineStyle)
		this.DATA.STYLE.lineStyle = arPolyline.STYLE.lineStyle;
	
	this.DATA.TITLE = arPolyline.TITLE;

	this.START_POINT = new jsYandexCE.context.YMaps.Placemark(new jsYandexCE.context.YMaps.GeoPoint(arPolyline.POINTS[0].LON, arPolyline.POINTS[0].LAT));
	this.END_POINT = new jsYandexCE.context.YMaps.Placemark(new jsYandexCE.context.YMaps.GeoPoint(arPolyline.POINTS[arPolyline.POINTS.length-1].LON, arPolyline.POINTS[arPolyline.POINTS.length-1].LAT));
	
	this.END_POINT.setIconContent(this.DATA.TITLE ? this.DATA.TITLE : jsYandexMess.poly_settings);
	jsYandexCE.map.addOverlay(this.END_POINT);
	
	this.setStyle();
	
	this.POLYLINE = new jsYandexCE.context.YMaps.Polyline(
		this.DATA.POINTS, // reference!
		{style: this.STYLE_ID, clickable: true}
	);
	
	this.prepareSettingsForm();
	
	jsYandexCE.map.addOverlay(this.POLYLINE);
	
	this.__createView();
	this.setTitle();
}

CBXYandexPoly.prototype.__createView = function()
{
	this.VIEW = document.getElementById('bx_yandex_polylines').appendChild(document.createElement('LI'));
	
	var obDeleteLink = this.VIEW.appendChild(document.createElement('A'));
	//obDeleteLink.style.width = '30px';
	obDeleteLink.href = "javascript: void(0)";
	obDeleteLink.className = 'bx-yandex-delete';
	obDeleteLink.onclick = this.Delete;
	obDeleteLink.style.display = 'none';

	var obLink = this.VIEW.appendChild(document.createElement('A'));
	obLink.className = 'bx-yandex-poly';
	obLink.href = 'javascript:void(0)';
	obLink.onclick = this.showSettingsForm;
	obLink.innerHTML = window.jsYandexMess.noname + ' (' + this.DATA.POINTS.length + ')';
	
	this.VIEW.onmouseover = this.__poly_link_hover;
	this.VIEW.onmouseout = this.__poly_link_hout;
	
	return this.VIEW;
}

CBXYandexPoly.prototype.prepareSettingsForm = function()
{
	var _this = this;
	
	this.EDIT_CONTROL = jsYandexCE.context.document.createElement('FORM');

	this.END_POINT.setBalloonContent(this.EDIT_CONTROL);
	this.EDIT_CONTROL.BX_POLYLINE = this;
	
	var obContainer = jsYandexCE.context.document.createElement('DIV');
	this.EDIT_CONTROL.appendChild(obContainer);
	
	this.COLORPICKER = new jsYandexCE.context.BXColorPicker(
		{
			'id': 'CP_bx_yandex_ce',
			'name': window.jsYandexMess.poly_opt_color,
			'OnSelect': function(color) {
				if (!color)
					color = 'FF0000';
				else
					color = color.substring(1);
				
				_this.EDIT_CONTROL.elements[1].value = color;
				_this.setStyle('color', color, _this.EDIT_CONTROL.elements[0]);
			}
		}
	);
	
	obContainer.innerHTML = '<b>' + window.jsYandexMess.poly_opt_header + '</b>' +
		'<br /><br /><table class="bx-yandex-poly-settings">' + 
		'<tr><td><span style="font-size: 11px;">' + window.jsYandexMess.poly_opt_title + '</span></td><td><input type="text" value="" style="width: 125px;" onkeyup="this.form.BX_POLYLINE.setTitle(this.value)" /></td></tr>' +
		'<tr><td><span style="font-size: 11px;">' + window.jsYandexMess.poly_opt_color + '</span></td><td><input type="text" value="' + this.STYLE.lineStyle.strokeColor.substring(0,6) + '" style="width: 100px; float: left;" onkeyup="this.form.BX_POLYLINE.setStyle(\'color\', this.value, this)" /></td></tr>' +
		'<tr><td><span style="font-size: 11px;">' + window.jsYandexMess.poly_opt_width + '</span></td><td><input type="text" value="' + this.STYLE.lineStyle.strokeWidth + '"  style="width: 125px;"onkeyup="this.form.BX_POLYLINE.setStyle(\'width\', this.value, this)" /></td></tr>' +
		'<tr><td><span style="font-size: 11px;">' + window.jsYandexMess.poly_opt_opacity + '</span></td><td><input type="text" value="' + Math.round(parseInt('0x' + this.STYLE.lineStyle.strokeColor.substring(6))/2.55) + '" style="width: 125px;"onkeyup="this.form.BX_POLYLINE.setStyle(\'opacity\', this.value, this)" /></td></tr>' +
		'</table>';
	
	if (null != this.DATA.TITLE)
		this.EDIT_CONTROL.elements[0].value = this.DATA.TITLE;
	
	try
	{
		this.EDIT_CONTROL.elements[1].parentNode.appendChild(this.COLORPICKER.pCont);
	}
	catch(e) 
	{
		// i hate ie6
		this.EDIT_CONTROL.elements[1].style.width = '125px';
	}
}

CBXYandexPoly.prototype.setTitle = function(title)
{
	if (null != title)
		this.DATA.TITLE = title;
	else
		title = this.DATA.TITLE;
	
	this.END_POINT.setIconContent(title);
	
	var value_view = '';
	if (this.DATA.TITLE.length > 0)
	{
		var rnpos = this.DATA.TITLE.indexOf("\n");
		value_view = rnpos <= 0 ? this.DATA.TITLE : this.DATA.TITLE.substring(0, rnpos);
		value_view = value_view.replace(/>/g, '&gt;');
		value_view = value_view.replace(/</g, '&lt;');
	}

	this.__updateViewText(value_view ? value_view : window.jsYandexMess.noname);
}
CBXYandexPoly.prototype.setStyle = function(property, value, obInput)
{
	if (null == this.STYLE)
	{
		this.STYLE = new jsYandexCE.context.YMaps.Style();
		this.STYLE.lineStyle = new jsYandexCE.context.YMaps.LineStyle();
		this.STYLE.lineStyle.strokeColor = this.DATA.STYLE.lineStyle == null ? 'FF00007F' : this.DATA.STYLE.lineStyle.strokeColor;
		this.STYLE.lineStyle.strokeWidth = this.DATA.STYLE.lineStyle == null ? '3' : this.DATA.STYLE.lineStyle.strokeWidth;
	
		this.STYLE_ID = "bitrix#line_" + Math.random();
		
		jsYandexCE.context.YMaps.Styles.add(this.STYLE_ID, this.STYLE);
	}

	var bError = false;
	if (null != property && null != value)
	{
		switch (property)
		{
			case 'color':
				if(/^[A-F0-9]{6}$/i.test(value))
				{
					this.STYLE.lineStyle.strokeColor = value + this.STYLE.lineStyle.strokeColor.substring(6);
				}
				else
				{
					bError = true;
				}
			break;
			case 'width':
				value = parseInt(value);
				if(isNaN(value))
				{
					bError = true;
				}
				else
				{
					this.STYLE.lineStyle.strokeWidth = value;
				}
			break;
			case 'opacity':
				value = parseInt(value);
				if(!isNaN(value) && value >= 0 && value <= 100)
				{
					value = Math.round(value * 2.55).toString(16).toUpperCase();
					
					this.STYLE.lineStyle.strokeColor = this.STYLE.lineStyle.strokeColor.substring(0,6)+value;
				}
				else
					bError = true;
			break;
		}

		if (null != obInput)
		{
			if (bError)
				obInput.style.backgroundColor = 'FFB0B0';
			else
				obInput.style.backgroundColor = 'white';
		}
		
		if (!bError && null != this.POLYLINE)
		{
			this.DATA.STYLE = this.STYLE.lineStyle;
			jsYandexCE.map.removeOverlay(this.POLYLINE);
			this.POLYLINE.setStyle(this.STYLE_ID);
			jsYandexCE.map.addOverlay(this.POLYLINE);
		}
	}
}
/* /CBXYandexPoly definition */

var jsYandexCE = {
	map: null,
	arData: {},
	obForm: null,
	
	currentView: '',
	
	bPositionFixed: true,
	bAddPointMode: false,
	bAddPolyMode: false,
	
	DblClickObserver: null,
	ClickObserver: null,
	
	onInitCompleted: null,
	bInitCompleted: false,
	bInitScriptsLoaded: false,
	
	__arValidKeys: ['yandex_lat', 'yandex_lon', 'yandex_scale', 'PLACEMARKS', 'LON', 'LAT', 'TEXT', 'POLYLINES', 'POINTS', 'STYLE', 'lineStyle', 'strokeColor', 'strokeWidth','TITLE'],
	
	__currentPolyLine: null,
	__currentPolyLineObject: null,
	
	init: function(map) 
	{
		if (null != map)
			jsYandexCE.map = map; //GLOBAL_arMapObjects['system_view_edit'];
		
		jsYandexCE.context = jsYandexCE.map.bx_context;

		jsYandexCE.context.BX = window.BX;

		var obHead = jsYandexCE.context.document.getElementsByTagName('HEAD')[0];
		var arStyles = ['/bitrix/components/bitrix/map.yandex.system/templates/.default/style.css', '/bitrix/components/bitrix/map.yandex.view/settings/settings_iframe.css'];
		
		for (var i = 0;i<arStyles.length;i++)
		{
			var lnk = jsYandexCE.context.document.createElement('LINK');
			lnk.href = arStyles[i]; lnk.rel = 'stylesheet'; lnk.type = 'text/css';
			obHead.appendChild(lnk);
		}

		jsYandexCE.context.BX.loadScript('/bitrix/components/bitrix/main.colorpicker/templates/.default/script.js', function() {top.jsYandexCE.bInitScriptsLoaded=true;top.jsYandexCE.checkInitCompleted();}, jsYandexCE.context.document);
		
		jsYandexCE.context.jsColorPickerMess = window.jsColorPickerMess;

		jsYandexCE.obForm = document.forms['bx_popup_form_yandex_map'];
		jsYandexCE.obForm.onsubmit = jsYandexCE.__saveChanges;
		
		jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.Move, jsYandexCE.__getPositionValues);
		jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.Update, jsYandexCE.__getPositionValues);
		jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.ChangeType, jsYandexCE.__getPositionValues);
		
		if (!jsYandexCE.arData.yandex_lat || !jsYandexCE.arData.yandex_lon || !jsYandexCE.arData.yandex_scale)
		{
			var obPos = jsYandexCE.map.getCenter();
			jsYandexCE.arData.yandex_lat = obPos.getLat();
			jsYandexCE.arData.yandex_lon = obPos.getLng();
			jsYandexCE.arData.yandex_scale = jsYandexCE.map.getZoom();
			jsYandexCE.bPositionFixed = false;
		}
		else
		{
			jsYandexCE.bPositionFixed = true;
		}

		jsYandexCE.setControlValue('yandex_lat', jsYandexCE.arData.yandex_lat);
		jsYandexCE.setControlValue('yandex_lon', jsYandexCE.arData.yandex_lon);
		jsYandexCE.setControlValue('yandex_scale', jsYandexCE.arData.yandex_scale);

		jsYandexCE.currentView = jsYandexMess.current_view;
		
		var obType = jsYandexCE.map.getType();
		jsYandexCE.setControlValue('yandex_view', obType.getName());
		
		document.getElementById('bx_restore_position').onclick = jsYandexCE.restorePositionValues;
		document.getElementById('bx_yandex_position_fix').onclick = function () {jsYandexCE.setFixedFlag(this.checked)};
		jsYandexCE.setFixedFlag(document.getElementById('bx_yandex_position_fix').defaultChecked);
		
		document.getElementById('bx_yandex_map_controls').style.visibility = 'visible';
		document.getElementById('bx_yandex_map_address_search').style.visibility = 'visible';

		jsYandexCE.bInitCompleted = true;
		jsYandexCE.checkInitCompleted();
	},
	
	checkInitCompleted: function()
	{
		if (jsYandexCE.bInitCompleted && jsYandexCE.bInitScriptsLoaded)
		{
			if (jsYandexCE.onInitCompleted)
				jsYandexCE.onInitCompleted();
			
			return true;
		}
		else
		{
			return false;
		}
	},
	
	__getPositionValues: function()
	{
		if (jsYandexCE.bPositionFixed)
			return;
	
		var obPos = jsYandexCE.map.getCenter();
		jsYandexCE.arData.yandex_lat = obPos.getLat();
		jsYandexCE.arData.yandex_lon = obPos.getLng();
		jsYandexCE.arData.yandex_scale = jsYandexCE.map.getZoom();
		
		jsYandexCE.setControlValue('yandex_lat', jsYandexCE.arData.yandex_lat);
		jsYandexCE.setControlValue('yandex_lon', jsYandexCE.arData.yandex_lon);
		jsYandexCE.setControlValue('yandex_scale', jsYandexCE.arData.yandex_scale);
		
		var obCurrentView = jsYandexCE.map.getType();
		
		jsYandexCE.currentView = (
			obCurrentView == jsYandexCE.context.YMaps.MapType.HYBRID
			? 'HYBRID'
			: (
				obCurrentView == jsYandexCE.context.YMaps.MapType.SATELLITE
				? 'SATELLITE'
				: 'MAP'
			)
		);
		
		jsYandexCE.setControlValue('yandex_view', obCurrentView.getName());
	},
	
	restorePositionValues: function(e)
	{
		BX.PreventDefault(e);
	
		if (jsYandexCE.currentView && jsYandexCE.context.YMaps.MapType[jsYandexCE.currentView])
			jsYandexCE.map.setType(jsYandexCE.context.YMaps.MapType[jsYandexCE.currentView]);
		
		jsYandexCE.map.setZoom(jsYandexCE.arData.yandex_scale);
		jsYandexCE.map.panTo(new jsYandexCE.context.YMaps.GeoPoint(jsYandexCE.arData.yandex_lon, jsYandexCE.arData.yandex_lat));
		
		return BX.PreventDefault(e);
	},
	
	setFixedFlag: function(value)
	{
		jsYandexCE.bPositionFixed = value;
		if (!value)
			jsYandexCE.__getPositionValues();
	},
	
	setControlValue: function(control, value)
	{
		var obControl = jsYandexCE.obForm['bx_' + control];
		if (null != obControl)
			obControl.value = value;
			
		var obControlOut = document.getElementById('bx_' + control + '_value');
		if (null != obControlOut)
			obControlOut.innerHTML = value;
	},
	
	addPoint: function()
	{
		if (!jsYandexCE.bAddPointMode)
		{
			if (jsYandexCE.bAddPolyMode)
				jsYandexCE.addPolyline();

			jsYandexCE.bAddPointMode = true;
			jsYandexCE.map.disableDblClickZoom();
			document.getElementById('bx_yandex_addpoint_link').style.display = 'none';
			document.getElementById('bx_yandex_addpoint_message').style.display = 'block';
			
			jsYandexCE.DblClickObserver = jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.DblClick, jsYandexCE.__addPoint);
		}
		else
		{
			jsYandexCE.bAddPointMode = false;
			jsYandexCE.map.enableDblClickZoom();
			document.getElementById('bx_yandex_addpoint_link').style.display = 'block';
			document.getElementById('bx_yandex_addpoint_message').style.display = 'none';
			
			jsYandexCE.DblClickObserver.cleanup();
		}
	},
	
	addPolyline: function()
	{
		if (jsYandexCE.bAddPolyMode)
		{
			if (null != jsYandexCE.arData.POLYLINES && jsYandexCE.arData.POLYLINES.length > 0 && !jsYandexCE.arData.POLYLINES[jsYandexCE.arData.POLYLINES.length-1].bFinished)
				jsYandexCE.arData.POLYLINES[jsYandexCE.arData.POLYLINES.length-1].Finish();
		
			jsYandexCE.bAddPolyMode = false;
			jsYandexCE.map.enableDblClickZoom();
			document.getElementById('bx_yandex_addpoly_link').style.display = 'block';
			document.getElementById('bx_yandex_addpoly_message').style.display = 'none';
			document.getElementById('bx_yandex_addpoly_message1').style.display = 'none';
			
			jsYandexCE.DblClickObserver.cleanup();
		}
		else
		{
			if (jsYandexCE.bAddPointMode)
				jsYandexCE.addPoint();
		
			jsYandexCE.bAddPolyMode = true;
			jsYandexCE.map.disableDblClickZoom();
			document.getElementById('bx_yandex_addpoly_link').style.display = 'none';
			document.getElementById('bx_yandex_addpoly_message').style.display = 'block';
			
			jsYandexCE.DblClickObserver = jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.DblClick, jsYandexCE.__startPoly);
		}
	},

	addCustomPoint: function(arPointInfo)
	{
		if (null == jsYandexCE.arData.PLACEMARKS)
			jsYandexCE.arData.PLACEMARKS = [];

		var index = jsYandexCE.arData.PLACEMARKS.length;
		jsYandexCE.arData.PLACEMARKS[index] = new CBXYandexPoint({
			TEXT: arPointInfo.TEXT, LON: arPointInfo.LON, LAT: arPointInfo.LAT
		});
		
		jsYandexCE.arData.PLACEMARKS[index].onDelete = function () {jsYandexCE.arData.PLACEMARKS[index].DELETED = 1};
		
		return index;
	},
	
	addCustomPoly: function(arPolyInfo)
	{
		if (null == jsYandexCE.arData.POLYLINES)
			jsYandexCE.arData.POLYLINES = [];
		var index = jsYandexCE.arData.POLYLINES.length;
		
		jsYandexCE.arData.POLYLINES[index] = new CBXYandexPoly(arPolyInfo);
		jsYandexCE.arData.POLYLINES[index].onDelete = function () {jsYandexCE.arData.POLYLINES[index].DELETED = 1};
		
		return index;
	},
	
	__addPoint: function(obEvent)
	{
		if (!jsYandexCE.bAddPointMode)
			return;

		var pos = obEvent.getGeoPoint();
		var index = jsYandexCE.addCustomPoint({
			TEXT: '', LON: pos.getLng(), LAT: pos.getLat()
		});
		
		jsYandexCE.arData.PLACEMARKS[index].Edit();
	},

	__startPoly: function(obEvent)
	{
		if (!jsYandexCE.bAddPolyMode)
			return;
		
		if (null == jsYandexCE.arData.POLYLINES)
			jsYandexCE.arData.POLYLINES = [];
	
		jsYandexCE.DblClickObserver.cleanup();
		document.getElementById('bx_yandex_addpoly_message').style.display = 'none';
		document.getElementById('bx_yandex_addpoly_message1').style.display = 'block';

		
		var index = jsYandexCE.arData.POLYLINES.length;
		
		jsYandexCE.arData.POLYLINES[index] = new CBXYandexPoly();
		jsYandexCE.arData.POLYLINES[index].Start(obEvent.getGeoPoint());
		
		jsYandexCE.arData.POLYLINES[index].onFinish = function() {
			document.getElementById('bx_yandex_addpoly_message').style.display = 'block';
			document.getElementById('bx_yandex_addpoly_message1').style.display = 'none';
			jsYandexCE.DblClickObserver = jsYandexCE.context.YMaps.Events.observe(jsYandexCE.map, jsYandexCE.map.Events.DblClick, jsYandexCE.__startPoly);
		};
		
		jsYandexCE.arData.POLYLINES[index].onDelete = function () {jsYandexCE.arData.POLYLINES[index].DELETED = 1};
	},
	
	__checkValidKey: function(key)
	{
		if (Number(key) == key)
			return true;
	
		for (var i = 0, len = jsYandexCE.__arValidKeys.length; i < len; i++)
		{
			if (jsYandexCE.__arValidKeys[i] == key)
				return true;
		}
		
		return false;
	},
	
	__serialize: function(obj)
	{
  		if (typeof(obj) == 'object')
  		{
    		var str = '', cnt = 0;
		    for (var i in obj)
		    {
				if (jsYandexCE.__checkValidKey(i))
				{
					++cnt;
					str += jsYandexCE.__serialize(i) + jsYandexCE.__serialize(obj[i]);
				}
		    }
		    
    		str = "a:" + cnt + ":{" + str + "}";
    		
    		return str;
		}
		else if (typeof(obj) == 'boolean')
		{
			return 'b:' + (obj ? 1 : 0) + ';';
		}
		else if (null == obj)
		{
			return 'N;'
		}
		else if (Number(obj) == obj && obj != '' && obj != ' ')
		{
			if (Math.floor(obj) == obj)
				return 'i:' + obj + ';';
			else
				return 'd:' + obj + ';';
    	}
  		else if(typeof(obj) == 'string')
  		{
			obj = obj.replace(/\r\n/g, "\n");
			obj = obj.replace(/\n/g, "###RN###");

			var offset = 0;
			if (window._global_BX_UTF)
			{
				for (var q = 0, cnt = obj.length; q < cnt; q++)
				{
					if (obj.charCodeAt(q) > 127) offset++;
				}
			}
			
  			return 's:' + (obj.length + offset) + ':"' + obj + '";';
		}
	},
	
	__saveChanges: function()
	{
		if (!jsYandexCE.map) 
			return false;
			
		jsYandexCE.bAddPointMode = false;
		
		var arSerializeData = {
			'yandex_lat':jsYandexCE.arData.yandex_lat,
			'yandex_lon':jsYandexCE.arData.yandex_lon,
			'yandex_scale':jsYandexCE.arData.yandex_scale
		};
		
		if (jsYandexCE.arData['PLACEMARKS'])
		{
			arSerializeData.PLACEMARKS = [];
		
			for(var i = 0, len = jsYandexCE.arData.PLACEMARKS.length; i < len; i++)
			{
				if (null == jsYandexCE.arData.PLACEMARKS[i].DELETED)
					arSerializeData.PLACEMARKS[arSerializeData.PLACEMARKS.length] = jsYandexCE.arData.PLACEMARKS[i].getData();
			}
		}

		if (jsYandexCE.arData['POLYLINES'])
		{
			arSerializeData.POLYLINES = [];
		
			for(var i = 0, len = jsYandexCE.arData.POLYLINES.length; i < len; i++)
			{
				if (null == jsYandexCE.arData.POLYLINES[i].DELETED && null != jsYandexCE.arData.POLYLINES[i].POLYLINE)
					arSerializeData.POLYLINES[arSerializeData.POLYLINES.length] = jsYandexCE.arData.POLYLINES[i].getData();
			}
		}
	
		window.jsYandexCEOpener.saveData(jsYandexCE.__serialize(arSerializeData), jsYandexCE.currentView);
		
		//jsYandexCE.clear();

		return false;
	},
	
	clear: function()
	{
		jsYandexCE.bInitCompleted = false;
		jsYandexCE.bInitScriptsLoaded = false;
		
		jsYandexCE.bAddPointMode = false;
		jsYandexCE.bAddPolyMode = false;
	
		if (null != jsYandexCE.arData.PLACEMARKS && jsYandexCE.arData.PLACEMARKS.length > 0)
		{
			for (var i = 0,len = jsYandexCE.arData.PLACEMARKS.length; i<len; i++)
			{
				jsYandexCE.arData.PLACEMARKS[i].Delete();
				jsYandexCE.arData.PLACEMARKS[i] = null;
			}
			jsYandexCE.arData.PLACEMARKS = [];
		}

		if (null != jsYandexCE.arData.POLYLINES && jsYandexCE.arData.POLYLINES.length > 0)
		{
			for (var i = 0,len = jsYandexCE.arData.POLYLINES.length; i<len; i++)
			{
				jsYandexCE.arData.POLYLINES[i].Delete();
				jsYandexCE.arData.POLYLINES[i] = null;
			}
			jsYandexCE.arData.POLYLINES = [];
		}
		
		jsYandexCE.map = null;
	}
}

var jsYandexCESearch = {
	bInited: false,

	map: null,
	geocoder: null,
	obInput: null,
	timerID: null,
	timerDelay: 1000,
	
	arSearchResults: [],
	
	obOut: null,
	
	__init: function(input)
	{
		if (jsYandexCESearch.bInited) return;
		
		jsYandexCESearch.map = jsYandexCE.map;
		jsYandexCESearch.obInput = input;
		
		input.form.onsubmit = function() {jsYandexCESearch.doSearch(); return false;}
		
		input.onfocus = jsYandexCESearch.showResults;
		input.onblur = jsYandexCESearch.hideResults;
		
		jsYandexCESearch.bInited = true;
	},
	
	setTypingStarted: function(input)
	{
		if (!jsYandexCESearch.bInited)
			jsYandexCESearch.__init(input);

		jsYandexCESearch.hideResults();
			
		if (null != jsYandexCESearch.timerID)
			clearTimeout(jsYandexCESearch.timerID);
	
		jsYandexCESearch.timerID = setTimeout(jsYandexCESearch.doSearch, jsYandexCESearch.timerDelay);
	},
	
	doSearch: function()
	{
		var value = BX.util.trim(jsYandexCESearch.obInput.value);
		if (value.length > 1)
		{
			var geocoder = new jsYandexCE.context.YMaps.Geocoder(value);
		
			jsYandexCE.context.YMaps.Events.observe(
				geocoder, 
				geocoder.Events.Load, 
				jsYandexCESearch.__searchResultsLoad
			);
			
			jsYandexCE.context.YMaps.Events.observe(
				geocoder, 
				geocoder.Events.Fault, 
				jsYandexCESearch.handleError
			);
		}
	},
	
	handleError: function(error)
	{
		alert(this.jsMess.mess_error + ': ' + error.message);
	},
	
	__generateOutput: function()
	{
		var obPos = BX.pos(jsYandexCESearch.obInput);
		
		jsYandexCESearch.obOut = document.body.appendChild(document.createElement('UL'));
		jsYandexCESearch.obOut.className = 'bx-yandex-address-search-results';
		jsYandexCESearch.obOut.style.top = (obPos.bottom + 2) + 'px';
		jsYandexCESearch.obOut.style.left = obPos.left + 'px';
		jsYandexCESearch.obOut.style.zIndex = parseInt(BX.WindowManager.Get().zIndex) + 200;
	},

	__searchResultsLoad: function(geocoder)
	{
		var _this = jsYandexCESearch;
	
		if (null == _this.obOut)
			_this.__generateOutput();
			
		_this.obOut.innerHTML = '';
		_this.clearSearchResults();
		
		if (len = geocoder.length()) 
		{
			for (var i = 0; i < len; i++)
			{
				_this.arSearchResults[i] = geocoder.get(i);
				
				var obListElement = document.createElement('LI');
				
				if (i == 0)
					obListElement.className = 'bx-yandex-first';

				var obLink = document.createElement('A');
				obLink.href = "javascript:void(0)";
				var obText = obLink.appendChild(document.createElement('SPAN'));
				obText.appendChild(document.createTextNode(_this.arSearchResults[i].text));
				
				obLink.BXSearchIndex = i;
				obLink.onclick = _this.__showSearchResult;
				
				obListElement.appendChild(obLink);
				_this.obOut.appendChild(obListElement);
			}
		} 
		else 
		{
			//var str = _this.jsMess.mess_search_empty;
			_this.obOut.innerHTML = '<li class="bx-yandex-notfound">' + window.jsYandexMess.nothing_found + '</li>';
		}
		
		_this.showResults();
		
		//_this.map.redraw();
	},
	
	__showSearchResult: function(e)
	{
		if (null !== this.BXSearchIndex)
		{
			jsYandexCESearch.map.panTo(jsYandexCESearch.arSearchResults[this.BXSearchIndex].getGeoPoint());
			jsYandexCESearch.map.redraw();
		}
		
		return BX.PreventDefault(e);
	},
	
	showResults: function()
	{
		if (null != jsYandexCESearch.obOut)
			jsYandexCESearch.obOut.style.display = 'block';
	},

	hideResults: function()
	{
		if (null != jsYandexCESearch.obOut)
		{
			setTimeout("jsYandexCESearch.obOut.style.display = 'none'", 300);
		}
	},
	
	clearSearchResults: function()
	{
		for (var i = 0; i < jsYandexCESearch.arSearchResults.length; i++)
		{
			delete jsYandexCESearch.arSearchResults[i];
		}

		jsYandexCESearch.arSearchResults = [];
	},
	
	clear: function()
	{
		if (!jsYandexCESearch.bInited)
			return;
			
		jsYandexCESearch.bInited = false;
		if (null != jsYandexCESearch.obOut)
		{
			jsYandexCESearch.obOut.parentNode.removeChild(jsYandexCESearch.obOut);
			jsYandexCESearch.obOut = null;
		}
		
		jsYandexCESearch.arSearchResults = [];
		jsYandexCESearch.map = null;
		jsYandexCESearch.geocoder = null;
		jsYandexCESearch.obInput = null;
		jsYandexCESearch.timerID = null;
	}
}

