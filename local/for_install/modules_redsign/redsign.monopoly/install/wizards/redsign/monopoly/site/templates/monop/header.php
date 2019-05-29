<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization;
use \Bitrix\Main\Page\Asset;

Localization\Loc::loadMessages(__FILE__);

if(!\Bitrix\Main\Loader::includeModule('redsign.monopoly')) {
	ShowError( GetMessage('RS.MONOPOLY.ERROR_MONOPOLY_NOT_INSTALLED') );
	die();
}
if(!\Bitrix\Main\Loader::includeModule('redsign.devfunc')) {
	ShowError( GetMessage('RS.MONOPOLY.ERROR_DEVFUNC_NOT_INSTALLED') );
	die();
} else {
	RSDevFunc::Init(array('jsfunc'));
}

CJSCore::Init('ajax');

// monopoly options
$blackMode = RSMonopoly::getSettings('blackMode', 'N' );
$headType = RSMonopoly::getSettings('headType', 'type1');
$filterType = RSMonopoly::getSettings('filterType', 'ftype0');
$sidebarPos = RSMonopoly::getSettings('sidebarPos', 'pos1');
global $IS_CATALOG, $IS_CATALOG_SECTION;
$IS_CATALOG = false;
$IS_CATALOG_SECTION = false;

// is main page
$IS_MAIN = false;
if( $APPLICATION->GetCurPage(true)==SITE_DIR.'index.php' )
	$IS_MAIN = true;

// is catalog page
$IS_CATALOG = true;
if( strpos($APPLICATION->GetCurPage(true), SITE_DIR.'catalog/')===false )
	$IS_CATALOG = false;

// get site data
$cache = new CPHPCache();
$cache_time = 86400;
$cache_id = 'CSiteGetByID'.SITE_ID;
$cache_path = '/siteData/';
if( $cache_time>0 && $cache->InitCache($cache_time, $cache_id, $cache_path) ) {
	$res = $cache->GetVars();
	if( is_array($res["CSiteGetByID"]) && (count($res["CSiteGetByID"])>0) )
		$CSiteGetByID = $res["CSiteGetByID"];
}
if(!is_array($CSiteGetByID)) {
	$rsSites = CSite::GetByID(SITE_ID);
	$CSiteGetByID = $rsSites->Fetch();
	if($cache_time>0) {
		$cache->StartDataCache($cache_time, $cache_id, $cache_path);
		$cache->EndDataCache(array("CSiteGetByID"=>$CSiteGetByID));
	}
}

// some strings
$Asset = Asset::getInstance();
$Asset->addString('<link href="'.SITE_DIR.'favicon.ico" rel="shortcut icon"  type="image/x-icon" />');
$Asset->addString('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
$Asset->addString('<meta name="viewport" content="width=device-width, initial-scale=1">');
$Asset->addString('<script async type="text/javascript" src="//yastatic.net/share/share.js" charset="'.SITE_CHARSET.'"></script>');
$Asset->addString('<link href="http://fonts.googleapis.com/css?family=PT+Sans:400,700|Roboto:500,300,400" rel="stylesheet" type="text/css">');
// add styles
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/style.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/owl.carousel.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/jquery.fancybox.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/header.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/sidebar.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/footer.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/content.css');
$Asset->addCss(SITE_TEMPLATE_PATH.'/styles/color.css'); // color scheme
$Asset->addCss(SITE_TEMPLATE_PATH.'/custom/style.css');
// add scripts
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/jquery-1.11.2.min.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/bootstrap/bootstrap.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/owl.carousel.min.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/fancybox/jquery.fancybox.pack.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/js/script.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/custom/script.js');

?><!DOCTYPE html><?
?><html><?
?><head><?
	$APPLICATION->ShowHead();
	?>
	    <title itemprop="name">
    <?php
    $APPLICATION->ShowTitle();
    if ($CSiteGetByID['SITE_NAME'] != ''):
        echo ' | '. $CSiteGetByID['SITE_NAME'];
    endif;
    ?>
    </title>
	<script type="text/javascript">
	// some JS params
	var SITE_ID = '<?=SITE_ID?>',
		SITE_DIR = '<?=str_replace('//','/',SITE_DIR);?>',
		SITE_TEMPLATE_PATH = '<?=str_replace('//','/',SITE_TEMPLATE_PATH);?>',
		BX_COOKIE_PREFIX = 'BITRIX_SM_',
		RS_MONOPOLY_COUNT_COMPARE = 0,
		RS_MONOPOLY_COUNT_FAVORITE = 0,
		RS_MONOPOLY_COUNT_BASKET = 0;
	// messages
	BX.message({
		"RSMONOPOLY_JS_REQUIRED_FIELD":"<?=CUtil::JSEscape(GetMessage('RS.MONOPOLY.JS_REQUIRED_FIELD'))?>"
	});
</script><?
?></head>
<body class="<?if($blackMode=='Y'):?>blackMode<?endif;?>">
	
	<div id="panel"><?=$APPLICATION->ShowPanel()?></div>

	<?$APPLICATION->IncludeFile(SITE_DIR."include_areas/body_start.php",array(),array("MODE"=>"html"));?>

	<div class="wrapper">
		<div class="container">
			<div class="row topline">
				<div class="col-md-12 text-right hidden-xs hidden-sm">
					<?$APPLICATION->IncludeFile(SITE_DIR."include_areas/top_line.php",array(),array("MODE"=>"html"));?>
				</div>
			</div>
		</div>

<?$APPLICATION->IncludeComponent("bitrix:main.include", "monopoly", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/header/head_type1_menu.php", "EDIT_TEMPLATE" => ""),	false);?>

<?php
if($IS_MAIN && $headType != 'type3') {
	include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/main_banners.php');
}
?>

<!-- container --><div class="container">

<div class="row <?if(!$IS_MAIN):?> notmain<?endif;?>">
<div class="col col-md-9<?=($sidebarPos=='pos1' ? ' col-md-push-3' : '' )?> maincontent">

<?php
if($IS_MAIN && $headType == 'type3') {
	include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/main_banners.php');
}
?>

<?if(!$IS_MAIN):?>
<div class="js-brcrtitle"><?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/breadcrumb.php'); ?></div>
<div class="js-ttl">
<div class="page-header"><h1><?$APPLICATION->ShowTitle(false)?></h1></div>
</div>
<div class="sidebar-menu visible-xs visible-sm">
    <?php
	if($headType != 'type3') {
        $APPLICATION->ShowViewContent('sidebar_menu');
    } elseif(!$IS_CATALOG) {
       include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/sidebar_menu.php');
    }
	?>
</div>
<?endif;?>