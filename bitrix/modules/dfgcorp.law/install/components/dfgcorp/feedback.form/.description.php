<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("FEEDBACK_FORM_NAME"),
	"DESCRIPTION" => GetMessage("FEEDBACK_FORM_DESCRIPTION"),
	"ICON" => "/images/eaddform.gif",
	"PATH" => array(
		"ID" => "dfg.corp",
        "NAME" => GetMessage("T_FEEDBACK_ADD"),
		"CHILD" => array(
			"ID" => "feedback_add",
			"NAME" => GetMessage("T_FEEDBACK_GROUP_NAME_ADD"),
			"CHILD" => array(
				"ID" => "element_add_cmpx",
			),
		),
	),
);
?>