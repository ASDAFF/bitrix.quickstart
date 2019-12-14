<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$OFFER_IBLOCK_ID = 0;
if ($arResult['CATALOG']['PRODUCT_IBLOCK_ID'] == '0') {
	$IBLOCK_ID = $arResult['CATALOG']['IBLOCK_ID'];
} else {
	$IBLOCK_ID = $arResult['CATALOG']['PRODUCT_IBLOCK_ID'];
	$OFFER_IBLOCK_ID = $arResult['CATALOG']['IBLOCK_ID'];
}
if ($arParams['ICON_MEN_PROP'] != '' && $arParams['ICON_MEN_PROP'] != '-') {
	$arParams['ICON_MEN_PROP'] = array($IBLOCK_ID => $arParams['ICON_MEN_PROP']);
} else {
    $arParams['ICON_MEN_PROP'] = array();
}

if ($arParams['ICON_WOMEN_PROP'] != '' && $arParams['ICON_WOMEN_PROP'] != '-') {
	$arParams['ICON_WOMEN_PROP'] = array($IBLOCK_ID => $arParams['ICON_WOMEN_PROP']);
} else {
    $arParams['ICON_WOMEN_PROP'] = array();
}

if ($arParams['ICON_NOVELTY_PROP'] != '' && $arParams['ICON_NOVELTY_PROP'] != '-') {
	$arParams['ICON_NOVELTY_PROP'] = array($IBLOCK_ID => $arParams['ICON_NOVELTY_PROP']);
} else {
    $arParams['ICON_NOVELTY_PROP'] = array();
}

if ($arParams['ICON_DEALS_PROP'] != '' && $arParams['ICON_DEALS_PROP'] != '-') {
	$arParams['ICON_DEALS_PROP'] = array($IBLOCK_ID => $arParams['ICON_DEALS_PROP']);
} else {
    $arParams['ICON_DEALS_PROP'] = array();
}

if ($arParams['ICON_DISCOUNT_PROP'] != '' && $arParams['ICON_DISCOUNT_PROP'] != '-') {
	$arParams['ICON_DISCOUNT_PROP'] = array($IBLOCK_ID => $arParams['ICON_DISCOUNT_PROP']);
} else {
    $arParams['ICON_DISCOUNT_PROP'] = array();
}

if ($arParams['ADDITIONAL_PICT_PROP'] != '' && $arParams['ADDITIONAL_PICT_PROP'] != '-') {
	$arParams['ADDITIONAL_PICT_PROP'] = array($IBLOCK_ID => $arParams['ADDITIONAL_PICT_PROP']);
} else {
    $arParams['ADDITIONAL_PICT_PROP'] = array();
}

if ($arParams['BRAND_PROP'] != '' && $arParams['BRAND_PROP'] != '-') {
	$arParams['BRAND_PROP'] = array($IBLOCK_ID => $arParams['BRAND_PROP']);
} else {
    $arParams['BRAND_PROP'] = array();
}

if ($arParams['AJAX_FILTER_PROPS'] != '' && $arParams['AJAX_FILTER_PROPS'] != '-') {
	$arParams['AJAX_FILTER_PROPS'] = array($IBLOCK_ID => $arParams['AJAX_FILTER_PROPS']);
}

if ($OFFER_IBLOCK_ID) {
	if ($arParams['OFFER_ADDITIONAL_PICT_PROP'] != '' && $arParams['OFFER_ADDITIONAL_PICT_PROP'] != '-') {
		if (is_array($arParams['ADDITIONAL_PICT_PROP'])) {
			$arParams['ADDITIONAL_PICT_PROP'][$OFFER_IBLOCK_ID] = $arParams['OFFER_ADDITIONAL_PICT_PROP'];
		}
	}
	if (is_array($arParams['OFFER_TREE_PROPS'])) {
		$arProps = $arParams['OFFER_TREE_PROPS'];
		unset($arParams['OFFER_TREE_PROPS']);
		$arParams['OFFER_TREE_PROPS'] = array($OFFER_IBLOCK_ID => $arProps);
	}
    if (is_array($arParams['OFFER_TREE_BTN_PROPS'])) {
        $arProps = $arParams['OFFER_TREE_BTN_PROPS'];
        unset($arParams['OFFER_TREE_BTN_PROPS']);
        $arParams['OFFER_TREE_BTN_PROPS'] = array($OFFER_IBLOCK_ID => $arProps);
    }
	if (is_array($arParams['OFFER_TREE_COLOR_PROPS'])) {
		$arProps = $arParams['OFFER_TREE_COLOR_PROPS'];
		unset($arParams['OFFER_TREE_COLOR_PROPS']);
		$arParams['OFFER_TREE_COLOR_PROPS'] = array($OFFER_IBLOCK_ID => $arProps);
	}
}

$sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/result_modifier.php';
if (file_exists($sTemplateExtPath)) {
    include($sTemplateExtPath);    
}