jQuery(document).ready(function($) {

 	$.preloadCssImages();
	
    $('#header_pane').toggle(function(){
	
            $(this).addClass('closed').animate({top: 0}, 300);
            $('.header').slideUp(300);
            $('#slide_info_container').animate({top: 32}, 300);
			
        }, function () {
		
            $(this).removeClass('closed').animate({top: 96}, 300);
            $('.header').slideDown(300);
            $('#slide_info_container').animate({top: 130}, 300);
    });
    
    $('#slide_info_toggle').toggle(function(){
	
            $(this).addClass('closed');
            $('#slidecaption, #slidedescription').hide();
			
        }, function () {
		
            $(this).removeClass('closed');
            $('#slidecaption, #slidedescription').show();
    });

	
});