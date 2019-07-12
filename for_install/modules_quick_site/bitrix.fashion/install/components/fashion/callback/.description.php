<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ALTOP_CALLBACK_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("ALTOP_CALLBACK_COMPONENT_DESCR"),
	"ICON" => "/images/callback.gif",
	"PATH" => array(
		"ID" => "altop_tools",
		"NAME" => GetMessage("ALTOP_GROUP_NAME"),
		"CHILD" => array(
			"ID" => "altop_callback",
			"NAME" => GetMessage("ALTOP_NAME")
		)
	),
);
?>