<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WD_REVIEWS2_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("WD_REVIEWS2_COMPONENT_DESC"),
	"ICON" => "/images/image.gif",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "webdebug",
		"NAME" => GetMessage("WD_REVIEWS2_COMPONENT_SECTION_WEBDEBUG"),
		"SORT" => 450,
		"CHILD" => array(
			"ID" => "webdebug_reviews",
			"NAME" => GetMessage("WD_REVIEWS2_COMPONENT_SECTION_WEBDEBUG_REVIEWS"),
			"SORT" => 40,
		),
	),
);
?>