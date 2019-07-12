<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SHOES_MAIN_PAGE_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("SHOES_MAIN_PAGE_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/cat_all.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 90,
	"PATH" => array(
		"ID" => "shoes",
		"CHILD" => array(
			"ID" => "shoes_banner",
			"NAME" => GetMessage("T_SHOES_BANNER"),
			"SORT" => 30,
		)
	),
);

?>