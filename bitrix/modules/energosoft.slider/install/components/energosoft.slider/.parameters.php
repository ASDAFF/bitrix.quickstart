<?
######################################################
# Name: energosoft.slider                            #
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

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()) $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty = array();
$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while($p=$rsProp->Fetch()) $arProperty[$p["CODE"]] = "[".$p["CODE"]."] ".$p["NAME"];

$arUrlType = array(
	"none" => GetMessage("ES_URL_TYPE_NONE"),
	"property" => GetMessage("ES_URL_TYPE_PROPERTY"),
	"iblock" => GetMessage("ES_URL_TYPE_IBLOCK"),
);

$arPropertyTarget = array(
	"_self" => GetMessage("ES_PROPERTY_URL_TARGET_SELF"),
	"_blank" => GetMessage("ES_PROPERTY_URL_TARGET_BLANK"),
);

$arSortFields = array(
	"id" => GetMessage("ES_SORT_FIELD_ID"),
	"name" => GetMessage("ES_SORT_FIELD_NAME"),
	"sort" => GetMessage("ES_SORT_FIELD_SORT"),
	"shows" => GetMessage("ES_SORT_FILED_SHOWS"),
	"timestamp_x" => GetMessage("ES_SORT_FIELD_TIMESTAMP"),
	"active_from" => GetMessage("ES_SORT_FILED_ACTIVE_FROM"),
	"active_to" => GetMessage("ES_SORT_FILED_ACTIVE_TO"),
);

$arAscDescRand = array(
	"asc" => GetMessage("ES_SORT_ORDER_ASC"),
	"desc" => GetMessage("ES_SORT_ORDER_DESC"),
	"rand" => GetMessage("ES_SORT_ORDER_RAND"),
);

$arOrientation = array(
	"false" => GetMessage("ES_ORIENTATION_HORIZONTAL"),
	"true" => GetMessage("ES_ORIENTATION_VERTICAL"),
);

$arRtl = array(
	"false" => GetMessage("ES_RTL_FALSE"),
	"true" => GetMessage("ES_RTL_TRUE"),
);

$arWrap = array(
	"null" => GetMessage("ES_WRAP_NULL"),
	"first" => GetMessage("ES_WRAP_FIRST"),
	"last" => GetMessage("ES_WRAP_LAST"),
	"both" => GetMessage("ES_WRAP_BOTH"),
	"circular" => GetMessage("ES_WRAP_CIRCULAR"),
);

