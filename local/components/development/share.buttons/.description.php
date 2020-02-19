<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME"),
	"DESCRIPTION" => GetMessage("DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "CHILD" => array(
            "ID" => 'utility',
            "NAME" => 'Кнопки расшаривания',
            "SORT" => 10,
        ),
	),
);

?>