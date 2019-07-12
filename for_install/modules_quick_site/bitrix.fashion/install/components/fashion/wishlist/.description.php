<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WISHLIST"),
	"DESCRIPTION" => GetMessage("WISHLIST"),
	"ICON" => "/images/iblock_compare_list.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 50,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "wishlist",
			"NAME" => GetMessage("WISHLIST"),
			"SORT" => 30,
			"CHILD" => array(
				"ID" => "catalog_cmpx",
			),
		),
	),
);

?>