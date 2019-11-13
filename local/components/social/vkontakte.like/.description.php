<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME"),
	"DESCRIPTION" => GetMessage("DESC"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "socialservice",
		"NAME" => GetMessage("SOCIAL_SERVICE_NAME"),
		"CHILD" => array(
			"ID" => "pxvkontakte",
			"NAME" => GetMessage("CHILD_SOCIAL_SERVICE_NAME")
		)
	),
);
?>