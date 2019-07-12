<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("DVS_BRANDINFO"),
	"DESCRIPTION" => GetMessage("DVS_BRANDDESC"),
	"ICON" => "/images/cat_list.gif",
	"CACHE_PATH" => "N",
	"SORT" => 30,
	"PATH" => array(
		"ID" => "dvsfashion",
		"NAME" => GetMessage("DVS_GENERAL"),
		"CHILD" => array(
			"ID" => "df_catbrands",
			"NAME" => GetMessage("DVS_CATBYBRANDS")
		),
	),
);

?>