<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("TEMPLATE_DESCRIPTION"),
	"PATH" => array(	
		"ID" => "ASDAFF",
		"NAME" => "ASDAFF",
		"CHILD" => array(
			"ID" => "content",
			"NAME" => "Режим работы",
			"SORT" => 30
		)

	),
);

?>