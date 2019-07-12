<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_FILTER_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_FILTER_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/iblock_filter.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 70,
	"PATH" => array(
		"ID" => "store",
		"NAME" => GetMessage("STORE"),
		"SORT" => 1000,
		"CHILD" => array(
			"ID" => "store_catalog",
			"NAME" => GetMessage("T_IBLOCK_DESC_CATALOG"),
			"SORT" => 30,
			"CHILD" => array(
				"ID" => "store_catalog_cmpx",
			),
		),
	),
);
?>