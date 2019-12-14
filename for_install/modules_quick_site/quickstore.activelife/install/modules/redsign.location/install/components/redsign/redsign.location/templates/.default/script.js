var RS = RS || {};

RS.namespace = function(ns) {
	var parts = ns.split('.'),
		parent = RS,
		i;
		
	if (parts[0] === 'RS') {
		parts = parts.slice(1);
	}
	
	for (i = 0; i < parts.length; i+= 1) {
		if (typeof parent[parts[i]] === "undefined") {
			parent[parts[i]] = {};
		}
		parent = parts[i];
	}

	return parent;
};

RS.namespace('RS.Cookie');
RS.namespace('RS.Popup');
RS.namespace('RS.Download');

RS.Cookie = (function() {

	//Устанавливаем cookie
	setCookie = function(name, value) {
		var date = new Date();
		
		if (getCookie(name) !== null) {
			deleteCookie(name);
		}
		
		date.setDate(365 + date.getDate());
		var domain = window.location.hostname;

		var tmpAr = domain.split('.');
		if (tmpAr[0] === 'www' || tmpAr.length === 4) {
			tmpAr.shift();
			domain = tmpAr.join('.');
		}
		if(tmpAr.length == 1){
			domain = '';
		}

		document.cookie = name + "=" + value + "; path=/; expires="+ date.toGMTString() + "; domain=" + domain;
	},
	//Устанавливаем cookie для list
	setCookieList = function(location) {
		var Elems = location.split(', ');

		if (Elems.length == 2) {
			setCookie('REDSIGN_CITY', Elems[1] + '/empty/' + Elems[0]);
		} else {
			setCookie('REDSIGN_CITY', Elems[2] + '/' + Elems[1] + '/' + Elems[0]);
		}
	},
	
	//Устанавливаем cookie после submit
	setCookieButtonClick = function() {
		var city = $('.rs-city-header').text();
		$.ajax({
			url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/get.php?city_name=' + encodeURI(city),
			async: false,
			success: function(out)	{
				var tmp = out.split('/');
				
				if (tmp.length == 1 || tmp.length == 0) {
					setCookie('REDSIGN_CITY', out + '/empty/' + city);
				} else if (tmp.length == 2) {
					setCookie('REDSIGN_CITY', out + '/' + city);
				}
			}
		});
	},
	
	//получаем cookie
	getCookie = function(name) {
		var cookie = " " + document.cookie,
			search = " " + name + "=",
			setStr = null,
			offset = 0,
			end = 0;

		if (cookie.length > 0) {
			offset = cookie.indexOf(search);
			
			if (offset != -1) {
				offset += search.length;
				end = cookie.indexOf(";", offset);
				
				if (end == -1) {
					end = cookie.length;
				}
				setStr = unescape(cookie.substring(offset, end));
			}
		}
		return setStr;
	},
	
	//получаем cookie текущего города
	getCookieCity = function(name) {
		var cookie = getCookie(name);
		
		if (cookie !== null) {

			if (cookie.indexOf('/') == -1) {
				return cookie;
				
			} else {
				//если есть
				return cookie.split('/')[2];
			}
		}
		
		return null;
	},
	
	getCookieCityId = function(name) {
		var cookie = getCookie(name),
			masid = Array();
		
		if (cookie !== null) {
			$.ajax({
				url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/get.php?city=' + encodeURI(cookie.split('/')[2]),
				async: false,
				dataType: 'json',
				success: function(out)	{
					masid = out;
					
				}
			});
			return masid;
		}
	},
	
	getCookieAllName = function(name) {
		var cookie = getCookie(name),
			masAll = Array();
		
		if (cookie !== null) {
			$.ajax({
				url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/get.php?all_name=' + encodeURI(cookie.split('/')[2]),
				async: false,
				dataType: 'json',
				success: function(out)	{
					masAll = out;
				}
			});
			
			return masAll;
		}
	},
	
	getCookieProps = function(name) {
		var cookie = getCookie(name),
			props = Array();
		
		if (cookie !== null) {
			$.ajax({
				url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/get.php?props=' + encodeURI(cookie.split('/')[2]),
				async: false,
				dataType : "json",
				success: function(out)	{
					props = out;
				}
			});
			
			return props;
		}
	},
	
	//удаляем cookie
	deleteCookie = function(cookieName) {
		var cookieDate = new Date();  
		cookieDate.setTime (cookieDate.getTime() - 1);
		document.cookie = cookieName += "=; expires=" + cookieDate.toGMTString();
	}
	
	return {
		setCookieList:				setCookieList,
		setCookieButtonClick: 	    setCookieButtonClick,	
		getCookieCity: 				getCookieCity,
		getCookieCityId:			getCookieCityId,
		getCookieAllName:           getCookieAllName,
		getCookieProps:             getCookieProps
	};
}());

