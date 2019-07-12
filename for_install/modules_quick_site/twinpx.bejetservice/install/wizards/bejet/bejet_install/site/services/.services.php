<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix public files
			"b_files.php", // Copy bitrix private files
			"template.php", // Install template
			"theme.php" // Install theme
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"orders.php", //IBlock types
			"services.php",
			"temaimg.php"			
		),
	),
);
?>

