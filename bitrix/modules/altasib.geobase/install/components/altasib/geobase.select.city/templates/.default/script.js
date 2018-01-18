$(document).ready(function(){
	$(this).keydown(function(e){
		if (e.keyCode === 27 && $('div#altasib_geobase_win').is(':visible')){
			if(altasib_geobase.is_mobile)
				altasib_geobase.sc_mb_cls();
			else
				altasib_geobase.sc_cls();
		}
	});
	altasib_geobase.sc_parse_city();
});
if (typeof altasib_geobase == "undefined")
	var altasib_geobase = new Object();

altasib_geobase.letters = '';
altasib_geobase.timer = '0';
altasib_geobase.request_numb = 0;

if(altasib_geobase.is_mobile)
	altasib_geobase.search_en = $('input').is('[name=altasib_geobase_mb_search]');
else
	altasib_geobase.search_en = $('input').is('[name=altasib_geobase_search]');
altasib_geobase.select_city = true;


altasib_geobase.sc_init_vars = function (){ // altasib_geobase_sc_init_vars
	if(altasib_geobase.is_mobile){
		altasib_geobase.search_en = $('input').is('[name=altasib_geobase_mb_search]');
		if(altasib_geobase.search_en){
			var search = $('input[name=altasib_geobase_mb_search]');
		}
	} else {
		altasib_geobase.search_en = $('input').is('[name=altasib_geobase_search]');
	
		if(altasib_geobase.search_en){
			var search = $('input[name=altasib_geobase_search]');
		}
		altasib_geobase.height = $('div#altasib_geobase_win').css('height');
	}
}

altasib_geobase.sc_repeat = function (){ // altasib_geobase_sc_repeat
	if(altasib_geobase.is_mobile){
		var list = '#altasib_geobase_mb_list_';
		var actClass = 'altasib_geobase_mb_act';
		if((actEl = $('.altasib_geobase_mb_cities .altasib_geobase_mb_fst .'+ actClass)).length == 0)
			actEl = $('.altasib_geobase_mb_cities .altasib_geobase_mb_list_ie .'+ actClass);
	} else {
		var list = '#altasib_geobase_list_';
		var actClass = 'altasib_geobase_act';
		if((actEl = $('.altasib_geobase_cities .altasib_geobase_fst .'+ actClass)).length == 0)
			actEl = $('.altasib_geobase_cities .altasib_geobase_list_ie .'+ actClass);
	}
	var flag = true;
	if(typeof altasib_geobase.codes !== 'undefined'){
		for(var i=0; i<altasib_geobase.codes.length; i++){
			if(actEl.children().is(list + altasib_geobase.codes[i])){
				flag = false;
				break;
			}
		}
		if(flag)
			actEl.remove();
		else
			actEl.removeClass(actClass);
	}
}

