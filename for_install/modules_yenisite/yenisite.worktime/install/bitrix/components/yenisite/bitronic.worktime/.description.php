<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WORKTIME_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("WORKTIME_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/ys_wt.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 70,
	"PATH" => array(	
		"ID" => "yenisite",
		"NAME" => GetMessage("YENISITE_COMPONENTS"),
		"CHILD" => array(
			"ID" => "bitronic",
			"NAME" => GetMessage("CD_RO_RSS"),
			"SORT" => 30
		)

	),
);

?>