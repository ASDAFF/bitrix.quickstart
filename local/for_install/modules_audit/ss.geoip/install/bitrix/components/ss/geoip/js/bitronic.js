$(document).ready(function () {
	var ysLocPopUp  = YS.GeoIP.PopUpWindow,
		ysLocAutoC	= YS.GeoIP.AutoComplete,
		ysLocCookie = YS.GeoIP.Cookie;
	function geoIpComponentInitHandlers ()
	{
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
			checkForUpdate();
			
			var town = ysLocCookie.getCookieTown('YS_GEO_IP_CITY');

			$('a.ys-loc-city').text(town);
			$('a.ys-loc-city').css('display', 'inline');

			// Set new Active Item for yenisite.geoipstore
			if ($('#ys-geoipstore').length > 0) {
				$.ajax({
					url: '/bitrix/js/yenisite.geoipstore/ajax.php?action=update',
					dataType: 'json',
					success: function(data) {
						if (data.UPDATE !== 'N') {
							$('.ys-geoip-store-city').text(data.CITY_NAME);

							var span = $('#ys-geoipstore .ys-geoipstore-itemlink + .sym').detach();
							$('.ys-geoipstore-itemlink').each(function() {
								if (parseInt($(this).data('ys-item-id')) === parseInt(data.ID)) {
									$(this).after(span);
									$('.ys-geoipstore-cont-active').removeClass('ys-geoipstore-cont-active');
									$(this).parent().parent().addClass('ys-geoipstore-cont-active');
								}
							});
						}
					}
				});
			} else if(typeof ysGeoStoreList != "undefined") {
				if(YS.GeoIP.Cookie.getCookieTown('YS_GEO_IP_CITY') in ysGeoStoreList){
					if(ysGeoStoreList[YS.GeoIP.Cookie.getCookieTown('YS_GEO_IP_CITY')] != ysGeoStoreActiveId){
						YS.GeoIPStore.Core.setActiveItem(ysGeoStoreList[YS.GeoIP.Cookie.getCookieTown('YS_GEO_IP_CITY')]) ;
					}
				}
				else if (ysGeoStoreActiveId != ysGeoStoreDefault) {
					YS.GeoIPStore.Core.setActiveItem(ysGeoStoreDefault) ;
				}
			}
		});

		$('a.ys-loc-city').off().on('click', function() {
			ysLocPopUp.showPopUpGeoIP();
		});
		
		$('.ys-my-city .button').off().on('click', function() {
			ysLocCookie.setCookieFromButtonClick();
			ysLocPopUp.hidePopUpGeoIP();
			checkForUpdate();
			
			$('a.ys-loc-city').text(ysLocCookie.getCookieTown('YS_GEO_IP_CITY'));
			$('a.ys-loc-city').css('display', 'inline');
		});

		// YS.GeoIP.AutoConfirm sets in bitrix\components\yenisite\geoip.city\templates\.default\component_epilog.php
		if (YS.GeoIP.AutoConfirm) {
			var confirm = function(){
				$('.ys-my-city .button').trigger('click');
			}
			$('#ys-geoip-mask, .ys-del-to a.close').off('click').on('click', confirm);
			$(document).keydown(function (eventObject) {
				if (eventObject.which == 27) confirm();
			});
		} else {
			$('#ys-geoip-mask, .ys-del-to a.close').off('click').on('click', function(){
				ysLocPopUp.hidePopUpGeoIP();
			});
			$(document).keydown(function (eventObject) {
				if (eventObject.which == 27) {
					$('#ys-geoip-mask').hide();
				}
			});
		}
		// ------------- end click handlers ---------------
		
		// city text input handler
		var textchangeInterval;
		$('.popup .ys-city-query').off().on('textchange', function() {
			var txtField = $(this);
			if (txtField.val().length > 1)
			{
				if (textchangeInterval) {
					clearInterval(textchangeInterval);
				}
				textchangeInterval = setInterval(function(){
					ysLocAutoC.buildList( txtField.val(), checkForUpdate );
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

		//show popup if city is undefined
		if ($('#ys-locator').length) {
			if (town === null) {
				ysLocPopUp.showPopUpGeoIP();
			} else {
				$('a.ys-loc-city').text(town);
				$('a.ys-loc-city').css('display', 'inline');
			}
		}
		
		// initial geostore
		if(typeof ysGeoStoreList != "undefined") {
			if(YS.GeoIP.Cookie.getCookieTown('YS_GEO_IP_CITY') in ysGeoStoreList){
				if(ysGeoStoreList[YS.GeoIP.Cookie.getCookieTown('YS_GEO_IP_CITY')] != ysGeoStoreActiveId){
					YS.GeoIPStore.Core.setActiveItem(ysGeoStoreList[YS.GeoIP.Cookie.getCookieTown('YS_GEO_IP_CITY')]) ;
				}
			}
		}
	}

	function checkForUpdate() {
		var step = $('input[name="CurrentStep"]');
		if (step.length > 1) {
			if (parseInt(step.val()) != 3) return;
		}
		
		town = ysLocCookie.getCookieTown('YS_GEO_IP_CITY');
		region = ysLocCookie.getRegionCookie('YS_GEO_IP_CITY');
		regionId = ysLocCookie.getRegionId('YS_GEO_IP_CITY');
		country = ysLocCookie.getCountryCookie('YS_GEO_IP_CITY');
		countryId = ysLocCookie.getCookieCountryId('YS_GEO_IP_CITY');
		townId_ = ysLocCookie.getLocationID('YS_GEO_IP_LOC_ID');
		
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
			updateLocationPopup(locationPropID, cityPropID);
		} else {
			checkUpdateAjaxLocationsDefault();
		}
	}
	function updateProfileLocation(locationPropID, cityPropID)
	{
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

		if (!!orderProps['ONE_STEP'] || $('[id^=ID_PAY_SYSTEM_ID]').length != 0 || $('[id^=ID_DELIVERY_]').length != 0 ) {
			dataLoc['ONCITYCHANGE'] = 'submitForm()';
		}
		
		$.ajax({
			type: 'POST',
			url: 'http://' + window.location.host +
				'/bitrix/components/bitrix/sale.ajax.locations/templates/.default/ajax.php',
			data: dataLoc,
			async: false,
			success: function(out) {
				var tmp = out.split('name="ORDER_PROP_' + locationPropID +'"');
				var reg = new RegExp('option value="([0-9]+)">' + town);
				var res = reg.exec(tmp[1]);
					//tmp = out.split(townId + '"');

				$('#LOCATION_ORDER_PROP_' + locationPropID).prepend(out);
				$('input[name="ORDER_PROP_' + cityPropID +'"]').val(town);
				
				if (res !== null) {
					var townId = res[1];
					$('select[name="ORDER_PROP_' + locationPropID + '"]').val(townId).change(); 
				} else {
					var cityFillInterval = setInterval(function(){
						if ($('#ORDER_PROP_' + cityPropID).val(town).length == 0) return;
						clearInterval(cityFillInterval);
					}, 400);
				}
			}
		});
	}
	
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

	function checkUpdateAjaxLocationsDefault()
	{
		var select1 = $('select[name="ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID'] +'"]');
		var select2 = $('select[name="ORDER_PROP_' + orderProps['PERSON_TYPE_2']['locationID'] +'"]');

		if ((select1.length || select2.length)
		&& $('form input[name="action"]').val() == 'create'
		&& $('form input[name="ID"]').length == 0)
		{
			var locID = 0;
			var cityID = 0;
			if (select1.length > 0) {
				locID = orderProps['PERSON_TYPE_1']['locationID'];
				cityID = orderProps['PERSON_TYPE_1']['cityID'];
				select1.selectBox('destroy');
			}
			else if (select2.length > 0) {
				locID = orderProps['PERSON_TYPE_2']['locationID'];
				cityID = orderProps['PERSON_TYPE_2']['cityID'];
				select2.selectBox('destroy');
			}

			if (locID > 0) {
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
			}
		}
	}
	
	//When user picks new profile
	$(document).on('change', '#ID_PROFILE_ID', function(){
		if (parseInt($(this).val()) != 0) return;
		
		var intervalTimer = setInterval(function() {
			if ($('#wait_order_form_content').length) return;
			clearInterval(intervalTimer);
			checkForUpdate();
		}, 1000);
	});
	
	var town = ysLocCookie.getCookieTown('YS_GEO_IP_CITY'),
		region = ysLocCookie.getRegionCookie('YS_GEO_IP_CITY'),
		regionId = ysLocCookie.getRegionId('YS_GEO_IP_CITY'),
		country = ysLocCookie.getCountryCookie('YS_GEO_IP_CITY'),
		countryId = ysLocCookie.getCookieCountryId('YS_GEO_IP_CITY'),
		siteId = $('#ys-SITE_ID').val(),
		townId_ = ysLocCookie.getLocationID('YS_GEO_IP_LOC_ID'),
		dataLoc,
		tmpVal,
		orderProps = {	
			// default values
			'PERSON_TYPE_1' : {'locationID' : 5,	'cityID' : 6},
			'PERSON_TYPE_2' : {'locationID' : 18,	'cityID' : 17}
		};
		
	if(YS.GeoIP.OrderProps != undefined)
	{
		// YS.GeoIP.OrderProps sets in bitrix\components\yenisite\geoip.city\templates\.default\component_epilog.php
		orderProps = YS.GeoIP.OrderProps;
	}
	
	var step = $('input[name="CurrentStep"]');
	// auto insert location in order form or profile add form
	if ( (step.length == 0 || parseInt(step.val()) == 3) && town !== null ) {
		if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID']).length != 0 ) {
			
			updateProfileLocation(orderProps['PERSON_TYPE_1']['locationID'], orderProps['PERSON_TYPE_1']['cityID']);
			
			$(document).on('click', '#PERSON_TYPE_2', function() {
				var intervalTimer = setInterval(function() {
					if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_2']['locationID']).length != 0 ) {
						clearInterval(intervalTimer);
						updateProfileLocation(orderProps['PERSON_TYPE_2']['locationID'], orderProps['PERSON_TYPE_2']['cityID']);
					}
				}, 1000);
			});
			
			$(document).on('click', '#PERSON_TYPE_1', function() {
				var intervalTimer = setInterval(function() {
					if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID']).length != 0 ) {
						clearInterval(intervalTimer);
						updateProfileLocation(orderProps['PERSON_TYPE_1']['locationID'], orderProps['PERSON_TYPE_1']['cityID']);
					}
				}, 1000);
			});
			
		} else if ($('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_2']['locationID']).length != 0) {
		
			updateProfileLocation(orderProps['PERSON_TYPE_2']['locationID'], orderProps['PERSON_TYPE_2']['cityID']);
			
		} else if( $('#ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID'] + '_val').length != 0 ) { // if ( $('#LOCATION_ORDER_PROP_' + orderProps['PERSON_TYPE_1']['locationID']).length != 0 )
			
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
			checkUpdateAjaxLocationsDefault();
		}
	}
	
	geoIpComponentInitHandlers();
	BX.addCustomEvent("onFrameDataReceived", geoIpComponentInitHandlers);
});