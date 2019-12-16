<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arTemplateParameters = array(
	"USER_PROPERTY_NAME"=>array(
		"NAME" => GetMessage("USER_PROPERTY_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
	),
	"AUTH_AUTH_URL"=>array(
		"NAME" => GetMessage("RS_SLINE.AUTH_AUTH_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => "/auth/",	
	),
);
?>