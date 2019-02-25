<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IPOLSDEK_COMP_NAME"),
	"DESCRIPTION" => GetMessage("IPOLSDEK_COMP_DESCR"),
	"ICON" => "/images/sdek_pickup.png",
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "ipol",
			"NAME" => GetMessage("IPOLSDEK_GROUP"),
			"SORT" => 30,
			"CHILD" => array(
				"ID" => "ipol_sdekPickup",
			),
		),
	),
);
?>