<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Loader;

if(!Loader::includeModule('iblock')) {
    return;
}

$catalogIncluded = Loader::includeModule('catalog');

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);

$arPrice = array();
if ($catalogIncluded)
{
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
}

$arTemplateParameters["IBLOCK_TYPE"] = array(
    "PARENT" => "BASE",
    "NAME" => GetMessage("IBLOCK_TYPE"),
    "TYPE" => "LIST",
    "VALUES" => $arIBlockType,
    "REFRESH" => "Y",
);

$arTemplateParameters["IBLOCK_ID"] = array(
    "PARENT" => "BASE",
    "NAME" => GetMessage("IBLOCK_ID"),
    "TYPE" => "LIST",
    "ADDITIONAL_VALUES" => "Y",
    "VALUES" => $arIBlockType,
    "REFRESH" => "Y",
);
$arTemplateParameters["PRICE_CODE"] = array(
    "PARENT" => "PRICES",
    "NAME" => GetMessage("IBLOCK_PRICE_CODE"),
    "TYPE" => "LIST",
    "MULTIPLE" => "Y",
    "VALUES" => $arPrice,
);
$arTemplateParameters["SHOW_POPUP"] = array(
    "NAME" => GetMessage("RS.FLYAWAY.SHOW_POPUP"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "Y"
);
