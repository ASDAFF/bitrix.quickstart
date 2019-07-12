$(document).ready(function(){
	if(parseInt(RSGOPRO_change_speed)<1)
		RSGOPRO_change_speed = 2000;
	if(parseInt(RSGOPRO_change_delay)<0)
		RSGOPRO_change_delay = 8000;
	
	if($('#jssor_slider1').find('.item').length>0)
	{
		var options = {
			$AutoPlay: 1,										//[Optional] Whether to auto play, to enable slideshow, this option must be set to true
			$DragOrientation: 1,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)
			$AutoPlayInterval: RSGOPRO_change_delay,			//[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing
			$SlideDuration: RSGOPRO_change_speed,               //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500

			$DirectionNavigatorOptions: {                       //[Optional] Options to specify and enable direction navigator or not
				$Class: $JssorDirectionNavigator$,              //[Requried] Class to create direction navigator instance
				$ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
				$AutoCenter: 2,                                 //[Optional] Auto center arrows in parent container, 0 No, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
				$Steps: 1                                       //[Optional] Steps to go for each navigation request, default value is 1
			},
			
			$NavigatorOptions: {                                //[Optional] Options to specify and enable navigator or not
				$Class: $JssorNavigator$,                       //[Required] Class to create navigator instance
				$ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
				$Steps: 1,                                      //[Optional] Steps to go for each navigation request, default value is 1
				$Lanes: 1,                                      //[Optional] Specify lanes to arrange items, default value is 1
				$SpacingX: 13,                                  //[Optional] Horizontal space between each item in pixel, default value is 0
				$SpacingY: 13,                                  //[Optional] Vertical space between each item in pixel, default value is 0
				$Orientation: 1                                 //[Optional] The orientation of the navigator, 1 horizontal, 2 vertical, default value is 1
			}
		};
		var jssor_slider1 = new $JssorSlider$("jssor_slider1", options);
		// responsive code begin
		function RSGOPRO_ScaleSlider() 
		{
			var parentWidth = $('#jssor_slider1').parent().width();
			if (parentWidth) {
				if( parentWidth<988 ) {
					jssor_slider1.$SetScaleWidth(parentWidth);
				} else {
					jssor_slider1.$SetScaleWidth(990);
				}
			} else {
				window.setTimeout(RSGOPRO_ScaleSlider, 30);
			}
		}
		RSGOPRO_ScaleSlider();
		$(window).bind('resize', RSGOPRO_ScaleSlider);
		// responsive code end
	}
	
	// play video
	$('.aroundjssorslider1').find('video').each(function(){
		if( $(this).attr('autoplay')=='autoplay' ) {
			$(this).get(0).play();
		}
	});
	
});