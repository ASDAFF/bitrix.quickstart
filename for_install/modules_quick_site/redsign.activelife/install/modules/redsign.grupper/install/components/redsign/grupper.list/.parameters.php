<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"PARAMETERS" => array(
		"DISPLAY_PROPERTIES" => array(
			"NAME" => GetMessage("ALFA_MSG_DISPLAY_PROPERTIES"),
			"TYPE" => "STRING",
			"PARENT" => "BASE",
		),
		"CACHE_TIME"  =>  Array(
			"PARENT" => "CACHE_SETTINGS",
			"DEFAULT" => 3600000,
		),
	)
);