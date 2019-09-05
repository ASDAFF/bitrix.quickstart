<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CATALOG_SECTION_LIST_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CATALOG_SECTION_LIST_IBLOCK_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "FIELDS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CATALOG_SECTION_LIST_FIELDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => "10",
            "VALUES" => array(
                "ID" => GetMessage("CATALOG_SECTION_LIST_FIELDS_ID"),
                "NAME" => GetMessage("CATALOG_SECTION_LIST_FIELDS_NAME"),
                "DESCRIPTION" => GetMessage("CATALOG_SECTION_LIST_FIELDS_DESCRIPTION")
            ),
            "ADDITIONAL_VALUES" => "Y",
        ),
        'FILTER_NAME' => array(
            'NAME' => GetMessage('CATALOG_SECTION_LIST_FILTER_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'arrFilter',
            'PARENT' => 'BASE',
        ),
	)
);
?>
