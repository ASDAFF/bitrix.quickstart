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

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arAscDesc = array(
	"asc" => GetMessage("A_SORT_ASC"),
	"desc" => GetMessage("A_SORT_DESC"),
);

$arPrice = array();
$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
while($arr=$rsPrice->Fetch())
	$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
	
$arComponentParameters = array(
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => array(
			"SECTION_ID" => array("NAME" => GetMessage("SECTION_ID_DESC")),
			"ELEMENT_ID" => array("NAME" => GetMessage("ELEMENT_ID_DESC")),
		),
		"AJAX_MODE" => array(),
		"SEF_MODE" => array(
			"sections" => array(
				"NAME" => GetMessage("SECTIONS_TOP_PAGE"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"section" => array(
				"NAME" => GetMessage("SECTION_PAGE"),
				"DEFAULT" => "#SECTION_ID#/",
				"VARIABLES" => array("SECTION_ID"=>"SID"),
			),
			"element" => array(
				"NAME" => GetMessage("DETAIL_PAGE"),
				"DEFAULT" => "#SECTION_ID#/#ELEMENT_ID#/",
				"VARIABLES" => array("ELEMENT_ID"=>"EID"),
			),
		),
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
		"AUCTION_BUY_LOT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("A_BUY_LOT"),
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
?>