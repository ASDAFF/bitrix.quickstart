function emarket_mSlider(action) {
	var _window = $('.emarket-mSlider').children(".mSlider-wrap").children(".mSlider-window");
	
	var	element_wcount = 4,
		window_width = _window.parent(".mSlider-wrap").width(),
		element_width = window_width/element_wcount,
		element_Sum = _window.find("li").size(),
        all_ctn = $(".head .mSlider-wrap li").size();
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
			}
		break;
		case 'next':			
			var next_slide = _window.children("li.current").next();
            var cnt_item =  _window.children("li.current").index()+4;
            
			if(all_ctn > cnt_item) {
			
				_window.animate({left: "-="+element_width+"px"});
				_window.children("li").removeClass("current");
				
				next_slide.addClass("current");
			}
		break;
		default: break;
	}
}

$(document).on("click", "a.mSlider-prev", function(event){
	event.preventDefault();
	emarket_mSlider('prev');
});
$(document).on("click", "a.mSlider-next", function(event){
	event.preventDefault();
	emarket_mSlider('next');
});

$(document).ready(function(){

	$(".emarket-mSlider").each(function(){
		$.ajax({
		type: "POST",
		url: "/ajax/get_compare.php",
		dataType: "html",
		success: function(html){
			$("#emarket-compare-list").html(html);
		}
	});
		var _window = $(this).children(".mSlider-wrap").children(".mSlider-window");
		
		var	element_wcount = 4,
			window_width = _window.parent(".mSlider-wrap").width(),
			element_width = window_width/element_wcount,
			element_Sum   = _window.find("li").size(),
			slider_ReelWidth = element_width * element_Sum;
			
		_window.width(slider_ReelWidth);
		_window.find("li").width(element_width);
		//del item from compare
		_window.find("li").children('.close').on('click', function(){
			compare_item_form.submit();
		});
	});
	
	
	$('#switch').on('change', function(){
		var u = new Url(location.href);
		console.log(u);
		//alert();
		if($(this).prop('checked'))
			 window.location = u.path+'?DIFFERENT=Y';
		else
			 window.location = u.path+'?DIFFERENT=N';

	})

	
	
	
})