<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_NAME" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),

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
