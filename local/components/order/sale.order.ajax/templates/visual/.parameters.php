<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"ALLOW_NEW_PROFILE" => Array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"Y",
		"PARENT" => "BASE",
	),
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "90",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "90",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => Array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => Array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
);
?>
