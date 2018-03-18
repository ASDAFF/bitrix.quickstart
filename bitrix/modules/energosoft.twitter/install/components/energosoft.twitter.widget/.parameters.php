<?
######################################################
# Name: energosoft.twitter                           #
# File: .parameters.php                              #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

$arWidgetType = array(
	"profile" => GetMessage("ES_TYPE_PROFILE"),
	"search" => GetMessage("ES_TYPE_SEARCH"),
	"faves" => GetMessage("ES_TYPE_FAVES"),
	"list" => GetMessage("ES_TYPE_LIST"),
);

$arWidgetBehavior = array(
	"all" => GetMessage("ES_BEHAVIOR_ALL"),
	"default" => GetMessage("ES_BEHAVIOR_DEFAULT"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"ES_SETTINGS" => array(
			"NAME" => GetMessage("ES_SETTINGS"),
		),
		"ES_PREFERENCES" => array(
			"NAME" => GetMessage("ES_PREFERENCES"),
		),
		"ES_DECORATION" => array(
			"NAME" => GetMessage("ES_DECORATION"),
		),
		"ES_DIMENSION" => array(
			"NAME" => GetMessage("ES_DIMENSION"),
		),
	),
	"PARAMETERS" => array(
		"ES_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arWidgetType,
			"DEFAULT" => "profile",
			"REFRESH" => "Y",
		),
	),
);

