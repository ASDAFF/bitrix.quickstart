function RSMonopolyDrawPlacemark(arShopsItem,rsPlacemark) {
	arShopsItem.each(function(){
		if($(this).hasClass('cityempty') || $(this).hasClass('typeempty')){
			rsPlacemark[$(this).data('id')].options.set('visible', false);
		} else {
			rsPlacemark[$(this).data('id')].options.set('visible', true);
		}
	});
}

$(document).ready(function(){

	var arShopsItem = $('.shops_list').find('.item'),
		arMapCoord = [0, 0],
		rsPlacemark = {},
		rsYMapShops;

	arShopsItem.each(function(){
		var arCoords = $(this).data('coords').split(',');
		arMapCoord[0] = arMapCoord[0] + parseFloat(arCoords[0]);
		arMapCoord[1] = arMapCoord[1] + parseFloat(arCoords[1]);
	});
	arMapCoord[0] = arMapCoord[0] / arShopsItem.length;
	arMapCoord[1] = arMapCoord[1] / arShopsItem.length;
	var rsPlacemark = {}, rsYMapShops;
	ymaps.ready(function(){
		rsYMapShops = new ymaps.Map('rsYMapShops', {
			center: arMapCoord,
			zoom: 16,
			type:'yandex#publicMap',
			behaviors: ['default', 'scrollZoom']
		});
		arShopsItem.each(function(){
			var arCoords = $(this).data('coords').split(','),
				id = $(this).data('id');
			arCoords[0] = parseFloat(arCoords[0]);
			arCoords[1] = parseFloat(arCoords[1]);
			rsPlacemark[id] = new ymaps.Placemark(
				arCoords, {
					balloonContentHeader: $(this).find('name').html(),
					balloonContentBody: $(this).find('.descr').html()
				}
			);
			rsYMapShops.geoObjects.add(rsPlacemark[id]);
		});
		rsYMapShops.setBounds(rsYMapShops.geoObjects.getBounds(), {checkZoomRange: true}).controls.add('mapTools').add('zoomControl').add('typeSelector');
	});

	arShopsItem.on('mouseenter', function(){
		rsPlacemark[$(this).data('id')].options.set('preset', 'twirl#redDotIcon');
	}).on('mouseleave', function(){
		rsPlacemark[$(this).data('id')].options.set('preset', 'twirl#blueIcon');
	});

	// city search
	$(document).on('keyup','.search_city input',function(){
		var $inputObj = $(this);
		var $citiesList = $inputObj.parents('.search_city').find('.cities_list');
		var value = $inputObj.val();
		var len = 0;
		
		if(value.length<1) {
			$citiesList.css('display','none');
		} else {
			$citiesList.css('display','block');
			len = 0;
			$citiesList.find('a').each(function(){
				var a_value = $(this).html().substr(0,value.length);
				if( value.toLowerCase()==a_value.toLowerCase() ) {
					$(this).parent().css('display','block');
					len++;
				} else {
					$(this).parent().css('display','none');
				}
			});
			if( len<1 ) {
				$citiesList.css('display','none');
			}
		}
	});
	$(document).on('blur','.search_city input',function(){
		var value = $(this).val();
		if(value.length<1) {
			$('.shops_list').find('li').removeClass('cityempty');
		} else {
			$('.search_city input').trigger('keyup');
		}
		RSMonopolyDrawPlacemark(arShopsItem,rsPlacemark);
	});
	$(document).on('click','.cities_list a',function(){
		var cityFilter = $(this).data('filter');
		$('.search_city input').val( $(this).html() );
		$('.cities_list').css('display','none');
		$('.shops_list').find('li').addClass('cityempty');
		$('.shops_list').find('li[data-city="'+cityFilter+'"]').removeClass('cityempty');
		RSMonopolyDrawPlacemark(arShopsItem,rsPlacemark);
		return false;
	});

	// filter
	$(document).on('click','.shops .filter .btn',function(){
		$('.shops .filter').find('.btn').removeClass('btn-primary').addClass('btn-default');
		$(this).addClass('btn-primary').removeClass('btn-default');
		var typeFilter = $(this).data('filter');
		if(typeFilter.length>0) {
			$('.shops_list').find('li').addClass('typeempty');
			$('.shops_list').find('li[data-type="'+typeFilter+'"]').removeClass('typeempty');
		} else {
			$('.shops_list').find('li').removeClass('typeempty');
		}
		RSMonopolyDrawPlacemark(arShopsItem,rsPlacemark);
	});

});