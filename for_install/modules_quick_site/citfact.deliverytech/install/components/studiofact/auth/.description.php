<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("PVKD_AUTH_NAME"),
	"DESCRIPTION" => GetMessage("PVKD_AUTH_DESCRIPTION"),
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "auth",
			"NAME" => GetMessage("PVKD_AUTH_NAME"),
		),
	),
); ?>