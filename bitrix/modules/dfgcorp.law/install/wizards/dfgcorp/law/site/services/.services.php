<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", 
			"events.php", 
			"components.php", 
			"template.php", 
			"theme.php", 
			"group.php", 
			"menu.php", 
			"settings.php", 
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK"),
		"STAGES" => Array(
			"types.php", 
			"news.php",
			"articles.php",
			"clients.php",
			"faq.php",
			"requests.php",
			"review.php",
			"slider.php",
			"vacancy.php",
		),
	),
);
?>