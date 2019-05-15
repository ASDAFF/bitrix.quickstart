if (typeof (altasib_geobase) == "undefined"){
	var altasib_geobase = new Object();
}

$(document).ready(function(){
	if(altasib_geobase.yc_init_vars())
		altasib_geobase.yc_load_html();
	
	if(altasib_geobase.is_mobile){
		$(this).keydown(function(e){
			if (e.keyCode === 27 && $('div#altasib_geobase_mb_window').is(':visible'))
				altasib_geobase.yc_close();
		});
	} else {
		$(this).keydown(function(e){
			if (e.keyCode === 27 && $('div#altasib_geobase_window').is(':visible'))
				altasib_geobase.yc_close();
		});
	}
});

altasib_geobase.yc_init_vars = function () { // altasib_geobase_yc_init_vars
	if(altasib_geobase.autoload != 'N')
		altasib_geobase.autoload = 'Y';
	altasib_geobase.no = false;
	return true;
}

altasib_geobase.yc_load_html = function (){ // altasib_geobase_yc_load_html
	if(altasib_geobase.is_mobile)
		var win = $('div#altasib_geobase_mb_window');
	else
		var win = $('div#altasib_geobase_window');
	if(win.length == 0){
		var rezult = $.ajax({
			url: '/bitrix/tools/altasib.geobase/your_city.php',
			dataType: 'html',
			data: { 'SITE_ID': BX.message('SITE_ID'),
				'locate': 'Y',
				'AUTOLOAD': altasib_geobase.autoload
			},
			type: 'POST',
			success: function (data) {
				$('body').append(data);
				altasib_geobase.yc_open();
				if(typeof altasib_geobase.parse_city != "undefined")
					altasib_geobase.parse_city();
				else
					altasib_geobase.yc_parse_city();
				if(typeof altasib_geobase.replace != "undefined") // script of module
					altasib_geobase.replace();
			}
		});
	} else {
		altasib_geobase.yc_open();
	}
}

altasib_geobase.yc_no_click = function (){ // altasib_geobase_yc_no_click
	if(!altasib_geobase.no){
		if(typeof altasib_geobase != "undefined"){
			var popup = 'altasib_geobase_popup';
			if(altasib_geobase.is_mobile)
				popup = 'altasib_geobase_mb_popup';
			
			if(typeof altasib_geobase.select_city != "undefined"
				&& document.getElementById(popup) != null){
				if (typeof altasib_geobase.sc_open != "undefined"){
					var event_status = "no_click";
					altasib_geobase.yc_close(event_status);
					altasib_geobase.sc_open();
				}
			}else
				altasib_geobase.yc_open_sc();
		}else
			altasib_geobase.yc_open_sc();
	}
	altasib_geobase.no = true;
}

altasib_geobase.yc_open_sc = function (){ // altasib_geobase_yc_open_sc
	var rezult = $.ajax({
		url: '/bitrix/tools/altasib.geobase/select_city.php',
		dataType: 'html',
		data: { 'SITE_ID': BX.message('SITE_ID'),
				'show_select': 'Y',
				'AUTOLOAD': altasib_geobase.autoload
			},
		type: 'POST',
		success: function (data) {
			$('body').append(data);
			
			altasib_geobase.yc_close();
			if (typeof altasib_geobase.sc_open != "undefined")
				altasib_geobase.sc_open();
		},
	});
}

