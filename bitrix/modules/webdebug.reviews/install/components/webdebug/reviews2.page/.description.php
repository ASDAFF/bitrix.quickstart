<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WD_REVIEWS2_COMP_NAME"),
	"DESCRIPTION" => GetMessage("WD_REVIEWS2_COMP_DESC"),
	"ICON" => "/images/image.gif",
	"COMPLEX" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "webdebug",
		"NAME" => GetMessage("WD_REVIEWS2_COMP_SECTION_WEBDEBUG"),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => "webdebug_reviews",
			"NAME" => GetMessage("WD_REVIEWS2_COMP_SECTION_WEBDEBUG_REVIEWS"),
			"SORT" => 30,
		),
	),
);
?>