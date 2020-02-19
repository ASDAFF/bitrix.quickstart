<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "development",
		"NAME" => "DEVELOPMENT",
		"CHILD" => array(
			"ID" => "SOCIAL_SERVICES",
			"NAME" => GetMessage("GROUP"),
			"SORT" => 30,
		),
	),
);
?>