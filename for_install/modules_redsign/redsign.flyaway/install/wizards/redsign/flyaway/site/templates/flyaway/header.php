<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

if(!\Bitrix\Main\Loader::includeModule('redsign.flyaway')) {
    ShowError(Loc::getMessage('RS.FLYAWAY.ERROR_FLYAWAY_NOT_INSTALLED'));
    die();
}
if(!\Bitrix\Main\Loader::includeModule('redsign.devfunc')) {
    ShowError(Loc::getMessage('RS.FLYAWAY.ERROR_DEVFUNC_NOT_INSTALLED'));
    die();
} else {
    RSDevFunc::Init(array('jsfunc'));
}

CJSCore::Init('core');
CJSCore::Init('ls');
CJSCore::Init('ajax');

// options
$sidebarPos = RSFlyaway::getSettings('sidebarPos', 'pos1');
$optionForm = COption::GetOptionString('redsign.flyaway', 'optionFrom', 'module');

// is main page
$IS_MAIN = false;
if ($APPLICATION->GetCurPage(true)==SITE_DIR.'index.php') {
    $IS_MAIN = true;
}

// is catalog page
global $IS_CATALOG;
$IS_CATALOG = false;
if (
    strpos($APPLICATION->GetCurPage(true), SITE_DIR.'catalog/') !== false ||
    strpos($APPLICATION->GetCurPage(true), SITE_DIR.'brands/') !== false
) {
    $IS_CATALOG = true;
}

// hide sidebar
global $HIDE_SIDEBAR;
$HIDE_SIDEBAR = false;
if (($APPLICATION->GetProperty('hidesidebar') == 'Y' || $IS_MAIN)) {
    $HIDE_SIDEBAR = true;
}

