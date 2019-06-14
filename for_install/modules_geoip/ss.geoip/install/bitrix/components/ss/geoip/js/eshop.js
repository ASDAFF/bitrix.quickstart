$(document).ready(function() {
	var ysLocCookie = YS.GeoIP.Cookie,
		ysLocPopUp  = YS.GeoIP.PopUpWindow,
		ysLocAutoC	= YS.GeoIP.AutoComplete,
		town = ysLocCookie.getCookieTown('YS_GEO_IP_CITY'),
		region = ysLocCookie.getRegionCookie('YS_GEO_IP_CITY'),
		regionId = ysLocCookie.getRegionId('YS_GEO_IP_CITY'),
		country = ysLocCookie.getCountryCookie('YS_GEO_IP_CITY'),
		countryId = ysLocCookie.getCookieCountryId('YS_GEO_IP_CITY'),
		siteId = $('#ys-SITE_ID').val(),
		townId_ = ysLocCookie.getLocationID('YS_GEO_IP_LOC_ID'),
		townId,
		dataLoc,
		tmpVal,
		orderProps = {	
			// default values
			'PERSON_TYPE_1' : {'locationID' : 6,	'cityID' : 5},
			'PERSON_TYPE_2' : {'locationID' : 18,	'cityID' : 17}
		};
			
		if(YS.GeoIP.OrderProps != undefined)
		{
			// YS.GeoIP.OrderProps sets in bitrix\components\yenisite\geoip.city\templates\.default\component_epilog.php
			orderProps = YS.GeoIP.OrderProps;
		}

	$(document).keydown(function (eventObject) {
		if (eventObject.which == 27) {
			$('#ys-geoip-mask, .popup').hide();
		}
	});

	// For IE
	if(typeof submitForm !== 'function') {
		submitForm = function(val) {
			if(val != 'Y')
				BX('confirmorder').value = 'N';

			var orderForm = BX('ORDER_FORM');

			BX.ajax.submitComponentForm(orderForm, 'order_form_content', true);
			BX.submit(orderForm);

			return true;
		}
	}
	// ===
	function updateProfileLocation(locationPropID, cityPropID) {
		$('#LOCATION_ORDER_PROP_' + locationPropID).empty();
		
		dataLoc = { 'COUNTRY_INPUT_NAME' : 'COUNTRY',
					'REGION_INPUT_NAME' : 'REGION',
					'CITY_INPUT_NAME' : 'ORDER_PROP_' + locationPropID,
					'CITY_OUT_LOCATION' : 'Y',
					'ALLOW_EMPTY_CITY' : 'Y',
					'ONCITYCHANGE' : '',
					'COUNTRY' : countryId,
					'REGION' : regionId,
					'SITE_ID': siteId };
		
		if ( $('[id^=ID_PAY_SYSTEM_ID]').length != 0 || $('[id^=ID_DELIVERY_]').length != 0 ) {
			dataLoc['ONCITYCHANGE'] = 'submitForm()';
		}
		
		$.ajax({
			type: 'POST',
			url: 'http://' + window.location.host +
				'/bitrix/components/bitrix/sale.ajax.locations/templates/.default/ajax.php',
			data: dataLoc,
			async: false,
			success: function(out) {
				var tmp = out.split('id="ORDER_PROP_' + locationPropID +'"');
				var reg = new RegExp('option value="([0-9]+)">' + town);
				var res = reg.exec(tmp[1]);
					//tmp = out.split(townId + '"');

				$('#LOCATION_ORDER_PROP_' + locationPropID).prepend(out);
				$('input[name="ORDER_PROP_' + cityPropID +'"]').val(town);
				
				if (res !== null) {
					var townId = res[1];
					$('#ORDER_PROP_' + locationPropID).val(townId).change();
				} else {
					var cityFillInterval = setInterval(function(){
						if ($('#ORDER_PROP_' + cityPropID).val(town).length == 0) return;
						clearInterval(cityFillInterval);
					}, 400);
				}
			}
		});
	}
	
	//bitrix:sale.ajax.locations, template "popup"
	function updateLocationPopup(locationPropID, cityPropID) {
		if (isNaN(parseInt(townId_))) {
			$('#ORDER_PROP_' + locationPropID +'_val').attr('value', '');
			$('#ORDER_PROP_' + locationPropID).val('');
		} else {

			if (region != '') {
				$('#ORDER_PROP_' + locationPropID +'_val').attr('value', town + ', ' + region + ', ' + country);
			} else {
				$('#ORDER_PROP_' + locationPropID +'_val').attr('value', town + ', ' + country);
			}
			$('#ORDER_PROP_' + locationPropID).val(townId_);
		}
		if ($('#ORDER_PROP_' + cityPropID).val(town).length == 0) {
			var cityFillInterval = setInterval(function(){
				if ($('#ORDER_PROP_' + cityPropID).val(town).length == 0) return;
				clearInterval(cityFillInterval);
			}, 400);
		}
		submitForm();
	}
	
	if (town === null) {
		ysLocPopUp.showPopUpGeoIP();
	} else {
		// ====================== Template visual / .default ======================
		// Default location template
		if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID']).length != 0 ) {
			
			updateProfileLocation(orderProps['PERSON_TYPE_1']['locationID'], orderProps['PERSON_TYPE_1']['cityID']);
			
			$(document).on('click', '#PERSON_TYPE_1', function() {
				var intervalTimer = setInterval(function() {
					if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID']).length != 0 ) {
						clearInterval(intervalTimer);
						updateProfileLocation(orderProps['PERSON_TYPE_1']['locationID'], orderProps['PERSON_TYPE_1']['cityID']);
					}
				}, 1000);
			});
			
			$(document).on('click', '#PERSON_TYPE_2', function() {
				var intervalTimer = setInterval(function() {
					if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_2']['locationID']).length != 0 ) {
						clearInterval(intervalTimer);
						updateProfileLocation(orderProps['PERSON_TYPE_2']['locationID'], orderProps['PERSON_TYPE_2']['cityID']);
					}
				}, 1000);
			});
			
			$('input[name="ORDER_PROP_' + orderProps['PERSON_TYPE_1']['cityID'] +'"]').val(town);
			
		} else if ( $('#ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID'] +'_val').length != 0 ) { // if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID']).length != 0 )
		
			// Template location - search string (popup)
			updateLocationPopup(orderProps['PERSON_TYPE_1']['locationID'], orderProps['PERSON_TYPE_1']['cityID']);
			
			$(document).on('click', '#PERSON_TYPE_2', function() {
				var intervalTimer = setInterval(function() {
					if ( $('#ORDER_PROP_' + orderProps['PERSON_TYPE_2']['locationID'] +'_val').length != 0 ) {
						clearInterval(intervalTimer);
						updateLocationPopup(orderProps['PERSON_TYPE_2']['locationID'], orderProps['PERSON_TYPE_2']['cityID']);
					}
				}, 1000);
			});
			
			$(document).on('click', '#PERSON_TYPE_1', function() {
				var intervalTimer = setInterval(function() {
					if ( $('#ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID'] +'_val').length != 0 ) {
						clearInterval(intervalTimer);
						updateLocationPopup(orderProps['PERSON_TYPE_1']['locationID'], orderProps['PERSON_TYPE_1']['cityID']);
					}
				}, 1000);
			});
		} else {
			// client profile add (simple mode)
			var select1 = $('select[name="ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID'] +'"]');
			var select2 = $('select[name="ORDER_PROP_' + orderProps['PERSON_TYPE_2']['locationID'] +'"]');

			var locID = 0;
			var cityID = 0;
			if (select1.length > 0) {
				locID = orderProps['PERSON_TYPE_1']['locationID'];
				cityID = orderProps['PERSON_TYPE_1']['cityID'];
			}
			else if (select2.length > 0) {
				locID = orderProps['PERSON_TYPE_2']['locationID'];
				cityID = orderProps['PERSON_TYPE_2']['cityID'];
			}

			if (locID > 0) {
				if ($('form input[name="action"]').val() == 'create'
				&&  $('form input[name="ID"]').length == 0) {

					var tmpVal;
					$('select[name="ORDER_PROP_' + locID +'"] option').each(function() {
						if ( $(this).text() == country + ' - ' + town  ) {
							tmpVal = $(this).val();
						}
		
						if ($(this).attr('selected') == 'selected') {
							$(this).removeAttr('selected');
						}
					});

					$('select[name="ORDER_PROP_' + locID +'"]').val(tmpVal);
					$('input[name="ORDER_PROP_' + cityID +'"]').val(town);
				} else {
					var optionContent = $('select[name="ORDER_PROP_' + locID +'"] option')
						.filter(':selected').text().split(' - ');

					if (Array.isArray(optionContent) && optionContent.length > 1) {
						if (optionContent[1].length > 0) {
							var cityField = $('input[name="ORDER_PROP_' + cityID +'"]');

							if (cityField.val() != optionContent[1]) {
								cityField.val(optionContent[1]);
							}
						}
					}
				}
			}
		}
		// ============================================================
	
		$('a.ys-loc-city').text(town);
		$('a.ys-loc-city').css('display', 'inline');
	} // if (town === null) else
	

	var confirmCity = function() {
		ysLocCookie.setCookieFromButtonClick();
		ysLocPopUp.hidePopUpGeoIP();
		
		$('a.ys-loc-city').text(ysLocCookie.getCookieTown('YS_GEO_IP_CITY'));
		$('a.ys-loc-city').css('display', 'inline');
		
		window.location.reload();
	}
	var textchangeInterval;

	function geoIpComponentInitHandlers ()
	{
		if (town !== null) {
			$('a.ys-loc-city').text(town);
			$('a.ys-loc-city').css('display', 'inline');
		}
		
		// ------------ click handlers -------------
		$('.ys-loc-cities a').off().on('click', function() {
			var locId = $(this).find('span').attr('data-location');
			if (typeof locId != "undefined" && locId != '0') {
				ysLocCookie.setLocationID(locId);
				ysLocCookie.setCookieFromLocationID(locId);
			} else {
				ysLocCookie.setCookieFromTownClick($(this).text());
			}
			
			ysLocPopUp.hidePopUpGeoIP();
			
			var town = ysLocCookie.getCookieTown('YS_GEO_IP_CITY');

			$('a.ys-loc-city').text(town);
			$('a.ys-loc-city').css('display', 'inline');
			
			window.location.reload();
		});
		
		$('a.ys-loc-city').off().on('click', function() {
			ysLocPopUp.showPopUpGeoIP();
		});
		
		$('.ys-my-city .button').off().on('click', confirmCity);
		
		$('#ys-geoip-mask, a.close').off().on('click', function() {
			// YS.GeoIP.AutoConfirm sets in bitrix\components\yenisite\geoip.city\templates\.default\component_epilog.php
			if (YS.GeoIP.AutoConfirm && town === null) {
				confirmCity();
			} else {
				ysLocPopUp.hidePopUpGeoIP();
			}
		});
		// ------------- end click handlers ---------------

		// city text input handler
		$('.ys-popup .txt').off().on('textchange', function() {
			var txtField = $(this);
			if (txtField.val().length > 1)
			{
				if (textchangeInterval) {
					clearInterval(textchangeInterval);
				}
				textchangeInterval = setInterval(function(){
					ysLocAutoC.buildList( txtField.val(), function(){
						window.location.reload();
					});
					clearInterval(textchangeInterval);
				}, 500);
				
			} else if(txtField.val().length <= 1) {
				clearInterval(textchangeInterval);
				$('.ys-loc-autocomplete').css('display', 'none').empty();
			}
		}).on('keypress', function(e) {
			if (e.which != 13) return;
			$('.ys-loc-autocomplete div').eq(0).click();
		});
	}
	
	//When user picks new profile
	$(document).on('change', '#ID_PROFILE_ID', function(){
		if (parseInt($(this).val()) != 0) return;
		
		var intervalTimer = setInterval(function() {
			if ($('#wait_order_form_content').length) return;
			clearInterval(intervalTimer);
			var locationPropID;
			var cityPropID;
				
			if ($('#PERSON_TYPE_2').is(':checked') || parseInt($('input[name="PERSON_TYPE"]').val()) == 2) {
				locationPropID = orderProps['PERSON_TYPE_2']['locationID'];
				cityPropID     = orderProps['PERSON_TYPE_2']['cityID'];
			} else {
				locationPropID = orderProps['PERSON_TYPE_1']['locationID'];
				cityPropID     = orderProps['PERSON_TYPE_1']['cityID'];
			}
			if ( $('#LOCATION_ORDER_PROP_' + locationPropID).length != 0 ) {
				updateProfileLocation(locationPropID, cityPropID);
			} else if ( $('#ORDER_PROP_' + locationPropID +'_val').length != 0 ) {
				setTimeout( function(){updateLocationPopup(locationPropID, cityPropID)}, 1000);
			}
		}, 1000);
	});

	geoIpComponentInitHandlers();
	BX.addCustomEvent("onFrameDataReceived", geoIpComponentInitHandlers);
});