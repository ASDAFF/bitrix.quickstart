<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);

// check prokids module install
if(!IsModuleInstalled('redsign.prokids')){
	echo '<span style="color:red;">'.GetMessage('RSGOPRO.ERROR_NOT_INSTALLED_GOPRO').'</span>';
	die();
}
// /check prokids module install

// init devfunc
if(CModule::IncludeModule('redsign.devfunc')){
	RSDevFunc::Init(array('jsfunc'));
}
else{
	echo '<span style="color:red;">'.GetMessage('RSGOPRO.ERROR_NOT_INSTALLED_DEVFUNC').'</span>';
	die();
}

// is main page
$IS_MAIN = 'N';
if($APPLICATION->GetCurPage(true)==SITE_DIR.'index.php')
	$IS_MAIN = 'Y';

// is catalog page
$IS_CATALOG = 'Y';
if(strpos($APPLICATION->GetCurPage(true), SITE_DIR.'catalog/') === false)
	$IS_CATALOG = 'N';

// is personal page
$IS_PERSONAL = 'Y';
if(strpos($APPLICATION->GetCurPage(true), SITE_DIR.'personal/') === false)
	$IS_PERSONAL = 'N';

// is auth page
$IS_AUTH = 'Y';
if(strpos($APPLICATION->GetCurPage(true), SITE_DIR.'auth/') === false)
	$IS_AUTH = 'N';

?><!DOCTYPE html><?
?><html><?
?><head><?
	?><title><?$APPLICATION->ShowTitle()?></title><?
	// Google fonts
	//$APPLICATION->SetAdditionalCSS('http://fonts.googleapis.com/css?family=Open+Sans:700,400,300&subset=latin,cyrillic,cyrillic-ext,latin-ext');
	// for mobile devices
	$APPLICATION->AddHeadString('<meta http-equiv="X-UA-Compatible" content="IE=edge" />');
	$APPLICATION->AddHeadString('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
	// CSS -> media query
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/media.css');
	// jQuery
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery-1.11.0.min.js');
	// jQuery -> Mousewheel
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.mousewheel.min.js');
	// jQuery -> cookie
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.cookie.js');
	// jQuery -> jScrollPane
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jscrollpane/jquery.jscrollpane.min.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/jscrollpane/jquery.jscrollpane.css');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jscrollpane/script.js');
	// jQuery -> JSSor slider
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jssor/jssor.core.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jssor/jssor.utils.js');
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jssor/jssor.slider.min.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/jssor/style.css');
	// jQuery -> Fancybox
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/fancybox/jquery.fancybox.pack.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/fancybox/jquery.fancybox.css');
	// jQuery -> scrollTo
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/scrollto/jquery.scrollTo.min.js');
	// general scripts
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/script.js');
	// offers
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/offers.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/offers.css');
	// popup
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/popup/script.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/popup/style.css');
	// Glass
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/glass/script.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/glass/style.css');
	// yandex share
	$APPLICATION->AddHeadString('<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>');
	// add style for auth pages
	if($IS_AUTH=='Y'){
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/auth.css');
	}
	// bitrix head and scripts
	$APPLICATION->ShowHead();

$adaptive = COption::GetOptionString('redsign.proopt', 'adaptive', 'Y');
$prop_option = COption::GetOptionString('redsign.proopt', 'prop_option', 'line_through');
?><script type="text/javascript">
	// some JS params
	var BX_COOKIE_PREFIX = 'BITRIX_SM_',
		SITE_ID = '<?=SITE_ID?>',
		SITE_DIR = '<?=str_replace('//','/',SITE_DIR);?>',
		SITE_TEMPLATE_PATH = '<?=str_replace('//','/',SITE_TEMPLATE_PATH);?>',
		SITE_CATALOG_PATH = 'catalog',
		RSGoPro_Adaptive = <?=( $adaptive=='Y' ? 'true' : 'false' )?>,
		RSGoPro_FancyCloseDelay = 1000,
		RSGoPro_FancyReloadPageAfterClose = false,
		RSGoPro_OFFERS = {},
		RSGoPro_FAVORITE = {},
		RSGoPro_COMPARE = {},
		RSGoPro_INBASKET = {},
		RSGoPro_STOCK = {},
		RSGoPro_PHONETABLET = "N";
	// messages
	BX.message({
		"RSGOPRO_JS_TO_MACH_CLICK_LIKES":"<?=CUtil::JSEscape(GetMessage('RSGOPRO.JS_TO_MACH_CLICK_LIKES'))?>",
		"RSGOPRO_JS_COMPARE":"<?=CUtil::JSEscape(GetMessage('RSGOPRO.RSGOPRO_JS_COMPARE'))?>",
		"RSGOPRO_JS_COMPARE_IN":"<?=CUtil::JSEscape(GetMessage('RSGOPRO.RSGOPRO_JS_COMPARE_IN'))?>"
	});
