$(document).ready(function(){
/*Анимация mainmenu*/
	$('#mainmenu a').not('#mainmenu ul ul a').mouseover(function(){
		$(this).closest('li').find('.active').not($(this).closest('li').children()).removeClass('.active');
		$(this).closest('li').children().addClass('active');
	});
	$('#mainmenu li').not('#mainmenu ul ul li').hover(function(){
	},function(){
		$(this).closest('li').children().removeClass('active');
	});

/*\\Анимация mainmenu*/
/*Поиск*/
	var initSearchFlag = false;
	var topSearchText;
	$('#topsearch .text').focusin(function(){
		if(!initSearchFlag){
			initSearchFlag = true;
			topSearchText = $(this).val();
			$(this).val('');	    
		};
	});
	$('#topsearch .text').focusout(function(){
		if($(this).val() == ''){
			$(this).val(topSearchText);
			initSearchFlag = false;
		};
	});
/*\\Поиск*/
/*Инициализация карусели*/
    $(function() {
        $(".carousel").jCarouselLite({
		btnNext: ".next",
		btnPrev: ".prev"
        });
    });
/*\Инициализация карусели*/
});