altasib_geobase.yc_yes_click = function (cityID){ // altasib_geobase_yc_yes_click
	var sendPars = { 'SITE_ID': BX.message('SITE_ID'),
		'set_loc': 'Y',
		'city_id': cityID
	};
	if(typeof cityID == 'undefined' || cityID == ''){
		if(typeof altasib_geobase.auto_code == "undefined"){
			if((altasib_geobase.auto_code = altasib_geobase.yc_getCookie(BX.message('COOKIE_PREFIX')+'_ALTASIB_GEOBASE')) !== null){
				altasib_geobase.auto_code = decodeURIComponent(altasib_geobase.auto_code.replace(/\+/g, " "));
				altasib_geobase.auto_code = $.parseJSON(altasib_geobase.auto_code);
			}
		}
		sendPars['CITY_NAME'] = altasib_geobase.auto_code['CITY_NAME'];
		sendPars['COUNTRY_CODE'] = altasib_geobase.auto_code['COUNTRY_CODE'];
		sendPars['REGION_CODE'] = altasib_geobase.auto_code['REGION_CODE'];
	}
	var rezult = $.ajax({
		url: '/bitrix/tools/altasib.geobase/your_city.php',
		dataType: 'html',
		data: sendPars,
		type: 'POST',
		success: function (data, textStatus) {
			if(altasib_geobase.is_mobile)
				var clink = $('.altasib_geobase_mb_link span.altasib_geobase_mb_link_city')
			else
				var clink = $('.altasib_geobase_link span.altasib_geobase_link_city')
			if(clink.length > 0){
				clink.html(altasib_geobase.short_name);
				clink.attr('title', altasib_geobase.full_name);
			}
			altasib_geobase.yc_close();
			if(typeof altasib_geobase.add_city !== "undefined")
				altasib_geobase.add_city(sendPars['CITY_NAME'], sendPars['COUNTRY_CODE']);
			
			if(textStatus == 'success'){
				var ncity = (typeof sendPars['CITY_NAME'] != 'undefined' ? sendPars['CITY_NAME'] : altasib_geobase.short_name);
				if(typeof cityID == 'undefined'){
					if(typeof altasib_geobase.auto_code != "undefined")
						cityID = altasib_geobase.auto_code['CITY_ID'];
				}
				var arEventPars = [ncity, cityID, altasib_geobase.full_name, data];
				BX.onCustomEvent('onAfterSetCity', arEventPars);
			}
		},
		complete: function(data, textStatus){
			if(textStatus != 'success'){
				var ncity = (typeof sendPars['CITY_NAME'] != 'undefined' ? sendPars['CITY_NAME'] : altasib_geobase.short_name);
				if(typeof cityID == 'undefined'){
					if(typeof altasib_geobase.auto_code != "undefined")
						cityID = altasib_geobase.auto_code['CITY_ID'];
				}
				var arEventPars = [ncity, cityID, altasib_geobase.full_name, data.responseText];
				BX.onCustomEvent('onAfterSetCity', arEventPars);
			}
		}
	});
}

altasib_geobase.yc_open = function (){ // altasib_geobase_yc_open
	if(altasib_geobase.is_mobile){
		$('div#altasib_geobase_mb_window').fadeIn().animate({top: '30%'}, 750);
		$('div#altasib_geobase_yc_mb_backg').show();
	} else {
		$('div#altasib_geobase_window').fadeIn().animate({top: '30%'}, 750);
		$('div#altasib_geobase_yc_backg').show();
	}
}

altasib_geobase.yc_x_clc = function (){ // altasib_geobase_yc_x_clc
	altasib_geobase.yc_close();
	if(altasib_geobase.is_mobile)
		var batnOk = $('.altasib_geobase_yc_mb_btn.altasib_geobase_yc_mb_disabled :first').attr('onclick');
	else
		var batnOk = $('.altasib_geobase_yc_btn.altasib_geobase_yc_disabled :first').attr('onclick');
	var num = altasib_geobase.strripos (batnOk, "; return false;");
	if (num){
		var strYes = batnOk.substring(0, num);
		if(strYes.length > 1)
			eval(strYes);
	}
}

altasib_geobase.yc_close = function (event_status){ // altasib_geobase_yc_close
	if(altasib_geobase.is_mobile){
		var win = $('div#altasib_geobase_mb_window');
		var bcg = $('div#altasib_geobase_yc_mb_backg');
	} else {
		var win = $('div#altasib_geobase_window');
		var bcg = $('div#altasib_geobase_yc_backg');
	}
	if(event_status != "no_click"){
		win.animate({top: '-50%'}, 750);
	}
	win.fadeOut('400');
	bcg.hide();
}

altasib_geobase.strripos = function (haystack, needle, offset) { // altasib_geobase_strripos
		// Find position of last occurrence of a case-insensitive string in a string - K. Zonneveld
		var i = haystack.toLowerCase().lastIndexOf( needle.toLowerCase(), offset ); // returns -1
		return i >= 0 ? i : false;
}

$(function(){
	$(document).click(function(event){
		var block = (altasib_geobase.is_mobile ? 'div#altasib_geobase_mb_window_block' : 'div#altasib_geobase_window_block');
		if ($(block).is(':visible')){
			if($(event.target).closest(block).length) return;
			altasib_geobase.yc_close();
			event.stopPropagation();
		}
	});
});

