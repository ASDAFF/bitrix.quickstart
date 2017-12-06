<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LW_INCLUDE_FILE_NAME"),
	"DESCRIPTION" => GetMessage("LW_INCLUDE_FILE_NAME__DESCR"),
	"ICON" => "/images/include.gif",
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "include_area",
			"NAME" => GetMessage("MAIN_INCLUDE_GROUP_NAME"),
		),
	),
);
?>