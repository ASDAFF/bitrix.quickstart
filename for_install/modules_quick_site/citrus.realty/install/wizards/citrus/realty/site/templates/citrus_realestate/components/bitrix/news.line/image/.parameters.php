<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"RESIZE_IMAGE_WIDTH" => Array(
		"NAME" => GetMessage("PARAM_RESIZE_IMAGE_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"RESIZE_IMAGE_HEIGHT" => Array(
		"NAME" => GetMessage("PARAM_RESIZE_IMAGE_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"COLORBOX_MAXWIDTH" => Array(
		"NAME" => GetMessage("PARAM_COLORBOX_MAXWIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "800",
	),
	"COLORBOX_MAXHEIGHT" => Array(
		"NAME" => GetMessage("PARAM_COLORBOX_MAXHEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "600",
	),
);

?>