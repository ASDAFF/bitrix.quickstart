<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */

$arComponentDescription = array(
    "NAME" => GetMessage("COMPONENT_NAME"),
    "DESCRIPTION" => GetMessage("COMPONENT_DESCRIPT"),
    "ICON" => "/images/icon.png",
    "PATH" => array(
        "ID" => "zixnru",
        "CHILD" => array(
            "ID" => GetMessage("COMPONENT_NAME"),
            "NAME" => GetMessage("COMPONENT_NAME")
        )
    ),
    "CACHE_PATH" => "Y",
    "COMPLEX" => ""
);
