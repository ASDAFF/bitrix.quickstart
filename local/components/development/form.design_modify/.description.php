<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESCRIPTION"),
	"ICON" => "/images/design_form_modify.gif",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		"CHILD" => array(
			"ID" => "EAform",
			"NAME" => GetMessage("SECTION_2_NAME")
		)
	),
);
?>