<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
    "NAME" => GetMessage("SS_SSG_NAME"),
    "DESCRIPTION" => GetMessage("SS_SSG_DESCRIPTION"),
    "PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "CHILD" => array(
            "ID" => 'content',
            "NAME" => 'Контент',
            "SORT" => 500,
        )
    ),
    "ICON" => "/images/icon.gif",
);
?>