<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MAIN_MENU_ITEMS_NAME"),
	"DESCRIPTION" => GetMessage("MAIN_MENU_ITEMS_DESC"),
	"ICON" => "/images/menu.gif",
	"PATH" => array(
		"ID" => GetMessage("MAIN_MENU_ITEMS_NAME"),
		"CHILD" => array(
			"ID" => "navigation",
			"NAME" => GetMessage("MAIN_NAVIGATION_SERVICE")
		)
	),
);

?>