<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;
$arTemplateParameters = array(
	"DISPLAY_AS_RATING" => Array(
		"NAME" => GetMessage("TP_CBIV_DISPLAY_AS_RATING"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"rating" => GetMessage("TP_CBIV_RATING"),
			"vote_avg" => GetMessage("TP_CBIV_AVERAGE"),
		),
		"DEFAULT" => "rating",
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
