<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MAIN_FEEDBACK_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MAIN_FEEDBACK_COMPONENT_DESCR"),
	"ICON" => "/images/feedback.gif",
    "PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "SORT" => 200,
        "CHILD" => array(
            "ID" => "feedback",
            "NAME" => GetMessage("COMPONENT_SUBFOLDER_NAME"),
            "SORT" => 10,
        )
    ),
);
?>