<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
    return;

if (!CModule::IncludeModule("catalog"))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-" => " "));

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while ($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSorts = Array("ASC" => GetMessage("INNET_SECTION_LIST_SORT_ASC"), "DESC" => GetMessage("INNET_SECTION_LIST_SORT_DESC"));

$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("INNET_SECTION_LIST_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "catalog",
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("INNET_SECTION_LIST_IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => 'catalog',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "COUNT_SECTION" => array(
            "NAME" => GetMessage("INNET_SECTION_LIST_COUNT_SECTION"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
            "DEFAULT" => "8",
        ),
        "COUNT_SECTION_NESTED" => array(
            "NAME" => GetMessage("INNET_SECTION_LIST_COUNT_SECTION_NESTED"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
            "DEFAULT" => "5",
        ),
        "UF_CODE" => array(
            "NAME" => GetMessage("INNET_SECTION_LIST_UF_CODE"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
        "SORT_SECTION" => Array(
            "PARENT" => "BASE",
            "NAME" => GEtMessage("INNET_SECTION_LIST_SORT"),
            "TYPE" => "LIST",
            "VALUES" => $arSorts,
        ),
        "SORT_SUB_SECTION" => Array(
            "PARENT" => "BASE",
            "NAME" => GEtMessage("INNET_SECTION_LIST_SORT_SUBSECTION"),
            "TYPE" => "LIST",
            "VALUES" => $arSorts,
        ),
        "ELEMENT_IN_SECTION" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("INNET_SECTION_LIST_ELEMENT_IN_SECTION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "WIDTH" => array(
            "NAME" => GetMessage("INNET_SECTION_LIST_WIDTH"),
            "TYPE" => "STRING",
        ),
        "HEIGHT" => array(
            "NAME" => GetMessage("INNET_SECTION_LIST_HEIGHT"),
            "TYPE" => "STRING",
        ),
        "CACHE_TIME" => Array(
            "PARENT" => "CACHE_SETTINGS",
            "DEFAULT" => 3600
        ),
    ),
);
?>
