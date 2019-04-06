<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SHS_PRODUCTVIEW_NAME"),
	"DESCRIPTION" => GetMessage("SHS_PRODUCTVIEW_DESCRIPTION"),
	"ICON" => "/images/shs_productvew.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 70,
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "ordered",
			"NAME" => GetMessage("SHS_PRODUCTVIEW_NAME"),
			"SORT" => 30,
		),
	),
);
?>