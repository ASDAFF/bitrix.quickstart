<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html>
<!--[if IE 9]> <html class="ie9"> <![endif]-->
<!--[if IE 8]> <html class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html>
<!--<![endif]-->
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<meta name="robots" content="all">
	<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>">
	<?$APPLICATION->ShowMeta("keywords")?>
	<?$APPLICATION->ShowMeta("description")?>
	
	<link type="image/x-icon" rel="shortcut icon" href="<?=SITE_DIR?>favicon.ico" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic,300,300italic&subset=latin,cyrillic,latin-ext,cyrillic-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=PT+Serif:400,400italic,700,700italic&subset=latin,cyrillic,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
	<link href="<?=SITE_TEMPLATE_PATH?>/fonts/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/fonts/fontello/css/fontello.css" rel="stylesheet">

	<link href="<?=SITE_TEMPLATE_PATH?>/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/rs-plugin/css/settings.css" media="screen" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/rs-plugin/css/extralayers.css" media="screen" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/magnific-popup/magnific-popup.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/css/animations.css" rel="stylesheet">
	<link href="<?=SITE_TEMPLATE_PATH?>/plugins/owl-carousel/owl.carousel.css" rel="stylesheet">	
	
	<?$APPLICATION->ShowCSS();?>

	<!-- Theme CSS -->
	<link href="<?=SITE_TEMPLATE_PATH?>/css/theme/color/<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["COLOR"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["COLOR"] : COption::GetOptionString("effortless", "QUICK_THEME_COLOR", "red", SITE_ID))?>.css" rel="stylesheet" type="text/css">	
	<link href="<?=SITE_TEMPLATE_PATH?>/css/theme/background/<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["BACKGROUND"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["BACKGROUND"] : COption::GetOptionString("effortless", "QUICK_THEME_BACKGROUND", "background0", SITE_ID))?>.css" rel="stylesheet" type="text/css">	
	<link href="<?=SITE_TEMPLATE_PATH?>/css/theme/boxed/<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["BOXED"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["BOXED"] : COption::GetOptionString("effortless", "QUICK_THEME_BOXED", "standard", SITE_ID))?>.css" rel="stylesheet" type="text/css">	

	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/modernizr.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/isotope/isotope.pkgd.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/owl-carousel/owl.carousel.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.appear.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.parallax-1.1.3.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/jquery.browser.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/plugins/SmoothScroll.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/plugins.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/template.js"></script>

	<?$APPLICATION->ShowHeadStrings();?>
	<?$APPLICATION->ShowHeadScripts();?>
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body><div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="page-wrapper">
	<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => "#SITE_DIR#include/line.php", 
		),
		false,
		array("ACTIVE_COMPONENT" => (!empty($_SESSION["QUICK_THEME"][SITE_ID]["LINE"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["LINE"] : COption::GetOptionString("effortless", "QUICK_THEME_LINE", "N", SITE_ID)))
	);?>
	<div class="header-top <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["HEADER_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["HEADER_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_HEADER_BG", "white", SITE_ID))?>">
		<div class="container">
			<div class="row">
				<div class="col-xs-8 col-sm-4 col-md-4">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "#SITE_DIR#include/phone.php",
						)
					);?>
				</div>
				<div class="hidden-xs col-sm-5 col-md-4">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "#SITE_DIR#include/email.php",
						)
					);?>
				</div>
				<div class="col-xs-4 col-sm-3 col-md-4">
					<div class="header-top-dropdown">
						<div class="btn-group dropdown">
							<button type="button" class="btn dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown"><i class="fa fa-phone text-default"></i> <?=GetMessage("QUICK_EFFORTLESS_HEADER_CALLBACK")?></button>
							<button type="button" class="btn dropdown-toggle hidden-lg hidden-md" data-toggle="dropdown"><i class="fa fa-phone text-default"></i></button>									
							<ul class="dropdown-menu dropdown-menu-right dropdown-animation">
								<li>
									<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
											"AREA_FILE_SHOW" => "file",
											"PATH" => "#SITE_DIR#include/form-callback-modal.php",
										)
									);?>
								</li>
							</ul>
						</div>
						<div class="btn-group dropdown">
							<button type="button" class="btn dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown"><i class="fa fa-search text-default"></i> <?=GetMessage("QUICK_EFFORTLESS_HEADER_SEARCH")?></button>
							<button type="button" class="btn dropdown-toggle hidden-lg hidden-md" data-toggle="dropdown"><i class="fa fa-search text-default"></i></button>
							<ul class="dropdown-menu dropdown-menu-right dropdown-animation">
								<li>
									<?$APPLICATION->IncludeComponent("bitrix:search.form","flat",Array(
											"USE_SUGGEST" => "N",
											"PAGE" => "/search/"
										)
									);?>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?if((!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU_TOP_SLIDER"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU_TOP_SLIDER"] : COption::GetOptionString("effortless", "QUICK_THEME_MENU_TOP_SLIDER", "Y", SITE_ID)) == "Y"):?>
	<script>
	jQuery(function(){
		var	headerTopHeight = $(".header-top").outerHeight(),
		headerHeight = $("header.header.float").outerHeight();
		$(window).scroll(function() {
			if (($(".header.float").length > 0)) { 
				if(($(this).scrollTop() > headerTopHeight+headerHeight) && ($(window).width() > 767)) {
					$("body").addClass("fixed-header-on");
					$(".header.float").addClass('animated object-visible fadeInDown');
					if (!($(".header.transparent").length>0)) {
						if ($(".banner:not(.header-top)").length>0) {
							$(".banner").css("marginTop", (headerHeight)+"px");
						} else if ($(".page-intro").length>0) {
							$(".page-intro").css("marginTop", (headerHeight)+"px");
						} else if ($(".page-top").length>0) {
							$(".page-top").css("marginTop", (headerHeight)+"px");
						} else {
							$("section.main-container").css("marginTop", (headerHeight)+"px");
						}
					}
				} else {
					$("body").removeClass("fixed-header-on");
					$("section.main-container").css("marginTop", (0)+"px");
					$(".banner").css("marginTop", (0)+"px");
					$(".page-intro").css("marginTop", (0)+"px");
					$(".page-top").css("marginTop", (0)+"px");
					$(".header.float").removeClass('animated object-visible fadeInDown');
				}
			};
		});		
	});
	</script>
	<header class="header <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU_TRANSPARENT"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU_TRANSPARENT"] : COption::GetOptionString("effortless", "QUICK_THEME_MENU_TRANSPARENT", "menu-transparent", SITE_ID))?> <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU"] : COption::GetOptionString("effortless", "QUICK_THEME_MENU", "float", SITE_ID))?> <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_HEADER_MENU_BG", "white", SITE_ID))?> clearfix">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<div class="header-left clearfix">
						<div class="logo">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "#SITE_DIR#include/company-logo.php", 
								)
							);?>
						</div>
					</div>
				</div>
				<div class="col-md-10">
					<div class="header-right clearfix">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
								"ROOT_MENU_TYPE" => "top",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"MENU_CACHE_GET_VARS" => "",
								"MAX_LEVEL" => "4",
								"CHILD_MENU_TYPE" => "podmenu",
								"USE_EXT" => "Y",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "N",
							),
							false
						);?>
					</div>
				</div>
			</div>
		</div>
	</header>
