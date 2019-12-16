<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ALFA_COM_NAME__RECALL2"),
	"DESCRIPTION" => GetMessage("ALFA_COM_DESCR__RECALL2"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("ALFA_COM_COMPONENTS__RECALL2"),
		"CHILD" => array(
			"ID" => "devcom",
			"NAME" => GetMessage("ALFA_COM_DEV_COM__RECALL2"),
			"SORT" => 8000,
		),
	),
);
?>