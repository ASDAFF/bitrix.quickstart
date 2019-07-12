<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('VIVOD'),
	"DESCRIPTION" => GetMessage('VIVODIT'),
	"ICON" => "",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "filter_request",
			"NAME" => GetMessage('FILTER')
		)
	),
);
?>