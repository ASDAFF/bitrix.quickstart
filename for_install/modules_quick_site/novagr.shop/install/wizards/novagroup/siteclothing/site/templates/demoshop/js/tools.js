/**
* Базовый объект с набором полезных инструментов
*/
var objTools = {
	/**
	* PHP аналог number_format()
	* форматирует число с разделением групп
	*/
	number_format	: function( num, dec, pnt, sep ) {
		var i, j, kw, kd, km;
		if( isNaN(dec = Math.abs(dec)) ){
			dec = 2;
		}
		if( pnt == undefined ){
			pnt = ",";
		}
		if( sep == undefined ){
			sep = ".";
		}
		i = parseInt(num = (+num || 0).toFixed(dec)) + "";
		if( (j = i.length) > 3 ){
			j = j % 3;
		} else{
			j = 0;
		}
		km = (j ? i.substr(0, j) + sep : "");
		kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + sep);
		kd = (dec ? pnt + Math.abs(num - i).toFixed(dec).replace(/-/, 0).slice(2) : "");
		return km + kw + kd;
	},
	/**
	* PHP аналог foreach()
	* цикл перебора массивов
	*/
	forEach			: function (data, callback) {
		for(var key in data)
			if(data.hasOwnProperty(key))
				callback(key, data[key]);
	},
	/*
	* поможет получить доп. параметры из подключаемого JS файла
	*/
	initParams	: function (selector) {
    	var src = $(selector).attr("src").split("?");    
    	var args = src[src.length-1];			// выбираем последнюю часть src после ?
    	args = args.split("&");					// разбиваем параметры &
    	var parameters = {};
    	for(var i=args.length-1; i >= 0; i--)	// заносим параметры в результирующий объект
    	{
        	var parameter = args[i].split("=");
        	parameters[parameter[0]] = parameter[1];
    	}
    	return parameters;
	}

};

/*
* фишка с наследованием
*/
function extend(Child, Parent) {
	var F = function() { }
	F.prototype = Parent.prototype;
	Child.prototype = new F();
	Child.prototype.constructor = Child;
	Child.superclass = Parent.prototype;
}

function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}