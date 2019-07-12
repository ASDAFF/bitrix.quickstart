//////////////////////////////////////////////////////  //
//    up button                                         //
//////////////////////////////////////////////////////  // 


(function($) {
	$(function() {

	$("#up").hide();

	$(window).scroll(function (){
		if ($(this).scrollTop() > 700){
			$("#up").fadeIn();
		} else{
			$("#up").fadeOut();
		}
	});

	  $('#up').click(function() {
		$('body,html').animate({scrollTop:0},500);
		return false;
	  })

	})
})(jQuery)
