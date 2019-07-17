<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CD_BLL_LISTS"),
	"DESCRIPTION" => GetMessage("CD_BLL_DESCRIPTION"),
	"ICON" => "/images/lists_list.gif",
	"SORT" => 30,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "ASDAFF",
		"CHILD" => array(
			"ID" => "content",
			"NAME" => 'Контент',
			"SORT" => 35,
		)
	),
);

?>