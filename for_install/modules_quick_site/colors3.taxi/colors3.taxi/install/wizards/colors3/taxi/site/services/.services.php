<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			"template.php", // Install template
			"theme.php", // Install theme
			"menu.php", // Install menu
		),
	),
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"jobs.php",//news
			"news.php",
			"reviews.php",
			"orders.php",
			"services.php",
			"taxis.php",
			"call.php",
			"tarify.php",
			"connect.php",
			"forgotten.php",
			"driver.php",
			"dop.php",		
		),
	),
);
?>