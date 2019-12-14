$(document).ready(function(){
	var arShopsItem = $('#lovekids_shops').find('.shop_item'),
		arShopsLink = arShopsItem.children('input');
		arMapCoord = [0, 0];
	arShopsLink.each(function(){
		var arCoords = $(this).data('coords').split(',');
		arMapCoord[0] = arMapCoord[0] + parseFloat(arCoords[0]);
		arMapCoord[1] = arMapCoord[1] + parseFloat(arCoords[1]);
	});
	arMapCoord[0] = arMapCoord[0] / arShopsLink.length;
	arMapCoord[1] = arMapCoord[1] / arShopsLink.length;
	var rsPlacemark = {}, rsYMapShops;
	ymaps.ready(function(){
			rsYMapShops = new ymaps.Map('rsYMapShops', {
			center: arMapCoord,
			zoom: 16,
			type:'yandex#publicMap',
			behaviors: ['default', 'scrollZoom']
		});
		
		arShopsLink.each(function(){
			var arCoords = $(this).data('coords').split(','),
				id = $(this).attr('id');
			arCoords[0] = parseFloat(arCoords[0]);
			arCoords[1] = parseFloat(arCoords[1]);
			rsPlacemark[id] = new ymaps.Placemark(
				arCoords, {
					balloonContentHeader: $(this).next().text(),
					balloonContentBody: $(this).siblings('.descr').html()
				}
			);
			rsYMapShops.geoObjects.add(rsPlacemark[id]);
			
		});
		rsYMapShops.setBounds(rsYMapShops.geoObjects.getBounds(), {checkZoomRange: true}).controls.add('mapTools').add('zoomControl').add('typeSelector');
	});
	arShopsLink.on('change', function(){
		var arShopsChecked = arShopsLink.filter(':checked');
		if(arShopsChecked.length == 0){
			for(var id in rsPlacemark){
				rsPlacemark[id].options.set('visible', true);
			}
		}
		else{
			arShopsLink.each(function(){
				if($(this).is(':checked')){
					rsPlacemark[$(this).attr('id')].options.set('visible', true);
				}
				else{
					rsPlacemark[$(this).attr('id')].options.set('visible', false);
				}
			});
		}
	});
	arShopsItem.children('label').on('mouseenter', function(){
		rsPlacemark[$(this).attr('for')].options.set('preset', 'twirl#redDotIcon');
	}).on('mouseleave', function(){
		rsPlacemark[$(this).attr('for')].options.set('preset', 'twirl#blueIcon');
	});
});