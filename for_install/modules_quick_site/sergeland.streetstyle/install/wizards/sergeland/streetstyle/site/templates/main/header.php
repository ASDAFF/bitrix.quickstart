<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE HTML>
<html>
<head>
<title><?$APPLICATION->ShowTitle()?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/fonts/fontawesome/font-awesome.css"/>
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/sharesl.css"/>

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/preloadCssImages.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/sharesl.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/zoomsl-3.0.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.select.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.jscrollpane.min.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/placeholdersl.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/tooltip.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/countdownsl.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/general.js"></script>

<link type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/supersized.css" rel="stylesheet"  media="screen" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/slideshow/supersized.3.2.6.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/slideshow/supersized.photoartist.js"></script>

<?$APPLICATION->ShowHead();?>
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/ie.css" /><![endif]-->
</head>

<body><div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="header">	
	<div class="header-info">
		<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", ".default", array(
			"REGISTER_URL" => "",
			"FORGOT_PASSWORD_URL" => "",
			"PROFILE_URL" => "#SITE_DIR#personal/",
			"SHOW_ERRORS" => "N"
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:search.title", "visual", array(
			"NUM_CATEGORIES" => "1",
			"TOP_COUNT" => "3",
			"ORDER" => "rank",
			"USE_LANGUAGE_GUESS" => "Y",
			"CHECK_DATES" => "Y",
			"SHOW_OTHERS" => "N",
			"PAGE" => "#SITE_DIR#search/",
			"CATEGORY_0_TITLE" => "#CATEGORY_0_TITLE#",
			"CATEGORY_0" => array(
				0 => "iblock_catalog",
			),
			"CATEGORY_0_iblock_catalog" => array(
				0 => "all",
			),
			"SHOW_INPUT" => "Y",
			"INPUT_ID" => "title-search-input",
			"CONTAINER_ID" => "title-search",
			"PRICE_CODE" => array(
				0 => "#PRICE_CODE#",
			),
			"PRICE_VAT_INCLUDE" => "Y",
			"PREVIEW_TRUNCATE_LEN" => "150",
			"SHOW_PREVIEW" => "Y",
			"PREVIEW_WIDTH" => "75",
			"PREVIEW_HEIGHT" => "75",
			"CONVERT_CURRENCY" => "Y",
			"CURRENCY_ID" => "RUB"
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include/telephone.php", 
			)
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "#SITE_DIR#include/socnet.php", 
			)
		);?>		
	</div>
    <div class="logo">
	<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => "#SITE_DIR#include/company_logo.php", 
		)
	);?>
    </div>    
    <div class="header_play_box"></div>   
    <div class="topmenu">
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
		<?$APPLICATION->IncludeComponent("bitrix:menu", "right", Array(
			"ROOT_MENU_TYPE" => "right",	
			"MENU_CACHE_TYPE" => "A",	
			"MENU_CACHE_TIME" => "3600",	
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => "",	
			"MAX_LEVEL" => "4",	
			"CHILD_MENU_TYPE" => "podmenu",	
			"USE_EXT" => "N",	
			"DELAY" => "N",	
			"ALLOW_MULTI_SELECT" => "N",	
			),
			false
		);?>		
    <div class="clear"></div>
	</div>        
</div>
<a id="header_pane"></a>
<!--/ header -->


