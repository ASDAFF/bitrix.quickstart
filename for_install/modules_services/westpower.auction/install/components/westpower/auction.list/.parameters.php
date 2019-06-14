<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("iblock"))
	return;
	
$arIBlockType = CIBlockParameters::GetIBlockTypes();
	
$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["IBLOCK_TYPE_ID"]][$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arAscDesc = array(
	"asc" => GetMessage("A_SORT_ASC"),
	"desc" => GetMessage("A_SORT_DESC"),
);

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
		"SECTION_ID" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("A_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("A_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("A_DETAIL_PAGE_URL"),
			"",
			"URL_TEMPLATES"
		),
		"AUCTION_JQUERY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"AUCTION_PRODUCT_PROPERTY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_PRODUCT_PROPERTY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_LAST_BETS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_LAST_BETS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_PERMISSIONS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_PERMISSIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_FILTER_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"FILTER_NAME_LOT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_FILTER_LOT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrLotFilter",
		),
		"LOT_SORT_FIELD1" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_AUCTION_SORT_FIELD1"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"LOT_SORT_ORDER1" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_AUCTION_SORT_ORDER1"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"LOT_SORT_FIELD2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_AUCTION_SORT_FIELD2"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "id",
		),
		"LOT_SORT_ORDER2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("A_AUCTION_SORT_ORDER2"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "desc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"AUCTION_HIDE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_HIDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_PAGE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"LINE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_LINE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"AUCTION_NAME" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_ACTIVE_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_LOT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_ACTIVE_LOT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"IMG_WIDTH" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IMG_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
		"IMG_HEIGHT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IMG_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
		
		"AUCTION_SET_TITLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("A_SET_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"AUCTION_TITLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("A_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("A_TITLE_VALUE"),
		),
		
		"CACHE_TIME" => array("DEFAULT" => 86400),		
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("A_PAGER_AUCTION"), true, true);
?>