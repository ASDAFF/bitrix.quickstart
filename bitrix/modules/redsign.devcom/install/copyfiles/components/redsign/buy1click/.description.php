<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ALFA_COM_NAME__BIY1CLICK"),
	"DESCRIPTION" => GetMessage("ALFA_COM_DESCR__BIY1CLICK"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("ALFA_COM_COMPONENTS__BIY1CLICK"),
		"CHILD" => array(
			"ID" => "devcom",
			"NAME" => GetMessage("ALFA_COM_DEV_COM__BIY1CLICK"),
			"SORT" => 8000,
		),
	),
);
?>