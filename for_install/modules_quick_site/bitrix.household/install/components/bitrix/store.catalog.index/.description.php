<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SCI_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("SCI_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_index.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "store",
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