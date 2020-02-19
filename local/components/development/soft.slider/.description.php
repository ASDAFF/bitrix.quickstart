<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ENERGOSOFT_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("ENERGOSOFT_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "CHILD" => array(
            "ID" => "media",
            "NAME" => "Мультимедия",
            "SORT" => 30
        ),
	),
);
?>