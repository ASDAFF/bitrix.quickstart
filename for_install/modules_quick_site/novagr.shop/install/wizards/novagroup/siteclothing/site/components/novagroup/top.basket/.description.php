<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SBBL_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("SBBL_DEFAULT_TEMPLATE_DESCRIPTION"),
	"PATH" => array(
		"ID" => "NovaGroup",
		"CHILD" => array(
			"ID" => "top.basket",
			"NAME" => GetMessage("SBBL_NAME")
		)
	),
);
?>