// click button "It is my city"
altasib_geobase.sc_onclk = function (cityid, ctryCode){ // altasib_geobase_sc_onclk
	var id = '', cityName = '';
	if(typeof ctryCode == 'undefined' || ctryCode == '')
		ctryCode = altasib_geobase.COUNTRY_CODE;
	
	if(altasib_geobase.is_mobile){
		var btn = $('#altasib_geobase_mb_btn');
		var dis = 'altasib_geobase_mb_disabled';
		var list_ = 'altasib_geobase_mb_list_';
		var search = $('input#altasib_geobase_mb_search');
		var cities = '.altasib_geobase_mb_cities';
		var fst = '.altasib_geobase_mb_fst';
		var list_ie = '.altasib_geobase_mb_list_ie';
	} else {
		var btn = $('#altasib_geobase_btn');
		var dis = 'altasib_geobase_disabled';
		var list_ = 'altasib_geobase_list_';
		var search = $('input#altasib_geobase_search');
		var cities = '.altasib_geobase_cities';
		var fst = '.altasib_geobase_fst';
		var list_ie = '.altasib_geobase_list_ie';
	}
	
	if(typeof cityid == 'undefined' && btn.hasClass(dis) && cityid != 'Enter')
		return false;
	if(typeof cityid !== 'undefined' && cityid != 'Enter' && cityid != ''){
		if(!isNaN(cityid)){
			id = cityid;
			altasib_geobase.short_name = BX.util.trim($('#'+ list_ + id).html());
			altasib_geobase.full_name = BX.util.trim($('#'+ list_ +id).attr('title'));
		} else {
			cityName = cityid;
			altasib_geobase.short_name = $('#' + list_ +cityName).html();
			altasib_geobase.full_name = $('#'+ list_ +cityName).attr('title');
		}
	}
	else if(typeof altasib_geobase.selected_id !== 'undefined'){
		id = altasib_geobase.selected_id;
	}
	altasib_geobase.sc_repeat();
	
	if (document.getElementById(list_ + (!isNaN(cityid) ? id : cityName.replace(/[_ \t]+/g, '_'))) == null){
		if((elem = $(cities + ' ' + fst)).length == 0)
			elem = $(cities + ' ' + list_ie);
		elem.prepend('<li>' +'<a id="'+ list_ + (!isNaN(cityid) ? id : cityName.replace(/[_ \t]+/g, '_'))
		+'" title="'+ search.val() +'" href="#"' +' onclick="altasib_geobase.sc_onclk(\''+ altasib_geobase.short_name 
		+'\', \''+ctryCode+'\'); return false;">'+ altasib_geobase.short_name +'</a></li>');
	}
	$.ajax({
		type: "POST",
		url: "/bitrix/tools/altasib.geobase/altasib_geobase_get.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
				'city_id': id,
				'CITY_NAME': cityName,
				'COUNTRY_CODE': ctryCode,
				'save': 'Y'
			},
		timeout: 10000,
		success: function(data, textStatus){
			altasib_geobase.after_set_city(data, cityid, cityName, id);
		},
		complete: function(data, textStatus){
			if(textStatus != 'success')
				altasib_geobase.after_set_city(data.responseText, cityid, cityName, id);
		}
	});
}

altasib_geobase.after_set_city = function (data, cityid, cityName, id){ // altasib_geobase_after_set_city
	if(altasib_geobase.is_mobile){
		var list = $('div#altasib_geobase_mb_info');
	} else {
		var list = $('div#altasib_geobase_info');
	}
	list.html('');
	if(data == '' || data == null)
		list.animate({ height: 'hide' }, "fast");
	else{
		if(data == '1' || data[data.length-1] == '1'){
			if(altasib_geobase.is_mobile){
				$('#altasib_geobase_mb_list_'+ (!isNaN(cityid) ? id : cityName.replace(/[_ \t]+/g, '_')))
					.parent().addClass('altasib_geobase_mb_act');
				$('#altasib_geobase_mb_btn').addClass('altasib_geobase_mb_disabled');
				
				var clink = $('.altasib_geobase_mb_link span.altasib_geobase_mb_link_city');
				var search = $('input#altasib_geobase_mb_search');
				window.setTimeout('altasib_geobase.sc_mb_cls()', 275);
			} else {
				$('#altasib_geobase_list_'+ (!isNaN(cityid) ? id : cityName.replace(/[_ \t]+/g, '_')))
					.parent().addClass('altasib_geobase_act');
				$('#altasib_geobase_btn').addClass('altasib_geobase_disabled');
				
				var clink = $('.altasib_geobase_link span.altasib_geobase_link_city');
				var search = $('input#altasib_geobase_search');
				window.setTimeout('altasib_geobase.sc_cls()', 275);
			}	
			clink.html(altasib_geobase.short_name);
			clink.attr('title', altasib_geobase.full_name);
			search.val('');
			altasib_geobase.sc_parse_city();
			
			if(typeof cityName == 'undefined' || cityName == '' || cityName == null)
				cityName = altasib_geobase.short_name;
			
			var arEventPars = [cityName, id, altasib_geobase.full_name, data];
			BX.onCustomEvent('onAfterSetCity', arEventPars);
			// BX.addCustomEvent("onAfterSetCity", function(par1,par2,par3){});
		}
	}
}

