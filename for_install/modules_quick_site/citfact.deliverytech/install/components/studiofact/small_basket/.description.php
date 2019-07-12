<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("PVKD_SMALL_BASKET_NAME"),
	"DESCRIPTION" => GetMessage("PVKD_SMALL_BASKET_DESCRIPTION"),
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "small_basket",
			"NAME" => GetMessage("PVKD_SMALL_BASKET_NAME"),
		),
	),
); ?>