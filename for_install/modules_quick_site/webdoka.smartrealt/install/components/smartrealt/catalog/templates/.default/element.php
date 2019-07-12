<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?$APPLICATION->IncludeComponent(
    "smartrealt:catalog.element",
    "",
    Array(
        "TYPE_CODE" => $arResult["VARIABLES"]["TYPE_CODE"],
        "RUBRIC_CODE" => $arResult["VARIABLES"]["TRANSACTION_TYPE"],
        "NUMBER" => $arResult["VARIABLES"]["NUMBER"],
        "DETAIL_IMAGE_MEDIUM_WIDTH" => $arParams["DETAIL_IMAGE_MEDIUM_WIDTH"],
        "DETAIL_IMAGE_MEDIUM_HEIGHT" => $arParams["DETAIL_IMAGE_MEDIUM_HEIGHT"],
        "DETAIL_IMAGE_BIG_WIDTH" => $arParams["DETAIL_IMAGE_BIG_WIDTH"],
        "DETAIL_IMAGE_BIG_HEIGHT" => $arParams["DETAIL_IMAGE_BIG_HEIGHT"],
        "DISPLAY_PANEL" => "N",
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SET_TITLE" => $arParams["SET_TITLE"],
    ),
    $component
);

$APPLICATION->IncludeComponent("smartrealt:catalog.feedback", ".default", array(
    "USE_CAPTCHA" => "Y",
    "OK_TEXT" => "Спасибо, ваше сообщение принято.",
    "MOBILEPHONE_TO" => "",
    "MESSAGE_DEFAULT" => "",
    "OBJECT_NUMBER_DEFAULT" => $arResult["VARIABLES"]["NUMBER"],
    "REQUIRED_FIELDS" => array(
        0 => "NAME",
        1 => "EMAIL",
        2 => "PHONE",
        3 => "OBJECT_NUMBER",
        4 => "MESSAGE",
    )
    ),
    $component
);
?>
