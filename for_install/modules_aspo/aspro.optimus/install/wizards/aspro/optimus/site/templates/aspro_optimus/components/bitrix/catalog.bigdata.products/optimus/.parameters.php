<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters['LINE_ELEMENT_COUNT'] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CVP_LINE_ELEMENT_COUNT"),
	"TYPE" => "STRING",
	"DEFAULT" => "3",
);
$arTemplateParameters['DISPLAY_COMPARE'] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("DISPLAY_COMPARE_NAME"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);

$arTemplateParameters['SHOW_RATING'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage("SHOW_RATING"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);