<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SUBSCR_EDIT_NAME"),
	"DESCRIPTION" => GetMessage("SUBSCR_EDIT_DESC"),
	"ICON" => "/images/subscr_edit.gif",
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