<?endif?>
<?if((!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER", "standart", SITE_ID)) == "standart"):?>
	<div class="banner <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER_BG", "gray-bg", SITE_ID))?>">
		<div class="<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_STANDART_BOXED"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_STANDART_BOXED"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER_STANDART_BOXED", "slideshow", SITE_ID))?>">
			<div class="slider-banner-container slider-load-box">
				<div class="<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID))?>">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "slider", Array(
							"IBLOCK_TYPE" => "#IBLOCK_TYPE_SLIDER#",
							"IBLOCK_ID" => "#IBLOCK_ID_SLIDER#",
							"SECTION_ID" => $_REQUEST["SECTION_ID"],
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => "",
							"ELEMENT_SORT_FIELD" => "sort",
							"ELEMENT_SORT_ORDER" => "asc",
							"ELEMENT_SORT_FIELD2" => "name",
							"ELEMENT_SORT_ORDER2" => "asc",
							"FILTER_NAME" => "arrFilter",
							"INCLUDE_SUBSECTIONS" => "Y",
							"SHOW_ALL_WO_SECTION" => "Y",
							"PAGE_ELEMENT_COUNT" => "1000000",
							"LINE_ELEMENT_COUNT" => "1",
							"PROPERTY_CODE" => array(
								0 => "TRANSITION",
								1 => "HEADER",
								2 => "DESCRIPTION",
							),
							"OFFERS_LIMIT" => "5",
							"ADD_PICT_PROP" => "-",
							"LABEL_PROP" => "-",
							"SECTION_URL" => "",
							"DETAIL_URL" => "",
							"SECTION_ID_VARIABLE" => "SECTION_ID",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "N",
							"AJAX_OPTION_HISTORY" => "N",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "36000000",
							"CACHE_GROUPS" => "Y",
							"SET_META_KEYWORDS" => "Y",
							"META_KEYWORDS" => "-",
							"SET_META_DESCRIPTION" => "Y",
							"META_DESCRIPTION" => "-",
							"BROWSER_TITLE" => "-",
							"ADD_SECTIONS_CHAIN" => "N",
							"DISPLAY_COMPARE" => "N",
							"SET_TITLE" => "N",
							"SET_STATUS_404" => "N",
							"CACHE_FILTER" => "Y",
							"PRICE_CODE" => "",
							"USE_PRICE_COUNT" => "N",
							"SHOW_PRICE_COUNT" => "1",
							"PRICE_VAT_INCLUDE" => "Y",
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",
							"USE_PRODUCT_QUANTITY" => "N",
							"ADD_PROPERTIES_TO_BASKET" => "Y",
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"PARTIAL_PRODUCT_PROPERTIES" => "N",
							"PRODUCT_PROPERTIES" => "",
							"PAGER_TEMPLATE" => "",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
							"PAGER_SHOW_ALL" => "N",
							"AJAX_OPTION_ADDITIONAL" => "",
							"PRODUCT_QUANTITY_VARIABLE" => "quantity",
							"TEMPLATE_THEME" => "blue",
							"MESS_BTN_BUY" => "",
							"MESS_BTN_ADD_TO_BASKET" => "",
							"MESS_BTN_SUBSCRIBE" => "",
							"MESS_BTN_DETAIL" => "",
							"MESS_NOT_AVAILABLE" => "",
							"SET_BROWSER_TITLE" => "Y",
							"BASKET_URL" => "/personal/basket.php",
							"PAGER_TITLE" => "",
						),
						false,
						array("ACTIVE_COMPONENT" => "Y")
					);?>
				</div>
			</div>
		</div>
	</div>
