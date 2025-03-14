<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arTemplateParameters = array(

"VIDEO_LINE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("VIDEO_COUNT_LINE"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"VIDEO_COUNT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("VIDEO_COUNT_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "9",
		),
    "JQUERY_ON" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("JQUERY_ON"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
    "FANCYBOX_SCRIPT_ON" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("FANCYBOX_ON"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
	),

);

?>