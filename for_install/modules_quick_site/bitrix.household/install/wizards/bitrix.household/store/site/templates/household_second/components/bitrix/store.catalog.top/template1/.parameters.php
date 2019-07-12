<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"SECTION_NAME" => Array(
		"NAME" => GetMessage("SECTION_NAME"),
		"TYPE" => "TEXT",
		"DEFAULT" => "Новинки",
	),

	"SECTION_LINK" => Array(
		"NAME" => GetMessage("SECTION_LINK"),
		"TYPE" => "TEXT",
		"DEFAULT" => "bestprice.php",
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
