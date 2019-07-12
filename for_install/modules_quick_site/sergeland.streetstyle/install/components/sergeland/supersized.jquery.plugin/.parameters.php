<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"BANNER_IMAGES_BIG" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BANNER_IMAGES_BIG"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"MULTIPLE" => "Y",
		),
		"BANNER_IMAGES_SMALL" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BANNER_IMAGES_SMALL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"MULTIPLE" => "Y",
		),		
		"BANNER_HEAD" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BANNER_HEAD"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"MULTIPLE" => "Y",
		),
		"BANNER_TEXT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BANNER_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"ROWS" => 5,
		),		
		"AUTOPLAY" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("AUTOPLAY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"START_SLIDE" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("START_SLIDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),		
		"SLIDE_INTERVAL" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SLIDE_INTERVAL"),
			"TYPE" => "STRING",
			"DEFAULT" => 9000,
		),
		"TRANSITION" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("TRANSITION"),
			"TYPE" => "STRING",
			"DEFAULT" => 1,
		),
		"TRANSITION_SPEED" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("TRANSITION_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => 600,
		),
		"SLIDE_LINKS" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SLIDE_LINKS"),
			"TYPE" => "STRING",
			"DEFAULT" => "empty",
		),
		"THEMEVARS_IMAGE_PATH" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("THEMEVARS_IMAGE_PATH"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),		
	),
);
?>