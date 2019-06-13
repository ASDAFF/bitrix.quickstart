<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
        "NAME" => GetMessage("ALTASIB_QR_NAME"),
        "DESCRIPTION" => GetMessage("ALTASIB_QR_DESC"),
        "ICON" => "/images/cmp.gif",
        "SORT" => 20,
        "CACHE_PATH" => "Y",
        "PATH" => array(
                "ID" => "IS-MARKET.RU",
                "SORT" => 2000,
                "CHILD" => array(
                        "ID" => "altasib_service",
                        "NAME" => GetMessage("ALTASIB_LISTERG_DISC_NAME_CHILD"),
                        "SORT" => 30,
                        "CHILD" => array(
                                "ID" => "altasib_qrcode",
                        ),
                ),
        ),
);
?>