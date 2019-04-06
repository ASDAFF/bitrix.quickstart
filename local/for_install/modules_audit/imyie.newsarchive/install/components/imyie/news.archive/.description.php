<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IMYIE_COM_NAME"),
	"DESCRIPTION" => GetMessage("IMYIE_COM_DESCRIPTION"),
	"ICON" => "",
	"PATH" => array(
		"ID" => "imyie",
		"SORT" => 10000,
		"NAME" => GetMessage("IMYIE_MAIN_SECTION"),
		"CHILD" => array(
			"ID" => "content",
			"NAME" => GetMessage("IMYIE_SUB_SECTION"),
			"SORT" => 10,
		)
	),
);
?>