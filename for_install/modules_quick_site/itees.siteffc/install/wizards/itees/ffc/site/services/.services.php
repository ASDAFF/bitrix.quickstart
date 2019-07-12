<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"site_create.php", // Create site
			"files.php", // Copy bitrix files
			"template.php", // Install template
			"theme.php", // Install theme
		),
	),
	
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"news.php",
			"services.php",
			"refbooks.php",
			"containers.php",
		),
	),
	
	"form" => Array(
		"NAME" => GetMessage("SERVICE_FORM"),
		"STAGES" => Array(
			"form_create.php",
		),
	),
	
	"fileman" => Array(
		"NAME" => GetMessage("SERVICE_SETTINGS"),
		"STAGES" => Array(
			"settings.php",
		),
	),
);
?>