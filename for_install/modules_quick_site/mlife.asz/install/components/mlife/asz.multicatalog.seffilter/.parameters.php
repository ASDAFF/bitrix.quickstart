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
$arProperty_N = array();
$arProperty_LS = array();
if (0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F"){
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			$arProperty[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
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
		
		if($arr["PROPERTY_TYPE"]=="L" || $arr["PROPERTY_TYPE"]=="S")
			$arProperty_LS[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arProperty_UF = array();
$arSProperty_LNS = array();

$arComponentParameters = array(
	"GROUPS" => array(
		"BASE" => array(
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_1"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_2"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_3"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_4"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_5"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_6"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_P_7"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
	),
);
if(!empty($arCurrentValues["PROPERTY_CODE"])){
	
	foreach($arProperty_N as $propId=>$prop){
		if(in_array($propId,$arCurrentValues["PROPERTY_CODE"])){
			$arComponentParameters["PARAMETERS"]["D_PROP_".$propId] = array(
				"NAME" => $prop,
				"TYPE" => "LIST",
				"VALUES" => array(
					"MODE1" => GetMessage("MLIFE_PD_FILTER_PARAM_D_PROP_MODE1"),
				),
				"PARENT" => "VISUAL",
			);
			$arComponentParameters["PARAMETERS"]["D_PROP_PARAM_".$propId] = array(
				"NAME" => $prop." - ".GetMessage("MLIFE_PD_FILTER_PARAM_D_PROP_MODE2"),
				"TYPE" => "TEXT",
				"PARENT" => "VISUAL",
			);
		}
	}
	
	foreach($arProperty_LS as $propId=>$prop){
		if(in_array($propId,$arCurrentValues["PROPERTY_CODE"])){
			$arComponentParameters["PARAMETERS"]["D_PROP_".$propId] = array(
				"NAME" => $prop,
				"TYPE" => "LIST",
				"VALUES" => array(
					"MODE4" => GetMessage("MLIFE_PD_FILTER_PARAM_D_PROP_MODE3"),
					"MODE5" => GetMessage("MLIFE_PD_FILTER_PARAM_D_PROP_MODE4"),
				),
				"PARENT" => "VISUAL",
			);
			$arComponentParameters["PARAMETERS"]["D_PROP_PARAM_".$propId] = array(
				"NAME" => $prop." - ".GetMessage("MLIFE_PD_FILTER_PARAM_D_PROP_MODE2"),
				"TYPE" => "TEXT",
				"PARENT" => "VISUAL",
			);
		}
	}
	
}
$arComponentParameters["PARAMETERS"]["PROP_HIDE"] = array(
	"NAME" => GetMessage("MLIFE_PD_FILTER_PARAM_PROP_HIDE"),
	"TYPE" => "LIST",
	"VALUES" => $arProperty,
	"PARENT" => "VISUAL",
	"MULTIPLE" => "Y",
);
?>