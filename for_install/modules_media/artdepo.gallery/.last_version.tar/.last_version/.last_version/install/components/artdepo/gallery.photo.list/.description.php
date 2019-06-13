<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_ARTDEPO_GALLERY_PHOTO_LIST_NAME"),
	"DESCRIPTION" => GetMessage("T_ARTDEPO_GALLERY_PHOTO_LIST_DESC"),
	"ICON" => "/images/photo_list.gif",
	"SORT" => 22,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "artdepo",
		"SORT" => 3000,
		"CHILD" => array(
			"ID" => "gallery",
			"NAME" => GetMessage("T_ARTDEPO_DESC_GALLERY"),
			"SORT" => 10,
		),
	),
);

?>
