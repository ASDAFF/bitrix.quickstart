<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_CB_DESC_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_CB_DESC_DESC"),
	"ICON" => "/images/slider.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "JustDevelop",
		"CHILD" => array(
			"ID" => "BANER",
			"NAME" => GetMessage("IBLOCK_CB_DESC_SLIDE"),
		),
	),
);

?>