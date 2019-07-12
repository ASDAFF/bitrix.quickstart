<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

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

//получаем типы цен для текущего сайта
$price = \Mlife\Asz\PricetipTable::getList(
	array(
		'select' => array('ID','NAME',"BASE"),
		'filter' => array("LOGIC"=>"OR",array("=SITE_ID"=>SITE_ID),array("=SITE_ID"=>false)),
	)
);
$arPrice = array();
while($arPricedb = $price->Fetch()){
	$arPrice[$arPricedb["ID"]] = $arPricedb["NAME"];
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
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_1"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_2"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_3"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_4"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"ADD_SECTIONS_CHAIN" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_5"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ADD_ELEMENT_CHAIN" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_12"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SET_TITLE" => array(),
		"SET_STATUS_404" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_6"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_7"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_8"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PRICE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_9"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
			"ADDITIONAL_VALUES" => "Y",
		),
		"HIDE_BY" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_10"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"HIDE_QUANT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_ELEMENT_P_11"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ZAKAZ" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
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
?>