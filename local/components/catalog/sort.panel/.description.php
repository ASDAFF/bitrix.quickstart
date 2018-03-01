<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SORT_PANEL_COMPONENT_NAME_VALUE"),
	"DESCRIPTION" => GetMessage("SORT_PANEL_COMPONENT_DESCRIPTION_VALUE"),
	"ICON" => "/images/icon.gif",
	"SORT" => 100,
	"PATH" => array(
		"ID" => "ASDAFF",
		"SORT" => 500,
		"NAME" => GetMessage("SORT.PANEL_COMPONENTS_FOLDER_NAME"),
		"CHILD" => array(
			"ID" => GetMessage("SORT_PANEL_COMPONENT_TYPE_CONTENT_VALUE"),
			"NAME" => '',
			"SORT" => 500,
		)
	),
);