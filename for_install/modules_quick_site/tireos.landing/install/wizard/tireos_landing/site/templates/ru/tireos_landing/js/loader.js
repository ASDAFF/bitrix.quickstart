$(document).ready(function() {
"use strict";

jQuery.fn.fixcenter = function () {
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2)) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2)) + "px");
    return this;
}

// *************
// Selectbox
// *************
// $('.filter select').selectbox();


$('select.styled').customSelect();


// *************
// Validate
// *************
$('form').validate({
	onKeyup : true,
	eachValidField : function() {
		$(this).closest('div').removeClass('error').addClass('success');
	},
	eachInvalidField : function() {
		$(this).closest('div').removeClass('success').addClass('error');
	}
});

// insert-attr
$('.insert-attr').attr('data-pattern', "^[-a-z0-9!#$%&'*+/=?^_`{|}~]+(\.[-a-z0-9!#$%&'*+/=?^_`{|}~]+)*@([a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?\.)*(aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|[a-z][a-z])$");


// *************
// Colorbox
// *************
$(".group1").colorbox({
	rel:'group1',
	className: 'hideElement',
	iframe:true,
	innerWidth:640,
	innerHeight:390,
});

$(".group2").colorbox({
	rel:'group2',
	title: false,
	width:"100%"
});

$(".group3").colorbox({
	rel:'group3',
	title: false,
	width:"100%"
});

$(".group5").colorbox({
	rel:'group1',
	className: 'hideElement',
	iframe:true,
	width: '100%',
	innerHeight:390,
});

// *************
// BxSlider
// *************
var bxSlider = $('.software_slider .form-bxslider').bxSlider({
	mode: 'fade',
	auto: true,
	pause: 10000,
	adaptiveHeight: true,
	pagerCustom: '#dafault_pager',
	onSliderLoad: function() {
		$('.form-bxslider li').each(function() {
			var setTimeoutID;
			if ( $(this).is(':visible') ) {
				setTimeoutID = setTimeout(function() {
					$('.fin_1').animate({ 'opacity': 1 }, 700);
				}, 400);
				setTimeoutID = setTimeout(function() {
					$('.fin_2').animate({ 'opacity': 1 }, 700);
				}, 800);
				setTimeoutID = setTimeout(function() {
					$('.fin_3').animate({ 'opacity': 1 }, 700);
				}, 1400);
			}
		});
	}
});

$('.xSlider').bxSlider({
	mode: 'fade',
	auto: true,
	pause: 10000,
	adaptiveHeight: true,
	onSliderLoad: function() {
		$('.form-bxslider li').each(function() {
			var setTimeoutID;
			if ( $(this).is(':visible') ) {
				setTimeoutID = setTimeout(function() {
					$('.fin_1').animate({ 'opacity': 1 }, 700);
				}, 400);
				setTimeoutID = setTimeout(function() {
					$('.fin_2').animate({ 'opacity': 1 }, 700);
				}, 800);
				setTimeoutID = setTimeout(function() {
					$('.fin_3').animate({ 'opacity': 1 }, 700);
				}, 1400);
			}
		});
	}
});



$('.aboutUs-slider').bxSlider({
	mode: 'horizontal',
	pause: 5000,
	autoHover: true,
	pager: false,
	auto: true
});

// *************
// Paralax
// *************

$('.dark-blue').waypoint(function() {
	setTimeout(function(){$('#animIt1').addClass('fadeOutRight')},0);
	setTimeout(function(){$('#animIt2').addClass('fadeOutRight')},600);
	setTimeout(function(){$('#animIt3').addClass('fadeOutRight')},1200);
	setTimeout(function(){$('#animIt4').addClass('fadeOutRight')},1800);
}, { offset: '100%' });

$('#trainings').waypoint(function() {
	setTimeout(function(){$('#trainings').addClass('fadeOutTop')},0);
}, { offset: '65%' });

$('#animIt5').waypoint(function() {
	setTimeout(function(){$('#animIt5').addClass('fadeOutBigLeft')},0);
	setTimeout(function(){$('#animIt6').addClass('fadeOutBigRight')},0);
}, { offset: '80%' });

$('#animIt7').waypoint(function() {
	setTimeout(function(){$('#animIt8').addClass('fadeOutBigLeft')},0);
	setTimeout(function(){$('#animIt7').addClass('fadeOutBigRight')},0);
}, { offset: '80%' });

$('#animIt9').waypoint(function() {
	setTimeout(function(){$('#animIt9').addClass('fadeOutBigLeft')},0);
	setTimeout(function(){$('#animIt10').addClass('fadeOutBigRight')},0);
}, { offset: '80%' });

$('#animIt11').waypoint(function() {
	setTimeout(function(){$('#animIt11').addClass('fadeOutRight')},0);
	setTimeout(function(){$('#animIt13').addClass('fadeOutBottom')},0);
	setTimeout(function(){$('#animIt12').addClass('fadeOutLeft')},0);
}, { offset: '70%' });

$('.animIt14').waypoint(function() {
	setTimeout(function(){$('.animIt14').addClass('fadeOutTop')},0);
	setTimeout(function(){$('.animIt15').addClass('fadeOutBottom')}, 600);
}, { offset: '70%' });

