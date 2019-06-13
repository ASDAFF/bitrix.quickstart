<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("iblock"))
	return;
if (!CModule::IncludeModule("catalog"))
	return;
	
$arIBlockType = CIBlockParameters::GetIBlockTypes();
	
$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["IBLOCK_TYPE_ID"]][$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arPrice = array();
$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
while($arr=$rsPrice->Fetch())
	$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];

	
$arComponentParameters = array(
	"PARAMETERS" => array(		
		"AUCTION_IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("A_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"AUCTION_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("A_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock[$arCurrentValues["AUCTION_IBLOCK_TYPE"]],
			"REFRESH" => "Y",
		),
		"BETS_IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("A_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"BETS_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("A_BETS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock[$arCurrentValues["BETS_IBLOCK_TYPE"]],
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("A_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("A_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"IBLOCK_URL" => CIBlockParameters::GetPathTemplateParam(
			"LIST",
			"IBLOCK_URL",
			GetMessage("A_AUCTION_LIST_URL"),
			"",
			"URL_TEMPLATES"
		),
		"AUCTION_JQUERY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"AUCTION_PERMISSIONS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_PERMISSIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"AUCTION_HIDE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_HIDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_BUY_LOT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_BUY_LOT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"AUCTION_SHOW_NAME" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_SHOW_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"AUCTION_EDIT_PRICE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_EDIT_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_DOUBLE_BETS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_DOUBLE_BETS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_PRICE_CONFIRM" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_PRICE_CONFIRM"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_CHAT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_CHAT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_EXTEND" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_EXTEND"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"AUCTION_INTERVAL" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_INTERVAL"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"AUCTION_MAX_BUY" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_MAX_BUY"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("A_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arPrice,
		),
		"COUNT_BETS" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_COUNT_BETS"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"AVATAR_WIDTH" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_BETS_AVATAR_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => 50,
		),
		"AVATAR_HEIGHT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_BETS_AVATAR_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => 50,
		),
		"IMAGE_WIDTH" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_IMAGE_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => 250,
		),
		"IMAGE_HEIGHT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_IMAGE_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => 250,
		),
		"AUCTION_SET_TITLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("A_SET_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_TIME" => array("DEFAULT" => 86400),
	),
);
?>