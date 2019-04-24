<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

$arComponentParameters = array(
    "PARAMETERS" => array(
        "HIGH_LOAD_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_CB_ELEMENT_CODE"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ),
        "CACHE_TIME"  =>  array(
            "DEFAULT" => 36000000
        ),
        "CACHE_GROUPS" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("IBLOCK_CB_CACHE_GROUPS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "SINGLE_COMPONENT" => array(
            "PARENT" => "BASKET",
            "NAME" => GetMessage("IBLOCK_CB_SINGLE_COMPONENT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "ELEMENT_COUNT" => array(
            "PARENT" => "BASKET",
            "NAME" => GetMessage("IBLOCK_CB_ELEMENT_COUNT"),
            "TYPE" => "STRING",
            "DEFAULT" => "3"
        )
    )
);