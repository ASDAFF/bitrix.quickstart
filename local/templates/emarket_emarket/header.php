<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$cur_page = $APPLICATION->GetCurPage(true);
$cp = str_replace(SITE_DIR, "/", $cur_page );
$cur_page_arr = explode('/', $cp);

IncludeTemplateLangFile(__FILE__);
CJSCore::RegisterExt('lang_js', array(
    'lang' => '/bitrix/templates/emarket_emarket/lang/'.LANGUAGE_ID.'/js/script.php'
));
CJSCore::Init(array('lang_js'));
?>
<!DOCTYPE html>
<html>
	<head>	
		<meta name="cmsmagazine" content="df80fa5f980c721d845aad3420f1d0dd" />
		<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
		<?
		//include fonts	
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/font/firasans/fonts.css");
		//include jquery-1.11.0 (http://jquery.com/)
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.11.0.min.js");	
		//include formstyler (https://github.com/Dimox/jQueryFormStyler)
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/formstyler/jquery.formstyler.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/formstyler/jquery.formstyler.min.js");
		//include script to check number (http://digitalbush.com/projects/masked-input-plugin/)
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.maskedinput.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/is.mobile.js");
		//include masonry (http://masonry.desandro.com/)
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/masonry.pkgd.min.js");
        //include jscrollpane (https://github.com/vitch/jScrollPane)
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jscrollpane/jquery.mousewheel.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jscrollpane/jquery.jscrollpane.min.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/jscrollpane/jquery.jscrollpane.css');
		//include main script
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/script.js");
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.elevatezoom.js");
		$APPLICATION->ShowHead();
		?>
		<title><?$APPLICATION->ShowTitle();?></title>
		<script>
		 var EmarketSite = {SITE_DIR:'<?=SITE_DIR?>'};
			(function($) {
				$(function() {
					$('select').styler();
				});				 
			})(jQuery);
		</script>
	</head>
<body>
	<div id="panel">
		<?$APPLICATION->ShowPanel();?>
	</div>
<div id="top-panel">
	<div class="wrapper">
	<?$APPLICATION->IncludeComponent(
		"bitrix:menu",
		"top-menu",
		Array(
			"ROOT_MENU_TYPE" => "top",
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "",
			"USE_EXT" => "N",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => array()
		),
	false
	);?>
	<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "emarket_auth", array(
		"REGISTER_URL" => SITE_DIR."auth/",
		"FORGOT_PASSWORD_URL" => "",
		"PROFILE_URL" => SITE_DIR."personal/",
		"SHOW_ERRORS" => "N"
		),
		false
	);?>
	</div>
</div>
	
<div class="wrapper">
	<header class="header">
		<div class="header-block" itemscope itemtype = "http://schema.org/LocalBusiness">
			<?if ($cur_page == SITE_DIR."index.php"){?><h1 class="site-title"><?}?>
				<a class="<?if($cur_page != SITE_DIR."index.php") echo 'site-title';?>" href="<?=SITE_DIR?>" itemprop = "name">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","", Array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/company_name.inc.php","EDIT_TEMPLATE" => ""));?>
				</a>
			<?if ($cur_page == SITE_DIR."index.php"){?></h1><?}?>
			<div class="site-feedback">
				<a href="#" id="feedback-call"><?=GetMessage("H_CALLBACK")?></a>
				<a href="#" id="feedback-message"><?=GetMessage("H_SEND_MSG")?></a>
			</div>
			<div class="site-telephone">
				<a href="<?=SITE_DIR?>contacts/" itemprop = "telephone">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone_1.inc.php"), false);?>
				</a>
				<a href="<?=SITE_DIR?>contacts/" itemprop = "telephone">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone_2.inc.php"), false);?>
				</a>					
			</div>
		</div>
		<div class="header-block">
			<?if($cur_page != SITE_DIR."index.php") {?>
				<div class="catalog-link">
					<a class="link" href="<?=SITE_DIR?>catalog/"><?=GetMessage("H_CATALOG")?></a><span class="ico arrow-ico"></span>
					<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog-menu", array(
						"ROOT_MENU_TYPE" => "catalog",
						"MENU_CACHE_TYPE" => "N",
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => array(
						),
						"MAX_LEVEL" => "1",
						"CHILD_MENU_TYPE" => "catalog",
						"USE_EXT" => "Y",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N"
						),
						false
					);?>
				</div>
			<?} else {?>
				<div class="catalog-link">
					<a class="link" href="<?=SITE_DIR?>catalog/"><?=GetMessage("H_CATALOG")?></a>
				</div>
			<?}?>
			<?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	".default", 
	array(
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "5",
		"ORDER" => "rank",
		"USE_LANGUAGE_GUESS" => "Y",
		"CHECK_DATES" => "N",
		"SHOW_OTHERS" => "N",
		"PAGE" => "#SITE_DIR#search/index.php",
		"CATEGORY_0_TITLE" => "'".GetMessage("H_CATALOGSEARCH")."'",
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "5",
		),
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "emarket-search-input",
		"CONTAINER_ID" => "emarket-search"
	),
	false
);?>
			<div id="emarket-compare-list" class="h-box">
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.compare.list",
				"",
				Array(
					"AJAX_MODE" => "N",
					"IBLOCK_TYPE" => "catalog",
					"IBLOCK_ID" => "5",
					"DETAIL_URL" => "",
					"COMPARE_URL" => SITE_DIR."catalog/compare.php",
					"NAME" => "CATALOG_COMPARE_LIST",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N"
				),
			false
			);?>
			</div>
			<div id="emarket-basket" class="h-box">
			<?$APPLICATION->IncludeComponent("cart:sale.basket.basket.small", ".default", array(
				"PATH_TO_BASKET" => SITE_DIR."personal/basket/",
				"PATH_TO_ORDER" => SITE_DIR."personal/order.php",
				"SHOW_DELAY" => "N",
				"SHOW_NOTAVAIL" => "N",
				"SHOW_SUBSCRIBE" => "N"
				),
				false
			);?>
			</div>
		</div>
	</header><!-- .header-->

	<div class="middle">
		<?$APPLICATION->IncludeComponent(
			"bitrix:breadcrumb",
			"",
			Array(
				"START_FROM" => "1",
				"PATH" => "",
				"SITE_ID" => "-"
			),
		false
		);?>
		<div class="container">
			<main class="content" style="<?if(($cur_page_arr[1] == "catalog") || ($cur_page_arr[1] == "personal") || ($cur_page_arr[1] == "news")){ echo "padding:0;";}?>">