altasib_geobase.add_city = function (city, country_code){ // altasib_geobase_add_city
	if(typeof city == "undefined")
		city = altasib_geobase.short_name;
	if(typeof country_code == "undefined")
		country_code = altasib_geobase.COUNTRY_CODE;
	var cityID = (!isNaN(city) ? city : city.replace(/[_ \t]+/g, '_'));
	if (document.getElementById('altasib_geobase_list_' + cityID) == null
			&& $('ul.altasib_geobase_fst li:contains("'+city+'")').length == 0){
		if((elem = $('.altasib_geobase_cities .altasib_geobase_fst')).length == 0)
			elem = $('.altasib_geobase_cities .altasib_geobase_list_ie');
		elem.prepend('<li class="altasib_geobase_act">' +'<a id="altasib_geobase_list_'+ cityID +'" title="'+ altasib_geobase.full_name +'" href="#"' +' onclick="altasib_geobase.sc_onclk(\''+ city +'\', \''+country_code+'\'); return false;">'+ city +'</a></li>');
	} else if($('ul.altasib_geobase_fst li:contains("'+city+'")').length > 0){
		$('ul.altasib_geobase_fst li:contains("'+ city +'"):first').addClass('altasib_geobase_act');
	}
}

altasib_geobase.sc_click = function (code, country){ // altasib_geobase_sc_click
	el = $('div#altasib_geobase_inp_'+ code);
	altasib_geobase.short_name = el.attr('value');
	
	if(altasib_geobase.is_mobile){
		var light = 'altasib_geobase_mb_light';
	} else {
		var light = 'altasib_geobase_light';
	}
	altasib_geobase.full_name = BX.util.trim(el.html().replace(new RegExp('<strong class="'+ light +'">','g'),'').replace(new RegExp('</strong>','g'),''));
	// for IE
	altasib_geobase.full_name = altasib_geobase.full_name.replace(new RegExp('<STRONG class='+ light +'>','g'),'').replace(new RegExp('</STRONG>','g'),'');

	altasib_geobase.selected_id = el.attr('id').substr(20);
	altasib_geobase.COUNTRY_CODE = country;
	return false;
}

$(function(){
	$(document).click(function(event){
		if(altasib_geobase.is_mobile){
			if($(event.target).closest("#altasib_geobase_mb_info").length) return;
			$("#altasib_geobase_mb_info").animate({ height: 'hide' }, "fast");
			if(altasib_geobase.search_en)
				if($('input#altasib_geobase_mb_search').val() == '' && !$('#altasib_geobase_mb_btn').hasClass('altasib_geobase_mb_disabled'))
					$('#altasib_geobase_mb_btn').addClass('altasib_geobase_mb_disabled');
		} else {
			if($(event.target).closest("#altasib_geobase_info").length) return;
			$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
			if(altasib_geobase.search_en)
				if($('input#altasib_geobase_search').val() == '' && !$('#altasib_geobase_btn').hasClass('altasib_geobase_disabled'))
					$('#altasib_geobase_btn').addClass('altasib_geobase_disabled');
		}
		event.stopPropagation();
	});
});

$(function(){
	$(document).click(function(event){
		if(altasib_geobase.is_mobile){
			if ($('div#altasib_geobase_mb_popup').is(':visible') && altasib_geobase.sc_is_open){
				if($(event.target).closest("div#altasib_geobase_mb_popup").length) return;
				altasib_geobase.sc_mb_cls('visible');
				event.stopPropagation();

			}
		} else {
			if ($('div#altasib_geobase_popup').is(':visible') && altasib_geobase.sc_is_open){
				if($(event.target).closest("div#altasib_geobase_popup").length) return;
				altasib_geobase.sc_cls('visible');
				event.stopPropagation();
			}
		}
	});
});

altasib_geobase.sc_cls = function (vis){ // altasib_geobase_sc_cls
	if(vis === 'visible')
		if (!$('div#altasib_geobase_popup').is(':visible'))
			return false;
	$('div#altasib_geobase_win').animate({ top: '-'+altasib_geobase.height }, 750)
		.fadeOut(400);
	$('div#altasib_geobase_popup_back').hide();
	altasib_geobase.sc_is_open = false;
	if(typeof altasib_geobase.replace != "undefined"){
		altasib_geobase.sc_parse_city();
		altasib_geobase.replace();
	}
}

