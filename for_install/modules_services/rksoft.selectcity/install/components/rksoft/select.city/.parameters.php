<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// component parameters

if(!CModule::IncludeModule("sale")) return;

$arComponentParameters = Array(
	"GROUPS" => Array(),
	"PARAMETERS" => Array(
		"ID_LOC_DEFAULT" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("rksoft.select_city_ID_LOC_DEFAULT"),
			"TYPE" => "STRING",
			"VALUES" => "",
		),
		
		"USE_LOC_GROUPS" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("rksoft.select_city_USE_LOC_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		
		"ID_LOC_GROUP_DEFAULT" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("rksoft.select_city_ID_LOC_GROUP_DEFAULT"),
			"TYPE" => "STRING",
			"VALUES" => "",
		),
		
		"LANG" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" =>  GetMessage("rksoft.select_city_LANG"),
			"TYPE" => "LIST",
			VALUES => array(
				"RU" => GetMessage("rksoft.select_city_LANG_RU"),
				"EN" => GetMessage("rksoft.select_city_LANG_EN"),
			),
			"DEFAULT" => "RU",
		),
		
		"CACHE_TIME"  =>  Array("DEFAULT" => 86400),
	),
);

if($arCurrentValues["USE_LOC_GROUPS"] != "Y") unset($arComponentParameters["PARAMETERS"]["ID_LOC_GROUP_DEFAULT"]);
?>