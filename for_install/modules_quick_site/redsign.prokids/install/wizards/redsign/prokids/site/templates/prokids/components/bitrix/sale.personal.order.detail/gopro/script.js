function CStoreMap(opts){

	/*** defaults ***/
	var s = this;

    /*** init ***/
	var so = opts;
	var sv = {};
	var sc = {};
	
	s.showHide = function(obj, way){
		var displayOn = (typeof(obj.prevDisplay) == "undefined" ? 'inline-block' : obj.prevDisplay);

		BX.style(obj, 'display', way ? displayOn : 'none');
	}
	s.show = function(obj){
		s.showHide(obj, true);
	}
	s.hide = function(obj){
		obj.prevDisplay = BX.style(obj, 'display');
		s.showHide(obj, false);
	}

	sc.area = BX(so.area);
	sc.mapContainer = BX('map-container');
	s.hide(sc.mapContainer);
	
	sc.btnShow = BX('map-show');
	BX.bind(sc.btnShow, 'click', function(){

		if(typeof sc.map == 'undefined') return;

		s.show(sc.btnHide);
		s.hide(this);

		s.show(sc.mapContainer);
		sc.map.container.fitToViewport();
	});

	sc.btnHide = BX('map-hide');
	BX.bind(sc.btnHide, 'click', function(){

		if(typeof sc.map == 'undefined') return;

		s.show(sc.btnShow);
		s.hide(this);

		s.hide(sc.mapContainer);
		sc.map.container.fitToViewport();

	});
	s.hide(sc.btnHide);

	BX(function(){

		ymaps.ready(function(){
			sc.map = window.GLOBAL_arMapObjects[so.mapId];
		});

	});

	return s;
}
