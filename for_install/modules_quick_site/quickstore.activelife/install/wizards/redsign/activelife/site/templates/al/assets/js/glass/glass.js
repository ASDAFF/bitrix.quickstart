var GLASS_magnify;
var $GLASS_glass;

var GLASS_uip = {
	fadeDelay		: 200,
	native_width	: 0,
	native_height	: 0,
	mouse_x			: 0,
	mouse_y			: 0,
	lupa			: ''
};

// Определяем положение курсора
var GLASS_mouseMove = function(e) 
{
	// Получаем отступы до края картинки слева и сверху
	var GLASS_magnify_offset = GLASS_uip.cur_img.offset();
	GLASS_uip.mouse_x = e.pageX - GLASS_magnify_offset.left;
	GLASS_uip.mouse_y = e.pageY - GLASS_magnify_offset.top;

	if(GLASS_uip.mouse_x < GLASS_uip.cur_img.width() && GLASS_uip.mouse_y < GLASS_uip.cur_img.height() && GLASS_uip.mouse_x > 0 && GLASS_uip.mouse_y > 0)
	{ // Если условие истинно переходим дальше
		GLASS_magnify(e);
	} else { // иначе скрываем
		GLASS_uip.lupa.hide();
	}
	return;
};

var GLASS_magnify = function(e) 
{
	var rx = Math.round(GLASS_uip.mouse_x/GLASS_uip.cur_img.width()*GLASS_uip.native_width -GLASS_uip.lupa.width()/2)*-1;
	var ry = Math.round(GLASS_uip.mouse_y/GLASS_uip.cur_img.height()*GLASS_uip.native_height - GLASS_uip.lupa.height()/2)*-1;
	var bg_pos = rx + 'px ' + ry + 'px';
	var glasspop_top  = e.pageY - GLASS_uip.cur_img.offset().top - GLASS_uip.lupa.height()/2;
	var glasspop_left = e.pageX - GLASS_uip.cur_img.offset().left - GLASS_uip.lupa.width()/2;
	
	// Теперь присваиваем полученные значения css свойствам лупы
	GLASS_uip.lupa.css({
		left: glasspop_left,
		top: glasspop_top,
		backgroundPosition: bg_pos
	});
	return;
};

$(document).ready(function(){
	
	if(RSAL_PHONETABLET!="Y")
	{
		// hide glass on click
		$(document).on('click', '.glass_lupa', function(){
			$GLASS_glass = $(this).closest('.glass');
			$GLASS_glass.find('.glass_lupa').removeClass('active').fadeOut(GLASS_uip.fadeDelay);
			$GLASS_glass.find('.js_picture_glass').unbind('mousemove');
		});
		
		// hide/show glass when mouseleave
		$(document).on('mouseleave', '.glass', function(){
			if (GLASS_uip.lupa.length)
			{
				GLASS_uip.lupa.fadeOut(GLASS_uip.fadeDelay);
			}
		});
		
		// show glass on click
    $(document).on('mouseenter', '.bx-no-touch .js_picture_glass', function(e){
			$GLASS_glass = $(this).closest('.glass');
			GLASS_uip.lupa = $GLASS_glass.find('.glass_lupa');
			GLASS_uip.lupa.addClass('active');

			// Движение курсора над изображению
			$(this).on('mousemove', function(){
				GLASS_uip.cur_img = $(this).find('img'); // Текущее изображение
				GLASS_uip.lupa.fadeIn(GLASS_uip.fadeDelay); // Плавное появление лупы
				var src = GLASS_uip.cur_img.data('large'); // определяем путь до картинки
				if (src) { // Если существует src, устанавливаем фон для лупы
					GLASS_uip.lupa.css({
						'background-image': 'url(' + src + ')',
						'background-repeat': 'no-repeat'
					});
				}
				
				if (!GLASS_uip.cur_img.data('GLASS_native_width') || GLASS_uip.cur_img.data('src') != src) {
					var image_object = new Image();
					image_object.onload = function(){
						// определяем реальные размеры картинки
						GLASS_uip.native_width = image_object.width;
						GLASS_uip.native_height = image_object.height;

						// Записываем эти данные
						GLASS_uip.cur_img.data('GLASS_native_width', GLASS_uip.native_width);
						GLASS_uip.cur_img.data('GLASS_native_height', GLASS_uip.native_height);
						GLASS_uip.cur_img.data('src',src);

						// Вызываем функцию mouseMove и происходит показ лупы 
						GLASS_mouseMove.apply(this, arguments);
						GLASS_uip.lupa.on('mousemove', GLASS_mouseMove);
					};
					image_object.src = src;
					return;
				} else {
					// получаем реальные размеры изображения  
					GLASS_uip.native_width = GLASS_uip.cur_img.data('GLASS_native_width');
					GLASS_uip.native_height = GLASS_uip.cur_img.data('GLASS_native_height');
				}
				// Вызываем функцию mouseMove и происходит показ лупы
				GLASS_mouseMove.apply(this, arguments);
				GLASS_uip.lupa.on('mousemove', GLASS_mouseMove);
			}); 
		});
	}
	
});