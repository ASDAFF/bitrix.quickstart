<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CODEBLOGPRO_SORT_PANEL_COMPONENT_NAME_VALUE"),
	"DESCRIPTION" => GetMessage("CODEBLOGPRO_SORT_PANEL_COMPONENT_DESCRIPTION_VALUE"),
	"ICON" => "/images/icon.gif",
	"SORT" => 100,
	"PATH" => array(
		"ID" => "codeblog.pro",
		"SORT" => 500,
		"NAME" => GetMessage("CODEBLOGPRO_SORT.PANEL_COMPONENTS_FOLDER_NAME"),
		"CHILD" => array(
			"ID" => GetMessage("CODEBLOGPRO_SORT_PANEL_COMPONENT_TYPE_CONTENT_VALUE"),
			"NAME" => '',
			"SORT" => 500,
		)
	),
);