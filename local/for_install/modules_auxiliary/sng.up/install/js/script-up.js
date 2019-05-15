$(document).ready(function(){ 
	$("body").append("<a class='scrollup' href='#'><img src='"+sng_up_button+"' alt='Вверх'></a>");
	$('.scrollup').css("bottom",sng_up_position_indent_y+'px');	//отступ снизу		
	$('.scrollup').fadeTo(0, sng_up_button_opacity/100);
	if ($(this).scrollTop() > 100) {
        $('.scrollup').fadeIn();
    } else {
        $('.scrollup').fadeOut();
		$('.scrollup').css("display",'none');	
    } 	
	if(sng_up_position=='center'){
		$('.scrollup').css("left",50+'%');				
	}else if(sng_up_position=='left'){
		$('.scrollup').css("left",sng_up_position_indent_x+'px');				
	}else if(sng_up_position=='right'){
		$('.scrollup').css("right",sng_up_position_indent_x+'px');				
	}	
    $(window).scroll(function(){
		if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        } 
    }); 
    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 500);
        return false;
    }); 	
}); 	