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
$arIBlockCatalog = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["ES_IBLOCK_TYPE_CATALOG"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()) $arIBlockCatalog[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array();
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

$arGroupCount = array();
for($i = 0; $i < 500; $i++) $arGroupCount[$i] = $i;
$arComponentParameters["GROUPS"]["ES_SETTING_GROUP"] = array("NAME" => GetMessage("ES_SETTING_GROUP"), "SORT" => $esSectSort++);
$arComponentParameters["PARAMETERS"]["ES_GROUP_COUNT"] = array(
	"PARENT" => "ES_SETTING_GROUP",
	"NAME" => GetMessage("ES_GROUP_COUNT"),
	"TYPE" => "LIST",
	"VALUES" => $arGroupCount,
	"REFRESH" => "Y",
	"DEFAULT" => "0",
);

if(intval($arCurrentValues["ES_IBLOCK_CATALOG"]) > 0 && intval($arCurrentValues["ES_GROUP_COUNT"]) > 0)
{
	$esSectSort = 1000;
	$arProperty = array();
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["ES_IBLOCK_CATALOG"]));
	while($p=$rsProp->Fetch()) if($p["CODE"] != "") $arProperty[$p["CODE"]] = "[".$p["CODE"]."] ".$p["NAME"];

	for($i = 0; $i < intval($arCurrentValues["ES_GROUP_COUNT"]); $i++)
	{
		$arComponentParameters["GROUPS"]["ES_GROUP_".$i] = array("NAME" => GetMessage("ES_GROUP").": ".($i+1), "SORT" => $esSectSort++);
		$arComponentParameters["PARAMETERS"]["ES_GROUP_NAME_".$i] = array(
			"PARENT" => "ES_GROUP_".$i,
			"NAME" => GetMessage("ES_GROUP_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		);
		$arComponentParameters["PARAMETERS"]["ES_GROUP_".$i] = array(
			"PARENT" => "ES_GROUP_".$i,
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