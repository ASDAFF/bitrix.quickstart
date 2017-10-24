$(document).ready(function(){
	
	$('.lwMap2Gis').each(function(index, element) {
		
		DG.then(function () {
			map = DG.map($(element).attr('id'), {
				center: [$(element).attr('data-center-lat'), $(element).attr('data-center-long')],
				zoom: $(element).attr('data-zoom'),
				touchZoom: false,
				scrollWheelZoom: false,
				doubleClickZoom: $(element).attr('data-doubleClickZoom')=='Y' ? true : false,
				boxZoom: false,
				geoclicker: $(element).attr('data-geoclicker')=='Y' ? true : false,
				zoomControl: true,
				fullscreenControl: true
			});
			
			if ($(element).attr('data-iconpoints')){
				map_icon = DG.icon({
					iconUrl: $(element).attr('data-iconpoints'),
					iconSize: [$(element).attr('data-iconpoints-width'), $(element).attr('data-iconpoints-height')]
				});
				DG.marker([$(element).attr('data-coordinates-points-lat'), $(element).attr('data-coordinates-points-long')], {icon: map_icon}).addTo(map).bindPopup($(element).attr('data-post-points'));
			} else {
				DG.marker([$(element).attr('data-coordinates-points-lat'), $(element).attr('data-coordinates-points-long')]).addTo(map).bindPopup($(element).attr('data-post-points'));
			}
		});	
		
	});
	
});