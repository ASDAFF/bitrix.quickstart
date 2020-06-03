<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SORT_PANEL_COMPONENT_NAME_VALUE"),
	"DESCRIPTION" => GetMessage("SORT_PANEL_COMPONENT_DESCRIPTION_VALUE"),
	"ICON" => "/images/icon.gif",
	"SORT" => 100,
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		"SORT" => 500,
		"CHILD" => array(
			"ID" => 'content',
			"NAME" => GetMessage("SORT_PANEL_COMPONENT_TYPE_CONTENT_VALUE"),
			"SORT" => 500,
		)
	),
);