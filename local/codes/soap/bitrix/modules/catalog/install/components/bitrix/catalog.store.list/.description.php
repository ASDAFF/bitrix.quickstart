<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("PAGE_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("STORE_MAIN_PAGE_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/store_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 90,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "catalog",
			"NAME" => GetMessage("STORE_CATALOG_LIST"),
			"SORT" => 30,
		)
	),
);

?>