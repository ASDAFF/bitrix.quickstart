<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"GROUPS" => array(
      "FORM_SETTINGS" => array(
         "NAME" => GetMessage("FORM_SETTINGS"),
         "SORT" => 101
      ),
   ),
	"PARAMETERS" => array(
		"ID_APLICATION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ID_APLICATION"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25
		),
		"ID_POLL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ID_POLL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 30
		),
		"WIDTH_FORM" => array(
			"PARENT" => "FORM_SETTINGS",
			"NAME" => GetMessage("WIDTH_FORM"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "200",
			"COLS" => 5
		),
	),
);
?>
