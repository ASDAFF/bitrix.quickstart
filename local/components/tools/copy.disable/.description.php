<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COPY_NAME"),
	"DESCRIPTION" => GetMessage("COPY_DESC"),
	"SORT" => 11,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "ASDAFF",
		"SORT" => 2000,
        "CHILD" => array(
            "ID" => "utilites",
            "NAME" => 'Разное',
            "SORT" => 500,
        )
	),
);