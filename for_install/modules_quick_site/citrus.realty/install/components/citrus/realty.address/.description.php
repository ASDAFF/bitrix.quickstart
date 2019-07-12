<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CITRUS_MYMV_COMP_NAME"),
	"DESCRIPTION" => GetMessage("CITRUS_MYMV_COMP_DESCRIPTION"),
	"ICON" => "/images/map_view.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "citrus",
		"NAME" => GetMessage("CITRUS_C_CITRUS"),
	),
);

?>