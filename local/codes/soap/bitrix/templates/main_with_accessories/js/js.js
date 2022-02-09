$.fn.popup = function(options) {
	var options = $.extend({
			popup_data: "href" // цвет шрифта при hover
		}, options);

    return this.each(function() {
        var popup = {
				obj:	$(this).attr(options.popup_data),
				top:	$(this).offset().top + $(this).height() + 12,
				left:	$(this).offset().left + ($(this).width() / 2) - 40
			},
			popup_window = $(popup.obj);
		
		$(document).bind("click", function (e) {
			if ($(e.target).closest(".b-popup").length == 0) {
				$(".b-popup").removeClass("active");
			}
		});
		
		$(this).click(function() {
			if(popup_window.hasClass("active") == false)
				$(".b-popup").filter(".active").removeClass("active");
		
			if(popup_window.length) {
				popup_window.css({
					left: popup.left,
					top: popup.top
				});
			}
			else {
				// вывод ошибки если не нашли окно
				console.log("Error! Can't find popup window " + popup.obj + ". File js.js");
			}
			
			popup_window.toggleClass("active");
			
			return false;
		});
    });
};

$(document).ready(function() {
	$("a[href='#b-wishlist__add']").click(function(){
		$("#wishlist_add_el").attr('el',$(this).attr('el'))
	})
    function wishlist_element_add(object){
        var ID = object.attr('el')
        var category = $('#cat_list').val()
		var name = $('.b-cart-field__input').val()
        var button = object
        $.ajax({
            type: "POST",
            url: "/includes/ajax/wishlist/add_element.php",
            data: ({
                    element : ID,
                    cat : category,
                    name: name
                    }),
            success: function(html){
		console.log(html)
            }
        })
    }
    $("#wishlist_add_el").click(function(){
        var button = $(this)
        if(button.attr('el')){
            wishlist_element_add(button)
        }
        
    }) 

/* переключение табов (begin) */
	$(".b-tab-head__link").click(function() {
		// логика простая
		// у ссылки берется href, в ней лежит id div'а 
		// если существует такой div то он показывается 
		// иначе ссылка просто работает как сслыка
		// использовать можно где угодно главное у ссылки 
		// должен быть класс b-tab-head__link а у div'а 
		// класс b-tab__body и оба и у обоих совпадал класс active
		var href = $(this).attr("href");
		
		if($(href).length) {
			$(".b-tab-head__link").removeClass("active");
			$(this).addClass("active");
			
			$(".b-tab__body").removeClass("active");
			$(href).addClass("active");
			return false;
		}	
	});
/* переключение табов (end) */
/* кнопка Войти (begin) */	
	$(".m-wishlist__add").popup();
	$(".m-login__link").popup();
	$(".m-user__auth").popup();
	$(".m-compare__add").popup();
/* кнопка Войти (end) */

/* он лайн консультант  (begin) */
	var $chat = $(".b-chat"),
		chat_width = $chat.width();
	$(".b-chat-show__btn").click(function() {
		$chat.animate({
			right: -chat_width
		}, "fast", function() {
			// прячем кнопку открытия
			$(".b-chat-show").hide(); 
			// показываем окно чата
			$(".b-chat-window").show();
			
			$(this).animate({
				right: 0
			}, "fast");
		});
		
		return false;
	});
	$(".b-chat-hide__btn").click(function() {
		$chat.animate({
			right: -chat_width
		}, "fast", function() {
			// прячем кнопку открытия
			$(".b-chat-show").show(); 
			// показываем окно чата
			$(".b-chat-window").hide();
			
			$(this).animate({
				right: 0
			}, "fast");
		});
		return false;
	});
/* он лайн консультант  (end) */

/* каталожное меню по клику (begin) */
	$("#b-slide__aside").click(function() {
		var h = $("#b-slidedown").height() + 20;
		
		if($(this).parent().hasClass("active")) {
			$(".b-sidebar").css({
				marginTop: 0,
				borderTop: "1px solid #dadada"
			});
		}
		else {
			$(".b-sidebar").css({
				marginTop: h,
				borderTop: "0 none"
			});
		}
	});
	$(".b-nav-category__link").click(function() {
		$(this).parent().toggleClass("active");
		return false;
	});
	$(".m-menu").hover(
		function() {},
		function() {
			$(this).removeClass("active");
			$(".b-sidebar").css("margin", 0);
		}
	);
/* каталожное меню по клику (end) */

/* таблица сравнения показать/скрыть различие (begin) */
	if($("#m-changes__show input[type='checkbox']").is(":checked"))
		$(".b-compare-wrapper").addClass("m-changes__show");
	else
		$(".b-compare-wrapper").removeClass("m-changes__show");
		
	$("#m-changes__show input[type='checkbox']").change(function() {
		$(".b-compare-wrapper").toggleClass("m-changes__show");
	});
/* таблица сравнения показать/скрыть различие (end) */

/* скролящийся топ (begin) */
	/* функция кроссбраузерного определения отступа от верха документа к текущей позиции скроллера прокрутки */
	function getScrollTop() {
		var scrOfY = 0;
		if(typeof(window.pageYOffset) == "number") {
			//Netscape compliant
			scrOfY = window.pageYOffset;
		} 
		else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
			//DOM compliant
			scrOfY = document.body.scrollTop;
		} 
		else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
			//IE6 Strict
			scrOfY = document.documentElement.scrollTop;
		}
		return scrOfY;
	}
	/* пересчитываем отступ при прокрутке экрана */
	$(window).scroll(function() {
		fixPaneRefresh();
	});
	
	function fixPaneRefresh(){
		if ($("#b-fixed").length) {
			var top = getScrollTop();
			if(top > 170 && $("#b-fixed").hasClass("m-show") == false) {
				$("#b-fixed").addClass("m-show").stop().animate({
					top: 0
				}, "fast");
			}
			else if(top < 170) {
				$("#b-fixed").removeClass("m-show").stop().animate({
					top: -120
				}, "fast");
			}
		}
	}
