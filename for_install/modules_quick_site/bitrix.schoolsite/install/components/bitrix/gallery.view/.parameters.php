<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule("bitrix.schoolsite")) {
    return;
}

if (!CModule::IncludeModule("iblock")) {
    return;
}

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSorts = array("ASC"=>GetMessage("T_IBLOCK_DESC_ASC"), "DESC"=>GetMessage("T_IBLOCK_DESC_DESC"));
$arSortFields = array(
    "ID"=>GetMessage("T_IBLOCK_DESC_FID"),
    "NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
    "ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
    "SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
    "TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
);

$arComponentParameters = array(
    "GROUPS" => array(
        'VIEW' => array(
            'NAME' => GetMessage("GALLERY_VIEW"),
        ),
    ),
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "SECTION_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_SECTION_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '',
        ),
        "GALLERY_ID" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("GALLERY_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => 'main',
        ),
        "GALLERY_SKIN" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("GALLERY_SKIN"),
            "TYPE" => "STRING",
            "DEFAULT" => 'jcarousel-skin-tango',
        ),
        "GALLERY_CSS" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("GALLERY_CSS"),
            "TYPE" => "STRING",
            "DEFAULT" => '',
        ),        
        "SMALL_IMAGE_WIDTH" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("SMALL_IMAGE_WIDTH"),
            "TYPE" => "STRING",
            "DEFAULT" => '111',
        ),
        "SMALL_IMAGE_HEIGHT" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("SMALL_IMAGE_HEIGHT"),
            "TYPE" => "STRING",
            "DEFAULT" => '111',
        ),
        "SHOW_BIG_IMAGE" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("SHOW_BIG_IMAGE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => 'N',
        ),
        "BIG_IMAGE_WIDTH" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("BIG_IMAGE_WIDTH"),
            "TYPE" => "STRING",
            "DEFAULT" => '555',
        ),
        "BIG_IMAGE_HEIGHT" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("BIG_IMAGE_HEIGHT"),
            "TYPE" => "STRING",
            "DEFAULT" => '1200',
        ),
        "USE_PRELOADER" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("USE_PRELOADER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => 'Y',
        ),
        "SHOW_IMAGE_CAPTIONS" => array(
            "PARENT" => "VIEW",
            "NAME" => GetMessage("SHOW_IMAGE_CAPTIONS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => 'Y',
        ),
        "SORT_BY1" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ACTIVE_FROM",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER1" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_BY2" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBORD2"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER2" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBBY2"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "FILTER_NAME" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_FILTER"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "INCLUDE_SUBSECTIONS" => array(
            'PARENT' => 'DATA_SOURCE',
            'NAME' => GetMessage("INCLUDE_SUBSECTIONS"),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => '',
        ),
        "CACHE_TIME"  =>  Array("DEFAULT"=>3600000),
    ),
);
?>
