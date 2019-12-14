<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			"search.php", // Indexing files
			"template.php", // Install template
			"theme.php", // Install theme
			"menu.php", // Install menu
			"settings.php",
		),
	),
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"catalog.php",
			"news.php",
			"history.php",
			"honors.php",
			"leadership.php",
			"lic.php",
			"production.php",
			"salesdepartment.php",
			"thankyouletters.php",
			"theserviceteam.php",
			"vacancies.php",
			"stocks.php",
			"forusers.php",
			"technicaldocumentation.php",
			"uchreditelnye_dokumenty.php",
			"current.php",
			"finished.php",
			"perspective.php",
			"3d_design.php",
			"action.php",
			"articles.php",
			"banners.php",
			"customerreviews.php",
			"faq.php",
			"features.php",
			"partners.php",
			"press_about_us.php",
			"projectphotogallery.php",
			"services.php",
			"shops.php",
			"sidebanners.php",
			"small_banners.php",
			"superbanner.php",
		),
	),
	"forms" => Array(
		"NAME" => GetMessage("SERVICE_FORMS")
	),
	"subscribe" => Array(
		"NAME" => GetMessage("SERVICE_SUBSCRIBE")
	),
	"redsign" => Array(
		"NAME" => GetMessage("SERVICE_REDSIGN"),
        "STAGES" => Array(
			"devcom.php",
			"devfunc.php",
			"widget.php",
			"settings.php",
		),
        "MODULE_ID" => "redsign.monopoly"
	),
);
?>