/* скролящийся топ (end) */

	$("#slider, #slider_two, #slider_three, #slider-recommended, #b-detail-slider, #b-popup__slider").slides({
		container: "b-slider",
		prev: "m-prev",
		next: "m-next",
		paginationClass: "b-pager",
		autoHeight: true,
		play: 3000
	});
	
/* показываем/скрываем фильтр (begin) */	
	$(".b-toggle__btn").each(function() {
		var $id = $(this).attr("id");
		
		if($(this).hasClass("active"))
			$("#" + $id + "__toggle").show();
		else
			$("#" + $id + "__toggle").hide();
	});
	$(".b-toggle__btn").click(function() {
		var $id = $(this).attr("id");
		
		if($(this).hasClass("active")) {
			$(this).removeClass("active");
			$("#" + $id + "__toggle").slideUp();
		}
		else {
			$(this).addClass("active");
			$("#" + $id + "__toggle").slideDown();
			$("#" + $id + "__toggle").tinyscrollbar_update();
		}
		
		return false;
	});
/* показываем/скрываем фильтр (end) */	

/* input radio & checkbox (begin) */
	$(".b-radio, .b-checkbox").each(function() {
		var radio = $(this).find("input"),
			name = radio.attr("name");
			
		$(this).addClass("m-" + name);
		
		if(radio.is(":checked")) {
			$(this).addClass("b-checked");
		}
	});
	$(".b-radio input[type='radio']").change(function() {
		var name = $(this).attr("name");
		$(".m-" + name).removeClass("b-checked");
		$(this).parent().addClass("b-checked");
	});
	$(".b-checkbox input[type='checkbox']").change(function() {
		var name = $(this).attr("name");
		$(this).parent().toggleClass("b-checked");
	});
/* input radio & checkbox (end) */

/* кнопки: сортировать по (begin) */
	$(".b-sort__link").click(function() {
		if($(this).hasClass("active") == false) {
			$(".b-sort__link").removeClass("active");
			$(this).addClass("active");
		}
		else {
			$(this).toggleClass("m-sort__down");
		}
		return false;
	});
/* кнопки: сортировать по (end) */

/* вертикальный слайдер (begin) */
	$(".b-slider-vert").jCarouselLite({
		btnNext: ".m-vert__up",
		btnPrev: ".m-vert__down",
		vertical: true,
		//auto: 3000,
		//speed: 1000,
		//visible: 3, // количество видимых элементов
	});
/* вертикальный слайдер (end) */

/* кнокпи + и - в корзине (begin) */
	$(".b-cart-count__btn").click(function() {
		var val = $(this).data("value");
			current_val = parseInt($("#item_count_" + val).text());
			current_val_hiden = parseInt($("#item_hidden_" + val).val());
			console.log($("#item_hidden_" + val).val());
		if($(this).hasClass("m-dec") && current_val > 1){
			current_val--;
			current_val_hiden--;}
		if($(this).hasClass("m-inc")){
			current_val++;
			current_val_hiden++;}
			
		$("#item_count_" + val).text(current_val);
		$("#item_hidden_" + val).val(current_val_hiden);
		return false;
	});
/* кнокпи + и - в корзине (end) */

/* кнопка быстрого заказа (begin) */
	$("#b-fast_order").click(function() {
		var id = $(this).attr("id");
		
		$("." + id).toggleClass("active");
		return false;
	});
	$(document).bind("click.myEvent", function (e) {
		if ($(e.target).closest(".b-fast_order").length == 0) {
			$(".b-fast_order").removeClass("active");
		}
	});
/* кнопка быстрого заказа (end) */

/* слайдер, детальный просмотр (begin) */
	$(".b-detail-slider__item a").click(function() {
		var $src = $(this).attr("href");
		$("#b-detail__image").attr("src", $src);
		
		$(".b-detail-slider__item").removeClass("active");
		$(this).parent().addClass("active")
		
		return false;
	});
