function SmartRealtMap()
{
    var sMapId;
    var sMapType;
    var isYandex;
    var iWidth;
    var iHeight;
    var arMarkers;
    
    function loadMap()
    {
        if (isYandex)
        {
            var myOptions = {
                center: [55.76, 37.64],
                zoom: 10
            };
            
            var bounds = false;
            
            $("#"+sMapId).empty();
            
            var map = new ymaps.Map(sMapId, myOptions);
            map.controls.add('typeSelector');
            map.controls.add('smallZoomControl', { left: 7, top: 7 });
            // РЎРѕР·РґР°РµРј РєРѕР»Р»РµРєС†РёСЋ, РІ РєРѕС‚РѕСЂСѓСЋ Р±СѓРґРµРј РґРѕР±Р°РІР»СЏС‚СЊ РјРµС‚РєРё
            myCollection = new ymaps.GeoObjectCollection();
            
            for (iIndex in arMarkers)
            {
                var oMarker = arMarkers[iIndex];
                var zoom = 0;
                
                if ((oMarker['Latitude']+'').length == 0 || (oMarker['Longitude']+'').length == 0)
                    continue;
                
                //Проверим установлен ли Zoom 
                if ((oMarker['Zoom']+'').length != 0)
                    zoom = parseInt(oMarker['Zoom']) ;
                
                myPlacemark = new ymaps.Placemark([oMarker['Latitude'], oMarker['Longitude']], {
                    // РЎРІРѕР№СЃС‚РІР°
                    balloonContentBody: oMarker['Info'],
                    maxWidth: 270
                }, {
                    // РћРїС†РёРё
                });
                
                if (!bounds)
                {
                    bounds = myPlacemark.geometry.getBounds();
                }
                else
                {
                    updateBounds(myPlacemark.geometry.getBounds(), bounds);
                }
                
                myCollection.add(myPlacemark);                                  
            }   
            map.geoObjects.add(myCollection);                  
            
            map.setBounds(bounds, {
                checkZoomRange: true,
                callback: function (err)
                {
                    //Если 1 элемент и Zoom установлен установим его 
                    if (arMarkers.length == 1 && zoom > 0)
                    {                                               
                        map.setZoom(zoom);
                    } 
                }
            });
        }
        else
        {
            var myOptions = {
                scrollwheel: false,
                mapTypeControl: true,
                mapTypeControlOptions : {style:google.maps.MapTypeControlStyle.DROPDOWN_MENU},
                streetViewControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            var map = new google.maps.Map(document.getElementById(sMapId), myOptions);
            var latlngbounds = new google.maps.LatLngBounds();
            var infowindow = new google.maps.InfoWindow();
            for (iIndex in arMarkers)
            {
                var oMarker = arMarkers[iIndex];
                var zoom = 0;
                
                if ((oMarker['Latitude']+'').length == 0 || (oMarker['Longitude']+'').length == 0)
                    continue;
                
                //Проверим установлен ли Zoom 
                if ((oMarker['Zoom']+'').length != 0)
                    zoom = parseInt(oMarker['Zoom']) ;
                
                var myLatlng = new google.maps.LatLng(oMarker['Latitude'], oMarker['Longitude']);
                latlngbounds.extend(myLatlng); 
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
                    title: oMarker['SectionFullNameSign'] + ' ' + oMarker['Address'],
                    content: oMarker['Info']
                });

                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,this);
                    infowindow.setContent(this.content);
                });
            }
            
            if (arMarkers.length == 1 && zoom > 0)  
            {
                map.setCenter( latlngbounds.getCenter());
                map.setZoom(zoom);
            }
            else
                map.setCenter( latlngbounds.getCenter(), map.fitBounds(latlngbounds));
        }
    }
    
    function updateBounds(pointBounds, bounds)
    {
        if(pointBounds[0] && pointBounds[1]) {
            bounds[0][0] = Math.min(pointBounds[0][0], bounds[0][0]);
            bounds[0][1] = Math.min(pointBounds[0][1], bounds[0][1]);
            
            bounds[1][0] = Math.max(pointBounds[1][0], bounds[1][0]);
            bounds[1][1] = Math.max(pointBounds[1][1], bounds[1][1]);
        }
    }
    
    function PublicMap()
    {
        this.LoadMap = function()
        {
            if (isYandex)
                ymaps.ready(loadMap);
            else
                loadMap();
        }
        
        this.SetMapId = function(_mapId)
        {
            sMapId = _mapId;
        }
        
        this.SetMapType = function(_mapType)
        {
            sMapType = _mapType;
            isYandex = sMapType == 'yandex';
        }
        
        this.SetWidth = function(_width)
        {
            iWidth = _width;
        }
        
        this.SetHeight = function(_height)
        {
            iHeight = _height;
        }
        
        this.SetMarkers = function(_markers)
        {
            arMarkers = _markers;
        }
    }
    
    return new PublicMap();
}