altasib_geobase.sc_open = function (){ // altasib_geobase_sc_open
	if(altasib_geobase.is_mobile){
		$('div#altasib_geobase_mb_win').fadeIn();
		altasib_geobase.sc_init_vars();
		$('div#altasib_geobase_mb_popup_back').show();
	} else {
		var win = $('div#altasib_geobase_win');
		win.fadeIn();
		altasib_geobase.sc_init_vars();
		$('div#altasib_geobase_popup_back').show();
		win.animate({ top: '9%' }, 750);
		// altasib_geobase.sc_clear_cities();
	}
	window.setTimeout('altasib_geobase.sc_is_open = true;', 1000);
}

altasib_geobase.sc_clear_cities = function (){ // altasib_geobase_sc_clear_cities
	var elems = $('ul.altasib_geobase_fst li:not(.altasib_geobase_auto):not(.altasib_geobase_act)');
	if(elems.length > 0){
		altasib_geobase.sc_country();
		if(typeof altasib_geobase.country_cur != 'undefined' && altasib_geobase.country_cur != null)
			if (typeof altasib_geobase.country_cur.country != "undefined")
				if(altasib_geobase.country_cur.country !== "RU")
					elems.empty();
	}
}

// altasib_geobase_sc_country
altasib_geobase.sc_country = function (){ // Country from cookies
	if((altasib_geobase.country_cur = altasib_geobase.sc_getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE_COUNTRY')) !== null){
		altasib_geobase.country_cur = decodeURIComponent(altasib_geobase.country_cur.replace(/\+/g, " "));
		altasib_geobase.country_cur = $.parseJSON(altasib_geobase.country_cur);
	}
}

// altasib_geobase_sc_add_city
altasib_geobase.sc_add_city = function (e){ // on click Select
	if(altasib_geobase.is_mobile){
		$('#altasib_geobase_mb_btn').removeClass('altasib_geobase_mb_disabled');
		$('#altasib_geobase_mb_search').focus();
		$("#altasib_geobase_mb_info").animate({ height: 'hide' }, "fast");
		$('input#altasib_geobase_mb_search').val(altasib_geobase.full_name);
	} else {
		$('#altasib_geobase_btn').removeClass('altasib_geobase_disabled');
		$('#altasib_geobase_search').focus();
		$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
		$('input#altasib_geobase_search').val(altasib_geobase.full_name);
	}
}

altasib_geobase.sc_load = function (){ // altasib_geobase_sc_load
	altasib_geobase.timer = 0;
	var reqId = ++altasib_geobase.request_numb;
	if(typeof altasib_geobase.lang == 'undefined'){
		altasib_geobase.sc_country();
		if(typeof altasib_geobase.country_cur != 'undefined' && altasib_geobase.country_cur != null
			&& typeof altasib_geobase.country_cur.country != "undefined"){
					altasib_geobase.lang = altasib_geobase.country_cur.country;
		}
		else
			altasib_geobase.sc_lang_set();
	}
	$.ajax({
		type: "POST",
		url: "/bitrix/tools/altasib.geobase/altasib_geobase_get.php",
		dataType: 'json',
		data: { 'city_name': altasib_geobase.letters,
				'sessid': BX.message('bitrix_sessid'),
				'lang': altasib_geobase.lang
			},
		timeout: 10000,
		success: function(data){
			if(reqId == altasib_geobase.request_numb){
				if(altasib_geobase.is_mobile) {
					var list = $('div#altasib_geobase_mb_info');
				} else {
					var list = $('div#altasib_geobase_info');
				}
				
				list.html('');
				if(data == '' || data == null)
					list.animate({ height: 'hide' }, "fast");
				else{
					var arOut = '';
					var oLength = 0;
					for(var prop = 'REGION' in data){
						if(data.hasOwnProperty(prop))
							oLength++;
					}
					
					for(var i=0; i < oLength; i++){
						var sOpt = data[i]['CITY']
						+ (typeof(data[i]['REGION']) == "undefined" || data[i]['REGION'] == ' ' || data[i]['REGION'] == null ? '' : ', ' + data[i]['REGION'])
						+ (typeof(data[i]['DISTRICT']) == "undefined" || data[i]['DISTRICT'] == ' ' ? '' : ', ' + data[i]['DISTRICT'])
						+ (typeof(data[i]['COUNTRY']) == "undefined" || data[i]['COUNTRY'] == ' ' ? '' : ', ' + data[i]['COUNTRY']);
						var code = typeof(data[i]['C_CODE']) == "undefined" ? data[i]['ID'] : data[i]['C_CODE'];
						var countryID = (typeof(data[i]['COUNTRY_CODE']) == 'undefined' ? data[i]['COUNTRY'] : data[i]['COUNTRY_CODE']);
						var sOptVal = data[i]['CITY'];
						arOut += '<div id="altasib_geobase_inp_'+ code +'"' +'value = "'+ sOptVal +'" onclick="altasib_geobase.sc_click(\''+ code +'\',\''
						+ (typeof countryID == 'undefined' ? '': countryID) +'\');">'+ sOpt +'</div>\n';
					}
					list.html(arOut);
					if(typeof $.fn.altasib_geobase_light != 'undefined')
						list.altasib_geobase_light(altasib_geobase.letters);
					list.animate({ height: 'show' }, "fast");
				}
			}
		}
	});
}

// altasib_geobase_sc_selKey
altasib_geobase.sc_selKey = function (e){ // called when a key is pressed in Select
	e=e||window.event;
	t=(window.event) ? window.event.srcElement : e.currentTarget; // object for which due
	if(!altasib_geobase.is_mobile) {
		if(e.keyCode == 13){ // Enter
			altasib_geobase.sc_onclk('Enter');
			$("#altasib_geobase_info").hide();
			return;
		} else if(e.keyCode == 38 && $('div#altasib_geobase_info > div :first').hasClass('altasib_geobase_focus')){ // up arrow
			$('.altasib_geobase_find input[name=altasib_geobase_search]').focus();
			$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
			$('div.altasib_geobase_focus').removeClass('altasib_geobase_focus');
		}
	} else {
		if(e.keyCode == 13){ // Enter
			altasib_geobase.sc_onclk('Enter');
			$("#altasib_geobase_mb_info").hide();
			return;
		} else if(e.keyCode == 38 && $('div#altasib_geobase_mb_info > div :first').hasClass('altasib_geobase_mb_focus')){ // up arrow
			$('.altasib_geobase_mb_find input[name=altasib_geobase_mb_search]').focus();
			$("#altasib_geobase_mb_info").animate({ height: 'hide' }, "fast");
			$('div.altasib_geobase_mb_focus').removeClass('altasib_geobase_mb_focus');
		}
	}
}

// altasib_geobase_sc_inpKeyDwn
altasib_geobase.sc_inpKeyDwn = function (e){ // input search
	e = e||window.event;
	t = (window.event) ? window.event.srcElement : e.currentTarget; // object for which due
	if(altasib_geobase.is_mobile) {
		var list = $('div#altasib_geobase_mb_info');
		var listDiv = $('div#altasib_geobase_mb_info div');
		var focus = 'altasib_geobase_mb_focus';		
		var dFocus = 'div.altasib_geobase_mb_focus';
		var dInfo = 'div#altasib_geobase_mb_info';
		var find = '.altasib_geobase_mb_find';
		var Search = 'altasib_geobase_mb_search';
	} else {
		var list = $('div#altasib_geobase_info');
		var listDiv = $('div#altasib_geobase_info div');
		var focus = 'altasib_geobase_focus';		
		var dFocus = 'div.altasib_geobase_focus';
		var dInfo = 'div#altasib_geobase_info';
		var find = '.altasib_geobase_find';
		var Search = 'altasib_geobase_search';
	}
	
	if(e.keyCode == 40){	// down arrow
		if(BX.util.trim(list.html()) != ''){
			list.animate({ height: 'show' }, "fast");
		}
		if(!listDiv.hasClass(focus)){
			$(dInfo + ' > div :first').addClass(focus);
			return;
		}else{
			if($(dInfo + ' > div :last').hasClass(focus)){
				return;
			} else {
				$(dInfo + ' >.' + focus).next('div').addClass(focus);
				$(dFocus + ' :first').removeClass(focus);
			}
		}
	} else if(e.keyCode == 13){ // Enter
		if(altasib_geobase.is_mobile) {
			if(!$('#altasib_geobase_mb_btn').hasClass('altasib_geobase_mb_disabled')){
				altasib_geobase.sc_onclk('Enter');
				list.hide();
				$('div.altasib_geobase_mb_focus :first').removeClass(focus);
			}
			else if(listDiv.hasClass(focus)){
				id = $('div#altasib_geobase_mb_info div.altasib_geobase_mb_focus').attr('id');
				$('#'+id).click();
			}
		} else {
			if(!$('#altasib_geobase_btn').hasClass('altasib_geobase_disabled')){
				altasib_geobase.sc_onclk('Enter');
				list.hide();
				$(dFocus + ' :first').removeClass(focus);
			} else if(listDiv.hasClass(focus)){
				id = $(dInfo + ' ' + dFocus).attr('id');
				$('#'+id).click();
			}
		}
		return;
	}
	else if(e.keyCode == 38){ // up arrow
		if(listDiv.hasClass(focus)){
			if($(dInfo + ' > div :first').hasClass(focus)){
				$(find + ' input[name='+ Search +']').focus();
				list.animate({ height: 'hide' }, "fast");
				$(dFocus).removeClass(focus);
			}
			$(dInfo + ' >.' + focus).prev('div').addClass(focus);
			$(dFocus + ' :last').removeClass(focus);
			return;
		}
	} else if(e.keyCode == 35){ // End
		if(listDiv.hasClass(focus)){
			$(dInfo + ' > div :last').addClass(focus);
			$(dFocus + ' :first').removeClass(focus);
			return;
		}
	} else if(e.keyCode == 36){ // Home
		if(listDiv.hasClass(focus)){
			$(dInfo + ' > div :first').addClass(focus);
			$(dFocus + ' :last').removeClass(focus);
			return;
		}
	}
}

// altasib_geobase_sc_inpKey
altasib_geobase.sc_inpKey = function (e){ // input search
	e = e||window.event;
	t = (window.event) ? window.event.srcElement : e.currentTarget; // object for which due
	if(altasib_geobase.is_mobile) {
		var list = $('div#altasib_geobase_mb_info');
	} else {
		var list = $('div#altasib_geobase_info');
	}
	var sFind = BX.util.trim(t.value);
	
	if(altasib_geobase.letters == sFind)
		return; // if nothing has changed, do not do heavy load server
	altasib_geobase.letters = sFind;
	if(altasib_geobase.timer){
		clearTimeout(altasib_geobase.timer);
		altasib_geobase.timer = 0;
	}
	if(altasib_geobase.letters.length < 2){
		list.animate({ height: 'hide' }, "fast");
		return;
	}
	altasib_geobase.timer = window.setTimeout('altasib_geobase.sc_load()', 190); // Load through 190ms after the last keystroke
}

// highlighting
$.fn.altasib_geobase_light = function(pat) {
	function altasib_geobase_innerLight(node, pat) {
		var skip = 0;
		if (node.nodeType == 3) {
			var pos = node.data.toUpperCase().indexOf(pat);
			if (pos >= 0) {
				var strongnode = document.createElement('strong');
				if(altasib_geobase.is_mobile)
					strongnode.className = 'altasib_geobase_mb_light';
				else
					strongnode.className = 'altasib_geobase_light';
				var middlebit = node.splitText(pos);
				var endbit = middlebit.splitText(pat.length);
				var middleclone = middlebit.cloneNode(true);
				strongnode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(strongnode, middlebit);
				skip = 1;
			}
		}
		else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
			for (var i = 0; i < node.childNodes.length; ++i) {
				i += altasib_geobase_innerLight(node.childNodes[i], pat);
			}
		}
		return skip;
	}
	return this.each(function() {
		altasib_geobase_innerLight(this, pat.toUpperCase());
	});
};

