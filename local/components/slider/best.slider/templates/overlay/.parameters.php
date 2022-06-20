<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	"FONT_SIZE_TITLE"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("FONT_SIZE_TITLE"),
		"TYPE"    => "STRING",
		"DEFAULT" => "30px"),
	"FONT_SIZE_DESC"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("FONT_SIZE_DESC"),
		"TYPE"    => "STRING",
		"DEFAULT" => "18px"),
	"FONT_SIZE_TITLE_MOB"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("FONT_SIZE_TITLE_MOB"),
		"TYPE"    => "STRING",
		"DEFAULT" => "20px"),
	"FONT_SIZE_DESC_MOB"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("FONT_SIZE_DESC_MOB"),
		"TYPE"    => "STRING",
		"DEFAULT" => "16px"),
);

?>