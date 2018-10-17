<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MD_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MD_COMPONENT_DESCR"),
	"ICON" => "/images/feedback.gif",
	"PATH" => array(
		"ID" => "micros",
		"CHILD" => array(
			"ID" => "request",
			"NAME" => GetMessage("MICROS_TECH")
		)

	),
);
?>