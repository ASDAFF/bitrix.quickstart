<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LW_WIDGET_ELEMANTS_LIST"),
	"DESCRIPTION" => GetMessage("LW_WIDGET_ELEMANTS_LIST_DESC"),
	"ICON" => "/images/widget_list.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "widget_list",
		"NAME" => GetMessage("LW_WIDGETS"),
		"SORT" => 10,
	),
);

?>