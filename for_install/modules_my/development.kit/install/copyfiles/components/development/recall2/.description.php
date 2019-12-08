<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME__RECALL2"),
	"DESCRIPTION" => GetMessage("DESCR__RECALL2"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("COMPONENTS__RECALL2"),
		"CHILD" => array(
			"ID" => "kit",
			"NAME" => GetMessage("KIT__RECALL2"),
			"SORT" => 8000,
		),
	),
);
?>