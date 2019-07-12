<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("F_INSTALL"),
	"DESCRIPTION" => GetMessage("F_CREATE"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "filter_show",
			"NAME" => GetMessage("FILTER")
		)
	),
);
?>