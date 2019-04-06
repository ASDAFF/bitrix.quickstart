if(typeof altasib_geobase=="undefined")
	var altasib_geobase = new Object();

$(document).ready(function(){
	altasib_geobase.parse_city();
	altasib_geobase.replace();
});

altasib_geobase.replace = function(){ // altasib_geobase_replace
	var country = "", city = '', region = '';

	if(typeof altasib_geobase.city != "undefined")
		city = altasib_geobase.city;
	if(typeof altasib_geobase.region != "undefined" && altasib_geobase.region != "undefined"
		&& altasib_geobase.region != "undefined undefined")
		region = altasib_geobase.region;

	if(typeof altasib_geobase.country != "undefined")
		country = altasib_geobase.country;
	else if(typeof altasib_geobase.def_location != "undefined")
		country = altasib_geobase.def_location;
	else
		country = '';


	if(typeof altasib_geobase.region != "undefined" || typeof altasib_geobase.city != "undefined"){
		var field_loc = altasib_geobase.field_loc_ind;
		if(typeof field_loc=='undefined' || field_loc==null || field_loc=='')
			field_loc = 'ORDER_PROP_2';

		if(field_loc[0] != "#")
			field_loc = '#'+field_loc;

		var fLocVal = $(field_loc+'_val');
		var fLoc = $(field_loc);

		if(typeof fLoc=='undefined' || fLoc.length==0){
			fLocVal = $('input[name="'+altasib_geobase.field_loc_ind+'"]');
			fLoc = $('input[name="'+altasib_geobase.field_loc_ind+'"]');
		}
		if(fLocVal.length != 0){ // Template location - Search String
			fLocVal.val(city+', '+region+', '+country);
			if(region != '')
				fLocVal.attr('value', city+', '+region+', '+country);
			else
				fLocVal.attr('value', city+', '+country);

			$.ajax({
				url: 'http://'+window.location.host+
					'/bitrix/components/bitrix/sale.ajax.locations/search.php?search='+city.replace(/[\u0080-\uFFFF]/g,
						function(s){ return "%u"+('000'+s.charCodeAt(0).toString(16)).substr(-4); }),
				data:{ 'params': 'siteId:'+BX.message('SITE_ID')},
				async: false,
				success: function(out){
					if(out.length > 2){
						out = $.parseJSON(out.replace(new RegExp("'",'g'),'"'));
						if(out !== null){
							if(out.length > 0)
								out = out[0];
							if(typeof out=='object'){
								fLocVal.val(out['NAME']+', '+out['REGION_NAME']+', '+out['COUNTRY_NAME']);
								if(out['REGION_NAME']!= '')
									fLocVal.attr('value', out['NAME']+', '+out['REGION_NAME']+', '+out['COUNTRY_NAME']);
								else
									fLocVal.attr('value', out['NAME']+', '+out['COUNTRY_NAME']);

								fLoc.val(out['ID']);
								altasib_geobase.send_form();
							}
						}
					}
				}
			});

			$('body').on('click', '#PERSON_TYPE_2', function(){
				var intervalTimer = setInterval(function(){
					var field_loc_leg = altasib_geobase.field_loc_leg;
					if(typeof field_loc_leg=='undefined' || field_loc_leg==null || field_loc_leg=='')
						field_loc_leg = 'ORDER_PROP_3';

					if(field_loc_leg[0] != "#")
						field_loc_leg = '#'+field_loc_leg;

					var fLocJurVal = $(field_loc_leg+'_val');
					if(typeof fLocJurVal=='undefined' || fLocJurVal.length==0)
						fLocJurVal = $('input[name="'+altasib_geobase.field_loc_leg+'"]');

					if(fLocJurVal.length != 0){
						fLocJurVal.val(city+', '+region+', '+country);
						if(region != '')
							fLocJurVal.attr('value', city+', '+region+', '+country);
						else
							fLocJurVal.attr('value', city+', '+country);

						$.ajax({
							url: 'http://'+window.location.host+
								'/bitrix/components/bitrix/sale.ajax.locations/search.php?search='+city.replace(/[\u0080-\uFFFF]/g,
									function(s){ return "%u"+('000'+s.charCodeAt(0).toString(16)).substr(-4); }),
							data:{'params': 'siteId:'+BX.message('SITE_ID')},
							async: false,
							success: function(out){
								var tmp = out.split('NAME'),
									townId = tmp[0].split("'")[3];

								var oLeg = $(field_loc_leg);
								if(typeof oLeg=='undefined' || oLeg.length==0)
									$('input[name="'+altasib_geobase.field_loc_leg+'"]').val(townId);
								else
									oLeg.val(townId);

								clearInterval(intervalTimer);
								if(out != '[]')
									eval("out = "+out+";");

								var fLocVal = $(field_loc+'_val');
								if(typeof fLocVal=='undefined' || fLocVal.length==0)
									fLocVal = $('input[name="'+altasib_geobase.field_loc_leg+'"]');

								if(out==null)
									out = $.parseJSON(out.replace(new RegExp("'",'g'),'"'));
								if(out !== null && out.length > 2){
									if(out['REGION_NAME']!= '')
										fLocVal.attr('value', out[0]['NAME']+', '+out[0]['REGION_NAME']+', '+out[0]['COUNTRY_NAME']);
									else
										fLocVal.attr('value', out[0]['NAME']+', '+out[0]['COUNTRY_NAME']);
								}
								altasib_geobase.send_form();
							}
						});
					}
				}, 1000);
			});

			$('body').on('click', '#PERSON_TYPE_1', function(){
				var intervalTimer = setInterval(function(){
					var fLocVal = $(field_loc+'_val');
					if(typeof fLocVal=='undefined' || fLocVal.length==0)
						fLocVal = $('input[name="'+altasib_geobase.field_loc_ind+'"]');

					if(fLocVal.length != 0){
						fLocVal.val(city+', '+region+', '+country);
						if(region != '')
							fLocVal.attr('value', city+', '+region+', '+country);
						else
							fLocVal.attr('value', city+', '+country);

						$.ajax({
							url: 'http://'+window.location.host+
								'/bitrix/components/bitrix/sale.ajax.locations/search.php?search='+city.replace(/[\u0080-\uFFFF]/g,
									function(s){ return "%u"+('000'+s.charCodeAt(0).toString(16)).substr(-4); }),
							data:{
								'params': 'siteId:'+BX.message('SITE_ID')
							},
							async: false,
							success: function(out){
								var tmp = out.split('NAME'),
									townId = tmp[0].split("'")[3];

								fLoc = $(field_loc);
								if(typeof fLoc=='undefined' || fLoc.length==0)
									fLoc = $('input[name="'+altasib_geobase.field_loc_ind+'"]');

								fLoc.val(townId);

								clearInterval(intervalTimer);

								altasib_geobase.send_form();
							}
						});
					}
				}, 1000);
			});
		} else{ // Editing the Personal profile (easy mode)
			$('select[name="'+altasib_geobase.field_loc_ind+'"] option').each(function(){
				if($(this).text()==country+' - '+city)
					tmpVal = $(this).val();

				if($(this).attr('selected')=='selected')
					$(this).removeAttr('selected');

				$('select[name="'+altasib_geobase.field_loc_ind+'"]').val(tmpVal);
			});
		}
	}
}

