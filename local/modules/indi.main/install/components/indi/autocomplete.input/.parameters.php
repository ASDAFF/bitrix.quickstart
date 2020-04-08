<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!CModule::IncludeModule("search")) {
	return;
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SEARCH_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "search",
		),
		"VALUE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SEARCH_VALUE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CLASS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SEARCH_CLASS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CONTROLLER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SEARCH_CONTROLLER"),
			"TYPE" => "STRING",
			"DEFAULT" => "/ajax/search/tags/",
		),
	),
);