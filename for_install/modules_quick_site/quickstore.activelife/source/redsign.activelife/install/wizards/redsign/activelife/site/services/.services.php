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
			"news.php",
			"action.php",
			"brands.php",
			"shops.php",
			"banners.php",
			"additional_banners.php",
			"mainsmallbanners.php",
			"main4banners.php",
			"references.php",//reference of colors
			"references2.php",
			"catalog.php",//catalog iblock import
			"catalog2.php",//offers iblock import
			"catalog3.php",
			"catalog4.php",
			"binds_items.php",
		),
	),
	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"locations.php",
			"step1.php",
			"step2.php",
			"step3.php"
		),
	),
	"catalog" => Array(
		"NAME" => GetMessage("SERVICE_CATALOG_SETTINGS"),
		"STAGES" => Array(
			"index.php",
			"eshopapp.php",
		),
	),
	"forum" => Array(
		"NAME" => GetMessage("SERVICE_FORUM")
	),
	"advertising" => Array(
		"NAME" => GetMessage("SERVICE_ADVERTISING"),
	),
	"redsign" => Array(
		"NAME" => GetMessage("SERVICE_REDSIGN"),
        "STAGES" => Array(
			"daysarticle.php",
			"devcom.php",
			"devfunc.php",
			"favorite.php",
			"grupper.php",
			"quickbuy.php",
			"forms.php",
			"settings.php",
		),
        "MODULE_ID" => "redsign.activelife"
	),
);
?>