jQuery(document).ready(function($) {

 	  $.preloadCssImages();

	  //slider
	  $('#header_pane').toggle(function(){
	  
			  $(this).addClass('closed').animate({top: 0}, 300);
			  $('.header').slideUp(300);
			  $('#slide_info_container').animate({top: 32}, 300);
			  
		  }, function () {
		  
			  $(this).removeClass('closed').animate({top: 76}, 300);
			  $('.header').slideDown(300);
			  $('#slide_info_container').animate({top: 107}, 300);
	  });

	  
	  $('#slide_info_toggle').toggle(function(){
	  
			  $(this).addClass('closed');
			  $('#slidecaption, #slidedescription').hide();
			  
		  }, function () {
		  
			  $(this).removeClass('closed');
			  $('#slidecaption, #slidedescription').show();
	  });

	  
	  $(".header-info-close").click(function(){
			
			var that = this;			
			if(parseInt($(".header").css("top")) == 0){
					$(".header").stop(true, true).animate({"top":"-21px"}, 400, "swing");
					$(that).stop(true, true).animate({"height":"20px"}, 400, "swing", function(){$(that).html("i")});
					$(".middle.cols2_wide").stop(true, true).animate({"paddingTop":parseInt($(".middle.cols2_wide").css("paddingTop")) - 21 + "px"}, 400, "swing");				 
			}
			else{
					$(".header").stop(true, true).animate({"top":"0px"}, 400, "swing");	
					$(that).stop(true, true).animate({"height":"24px"}, 400, "swing", function(){$(that).html("<div>x</div>")});
					$(".middle.cols2_wide").stop(true, true).animate({"paddingTop":parseInt($(".middle.cols2_wide").css("paddingTop")) + 21 + "px"}, 400, "swing");				 					
			}
	  });	  
});