$('#partners').waypoint(function() {
	/*setTimeout(function(){$('#animIt16').addClass('fadeOutRight')},0);
	setTimeout(function(){$('#animIt17').addClass('fadeOutRight')},200);
	setTimeout(function(){$('#animIt18').addClass('fadeOutRight')},400);
	setTimeout(function(){$('#animIt19').addClass('fadeOutRight')},600);
	setTimeout(function(){$('#animIt20').addClass('fadeOutRight')},800);
	setTimeout(function(){$('#animIt21').addClass('fadeOutRight')},1000);
	setTimeout(function(){$('#animIt22').addClass('fadeOutRight')},1200);
	setTimeout(function(){$('#animIt23').addClass('fadeOutRight')},1400);
	setTimeout(function(){$('#animIt24').addClass('fadeOutRight')},1600);
	setTimeout(function(){$('#animIt25').addClass('fadeOutRight')},1800);
	setTimeout(function(){$('#animIt26').addClass('fadeOutRight')},2000);
	setTimeout(function(){$('#animIt27').addClass('fadeOutRight')},2200);*/

}, { offset: '90%' });

$(".more-photo").click(function(){
	$('.gItemHidden:lt(3)').removeClass("gItemHidden").addClass('fadeOutBottomBonus');
	if(!$('.gItemHidden').length) $(this).remove();
	return false;
})

$('.software_slider').parallax("50%", 0.4);
$('#form_slider .form-bxslider li').parallax("50%", 0.4);


var cnt = 16;
while(cnt<1000){
	if($('#animIt'+cnt).length){
		$('#animIt'+cnt).css("opacity", 1);
		cnt++;
	}
	else
		break;
}

$(".partners-wrap > div").jcarousel();

$('.partner-prev')
	.on('jcarouselcontrol:active', function() {
		$(this).removeClass('inactive');
	})
	.on('jcarouselcontrol:inactive', function() {
		$(this).addClass('inactive');
	})
	.jcarouselControl({
		target: '-=1'
	});

$('.partner-next')
	.on('jcarouselcontrol:active', function() {
		$(this).removeClass('inactive');
	})
	.on('jcarouselcontrol:inactive', function() {
		$(this).addClass('inactive');
	})
	.jcarouselControl({
		target: '+=1'
	});


// devicePixelRatio
if (window.devicePixelRatio > 1.5) {
	var lowresImages = $('img');
	lowresImages.each(function(i) {
		var lowres = $(this).attr('src');
		var highres = lowres.replace(".", "r.");
		$(this).attr('src', highres);
	});

	$('.dark-blue').waypoint(function() {
		setTimeout(function(){$('#animIt1').addClass('fadeOutRight')},0);
		setTimeout(function(){$('#animIt2').addClass('fadeOutRight')},200);
		setTimeout(function(){$('#animIt3').addClass('fadeOutRight')},600);
		setTimeout(function(){$('#animIt4').addClass('fadeOutRight')},1000);
	}, { offset: '100%' });

	$('#trainings').waypoint(function() {
		setTimeout(function(){$('#trainings').addClass('fadeOutTop')},0);
	}, { offset: '100%' });

	$('#animIt5').waypoint(function() {
		setTimeout(function(){$('#animIt5').addClass('fadeOutBigLeft')},0);
		setTimeout(function(){$('#animIt6').addClass('fadeOutBigRight')},0);
	}, { offset: '100%' });

	$('#animIt7').waypoint(function() {
		setTimeout(function(){$('#animIt7').addClass('fadeOutBigLeft')},0);
		setTimeout(function(){$('#animIt8').addClass('fadeOutBigRight')},0);
	}, { offset: '100%' });

	$('#animIt9').waypoint(function() {
		setTimeout(function(){$('#animIt9').addClass('fadeOutBigLeft')},0);
		setTimeout(function(){$('#animIt10').addClass('fadeOutBigRight')},0);
	}, { offset: '100%' });

	$('#animIt11').waypoint(function() {
		setTimeout(function(){$('#animIt11').addClass('fadeOutRight')},0);
		setTimeout(function(){$('#animIt13').addClass('fadeOutBottom')},0);
		setTimeout(function(){$('#animIt12').addClass('fadeOutLeft')},0);
	}, { offset: '100%' });

	$('.animIt14').waypoint(function() {
		setTimeout(function(){$('.animIt14').addClass('fadeOutTop')},0);
		setTimeout(function(){$('.animIt15').addClass('fadeOutBottom')}, 600);
	}, { offset: '100%' });

	$('#partners').waypoint(function() {
		setTimeout(function(){$('#animIt16').addClass('fadeOutRight')},0);
		setTimeout(function(){$('#animIt17').addClass('fadeOutRight')},200);
		setTimeout(function(){$('#animIt18').addClass('fadeOutRight')},400);
		setTimeout(function(){$('#animIt19').addClass('fadeOutRight')},600);
		setTimeout(function(){$('#animIt20').addClass('fadeOutRight')},800);
	}, { offset: '100%' });


}



$.ajax({
  url: $("#myModal").attr("file-src")
}).done(function(data) {
  if(data) $("#myModal .modal-wr > div").html(data);
});


$(".modal-wr").bind("DOMSubtreeModified", function() {
	$(this).fixcenter();
});



$(".openform").click(function(event){
	var linkID = $(this).attr("href");
	$(linkID).css("opacity", "0").css("display", "block");
	$(linkID + " .modal-wr").fixcenter();
	$(linkID).css("display", "none").css("opacity", "1");
  	$(linkID).modal('show');
});



});