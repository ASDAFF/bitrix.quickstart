<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SPO_NAME"),
	"DESCRIPTION" => GetMessage("SPO_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
// UnitellerPlugin change
			'ID' => 'sale_personal_uniteller',
// /UnitellerPlugin change
			"NAME" => GetMessage("SPO_MAIN")
		)
	),
);
?>