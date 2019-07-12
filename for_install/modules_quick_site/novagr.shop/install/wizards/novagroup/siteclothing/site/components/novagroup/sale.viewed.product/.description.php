<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("VIEWED_NAME"),
	"DESCRIPTION" => GetMessage("VIEWED_DESCRIPTION"),
	"ICON" => "/images/sale_viewed.gif",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "sale_personal",
			"NAME" => GetMessage("VIEWED_MAIN")
		)
	),
);
?>