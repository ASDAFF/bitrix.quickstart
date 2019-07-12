<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"site_create.php", // Create site
			"files.php", // Copy bitrix files
			"forum.php",
			"template.php", // Install template
			"theme.php", // Install theme
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"news.php",
			"services.php",
			"fotogallery.php",
			"offers.php",
			"action.php",
			"producer.php",
			"washing.php",
			"stoves.php",
			"refrigerators.php",
			"home.php",
			"builtin.php",
			"appliance.php",
			"slides_main.php"
		),
	),
	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"step1.php", "step2.php",
		),
	),
	
);
?>