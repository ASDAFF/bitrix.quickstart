<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_CATALOG_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_CATALOG_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "aqw",
		"NAME" => GetMessage("AQW_COMPONENTS"),		
		"CHILD" => array(
			"ID" => "media",
			"NAME" => GetMessage("T_IBLOCK_DESC_CATALOG"),
			"SORT" => 10,
		),
	),
);

?>