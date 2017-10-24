<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MILLCOM_MENU_NAME"),
	"DESCRIPTION" => GetMessage("MILLCOM_MENU_DESC"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "navigation",
			"NAME" => GetMessage("MAIN_NAVIGATION_SERVICE"),
		)
	)
);
?>