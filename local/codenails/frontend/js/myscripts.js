/*ТАБЫ =====================================================
			<ul class="tabs">
				<li class="active"><a href="#tab1">1</a></li>
				<li><a href="#tab2">2</a></li>
			</ul>
			<div class="clb"></div>
			<div class="tab_container">
				<div class="tab_content" id="tab1">
					1
				</div>
				<div class="tab_content" id="tab2">
					2
				</div>
			</div>

			ul.tabs { list-style: none;
				li { 
					a {
						&:hover {}
					}
				}	
				li.active {
					a {}
				}
			}
			.tab_container {
				.tab_content {}
			}

*/
$(document).ready(function(){
	$(".tab_content").hide();
	$("ul.tabs li:first").addClass("active").show();
	$(".tab_content:first").show();
	$("ul.tabs li").click(function(){
	$("ul.tabs li").removeClass("active");
	$(this).addClass("active");
	$(".tab_content").hide();
	var activeTab = $(this).find("a").attr("href");
	$(activeTab).fadeIn();
	return false;});
});

/* ВСПЛЫВАЮЩИЕ ОКНА =====================================================
			<a href="#" class="trigger">Открыть</a>
			<div class="panel">
				<a href="#" class="trigger">x</a>
			</div>

			.panel {
				position:fixed; top:0; right:0; display:none;
					a.trigger {}	
			}
*/
$(document).ready(function(){
	$(".trigger").click(function(){
	$(".panel").toggle("fast");
	$(this).toggleClass("active");
	return false;
	});
});





//Фиксация меню добавляет или убавляет класс fix для ид menu
$(document).ready(function () {
    var $div = $("#menu")
    ;
    $(window).scroll(function () {
        if ($(this).scrollTop() > 182 && $div.hasClass("normal")) {
            $div.removeClass("normal").addClass("fix");
        }
        else if ($(this).scrollTop() <= 182 && $div.hasClass("fix")) {
            $div.removeClass("fix").addClass("normal");
        }
    });
});
