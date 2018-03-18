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
		"ID_ELEMENT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ID_ELEMENT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25
		),
		"TYPE_BUTTON" => Array(
			"NAME"=>GetMessage("TYPE_BUTTON"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "full", 
			"VALUES"=>array(
				"full" => GetMessage("TYPE_BUTTON_FULL"),
				"button" => GetMessage("TYPE_BUTTON_BUTTON"),
				"mini" => GetMessage("TYPE_BUTTON_MINI"),
				"vertical" => GetMessage("TYPE_BUTTON_VERTICAL")), 
			"ADDITIONAL_VALUES"=>"N"
		),
		"HEIGHT_BUTTON" => Array(
			"NAME"=>GetMessage("HEIGHT_BUTTON"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "22", 
			"VALUES"=>array(
				"18" => "18 px",
				"20" => "20 px",
				"22" => "22 px",
				"24" => "24 px"), 
			"ADDITIONAL_VALUES"=>"N"
		),
		"NAME_BUTTON" => Array(
			"NAME"=>GetMessage("NAME_BUTTON"), 
			"PARENT" => "FORM_SETTINGS",
			"TYPE"=>"LIST", 
			"DEFAULT" => "like", 
			"VALUES"=>array(
				"like" => GetMessage("NAME_BUTTON_LIKE"),
				"interes" => GetMessage("NAME_BUTTON_INTERES")), 
			"ADDITIONAL_VALUES"=>"N"
		),
	),
);
?>
