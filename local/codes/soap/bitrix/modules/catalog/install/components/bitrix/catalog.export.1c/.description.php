<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CD_BCE1_NAME"),
	"DESCRIPTION" => GetMessage("CD_BCE1_DESCRIPTION"),
	"ICON" => "/images/1c-exp.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 120,
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "catalog",
			"NAME" => GetMessage("CD_BCE1_CATALOG"),
			"SORT" => 30,
		),
	),
);

?>