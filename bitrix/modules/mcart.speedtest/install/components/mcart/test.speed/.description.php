<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arComponentDescription = array(
	"NAME" => GetMessage("MCART_TEST_SPEED"),
	"DESCRIPTION" => GetMessage("MCART_TEST_SPEED_DESC"),
	"ICON" => "/images/eaddform.gif",
	"PATH" => array(
		"ID" => "mcart",
		"NAME" => GetMessage("MCART_PARTNER_NAME"),
		"CHILD" => array(
			"ID" => "utils",
			"NAME" => GetMessage("MCART_UTILS")
		),
	),
);
?>