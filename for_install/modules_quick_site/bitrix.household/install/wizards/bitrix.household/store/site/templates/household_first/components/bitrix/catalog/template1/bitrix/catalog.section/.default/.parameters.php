<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;
$arTemplateParameters = array(
	"RATING_TYPE" => Array(
		"NAME" => GetMessage("RATING_TYPE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"VALUES" => array(			
			"bitrix" => GetMessage("bitrix"),
			"average" => GetMessage("average"),
		),		
		"DEFAULT" => "bitrix",
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
