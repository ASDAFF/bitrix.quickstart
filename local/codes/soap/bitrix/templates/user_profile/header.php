<!DOCTYPE html>
<!--[if IE 7 ]><html lang="ru" class="no-js ie7"><![endif]-->
<!--[if IE 8 ]><html lang="ru" class="no-js ie8"><![endif]-->
<!--[if IE 9 ]><html lang="ru" class="no-js ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="ru" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?$APPLICATION->ShowTitle()?> :: COMP2YOU</title>

    <!-- google webfonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:600,700&subset=latin,cyrillic">

    <link rel="stylesheet" href="/js/libs/jquery-ui/ui-lightness/jquery-ui-1.10.0.custom.css">
    <link rel="stylesheet" href="/js/libs/tinyscrollbar/tinyscrollbar.css">
    <link rel="stylesheet" href="/css/normalize.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <!--[if lt IE 9]><link rel="stylesheet" href="/css/ie.css"><![endif]-->	

    <script type="text/javascript" src="/js/libs/modernizr-2.6.2.min.js"></script>

    <!-- PLUGINS: jQuery v1.8.2 -->
    <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
    <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>

    <!-- Slides -->
    <script src="/js/libs/slides.min.jquery.js"></script>
    <!-- jQuery UI v1.10.0 -->
    <script src="/js/libs/jquery-ui/jquery-ui-1.10.0.custom.min.js"></script>
    <!-- Tiny Scrollbar -->
    <script src="/js/libs/tinyscrollbar/jquery.tinyscrollbar.min.js"></script>
    <!-- JCarouselLite -->
    <script src="/js/libs/jcarousellite1.0.1.pack.js"></script>
	<!-- fancyBox 2.1.4 -->
	<script src="/js/libs/fancybox/jquery.fancybox.pack.js"></script>
	<link rel="stylesheet" href="/js/libs/fancybox/jquery.fancybox.css">

    <script src="/js/js.js"></script>
    <!--[if lt IE 10]>
    <script src="/js/libs/jquery.placeholder.min.js"></script>
    <script>
    $(document).ready(function() {
    $("input[placeholder], textarea[placeholder]").placeholder();
    });
    </script>
    <![endif]-->	
    <link rel="stylesheet" href="/js/gritter/css/jquery.gritter.css">
    <script src="/js/gritter/js/jquery.gritter.js"></script>
    <script src="/js/subscr.js"></script>
    <?$APPLICATION->ShowHead();?>
   <script type="text/javascript">
        $(document).ready(
            function(){
                //ajax добавление в корзину
                buy_btns = $('a[href*="ADD2BASKET"]');
                buy_btns.each(
                    function(){
                        $(this).attr("rel", $(this).attr("href"));
                    }
                );
                buy_btns.attr("href","javascript:void(0);");
                function getBasketHTML(html)
                {
                    txt = html.split('<!--start--><div id="bid">');
                    txt = txt[2];
                    txt = txt.split('</div><!--end-->');
                    txt = txt[0];
                    return txt;
                }

                $('a[rel*="ADD2BASKET"]').click(                
                    function(){
                        var imgid = $(this).attr("id");
                        $.ajax({
                                type: "GET",
                                url: $(this).attr("rel"),
                                dataType: "html",
                                success: function(out){

                                    $("#bid").html(getBasketHTML(out));
                                    var imageElement =  document.getElementById(imgid);
                                    var imageToFly = $(imageElement);
                                    var position = imageToFly.offset();
                                    var flyImage = imageToFly.clone().appendTo("body");
                                    var basketposition = $("div .b-cart-mini").offset();
									
								flyImage.css({ "position": "absolute", "left": position.left, "top": position.top, "z-index": 2000 });
								flyImage.animate({ width: 0, height: 0, left: basketposition.left, top: basketposition.top}, 800, 'linear', function() {
									flyImage.remove();
								});

                                    return false;
                                }
                        });
                    }
                );
                //ajax добавление в сравнение
                conf_btns = $('a[href*="ADD_TO_COMPARE_LIST"]');
                conf_btns.each(
                    function(){
                        $(this).attr("rel", $(this).attr("href"));
                    }
                );
                conf_btns.attr("href","javascript:void(0);");
                function getConfHTML(html)
                {
                    txt = html.split('<!--startc--><span id="cid">');
                    txt = txt[2];
                    txt = txt.split('</span><!--endc-->');
                    txt = txt[0];
                    return txt;
                }

                $('a[rel*="ADD_TO_COMPARE_LIST"]').click(                
                    function(){
                        var imgid = $(this).attr("id"),
							$_this = $(this);
                        $.ajax({
                                type: "GET",
                                url: $(this).attr("rel"),
                                dataType: "html",
                                success: function(out){
									$_this.removeClass("m-compare__add").addClass("js-compare__added");
									console.log($(this));
									
                                    $("#cid").html(getConfHTML(out));
                                    var imageElement =  document.getElementById(imgid);
                                    var imageToFly = $(imageElement);
                                    var position = imageToFly.offset();
                                    var flyImage = imageToFly.clone().appendTo("body");
                                    var confposition = $("div .b-header-user__link").offset();

								flyImage.css({ "position": "absolute", "left": position.left, "top": position.top, "z-index": 2000 });
								flyImage.animate({ width: 0, height: 0, left: confposition.left, top: confposition.top}, 800, 'linear', function() {
									flyImage.remove();
								});

                                    return false;
                                }
                        });
                    }
                );


            }
        );

    </script>
