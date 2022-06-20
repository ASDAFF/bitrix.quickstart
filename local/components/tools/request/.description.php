<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MD_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MD_COMPONENT_DESCR"),
	"ICON" => "/images/feedback.gif",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		"CHILD" => array(
			"ID" => "request",
			"NAME" => GetMessage("MICROS_TECH")
		)

	),
);
?>