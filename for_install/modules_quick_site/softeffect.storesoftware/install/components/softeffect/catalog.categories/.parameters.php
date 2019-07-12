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
while($arRes = $db_iblock->Fetch())
{
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$catURLDef = "/catalog/category/";

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		"CATEGORIES_URL"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SE_LINK_CATEGORIES"),
			"TYPE" => "STRING",
			"DEFAULT" => $catURLDef,
		),
	),
);
?>
