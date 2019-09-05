<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentDescription = array(
	"NAME" => GetMessage("CD_BSN_NAME"),
	"DESCRIPTION" => GetMessage("CD_BSN_DESCRIPTION"),
	"ICON" => "/images/component.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "subscribe",
			"NAME" => GetMessage("CD_BSN_SERVICE")
		)
	),
);