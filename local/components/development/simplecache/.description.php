<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$arComponentDescription = array(
    "NAME" => GetMessage("TEGA_SIMPLE_CACHE_NAME"),
    "DESCRIPTION" => GetMessage("TEGA_SIMPLE_CACHE_DESCRIPTION"),
    "SORT" => 10,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "ASDAFF",
        "SORT" => 2000,
        "CHILD" => array(
            "ID" => "utilites",
            "NAME" => 'Утилиты',
            "SORT" => 500,
        )
    ),
    "ICON" => "/images/icon.gif",
);
