function emarket_mSlider_refresh(parent_id, element_wcount) {
	var _window = $('#'+parent_id).children(".mSlider-wrap").children(".mSlider-window"),
		element_current = _window.children("li.current").attr("data-slide"),
		element_first   = _window.children("li:first").attr("data-slide"),
		element_last    = _window.children("li:last").attr("data-slide");

	if(element_current == element_first){
		_window.children("a.mSlider-prev").removeClass("arrow_act");
		_window.children("a.mSlider-next").addClass("arrow_act");
	} else
	if(element_current == element_last-(element_wcount+1)){
		_window.children("a.mSlider-prev").addClass("arrow_act");
		_window.children("a.mSlider-next").removeClass("arrow_act");
	}
	else{
		_window.children("a.mSlider-prev").addClass("arrow_act");  
		_window.children("a.mSlider-next").addClass("arrow_act");        
	}
};

function emarket_mSlider(parent_id, action) {
	var _window = $('#'+parent_id).children(".mSlider-wrap").children(".mSlider-window");
	
	var	element_wcount = 4,
		window_width = _window.parent(".mSlider-wrap").width(),
		element_width = window_width/element_wcount,
		element_Sum = _window.find("li").size(),
		slider_ReelWidth = element_width * element_Sum;
	
	_window.width(slider_ReelWidth);
	_window.find("li").width(element_width);
	
	switch(action) {
		case 'prev':
			var prev_slide = _window.children("li.current").prev();
			if (prev_slide.length > 0) {
				_window.animate({left: "+="+element_width+"px"});
				_window.children("li").removeClass("current");
				prev_slide.addClass("current");
				emarket_mSlider_refresh(parent_id, element_wcount);
			}
		break;
		case 'next':			
			var next_slide = _window.children("li.current").next();
			var naxi = _window.children("li.current").nextAll();
			var element_count = 0;
			naxi.each(function(){element_count++;})
			
			if((next_slide.length > 0) && (element_count >= element_wcount)) {
			
				_window.animate({left: "-="+element_width+"px"});
				_window.children("li").removeClass("current");
				
				next_slide.addClass("current");
				emarket_mSlider_refresh(parent_id, element_wcount);
			}
		break;
		default: break;
	}
}

$(document).on("click", "a.mSlider-prev", function(event){
	event.preventDefault();
	var parent_id = $(this).parent(".emarket-mSlider").attr('id');
	emarket_mSlider(parent_id, 'prev');
});
$(document).on("click", "a.mSlider-next", function(event){
	event.preventDefault();
	var parent_id = $(this).parent(".emarket-mSlider").attr('id');
	emarket_mSlider(parent_id, 'next');
});

$(document).ready(function(){
	$(".emarket-mSlider").each(function(){
		var parent_id = $(this).attr('id');
		var _window = $('#'+parent_id).children(".mSlider-wrap").children(".mSlider-window");
		
		var	element_wcount = 4,
			window_width = _window.parent(".mSlider-wrap").width(),
			element_width = window_width/element_wcount,
			element_Sum   = _window.find("li").size(),
			slider_ReelWidth = element_width * element_Sum;
			
		_window.width(slider_ReelWidth);
		_window.find("li").width(element_width);
	})
})