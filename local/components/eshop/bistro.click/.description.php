 <?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CAT_BYCLICK_NAME"),
	"DESCRIPTION" => GetMessage("CAT_BYCLICK_DESCRIPTION"),
	"ICON" => "/images/component.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"NAME" => GetMessage("ASDAFF"),
		"ID" => "ASDAFF",
		"CHILD" => array(
			"ID" => 'order',
			"NAME" => GetMessage("ORDER"),
			"SORT" => 9,
		),
	),
);
?>
