<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ML2WEBFORMS_FD_NAME"),
	"DESCRIPTION" => GetMessage("ML2WEBFORMS_FD_DESCRIPTION"),
	"ICON" => "/images/mlform.png",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "multiline",
		"SORT" => 2100,
		"NAME" => GetMessage("ML2WEBFORMS_FD_COMPONENTS"),
		"CHILD" => array(
			"ID" => "multiline_ml2webforms",
			"NAME" => GetMessage("ML2WEBFORMS_FD_MENU_NAME"),
		)
	),
);

?>