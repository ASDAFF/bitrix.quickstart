<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MAIN_GETCODE_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MAIN_GETCODE_COMPONENT_DESCR"),
	"ICON" => "/images/main.getcode.gif",
	"SORT" => 1,
    "PATH" => array(
        "ID" => "ASDAFF",
        "NAME" => "ASDAFF",
        "CHILD" => array(
            "ID" => 'utility',
            "NAME" => 'Разное',
            "SORT" => 30
        ),
    ),
);
?>