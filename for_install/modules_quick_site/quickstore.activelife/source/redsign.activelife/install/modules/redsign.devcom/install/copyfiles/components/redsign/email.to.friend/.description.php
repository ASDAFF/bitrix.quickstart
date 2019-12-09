<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ALFA_COM_NAME__SEND_EMAIL"),
	"DESCRIPTION" => GetMessage("ALFA_COM_DESCR__SEND_EMAIL"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("ALFA_COM_COMPONENTS__SEND_EMAIL"),
		"CHILD" => array(
			"ID" => "devcom",
			"NAME" => GetMessage("ALFA_COM_DEV_COM__SEND_EMAIL"),
			"SORT" => 8000,
		),
	),
);
?>