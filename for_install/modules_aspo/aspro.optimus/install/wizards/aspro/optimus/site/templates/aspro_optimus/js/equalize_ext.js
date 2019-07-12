$.fn.equalizeHeightsExt = function( outer, classNull, minHeight ){
	var maxHeight = this.map( function( i, e ){
		var minus_height=0,
			calc_height=0;
		if(classNull!==false){
			minus_height=parseInt($(e).find(classNull).actual('outerHeight'));
		}
		if(minus_height)
			minus_height+=12;
		$(e).css('height', '');
		if( outer == true ){
			calc_height=$(e).actual('outerHeight')-minus_height;
		}else{
			calc_height=$(e).actual('height')-minus_height;			
		}
		/*if(minHeight!==false){
			if(calc_height<minHeight){
				calc_height+=(minHeight-calc_height);
			}
			if(window.matchMedia('(max-width: 520px)').matches){
				calc_height=300;
			}
			if(window.matchMedia('(max-width: 400px)').matches){
				calc_height=200;
			}
		}*/
		return calc_height;
	}).get();
	
	for(var i = 0, c = maxHeight.length; i < c; ++i){
		if(maxHeight[i] % 2){
			--maxHeight[i];
		}
	}


	return this.height( Math.max.apply( this, maxHeight ) );
}

$.fn.sliceHeightExt = function( options ){
	function _slice(el){
		el.each(function() {
			$(this).css('line-height', '');
			$(this).css('height', '');
		});
		if(typeof(options.autoslicecount) == 'undefined' || options.autoslicecount !== false){
			var elw = (el.first().hasClass('item') ? el.first().outerWidth() : el.first().parents('.item').outerWidth());
			var elsw = el.first().parents('.top_wrapper').outerWidth();
			if(!elw){
				if(options.parent){
					elw=el.closest(options.parent).outerWidth()-5;
				}else{
					elw=el.first().outerWidth()-5;
				}
			}
			if(!elsw){
				elsw = el.first().parents('.row').outerWidth();
			}
			if(elsw && elw){
				options.slice = Math.floor(elsw / elw);
			}
		}
		if(options.slice){
			for(var i = 0; i < el.length; i += options.slice){
				$(el.slice(i, i + options.slice)).equalizeHeightsExt(options.outer, options.classNull, options.minHeight);
			}
		}
		if(options.lineheight){
			var lineheightAdd = parseInt(options.lineheight);
			if(isNaN(lineheightAdd)){
				lineheightAdd = 0;
			}
			el.each(function() {
				$(this).css('line-height', ($(this).actual('height') + lineheightAdd) + 'px');
			});
		}
	}
	var options = $.extend({
		slice: null,
		outer: false,
		lineheight: false,
		autoslicecount: true,
		classNull: false,
		minHeight: false,
		options: false,
		parent: false,
	}, options);

	var el = $(this);
	_slice(el);

	BX.addCustomEvent('onWindowResize', function(eventdata) {
		ignoreResize.push(true);
		_slice(el);
		ignoreResize.pop();
	});
}

var timerResize = false, ignoreResize = [];
$(window).resize(function(){
	if(!ignoreResize.length){
		if(timerResize){
			clearTimeout(timerResize);
			timerResize = false;
		}
		timerResize = setTimeout(function(){
			BX.onCustomEvent('onWindowResize', false);
		}, 100);
	}
});