<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("TABC_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("TABC_COMPONENT_DESCR"),
	"ICON" => "/images/catalog.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "lists",
			"NAME" => GetMessage("CD_BLL_LISTS"),
		)
	),
);

?>