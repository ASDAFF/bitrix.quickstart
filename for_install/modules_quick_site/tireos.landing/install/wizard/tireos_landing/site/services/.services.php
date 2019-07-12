<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			//"search.php", // Indexing files
			//"template.php", // Install template
			//"theme.php", // Install theme
			//"menu.php", // Install menu
			//"settings.php",
		),
	)
);
?>