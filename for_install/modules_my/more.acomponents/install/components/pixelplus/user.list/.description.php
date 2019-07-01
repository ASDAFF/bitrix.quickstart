<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("PP_USER_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("PP_USER_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_list.gif",
	"PATH" => array(
		"ID" => "ASDAFF",
		"NAME" => GetMessage("ASDAFF_PARENT"),
		"CHILD" => array(
			"ID" => "PPAcomponents",
			"NAME" => GetMessage("PP_USER_DESC_CATALOG"),
		)
	)
);
?>