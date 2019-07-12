<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!\Bitrix\Main\Loader::includeModule('redsign.flyaway')) {
	return;
}

$arParams['HEAD_TYPE'] = RsFlyaway::getSettings('headType', 'type1');
$arParams['FILTER_TYPE'] = RsFlyaway::getSettings('filterType', 'ftype1');
$arParams['USE_FILTER'] = ( $arParams['FILTER_TYPE']!='ftype0' ? 'Y' : 'N' );

// have sidebar?
$arResult['SIDEBAR'] = 'N';

if ($arParams["HEAD_TYPE"] == 'type3' || $arParams['FILTER_TYPE'] == 'ftype1') {
	$arResult['SIDEBAR'] = 'Y';
}
// /have sidebar?

$arResult['FAVORITE_URL'];

$arParams['TEMPLATE_AJAX_ID'] = 'js-ajaxcatalog';
