<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("WAPXAZ_AJAXFORM_COMPONENT_NAME"),
    "DESCRIPTION" => GetMessage("WAPXAZ_AJAXFORM_COMPONENT_DESCR"),
    "ICON" => "/images/icon.gif",
    "SORT" => 20,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "ASDAFF",
        "CHILD" => array(
            "ID" => "feedback",
            "SORT" => 10,
            "CHILD" => array(
                "ID" => "form",
            ),
        ),
    ),
);
?>