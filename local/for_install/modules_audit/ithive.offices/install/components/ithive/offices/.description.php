<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMP_NAME"),
	"DESCRIPTION" => GetMessage("COMP_DESCR"),
	"ICON" => "/images/offices.gif",
	"PATH" => array(
		"ID" => "ithive",
		"NAME" => GetMessage("ITHIVE_PARENT"),
		"CHILD" => array(
			"ID" => "ithiveoffices",
			"NAME" => GetMessage("ITHIVE_SECTION"),
		)
	)
);

?>