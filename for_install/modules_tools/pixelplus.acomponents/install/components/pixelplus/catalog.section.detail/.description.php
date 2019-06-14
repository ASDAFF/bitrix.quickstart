<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arComponentDescription = array(
	"NAME" => GetMessage("PX_SECTION_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("PX_SECTION_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_list.gif",
	"PATH" => array(
		"ID" => "pixelplus",
		"NAME" => GetMessage("PIXELPLUS_PARENT"),
		"CHILD" => array(
			"ID" => "pixelplusacomponents",
			"NAME" => GetMessage("PX_SECTION_DESC_CATALOG"),
		)
	)
);
?>