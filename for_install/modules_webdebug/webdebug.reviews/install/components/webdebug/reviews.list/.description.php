<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WEBDEBUG_REVIEWS_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("WEBDEBUG_REVIEWS_COMPONENT_DESC"),
	"ICON" => "/images/image.gif",
	"SORT" => 20,
	"PATH" => array(
		"ID" => "webdebug",
		"NAME" => GetMessage("WEBDEBUG_REVIEWS_COMPONENT_SECTION_WEBDEBUG"),
		"SORT" => 450,
		"CHILD" => array(
			"ID" => "webdebug_reviews",
			"NAME" => GetMessage("WEBDEBUG_REVIEWS_COMPONENT_SECTION_WEBDEBUG_REVIEWS"),
			"SORT" => 1000,
		),
	),
);
?>