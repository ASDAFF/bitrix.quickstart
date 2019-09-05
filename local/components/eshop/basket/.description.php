<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("BEONO_BASKET_NAME"),
	"DESCRIPTION" => GetMessage("BEONO_DESCRIPTION"),
	"ICON" => "/images/sale_basket.gif",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "sale_basket",
			"NAME" => GetMessage("BEONO_BASKET_CATNAME")
		)
	),
);
?>