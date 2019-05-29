<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Loader;
    
if(
    !Loader::includeModule('iblock') ||
    !Loader::includeModule('redsign.devfunc')
) {
    return;
}

Loc::loadMessages(__FILE__);


$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);

$arIBlockTypes = array(); 
$dbIBlockType = CIBlockType::GetList(
   array("sort" => "asc"),
   array("ACTIVE" => "Y")
);
while ($arIBlockType = $dbIBlockType->Fetch()) {
    $arIBlockLangName = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID);
    if($arIBlockLangName) {
        $arIBlockTypes[$arIBlockType["ID"]] = "[".$arIBlockType["ID"]."] ".$arIBlockTypeLang["NAME"];
    }
}

$arIBlocksServices = array();
$iblockFilter = (
	!empty($arCurrentValues['RS_SIDEBANNERS_IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['RS_SIDEBANNERS_IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $dbIBlock->Fetch()) {
	$arIBlocksServices[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}

$bannerTypes = array(
    "wide" => Loc::getMessage("RS_BANNER_TYPE__WIDE"),
    "center" => Loc::getMessage("RS_BANNER_TYPE__CENTER")
);

$arSidebannerTypes = array(
    "none" => Loc::getMessage("RS_SIDEBANNERS__NONE"),
    "left" => Loc::getMessage("RS_SIDEBANNERS__LEFT"),
    "right" => Loc::getMessage("RS_SIDEBANNERS__RIGHT"),
    "both" => Loc::getMessage("RS_SIDEBANNERS__BOTH")
);

$arTemplateParameters = array(

    "RS_BANNER_HEIGHT" => array(
        "NAME" => Loc::getMessage("RS_BANNER_HEIGHT"),
        "TYPE" => "STRING",
        "DEFUALT" => "400px"
    ),
    
    "RS_BANNER_TYPE" => array(
        "NAME" => Loc::getMessage("RS_BANNER_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $bannerTypes,
        "DEFUALT" => "wide"
    ),
    
    "RS_BANNER_IS_AUTOPLAY" => array(
        "NAME" => Loc::getMessage("RS_BANNER_IS_AUTOPLAY"),
        "TYPE" => "CHECKBOX",
        "DEFUALT" => "Y",
        "REFRESH" => "Y"
    ),
    
    "RS_TITLE_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_TITLE_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['SNL'],
        "DEFUALT" => "TITLE"
    ),
    
    "RS_DESC_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_DESC_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['SNL'],
        "DEFUALT" => "DESCRIPTION"
    ),
    
    "RS_PRICE_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_PRICE_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['SNL'],
        "DEFUALT" => "PRICE"
    ),
    
    "RS_LINK_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_LINK_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['SNL'],
        "DEFUALT" => "LINK"
    ),
    
    "RS_BACKGROUND_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_BACKGROUND_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['F'],
        "DEFUALT" => "BACKGROUND"
    ),
    
    "RS_IMG_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_IMG_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['F'],
        "DEFUALT" => "PRODUCT_IMAGE"
    ),
    
    "RS_BUTTON_TEXT_PROPERTY" => array(
        "NAME" => Loc::getMessage("RS_BUTTON_TEXT_PROPERTY"),
        "TYPE" => "LIST",
        "VALUES" => $listProp['SNL'],
        "DEFUALT" => "BUTTON_TEXT"
    ),
    
    "RS_SIDEBANNERS_IBLOCK_TYPE" => array(
        "NAME" => Loc::getMessage("RS_SIDEBANNERS_IBLOCK_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlockTypes,
        "DEFUALT" => "services",
        "REFRESH" => "Y"
    ),
    
);

if(!empty($arCurrentValues['RS_SIDEBANNERS_IBLOCK_TYPE'])) {
    
    $arTemplateParameters['RS_SIDEBANNERS_IBLOCK_ID'] = array(
        "NAME" => Loc::getMessage("RS_SIDEBANNERS_IBLOCK_ID"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlocksServices
    );
}

if(!empty($arCurrentValues['RS_SIDEBANNERS_IBLOCK_ID'])) {
    
    $arTemplateParameters['RS_SIDEBANNERS'] = array(
        "NAME" => Loc::getMessage("RS_SIDEBANNERS"),
        "TYPE" => "LIST",
        "VALUES" => $arSidebannerTypes
    );
}


if(
    !empty($arCurrentValues['RS_BANNER_IS_AUTOPLAY']) &&
    $arCurrentValues['RS_BANNER_IS_AUTOPLAY'] == "Y"
) {
    
    $arTemplateParameters['RS_BANNER_AUTOPLAY_SPEED'] = array(
        "NAME" => Loc::getMessage("RS_BANNER_AUTOPLAY_SPEED"),
        "TYPE" => "STRING",
        "DEFUALT" => "2000"
    );
    
    $arTemplateParameters['RS_BANNER_AUTOPLAY_TIMEOUT'] = array(
        "NAME" => Loc::getMessage("RS_BANNER_AUTOPLAY_TIMEOUT"),
        "TYPE" => "STRING",
        "DEFUALT" => "7000"
    );
}
