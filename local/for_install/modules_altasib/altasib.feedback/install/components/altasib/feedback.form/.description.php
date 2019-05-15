<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ALX_DESC_FAQ_ADD_NAME"),
	"DESCRIPTION" => GetMessage("ALX_DESC_REVIEW_ADD_DESC"),
	"ICON" => "/images/icon.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "IS-MARKET.RU",
		"CHILD" => array(
			"ID" => "altasib_feedback",
			"NAME" => GetMessage("ALX_DESC_FORMS_SECTION_NAME"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "review_add",
			),
		),
	),
);

?>