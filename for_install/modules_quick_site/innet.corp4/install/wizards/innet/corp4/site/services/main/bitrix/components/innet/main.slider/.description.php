<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("INNET_MAIN_SLIDER_NAME"),
	"DESCRIPTION" => GetMessage("INNET_MAIN_SLIDER_DESCRIPTION"),
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "innet",
		"SORT" => 1000,
		"NAME" => GetMessage("INNET_MAIN_SLIDER_COMPONENTS"),
	),
);
?>