<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("MLIFE_ASZ_SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php",
			"template.php",
			"theme.php",
			"menu.php",
			"settings.php",
		),
	),
	"iblock" => Array(
		"NAME" => GetMessage("MLIFE_ASZ_SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php",
			"mlife_asz_catalog.php",
		),
	),
	"mlife.asz" => Array(
		"NAME" => GetMessage("MLIFE_ASZ_SERVICE_ASZ_SETTINGS"),
		"STAGES" => Array(
			"curency.php",
			"price.php",
			"status.php",
			"pay.php",
			"delivery.php",
			"state.php",
			"userprops.php",
		),
	),
);
?>