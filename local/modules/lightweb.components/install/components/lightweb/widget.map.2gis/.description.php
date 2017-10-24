<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LW_WIDGET_MAP_2GIS_NAME"),
	"DESCRIPTION" => GetMessage("LW_WIDGET_MAP_2GIS__DESC"),
	"ICON" => "/images/widget_2gis.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "widget_list",
		"NAME" => GetMessage("LW_WIDGETS"),
		"SORT" => 10,
	),
);

?>