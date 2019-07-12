<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $USER_FIELD_MANAGER;

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

//получаем типы цен для текущего сайта
$price = \Mlife\Asz\PricetipTable::getList(
	array(
		'select' => array('ID','NAME',"BASE","SITE_ID"),
	)
);
$arPrice = array();
while($arPricedb = $price->Fetch()){
	$arPrice[$arPricedb["ID"]] = "[".$arPricedb["SITE_ID"]."] - ".$arPricedb["NAME"];
}
	
$arProperty = array();
$arPropertyID = array();
$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
if (0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F"){
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			$arPropertyID[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}

		if($arr["PROPERTY_TYPE"]=="N")
			$arProperty_N[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

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
}

$arProperty_UF = array();
$arSProperty_LNS = array();

$arComponentParameters = array(
	"GROUPS" => array(
		"BASE" => array("NAME"=>GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_1")),
		"DATA_SOURCE" => array("NAME"=>GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_2")),
		"URL_TEMPLATES" => array("NAME"=>GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_3")),
		"ADDITIONAL_SETTINGS" => array("NAME"=>GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_4")),
		"VISUAL" => array("NAME"=>GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_5")),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_6"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_7"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_8"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_9"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_10"),
			"TYPE" => "STRING",
			"DEFAULT" => "NAME",
		),
		"ELEMENT_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_11"),
			"TYPE" => "STRING",
			"DEFAULT" => "ASC",
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_12"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_URL",
			GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_13"),
			"",
			"URL_TEMPLATES"
		),
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_14"),
			"",
			"URL_TEMPLATES"
		),
		"ADD_SECTIONS_CHAIN" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_14_2"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"HIDE_BY" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_21"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"HIDE_QUANT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_22"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SET_TITLE" => array(),
		"SET_STATUS_404" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_15"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_16"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_17"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPropertyID,
			"ADDITIONAL_VALUES" => "Y",
		),
		"PRICE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_20"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
			"ADDITIONAL_VALUES" => "Y",
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_18"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_19"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ZAKAZ" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_ZAKAZ"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PROPERTY_CODE_LABEL" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_SECTION_P_PROPERTY_CODE_LABEL"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);
?>