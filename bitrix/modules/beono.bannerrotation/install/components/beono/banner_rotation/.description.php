<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("BEONO_BANNER_NAME"),
	"DESCRIPTION" => GetMessage("BEONO_BANNER_DESC"),
	"ICON" => "/images/banner.png",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "advertising",
			"NAME" => GetMessage("BEONO_BANNER_ADV")
		)
	),
);
?>