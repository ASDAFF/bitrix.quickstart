<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentDescription = array(
	"NAME" => GetMessage("SEARCH_AUTOCOMPLETE_NAME"),
	"DESCRIPTION" => GetMessage("SEARCH_AUTOCOMPLETE_DESC"),
	"ICON" => "/images/component.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "search",
			"NAME" => GetMessage("SEARCH_SERVICE")
		)
	),
);