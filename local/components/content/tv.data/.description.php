<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("TEMPLATE_DESCRIPTION"),
	"PATH" => array(	
		"ID" => "tv_data",
		"NAME" => "TV.DATA",
		"CHILD" => array(
			"ID" => "custom",
			"NAME" => "CUSTOM Компоненты",
			"SORT" => 30
		)

	),
);

?>