</head>
<body>
<?$APPLICATION->ShowPanel();?>
<div class="b-page">
<div class="b-wrapper">
<div class="b-header__line">
<div class="b-nav__wrapper">
<header class="b-header clearfix">
    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => "/includes/logo.php",
                "EDIT_TEMPLATE" => ""
            ),
            false
        );?>
    <div class="b-header-text">
        <div class="b-header-text__work">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => "/includes/top_work.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );?>
        </div>
        <div class="b-header-text__phone">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => "/includes/top_phone.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );?>
        </div>
    </div>
    <div class="b-header-nav">
        <?$APPLICATION->IncludeComponent("bitrix:menu", "top_nav_menu", array(
                    "ROOT_MENU_TYPE" => "top",
                    "MENU_CACHE_TYPE" => "N",
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(
                    ),
                    "MAX_LEVEL" => "1",
                    "CHILD_MENU_TYPE" => "left",
                    "USE_EXT" => "N",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N"
                ),
                false
            );?>
        <div class="b-header-user">
            <?
$APPLICATION->IncludeComponent("cm:system.auth.form", "main_auth", array(
	"REGISTER_URL" => "/user/registration/",
	"FORGOT_PASSWORD_URL" => "",
	"PROFILE_URL" => "/user/profile/",
	"ORDER_URL" => "/user/profile/history/",
	"ADRESS_URL" => "/user/profile/address/",
	"SHOW_ERRORS" => "Y"
	),
	false
);
?>
            <!--startc--><span id="cid">
                <?$APPLICATION->IncludeComponent("bitrix:catalog.compare.list", "top_compare_list", array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => "1",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"DETAIL_URL" => "",
	"COMPARE_URL" => "/catalogue/compare.php",
	"NAME" => "CATALOG_COMPARE_LIST",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
            </span><!--endc-->
            <a href="/wishlist/" class="b-header-user__link">Мои вишлисты</a>
        </div>
    </div>
</header><!--/header-->
<nav class="b-nav clearfix">
    <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "section_tree2", Array(
	"IBLOCK_TYPE" => "catalog",	// Тип инфоблока
	"IBLOCK_ID" => "1",	// Инфоблок
	"SECTION_ID" => $_REQUEST["SECTION_ID"],	// ID раздела
	"SECTION_CODE" => "",	// Код раздела
	"SECTION_URL" => "",	// URL, ведущий на страницу с содержимым раздела
	"COUNT_ELEMENTS" => "Y",	// Показывать количество элементов в разделе
	"TOP_DEPTH" => "2",	// Максимальная отображаемая глубина разделов
	"SECTION_FIELDS" => "",	// Поля разделов
	"SECTION_USER_FIELDS" => "",	// Свойства разделов
	"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
	"CACHE_TYPE" => "A",	// Тип кеширования
	"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"CACHE_NOTES" => "",
	"CACHE_GROUPS" => "Y",	// Учитывать права доступа
	),
	false
);?>
    <div class="b-nav-category m-search">
