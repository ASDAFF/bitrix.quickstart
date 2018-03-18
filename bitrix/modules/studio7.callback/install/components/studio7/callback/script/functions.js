 // функция показывает «затемнение»
	function showOverlay()
		{
		  // определяем блок
		  var over = $('<div id="overlay"></div>');
		  over.appendTo('body');
			
		  // увеличиваем его размер до размеров окна и проявляем его
		  //over.height($('#all_wrap').height()+'px').width($('html').width()+'px').fadeIn(500);	    	   
		  over.height($(document).height()+'px').width($(document).width()+'px').fadeIn(500);	    	   
			  
		}
		
  //настраиваем позиции при скролле страницы
	function ajustScrollTop(obj)
		 {
		   // определяем высоту видимой части страницы
		   var clHght = document.documentElement.clientHeight;
		   // определяем высоту изображения
		   var clformh = $(obj).height();
		   // вычислям позицию верхнего левого угла блока с изображением
		   var posY = (clHght - clformh)/2+$(window).scrollTop();
		   // позиционируем блок
			 $(obj).css('top',posY).css('margin-top','0');
		 }