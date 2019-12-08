<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock")) return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	
	$arComponentParameters = array(
		"GROUPS"=>array(
		),
		"PARAMETERS"=>array(
			"IBLOCK_TYPE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SS_SSG_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		   ),
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SS_SSG_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"REFRESH" => "Y",
		),
		"CHECK_DATES" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SS_SSG_IBLOCK_CHECK_DATES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SSG_SLIDESHOW_MODE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SS_SSG_MODE_DESCR"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SSG_PRELOAD_IMG" => array(
			"PARENT"=>"ADDITIONAL_SETTINGS",
			"NAME"=>GetMessage("SS_SSG_PRELOAD_IMG"),
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"Y",
		),
		"SSG_ONLOAD_START" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SS_SSG_ONLOAD_START"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		
		
		),
	);


?>