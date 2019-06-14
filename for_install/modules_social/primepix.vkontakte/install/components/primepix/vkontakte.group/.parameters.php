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
		"ID_GROUP" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ID_GROUP"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "33389398",
			"COLS" => 25
		),
		"TYPE_FORM" => Array(
			"NAME"=>GetMessage("TYPE_FORM"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "party", 
			"VALUES"=>array(
				"0" => GetMessage("TYPE_FORM_PARTIC"),
				"2" => GetMessage("TYPE_FORM_NEWS"),
				"1" => GetMessage("TYPE_FORM_NAME")), 
			"ADDITIONAL_VALUES"=>"N"
		),
		"WIDTH_FORM" => array(
			"PARENT" => "FORM_SETTINGS",
			"NAME" => GetMessage("WIDTH_FORM"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "200",
			"COLS" => 5
		),
		"HEIGHT_FORM" => array(
			"PARENT" => "FORM_SETTINGS",
			"NAME" => GetMessage("HEIGHT_FORM"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "290",
			"COLS" => 5
		),
	),
);
?>
