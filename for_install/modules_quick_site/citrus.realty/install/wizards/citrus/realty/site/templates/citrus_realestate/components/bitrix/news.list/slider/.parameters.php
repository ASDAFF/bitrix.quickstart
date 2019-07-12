<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

IncludeTemplateLangFile(__FILE__);

$arTemplateParameters = array(
	"SHOW_NEXT_PREV" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_SHOW_NEXT_PREV"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SHOW_PAGINATION" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_SHOW_PAGINATION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"WIDTH" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "1024",
	),
	"HEIGHT" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "460",
	),
	"DELAY" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_DELAY"),
		"TYPE" => "STRING",
		"DEFAULT" => "5000",
	),
	"SPEED" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_SPEED"),
		"TYPE" => "STRING",
		"DEFAULT" => "350",
	),
	"HOVER_PAUSE" => array(
		"NAME" => GetMessage("CITRUS_SLIDER_HOVER_PAUSE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),

);
?>