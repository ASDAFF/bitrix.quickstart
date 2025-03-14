<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arTemplateParameters = array(

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