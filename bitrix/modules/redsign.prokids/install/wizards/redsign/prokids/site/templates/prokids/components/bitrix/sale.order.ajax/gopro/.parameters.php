<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"ALLOW_NEW_PROFILE" => Array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=> "Y",
		"PARENT" => "BASE",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => Array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => Array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"PARENT" => "BASE",
	),
	"CODE_PHONES" => Array(
		"NAME" => GetMessage("CODE_PHONES"),
		"TYPE" => "STERING",
		"DEFAULT" => "",
		"MULTIPLE" => "Y",
	),
);