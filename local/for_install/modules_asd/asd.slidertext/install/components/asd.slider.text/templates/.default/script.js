if (typeof requestInterval != 'function') {
	window.requestInterval = function(fn, delay) {
		return setInterval(fn, delay);
	}
}
if (typeof clearRequestInterval != 'function') {
	window.clearRequestInterval = function(handle) {
		return clearInterval(handle);
	}
}

$(document).ready(function() {

	//Set Default State of each portfolio piece
	$('#asd_slider_paging').show();
	$('#asd_slider_paging a:first').addClass('active');

	//Get size of images, how many there are, then determin the size of the image reel.
	var imageWidth = $('.asd_slider_window').width();
	var imageSum = $('.asd_slider_image_reel img').size();
	var imageReelWidth = imageWidth * imageSum;

	//Adjust the image reel to its new size
	$('.asd_slider_image_reel').css({'width' : imageReelWidth});

	//Paging + Slider Function
	rotate = function(){
		var triggerID = $active.attr('rel') - 1; //Get number of times to slide
		var image_reelPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

		$('#asd_slider_paging a').removeClass('active'); //Remove all active class
		$active.addClass('active'); //Add active class (the $active is declared in the rotateSwitch function)

		//Slider Animation
		$('.asd_slider_image_reel').animate({
			left: -image_reelPosition
		}, 500 );

		//Change text
		$('#asd_slider_overtext .asd_slider_title').html($('#asd_slider_title_' + triggerID).html());
		$('#asd_slider_overtext .asd_slider_title').attr('href', $('#asd_slider_link_' + triggerID).html());
		$('#asd_slider_overtext .asd_slider_des').html($('#asd_slider_text_' + triggerID).html());
		$('#asd_slider_overtext .asd_slider_des').attr('href', $('#asd_slider_link_' + triggerID).html());
	};

	//Rotation + Timing Event
	rotateSwitch = function(){
		play = requestInterval(function(){ //Set timer - this will repeat itself every 3 seconds
			$active = $('#asd_slider_paging a.active').next();
			if ( $active.length === 0) { //If paging reaches the end...
				$active = $('#asd_slider_paging a:first'); //go back to first
			}
			rotate(); //Trigger the paging and slider function
		}, timerSpeed); //Timer speed in milliseconds (3 seconds)
	};

	rotateSwitch(); //Run function on launch

	//On Hover
	$('.asd_slider_image_reel').hover(function() {
		clearRequestInterval(play); //Stop the rotation
	}, function() {
		rotateSwitch(); //Resume rotation
	});
	$('#asd_slider_overtext').hover(function() {
		clearRequestInterval(play); //Stop the rotation
	}, function() {
		rotateSwitch(); //Resume rotation
	});

	//On Click
	$('#asd_slider_paging a').click(function() {
		$active = $(this); //Activate the clicked paging
		//Reset Timer
		clearRequestInterval(play); //Stop the rotation
		rotate(); //Trigger rotation immediately
		rotateSwitch(); // Resume rotation
		return false; //Prevent browser jump to link anchor
	});

});