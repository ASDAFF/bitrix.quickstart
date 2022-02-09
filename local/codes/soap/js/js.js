
 $.fn.popup = function(options) {
	var options = $.extend({
			popup_data: "href" // цвет шрифта при hover
		}, options);
	
	$("body").css("position", "relative");
	 
	 $(document).bind("click", function(e) {
                                        if($(e.target).closest(".popup-show").length == 0) {
                                                if($(".popup-show").length) {
                                                        $w = $(".popup-show").clone(true);
                                                        $w.removeClass("popup-show").hide();
                                                        $(".popup-placeholder").replaceWith($w);
                                                        $(".popup-show").remove();
                                                }
                                        }
                                });
		 
    return this.each(function() {			
		
		
		$(this).live('click', function(event) { 
			if($(".popup-show").length) {
				$w = $(".popup-show").clone(true);
				$w.removeClass("popup-show").hide();
				$(".popup-placeholder").replaceWith($w);
				$(".popup-show").remove();
			}
			
			var popup = {
					obj:	$(this).attr(options.popup_data),
					top:	$(this).offset().top + $(this).outerHeight() + 12,
					left:	$(this).offset().left + ($(this).outerWidth() / 2) - 40
				},
				$popup_window = $(popup.obj),
				$popup_clone = $(popup.obj).clone(true);
				
			// заменяем окно
			$popup_window.replaceWith("<div class='popup-placeholder' style='display: none' />");
			
			$("body").append($popup_clone);
			$popup_clone
				.addClass("popup-show")
				.css({
					left: popup.left,
					top: popup.top
				})
				.show();
                                  
                         if($(this).hasClass('listcompare') ){ 
                               $(this).removeClass('listcompare');
                               var imgid = $(this).attr("id"),
                                $_this = $(this);
                                $.ajax({
                                    type: "GET",
                                    url: $(this).attr("rel"),
                                    dataType: "html",
                                    success: function(out){
                                        $_this.removeClass("m-compare__add").addClass("js-compare__added");

                                        $("#cid").html(getConfHTML(out));
                                        //var imageElement =  document.getElementById(imgid);
                                        var imageToFly = $("#image-" + imgid);
                                        var position = imageToFly.offset();
                                        var flyImage = imageToFly.clone().appendTo("body");
                                        var confposition = $("#cid").offset();

                                        flyImage.css({ "position": "absolute", "left": position.left, "top": position.top, "z-index": 2000 });
                                        flyImage.animate({ width: 0, height: 0, left: confposition.left, top: confposition.top}, 800, 'linear', function() {
                                            flyImage.remove();
                                        }); 

                                    }   
                                }); 
                            }            
               
                         return false;
		});
    });
};
 
