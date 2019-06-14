<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SPOD_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("SPOD_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/sale_order_detail.gif",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
// UnitellerPlugin change
			'ID' => 'sale_personal_uniteller',
// /UnitellerPlugin change
			"NAME" => GetMessage("SPOD_NAME")
		)
	),
);
?>