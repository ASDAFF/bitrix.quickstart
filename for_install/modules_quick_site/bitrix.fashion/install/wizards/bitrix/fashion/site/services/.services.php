<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			"template.php", // Install template
			"theme.php", // Install theme
			"settings.php",
			"wishlist.php",
			"callback.php"
		),
	),

	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(
			"types.php", //IBlock types
			"colors.php",
			"sizes.php",
			"catalog.php",
			"promo.php",
			"reviews.php",
			"brands.php",
			"sets.php",
			"banners.php"
		),
	),

	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"step1.php",
			"step2.php",
		),
	),
);

COption::SetOptionString("fashion", "wizard_installed", "Y", false, WIZARD_SITE_ID);
?>