$(document).ready(function() {
		$(".js-hover__detail").hover(
			function() {
				var id = $(this).data("id");

				if($(id).length) {
					//console.log($(this).position());
					$(id).css({
						left: $(this).position().left + $(this).width() + 15
					});
					$(id).show();
				}
			},
			function() {
				var id = $(this).data("id");
				if($(id).length) {
					$(id).hide();
				}
			}
		);
        $("a[href='#b-wishlist__add']").click(function(){
                $("#wishlist_add_el").attr('el',$(this).attr('el'))
        })
        function wishlist_element_add(object){
            var ID = object.attr('el')
            var category = $('#cat_list').val()
            var name1 = $('#new_wish_field').val()
			
            var button = object
            $.ajax({
                    type: "POST",
                    url: "/includes/ajax/wishlist/add_element.php",
                    data: ({
                            element : ID,
                            cat : category,
                            name: name1
                    }),
                    success: function(html){
						//console.log(html)
						$('#b-wishlist__add').hide()
						
						if($("#cat_list option[value="+html+"]").length){
							
						} else {
							var opt = "<option value="+html+">"+name+"</option>"
							$("#cat_list").append(opt)
						}
                        $.gritter.add({
                                title: 'Добавление товара',
                                text: 'Товар был успешно добавлен в вишлист!',
                                sticky: false,
                                time: 2500
                        });
                    }
            })
        }
        $("#wishlist_add_el").click(function(){
                var button = $(this)
                if(button.attr('el')){
                    wishlist_element_add(button)
                }

        }) 

/* история заказов (begin) */
	$(".b-history__tr").click(function() {
		var rel = $(this).attr("rel");
		
		if(typeof rel == "undefined")
			return;
		
		$.fancybox({
			href: rel,
					padding: 0,
					fitToView: false
		});
	});
/* история заказов (end) */

/* переключение табов (begin) */
        //	$(".b-tab-head__link").click(function() {
        // логика простая
        // у ссылки берется href, в ней лежит id div'а 
        // если существует такой div то он показывается 
        // иначе ссылка просто работает как сслыка
        // использовать можно где угодно главное у ссылки 
        // должен быть класс b-tab-head__link а у div'а 
        // класс b-tab__body и оба и у обоих совпадал класс active
        //		var href = $(this).attr("href");
        //		
        //		if($(href).length) {
        //			$(".b-tab-head__link").removeClass("active");
        //			$(this).addClass("active");
        //			
        //			$(".b-tab__body").removeClass("active");
        //			$(href).addClass("active");
        //			return false;
        //		}	
        //	});

	$(".b-tab-head__link").click(function() {
		// логика простая
		// у ссылки берется href, в ней лежит id div'а 
		// если существует такой div то он показывается 
		// иначе ссылка просто работает как сслыка
		// использовать можно где угодно главное у ссылки 
		// должен быть класс b-tab-head__link а у div'а 
		// класс b-tab__body и оба и у обоих совпадал класс active
                var href = $(this).attr("href"),
					slider = $(this).data("slider"),
					$this_parent = $(this).closest(".b-sw");
		
				//tab_length = $(".b-tab-head").children().length - 1;
				//console.log(tab_length)
		if($(href).length) {
                    $(this).parent().children().removeClass("active");
			$(this).addClass("active");
			
                          $(this).parent().parent().children('.b-tab__body').removeClass("active");
						  //console.log($(this).parent().parent().children('.b-tab__body').each());
			$(href).addClass("active");

                    // срабатывает только для слайдеров
                    if(slider == "Y") {
                        var slider_id = $(href).find(".b-slider-wrapper").attr("id");
                        $slider = $("#" + slider_id);

                        is_active = $slider.find(".slides_control").length;

                        if(is_active == false) {
                            var slide_last = 0, slide_length = $slider.find(".b-slider").children().length;
                            $slider.slides({
                                    container: "b-slider",
                                    prev: "m-prev",
                                    next: "m-next",
                                    paginationClass: "b-pager",
                                    animationStart: function(i) {
                                        if(slide_last == slide_length && i == "next") {
                                            var tab_current = $this_parent.find(".b-tab-head").children(".active").index(),
												tab_length = $this_parent.find(".b-tab-head").children().length - 1,
												tab_next = tab_current == tab_length ? 0 : (tab_current + 1);
												
											$this_parent.find(".b-tab-head__link").eq(tab_next).click();
                                        }
                                    },
                                    animationComplete: function(i) {
                                        slide_last = i;
                                    }
                            });
                        }
                    }

			return false;
		}	
	});
/* переключение табов (end) */ 

/* кнопка Войти (begin) */	
	$(".m-wishlist__add").popup();
	$(".m-login__link").popup();
	$(".m-user__auth").popup();
	$(".m-compare__add").popup();
	$(".b-show-fast__order").popup();
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
	var header_top = $(".b-header-user").offset().top - 16;
	
	$(window).scroll(function() {
		var scroll_top = $(this).scrollTop();
		//console.log(scroll_top);
		if(scroll_top >= header_top) {
			$(".b-nav__wrapper").addClass("m-fixed");
		}
		else 
			$(".b-nav__wrapper").removeClass("m-fixed");
	});
	/* функция кроссбраузерного определения отступа от верха документа к текущей позиции скроллера прокрутки 
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
        /* пересчитываем отступ при прокрутке экрана
	$(window).scroll(function() {
		fixPaneRefresh();
	});
	
	function fixPaneRefresh(){
		$("#b-search__popup").hide();
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
				$("#b-fixed-search__popup").hide();
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


/* кнокпи + и - в корзине (begin) */
	$(".b-cart-count__btn").click(function() {
                var val = $(this).data("value");
			current_val = parseInt($("#item_count_" + val).text());
				price_one_item = $(this).parent().parent().parent();
				price_one = price_one_item.attr("price")
                current_val_hiden = parseInt($("#item_hidden_" + val).val());
                //console.log($("#item_hidden_" + val).val());
                if($(this).hasClass("m-dec") && current_val > 1){
			current_val--;
                    current_val_hiden--;} 
                if($(this).hasClass("m-inc")){
			current_val++;
                    current_val_hiden++;} 
				price_one_item.attr('final_price',price_one*current_val)
			
				var itog = ''+parseInt(price_one*current_val)
				price_one_item.parent().find('.total_right').text(itog.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')+'.00.-')
				//console.log(price_one_item.attr('final_price'))
				var summ = 0;
				$('.b-cart__price').each(function(){
					summ = summ + parseInt($(this).attr('final_price'))
					//console.log(summ)
				}) 
				summ =''+summ
				$('.b-total__price').children().text(summ.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')+'.00.-')
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

/* вертикальный слайдер (begin) */
 
	$(".b-slider-vert").jCarouselLite({
		btnNext: ".m-vert__up",
		btnPrev: ".m-vert__down",
		vertical: true
		//auto: 3000,
		//speed: 1000,
		//visible: 3, // количество видимых элементов
	});
/* вертикальный слайдер (end) */



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
            $("#catlistnavnext span").html($("#catlistnavnext span").html());
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
                                makeAdd2cartLinks(); 
                                makeAdd2compareLinks();  
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

        $('.baloon').balloon({
			contents: 'Показать другие товары с <br /> таким параметром',
			position: "right"
        });
        
        

        $("#arrFilter_P1_MIN, #arrFilter_P1_MAX").change(function() {
                $("#slider-arrFilter_P1_MIN").slider("values", (this.id == "#arrFilter_P1_MIN" ? 0 : 1), $(this).val());
        });

//
        var maxprice = parseInt($("#arrFilter_P1_MAX", this).val());
        var minprice = parseInt($("#arrFilter_P1_MIN", this).val());
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
$(function(){
        $('.b-search-form__lucky').live('click', function(){

                t = $(this).parent().find('.b-search-form__text').val();

                if(t)
                    location.href = '/search/index.php?q=' + t + '&luck';

                return false;  });
});