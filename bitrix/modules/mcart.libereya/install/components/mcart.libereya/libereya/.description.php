<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LIBEREYA_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("LIBEREYA_COMPONENT_DESCRIPTION"),
	"ICON" => "images/news_all.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "mcart",
		"NAME" => GetMessage("MCART_GROUP_NAME"),
		"CHILD" => array(
			"ID" => "libereya",
			"NAME" => GetMessage("LIBEREYA_COMPONENT_NAME"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "news_cmpx",
			),
		),
	),
);

?>