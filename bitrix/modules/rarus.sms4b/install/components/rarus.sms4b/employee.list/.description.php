<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("EMPLOYEE_LIST"),
	"DESCRIPTION" => GetMessage("EMPLOYEE_LIST_DESC"),
	"ICON" => "/images/comp.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "sms4b",
		"NAME" => GetMessage("NAME"),
		"CHILD" => array(
			"ID" => "CorPor",
			"NAME" => GetMessage("SUB_SECTION")
		)
	),
);
?>