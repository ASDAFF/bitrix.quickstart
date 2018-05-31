<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
        "NAME" => GetMessage("REASPEKT_GEOIP_NAME"),
        "DESCRIPTION" => GetMessage("REASPEKT_GEOIP_DESC"),
        "ICON" => "/images/icon.gif",
        "CACHE_PATH" => "Y",
        "PATH" => array(
                "ID" => "REASPEKT.RU",
                "NAME" => GetMessage("REASPEKT_DESC_SECTION_NAME"),
                "CHILD" => array(
                        "ID" => "REASPEKT_serv",
                        "NAME" => GetMessage("REASPEKT_GEOIP_SERVICE")
                )
        ),
);

?>
