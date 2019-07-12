<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = Array();
$db_iblock_type = CIBlockType::GetList(Array("SORT"=>"ASC"));
while($arRes = $db_iblock_type->Fetch())
{
	if($arIBType = CIBlockType::GetByIDLang($arRes["ID"], LANG))
	{
		$arTypesEx[$arRes["ID"]] = $arIBType["NAME"];
	}
}

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$brandURLDef = SITE_DIR."brands/";

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"IBLOCK_TYPE"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "banner",
			"REFRESH" => "Y",
		),
		"IBLOCK"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
		),
		"MAX_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "15",
		),
		"BRANDS_URL"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_LINK_BRANDS"),
			"TYPE" => "STRING",
			"DEFAULT" => $brandURLDef,
		),
		"CATALOG_SECTION"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_CATALOG_SECTION"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
	),
);
?>
