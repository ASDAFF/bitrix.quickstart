<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_BASKET" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"DEFAULT" => SITE_DIR."personal/cart/",
		),
	),
); ?>