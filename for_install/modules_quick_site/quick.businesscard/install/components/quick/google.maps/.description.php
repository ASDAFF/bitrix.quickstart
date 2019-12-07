<?
/**
 * Copyright (c) 6/12/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_IBLOCK_DESC_LIST"),
	"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_LIST_DESC"),
	"ICON" => "/images/component.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "google_maps_quick",
			"NAME" => GetMessage("T_IBLOCK_DESC_SLIDER"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "google_maps_quick_cmpx",
			),
		),
	),
);

?>