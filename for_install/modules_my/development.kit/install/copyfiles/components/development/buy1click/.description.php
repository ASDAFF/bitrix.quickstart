<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME__BIY1CLICK"),
	"DESCRIPTION" => GetMessage("DESCR__BIY1CLICK"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("COMPONENTS__BIY1CLICK"),
		"CHILD" => array(
			"ID" => "kit",
			"NAME" => GetMessage("KIT__BIY1CLICK"),
			"SORT" => 8000,
		),
	),
);
?>