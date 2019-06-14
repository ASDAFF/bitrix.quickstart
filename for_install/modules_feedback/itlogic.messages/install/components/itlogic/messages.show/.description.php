<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MESSAGE_SHOW_NAME"),
	"DESCRIPTION" => GetMessage("MESSAGE_SHOW_DESC"),
	"ICON" => "/images/mail_detail.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "itlogic",
		"SORT" => 2000,
		"NAME" => GetMessage("ITLOGIC_COMPONENTS"),
	),
);

?>