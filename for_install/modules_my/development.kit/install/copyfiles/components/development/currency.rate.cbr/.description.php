<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME"),
	"DESCRIPTION" => GetMessage("DESCRIPTION"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("COMPONENTS"),
		"CHILD" => array(
			"ID" => "kit",
			"NAME" => GetMessage("KIT"),
			"SORT" => 8000,
		),
	),
);
?>