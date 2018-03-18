<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESCRIPTION"),
	"ICON" => "/images/design_form_modify.gif",
	"PATH" => array(
		"ID" => "elipseart",
		"NAME" => GetMessage("SECTION_1_NAME"),
		"CHILD" => array(
			"ID" => "EAform",
			"NAME" => GetMessage("SECTION_2_NAME")
		)
	),
);
?>