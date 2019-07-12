<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = Array();
$db_iblock_type = CIBlockType::GetList(Array("SORT"=>"ASC"));
while($arRes = $db_iblock_type->Fetch()) {
	if ($arIBType = CIBlockType::GetByIDLang($arRes["ID"], LANG)) {
		$arTypesEx[$arRes["ID"]] = $arIBType["NAME"];
	}
}

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK_TYPEG_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGCATEGORY_IBLOCK_TYPEG_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK_TYPEG_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGCATEGORY_IBLOCK_TYPEG_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		
		"IBLOCK_CATALOG_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGCATEGORY_IBLOCK_CATALOG_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK_CATALOG_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGCATEGORY_IBLOCK_CATALOG_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		"CATALOG_CATEGORY_CODE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGCATEGORY_CATALOG_CATEGORY_CODE"),
			"TYPE" => "TEXT",
			"VALUES" => "",
			"DEFAULT" => "",
		),
	),
);
?>