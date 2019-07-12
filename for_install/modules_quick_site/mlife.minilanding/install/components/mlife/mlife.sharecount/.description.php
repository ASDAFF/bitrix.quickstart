<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MLIFE_SARECOUNT_NAME"),
	"DESCRIPTION" => GetMessage("MLIFE_SARECOUNT_NAME_DESC"),
	"ICON" => "/images/icon_dedline.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => 'mlife',
		"NAME" => GetMessage("MLIFE_MAINSITE_NAME"),
		"CHILD" => array(
			"ID" => 'mlife_counter',
			"NAME" =>  GetMessage("MLIFE_COUNTER_GROUP_NAME")
		)
	),
);

?>