$arEffect = array(
	"linear" => "linear",
	"swing" => "swing",
	"backEaseIn" => "backEaseIn",
	"backEaseOut" => "backEaseOut",
	"backEaseInOut" => "backEaseInOut",
	"bounceEaseIn" => "bounceEaseIn",
	"bounceEaseOut" => "bounceEaseOut",
	"circEaseIn" => "circEaseIn",
	"circEaseOut" => "circEaseOut",
	"circEaseInOut" => "circEaseInOut",
	"cubicEaseIn" => "cubicEaseIn",
	"cubicEaseOut" => "cubicEaseOut",
	"cubicEaseInOut" => "cubicEaseInOut",
	"elasticEaseIn" => "elasticEaseIn",
	"elasticEaseOut" => "elasticEaseOut",
	"expoEaseIn" => "expoEaseIn",
	"expoEaseOut" => "expoEaseOut",
	"expoEaseInOut" => "expoEaseInOut",
	"quadEaseIn" => "quadEaseIn",
	"quadEaseOut" => "quadEaseOut",
	"quadEaseInOut" => "quadEaseInOut",
	"quartEaseIn" => "quartEaseIn",
	"quartEaseOut" => "quartEaseOut",
	"quartEaseInOut" => "quartEaseInOut",
	"quintEaseIn" => "quintEaseIn",
	"quintEaseOut" => "quintEaseOut",
	"quintEaseInOut" => "quintEaseInOut",
	"sineEaseIn" => "sineEaseIn",
	"sineEaseOut" => "sineEaseOut",
	"sineEaseInOut" => "sineEaseInOut",
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ES_INCLUDE_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ES_INCLUDE_JQUERY_EASING" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY_EASING"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ES_INCLUDE_JQUERY_JCAROUSEL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY_JCAROUSEL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"ES_SECTION_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ES_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_SORT_FIELD" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ES_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSortFields,
			"DEFAULT" => "sort",
		),
		"ES_SORT_ORDER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ES_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDescRand,
			"DEFAULT" => "asc",
		),
		"ES_PROPERTY" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ES_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"REFRESH" => "Y",
		),
		"ES_URL_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("ES_URL_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arUrlType,
			"REFRESH" => "Y",
			"DEFAULT" => "none",
		),
	),
);
if($arCurrentValues["ES_URL_TYPE"] == "property")
{
	$arComponentParameters["PARAMETERS"]["ES_PROPERTY_URL"] = array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("ES_PROPERTY_URL"),
		"TYPE" => "LIST",
		"VALUES" => $arProperty,
		"REFRESH" => "Y",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_PROPERTY_URL_TARGET"] = array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("ES_PROPERTY_URL_TARGET"),
		"TYPE" => "LIST",
		"VALUES" => $arPropertyTarget,
		"DEFAULT" => "_self",
	);
}
if($arCurrentValues["ES_URL_TYPE"] == "iblock")
{
	$arComponentParameters["PARAMETERS"]["ES_DETAIL_URL"] = CIBlockParameters::GetPathTemplateParam(
		"DETAIL",
		"DETAIL_URL",
		GetMessage("IBLOCK_DETAIL_URL"),
		"",
		"DATA_SOURCE"
	);
	$arComponentParameters["PARAMETERS"]["ES_PROPERTY_URL_TARGET"] = array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("ES_PROPERTY_URL_TARGET"),
		"TYPE" => "LIST",
		"VALUES" => $arPropertyTarget,
		"DEFAULT" => "_self",
	);
}
$arComponentParameters["PARAMETERS"]["ES_USEPRELOADER"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_USEPRELOADER"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arComponentParameters["PARAMETERS"]["ES_ORIENTATION"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_ORIENTATION"),
	"TYPE" => "LIST",
	"VALUES" => $arOrientation,
	"DEFAULT" => "false",
);
$arComponentParameters["PARAMETERS"]["ES_RTL"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_RTL"),
	"TYPE" => "LIST",
	"VALUES" => $arRtl,
	"DEFAULT" => "false",
);
$arComponentParameters["PARAMETERS"]["ES_BLOCK_WITDH"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_BLOCK_WITDH"),
	"TYPE" => "STRING",
	"DEFAULT" => "125",
);
$arComponentParameters["PARAMETERS"]["ES_BLOCK_HEIGHT"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_BLOCK_HEIGHT"),
	"TYPE" => "STRING",
	"DEFAULT" => "55",
);
$arComponentParameters["PARAMETERS"]["ES_BLOCK_MARGIN"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_BLOCK_MARGIN"),
	"TYPE" => "STRING",
	"DEFAULT" => "10",
);
$arComponentParameters["PARAMETERS"]["ES_COUNT"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_COUNT"),
	"TYPE" => "STRING",
	"DEFAULT" => "4",
);
$arComponentParameters["PARAMETERS"]["ES_STEP"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_STEP"),
	"TYPE" => "STRING",
	"DEFAULT" => "4",
);
$arComponentParameters["PARAMETERS"]["ES_SHOW_BUTTONS"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_SHOW_BUTTONS"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);
$arComponentParameters["PARAMETERS"]["ES_AUTO"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_AUTO"),
	"TYPE" => "STRING",
	"DEFAULT" => "0",
);
$arComponentParameters["PARAMETERS"]["ES_WRAP"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_WRAP"),
	"TYPE" => "LIST",
	"VALUES" => $arWrap,
	"DEFAULT" => "circular",
);
$arComponentParameters["PARAMETERS"]["ES_EFFECT"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_EFFECT"),
	"TYPE" => "LIST",
	"VALUES" => $arEffect,
	"DEFAULT" => "linear",
);
$arComponentParameters["PARAMETERS"]["ES_ANIMATION"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("ES_ANIMATION"),
	"TYPE" => "STRING",
	"DEFAULT" => "normal",
);
$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array("DEFAULT"=>3600);
?>