<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$fields=Array(
		"NONE" => GetMessage("MD_ALL_REQ"),
		"NAME" => GetMessage("MD_NAME"),
		"EMAIL" => "E-mail",
		"MESSAGE" => GetMessage("MD_MESSAGE")
		);
if($arCurrentValues["EXT_FIELDS"]){
foreach($arCurrentValues["EXT_FIELDS"] as $field)
{
	if($field)	
	$fields[str_replace(" ", "_", $field)]=$field;

}
}

$arComponentParameters = array(
	"PARAMETERS" => array(
			
		"ALLOW_NONUSER"=>Array(
			"NAME"=>GetMessage("MD_ALLOW_NONUSER"),
			"PARENT"=>"BASE",
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"Y"
				),
		"AUTO_REGISTER"=>Array(
			"NAME"=>GetMessage("MD_AUTO_REG"),
			"PARENT"=>"BASE",
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"N"
		),
		"EXT_FIELDS" => Array(
			"NAME" => GetMessage("MD_EXT_FIELDS"),
			"TYPE" => "STRING",
			"MULTIPLE"=>"Y",
			"PARENT" => "BASE",
			"REFRESH"=>"Y"
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("MD_REQUIRED_FIELDS"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"Y",
			"VALUES" => $fields,
			"DEFAULT"=>"",
			"COLS"=>25,
			"PARENT" => "BASE",
		),
		"SHOW_CATEGORY"=>Array(
			"NAME"=>GetMessage("MD_CATEGORY"),
			"PARENT"=>"BASE",
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"Y"
		),
		"SHOW_STATUS"=>Array(
			"NAME"=>GetMessage("MD_STATUS"),
			"PARENT"=>"BASE",
			"TYPE"=>"CHECKBOX",
			"DEFAULT"=>"Y"
		),
		"ADD_VAR" => Array(
			"NAME" => GetMessage("ADD_VAR"),
			"TYPE" => "STRING",
			"DEFAULT" =>'={$arResult}',
			"PARENT" => "BASE",
		),
		
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("MD_CAPTCHA"), 
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y", 
			"PARENT" => "BASE",
		),
		"SUCCESS_TEXT" => Array(
			"NAME" => GetMessage("MD_OK_MESSAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MD_OK_TEXT"), 
			"PARENT" => "BASE",
		),
		"AJAX_MODE" => array(),
	
		

	)
);


?>