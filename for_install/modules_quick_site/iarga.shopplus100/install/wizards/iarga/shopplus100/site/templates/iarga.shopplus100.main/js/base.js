
jQuery.fn.putBefore = function(dest){
   return this.each(function(){
	  $(dest).before($(this));
   });
}
jQuery.fn.putAfter = function(dest){
   return this.each(function(){
	  $(dest).after($(this));
   });
}

// Checkbox
$.fn.newCheckbox = function(){
	if($.browser.msie && $.browser.version == 7) {
		return false;
	} else {
		$(this).each(function(){
			var $this = $(this);
			$this.wrap('<i class="checkbox"/>');
			if($this.is(':checked')){
				$this.parent('.checkbox').addClass('checked');
			}
		});
	}	
	$(this).on('change', function(){
		var $this = $(this);
		$(this).parent().toggleClass('checked');
	});
};
	

var ajaxItem = function(){
	
	var thisParent = $(this).closest('.item');
	
	thisParent.addClass('disclosed');
	
	thisParent.append('<i class="hide-icon"/>');
	
	$('.hide-icon', thisParent).hide().fadeIn(300);
		
	$('.title-container', thisParent).show(300);
	
	$('.description-extended', thisParent).show(300);
	
	$('.description-preview', thisParent).hide(300);
	
	thisParent.animate({'padding-right':'0'}, 300);
	
	$('.sortable-control', thisParent).animate({'width':'hide', 'opacity':'hide'}, 300);
	
	$('.manipulation', thisParent).css({'float':'none', 'width':'auto', 'overflow':'hidden', 'padding-left':'0'});
	
	$('.availability', thisParent).putAfter(thisParent.find('.manipulation'));
	
	var windowWidth = $(window).width();
	
	if(windowWidth <= 760){
		
		$('.img', thisParent).animate({'width':'100%'}, 300, function(){
			$(this).removeAttr('style').addClass('open');
		}).css('height','auto');
		
	} else {

		$('.img', thisParent).animate({'width':'60%'}, 300, function(){
			$(this).removeAttr('style').addClass('open');
		}).css('height','auto');	

	}
	
	$('.img .preview', thisParent).fadeOut(300, function(){
		
		var sliderInstance = $('.royalSlider', thisParent).royalSlider({
			fullscreen: {
			  enabled: true,
			  nativeFS: false
			},
			controlNavigation: false,
			autoScaleSlider: true, 
			autoScaleSliderWidth: 960,     
			autoScaleSliderHeight: 650,
			loop: false,
			imageScaleMode: 'fit-if-smaller',
			navigateByClick: true,
			numImagesToPreload:3,
			arrowsNavAutoHide: false,
			arrowsNavHideOnTouch: false,
			keyboardNavEnabled: false,
			fadeinLoadedSlide: true,
			globalCaptionInside: false,
			imageScalePadding: 12
		}).data("royalSlider");	
		
		$('.img .royalSlider', thisParent).fadeIn(300, function(){
			sliderInstance.updateSliderSize();
		});
		
	});
	
	return false;

}