<!--        <form action="">
            <div class="b-search-form">
                <input type="text" class="b-search-form__text" />
                <input type="submit" class="b-search-form__submit" value="" />
            </div>
            <button class="b-search-form__lucky">Мне<br>повезет</button>
        </form>   -->

        <?$APPLICATION->IncludeComponent("cm:search.title", "static_header", array(
	"NUM_CATEGORIES" => "1",
	"TOP_COUNT" => "10",
	"ORDER" => "date",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "Y",
	"SHOW_OTHERS" => "N",
	"PAGE" => "#SITE_DIR#search/index.php",
	"CATEGORY_0_TITLE" => "",
	"CATEGORY_0" => array(
		0 => "iblock_catalog",
	),
	"CATEGORY_0_iblock_catalog" => array(
		0 => "all",
	),
	"SHOW_INPUT" => "Y",
	"INPUT_ID" => "header-title-search-input",
	"CONTAINER_ID" => "header-title-search"
	),
	false
);?>


    </div>
    <!--start--><div id="bid">
        <?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "small_info_basket", array(
                    "PATH_TO_BASKET" => "/basket/",
                    "PATH_TO_PERSONAL" => "/personal/",
                    "SHOW_PERSONAL_LINK" => "Y"
                ),
                false
        );?></div><!--end-->
    <?$APPLICATION->IncludeComponent("bitrix:sale.viewed.product", "viewed_products", Array(
                "VIEWED_COUNT" => "10000",	// Количество элементов
                "VIEWED_NAME" => "Y",	// Показывать наименование
                "VIEWED_IMAGE" => "Y",	// Показывать изображение
                "VIEWED_PRICE" => "Y",	// Показывать цену
                "VIEWED_CANBUY" => "N",	// Показать "Купить"
                "VIEWED_CANBUSKET" => "N",	// Показать "В корзину"
                "VIEWED_IMG_HEIGHT" => "150",	// Высота изображения
                "VIEWED_IMG_WIDTH" => "150",	// Ширина изображения
                "BASKET_URL" => "/basket/",	// URL, ведущий на страницу с корзиной покупателя
                "ACTION_VARIABLE" => "action",	// Название переменной, в которой передается действие
                "PRODUCT_ID_VARIABLE" => "id",	// Название переменной, в которой передается код товара для покупки
                "SET_TITLE" => "N",	// Устанавливать заголовок страницы
            ),
            false
        );?> 
</nav>
	</div><!--/.b-nav__wrapper-->
</div><!--/.b-header__line-->
    <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "breadcrumbs", array(
                "START_FROM" => "",
                "PATH" => "",
                "SITE_ID" => "s1"
            ),
            false
        );?>
<div class="b-container clearfix">
<aside class="b-sidebar">
        <div class="b-sidebar-filter m-sidebar">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/user_order_info.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
        </div>
</aside>
<section class="b-content">
<article class="b-detail-wrapper">
<?$APPLICATION->IncludeComponent("bitrix:menu", "inner_section_menu", array(
	"ROOT_MENU_TYPE" => "section",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
<h3 class=b-detail__h3><?$APPLICATION->ShowTitle()?></h3>
