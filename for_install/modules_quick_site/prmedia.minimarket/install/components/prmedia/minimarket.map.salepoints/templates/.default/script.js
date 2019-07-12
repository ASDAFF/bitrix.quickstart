(function($, ymaps, window) {
	$(function() {
		var map_salepoints = $('#map_salepoints');
		if (window.mm_map_salepoints.length > 0 && map_salepoints.length && window.ymaps) {
			function init() {
				var def_zoom = 10;
				var map = new ymaps.Map('map_salepoints', {
					center: [0,0],
					zoom: def_zoom,
					controls: ['zoomControl', 'fullscreenControl'],
					behaviors: ['drag']
				});
				var item = [];
				for (var i in window.mm_map_salepoints) {
					item = window.mm_map_salepoints[i];
					map.geoObjects.add(new ymaps.Placemark(item.loc, {}));
				}
				
				if (map.geoObjects.getBounds() != null) {
					map.setBounds(map.geoObjects.getBounds());
				}
				if (map.getZoom() > def_zoom) {
					map.setZoom(def_zoom);
				}
			}
			ymaps.ready(init);
		}
	});
})(jQuery, ymaps, window);