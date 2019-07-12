<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE HTML>
<html>
<head>
<title><?$APPLICATION->ShowTitle()?></title>
<link type="image/x-icon" rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/jquery.jscrollpane.css"/>
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/jquery.select.css"/>
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/fonts/fontawesome/font-awesome.css"/>
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/supersized.css" media="screen" />
<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/sharesl.css"/>

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/preloadCssImages.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/sharesl.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.jscrollpane.min.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.scrollTo-1.4.3.1-min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/zoomsl-3.0.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/placeholdersl.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.select.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/tooltip.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/countdownsl.js" ></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/general.js"></script>

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/slideshow/supersized.3.2.6.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/slideshow/supersized.photoartist.js"></script>
<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "contacts",
        "AREA_FILE_RECURSIVE" => "N"
	)
);?>
<?$APPLICATION->IncludeComponent("sergeland:supersized.jquery.plugin", "", array(
	"BANNER_IMAGES_BIG" => array(
		0 => "#SITE_DIR#images/supersized/slides/slide_01.jpg",
		1 => "#SITE_DIR#images/supersized/slides/slide_02.jpg",
		2 => "#SITE_DIR#images/supersized/slides/slide_03.jpg",
		3 => "#SITE_DIR#images/supersized/slides/slide_04.jpg",
		4 => "#SITE_DIR#images/supersized/slides/slide_05.jpg",
		5 => "#SITE_DIR#images/supersized/slides/slide_07.jpg",
		6 => "#SITE_DIR#images/supersized/slides/slide_19.jpg",
		7 => "#SITE_DIR#images/supersized/slides/slide_15.jpg",
		8 => "#SITE_DIR#images/supersized/slides/slide_08.jpg",
		9 => "#SITE_DIR#images/supersized/slides/slide_06.jpg",
		10 => "#SITE_DIR#images/supersized/slides/slide_09.jpg",
		11 => "#SITE_DIR#images/supersized/slides/slide_10.jpg",
		12 => "#SITE_DIR#images/supersized/slides/slide_11.jpg",
		13 => "#SITE_DIR#images/supersized/slides/slide_12.jpg",
		14 => "#SITE_DIR#images/supersized/slides/slide_13.jpg",
		15 => "#SITE_DIR#images/supersized/slides/slide_14.jpg",
		16 => "#SITE_DIR#images/supersized/slides/slide_16.jpg",
		17 => "#SITE_DIR#images/supersized/slides/slide_17.jpg",
		18 => "#SITE_DIR#images/supersized/slides/slide_18.jpg",
		19 => "#SITE_DIR#images/supersized/slides/slide_20.jpg",
		20 => "",
	),
	"AUTOPLAY" => "N",
	"START_SLIDE" => "N",
	"SLIDE_INTERVAL" => "9000",
	"TRANSITION" => "1",
	"TRANSITION_SPEED" => "600",
	"SLIDE_LINKS" => "empty",
	"THEMEVARS_IMAGE_PATH" => "#SITE_DIR#images/supersized/"
	),
	false
);?>
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
<!--/ header -->

<!-- middle -->
<div class="middle cols2_wide">
<table border="0" cellpadding="0" cellspacing="0"><tr>
<td>	
    <!-- sidebar 1 -->
    <div class="sidebar sidebar_1">     
		<? 
		global $arrFilterStreetstyle;
		
		$arrFilterStreetstyle = $_REQUEST["arrFilter"];
		if(!is_array($arrFilterStreetstyle)) $arrFilterStreetstyle = array();
		
		if( array_key_exists("!PROPERTY_NEWPRODUCT", $arrFilterStreetstyle) || (stripos(GetPagePath(), SITE_DIR."new/") !== false && stripos(GetPagePath(), SITE_DIR."new/") == 0) )		
			$arrFilterStreetstyle = array("!PROPERTY_NEWPRODUCT"=>false);
		if( array_key_exists("!PROPERTY_ACTION", $arrFilterStreetstyle) || (stripos(GetPagePath(), SITE_DIR."actions/") !== false && stripos(GetPagePath(), SITE_DIR."actions/") == 0) )		
			$arrFilterStreetstyle = array("!PROPERTY_ACTION"=>false);
		if( array_key_exists("!PROPERTY_SPECIALOFFER", $arrFilterStreetstyle) || (stripos(GetPagePath(), SITE_DIR."sale/") !== false && stripos(GetPagePath(), SITE_DIR."sale/") == 0) )		
			$arrFilterStreetstyle = array("!PROPERTY_SPECIALOFFER"=>false);
				
		$APPLICATION->IncludeComponent("sergeland:catalog.section.filter", ".default", array(
			"IBLOCK_TYPE" => "#IBLOCK_TYPE#",
			"IBLOCK_ID" => "#IBLOCK_ID#",
			"FILTER_NAME" => "arrFilter",
			"FILTER_NAME2" => "arrFilterStreetstyle",
			"HIDE_NOT_AVAILABLE" => "N",
			"PROPERTY_CODE" => array(
				0 => "BREND",
				1 => "COLLECTION",
				2 => "COUNTRY",
				3 => "",
			),
			"SORT_FIELD" => "sort",
			"SORT_FIELD_ORDER" => "asc",
			"SORT_VALUE_ORDER" => "asc",
			"SORT_VALUE" => "sort",
			"SHOW_FIELD" => "Y",
			"SHOW_COUNT_ELEMENT" => "Y",
			"FOLDER" => "#SITE_DIR#catalog/",
			"SEF_URL_SECTION_TEMPLATE" => "#SECTION_CODE#/",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_GROUPS" => "Y",
			"PRICE_CODE" => array(
				0 => "#PRICE_CODE#",
			),
			"SHOW_PRICE" => "Y"
			),
			false
		);?>                            
    </div>
    <!--/ sidebar 1 -->
</td><td>
   	<!-- center part -->
    <div class="content_wrapper">
        <!-- content -->
        <div class="content">               
          <div class="post-item post-detail">  
			<div class="post-title">
				<?if(ERROR_404 == "Y"):?>
					<h1><a href="#"><?=GetMessage("SERGELAND_STREETSTYLE_ERROR_404")?></a></h1>
				<?else:?>
					<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "breadcrumb",
					
							Array(
									"START_FROM" => "1",
									"PATH" => "",
									"SITE_ID" => SITE_ID,
								 ),								 
							false
						);
					?>
				<?endif?>
			</div>
			<div class="entry">                    		
			<div class="divider_thin"></div> 

			