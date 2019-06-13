<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ALFA_COM_NAME"),
	"DESCRIPTION" => GetMessage("ALFA_COM_DESCRIPTION"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("ALFA_COM_COMPONENTS"),
		"CHILD" => array(
			"ID" => "devcom",
			"NAME" => GetMessage("ALFA_COM_DEV_COM"),
			"SORT" => 8000,
		),
	),
);
?>