<?
######################################################
# Name: energosoft.grouping                          #
# File: .parameters.php                              #
# (c) 2005-2012 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

$esSectSort = 100;
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlockGroup = array();
$arIBlockCatalog = array();

$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["ES_IBLOCK_TYPE_GROUP"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()) $arIBlockGroup[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["ES_IBLOCK_TYPE_CATALOG"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()) $arIBlockCatalog[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array();
$arComponentParameters["GROUPS"]["ES_SETTING_GROUP"] = array("NAME" => GetMessage("ES_SETTING_GROUP"), "SORT" => $esSectSort++);
$arComponentParameters["PARAMETERS"]["ES_IBLOCK_TYPE_GROUP"] = array(
	"PARENT" => "ES_SETTING_GROUP",
	"NAME" => GetMessage("ES_IBLOCK_TYPE"),
	"TYPE" => "LIST",
	"VALUES" => $arIBlockType,
	"REFRESH" => "Y",
	"DEFAULT" => "",
);
$arComponentParameters["PARAMETERS"]["ES_IBLOCK_GROUP"] = array(
	"PARENT" => "ES_SETTING_GROUP",
	"NAME" => GetMessage("ES_IBLOCK_GROUP"),
	"TYPE" => "LIST",
	"VALUES" => $arIBlockGroup,
	"REFRESH" => "Y",
	"ADDITIONAL_VALUES" => "Y",
);
$arComponentParameters["PARAMETERS"]["ES_IBLOCK_GROUP_SORT_FIELD"] = array(
	"PARENT" => "ES_SETTING_GROUP",
	"NAME" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD"),
	"TYPE" => "LIST",
	"VALUES" => array(
		"id" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_ID"),
		"name" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_NAME"),
		"sort" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_SORT"),
		"shows" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_SHOWS"),
		"timestamp_x" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_TIMESTAMP"),
		"active_from" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_ACTIVE_FROM"),
		"active_to" => GetMessage("ES_IBLOCK_GROUP_SORT_FIELD_ACTIVE_TO"),
	),
	"DEFAULT" => "sort",
	"REFRESH" => "Y",
);
$arComponentParameters["PARAMETERS"]["ES_IBLOCK_GROUP_SORT_ORDER"] = array(
	"PARENT" => "ES_SETTING_GROUP",
	"NAME" => GetMessage("ES_IBLOCK_GROUP_SORT_ORDER"),
	"TYPE" => "LIST",
	"VALUES" => array(
		"asc" => GetMessage("ES_IBLOCK_GROUP_SORT_ORDER_ASC"),
		"desc" => GetMessage("ES_IBLOCK_GROUP_SORT_ORDER_DESC"),
	),
	"DEFAULT" => "asc",
	"REFRESH" => "Y",
);

$arComponentParameters["GROUPS"]["ES_SETTING_CATALOG"] = array("NAME" => GetMessage("ES_SETTING_CATALOG"), "SORT" => $esSectSort++);
$arComponentParameters["PARAMETERS"]["ES_IBLOCK_TYPE_CATALOG"] = array(
	"PARENT" => "ES_SETTING_CATALOG",
	"NAME" => GetMessage("ES_IBLOCK_TYPE"),
	"TYPE" => "LIST",
	"VALUES" => $arIBlockType,
	"REFRESH" => "Y",
	"DEFAULT" => "",
);
$arComponentParameters["PARAMETERS"]["ES_IBLOCK_CATALOG"] = array(
	"PARENT" => "ES_SETTING_CATALOG",
	"NAME" => GetMessage("ES_IBLOCK_CATALOG"),
	"TYPE" => "LIST",
	"VALUES" => $arIBlockCatalog,
	"REFRESH" => "Y",
	"ADDITIONAL_VALUES" => "Y",
);
$arComponentParameters["PARAMETERS"]["ES_ELEMENT"] = array(
	"PARENT" => "ES_SETTING_CATALOG",
	"NAME" => GetMessage("ES_ELEMENT"),
	"TYPE" => "STRING",
	"DEFAULT" => '={$arResult["ID"]}',
);
$arComponentParameters["PARAMETERS"]["ES_SHOW_EMPTY"] = array(
	"PARENT" => "ES_SETTING_CATALOG",
	"NAME" => GetMessage("ES_SHOW_EMPTY"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arComponentParameters["PARAMETERS"]["ES_SHOW_EMPTY_PROPERTY"] = array(
	"PARENT" => "ES_SETTING_CATALOG",
	"NAME" => GetMessage("ES_SHOW_EMPTY_PROPERTY"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arComponentParameters["PARAMETERS"]["ES_REMOVE_HREF"] = array(
	"PARENT" => "ES_SETTING_CATALOG",
	"NAME" => GetMessage("ES_REMOVE_HREF"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);

if(intval($arCurrentValues["ES_IBLOCK_CATALOG"]) > 0 && intval($arCurrentValues["ES_IBLOCK_GROUP"]) > 0)
{
	$esSectSort = 1000;
	$arProperty = array();
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["ES_IBLOCK_CATALOG"]));
	while($p=$rsProp->Fetch()) if($p["CODE"] != "") $arProperty[$p["CODE"]] = "[".$p["CODE"]."] ".$p["NAME"];

	$arSelect = array("ID", "NAME");
	$arSort = array($arCurrentValues["ES_IBLOCK_GROUP_SORT_FIELD"] => $arCurrentValues["ES_IBLOCK_GROUP_SORT_ORDER"]);
	$arFilter = array(
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
		"IBLOCK_ID" => $arCurrentValues["ES_IBLOCK_GROUP"],
		"IBLOCK_ACTIVE" => "Y",
	);
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = array();
		$arItem = $obElement->GetFields();

		$arComponentParameters["GROUPS"]["ES_GROUP_".$arItem["ID"]] = array("NAME" => GetMessage("ES_GROUP").": ".$arItem["NAME"], "SORT" => $esSectSort++);
		$arComponentParameters["PARAMETERS"]["ES_GROUP_".$arItem["ID"]] = array(
			"PARENT" => "ES_GROUP_".$arItem["ID"],
			"NAME" => GetMessage("ES_GROUP_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty,
			"MULTIPLE" => "Y",
			"DEFAULT" => "",
			"SIZE" => "10",
		);
	}
}
$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array("DEFAULT"=>3600);
?>