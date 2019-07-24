ymaps.ready(function () {
    var contactsMap = new ymaps.Map('contacts_map_wrapper', {
        center: [55.733835, 37.588227],
        controls: [
            'fullscreenControl',
            'geolocationControl',
            'searchControl',
            'typeSelector',
            'zoomControl'
        ],
        zoom: 6
    });
    
    var obContactsCollection = new ymaps.GeoObjectCollection({},{
        iconLayout: 'default#image',
        iconImageHref: pathMarker,
        iconImageSize: [36, 51]
    });
    
    for (var i = 0; i < obYAMap.length; ++i) {
        var obContact = obYAMap[i];
        
        var sBalloonContentSrc = '<div style="font-size:12px; padding-bottom: 15px;">';
        
        sBalloonContentSrc += '<div style="color:#5b7f91;font-weight:bold">' + obContact.name + '</div>';
        sBalloonContentSrc += '<div>Адрес: ' + obContact.address + '</div>';
        
        if ((obContact.phone.length > 0) || (obContact.fax != "")) {
            sBalloonContentSrc += '<div>Контакты: '
            if (obContact.phone.length > 0) {
                sBalloonContentSrc += 'телефон ' + obContact.phone.join(', ');
            }
            if (obContact.fax != "") {
                sBalloonContentSrc += '; факс ' + obContact.fax;
            }
            sBalloonContentSrc += '</div>';
        }
        
        if ((obContact.email.length && obContact.email.length > 0) || (obContact.site != "")) {
            sBalloonContentSrc += '<div>'
            if (obContact.email.length > 0) {
                for (var j = 0; j < obContact.email.length; ++j) {
                    sBalloonContentSrc += '<a href="mailto:' + obContact.email[j] + '">' + obContact.email[j] + '</a>';
                    if (j < (obContact.email.length - 1)) {
                        sBalloonContentSrc += ', ';
                    }
                }
            }
            
            if (obContact.site != "") {
                sBalloonContentSrc += '<a style="margin-left: 10px;" href="http://' + obContact.site + '" target="_blank">' + obContact.site + '</a>';
            }
            
            sBalloonContentSrc += '</div>';
        }        
        
        sBalloonContentSrc += '</div>';
        
        var obContactPlacemark = new ymaps.Placemark([obContact.coords.x, obContact.coords.y], {
            hintContent: obContact.name,
            balloonContent: sBalloonContentSrc
        });
        
        obContactsCollection.add(obContactPlacemark);
    }
    
    contactsMap.geoObjects.add(obContactsCollection);
    contactsMap.setBounds(contactsMap.geoObjects.getBounds(), {
        checkZoomRange: true
    });
});