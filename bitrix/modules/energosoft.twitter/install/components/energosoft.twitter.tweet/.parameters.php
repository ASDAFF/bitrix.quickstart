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

$arDataCount = array(
	"vertical" => GetMessage("ES_DATA_COUNT_VERTICAL"),
	"horizontal" => GetMessage("ES_DATA_COUNT_HORIZONTAL"),
	"none" => GetMessage("ES_DATA_COUNT_NONE"),
);

$arDataText = array(
	"header" => GetMessage("ES_DATA_TEXT_HEADER"),
	"self" => GetMessage("ES_DATA_TEXT_SELF"),
);

$arDataUrl = array(
	"currentpage" => GetMessage("ES_DATA_URL_CURRENTPAGE"),
	"self" => GetMessage("ES_DATA_URL_SELF"),
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"ES_TWITTER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_TWITTER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_DATA_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_COUNT"),
			"TYPE" => "LIST",
			"VALUES" => $arDataCount,
			"DEFAULT" => "vertical",
		),
		"ES_DATA_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_TEXT"),
			"TYPE" => "LIST",
			"VALUES" => $arDataText,
			"DEFAULT" => "header",
		),
		"ES_DATA_TEXT_SELF" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_TEXT_SELF"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_DATA_URL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_URL"),
			"TYPE" => "LIST",
			"VALUES" => $arDataUrl,
			"DEFAULT" => "currentpage",
		),
		"ES_DATA_URL_SELF" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_URL_SELF"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_DATA_RELATED" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_RELATED"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"ES_DATA_RELATED_DESCRIPTION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ES_DATA_RELATED_DESCRIPTION"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
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