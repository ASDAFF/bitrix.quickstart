<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	
	'main' => Array(
		'NAME' => GetMessage("SERVICE_MAIN_SETTINGS"),
		'STAGES' => Array(
			"files.php",
			"template.php",
			"settings.php",
			"post_event.php",
            "import.php",
		),
	),
	"iblock"	=> array(
		"NAME"		=> GetMessage("SERVICE_IBLOCK"),
		"STAGES"	=> array(
			"types.php",
			"TEMPLATE_GALERY.php",
			"material.php",
            "sizes.php",
            "vendor.php",
			"products.php",
			"offers.php",
			"orders.php",
		),
	),
);
?>
