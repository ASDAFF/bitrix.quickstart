<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");
$wizTemplateId = COption::GetOptionString("main", "wizard_template_id", "bejetstore_purple_white", SITE_ID);
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR?>favicon.ico" />
	<?echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
	$APPLICATION->ShowMeta("robots", false, true);
	$APPLICATION->ShowMeta("keywords", false, true);
	$APPLICATION->ShowMeta("description", false, true);
	?>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<?
	$APPLICATION->ShowCSS(true, true);
	$APPLICATION->ShowHeadStrings();
	$APPLICATION->ShowHeadScripts();
	//jquery
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.11.1.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
	//bootstrap
	if($_COOKIE["mobile"] != "mobile"){
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
	}else{
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-mobile.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap-mobile.js");
	}
	//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-theme.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/jquery-ui.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/jquery-ui.structure.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/jquery-ui.theme.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/plugins.css");?>
<link rel="stylesheet" type="text/css" href="<?=CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH."/colors.css")?>" />
	<?
	
	//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/html5shiv.js");
	//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/plugins.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/script.js");
	?>
	<title><?$APPLICATION->ShowTitle()?></title>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<header class="bj-page-header">
	<section class="bj-page-header__top">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-5 col-xs-12 bj-logo-space">
					<?if($curPage == SITE_DIR."index.php"):?>
						<span class="bj-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></span>
					<?else:?>
						<a href="<?=SITE_DIR?>" class="bj-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></a>
					<?endif;?>
					<a href="<?=SITE_DIR?>info/" class="visible-xs-inline-block visible-sm-inline-block bj-logo-space__icon bj-info-icon"></a>
					<?$APPLICATION->IncludeComponent("bitrix:menu", "personal_menu", array(
						"ROOT_MENU_TYPE" => ($USER->IsAuthorized() ? "personal_menu_auth" : "personal_menu_not_auth"),
						"MENU_CACHE_TYPE" => "A",
						"MENU_CACHE_TIME" => "36000000",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => array(
						),
						"MAX_LEVEL" => "2",
						"CHILD_MENU_TYPE" => ($USER->IsAuthorized() ? "personal_menu_auth" : "personal_menu_not_auth"),
						"USE_EXT" => "Y",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N"
						),
						false
					);?>
					<?
						$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "top", array(
						"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
						"PATH_TO_PERSONAL" => SITE_DIR."personal/",
						"SHOW_PERSONAL_LINK" => "N"
						),
						false,
						Array('')
						);
					?>
<?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"simple", 
	array(
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "5",
		"CHECK_DATES" => "N",
		"SHOW_OTHERS" => "Y",
		"PAGE" => SITE_DIR."catalog/",
		"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "2",
		),
		"CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "search",
		"ORDER" => "date",
		"USE_LANGUAGE_GUESS" => "Y",
		"PRICE_CODE" => array(
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SHOW_PREVIEW" => "Y",
		"CONVERT_CURRENCY" => "N"
	),
	false
);?>					
				</div>
				<div class="col-md-5 hidden-sm hidden-xs">
				<?$APPLICATION->IncludeComponent("bitrix:menu", "top_menu", array(
					"ROOT_MENU_TYPE" => "top",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "2",
					"CHILD_MENU_TYPE" => "top",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
					),
					false
				);?>
				</div>
				<div class="col-md-2 hidden-sm hidden-xs bj-phone">
					<span class="glyphicon glyphicon-earphone"></span>
					<span class="bj-phone__num">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?>
						<span class="bj-phone__time"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></span>
					</span>
				</div>
			</div>
		</div>
	</section>
	<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog_menu", array(
		"ROOT_MENU_TYPE" => "catalog",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "catalog",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
		),
		false
	);?>
	<section class="bj-page-header__submenu">
		<div class="container-fluid">
			<div class="row">
				
				<div class="col-md-2 col-xs-4">
					<a href="<?=SITE_DIR?>catalog/" class="bj-page-header__menu-link bj-icon-link">
						<span class="bj-icon i-menu bj-icon-link__icon"></span>
						<span class="bj-icon-link__link"><?=GetMessage("CATALOG")?></span>
					</a>
				</div>
						
				<?$APPLICATION->IncludeComponent("bitrix:menu", "lookbook", array(
					"ROOT_MENU_TYPE" => "lookbook",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "2",
					"CHILD_MENU_TYPE" => "",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
					),
					false
				);?>
				
				
<?global $addFilter;?>
<?include_once($_SERVER["DOCUMENT_ROOT"].SITE_DIR."catalog/filter_array.php");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:search.tags.cloud",
	"top_tags",
	Array(
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => "C0C0C0",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "Y",
		"COLOR_TYPE" => "Y",
		"WIDTH" => "100%",
		"SORT" => "NAME",
		"PAGE_ELEMENTS" => "5",
		"PERIOD" => "",
		"URL_SEARCH" => SITE_DIR."catalog/",
		"TAGS_INHERIT" => "Y",
		"CHECK_DATES" => "N",
		"FILTER_NAME" => "addFilter",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"arrFILTER" => array()
	),
false
);?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "campaign", array(
	"ROOT_MENU_TYPE" => "campaign",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
			</div>
		</div>
	</section>
</header>
<div class="bj-page-content container-fluid">
	<div class="bj-top-decoration"></div>
<?if($APPLICATION->GetCurDir() != SITE_DIR):?>
<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
		"START_FROM" => "0",
		"PATH" => "",
		"SITE_ID" => "-"
	),
	false,
	Array('HIDE_ICONS' => 'Y')
);?>
<?endif;?>
<?if ($curPage != SITE_DIR."index.php"):?>
<?if(strpos($APPLICATION->GetCurDir(),"/catalog/") !== false):?>
<div class="row">
	<div class="col-sm-6">
	<h1><?=$APPLICATION->ShowTitle(false);?></h1>
	</div>
	<div class="col-sm-6">
	<?$APPLICATION->ShowViewContent("section_tags_position");?>
	</div>
</div>
<?else:?>
<h1><?=$APPLICATION->ShowTitle(false);?></h1>
<?endif;?>
<?endif?>