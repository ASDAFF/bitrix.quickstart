<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("SBBS_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/basket.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_ORDER" => Array(
			"NAME" => GetMessage("SBBS_PATH_TO_ORDER"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/order.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"SHOW_DELAY" => array(
			"NAME" => GetMessage('SBBS_SHOW_DELAY'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"MULTIPLE" => "N",
		),
		"SHOW_NOTAVAIL" => array(
			"NAME" => GetMessage('SBBS_SHOW_NOTAVAIL'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"MULTIPLE" => "N",
		),
		"SHOW_SUBSCRIBE" => array(
			"NAME" => GetMessage('SBBS_SHOW_SUBSCRIBE'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"MULTIPLE" => "N",
		),
	)
);
?>