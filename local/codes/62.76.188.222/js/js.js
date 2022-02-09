$(document).ready(function() {

        $('.b-catalog-sort .b-chosen__no-text').change(function(){
            location.href='?cnt=' + $(this).val();
        });

/* окно авторизации (begin) */
	$("#b-auth__login").fancybox({
		padding: 25,
		wrapCSS: "b-detail-image__big"
	});
/* окно авторизации (end) */

/* таблица сравнения фиксированный блок с товаром (begin) */
	var f, $fixed_block;
	
	if($(".b-compare-header").length) {
		f = $(".b-compare-header").offset();
		$fixed_block = $(".b-compare-header");
	}
	if($(".b-compare-added").length) {
		f = $(".b-compare-added").offset();
		$fixed_block = $(".b-compare-added");
	}
		
	$(window).scroll(function(){  
		if(typeof $fixed_block !== "undefined") {
			if($(this).scrollTop() >= f.top){
				$fixed_block.addClass("m-fixed");
			}
			else{
				$fixed_block.removeClass("m-fixed");
			}
		}
	});
/* таблица сравнения фиксированный блок с товаром (end) */

/* таблица сравнения показать/скрыть различие (begin) */
	if($("#m-changes__show input[type='checkbox']").is(":checked"))
		$(".b-compare-body").addClass("m-changes__show");
	else
		$(".b-compare-body").removeClass("m-changes__show");
		
	$("#m-changes__show input[type='checkbox']").change(function() {
		$(".b-compare-body").toggleClass("m-changes__show");
		$("#b-compare__table").height($("#b-compare__table").children().height());
	});
/* таблица сравнения показать/скрыть различие (end) */


/* главное меню (begin) */
	$(".b-menu__item").hover(
		function() {
			$(this).addClass("b-menu__active");
		},
		function() {
			$(this).removeClass("b-menu__active");
			$(".b-level2__item").removeClass("b-level2__selected");
			$(".b-level2__line").removeClass("b-level2__active");
			$(".b-menu-level3").removeClass("selected");
		}
	);
	$(".b-level2__item").click(function() {
		if($(this).hasClass("b-level2__has-child")) {
			var href = $(this).attr("href");
			if($(this).hasClass("b-level2__selected") == false) {
				$(".b-level2__item").removeClass("b-level2__selected");
				$(this).addClass("b-level2__selected");
				
				$(".b-level2__line").removeClass("b-level2__active");
				$(this).parent().addClass("b-level2__active");
				
				$(".b-menu-level3").removeClass("selected");
				$(href).addClass("selected");
			}
			else {
				$(this).removeClass("b-level2__selected");
				$(this).parent().removeClass("b-level2__active");
				$(href).removeClass("selected");
			}
			return false;
		}
	});
/* главное меню (end) */

/* кнопки сортировки (begin) */
	$(".b-catalog-sort__link").click(function() {
		if($(this).hasClass("b-catalog-sort__active")) {
			$(this).toggleClass("m-catalog-sort__up");
		}
		else {
			$(".b-catalog-sort__link").removeClass("b-catalog-sort__active").removeClass("m-catalog-sort__up");
			$(this).addClass("b-catalog-sort__active");
		}
		//return false;
	});
/* кнопки сортировки (end) */

/* chosen без текстового поля (begin) */
	if($(".b-chosen__no-text").length) {
		$(".b-chosen__no-text").chosen();
	}
/* chosen без текстового поля (end) */

/* переключатели вида в каталоге таблица/список с картинками/список (begin) */
	$(".b-catalog-sort__link-list").click(function() {
		if($(this).hasClass("active") == false) {
			$(".b-catalog-sort__link-list").removeClass("active");
			$(this).addClass("active");
		}
		//return false;
	});
/* переключатели вида в каталоге таблица/список с картинками/список (end) */

/* устанавливаем одинаковою высоту названия в каталоге (begin) */
	/* подругому не было времени думать 
	$(".b-catalog-list__line").each(function() {
		var $item = $(this).find(".b-catalog-list_item");
		
		var max_h = 0;
		$item.find(".b-catalog-list_item__name").each(function() {
			if($(this).height() > max_h)
				max_h = $(this).height();
		});
		$item.find(".b-catalog-list_item__name").each(function() {
			$(this).height(max_h);
		});
	});
/* устанавливаем одинаковою высоту названия в каталоге (end) */

/* input radio & checkbox (begin) */

      function setRadio(){
	$(".b-radio, .b-checkbox").each(function() {
		var radio = $(this).find("input"),
			name = radio.attr("name");
			
		$(this).addClass("m-" + name);
		
		if(radio.is(":checked")) {
			$(this).addClass("b-checked");
		} 
	});
      }
        setRadio();
        
	$(".b-radio input[type='radio']").live('change',function() {setRadio();
            
		var name = $(this).attr("name");
		$(".m-" + name).removeClass("b-checked");
		$(this).parent().addClass("b-checked");
	});
	$(".b-checkbox input[type='checkbox']").change(function() {
		var name = $(this).attr("name");
		$(this).parent().toggleClass("b-checked");
	});
/* input radio & checkbox (end) */

/* каталог левое меню (begin) */
	$(".b-sidebar-menu__link").click(function() {
		var $parent = $(this).parent(),
			$submenu = $parent.find(".b-sidebar-submenu");
		
		if($submenu.length) {
			if($parent.hasClass("selected")) {
				$parent.removeClass("selected");
			}
			else {
				$(".b-sidebar-menu__item").removeClass("selected");
				$parent.addClass("selected");
			}
			return false;
		}
	});
/* каталог левое меню (end) */

 
/* детальная страница: переключение фото (begin) */
	$(".b-detail-photo_list__link").click(function() {
		var $parent = $(this).parent();
		
		if($parent.hasClass("active") == false) {
			$("#DETAIL_IMAGE_BIG").height($("#DETAIL_IMAGE_BIG").height()).attr("src", "");
		
			$(".b-detail-photo_list__item").removeClass("active");
			$parent.addClass("active");
			
			var href = $(this).attr("href");
			
			$("#DETAIL_IMAGE_BIG").attr("src", href);
			$("#DETAIL_ZOOM_PHOTO").attr("href", href);
			$("#DETAIL_IMAGE_BIG").load(function() {
				$(this).height("auto");
			});
		}
		return false;
	});
	$("#DETAIL_ZOOM_PHOTO").fancybox({
		padding: 25,
		wrapCSS: "b-detail-image__big"
	});
	$("#DETAIL_ZOOM").click(function() {
		$("#DETAIL_ZOOM_PHOTO").click();
		return false;
	});
/* детальная страница: переключение фото (end) */

/* меню второго и третьего уровня детальный простотр (begin) */
	$(".b-section-menu__link").click(function() {	
		var $parent = $(this).parent(),
			$parent_width = $parent.outerWidth();
		
		if($parent.hasClass("active")) {
			$parent.removeClass("active");
		}
		else {
			$(".b-section-menu").removeClass("active");
			$parent.find(".b-section-popup").width($parent_width - 40);
			$parent.addClass("active");
		}
		return false;
	});
	
	$(document).bind("click.myEvent", function (e) {
		if ($(e.target).closest(".b-section-popup").length == 0) {
			$(".b-section-menu").removeClass("active");
		}
	});
/* меню второго и третьего уровня детальный простотр (end) */

/* кнопки увеличение и уменьщение товара в корзине (begin) */
	$(".b-basket-item-count__text").numOnly();
	$(".b-basket-item-count__btn").click(function() {
		var inc = true, 
			id = $(this).data("id"),
			val = $("#text-item_" + id).val();
			
		if($(this).hasClass("m-dec"))
			inc = false;
			
		if(inc )
			val++;
		else if(val > 0)
			val--;
			
		$("#text-item_" + id).val(val);
	});
/* кнопки увеличение и уменьщение товара в корзине (end) */

/* IE8 bug fix (begin) */
	if($.browser.msie && $.browser.version < 9) {
		//$(".b-catalog-list_item:nth-child(3n)").addClass("nth-child-3n");
		$(".b-catalog-table tbody tr:nth-child(even)").addClass("b-catalog-table-tr__even");
	}
/* IE8 bug fix (end) */




$('.b-sidebar-filter-caption').bind('click', function(){
    $(this).next().toggle(); 
}); 
 

    $('.buy_').bind('click', function(){
   
        if(!$(this).hasClass('m-in_basket')){
            id = $(this).data('id');
            $.ajax({
                url: '/inc/basket_line.php',
                data: 'action=ADD2BASKET&id=' + id,
                success: function(data){
                    $('.b-minicart').html(data);
                }
            });
 
            $(this).addClass('m-in_basket')
            .html('<span class="b-catalog-list_item__cart">добавлен<br>в корзину</span>');
         }  else {
             
             location.href="/personal/cart/";
             
         }
    });  


    $('.add2compare_').live('click', function(){
        
        if(!$(this).hasClass('m-compare__added')){
            
            $(this).addClass('m-compare__added')
            .find('span').text('Добавлен к сравнению');
 
                if($('.b-compare-added').hasClass('hidden_')){
                   $('.b-compare-added').addClass('clearfix').removeClass('hidden_');
                }
  
                var id = $(this).data('id');
                $.ajax({
                    url: '/api/?action=add2compare_&id=' + id,
                    success: function(data){
                        $('.b-compare-added').html(data);      
                    }
                });
        }
        return false;
    }); 
  
  

    $('.compare_from_list_').live('click', function(){
  
                if($('.b-compare-added').hasClass('hidden_')){
                   $('.b-compare-added').addClass('clearfix').removeClass('hidden_');
                }
  
                var id = $(this).data('id');
                $.ajax({
                    url: '/api/?action=add2compare_&id=' + id,
                    success: function(data){
                        $('.b-compare-added').html(data);      
                    }
                });
  
        return false;
    }); 
  
  
  
  
  
        $('.compare_').live('click', function(){
            location.href = "/catalog/compare/";
            
        });
 

});

$.fn.numOnly = function() {
    return this.each(function() {
        $(this).keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            // Разрешаем backspace, tab, delete, стрелки, обычные цифры и цифры на дополнительной клавиатуре
            return (
                key == 8 ||
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};  