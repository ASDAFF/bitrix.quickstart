<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"SLIDING_PER_CLICK" => Array(
		"NAME" => "Пролистывать за клик",
		"TYPE" => "TEXT",
		"DEFAULT" => "2",
	),
	"SLIDER_COUNT" => Array(
		"NAME" => "Элементов в слайдере",
		"TYPE" => "TEXT",
		"DEFAULT" => "6",
	),
	"IMG_WIDTH" => Array(
		"NAME" => "Размер картинки",
		"TYPE" => "TEXT",
		"DEFAULT" => "75",
	),
	"IMG_PADDING" => Array(
		"NAME" => "Отступ между картинками",
		"TYPE" => "TEXT",
		"DEFAULT" => "10",
	),

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
);
?>