$.fn.altasib_geobase_removeLight = function() {
	return this.find(altasib_geobase.is_mobile ? "strong.altasib_geobase_mb_light" : "strong.altasib_geobase_light").each(function() {
		this.parentNode.firstChild.nodeName;
		with (this.parentNode) {
			replaceChild(this.firstChild, this);
			normalize();
		}
	}).end();
};

altasib_geobase.sc_lang_set = function (){ // altasib_geobase_sc_lang_set
	altasib_geobase.lang = 'en';
	if (typeof altasib_geobase.auto_code !== 'undefined' && altasib_geobase.auto_code !== null)
		if(typeof altasib_geobase.auto_code.COUNTRY_CODE !== 'undefined'){
			if(altasib_geobase.auto_code.COUNTRY_CODE == 'RU')
				altasib_geobase.lang = 'ru';
			altasib_geobase.COUNTRY_CODE = altasib_geobase.auto_code.COUNTRY_CODE;
		}
		
	if (typeof altasib_geobase.manual_code !== 'undefined' && altasib_geobase.manual_code !== null)
		if(typeof altasib_geobase.manual_code.COUNTRY_CODE !== 'undefined'){
			if(altasib_geobase.manual_code.COUNTRY_CODE == 'RU')
				altasib_geobase.lang = 'ru';
			altasib_geobase.COUNTRY_CODE = altasib_geobase.manual_code.COUNTRY_CODE;
		}
	
	if(typeof altasib_geobase.manual_code !== 'undefined' && typeof altasib_geobase.COUNTRY_CODE == 'undefined'){
		if(altasib_geobase.manual_code != null && typeof altasib_geobase.manual_code['REGION'] !== 'undefined')
			if(typeof altasib_geobase.manual_code['REGION']['CODE'] !== 'undefined')
				altasib_geobase.COUNTRY_CODE = 'RU';
	}
}

