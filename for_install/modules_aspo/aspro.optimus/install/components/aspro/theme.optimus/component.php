<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule('aspro.optimus')){
	die;
}

global $TEMPLATE_OPTIONS, $SITE_THEME, $THEME_SWITCHER, $USER;	
$arResult = array();
if(($arResult["ITEMS"] = $TEMPLATE_OPTIONS) && is_array($arResult["ITEMS"])){
	foreach($arResult["ITEMS"] as $i => $value){
		if($value['ID'] === 'LOGO_IMAGE' || $value['ID'] === 'FAVICON_IMAGE' || $value['ID'] === 'APPLE_TOUCH_ICON_IMAGE'){
			unset($arResult["ITEMS"][$i]);
		}
	}
}

if($THEME_SWITCHER == "Y"){
	\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/spectrum.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/spectrum.css');
	$this->IncludeComponentTemplate();
}

return $TEMPLATE_OPTIONS;
?>
