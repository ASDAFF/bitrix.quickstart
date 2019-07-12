<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy  files
			"template.php", // Install template
			"menu.php", // Install menu
			"settings.php", // main settings
			"search.php", // search reindex
		),
	),
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"news.php",//news and other IB
			//"catalog.php",

		),
	),
	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"step1.php",
			"step2.php",
		),
	),
	"catalog" => Array(
		"NAME" => GetMessage("SERVICE_CATALOG_SETTINGS"),
		"STAGES" => Array(
			"index.php"
		),
	),
	"forum" => Array(
		"NAME" => GetMessage("SERVICE_FORUM"),
		"STAGES" => Array(
			"index.php",

		),
	),

);
?>