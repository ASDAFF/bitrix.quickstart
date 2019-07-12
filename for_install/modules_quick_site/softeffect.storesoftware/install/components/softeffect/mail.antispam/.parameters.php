<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"EMAIL"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_EMAIL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"LINK"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_LINK"),
			"TYPE" => "CHECKBOX",
		),
		"ELEMENT_CLASS"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_ELEMENT_CLASS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
	),
);
?>
