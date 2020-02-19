<?
/**
 * Copyright (c) 27/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('HLLIST_COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('HLLIST_COMPONENT_DESCRIPTION'),
	"ICON" => "images/hl_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 10,
    "PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
        "CHILD" => array(
            "ID" => "content",
            "NAME" => 'Контент',
            "SORT" => 30
        ),
    ),
);

?>