altasib_geobase.parse_city = function(){ // altasib_geobase_parse_city
	if((altasib_geobase.manual_code = altasib_geobase.getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE_CODE')) !== null){
		altasib_geobase.manual_code = decodeURIComponent(altasib_geobase.manual_code.replace(/\+/g, " "));
		altasib_geobase.manual_code = $.parseJSON(altasib_geobase.manual_code);
	}
	if((altasib_geobase.auto_code = altasib_geobase.getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE')) !== null){
		altasib_geobase.auto_code = decodeURIComponent(altasib_geobase.auto_code.replace(/\+/g, " "));
		altasib_geobase.auto_code = $.parseJSON(altasib_geobase.auto_code);
	}

	if(altasib_geobase.manual_code !== null){
		if(typeof altasib_geobase.manual_code['CITY'] != 'undefined'){
			if(typeof altasib_geobase.manual_code['CITY']['NAME'] != 'undefined')
				altasib_geobase.city = altasib_geobase.manual_code['CITY']['NAME'];
			else if(typeof altasib_geobase.manual_code['CITY']=='string')
				altasib_geobase.city = altasib_geobase.manual_code['CITY'];
		}
		else if(typeof altasib_geobase.manual_code['CITY_RU'] != 'undefined')
			altasib_geobase.city = altasib_geobase.manual_code['CITY_RU'];
		else if(typeof altasib_geobase.manual_code['CITY_NAME'] != 'undefined')
			altasib_geobase.city = altasib_geobase.manual_code['CITY_NAME'];

		if(typeof altasib_geobase.manual_code['REGION'] != 'undefined'){
			if(typeof altasib_geobase.manual_code['REGION']['NAME'] != 'undefined')
				altasib_geobase.region = altasib_geobase.manual_code['REGION']['NAME']+' '
				+(typeof altasib_geobase.manual_code['REGION']['SOCR'] != 'undefined' ?
					altasib_geobase.manual_code['REGION']['SOCR'] : '');
			else if(typeof altasib_geobase.manual_code['REGION']=='string')
				altasib_geobase.region = altasib_geobase.manual_code['REGION'];
		}
		else if(typeof altasib_geobase.manual_code['REGION_NAME'] != 'undefined')
			altasib_geobase.region = altasib_geobase.manual_code['REGION_NAME'];

	} else if(altasib_geobase.auto_code !== null){
		altasib_geobase.city = altasib_geobase.auto_code['CITY_NAME'];
		altasib_geobase.region = altasib_geobase.auto_code['REGION_NAME'];
	}
}

altasib_geobase.getCookie = function(name){ // altasib_geobase_getCookie
	var nameEQ = name+'=';
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++){
		var c = ca[i];
		while(c.charAt(0)==' ')
			c = c.substring(1, c.length);
		if(c.indexOf(nameEQ)==0)
			return c.substring(nameEQ.length, c.length);
	}
	return null;
}
altasib_geobase.send_form = function(){
	if(typeof submitForm != 'undefined' && typeof submitForm=='function'){
		if(altasib_geobase.is_mobile){
			if(!$('div#altasib_geobase_mb_popup').is(':visible') && !altasib_geobase.sc_is_open && !$('div#altasib_geobase_mb_window').is(':visible'))
				submitForm();
		} else{
			if(!$('div#altasib_geobase_popup').is(':visible') && !altasib_geobase.sc_is_open && !$('div#altasib_geobase_window').is(':visible'))
				submitForm();
		}
	}
}