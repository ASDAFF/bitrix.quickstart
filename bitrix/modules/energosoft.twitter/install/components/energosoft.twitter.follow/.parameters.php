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

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ES_TWITTER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_TWITTER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_DATA_SHOW_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_SHOW_COUNT"),
			"TYPE" => "LIST",
			"VALUES" => $arYesNo,
			"DEFAULT" => "true",
		),
		"ES_DATA_BUTTON" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_BUTTON"),
			"TYPE" => "LIST",
			"VALUES" => $arButtonColor,
			"DEFAULT" => "blue",
		),
		"ES_DATA_TEXT_COLOR" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_TEXT_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "000000",
		),
		"ES_DATA_LINK_COLOR" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_LINK_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "186487",
		),
		"ES_DATA_WIDTH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_WIDTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "100%",
		),
		"ES_DATA_ALIGN" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_ALIGN"),
			"TYPE" => "LIST",
			"VALUES" => $arAlign,
			"DEFAULT" => "left",
		),
		"ES_DATA_LANG" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_LANG"),
			"TYPE" => "LIST",
			"VALUES" => $arLanguage,
			"DEFAULT" => "ru",
		),
	),
);
?>