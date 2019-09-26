<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_SECTION_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_SECTION_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_sliding.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "ASDAFF",
        "CHILD" => array(
            "ID" => "media",
            "NAME" => "Мультимедия",
            "SORT" => 30
        ),
	),
);

?>
