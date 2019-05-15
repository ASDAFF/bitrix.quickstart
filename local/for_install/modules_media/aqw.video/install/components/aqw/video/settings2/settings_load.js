var jsAqwVideoCE2 = {
	map: null,
	arData: null,
	obForm: null,

	currentView: '',

	bPositionFixed: true,
	bAddPointMode: false,
	bAddPolyMode: false,

	DblClickObserver: null,

	__arValidKeys: ['google_lat', 'google_lon', 'google_scale', 'PLACEMARKS', 'LON', 'LAT', 'TEXT'],

	__currentPolyLine: null,
	__currentPolyLineObject: null,
	
	__checkValidKey: function(key)
	{
		if (Number(key) == key)
			return true;
	
		for (var i = 0, len = jsAqwVideoCE2.__arValidKeys.length; i < len; i++)
		{
			if (jsAqwVideoCE2.__arValidKeys[i] == key)
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
					++cnt;
					str += jsAqwVideoCE2.__serialize(i) + jsAqwVideoCE2.__serialize(obj[i]);
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
		window.jsAqwVideoCEOpener.saveData(jsAqwVideoCE2.__serialize(jsAqwVideoCE2.arData));
		return false;
	}
}
