<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("RELOAD_CAPTCHA"),
	"DESCRIPTION" => GetMessage("RELOAD_CAPTCHA_DESC"),
	"ICON" => "/images/icon.gif",
	#"SORT" => 10,
	"COMPLEX" => "N",
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		)
);
?>