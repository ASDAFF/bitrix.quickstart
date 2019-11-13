<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_SECTION_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_SECTION_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_sliding.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "g-tech",
		"NAME" => GetMessage("GTECH_SLIDER_SECTION_TITLE"),
		"CHILD" => array(
			"ID" => "services",
			"NAME" => GetMessage("GTECH_SLIDER_SERVICES_TITLE"),
			"SORT" => 30,
			"CHILD" => array(
				"ID" => "catalog_cmpx",
			),
		),
	),
);

?>
