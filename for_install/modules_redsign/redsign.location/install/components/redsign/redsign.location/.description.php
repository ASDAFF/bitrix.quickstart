<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("RS_LOCATION_COM_NAME"),
	"DESCRIPTION" => GetMessage("RS_LOCATION_COM_DESCRIPTION"),
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "alfa_com",
		"SORT" => 2000,
		"NAME" => GetMessage("ALFA_COM_COMPONENTS"),
		"CHILD" => array(
			"ID" => "redsign_location",
			"NAME" => GetMessage("ALFA_COM_SEC_NAME"),
			"SORT" => 10,
		)
	),
);
