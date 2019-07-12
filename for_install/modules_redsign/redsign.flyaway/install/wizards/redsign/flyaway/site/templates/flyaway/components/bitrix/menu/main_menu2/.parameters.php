<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Loader,
    \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

 if(
    !Loader::includeModule('redsign.devfunc') ||
    !Loader::includeModule('iblock') ||
    !Loader::includeModule('catalog')
) {
    return;
}

$arIBlockTypes = CIBlockParameters::GetIBlockTypes();

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

$arPrices = CCatalogIBlockParameters::getPriceTypesList();

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);
$arCatalog = CCatalog::GetByID($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = array(
    "IBLOCK_TYPE" => array(
		"PARENT" => "BASE",
		"NAME" => Loc::getMessage("IBLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockTypes,
		"REFRESH" => "Y",
	),
    "IBLOCK_ID" => array(
		"PARENT" => "BASE",
		"NAME" => Loc::getMessage("IBLOCK_IBLOCK"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
    "PROPERTY_CODE_ELEMENT_IN_MENU" => array(
		"PARENT" => "BASE",
		"NAME" => Loc::getMessage("RS.FLYAWAY.PROPERTY_CODE_ELEMENT_IN_MENU"),
		"TYPE" => "LIST",
		"VALUES" => $listProp['L'],
	),
    "PRICE_CODE" => array(
		"NAME" => Loc::getMessage("IBLOCK_PRICE_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPrices,
	),
    'RSFLYAWAY_PROP_MORE_PHOTO' => array(
		'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp['F'],
	),
    "RSFLYAWAY_IS_SHOW_PRODUCTS" => array(
        "NAME" => Loc::getMessage("RS.FLYAWAY.IS_SHOW_PRODUCTS"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
    ),
    "RSFLYAWAY_IS_SHOW_IMAGE" => array(
        "NAME" => Loc::getMessage("RS.FLYAWAY.IS_SHOW_PRODUCTS"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
    )
);

if ((int)$arCatalog["OFFERS_IBLOCK_ID"]) {
	$listProp2 = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCatalog['OFFERS_IBLOCK_ID']);
	$arTemplateParameters['RSFLYAWAY_PROP_SKU_MORE_PHOTO'] = array(
		'NAME' => GetMessage('RS.FLYAWAY.PROP_SKU_MORE_PHOTO'),
		'TYPE' => 'LIST',
		'VALUES' => $listProp2['F'],
	);
}
