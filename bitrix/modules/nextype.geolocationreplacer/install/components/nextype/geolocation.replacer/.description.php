<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = array(
    "NAME" => GetMessage("NT_GEOLOCATION_REPLACER_NAME"),
    "DESCRIPTION" => GetMessage("NT_GEOLOCATION_REPLACER_DESCRIPTION"),
    "ICON" => "/images/icon.gif",
    "PATH" => array(
        "ID" => "nextype",
        "NAME" => GetMessage("NT_GEOLOCATION_REPLACER_TAB_NAME")
    ),
);
?>