RS.Popup = (function() {
	var rscookie = RS.Cookie;

	show = function() {
		var city = rscookie.getCookieCity('REDSIGN_CITY');
		var mas = rscookie.getCookieAllName('REDSIGN_CITY');

		if (city !== null) {
			$('.rs-city-header').text(city);
			$('.rscountry').text(mas['country_name']);
			$('.rsregion').text(mas['region_name']);
		}
		if($('.geoip_city').text() == ''){
			$('.rs-my-city').css('display', 'none');
			$('.rs-city-header').css('display', 'none');
			$('.rs-vopros').css('display', 'none');
			$('.rs-geocountry').css('display', 'none');
		}else{
			$('.rs-my-city').css('display', 'block');
			$('.rs-city-header').css('display', 'inline');
			$('.rs-vopros').css('display', 'inline');
			$('.rs-geocountry').css('display', 'block');
			$('.rs-city-header-not').css('display', 'none');
		}
		$('#rs-location').css('display', 'block');
	},

	hide = function() {
		$('#rs-location').css('display', 'none');
		$('.rs-download').css('display', 'none');
		$('#rs-location input.txt').val('');
	};
	
	return {
		show: show,
		hide: hide
	};
}());

RS.Download = function () {
	var rscookie = RS.Cookie,
		rspopup = RS.Popup,
		tmp_ = 0,
		aEl = null,
		aResult = Array(),
		aRes = Array(),
		iCnt = 0,
		sPrefix = '',
		spisok = function (query) {
			$.ajax({
				url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/search.php?query=' + encodeURI(query),
				dataType : "json", 
				success: function (result) { 
					
					$('.rs-download').empty();
					for (tmp_ in result)
					{
						aEl = result[tmp_];
						aRes = Array();
						aRes['ID'] = (aEl['ID'] && aEl['ID'].length > 0) ? aEl['ID'] : iCnt++;
						aRes['GID'] = sPrefix + '_' + aRes['ID'];
						
						locName = aEl['NAME'];
						if (aEl['REGION_NAME'].length > 0 && locName.length <= 0)
							locName = aEl['REGION_NAME'];
						else if (aEl['REGION_NAME'].length > 0)
							locName = locName +', '+ aEl['REGION_NAME'];
						
						if (aEl['COUNTRY_NAME'].length > 0)
							locName = locName +', '+ aEl['COUNTRY_NAME'];
							
						aRes['NAME'] = locName.replace(/&amp;/g, '&amp;amp;').replace(/&lt;/g, '&amp;lt;').replace(/&gt;/g, '&amp;gt;').replace(/&quot;/g, '&amp;quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
						aResult[aRes['GID']] = aRes;
						$('.rs-download').append('<div>'+aRes['NAME']+'</div>').css('display', 'block');
					}
					
					$('.rs-download div').each(function () {
					
						$(this).hover(function () {
							$(this).addClass('rs-act-sel');
						}, function () {
							$(this).removeClass('rs-act-sel');
						});
						
						$(this).on('click', function () {
							rscookie.setCookieList($(this).text());
							rspopup.hide();
							
							$('a.geoip_city').text(rscookie.getCookieCity('REDSIGN_CITY'));
							$('a.geoip_city').css('display', 'inline');

							if (window.location.href.indexOf('/compare/') != -1) {
								window.location.reload();
							}
						});
					});
				}
			});
		};

	return {
		List: spisok
	};
}();

$(document).ready(function() {

	var rsLocCookie = RS.Cookie,
		rsPopup  = RS.Popup,
		rsDownload	= RS.Download,
		city = rsLocCookie.getCookieCity('REDSIGN_CITY'),
		masId = rsLocCookie.getCookieCityId('REDSIGN_CITY'),
		props = rsLocCookie.getCookieProps('REDSIGN_CITY'),
		data,
		inp_name,
		inp_name2,
		local_type_1,
		local_type_2,
		tmpVal;
				
	if (city === null) {
		rsPopup.show();
	} else {
		$('a.geoip_city').text(city);
		$('a.geoip_city').css('display', 'inline');
	
		local_type_1 = $('#LOCATION_ORDER_PROP_' + props['type1']);
		inp_name = 'ORDER_PROP_' + props['type1'];
		// Шаблон местоположения - По умолчанию
		if (local_type_1.length != 0 ) {
		
			local_type_1.empty();
			
			data = {"CITY_INPUT_NAME" : inp_name,'COUNTRY' : masId['country_id'],'REGION' : masId['region_id'],'CITY_OUT_LOCATION' : 'Y', 'ALLOW_EMPTY_CITY' : 'Y'};
			if ( $('[id^=ID_PAY_SYSTEM_ID]').length != 0 ) { data['ONCITYCHANGE'] = 'submitForm()';}
			
			$.ajax({
				type: 'POST',
				url: 'http://' + window.location.host + '/bitrix/components/bitrix/sale.ajax.locations/templates/.default/ajax.php',
				data: data,
				async: false,
				success: function(out) {
					local_type_1.prepend(out);
					$('select[name="'+inp_name+'"]').val(masId['city_id']).change();
				}
			});
			
			
			$(document).on('click', '#PERSON_TYPE_1', function(){
				setTimeout(function(){
				local_type_1 = $('#LOCATION_ORDER_PROP_' + props['type1']);
				inp_name = 'ORDER_PROP_' + props['type1'];
						$.ajax({
							type: 'POST',
							url: 'http://' + window.location.host + '/bitrix/components/bitrix/sale.ajax.locations/templates/.default/ajax.php',
							data: {"CITY_INPUT_NAME" : inp_name, 'ONCITYCHANGE' : 'submitForm()', 'COUNTRY' : masId['country_id'],'REGION' : masId['region_id'],'CITY_OUT_LOCATION' : 'Y', 'ALLOW_EMPTY_CITY' : 'Y'},
							async: false,
							success: function(out) {
								local_type_1.empty();
								local_type_1.prepend(out);	
								$('select[name="'+inp_name+'"]').val(masId['city_id']).change();
							}								
						});
				}, 1000);
			});
			
			$(document).on('click', '#PERSON_TYPE_2', function(){

				setTimeout(function(){
				local_type_2 = $('#LOCATION_ORDER_PROP_' + props['type2']);
				inp_name2 = 'ORDER_PROP_' + props['type2'];	
						$.ajax({
							type: 'POST',
							url: 'http://' + window.location.host + '/bitrix/components/bitrix/sale.ajax.locations/templates/.default/ajax.php',
							data: { 'CITY_INPUT_NAME' : inp_name2 , 'ONCITYCHANGE' : 'submitForm()','COUNTRY' : masId['country_id'],'REGION' : masId['region_id'],'CITY_OUT_LOCATION' : 'Y', 'ALLOW_EMPTY_CITY' : 'Y'},
							async: false,
							success: function(out) {
								local_type_2.empty();
								local_type_2.prepend(out);
								$('select[name="'+inp_name2+'"]').val(masId['city_id']).change();				
							}
						});
				}, 1000);
			});
			
		}else if( $('#ORDER_PROP_' + props['type1'] + '_val').length != 0 ) {
		
			// Шаблон местоположения - Строка поиска физ лица
			$.ajax({
				url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/search.php?query=' + encodeURI(city),
				dataType : "json", 
				success: function(res) {
					$('#ORDER_PROP_' + props['type1'] + '_val').attr('value', res[0]['NAME'] + ', ' + res[0]['REGION_NAME'] + ', ' + res[0]['COUNTRY_NAME']);
					$('#ORDER_PROP_' + props['type1']).val(res[0]['ID']);
				}
			});
			
			$(document).on('click', '#PERSON_TYPE_1', function(){
				setTimeout(function(){
						$.ajax({
							url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/search.php?query=' + encodeURI(city),
							dataType : "json", 
							success: function(res) {
								$('#ORDER_PROP_' + props['type1'] + '_val').attr('value', res[0]['NAME'] + ', ' + res[0]['REGION_NAME'] + ', ' + res[0]['COUNTRY_NAME']);
								$('#ORDER_PROP_' + props['type1']).val(res[0]['ID']);
							}
						});
				}, 1000);
			});
			
			$(document).on('click', '#PERSON_TYPE_2', function(){
				setTimeout(function(){	
						$.ajax({
							url: 'http://' + window.location.host + '/bitrix/components/redsign/redsign.location/search.php?query=' + encodeURI(city),
							dataType : "json", 
							success: function(res) {
								$('#ORDER_PROP_' + props['type2'] + '_val').attr('value', res[0]['NAME'] + ', ' + res[0]['REGION_NAME'] + ', ' + res[0]['COUNTRY_NAME']);
								$('#ORDER_PROP_' + props['type2']).val(res[0]['ID']);
							}
						});
				}, 1000);
			});
			
		}
	} 
	
	
	// ------------ click  --------------- //
	$(document).on('click', 'a.geoip_city', function(){
		rsPopup.show();
	});
	
	$(document).on('click', 'a.close', function(){
		rsPopup.hide();
	});
	
	$(document).on('click', '.rs-my-city .button', function(){
		rsLocCookie.setCookieButtonClick();
		rsPopup.hide();
		
		$('a.geoip_city').text(rsLocCookie.getCookieCity('REDSIGN_CITY'));
		$('a.geoip_city').css('display', 'inline');
	});
	// ------------- / click ---------------//
	
	// -------------   keyup --------------//
	$(document).on('keyup', '.txt', function(){
		if ($(this).val().length > 1)
		{
			rsDownload.List( $(this).val() );
			
		} else if($(this).val().length <= 1) {
			$('.rs-download').css('display', 'none');
		}
	});
	// ------------- / keyup --------------//
	
});