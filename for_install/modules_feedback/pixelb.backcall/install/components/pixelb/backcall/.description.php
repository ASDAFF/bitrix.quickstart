<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("FORM_NAME_LABEL"),
	"DESCRIPTION" => GetMessage("FORM_DESC_LABEL"),
	"ICON" => "/images/form.gif",
	"PATH" => array(
		"ID" => "PIXELB",
		"CHILD" => array(
			"ID" => "backcall",
			"NAME" => GetMessage("MAIN_FORM_SERVICE")
		)
	),
);

?>
