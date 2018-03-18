<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"BLOCK_TITLE" => Array(
		"NAME" => GetMessage("T_IBLOCK_TITLE"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("T_IBLOCK_TITLE_DEFAULT"),
	),
	"MAX_WIDTH" => Array(
		"NAME" => GetMessage("T_IMAGE_MAX_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("T_IMAGE_MAX_WIDTH_DEFAULT"),
	),
	"MAX_HEIGHT" => Array(
		"NAME" => GetMessage("T_IMAGE_MAX_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("T_IMAGE_MAX_HEIGHT_DEFAULT"),
	),
	
);

?>
