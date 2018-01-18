<?php

if( !defined("B_PROLOG_INCLUDED") || (B_PROLOG_INCLUDED!==true) ) die();

$arComponentDescription = array(
	"NAME"        => GetMessage("2GIS_MAP_SIMPLE_NAME"),
	"DESCRIPTION" => GetMessage("2GIS_MAP_SIMPLE_DESC"),
	"ICON"        => "/images/icon.gif",
	"SORT"        => 20,
	"CACHE_PATH"  => "Y",
	"PATH"        => array(
		"ID"    => "simai",
		"NAME"  => GetMessage("SIMAI_COMPONENTS_NAME"), // "SIMAI Components",
		"CHILD" => array(
			"ID"   => "simai_maps",
			"NAME" => GetMessage("SIMAI_COMPONENTS_MAPS_NAME"), // "Maps"
			"SORT" => 10,
		),
	),
);

?>
