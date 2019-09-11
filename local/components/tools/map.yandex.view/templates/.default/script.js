/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!window.BX_YMapAddPlacemark)
{
	window.BX_YMapAddPlacemark = function(map, arPlacemark)
	{
		if (null == map)
			return false;
		
		if(!arPlacemark.LAT || !arPlacemark.LON)
			return false;
		
		var obPlacemark = new map.bx_context.YMaps.Placemark(new map.bx_context.YMaps.GeoPoint(arPlacemark.LON, arPlacemark.LAT), {style:window.plainstyle});
		
		if (null != arPlacemark.TEXT && arPlacemark.TEXT.length > 0)
		{
			obPlacemark.setBalloonContent(arPlacemark.TEXT.replace(/\n/g, '<br />'));

			var value_view = '';
			if (arPlacemark.TEXT.length > 0)
			{
				var rnpos = arPlacemark.TEXT.indexOf("\n");
				value_view = rnpos <= 0 ? arPlacemark.TEXT : arPlacemark.TEXT.substring(0, rnpos);
				//value_view = value_view.replace(/>/g, '&gt;');
				//value_view = value_view.replace(/</g, '&lt;');
			}
			
			obPlacemark.setIconContent(value_view);
		}

		map.addOverlay(obPlacemark);
		
		return obPlacemark;
	}
}

if (!window.BX_YMapAddPolyline)
{
	window.BX_YMapAddPolyline = function(map, arPolyline)
	{
		if (null == map)
			return false;
		
		if (null != arPolyline.POINTS && arPolyline.POINTS.length > 1)
		{
			var arPoints = [];
			for (var i = 0, len = arPolyline.POINTS.length; i < len; i++)
			{
				arPoints[i] = new map.bx_context.YMaps.GeoPoint(arPolyline.POINTS[i].LON, arPolyline.POINTS[i].LAT);
			}
		}
		else
		{
			return false;
		}
		
		if (null != arPolyline.STYLE)
		{
			var obStyle = new map.bx_context.YMaps.Style();
			obStyle.lineStyle = new map.bx_context.YMaps.LineStyle();
			obStyle.lineStyle.strokeColor = arPolyline.STYLE.lineStyle.strokeColor;
			obStyle.lineStyle.strokeWidth = arPolyline.STYLE.lineStyle.strokeWidth;
			
			var style_id = "bitrix#line_" + Math.random();
			
			map.bx_context.YMaps.Styles.add(style_id, obStyle);
		}
		
		var obPolyline = new map.bx_context.YMaps.Polyline(
			arPoints,
			{style: style_id, clickable: true}
		);
		obPolyline.setBalloonContent(arPolyline.TITLE);
		
		map.addOverlay(obPolyline);
		
		return obPolyline;
	}
}