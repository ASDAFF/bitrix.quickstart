<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SS_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("SS_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 30,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "ss",
		"NAME" => GetMessage("SS_DESC_SYSTEM_GROUP_NAME"),
		"CHILD" => array(
			"ID" => "ss_geoip",
			"NAME" => GetMessage("SS_DESC_SYSTEM_SECTION_NAME"),
		),
	),
);
?>
