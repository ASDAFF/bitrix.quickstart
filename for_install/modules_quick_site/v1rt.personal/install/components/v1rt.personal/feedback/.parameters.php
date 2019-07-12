<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "CAPTCHA" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CAPTCHA"),
			"TYPE" => "CHECKBOX"
		),
	),
);
?>