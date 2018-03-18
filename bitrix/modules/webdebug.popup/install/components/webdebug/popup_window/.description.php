<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WD_POPUP_WINDOW_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("WD_POPUP_WINDOW_COMPONENT_DESCR"),
	"ICON" => "/images/include.gif",
	"PATH" => array(
		"ID" => "webdebug",
		"NAME" => GetMessage("WD_POPUP_WINDOW_GROUP_NAME"),
	),
);
?>