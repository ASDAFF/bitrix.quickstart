<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_DESC_REPORTS"),
	"DESCRIPTION" => GetMessage("T_DESC_REPORTS_DESC"),
	"ICON" => "/images/reports.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "sms4b",
		"NAME" => GetMessage("NAME"),
		"CHILD" => array(
			"ID" => "some",
			"NAME" => GetMessage("SUBSCR_SERVICE")
		)
	),
);
?>