if($arCurrentValues["ES_TYPE"]=="") $arCurrentValues["ES_TYPE"] = "profile";
if($arCurrentValues["ES_TYPE"]=="profile")
{
	$arComponentParameters["PARAMETERS"]["ES_TWITTER"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_TWITTER"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}
if($arCurrentValues["ES_TYPE"]=="search")
{
	$arComponentParameters["PARAMETERS"]["ES_SEARCH"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_SEARCH"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_TITLE"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_SUBJECT"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_SUBJECT"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}
if($arCurrentValues["ES_TYPE"]=="faves")
{
	$arComponentParameters["PARAMETERS"]["ES_TWITTER"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_TWITTER"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_TITLE"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_SUBJECT"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_SUBJECT"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}
if($arCurrentValues["ES_TYPE"]=="list")
{
	$arComponentParameters["PARAMETERS"]["ES_TWITTER"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_TWITTER"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_LIST"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_LIST"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_TITLE"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	$arComponentParameters["PARAMETERS"]["ES_SUBJECT"] = array(
		"PARENT" => "ES_SETTINGS",
		"NAME" => GetMessage("ES_SUBJECT"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}

// PREFERENCES GROUP
$arComponentParameters["PARAMETERS"]["ES_REFRESH"] = array(
	"PARENT" => "ES_PREFERENCES",
	"NAME" => GetMessage("ES_REFRESH"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arComponentParameters["PARAMETERS"]["ES_SCROLL"] = array(
	"PARENT" => "ES_PREFERENCES",
	"NAME" => GetMessage("ES_SCROLL"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arComponentParameters["PARAMETERS"]["ES_BEHAVIOR"] = array(
	"PARENT" => "ES_PREFERENCES",
	"NAME" => GetMessage("ES_BEHAVIOR"),
	"TYPE" => "LIST",
	"VALUES" => $arWidgetBehavior,
	"DEFAULT" => "all",
	"REFRESH" => "Y",
);
if($arCurrentValues["ES_BEHAVIOR"]=="") $arCurrentValues["ES_BEHAVIOR"] = "all";
if($arCurrentValues["ES_BEHAVIOR"]=="default")
{
	$arComponentParameters["PARAMETERS"]["ES_LOOP"] = array(
		"PARENT" => "ES_PREFERENCES",
		"NAME" => GetMessage("ES_LOOP"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	);
	$arCount = array();
	for($i = 30; $i <= 60; $i++) $arCount[$i] = $i;
	if($arCurrentValues["ES_INTERVAL"]=="") $arCurrentValues["ES_INTERVAL"]="30";
	$arComponentParameters["PARAMETERS"]["ES_INTERVAL"] = array(
		"PARENT" => "ES_PREFERENCES",
		"NAME" => GetMessage("ES_INTERVAL"),
		"TYPE" => "LIST",
		"VALUES" => $arCount,
		"DEFAULT" => "30",
	);
}
if($arCurrentValues["ES_TYPE"]=="profile")
{
	$arCount = array();
	for($i = 1; $i <= 30; $i++) $arCount[$i] = $i;
	if($arCurrentValues["ES_COUNT"]=="") $arCurrentValues["ES_COUNT"]="4";
	if($arCurrentValues["ES_COUNT"]<1 || $arCurrentValues["ES_COUNT"]>30) $arCurrentValues["ES_COUNT"]="4";
	$arComponentParameters["PARAMETERS"]["ES_COUNT"] = array(
		"PARENT" => "ES_PREFERENCES",
		"NAME" => GetMessage("ES_COUNT"),
		"TYPE" => "LIST",
		"VALUES" => $arCount,
		"DEFAULT" => "4",
	);
}
if($arCurrentValues["ES_TYPE"]=="search")
{
	$arCount = array();
	for($i = 1; $i <= 100; $i++) $arCount[$i] = $i;
	if($arCurrentValues["ES_COUNT"]=="") $arCurrentValues["ES_COUNT"]="30";
	if($arCurrentValues["ES_COUNT"]<1 || $arCurrentValues["ES_COUNT"]>100) $arCurrentValues["ES_COUNT"]="4";
	$arComponentParameters["PARAMETERS"]["ES_COUNT"] = array(
		"PARENT" => "ES_PREFERENCES",
		"NAME" => GetMessage("ES_COUNT"),
		"TYPE" => "LIST",
		"VALUES" => $arCount,
		"DEFAULT" => "30",
	);
}
if($arCurrentValues["ES_TYPE"]=="faves")
{
	$arCount = array();
	for($i = 1; $i <= 20; $i++) $arCount[$i] = $i;
	if($arCurrentValues["ES_COUNT"]=="") $arCurrentValues["ES_COUNT"]="10";
	if($arCurrentValues["ES_COUNT"]<1 || $arCurrentValues["ES_COUNT"]>20) $arCurrentValues["ES_COUNT"]="4";
	$arComponentParameters["PARAMETERS"]["ES_COUNT"] = array(
		"PARENT" => "ES_PREFERENCES",
		"NAME" => GetMessage("ES_COUNT"),
		"TYPE" => "LIST",
		"VALUES" => $arCount,
		"DEFAULT" => "10",
	);
}
if($arCurrentValues["ES_TYPE"]=="list")
{
	$arCount = array();
	for($i = 1; $i <= 100; $i++) $arCount[$i] = $i;
	if($arCurrentValues["ES_COUNT"]=="") $arCurrentValues["ES_COUNT"]="30";
	if($arCurrentValues["ES_COUNT"]<1 || $arCurrentValues["ES_COUNT"]>100) $arCurrentValues["ES_COUNT"]="4";
	$arComponentParameters["PARAMETERS"]["ES_COUNT"] = array(
		"PARENT" => "ES_PREFERENCES",
		"NAME" => GetMessage("ES_COUNT"),
		"TYPE" => "LIST",
		"VALUES" => $arCount,
		"DEFAULT" => "30",
	);
}

// DECORATION GROUP
$arComponentParameters["PARAMETERS"]["ES_SHELL_BACKGROUND"] = array(
	"PARENT" => "ES_DECORATION",
	"NAME" => GetMessage("ES_SHELL_BACKGROUND"),
	"TYPE" => "COLORPICKER",
	"DEFAULT" => "333333",
);
$arComponentParameters["PARAMETERS"]["ES_SHELL_COLOR"] = array(
	"PARENT" => "ES_DECORATION",
	"NAME" => GetMessage("ES_SHELL_COLOR"),
	"TYPE" => "COLORPICKER",
	"DEFAULT" => "FFFFFF",
);
$arComponentParameters["PARAMETERS"]["ES_TWEETS_BACKGROUND"] = array(
	"PARENT" => "ES_DECORATION",
	"NAME" => GetMessage("ES_TWEETS_BACKGROUND"),
	"TYPE" => "COLORPICKER",
	"DEFAULT" => "000000",
);
$arComponentParameters["PARAMETERS"]["ES_TWEETS_COLOR"] = array(
	"PARENT" => "ES_DECORATION",
	"NAME" => GetMessage("ES_TWEETS_COLOR"),
	"TYPE" => "COLORPICKER",
	"DEFAULT" => "FFFFFF",
);
$arComponentParameters["PARAMETERS"]["ES_TWEETS_LINKS"] = array(
	"PARENT" => "ES_DECORATION",
	"NAME" => GetMessage("ES_TWEETS_LINKS"),
	"TYPE" => "COLORPICKER",
	"DEFAULT" => "4AED05",
);

// DIMENSION GROUP
$arComponentParameters["PARAMETERS"]["ES_WITDH"] = array(
	"PARENT" => "ES_DIMENSION",
	"NAME" => GetMessage("ES_WITDH"),
	"TYPE" => "STRING",
	"DEFAULT" => "250",
);
$arComponentParameters["PARAMETERS"]["ES_HEIGHT"] = array(
	"PARENT" => "ES_DIMENSION",
	"NAME" => GetMessage("ES_HEIGHT"),
	"TYPE" => "STRING",
	"DEFAULT" => "300",
);
$arComponentParameters["PARAMETERS"]["ES_WITDH_AUTO"] = array(
	"PARENT" => "ES_DIMENSION",
	"NAME" => GetMessage("ES_WITDH_AUTO"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
?>