<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ASKARON_IBVOTE_IBLOCK_VOTE_NAME"),
	"DESCRIPTION" => GetMessage("ASKARON_IBVOTE_IBLOCK_VOTE_DESCRIPTION"),
	"ICON" => "/images/photo_detail.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "askaron_components",
		"NAME" => GetMessage("ASKARON_COMPONENTS_GROUP_NAME"),
		"CHILD" => array(
			"ID" => "askaron_ibvote",
			"NAME" => GetMessage("ASKARON_IBVOTE_GROUP_NAME"),
		)
	),	
);
?>