<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COPY_NAME"),
	"DESCRIPTION" => GetMessage("COPY_DESC"),
	"SORT" => 11,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "itlogic",
		"SORT" => 2000,
		"NAME" => GetMessage("ITLOGIC_COMPONENTS"),
	),
);