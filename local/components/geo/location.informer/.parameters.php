<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "SHOW_COUNTRY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EVALGA_SHOW_COUNTRY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "SHOW_CITY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EVALGA_SHOW_CITY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
    ),
);