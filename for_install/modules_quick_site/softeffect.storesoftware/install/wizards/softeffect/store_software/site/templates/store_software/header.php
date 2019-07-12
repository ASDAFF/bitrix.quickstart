<?
IncludeTemplateLangFile(__FILE__);
$APPLICATION->IncludeFile('#SITE_DIR#admin-config/seo.php'); global $seo_title; global $seo_descr; global $seo_keywords;
require $_SERVER['DOCUMENT_ROOT'].'#SITE_DIR#admin-config/config.php';
CModule::AddAutoloadClasses( 
	'',
	array(
		'CSofteffect' => '#SITE_DIR#admin-config/functions.php', 
	) 
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET?>">
    <title><?$APPLICATION->ShowTitle()?> <?=$seo_title?></title>
    <meta name="keywords" content="<?$APPLICATION->ShowProperty('keywords')?>, <?=$seo_keywords?>" />
    <meta name="description" content="<?$APPLICATION->ShowProperty('description')?>. <?=$seo_descr?>" />
	<link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/main.css" media="all, screen" />
    <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/jqModal.css" />
    <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/js/shadowbox/shadowbox.css" />
    <!--[if IE]><link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/ie.css" media="all, screen" /><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/ie7.css" media="all, screen" /><![endif]-->
    <link type="text/css" rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/print.css" media="print" />
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.tools.min.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?=SITE_TEMPLATE_PATH?>/js/common.min.js"></script>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/img_magic.js"></script>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/shadowbox/shadowbox.js"></script>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/scripts.js"></script>
    <?$APPLICATION->ShowHead();?>
</head>
<?
$bodyClass = '';
if (CSite::InDir('#SITE_DIR#blog/')) {
	$bodyClass = 'blog-page';
} elseif (CSite::InDir('#SITE_DIR#basket/')) {
	$bodyClass = 'basket_main';
}
?>
<body<? if (strlen($bodyClass)>0) { ?> class="<?=$bodyClass?>"<? } ?>>
<?
$APPLICATION->ShowPanel();

CModule::IncludeModule('iblock'); 
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

if ($_REQUEST['clear_viewed_products']=='Y') { // ochischaem "nedavno smotreli"
    CSaleViewedProduct::DeleteForUser(CSaleBasket::GetBasketUserID());
}

CAjax::Init();
?>
<div id="container">
    <div id="header">
        <div class="floatright">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "top_super", array(
	"ROOT_MENU_TYPE" => "top_super",
	"MENU_CACHE_TYPE" => "A",
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
        </div>
        <div class="iconbox order"><a class="modalInput" href="#" rel="#phone"><?=GetMessage('CALLME');?></a></div>
		<div id="corp"><a href="#SITE_DIR#about/corporate_clients/"><?=GetMessage('CORP_CLIENTS');?> &raquo;</a></div>
		<div id="logo"><?$APPLICATION->IncludeFile('#SITE_DIR#include/company_logo.php', array(), array('MODE'=>'html')); ?></div>
        <div id="salesline"><?=GetMessage('PHONE');?>: <b><?$APPLICATION->IncludeFile('#SITE_DIR#include/telephone.php', array(), array('MODE'=>'html')); ?></b></div>
        <div class="comp_name"><?$APPLICATION->IncludeFile('#SITE_DIR#include/company_name.php', array(), array('MODE'=>'html')); ?></div>
	
		<div class="modal" id="phone"></div>
		<div class="modal" id="oneclick"></div>  
		<div class="modal" id="rating"></div> 

		<script type="text/javascript">
			jsAjaxUtil.InsertDataToNode('#SITE_DIR#include/phone-sbox.php', 'phone', false);
		</script>
        <?$APPLICATION->IncludeComponent("softeffect:catalog.toplevel", ".default", array(
	"IBLOCK_TYPE" => "sw_catalog",
	"IBLOCK" => "#sw_software#",
	"MAX_COUNT" => "15",
	"BRANDS_URL" => "#SITE_DIR#brends/",
	"CATALOG_SECTION" => $_REQUEST['SECTION']
	),
	false
);?>
        <div id="searchbar" class="noprint">
<?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
    "ROOT_MENU_TYPE" => "top",
    "MENU_CACHE_TYPE" => "A",
    "MENU_CACHE_TIME" => "3600",
    "MENU_CACHE_USE_GROUPS" => "Y",
    "MENU_CACHE_GET_VARS" => "",
    "MAX_LEVEL" => "1",
    "CHILD_MENU_TYPE" => "left",
    "USE_EXT" => "N",
    "DELAY" => "N",
    "ALLOW_MULTI_SELECT" => "N",
    ),
    false
);?>

<?$APPLICATION->IncludeComponent("bitrix:search.title", ".default", Array(
    "NUM_CATEGORIES" => "1",
    "TOP_COUNT" => "5",
    "ORDER" => "date",
    "USE_LANGUAGE_GUESS" => "Y",
    "CHECK_DATES" => "N",
    "SHOW_OTHERS" => "Y",
    "PAGE" => "#SITE_DIR#search/",
    "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
    "CATEGORY_0_TITLE" => GetMessage('SE_SEARCH_TITLE_CATALOG'),
    "CATEGORY_0" => array(
        0 => "sw_catalog",
    ),
    "CATEGORY_0_sw_catalog" => array(
        0 => "#sw_software#",
    ),
    "SHOW_INPUT" => "Y",
    "INPUT_ID" => "title-search-input",
    "CONTAINER_ID" => "search",
    ),
    false
);?>
        </div>
    </div>
    <div class="clearfloat"></div>
	<div id="enclosure">
		<div id="content" <? if ($APPLICATION->GetCurDir()!='#SITE_DIR#') { ?>class="pagebox iepageboxfix"<? }?>>
		<? if ($APPLICATION->GetCurDir()!='#SITE_DIR#') { ?>
			<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "cepo4ka", array(
					"START_FROM" => "0",
					"PATH" => "",
					"SITE_ID" => "-"
				),
				false
			); ?>
			<? if (strpos($APPLICATION->GetCurDir(), '#SITE_DIR#catalog/')===FALSE) { ?>
				<h1><?$APPLICATION->ShowTitle();?></h1>
			<? } ?>
		<? } ?>