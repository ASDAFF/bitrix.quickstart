<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "BASKET_URL" => array(
            "NAME" => GetMessage("BASKET_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        )
	),
);
?>
