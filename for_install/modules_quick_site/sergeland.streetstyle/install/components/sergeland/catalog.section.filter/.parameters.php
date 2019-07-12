<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;	
	
$boolCatalog = CModule::IncludeModule("catalog");
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if ($boolCatalog)
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));

while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];


if (0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	
	while ($arr=$rsProp->Fetch())
	  if($arr["PROPERTY_TYPE"] != "F")
		$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

$arSort = array(
	"name" => GetMessage("SORT_FIELD_NAME"),
	"id" => GetMessage("SORT_FIELD_ID"),
	"sort" => GetMessage("SORT_FIELD_SORT")
); 


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
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("FILTER_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"FILTER_NAME2" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("FILTER_NAME2"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter2",
		),		
		"FOLDER" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"IBLOCK_FOLDER",
			GetMessage("FOLDER"),
			SITE_DIR."catalog/",
			"URL_TEMPLATES"
		),
		"SEF_URL_SECTION_TEMPLATE" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"IBLOCK_FOLDER",
			GetMessage("SEF_URL_SECTION_TEMPLATE"),
			"#SECTION_CODE#/",
			"URL_TEMPLATES"
		),		
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),		
		"SORT_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			//"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),		
		"SORT_FIELD_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SORT_FIELD_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			//"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_VALUE_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SORT_VALUE_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			//"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_VALUE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SORT_VALUE"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			//"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "name",
		),
		"SHOW_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SHOW_FIELD"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),		
		"SHOW_COUNT_ELEMENT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SHOW_COUNT_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCS_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),		
	),
);

if($boolCatalog)
{
	$arComponentParameters["PARAMETERS"]['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('HIDE_NOT_AVAILABLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arComponentParameters["PARAMETERS"]["PRICE_CODE"] = array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
	);
	$arComponentParameters["PARAMETERS"]["SHOW_PRICE"] = array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("SHOW_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
	);
}
?>