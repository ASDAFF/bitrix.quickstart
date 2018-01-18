 <?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MLIFE_CAT_BISTROCKLICK_NAME"),
	"DESCRIPTION" => GetMessage("MLIFE_CAT_BISTROCKLICK_DESCRIPTION"),
	"ICON" => "/images/component.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"NAME" => GetMessage("MLIFE"),
		"ID" => "mlife",
		"CHILD" => array(
			"ID" => 'mlife_order',
			"NAME" => GetMessage("MLIFE_ORDER"),
			"SORT" => 9,
		),
	),
);
?>