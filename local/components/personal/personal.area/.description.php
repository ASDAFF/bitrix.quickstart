<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Личный кабинет",
	"DESCRIPTION" => "Комплексный компонент личного кабинета",
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		"CHILD" => array(
			"ID" => "personal", // for example "my_project:services"
			"NAME" => "Личный кабинет",  // for example "Services"
		),
	),
	"COMPLEX" => "Y",
);

?>