<?endif?>
<?if((!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER", "standart", SITE_ID)) == "boxed"):?>
	<div class="banner <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER_BG", "gray-bg", SITE_ID))?>">
		<div class="slideshow-boxed">
			<div class="container">
				<div class="slider-banner-container slider-load-box">
					<div class="<?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "QUICK_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID))?>">
						<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "slider", Array(
								"IBLOCK_TYPE" => "#IBLOCK_TYPE_SLIDER#",
								"IBLOCK_ID" => "#IBLOCK_ID_SLIDER#",
								"SECTION_ID" => $_REQUEST["SECTION_ID"],
								"SECTION_CODE" => "",
								"SECTION_USER_FIELDS" => "",
								"ELEMENT_SORT_FIELD" => "sort",
								"ELEMENT_SORT_ORDER" => "asc",
								"ELEMENT_SORT_FIELD2" => "name",
								"ELEMENT_SORT_ORDER2" => "asc",
								"FILTER_NAME" => "arrFilter",
								"INCLUDE_SUBSECTIONS" => "Y",
								"SHOW_ALL_WO_SECTION" => "Y",
								"PAGE_ELEMENT_COUNT" => "1000000",
								"LINE_ELEMENT_COUNT" => "1",
								"PROPERTY_CODE" => array(
									0 => "TRANSITION",
									1 => "HEADER",
									2 => "DESCRIPTION",
								),
								"OFFERS_LIMIT" => "5",
								"ADD_PICT_PROP" => "-",
								"LABEL_PROP" => "-",
								"SECTION_URL" => "",
								"DETAIL_URL" => "",
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"AJAX_MODE" => "N",
								"AJAX_OPTION_JUMP" => "N",
								"AJAX_OPTION_STYLE" => "N",
								"AJAX_OPTION_HISTORY" => "N",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "36000000",
								"CACHE_GROUPS" => "Y",
								"SET_META_KEYWORDS" => "Y",
								"META_KEYWORDS" => "-",
								"SET_META_DESCRIPTION" => "Y",
								"META_DESCRIPTION" => "-",
								"BROWSER_TITLE" => "-",
								"ADD_SECTIONS_CHAIN" => "N",
								"DISPLAY_COMPARE" => "N",
								"SET_TITLE" => "N",
								"SET_STATUS_404" => "N",
								"CACHE_FILTER" => "Y",
								"PRICE_CODE" => "",
								"USE_PRICE_COUNT" => "N",
								"SHOW_PRICE_COUNT" => "1",
								"PRICE_VAT_INCLUDE" => "Y",
								"ACTION_VARIABLE" => "action",
								"PRODUCT_ID_VARIABLE" => "id",
								"USE_PRODUCT_QUANTITY" => "N",
								"ADD_PROPERTIES_TO_BASKET" => "Y",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"PARTIAL_PRODUCT_PROPERTIES" => "N",
								"PRODUCT_PROPERTIES" => "",
								"PAGER_TEMPLATE" => "",
								"DISPLAY_TOP_PAGER" => "N",
								"DISPLAY_BOTTOM_PAGER" => "N",
								"PAGER_SHOW_ALWAYS" => "N",
								"PAGER_DESC_NUMBERING" => "N",
								"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
								"PAGER_SHOW_ALL" => "N",
								"AJAX_OPTION_ADDITIONAL" => "",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"TEMPLATE_THEME" => "blue",
								"MESS_BTN_BUY" => "",
								"MESS_BTN_ADD_TO_BASKET" => "",
								"MESS_BTN_SUBSCRIBE" => "",
								"MESS_BTN_DETAIL" => "",
								"MESS_NOT_AVAILABLE" => "",
								"SET_BROWSER_TITLE" => "Y",
								"BASKET_URL" => "/personal/basket.php",
								"PAGER_TITLE" => "",
							),
							false,
							array("ACTIVE_COMPONENT" => "Y")
						);?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif?>
