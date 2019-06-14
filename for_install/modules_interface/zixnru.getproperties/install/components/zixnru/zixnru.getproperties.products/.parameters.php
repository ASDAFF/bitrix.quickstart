<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */

$arComponentParameters = array(
    "GROUPS" => array(
        "IBLOCK_ID" => array(
            "NAME" => GetMessage("PARAMS_COMPONENT"),
            "SORT" => '100',
        ),
        "EXCLUDE_PROPS" => array(
            "NAME" => GetMessage("EXCLUDE_PROPS"),
            "SORT" => '200',
        ),
        "ID" => array(
            "NAME" => GetMessage("ELEMENT_ID"),
            "SORT" => "300",
        ),
    ),
    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("INFOBLOCK_ID"),
            "TYPE" => "STRING",
            "REFRESH" => "N",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "",
            "COLS" => '1',
        ),
        "EXCLUDE_PROPS" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("EXCLUDE_PROPS"),
            "TYPE" => "STRING",
            "REFRESH" => "N",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "",
            "COLS" => '9',
        ),
        "ID" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("ELEMENT_ID"),
            "TYPE" => "STRING",
            "REFRESH" => "N",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "Y",
            "DEFAULT" => "",
            "COLS" => '9',
        ),
    )
);
