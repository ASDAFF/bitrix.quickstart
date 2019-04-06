<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ASD_CMP_NAME"),
	"DESCRIPTION" => GetMessage("ASD_CMP_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 100,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"NAME" => GetMessage("ASD_CMP_GLOBAL_DIR_NAME"),
		"CHILD" => array(
			"ID" => "iblock",
			"NAME" => GetMessage("ASD_CMP_DIR_NAME")
		),

	),
);

?>