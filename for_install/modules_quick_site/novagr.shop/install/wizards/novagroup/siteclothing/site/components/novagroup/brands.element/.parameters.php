<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock")) return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-" => " "));
$arCatalogs = array();

$rsCatalogIBlock = CIBlock::GetList(
    array('SORT' => "ASC"),
    array('ACTIVE' => "Y", 'SITE_ID' => $_REQUEST["site"],
        'TYPE' => ($arCurrentValues["CATALOG_IBLOCK_TYPE"] != "-" ? $arCurrentValues["CATALOG_IBLOCK_TYPE"] : ""))
);

while ($data = $rsCatalogIBlock->Fetch()) $arCatalogs[$data["CODE"]] = $data["NAME"];

$arComponentParameters = array(
    'PARAMETERS' => array(
        "BRANDS_IBLOCK_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CATALOG_ELEM_IB_BRANDS_CODE"),
            "TYPE" => "LIST",
            "VALUES" => $arCatalogs,
            "DEFAULT" => "",
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "N",
        ),
        "BRAID_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("BRAID_ID_TITLE"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),

        'CACHE_TIME' => array('DEFAULT' => 3600),
    ),
);
?>