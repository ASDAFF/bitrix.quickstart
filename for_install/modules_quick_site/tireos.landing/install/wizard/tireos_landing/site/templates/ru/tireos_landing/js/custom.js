$(document).ready(function() {
"use strict";
//---------------------------------------------------------
// === placeholder ===
//---------------------------------------------------------

if(!Modernizr.input.placeholder){
	$('[placeholder]').focus(function() {var input = $(this);if (input.val() == input.attr('placeholder')) {input.val('');input.removeClass('placeholder');}}).blur(function() {var input = $(this);if (input.val() == '' || input.val() == input.attr('placeholder')) {input.addClass('placeholder');input.val(input.attr('placeholder'));}}).blur();$('[placeholder]').parents('form').submit(function() {$(this).find('[placeholder]').each(function() {var input = $(this);if (input.val() == input.attr('placeholder')) {input.val('');}})});
};

//---------------------------------------------------------
// === other class ===
//---------------------------------------------------------

$('ul li:last-child').addClass('lastItem');
$('ul li:first-child').addClass('firstItem');
$('#gallery .span6:eq(-1), #gallery .span6:eq(-2)').addClass('mar-clear');


//---------------------------------------------------------
// === change elements ===
//---------------------------------------------------------

// message is sent
$('.complete').appendTo('body');
setTimeout(function() {
	$('.complete').animate({ 'opacity': 0 }, 700);
}, 10000)
$('.complete').hover(function() {
	$(this).animate({ 'opacity': 'hide' }, 350);
})


// mob menu
$('.mob-ver-menu').click(function() {
	$(this).toggleClass('active')
	$('.trig-mob ul').slideToggle();
});


// random captcha
var a = (10 - 0.5 + Math.random() * (300-10+1)).toFixed();
var b = (5 - 0.5 + Math.random() * (40-5+1)).toFixed();
var result = +a + +b;

$('#numb1').html(a);
$('#numb2').html(b);
$('#chek').attr("data-pattern", result);
$('input[name = resultCaptcha]').val(result);





// ---------------------------------------------------------
// === options ===
// ---------------------------------------------------------




// **************
// Value
// **************
$("[data-default]").focus(function() {
if ( this.value == this.getAttribute( 'data-default' ) ) {
	this.value = "";
}
}).blur(function() {
if ( this.value == "" ) {
	this.value = this.getAttribute( 'data-default' );
}
}).blur();


// **************
// Back to Top
// **************
jQuery(window).scroll(function () {
	if (jQuery(this).scrollTop() > 750) {
		jQuery('#back-top').removeClass('bounceOut').addClass('bounceIn');
	} else {
		jQuery('#back-top').removeClass('bounceIn').addClass('bounceOut');
	}
});
jQuery('#back-top').click(function () {
	jQuery('body,html').stop(false, false).animate({
		scrollTop: 0
	}, 900);
	return false;
});


// **************
// ScrollAnchor
// **************
$('[data-scroll]').on('click', function() {
	var scrollAnchor = $(this).attr('data-scroll'),
		scrollPoint = $('[data-anchor="' + scrollAnchor + '"]').offset().top - 30;
	$('body,html').animate({
		scrollTop: scrollPoint
	}, 500);
	return false;
});





// **************
// Preloader
// **************
$(window).load(function() {
	$('#status').delay(100).fadeOut('slow');
	$('#preloader').delay(500).fadeOut('slow');
	$('body').delay(500).css({'overflow':'visible'});
});




// ********************************
// Request and contact Form
// ********************************
$("#formIndex, #contact").submit(function() {
		var elem = $(this);
		var urlTarget = $(this).attr("action");
		$.ajax({
				type : "POST",
				url : urlTarget,
				dataType : "html",
				data : $(this).serialize(),
				beforeSend : function() {
					elem.prepend("<div class='loading alert'>" + "<a class='close' data-dismiss='alert'>?</a>" + "Loading" + "</div>");
				},
				success : function(response) {
					elem.prepend(response);
					elem.find(".loading").hide();
					elem.find("input[type='text'],input[type='email'],textarea").val("");
				}
		});
		return false;
});










});

