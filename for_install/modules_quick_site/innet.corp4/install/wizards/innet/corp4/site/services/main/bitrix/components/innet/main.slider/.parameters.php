<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
    return;


$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-" => " "));

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while ($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];


$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("INNET_MAIN_SLIDER_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "catalog",
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("INNET_MAIN_SLIDER_IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => 'catalog',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "COUNT_ELEMENTS" => array(
            "NAME" => GetMessage("INNET_MAIN_SLIDER_COUNT_ELEMENTS"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
        "CACHE_TIME" => Array(
            "PARENT" => "CACHE_SETTINGS",
            "DEFAULT" => 3600
        ),
        "INNET_SLIDE_PAUSE" => array(
            "NAME" => GetMessage("INNET_SLIDE_PAUSE"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
        "INNET_SLIDE_SPEED" => array(
            "NAME" => GetMessage("INNET_SLIDE_SPEED"),
            "TYPE" => "STRING",
            "PARENT" => "BASE",
        ),
    ),
);
?>