$(document).ready(function() {
	
	$('input:checkbox').newCheckbox();
	
	// Информационный блок знаний
	var experience = $('.experience');
	experience.append('<i class="close"/>');
	$('.close', experience).on('click', function(){
		$(this).closest('.experience').hide(300, function(){
			$(this).remove();
		});
	});
	
	
	// Добавляем колонкам классы, отражающие положение среди других колонок
	$('.column-wrap .box').each(function(){
		var index = $(this).index()+1;
		$(this).addClass('eq'+index);
	});
	
	
	// Получание реальных размеров картинки
	// берем все необходимые нам картинки
	var $img = $('img');
	// ждем загрузки картинки браузером
	$img.load(function(){
		// удаляем атрибуты width и height
		$(this).removeAttr("width")
			   .removeAttr("height")
			   .css({ position: "relative", width: "auto", height: "auto" });
	 
		// получаем заветные цифры
		var width  = $(this).width();
		var height = $(this).height();
		$(this).removeAttr('style').css('max-width', width + 'px');
	});
	// для тех браузеров, которые подгрузку с кеша не считают загрузкой, пишем следующий код
	$img.each(function() {
		var src  = $(this).attr('src');
	    $(this).attr('src', '');
        $(this).attr('src', src);
	});


	// Сортировка товаров на странице "избранное"
	if($('#sortable').length > 0 || $('.slider-widget-container').length > 0){
		function sorteach(){
			$('#sortable .ui-state-default .sortable-control a').removeClass('hidden');
			$('#sortable .ui-state-default:first').find('.bg_t').addClass('hidden');
			$('#sortable .ui-state-default:last').find('.bg_b').addClass('hidden');
		}
		sorteach();
		
		var clickSortable = function(){
			
			var item = $(this).closest('.ui-state-default');
			
			if($(this).hasClass('bg_t')){
				item.putBefore(item.prev());
			}

			if($(this).hasClass('bg_b')){
				item.putAfter(item.next());
			}
			
			sorteach();
			
			return false;
			
		}
		
		$('#sortable').on('click', '.sortable-control a:not(.hidden)', clickSortable);
		
		$('#sortable').on('click', '.sortable-control a.hidden', function(){
			return false;
		});
		
		$("#sortable").sortable({
			axis: "y",
			handle:'.sortable-control',
			opacity:0.9,
			cursor:"n-resize",
			update: function(event, ui) {
				sorteach();
			},
			start: function(event, ui) {
				$('#sortable').off('click', '.sortable-control a:not(.hidden)');
			}, 
			stop: function(event, ui) {
				setTimeout(function() { $('#sortable').on('click', '.sortable-control a:not(.hidden)', clickSortable); }, 10);
			}
		});
	}
	
	
	
	
	// Показываем скрытые характеристики товара
	$(document).on('click', '.features .show-link', function(){
		if($(this).hasClass('active')){
			$(this).closest('.features').find('.hide .ellipsis').show(300);
			$(this).text($(this).attr("data-show")).removeClass('active').closest('.features').find('.hide div').hide(300);
		} else {
			$(this).closest('.features').find('.hide .ellipsis').hide(300);
			$(this).text($(this).attr("data-hide")).addClass('active').closest('.features').find('.hide div').show(300);			
		}
		return false;
	});
	
	
	// Раскрытие инфы .availability
	$(document).on('click', '.availability ul li.selected a', function(){
		var $this = $(this);
		if($this.hasClass('active')){
			$this.removeClass('active').closest('ul').find('li:not(.selected)').hide(300);
		} else {
			$this.addClass('active').closest('ul').find('li:not(.selected)').not($this).show(300);
		}
		return false;
	});
	$(document).on('click', '.availability ul li:not(.selected) a', function(){
		var $this = $(this),
			$thisText = $this.text(),
			$thisTextNext = $this.closest('ul').find('.selected a').text();
			$this.closest('ul').find('.selected a').text($thisText);
			$this.text($thisTextNext);
		return false;
	});


	// Увеличение/Уменьшение количества товаров
	$(document).on('click', '.select-number .plus', function(){
		var inp = $(this).siblings('input:text');
		var curNumbInp = parseInt(inp.val());
		curNumbInp++;
		inp.val(curNumbInp + ' шт.');
		return false;
	});
	$(document).on('click', '.select-number .minus', function(){
		var inp = $(this).siblings('input:text');
		var curNumbInp = parseInt(inp.val());
		if(curNumbInp > 1){
			curNumbInp--;
			inp.val(curNumbInp + ' шт.');
		}
		return false;
	});
	
	
	// Виджеты значений
	if($('.slider-widget-container').length > 0){

		$('.slider-widget').prepend('<div class="bg_l"></div><div class="bg_r"></div>')	
		
		$(".slider-widget").each(function() {
			
			$(this).slider({
				min: eval($(this).find(".minValue").val()), // Минимально допустимое знаечение для виджета
				max: eval($(this).find(".maxValue").val()), // Максимально допустимое знаечение для виджета
				animate: 150,
				step: parseInt($(this).find('.step').val()), // Значение шага для виджета
				values: [$(this).closest('.slider-widget-input').find('.minVal').val(),$(this).closest('.slider-widget-input').find('.maxVal').val()], 
				// Значения min и max, которые мы показываем на слайдере при загрузке страницы
				range: true,
				slide: function(event, ui){
					$(this).closest('.slider-widget-input').find(".minVal").val(ui.values[0]).keyup();
					$(this).closest('.slider-widget-input').find(".maxVal").val(ui.values[1]).keyup();
				}, 
				create: function(event, ui){
					$(this).closest('.slider-widget-input').find('.minVal').val($(this).slider('values',0));
					$(this).closest('.slider-widget-input').find('.maxVal').val($(this).slider('values',1));
				},
				stop: function(event, ui){
					if($(this).closest('.filter-extended').length){
						clearTimeout(timerId);
						$('.filter-extended .hint').appendTo($(this).closest('.slider-widget-input')).css({
							'margin':'0 -5px 0 0',
							'right':'100%',
							'top':'6px'	
						}).stop().fadeIn(300);
						timerId = setTimeout(function() {
							$('.filter-extended .hint').fadeOut(300);
						}, 3000);
					}
				}
			});
			
		});
		

		$('.maxVal').change(function(){
			var minValue=$(this).parents('.slider-widget-container').find('.minValue').val()
			var maxValue=$(this).parents('.slider-widget-container').find('.maxValue').val()
			var minVal=$(this).parents('.slider-widget-input').find(".minVal").val();
			var maxVal=$(this).parents('.slider-widget-input').find(".maxVal").val();
			if(parseInt(minVal) > parseInt(maxVal)){
				maxVal = minVal;
				$(this).parents('.slider-widget-input').find(".maxVal").val(maxVal);
			}
			
			if(parseInt(maxVal) > parseInt(maxValue)) {
				$(this).parents('.slider-widget-input').find('.maxVal').val(maxValue);
			}
			
			$(this).closest('.slider-widget-input').find('.slider-widget').slider("values", 1, maxVal);
		});
		
		$('.minVal').change(function(){
			var minValue = $(this).parents('.slider-widget-container').find('.minValue').val()
			var maxValue = $(this).parents('.slider-widget-container').find('.maxValue').val()
			var minVal = $(this).parents('.slider-widget-input').find(".minVal").val();
			var maxVal = $(this).parents('.slider-widget-input').find(".maxVal").val();
		
			if(parseInt(minVal) > parseInt(maxVal)){
				minVal = maxVal;
				$(this).parents('.slider-widget-input').find('.minVal').val(minVal);
			}
			
			if(parseInt(minVal) < parseInt(minValue)) {
				$(this).parents('.slider-widget-input').find('.minVal').val(minValue);
			}
			
			$(this).closest('.slider-widget-input').find('.slider-widget').slider("values", 0, minVal);	
		});
			

	}
	
	
	// Lightbox
	if($('.lightbox').length > 0){
		
		$('body').append('<div class="lightbox-container"></div><div class="lightbox-bg"></div>');
		
		$('.lightbox-bg').fadeTo(1, 0.5, function(){
			$(this).hide().css('visibility','visible');
		});
		
		$('.lightbox').each(function(){
			$(this).append('<a href="#" class="close"></a>').appendTo('.lightbox-container');
		});
		
		$(document).on('click', '.open-lightbox', function(){
			
			$('.lightbox-container, .lightbox-bg').fadeIn(500);
			
			$('.lightbox' + $(this).attr('href')).fadeIn(500);
			
			$('body').css('overflow','hidden');
			
			return false;
			
		});
		
		$(document).on('click', '.lightbox .close', function(){
			
			$(this).closest('.lightbox').fadeOut(500);
			
			$('.lightbox-container, .lightbox-bg').fadeOut(500);
			
			$('body').css('overflow','visible');
			
			return false;
			
		});
		
		$(document).on('click', function(event){
			
			if ($(event.target).closest(".lightbox-container").length) {
				$(this).siblings('.lightbox').fadeOut(500);
				
				$('.lightbox-container, .lightbox-bg').fadeOut(500);
				
				$('body').css('overflow','visible');
			}
		});
		
		$('.lightbox').on('click', function(event){
			event.stopPropagation(); 
		}); 
		
	}
	
	$('.bt_card').append('<i/>');
	
	
	// Расширенный фильтр
	$('.filter .link-open').on('click', function(){
		$('.filter').addClass('using');
		$(this).toggleClass('active');
		$('.filter-extended').slideToggle(500);
	});
	$('.filter-extended .link-open').on('click', function(){
		$('.filter .link-open').click();
	});
	$('.link-all-manufacturers').on('click', function(){
		if(!$(this).hasClass('active')){
			$('span:first', this).hide();
			$('span:last', this).show();
			$(this).addClass('active').closest('li').find('.all-manufacturers').slideDown(300);
		} else {
			$('span:last', this).hide();
			$('span:first', this).show();
			$(this).removeClass('active').closest('li').find('.all-manufacturers').slideUp(300);
		}
		return false;
	});
	function filterPosition(){
		if($('aside').width() != '272' && !$('.filter').hasClass('using')) {
			$('aside .filter-extended').hide();
			$('aside .filter .link-open').removeClass('active');
		} else if ($('aside').width() == '272' && !$('.filter').hasClass('using')) {
			$('aside .filter-extended').show();
			$('aside .filter .link-open').addClass('active');
		}
	}
	filterPosition();
	$(window).on('resize load', function(){
		filterPosition();
	});
	var timerId;
	$('.filter-extended label input:checkbox').change(function(){
		var $this = $(this);
		clearTimeout(timerId);
		$('.filter-extended .hint').appendTo($this.closest('li')).css({
			'margin':'-13px 9px 0 0',
			'right':'100%',
			'top':'50%'	
		}).stop().fadeIn(300);
		timerId = setTimeout(function() {
			$('.filter-extended .hint').fadeOut(300);
		}, 3000);
	});
	$('.range-widget .inp-text').change(function(){
		clearTimeout(timerId);
		$('.filter-extended .hint').appendTo($(this).closest('.slider-widget-input')).css({
			'margin':'0 -5px 0 0',
			'right':'100%',
			'top':'6px'	
		}).stop().fadeIn(300).delay(3000).fadeOut(300);
		timerId = setTimeout(function() {
			$('.filter-extended .hint').fadeOut(300);
		}, 3000);
	});

	
});