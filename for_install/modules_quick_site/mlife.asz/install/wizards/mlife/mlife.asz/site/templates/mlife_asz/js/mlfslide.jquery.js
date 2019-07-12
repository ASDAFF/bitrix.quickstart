;(function ($) {

$.fn.mlfslide = function( options ) {

 var settings = $.extend( {
      'id'         : 0,
      'mlfSlideSpeed' : 700,
	  'mlfTimeOut' : 5000,
	  'mlfNeedLinks' : true,
	  'mlfNeedCount' : true,
	  'sliwidth': 25,
	  'visible': 1,
	  'slideCount': 1
    },options);
	
	if(settings.visible) {
		var displayvis = 'block';
	}else{
		var displayvis = 'none';
	}
	
	
	$('#slider_'+settings.id).css({'display':displayvis});
	$('#slider_'+settings.id+' .slide').css(
		{"position" : "absolute",
		 "top":'0', "left": '0',}).hide().eq(0).show();
	var slideNum = 0;
	var slideTime;
	var slideCount = $("#slider_"+settings.id+" .slide").size();
	var animSlide = function(arrow){
		clearTimeout(slideTime);
		$('#slider_'+settings.id+' .slide').eq(slideNum).fadeOut(settings.mlfSlideSpeed);
		if(arrow == "next"){
			if(slideNum == (slideCount-1)){slideNum=0;}
			else{slideNum++}
			}
		else if(arrow == "prew")
		{
			if(slideNum == 0){slideNum=slideCount-1;}
			else{slideNum-=1}
		}
		else{
			slideNum = arrow;
			}
		$('#slider_'+settings.id+' .slide').eq(slideNum).fadeIn(settings.mlfSlideSpeed, rotator);
		$('#slider_'+settings.id+' .control-slide.active').removeClass("active");
		$('#slider_'+settings.id+' .control-slide').eq(slideNum).addClass('active');
		}
if(settings.mlfNeedLinks && slideCount > settings.slideCount){
var $linkArrow = $('<a id="prewbutton" href="#">&lt;</a><a id="nextbutton" href="#">&gt;</a>')
	.appendTo('#slider_'+settings.id+' .slider-wrap');
	$('#slider_'+settings.id+' #nextbutton').click(function(){
		animSlide("next");
		return false;
		})
	$('#slider_'+settings.id+' #prewbutton').click(function(){
		animSlide("prew");
		return false;
		})
}
if(settings.mlfNeedCount && slideCount > settings.slideCount){
	var $adderSpan = '';
	$('#slider_'+settings.id+' .slide').each(function(index) {
			$adderSpan += '<span class = "control-slide"><b>' + index + '</b></span>';
		});
	$('<div class ="sli-links">' + $adderSpan +'</div>').appendTo('#slider_'+settings.id+' .slider-wrap');
	$('#slider_'+settings.id+' .sli-links').css({'width': settings.sliwidth*slideCount});
	$('#slider_'+settings.id+' .control-slide:first').addClass("active");
}	

	
	$('#slider_'+settings.id+' .control-slide').click(function(){
	var goToNum = parseFloat($(this).text());
	animSlide(goToNum);
	});
	var pause = false;
	var rotator = function(){
			if(!pause && settings.mlfTimeOut){slideTime = setTimeout(function(){animSlide('next')}, settings.mlfTimeOut);}
			}
	$('#slider_'+settings.id+' .slider-wrap').hover(	
		function(){clearTimeout(slideTime); pause = true;},
		function(){pause = false; rotator();
		});
	rotator();

};
})(jQuery);