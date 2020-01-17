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

$arLanguage = array(
	"en" => GetMessage("ES_LANGUAGE_EN"),
	"id" => GetMessage("ES_LANGUAGE_ID"),
	"es" => GetMessage("ES_LANGUAGE_ES"),
	"it" => GetMessage("ES_LANGUAGE_IT"),
	"ko" => GetMessage("ES_LANGUAGE_KO"),
	"de" => GetMessage("ES_LANGUAGE_DE"),
	"nl" => GetMessage("ES_LANGUAGE_NL"),
	"pt" => GetMessage("ES_LANGUAGE_PT"),
	"ru" => GetMessage("ES_LANGUAGE_RU"),
	"tr" => GetMessage("ES_LANGUAGE_TR"),
	"fr" => GetMessage("ES_LANGUAGE_FR"),
	"ja" => GetMessage("ES_LANGUAGE_JA"),
);

$arTweetAutoRefresh = array(
	"0" => GetMessage("ES_TWITTER_AUTOREFRESH_OFF"),
	"60000" => GetMessage("ES_TWITTER_AUTOREFRESH_1"),
	"300000" => GetMessage("ES_TWITTER_AUTOREFRESH_5"),
	"600000" => GetMessage("ES_TWITTER_AUTOREFRESH_10"),
);

$arYesNo = array(
	"true" => GetMessage("ES_YES"),
	"false" => GetMessage("ES_NO"),
);

$arButtonColor = array(
	"blue" => "blue",
	"grey" => "grey",
);

$arAlign = array(
	"left" => GetMessage("ES_DATA_ALIGN_LEFT"),
	"right" => GetMessage("ES_DATA_ALIGN_RIGHT"),
);

$arCount = array();
for($i = 1; $i <= 20; $i++) $arCount[$i] = $i;

$arComponentParameters = array(
	"GROUPS" => array(
		"ES_FOLLOWBUTTON_GROUP" => array(
			"NAME" => GetMessage("ES_FOLLOWBUTTON_GROUP"),
		),
	),
	"PARAMETERS" => array(
		"ES_INCLUDE_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ES_INCLUDE_JQUERY_MOUSEWHEEL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY_MOUSEWHEEL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ES_INCLUDE_JQUERY_JSCROLLPANE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY_JSCROLLPANE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ES_INCLUDE_JQUERY_TIMEAGO" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY_TIMEAGO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ES_INCLUDE_JQUERY_TIMEAGO_RU" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_INCLUDE_JQUERY_TIMEAGO_RU"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"ES_TWITTER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_TWITTER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_TWITTER_AUTOREFRESH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_TWITTER_AUTOREFRESH"),
			"TYPE" => "LIST",
			"VALUES" => $arTweetAutoRefresh,
			"DEFAULT" => "0",
		),
		"ES_TWITTER_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_TWITTER_COUNT"),
			"TYPE" => "LIST",
			"VALUES" => $arCount,
			"DEFAULT" => "5",
		),
		"ES_WITDH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_WITDH"),
			"TYPE" => "STRING",
			"DEFAULT" => "300",
		),
		"ES_HEIGHT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "250",
		),
		"ES_FOLLOWBUTTON_USE" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_FOLLOWBUTTON_USE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ES_DATA_SHOW_COUNT" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_SHOW_COUNT"),
			"TYPE" => "LIST",
			"VALUES" => $arYesNo,
			"DEFAULT" => "true",
		),
		"ES_DATA_BUTTON" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_BUTTON"),
			"TYPE" => "LIST",
			"VALUES" => $arButtonColor,
			"DEFAULT" => "blue",
		),
		"ES_DATA_TEXT_COLOR" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_TEXT_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "000000",
		),
		"ES_DATA_LINK_COLOR" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_LINK_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "186487",
		),
		"ES_DATA_WIDTH" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "100%",
		),
		"ES_DATA_ALIGN" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_ALIGN"),
			"TYPE" => "LIST",
			"VALUES" => $arAlign,
			"DEFAULT" => "left",
		),
		"ES_DATA_LANG" => array(
			"PARENT" => "ES_FOLLOWBUTTON_GROUP",
			"NAME" => GetMessage("ES_DATA_LANG"),
			"TYPE" => "LIST",
			"VALUES" => $arLanguage,
			"DEFAULT" => "ru",
		),
	),
);
?>