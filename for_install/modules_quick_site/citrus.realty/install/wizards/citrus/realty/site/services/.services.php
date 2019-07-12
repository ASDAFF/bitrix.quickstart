<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			"template.php", // Install template
			"theme.php", // Install theme
			"messages.php", // типы почтовых событий и почтовые шаблоны
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"01_offices.php",
			"02_news.php",
			"03_licenses.php",
			"04_staff.php",
			"05_vacancies.php",
			"06_partners.php",
			"07_testimonials.php",
			"08_questions.php",
			"09_articles.php",
			"10_offers.php",
			"11_services.php",
			"12_requests.php",
			"offers_usertype.php",
			"offices.php",
		),
	),
	"finish" => Array(
		"NAME" => GetMessage("SERVICE_FINISH"),
		"MODULE_ID" => "main",
		"STAGES" => Array(
			"step.php",
		),
	),


);
?>