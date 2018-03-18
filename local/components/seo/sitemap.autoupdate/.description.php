<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SA_NAME"),
	"DESCRIPTION" => GetMessage("SA_DESC"),
	"ICON" => "/images/sitemap.png",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "ASDAFF",
		"NAME" => GetMessage("ASDAFF"),
		"CHILD" => array(
			"ID" => "saitmap",
			"NAME" => GetMessage("SA_NAME")
		)
	),
);

?>