altasib_geobase.yc_lang_set = function (){ // altasib_geobase_yc_lang_set
	altasib_geobase.lang = 'en';
	if (altasib_geobase.auto_code !== null)
		if(typeof altasib_geobase.auto_code.COUNTRY_CODE !== 'undefined'){
			if(altasib_geobase.auto_code.COUNTRY_CODE == 'RU')
				altasib_geobase.lang = 'ru';
			altasib_geobase.COUNTRY_CODE = altasib_geobase.auto_code.COUNTRY_CODE;
		}
		
	if (altasib_geobase.manual_code !== null)
		if(typeof altasib_geobase.manual_code.COUNTRY_CODE !== 'undefined'){
			if(altasib_geobase.manual_code.COUNTRY_CODE == 'RU')
				altasib_geobase.lang = 'ru';
			altasib_geobase.COUNTRY_CODE = altasib_geobase.manual_code.COUNTRY_CODE;
		}
	
	if(typeof altasib_geobase.COUNTRY_CODE == 'undefined'){
		if(altasib_geobase.manual_code != null && typeof altasib_geobase.manual_code['REGION'] !== 'undefined')
			if(typeof altasib_geobase.manual_code['REGION']['CODE'] !== 'undefined')
				altasib_geobase.COUNTRY_CODE = 'RU';
	}
}

altasib_geobase.yc_parse_city = function (){ // altasib_geobase_yc_parse_city
	if(typeof altasib_geobase.manual_code == 'undefined'){
		if((altasib_geobase.manual_code = altasib_geobase.yc_getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE_CODE')) !== null){
			altasib_geobase.manual_code = decodeURIComponent(altasib_geobase.manual_code.replace(/\+/g, " "));
			altasib_geobase.manual_code = $.parseJSON(altasib_geobase.manual_code);
		}
	}
	if(typeof altasib_geobase.auto_code == 'undefined'){
		if((altasib_geobase.auto_code = altasib_geobase.yc_getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE')) !== null){
			altasib_geobase.auto_code = decodeURIComponent(altasib_geobase.auto_code.replace(/\+/g, " "));
			altasib_geobase.auto_code = $.parseJSON(altasib_geobase.auto_code);
		}
	}
	
	altasib_geobase.yc_lang_set();
	
	if (altasib_geobase.manual_code !== null){
		if(typeof altasib_geobase.manual_code['CITY'] !== 'undefined'){
			if(typeof altasib_geobase.manual_code['CITY']['NAME'] !== 'undefined'){
				altasib_geobase.city = altasib_geobase.manual_code['CITY']['NAME'];
				altasib_geobase.region = altasib_geobase.manual_code['REGION']['NAME'] 
					+ ' ' + altasib_geobase.manual_code['REGION']['SOCR'];
			}
			else if(typeof altasib_geobase.manual_code['CITY_RU'] !== 'undefined'){
				if(altasib_geobase.lang == 'ru'){
					altasib_geobase.country = altasib_geobase.manual_code['COUNTRY_RU'];
					altasib_geobase.city = altasib_geobase.manual_code['CITY_RU'];
				} else{
					altasib_geobase.country = altasib_geobase.manual_code['COUNTRY'];
					altasib_geobase.city = altasib_geobase.manual_code['CITY'];
				}
				altasib_geobase.region = altasib_geobase.manual_code['REGION'];
				
				if(typeof altasib_geobase.manual_code['POST'] !== 'undefined')
					altasib_geobase.post = altasib_geobase.manual_code['POST'];
			}
		}
		else if(typeof altasib_geobase.manual_code['CITY_NAME'] !== 'undefined'){
			altasib_geobase.city = altasib_geobase.manual_code['CITY_NAME'];
			altasib_geobase.region = altasib_geobase.manual_code['REGION_NAME'];
			if(typeof altasib_geobase.manual_code['COUNTRY_NAME'] !== 'undefined')
				altasib_geobase.country = altasib_geobase.manual_code['COUNTRY_NAME'];
			if(typeof altasib_geobase.manual_code['POSTINDEX'] !== 'undefined')
				altasib_geobase.post = altasib_geobase.manual_code['POSTINDEX'];
		}
	} else if(altasib_geobase.auto_code !== null){
		altasib_geobase.city = altasib_geobase.auto_code['CITY_NAME'];
		altasib_geobase.region = altasib_geobase.auto_code['REGION_NAME'];
		if(typeof altasib_geobase.auto_code['COUNTRY_NAME'] !== 'undefined')
			altasib_geobase.country = altasib_geobase.auto_code['COUNTRY_NAME'];
		if(typeof altasib_geobase.auto_code['POSTINDEX'] !== 'undefined')
			altasib_geobase.post = altasib_geobase.auto_code['POSTINDEX'];
	}	
}

altasib_geobase.yc_getCookie = function (name) { // altasib_geobase_getCookie
	var nameEQ = name + '=';
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
			c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0)
			return c.substring(nameEQ.length, c.length);
	}
	return null;
}