/* слайдер, детальный просмотр (end) */

  // Скрипты каталога
  var isCatalog = $("div.catalog")[0];
  if(!isCatalog) return;

  // Внешняя страница каталога: подгрузка новых товаров при прокрутке вниз
	var showYouWatched = true;
	var link = $("#catlistnavnext").attr("href");
	var loading = true;
    if(link) {
	$("#catlistnavnext span").html("<img src='/i/ajax-loader.gif' style='padding: 0 5px;' />  " + $("#catlistnavnext span").html());
	$("#catlistnavnext").click(                
        function(){
                   $.ajax({
                    url: link,
					success : function(data) {
					  var ul = $("ul.quick_view");
					  $(".b-show_more").remove();
					  console.log(ul.html());
					  ul.html(ul.html() + data);
					  loading=false;
					}
					})
					return false;
				}
			);
	}
    
      $(".filterElem").change(function() {
    var name = this.name.split("__")[1];
    if(this.type == "checkbox") {
      var checked = $(this).attr("checked") == "checked";
      var orig = $("[name='" + name + "'][value='" + this.value + "']");
      $(orig).attr("checked", checked); 
    } else if(this.type == "text" && $(this).hasClass("searchTxt")) {
      $("[name='" + name + "']").val($(this).val());
    } else if(this.type == "text") {
      var newval = parseInt($(this).val());
      if(isNaN(newval)) newval = "";
      $("[name='" + name + "']").val(newval);
    }
    sendfilter();
  }).keypress(function(event){
    if(event.keyCode == 13) {
      $(this).unbind("change");
      var name = this.name.split("__")[1];
      $("[name='" + name + "']").val($(this).val());
      sendfilter();
    }
  });
  
  $("#ratingMIN, #ratingMAX").change(function() {
    $("#slider-rating").slider("values", (this.id == "#ratingMIN" ? 0 : 1), $(this).val());
  });
  
  if($("#slider-rating").hasClass("ui-slider")) {
    var maxprice = parseInt($("#ratingMAX", this).val().replace(" ", ""));
    var minprice = parseInt($("#ratingMIN", this).val().replace(" ", ""));
    $("#slider-rating").slider({
      min: minprice,
      max: maxprice,
      values: [$("#ratingMIN").val() || 0, $("#ratingMAX").val() || 20000],
      range: true,
        stop: function(event, ui) {
        $("input#ratingMIN").val($("#slider-rating").slider("values",0));
            $("input#ratingMAX").val($("#slider-rating").slider("values",1));
        var name = $("input#ratingMIN").attr("name").split("__")[1];
        $("[name='" + name + "']").val($("input#ratingMIN").val());
        var name = $("input#ratingMAX").attr("name").split("__")[1];
        $("[name='" + name + "']").val($("input#ratingMAX").val());
        sendfilter();
      },
      slide: function(event, ui){
            $("input#ratingMIN").val($("#slider-rating").slider("values",0));
            $("input#ratingMAX").val($("#slider-rating").slider("values",1));
      }
    });
  }
  
  
  
    $("#arrFilter_P1_MIN, #arrFilter_P1_MAX").change(function() {
    $("#slider-arrFilter_P1_MIN").slider("values", (this.id == "#arrFilter_P1_MIN" ? 0 : 1), $(this).val());
  });
  

    var maxprice = parseInt($("#arrFilter_P1_MAX", this).val().replace(" ", ""));
    var minprice = parseInt($("#arrFilter_P1_MIN", this).val().replace(" ", ""));
    $("#slider-arrFilter_P1_MIN").slider({
      min: minprice,
      max: maxprice,
      values: [$("#arrFilter_P1_MIN").val() || 0, $("#arrFilter_P1_MAX").val() || 20000],
      range: true,
        stop: function(event, ui) {
        $("input#arrFilter_P1_MIN").val($("#slider-arrFilter_P1_MIN").slider("values",0));
            $("input#arrFilter_P1_MAX").val($("#slider-arrFilter_P1_MIN").slider("values",1));
        var name = $("input#arrFilter_P1_MIN").attr("name").split("__")[1];
        $("[name='" + name + "']").val($("input#arrFilter_P1_MIN").val());
        var name = $("input#arrFilter_P1_MAX").attr("name").split("__")[1];
        $("[name='" + name + "']").val($("input#arrFilter_P1_MAX").val());
        sendfilter();
      },
      slide: function(event, ui){
            $("input#arrFilter_P1_MIN").val($("#slider-arrFilter_P1_MIN").slider("values",0));
            $("input#arrFilter_P1_MAX").val($("#slider-arrFilter_P1_MIN").slider("values",1));
      }
    });

  
  
  var xhr = false;
  function sendfilter() {
    //alert("work");
    $(".newList").css("visibility", "hidden");
    if(xhr) { xhr.abort(); }
    $(".next_page").remove();
        //$(window).unbind("scroll");
    
    var width = $('.b-catalog-list').width() + 5;
        var height = $('.b-catalog-list').height();
        

    var datas = $("form#form_filtering").serialize();
    //alert(datas);
    var url = "/ajax/catalog.php";
    xhr = $.ajax({
      url: url + "?ajax=Y&set_filter=Y",
      data: datas,
      method: 'post',
      success: function(data) {

        $(".b-catalog-list ul").html(data);

      }
    });
  }
  
  $("form#form_filtering input, form#form_filtering select").change(sendfilter);
  
	
});