<?if((!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU_TOP_SLIDER"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU_TOP_SLIDER"] : COption::GetOptionString("effortless", "QUICK_THEME_MENU_TOP_SLIDER", "Y", SITE_ID)) == "N"):?>
	<script>
		jQuery(function(){
			var	headerTopHeight = $(".header-top").outerHeight(),
			headerHeight = $("header.header.float").outerHeight() + $(".banner").innerHeight();
			$(window).scroll(function() {
				if (($(".header.float").length > 0)) { 
					if(($(this).scrollTop() > headerTopHeight+headerHeight) && ($(window).width() > 767)) {
						$("body").addClass("fixed-header-on");
						$(".header.float").addClass('animated object-visible fadeInDown');
					} else {
						$("body").removeClass("fixed-header-on");
						$("section.main-container").css("marginTop", (0)+"px");
						$(".banner").css("marginTop", (0)+"px");
						$(".page-intro").css("marginTop", (0)+"px");
						$(".page-top").css("marginTop", (0)+"px");
						$(".header.float").removeClass('animated object-visible fadeInDown');
					}
				};
			});
		});
	</script>
	<header class="header <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU_TRANSPARENT"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU_TRANSPARENT"] : COption::GetOptionString("effortless", "QUICK_THEME_MENU_TRANSPARENT", "menu-transparent", SITE_ID))?> <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["MENU"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["MENU"] : COption::GetOptionString("effortless", "QUICK_THEME_MENU", "float", SITE_ID))?> <?=(!empty($_SESSION["QUICK_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["QUICK_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("effortless", "QUICK_THEME_HEADER_MENU_BG", "white", SITE_ID))?> clearfix">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<div class="header-left clearfix">
						<div class="logo">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "#SITE_DIR#include/company-logo.php", 
								)
							);?>
						</div>
					</div>
				</div>
				<div class="col-md-10">
					<div class="header-right clearfix">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
								"ROOT_MENU_TYPE" => "top",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600",
								"MENU_CACHE_USE_GROUPS" => "Y",
								"MENU_CACHE_GET_VARS" => "",
								"MAX_LEVEL" => "4",
								"CHILD_MENU_TYPE" => "podmenu",
								"USE_EXT" => "Y",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "N",
							),
							false
						);?>
					</div>
				</div>
			</div>
		</div>
	</header>
<?endif?>
<?global $arrFilter; $arrFilter=array("!PROPERTY_TOP"=>false);?>