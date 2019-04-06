<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LW_WIDGET_MAP_YANDEX_NAME"),
	"DESCRIPTION" => GetMessage("LW_WIDGET_MAP_YANDEX__DESC"),
	"ICON" => "/images/widget_ymap.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "widget_list",
		"NAME" => GetMessage("LW_WIDGETS"),
		"SORT" => 10,
	),
);

?>