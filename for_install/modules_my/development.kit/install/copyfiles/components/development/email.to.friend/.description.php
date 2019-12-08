<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME__SEND_EMAIL"),
	"DESCRIPTION" => GetMessage("DESCR__SEND_EMAIL"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("COMPONENTS__SEND_EMAIL"),
		"CHILD" => array(
			"ID" => "kit",
			"NAME" => GetMessage("KIT__SEND_EMAIL"),
			"SORT" => 8000,
		),
	),
);
?>