altasib_geobase.sc_parse_city = function (){ // altasib_geobase_sc_parse_city

	if((altasib_geobase.manual_code = altasib_geobase.sc_getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE_CODE')) !== null){
		altasib_geobase.manual_code = decodeURIComponent(altasib_geobase.manual_code.replace(/\+/g, " "));
		altasib_geobase.manual_code = $.parseJSON(altasib_geobase.manual_code);
	}
	if((altasib_geobase.auto_code = altasib_geobase.sc_getCookie(BX.message('COOKIE_PREFIX')+'_'+'ALTASIB_GEOBASE')) !== null){
		altasib_geobase.auto_code = decodeURIComponent(altasib_geobase.auto_code.replace(/\+/g, " "));
		altasib_geobase.auto_code = $.parseJSON(altasib_geobase.auto_code);
	}
	
	altasib_geobase.sc_lang_set();
	
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

altasib_geobase.sc_getCookie = function (name) { // altasib_geobase_sc_getCookie
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

altasib_geobase.sc_mb_cls = function (vis){ // altasib_geobase_sc_mb_cls
	if(vis === 'visible')
		if (!$('div#altasib_geobase_mb_popup').is(':visible'))
			return false;
	$('div#altasib_geobase_mb_win').fadeOut(400);
	$('div#altasib_geobase_mb_popup_back').hide();
	altasib_geobase.sc_is_open = false;
	
	if(typeof altasib_geobase.replace != "undefined"){ // script of module
		altasib_geobase.parse_city();
		altasib_geobase.replace();
	} else if(typeof altasib_geobase.sc_parse_city != "undefined"){
		altasib_geobase.sc_parse_city();
	}
}

altasib_geobase.all_cities = function () { // altasib_geobase_all_cities
	$('#all_cities_button_mobile').hide();
	$('#altasib_geobase_mb_cities').css({
		'overflow-y': 'scroll',
		'height':	'259px'
	});
}

altasib_geobase.city_field_focus = function () { // altasib_geobase_city_field_focus
	$('#altasib_geobase_mb_header').hide();	
	$('#altasib_geobase_mb_find').css({
		'padding-top': '23px'
	});
}

altasib_geobase.city_field_blur = function () { // altasib_geobase_city_field_blur
	if($('#altasib_geobase_mb_search').val().length == 0)
	{
		$('#altasib_geobase_mb_header').show();	
		$('#altasib_geobase_mb_find').css({'padding-top': '0px'});
		$('#altasib_geobase_mb_search').val(altasib_geobase.perem);
		setTimeout("$('#altasib_geobase_mb_search').focus()", 1);
		$('div#altasib_geobase_mb_info').animate({height: 'show' }, "fast");
	}
}

altasib_geobase.clear_field = function (){ // altasib_geobase_clear_field
	altasib_geobase.perem = $('#altasib_geobase_mb_search').val();
	$('#altasib_geobase_mb_search').val('');
}