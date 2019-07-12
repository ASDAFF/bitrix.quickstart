<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_IBLOCK_DESC_CR_LIST"),
	"DESCRIPTION" => GetMessage("T_IBLOCK_DESC_CR_DESC"),
	"ICON" => "/images/photo_view.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "store",
		"CHILD" => array(
			"ID" => "store_catalog",
			"NAME" => GetMessage("T_IBLOCK_DESC_CATALOG"),
			"SORT" => 20,
		)
	),
);

?>