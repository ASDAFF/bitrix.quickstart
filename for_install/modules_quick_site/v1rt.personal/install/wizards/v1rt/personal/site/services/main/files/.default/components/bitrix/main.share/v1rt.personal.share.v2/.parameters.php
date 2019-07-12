<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/main.share/util.php");
$arHandlers = __bx_share_get_handlers($templateName);

$arTemplateParameters = array(
		"HIDE" => array(
			"NAME" => GetMessage("BOOKMARK_HIDE"),
			"TYPE" => "CHECKBOX",
			"VALUE" => "Y",
			"DEFAULT" => "N",
		),
		"HANDLERS" => array(
			"NAME" => GetMessage("BOOKMARK_SYSTEM"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arHandlers["HANDLERS"],
			"DEFAULT" => $arHandlers["HANDLERS_DEFAULT"],			
			"REFRESH"=> "Y",
		),
		"PAGE_URL" => array(
			"NAME" => GetMessage("BOOKMARK_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "",	
		),
		"PAGE_TITLE" => array(
			"NAME" => GetMessage("BOOKMARK_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",	
		),
);

if(is_array($arCurrentValues["HANDLERS"]) && in_array("twitter", $arCurrentValues["HANDLERS"]) > 0)
{
	$arTemplateParameters["SHORTEN_URL_LOGIN"] = array(
		"NAME" => GetMessage("BOOKMARK_SHORTEN_URL_LOGIN"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
	
	$arTemplateParameters["SHORTEN_URL_KEY"] = array(
		"NAME" => GetMessage("BOOKMARK_SHORTEN_URL_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}

?>
