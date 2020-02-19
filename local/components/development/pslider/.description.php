<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("KLONDIKE_PSLIDER_PRODAUSIY_SLAYDER"),
	"DESCRIPTION" => "",
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "CHILD" => array(
            "ID" => 'media',
            "NAME" => 'Слайдер товаров',
            "SORT" => 10,
        ),
	),
	"COMPLEX" => "N",
);

?>