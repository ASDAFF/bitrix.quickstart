<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("INSECO_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("INSECO_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "INSECO",
		"NAME" => GetMessage("INSECO"),
		"CHILD" => array(
			"ID" => "INSECO_MULTIMEDIA",
			"NAME" => GetMessage("INSECO_GROUP"),
			"SORT" => 30,
		),
	),
);
?>