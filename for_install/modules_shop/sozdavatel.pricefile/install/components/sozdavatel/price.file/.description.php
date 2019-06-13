<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SOZDAVATEL_PRICEFILE_NAME"),
	"DESCRIPTION" => GetMessage("SOZDAVATEL_PRICEFILE_DESCRIPTION"),
	"ICON" => "/images/pricefile.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "sozdavatel",
		"NAME" => GetMessage("SOZDAVATEL_NAME"),
		"SORT" => 10,
	),
);

?>