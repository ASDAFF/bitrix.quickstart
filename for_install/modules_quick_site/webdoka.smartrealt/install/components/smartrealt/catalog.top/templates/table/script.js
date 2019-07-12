jQuery.cookie=function(d,c,a){if(typeof c!="undefined"){a=a||{};if(c===null){c="";a.expires=-1}var b="";if(a.expires&&(typeof a.expires=="number"||a.expires.toUTCString)){if(typeof a.expires=="number"){b=new Date;b.setTime(b.getTime()+a.expires*24*60*60*1E3)}else b=a.expires;b="; expires="+b.toUTCString()}var e=a.path?"; path="+a.path:"",f=a.domain?"; domain="+a.domain:"";a=a.secure?"; secure":"";document.cookie=[d,"=",encodeURIComponent(c),b,e,f,a].join("")}else{c=null;if(document.cookie&&document.cookie!= ""){a=document.cookie.split(";");for(b=0;b<a.length;b++){e=jQuery.trim(a[b]);if(e.substring(0,d.length+1)==d+"="){c=decodeURIComponent(e.substring(d.length+1));break}}}return c}};

/**
* Класс для работы со списком объектов
*/
function WD_ObjectList()
{
	var oObjectListContainer = $('#objectListContainer');
	var oMapContainer = $('#mapContainer');
	
	var sPrintText = '';
        var sMapHideText = '';
        var sMapShowText = '';
	var sUndoPrintText = '';
	
	// Находится ли страница в режиме просмотра печати
	var bIsPrintView = false;
	
	var bIsMapLoaded = false;
	var arGoogleMarkers = null;
	
	
	/**
	* Скрытие и показ карты объектов
	*/
	function initShowOnMapHandler()
	{
		$('div.onmap a').bind('click',
			function()
			{
				if (oMapContainer.is(':visible'))
				{
					oMapContainer.hide();
                                        $('#showMapLink').get(0).innerHTML = sMapShowText;
                   $.cookie("hideMap", 1); 
				}
				else
				{
					$('#showMapLink').get(0).innerHTML = sMapHideText;
                                        oMapContainer.show();
                    $.cookie("hideMap", 0); 
				}
				
				return false;
			}
		);
	}
	
	/**
	* Обработчик изменения сортировки в выпадающем списке
	*/
	function initChangeSortInSelectHandler()
	{
		$('select[name="objectsSortInfoSelect"]', oObjectListContainer).bind('change',
			function()
			{
				if (this.value != '')
				{
					window.location = this.value;
				}
			}
		);
	}
	
	function initPrintLink()
	{
		$('#printLink').bind('click',
			function()
			{
				if (document.getElementsByTagName)
                    link = document.getElementsByTagName('link');
                else if (document.all)
                    link = document.all.tags('link');
                else
                    return;
                
                if (bIsPrintView)
				{
                    // Переход в обычный режим
					bIsPrintView = false;
					this.innerHTML = sPrintText;
					$('#searchContainerForPrint').addClass('hiddenBlock');
                    oWD_Template.setNormalView();
                     
				}
				else
				{
					// Переход в режим печати 
					bIsPrintView = true;
					this.innerHTML = sUndoPrintText;
					$('#searchContainerForPrint').removeClass('hiddenBlock');
                    oWD_Template.setPrintView();
				}
				
				return false;
			}
		);
	}
        
    function initMap()
	{
            if (!bIsMapLoaded)
                {
                    bIsMapLoaded = true;
						
                        if (GBrowserIsCompatible()) {
                            var size = new GSize(655, 300);
                            map = new GMap2(document.getElementById("google_map"), {'size': size});
                            map.addControl(new GSmallMapControl());
                            map.addControl(new GMapTypeControl());
                                
                            //map.setUIToDefault();
							var oIcon = new GIcon(G_DEFAULT_ICON);
							oIcon.image = "/bitrix/templates/kznrielt/images/home.png";
							oIcon.shadow = '';
							oIcon.iconSize = new GSize(27, 26);
                                
							function createMarker(point, name, html) {
								var marker = new GMarker(point, { icon:oIcon });
								marker.bindInfoWindowHtml(html);
								return marker;
							}

                            for (iIndex in arGoogleMarkers)
                            {
                                var oMarker = arGoogleMarkers[iIndex]; 
                                var point = new GLatLng(oMarker['fLatitude'], oMarker['fLongitude']);  
								var marker = createMarker(point, oMarker['sName'], oMarker['sHTML']);

                                map.addOverlay(marker); 
                                if (bound)
                                    bound.extend(point);
                                else
                                    var bound = new GLatLngBounds(point, point);
                            }

                            var center = bound.getCenter();
                            var zoom = map.getBoundsZoomLevel(bound);
                            if (zoom > 15)
                                zoom = 15;

                            map.setCenter(center, zoom);
                        }
                }
        }
	
	function ListObject()
	{
		this.InitHandlers = function()
		{
			initShowOnMapHandler();
			initChangeSortInSelectHandler();
			initPrintLink();
            initMap();
		}
		
		this.SetPrintText = function(sText)
		{
			sPrintText = sText;
		}
		
		this.SetMapHideText = function(sText)
		{
			sMapHideText = sText;
		}
		
		this.SetMapShowText = function(sText)
		{
			sMapShowText = sText;
		}

		
		this.SetUndoPrintText = function(sText)
		{
			sUndoPrintText = sText;
		}
		
		this.SetMarkers = function(arList)
		{
			arGoogleMarkers = arList;
		}
                
	}
	
	return new ListObject();
}