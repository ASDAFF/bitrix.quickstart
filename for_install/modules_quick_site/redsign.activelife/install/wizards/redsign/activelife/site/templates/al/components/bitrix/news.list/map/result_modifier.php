<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader;

if(!Loader::includeModule('iblock')) {
    return;
}

$coordsProp = 'SHOP_MAP_COORDS';
$typeProp = 'SHOP_TYPE';

$arResult['FILTER_TYPES'] = array();
$propertyEnums = CIBlockPropertyEnum::GetList(array(),array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "CODE"=> $typeProp));
while($arFields = $propertyEnums->GetNext()) {
    $arResult['FILTER_TYPES'][] = array(
		'ID' => $arFields['ID'],
		'VALUE' => $arFields['VALUE'],
		'XML_ID' => $arFields['XML_ID'],
	);
}

foreach($arResult['ITEMS'] as &$arItem) {
    $arItem['COORDINATES'] = $arItem['PROPERTIES'][$coordsProp]['VALUE'];
    $arItem['TYPE'] = $arItem['PROPERTIES'][$typeProp]['VALUE_XML_ID'];
}
unset($arItem);
