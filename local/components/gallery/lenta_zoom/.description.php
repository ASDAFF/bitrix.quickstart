<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("APROF_LENTAZOOM_LENTA_FOTOGRAFIY"),
	"DESCRIPTION" => GetMessage("APROF_LENTAZOOM_LENTA_FOTOGRAFIY"),
	"ICON" => "/images/icon.gif",
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