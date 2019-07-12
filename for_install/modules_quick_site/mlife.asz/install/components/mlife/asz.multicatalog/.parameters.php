<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
global $USER_FIELD_MANAGER;

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arProperty = array();
$arPropertyID = array();
$arProperty_N = array();
$arProperty_L = array();
$arProperty_X = array();
if (0 < intval($arCurrentValues["IBLOCK_ID"]))
{
	$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F"){
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			$arPropertyID[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}

		if($arr["PROPERTY_TYPE"] == "N")
			$arProperty_N[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			
		if($arr["PROPERTY_TYPE"] == "L")
			$arProperty_L[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

		if ($arr["PROPERTY_TYPE"] != "F")
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
$arProperty_LNS = $arProperty;

//получаем типы цен для текущего сайта
$price = \Mlife\Asz\PricetipTable::getList(
	array(
		'select' => array('ID','NAME',"BASE", "SITE_ID"),
		//'filter' => array("LOGIC"=>"OR",array("=SITE_ID"=>SITE_ID),array("=SITE_ID"=>false)),
	)
);
$arPrice = array();
while($arPricedb = $price->Fetch()){
	$arPrice[$arPricedb["ID"]] = "[".$arPricedb["SITE_ID"]."] - ".$arPricedb["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"FILTER_SETTINGS" => array(
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_1"),
		),
		"LIST_SETTINGS" => array(
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_2"),
		),
		"DETAIL_SETTINGS" => array(
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_3"),
		),
		"BASE" => array(
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_4"),
		),
	),
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => array(
			"SECTION_ID" => array("NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_5")),
			"ELEMENT_ID" => array("NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_6")),
		),
		"SEF_MODE" => array(
			"sections" => array(
				"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_7"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"section" => array(
				"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_8"),
				"DEFAULT" => "#SECTION_ID#/",
				"VARIABLES" => array("SECTION_ID"=>"SID"),
			),
			"element" => array(
				"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_9"),
				"DEFAULT" => "#SECTION_ID#/#ELEMENT_ID#/",
				"VARIABLES" => array("ELEMENT_ID"=>"EID"),
			),
			"filter" => array(
				"NAME" => GetMessage("MLIFE_PORTAL_DOSKA_FILTER_PAGE"),
				"DEFAULT" => "#SECTION_ID#/filter_#FILTER_ID#/",
				"VARIABLES" => array("FILTER_ID"=>"FID"),
			),
			"filtersection" => array(
				"NAME" => GetMessage("MLIFE_PORTAL_DOSKA_FILTERSECTION_PAGE"),
				"DEFAULT" => "filter_#FILTER_ID#/",
				"VARIABLES" => array("FILTER_ID"=>"FID"),
			),
		),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_10"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_11"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"USE_FILTER" => array(
			"PARENT" => "FILTER_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_12"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_13"),
			"TYPE" => "STRING",
			"DEFAULT" => "30",
		),
		"LIST_PROPERTY_CODE" => array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_14"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_LNS,
		),
		"DETAIL_PROPERTY_CODE" => array(
			"PARENT" => "DETAIL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_14"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_LNS,
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_15"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_16"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SET_STATUS_404" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_17"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PRICE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_18"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
			"ADDITIONAL_VALUES" => "Y",
		),
		"ZAKAZ" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_ZAKAZ"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PROPERTY_CODE_LABEL" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_PROPERTY_CODE_LABEL"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);

if($arCurrentValues["USE_FILTER"]=="Y")
{
	$arComponentParameters["PARAMETERS"]["FILTER_NAME"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_19"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["USE_FILTER_SUPER"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_PROP_SHOW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	);
	$arComponentParameters["PARAMETERS"]["FILTER_PROPERTY_CODE"] = array(
		"PARENT" => "FILTER_SETTINGS",
		"NAME" => GetMessage("MLIFE_ASZ_CATALOG_P_14"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPropertyID,
		"ADDITIONAL_VALUES" => "Y",
		"REFRESH" => "Y",
	);
}

if(!empty($arCurrentValues["FILTER_PROPERTY_CODE"]) && $arCurrentValues["USE_FILTER_SUPER"]=="Y"){
	
	foreach($arProperty_N as $propId=>$prop){
		if(in_array($propId,$arCurrentValues["FILTER_PROPERTY_CODE"])){
			$arComponentParameters["PARAMETERS"]["D_PROP_".$propId] = array(
				"NAME" => $prop,
				"TYPE" => "LIST",
				"VALUES" => array(
					"MODE1" => GetMessage("MLIFE_ASZ_CATALOG_P_PROP_MODE1"),
				),
				"PARENT" => "FILTER_SETTINGS",
			);
			$arComponentParameters["PARAMETERS"]["D_PROP_PARAM_".$propId] = array(
				"NAME" => $prop." - ".GetMessage("MLIFE_ASZ_CATALOG_P_PROP_MODE2"),
				"TYPE" => "TEXT",
				"PARENT" => "FILTER_SETTINGS",
			);
		}
	}
	
	foreach($arProperty_L as $propId=>$prop){
		if(in_array($propId,$arCurrentValues["FILTER_PROPERTY_CODE"])){
			$arComponentParameters["PARAMETERS"]["D_PROP_".$propId] = array(
				"NAME" => $prop,
				"TYPE" => "LIST",
				"VALUES" => array(
					"MODE4" => GetMessage("MLIFE_ASZ_CATALOG_P_PROP_MODE3"),
					"MODE5" => GetMessage("MLIFE_ASZ_CATALOG_P_PROP_MODE4"),
				),
				"PARENT" => "FILTER_SETTINGS",
			);
			$arComponentParameters["PARAMETERS"]["D_PROP_PARAM_".$propId] = array(
				"NAME" => $prop." - ".GetMessage("MLIFE_ASZ_CATALOG_P_PROP_MODE2"),
				"TYPE" => "TEXT",
				"PARENT" => "FILTER_SETTINGS",
			);
		}
	}
	
}

?>