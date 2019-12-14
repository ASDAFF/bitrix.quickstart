<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;


$typeProperty = $arParams['RSMONOPOLY_BANNER_TYPE'];
$text1Property = $arParams['RSMONOPOLY_TEXT_1'];
$text2Property = $arParams['RSMONOPOLY_TEXT_2'];
$priceProperty = $arParams['RSMONOPOLY_PRICE'];

$types = array('banner', 'text', 'product', 'video');

foreach ($arResult['ITEMS'] as &$arItem) {
    $typeVal = $arItem['PROPERTIES'][$typeProperty]['VALUE_XML_ID'];
    $arItem['BANNER_TYPE'] = in_array($typeVal, $types) ? $typeVal : '';
    $arItem['RS_TEXT1'] = $arItem['DISPLAY_PROPERTIES'][$text1Property]['DISPLAY_VALUE'];
    $arItem['RS_TEXT2'] = $arItem['DISPLAY_PROPERTIES'][$text2Property]['DISPLAY_VALUE'];
    $arItem['RS_PRICE'] = $arItem['DISPLAY_PROPERTIES'][$priceProperty]['DISPLAY_VALUE'];
}
unset($arItem);
$arResult['CHANGE_SPEED'] = (int) $arParams["RSMONOPOLY_OWL_CHANGE_SPEED"] < 1 ?
                                2000 : $arParams["RSMONOPOLY_OWL_CHANGE_SPEED"];

$arResult['CHANGE_DELAY'] = (int) $arParams["RSMONOPOLY_OWL_CHANGE_DELAY"] < 1 ?
                                8000 : $arParams["RSMONOPOLY_OWL_CHANGE_DELAY"];

$arResult['BANNER_TEMPLATE'] = !(empty($arParams['RSMONOPOLY_BANNER_TEMPLATE'])) ? $arParams['RSMONOPOLY_BANNER_TEMPLATE'] : 'wide'; 

if(!empty($arParams['RSMONOPOLY_SIDEBAR']) && $arParams['RSMONOPOLY_SIDEBAR'] == "Y") {
    $arResult['BANNER_TEMPLATE'] = 'htype3';
}

$arResult['BANNER_CLASSES'] = $arResult['BANNER_TEMPLATE'] == "extended" ? 
                                'center extended' : $arResult['BANNER_TEMPLATE'];