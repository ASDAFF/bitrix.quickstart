<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("INCLUDE_EXTENDED_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("INCLUDE_EXTENDED_COMPONENT_DESCR"),
	"PATH" => array(
		"ID" => "ASDAFF",
		"CHILD" => array(
			"ID" => "ajaxBasket",
			"NAME" => GetMessage("INCLUDE_EXTENDED_GROUP_NAME")
		)
	),
	"ICON" => "/images/icon.gif",
);
?>