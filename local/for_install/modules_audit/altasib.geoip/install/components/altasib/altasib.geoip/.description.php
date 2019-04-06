<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
        "NAME" => GetMessage("ALTASIB_GEOIP_NAME"),
        "DESCRIPTION" => GetMessage("ALTASIB_GEOIP_DESC"),
        "ICON" => "/images/icon.gif",
        "CACHE_PATH" => "Y",
        "PATH" => array(
                "ID" => "IS-MARKET.RU",
                "NAME" => GetMessage("ALTASIB_DESC_SECTION_NAME"),
                "CHILD" => array(
                        "ID" => "altasib_serv",
                        "NAME" => GetMessage("ALTASIB_GEOIP_SERVICE")
                )
        ),
);

?>
