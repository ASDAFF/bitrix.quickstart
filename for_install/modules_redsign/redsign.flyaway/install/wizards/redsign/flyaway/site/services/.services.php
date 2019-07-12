<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = array(
	"redsign_iblock" => array(
		"NAME" => GetMessage("SERVICE_REDSIGN"),
        "STAGES" => Array(
			"data_download.php",
			"data_unpack.php",
		),
        "MODULE_ID" => "redsign.flyaway"
	),
	"main" => array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => array(
			"files.php", // Copy bitrix files
			"search.php", // Indexing files
			"template.php", // Install template
			"theme.php", // Install theme
			"menu.php", // Install menu
			"settings.php",
		),
	),
	"iblock" => array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => array(
			"types.php", // IBlock types
			"company_part1.php",//
			"company_part2.php",//
			"document.php",//
			"projects.php",//
			"services_part1.php",//
			"services_part2.php",//
			"services_part3.php",//
			"references.php", // hl
			"references2.php",
			"catalog.php", // catalog iblock import
			"catalog2.php", // offers iblock import
			"catalog3.php", // catalog binds
			"catalog4.php", // reindex
			"binds_items.php",
		),
	),
	"sale" => array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => array(
			"locations.php",
			"step1.php",
			"step2.php",
			"step3.php"
		),
	),
	"catalog" => array(
		"NAME" => GetMessage("SERVICE_CATALOG_SETTINGS"),
		"STAGES" => array(
			"index.php",
			"eshopapp.php",
		),
	),
	"forum" => array(
		"NAME" => GetMessage("SERVICE_FORUM")
	),
	"redsign" => array(
		"NAME" => GetMessage("SERVICE_REDSIGN"),
        "STAGES" => array(
			"daysarticle.php",
			"devcom.php",
			"devfunc.php",
			"favorite.php",
			"grupper.php",
			"location.php",
			"recaptcha.php",
			"quickbuy.php",
			"form.php",
			"widget.php",
		),
        "MODULE_ID" => "redsign.flyaway"
	),
);