</script><?
?></head><?
?><body class="prop_option_<?=$prop_option?><?if($adaptive=="Y"):?> adaptive<?endif;?>"><?
	?><div id="panel"><?=$APPLICATION->ShowPanel()?></div><?
	?><div class="body"><!-- body --><?
		?><div class="tline"></div><?
		?><div id="tpanel" class="tpanel"><?
			?><div class="centering"><?
				?><div class="centeringin clearfix"><?
					?><div class="authandlocation nowrap"><?
					$APPLICATION->IncludeComponent("redsign:autodetect.location", "inheader", array(
						"RSLOC_INCLUDE_JQUERY" => "N",
						"RSLOC_LOAD_LOCATIONS" => "N"
						),
						false
					);
					?><?
					$APPLICATION->IncludeComponent(
						"bitrix:system.auth.form",
						"inheader",
						array(
							"REGISTER_URL" => "#SITE_DIR#auth/",
							"PROFILE_URL" => "#SITE_DIR#personal/profile/"
						)
					);
					?></div><?
					$APPLICATION->IncludeComponent(
						"bitrix:menu",
						"tpanel",
						array(
							"ROOT_MENU_TYPE" => "tpanel",
							"MAX_LEVEL" => "1",
							"CHILD_MENU_TYPE" => "",
							"USE_EXT" => "N",
							"MENU_CACHE_TYPE" => "A",
							"MENU_CACHE_TIME" => "3600",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => Array()
						)
					);
				?></div><?
			?></div><?
		?></div><?
		?><div id="header" class="header"><?
			?><div class="centering"><?
				?><div class="centeringin clearfix"><?
					?><div class="logo column1"><?
						?><div class="column1inner"><?
							?><a href="<?=SITE_DIR?>"><?
								$APPLICATION->IncludeFile(
									SITE_TEMPLATE_PATH."/include_areas/logo.php",
									Array(),
									Array("MODE"=>"html")
								);
							?></a><?
						?></div><?
					?></div><?
					?><div class="phone column1 nowrap"><?
						?><div class="column1inner"><?
							?><i class="icon pngicons mobile_hide"></i><?
							$APPLICATION->IncludeFile(
								SITE_TEMPLATE_PATH."/include_areas/header_phone.php",
								Array(),
								Array("MODE"=>"html")
							);
						?></div><?
					?></div><?
					?><div class="callback column1 nowrap"><?
						?><div class="column1inner"><?
							?><a class="fancyajax fancybox.ajax big" href="#SITE_DIR#nasvyazi/" title="<?=GetMessage("RSGOPRO.NA_SVYAZI_TITLE")?>"><?=GetMessage("RSGOPRO.NA_SVYAZI")?><i class="icon pngicons"></i></a><?
						?></div><?
					?></div><?
					?><div class="favorite column1 nowrap"><?
						?><div class="column1inner"><?
						$APPLICATION->IncludeComponent(
							"redsign:favorite.list",
							"inheader",
							array(
								"CACHE_TYPE" => "N",
								"CACHE_TIME" => "3600",
								"ACTION_VARIABLE" => "topaction",
								"PRODUCT_ID_VARIABLE" => "id"
							),
							false
						);
						?></div><?
					?></div><?
					?><div class="basket column1 nowrap"><?
						?><div class="column1inner"><?
						$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "inheader", array(
							"PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
							"PATH_TO_ORDER" => "#SITE_DIR#personal/order/",
							"SHOW_DELAY" => "Y",
							"SHOW_NOTAVAIL" => "Y",
							"SHOW_SUBSCRIBE" => "Y"
							),
							false
						);
						?></div><?
					?></div><?
				?></div><?
			?></div><?
			?><div class="centering"><?
				?><div class="centeringin clearfix"><?
				$APPLICATION->IncludeComponent("bitrix:menu", "catalog", array(
					"ROOT_MENU_TYPE" => "catalog",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "3600",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "1",
					"CHILD_MENU_TYPE" => "",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N",
					"CATALOG_PATH" => "#SITE_DIR#catalog/",
					"MAX_ITEM" => "9",
					"IS_MAIN" => $IS_MAIN
					),
					false
				);
				?><?
				$APPLICATION->IncludeComponent(
					"bitrix:search.title",
					"inheader",
					array(
						"NUM_CATEGORIES" => "1",
						"TOP_COUNT" => "5",
						"ORDER" => "date",
						"USE_LANGUAGE_GUESS" => "N",
						"CHECK_DATES" => "N",
						"SHOW_OTHERS" => "N",
						"PAGE" => "#SITE_DIR#search/",
						"CATEGORY_0_TITLE" => "",
						"CATEGORY_0" => array(
							0 => "no",
						),
						"SHOW_INPUT" => "Y",
						"INPUT_ID" => "title-search-input",
						"CONTAINER_ID" => "title-search",
						"IBLOCK_ID" => array(
							0 => "#IBLOCK_ID_catalog#",
						),
						"PRICE_CODE" => array(
							0 => "BASE",
							1 => "WHOLE",
							2 => "RETAIL",
							3 => "EXTPRICE",
							4 => "EXTPRICE2",
						),
						"PRICE_VAT_INCLUDE" => "N",
						"OFFERS_FIELD_CODE" => array(
							0 => "NAME",
							1 => "PREVIEW_PICTURE",
							2 => "",
						),
						"OFFERS_PROPERTY_CODE" => array(
							0 => "CML2_LINK",
							1 => "",
						),
						"CONVERT_CURRENCY" => "N",
						"USE_PRODUCT_QUANTITY" => "N",
						"PRODUCT_QUANTITY_VARIABLE" => "quan"
					),
					false
				);
				?></div><?
			?></div><?
		?></div><?
		if($IS_MAIN == 'N'){
			?><div id="title" class="title"><?
				?><div class="centering"><?
					?><div class="centeringin clearfix"><?
						$APPLICATION->IncludeComponent(
							"bitrix:breadcrumb",
							"gopro",
							Array(
								"START_FROM" => "0",
								"PATH" => "",
								"SITE_ID" => "-"
							),
							false
						);
						?><h1 class="pagetitle"><?$APPLICATION->ShowTitle(false)?></h1><?
					?></div><?
				?></div><?
			?></div><!-- /title --><?
		}
		?><div id="content" class="content"><?
			?><div class="centering"><?
				?><div class="centeringin clearfix"><?