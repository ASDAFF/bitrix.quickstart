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
		"IBLOCK_CATALOG_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_IBLOCK_CATALOG_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK_CATALOG_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_IBLOCK_CATALOG_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		
		"IBLOCK_REVIEWS_GOODS_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_IBLOCK_REVIEWS_GOODS_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK_REVIEWS_GOODS_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_IBLOCK_REVIEWS_GOODS_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		
		"IBLOCK_COMPARE_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_IBLOCK_COMPARE_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK_COMPARE_ID"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_IBLOCK_COMPARE_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		
		"CATALOG_ELEMENT_CODE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_CATALOGELEMENT_CATALOG_ELEMENT_CODE"),
			"TYPE" => "TEXT",
			"VALUES" => "",
			"DEFAULT" => "",
		),
	),
);
?>