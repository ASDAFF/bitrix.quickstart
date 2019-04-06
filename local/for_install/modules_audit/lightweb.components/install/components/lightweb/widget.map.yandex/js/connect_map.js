$(document).ready(function() {
	
	var id = parseInt($(".ymap").data("map-id")),
		center = $(".ymap").data("map-center").split(","),
		zoom = parseInt($(".ymap").data("map-zoom")),
		points = JSON.parse(JSON.stringify($(".ymap").data("map-points"), null, 2)),
		points_text = JSON.parse(JSON.stringify($(".ymap").data("map-points-text"), null, 2)),
		view = $(".ymap").data("map-view"),
		controls = $(".ymap").data("map-controls");
	
	ymaps.ready(init);

	function init () {
		
		myMap = new ymaps.Map("ymap"+id, {
			center: [center[0], center[1]],
			zoom: zoom,
			type: view,
			controls: []
		});
		
		if ($.inArray("none", controls) == "-1") {
			for (var i = 0; i < controls.length; i++) {
				myMap.controls.add(controls[i]);
			}
		}
		
		for (var i = 0; i < points.length; i++) {
			var point_coords = points[i].split(",");
			myGeoObject = new ymaps.GeoObject({
				geometry: {
						type: "Point",
						coordinates: [point_coords[0],point_coords[1]]
					},
					properties: {
						iconContent: points_text[i]
					}
				}, {
					preset: 'islands#blueStretchyIcon',
					draggable: false
				});	
			myMap.geoObjects.add(myGeoObject);
		}
	}
});