<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("all" => " "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT" => "ASC"), Array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="all"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arComponentParameters = array(
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MILLCOM_MENU_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "catalog",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MILLCOM_MENU_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '1',
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		),
		"SORT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MILLCOM_MENU_SORT"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"VALUE" => "Y",
			"DEFAULT" =>"Y",
		),
		"DEPTH_LEVEL" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("MILLCOM_MENU_DEPTH_LEVEL"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"CACHE_TIME"  =>  array("DEFAULT"  =>  36000000),
	)
);
?>