// some strings
$Asset = Asset::getInstance();
$Asset->addString('<link href="'.SITE_DIR.'favicon.ico" rel="shortcut icon"  type="image/x-icon" />');
$Asset->addString('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
$Asset->addString('<meta name="viewport" content="width=device-width, initial-scale=1">');
$Asset->addString('<script async type="text/javascript" src="//yastatic.net/share/share.js" charset="'.SITE_CHARSET.'"></script>');
$Asset->addString('<link href="//fonts.googleapis.com/css?family=PT+Sans:400,700|Roboto:500,300,400" rel="stylesheet" type="text/css">');
// add styles

$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/jquery.fancybox.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/owl.carousel.min.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/common.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/custom/style.css');
if($optionForm == "module") {
   $Asset->addCss(SITE_DIR.'include/color.css');
} else {
   $Asset->addCss(SITE_TEMPLATE_PATH.'/styles/color.php');
}

// add js
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/jquery-1.11.3.min.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/bootstrap/bootstrap.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/owl.carousel.min.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/fancybox/jquery.fancybox.pack.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.timer.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.compare.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.toggle.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.fix.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.views.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.basket.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.catalog-elements.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.select-drop.js');
//$Asset->addJs(SITE_TEMPLATE_PATH.'/js/libraries.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/main.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/custom/script.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/rs.offers.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/sticky.jquery.js');

/*  options  */
$preset = rsFlyaway::getSettings('presets', 'preset_1');
//$headType = rsFlyaway::getSettings('menuType', 'type1');
$headType = rsFlyaway::getSettings('openMenuType', 'type1');
//$headType = 'type3';
$filterSide = rsFlyaway::getSettings('filterSide', 'left');
//$blackMode = getSettings('blackMode', 'N');
$RSFichi = rsFlyaway::getSettings('Fichi', 'Y');
$RSSmallBanners = rsFlyaway::getSettings('SmallBanners', 'Y');
$RSNew = rsFlyaway::getSettings('New', 'Y');
$RSPopularItem = rsFlyaway::getSettings('PopularItem', 'Y');
$RSService = rsFlyaway::getSettings('Service', 'Y');
$RSAboutAndReviews = rsFlyaway::getSettings('AboutAndReviews', 'Y');
$RSNews = rsFlyaway::getSettings('News', 'Y');
$RSPartners = rsFlyaway::getSettings('Partners', 'Y');
$RSGallery = rsFlyaway::getSettings('Gallery', 'Y');
$RSSidemenuType = rsFlyaway::getSettings('sidemenuType', 'dark');
$RSStickyHeader = rsFlyaway::getSettings('StickyHeader', 'Y');

/*  /options  */


?><!DOCTYPE html>
<html>
<head>
	<?$APPLICATION->IncludeFile(SITE_DIR."include/template/head_start.php",array(),array("MODE"=>"html"))?>
	<title><?php $APPLICATION->ShowTitle() ?><?=$CSiteGetByID['SITE_NAME']?></title>
	<?php $APPLICATION->ShowHead(); ?>

	<script type="text/javascript">
		// some JS params
		var SITE_ID = '<?=SITE_ID?>',
			SITE_DIR = '<?=str_replace('//','/',SITE_DIR);?>',
			SITE_TEMPLATE_PATH = '<?=str_replace('//','/',SITE_TEMPLATE_PATH);?>',
			BX_COOKIE_PREFIX = 'BITRIX_SM_',
			FLYAWAY_COUNT_COMPARE = 0,
			rsFlyaway_COUNT_FAVORITE = 0,
			rsFlyaway_COUNT_BASKET = 0,
				rsFlyaway_PRODUCTS = {},
				rsFlyaway_OFFERS = {},
				rsFlyaway_FAVORITE = {},
				rsFlyaway_COMPARE = {},
				rsFlyaway_INBASKET = {},
				rsFlyaway_STOCK = {};
		// messages
		BX.message({
			"RSFLYAWAY_JS_REQUIRED_FIELD":"<?=CUtil::JSEscape(Loc::getMessage('RS.FLYAWAY.JS_REQUIRED_FIELD'))?>",
            "RSFLYAWAY_PRODUCT_ADDING2BASKET":"<?=CUtil::JSEscape(Loc::getMessage('RS.FLYAWAY.PRODUCT_ADDING2BASKET'))?>"
		});
	</script>
	<?$APPLICATION->IncludeFile(SITE_DIR."include/template/head_end.php",array(),array("MODE"=>"html"))?>
</head>
<body class="<?=$preset?> <?php if($headType == 'type3') echo 'is--sidenav';  ?> <?=($RSSidemenuType == 'white')? ' preset_10' : ' preset_11'?> side-<?=$RSSidemenuType?>">
  <?$APPLICATION->IncludeFile(SITE_DIR."include/template/body_start.php",array(),array("MODE"=>"html"))?>
  <div id="panel"><?=$APPLICATION->ShowPanel()?></div>

  <div class="wrapper">
    <div class="headline">
      <div class="container">
        <div class="row topline">
          <div class="col-xs-12">
            <div class="row">

              <div class="col col-xs-12 col-md-12 col-lg-12 hidden-xs">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", "flyaway", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/header/topline.php", "EDIT_TEMPLATE" => ""), false);?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?$APPLICATION->IncludeComponent("bitrix:main.include", "flyaway", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/header/flyaway_head.php", "EDIT_TEMPLATE" => ""), false);?>
    <?php if ($IS_MAIN): ?>
        <div class="main-banners">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include_areas/main_banners.php"), false);?>
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="row">
            <div class="col col-md-12 maincontent <?php $APPLICATION->ShowViewContent('sidebar_wrap'); ?> sidebar-wrap--<?=$filterSide?>">
                <?php if (!$IS_MAIN): ?>
                    <div class="page-title  ">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:breadcrumb",
                                "flyaway",
                                array()
                            );?>

                            <h1><?$APPLICATION->ShowTitle(false)?></h1>
                            <?php $APPLICATION->ShowViewContent('brand-preview'); ?>
                    </div>
                <?php endif; ?>
                <?php if (!$HIDE_SIDEBAR && !$IS_CATALOG):?>
                <div id="sidebar" class="fixsidebar">
                        <div>
                            <?$APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/menu.php",array("HEAD_TYPE"=>$headType),array("MODE"=>"html"));?>
                        </div>
                        <div class="hidden-xs hidden-sm">
                            <?$APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/widgets.php",array(),array("MODE"=>"html"));?>
                            <?$APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/text.php",array(),array("MODE"=>"html"));?>
                        </div>
                </div>
                <?php elseif($IS_CATALOG): ?>
                    <?php $APPLICATION->ShowViewContent('catalog_sidebar'); ?>
                <?php endif; ?>
                <div class="content">
