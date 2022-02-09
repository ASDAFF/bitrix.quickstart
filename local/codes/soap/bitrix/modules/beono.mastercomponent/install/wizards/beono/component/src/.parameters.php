<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
// + iblock
if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}
// - iblock	
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		// + iblock
		"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => "IBLOCK_TYPE",
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => "IBLOCK_ID",
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"ITEMS_LIMIT" => Array(
			"PARENT" => "BASE",
			"NAME" => "ITEMS_LIMIT",
			"TYPE" => "STRING",
			"DEFAULT" => "10",
		),
		// - iblock
		// + cache
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		// - cache
		// + complex
		"AJAX_MODE" => array(),
		"VARIABLE_ALIASES" => array(
			"ELEMENT_ID" => array(
				"NAME" => "ELEMENT_ID",
				"DEFAULT" => "ELEMENT_ID",
			),
		),
		"SEF_MODE" => array(
			"list" => array(
				"NAME" => "LIST PAGE",
				"DEFAULT" => "/",
				"VARIABLES" => array()
			),
			"detail" => array(
				"NAME" => "DETAIL PAGE",
				"DEFAULT" => "/#ELEMENT_ID#/",
				"VARIABLES" => array("ELEMENT_ID")
			),
		),
		// - complex
	),
);
?>