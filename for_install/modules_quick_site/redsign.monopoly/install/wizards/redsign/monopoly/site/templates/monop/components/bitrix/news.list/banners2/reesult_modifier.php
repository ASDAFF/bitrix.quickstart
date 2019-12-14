<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$typeProperty = $arParams['RSMONOPOLY_BANNER_TYPE'];
$types = array('text', 'product', 'video');

foreach ($arResult['ITEMS'] as &$arItem) {
    $typeVal = $arItem['PROPERTIES'][$typeProperty]['VALUE_XML_ID'];
    $arItem['BANNER_TYPE'] = in_array($typeVal, $types) ? $typeVal : '';
}
unset($arItem);

$arResult['CHANGE_SPEED'] = (int) $arParams["RSMONOPOLY_OWL_CHANGE_SPEED"]