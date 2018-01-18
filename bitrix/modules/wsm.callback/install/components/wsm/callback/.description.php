<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_WSM_CALLBACK_NAME"),
	"DESCRIPTION" => GetMessage("T_WSM_CALLBACK_DESC"),
	"ICON" => "/images/callback.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "service",
		"SORT" => 1000,
	),
);

?>