<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_NAME"),
	"DESCRIPTION" => GetMessage("MLIFE_ASZ_CATALOG_FILTER_DESC"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "mlife",
		"NAME" => GetMessage("MLIFE_NAME"),
		"CHILD" => array(
			"ID" => "mlifeasz",
			"NAME" => GetMessage("MLIFE_ASZ"),
			"SORT" => 30,
		),
	),
);

?>