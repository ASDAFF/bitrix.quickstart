<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

	if(in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}

	if($arr["PROPERTY_TYPE"]=="N")
	{
		$arProperty_N[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}

	if($arr["PROPERTY_TYPE"]!="F")
	{
		if($arr["MULTIPLE"] == "Y")
			$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		elseif($arr["PROPERTY_TYPE"] == "L")
			$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		elseif($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
			$arProperty_X[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arSProperty_LNS = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField)
	if($arUserField["USER_TYPE"]["BASE_TYPE"]=="string")
		$arSProperty_LNS[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("IBLOCK_PRICES"),
		),
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => array(
				"shows" => GetMessage("IBLOCK_SORT_SHOWS"),
				"sort" => GetMessage("IBLOCK_SORT_SORT"),
				"timestamp_x" => GetMessage("IBLOCK_SORT_TIMESTAMP"),
				"name" => GetMessage("IBLOCK_SORT_NAME"),
				"id" => GetMessage("IBLOCK_SORT_ID"),
				"active_from" => GetMessage("IBLOCK_SORT_ACTIVE_FROM"),
				"active_to" => GetMessage("IBLOCK_SORT_ACTIVE_TO"),
			),
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_FILTER_NAME_IN"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"INCLUDE_SUBSECTIONS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCS_INCLUDE_SUBSECTIONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_ALL_WO_SECTION" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCS_SHOW_ALL_WO_SECTION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SLIDE_SPEED" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_SLIDE_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "7",
		),
        "SLIDE_FADE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_SLIDE_FADE"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
        "SLIDE_WIDTH" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_SLIDE_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "500",
		),
        "SLIDE_HEIGHT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("IBLOCK_SLIDE_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "350",
		),
        "SLIDER_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => "Количество элементов в слайдере",
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCS_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);
?>
