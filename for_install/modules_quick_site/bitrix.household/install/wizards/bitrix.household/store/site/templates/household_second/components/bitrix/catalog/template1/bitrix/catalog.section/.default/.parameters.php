<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arTemplateParameters = array(
	"USE_FILTER" => Array(
		"NAME" => GetMessage("USE_FILTER"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"ADD_PRODUSER_TO_TITLE" => Array(
		"NAME" => GetMessage("ADD_PRODUSER_TO_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_FRACTION_PRICE" => Array(
		"NAME" => GetMessage("SHOW_FRACTION_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	)
);
?>
