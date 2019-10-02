<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arViewModeList = array(
	"LINE" => GetMessage("CPT_BCSL_VIEW_MODE_LINE"),
	"TEXT" => GetMessage("CPT_BCSL_VIEW_MODE_TEXT"),
	"TILE" => GetMessage("CPT_BCSL_VIEW_MODE_TILE")
);

$arTemplateParameters = array(
	"VIEW_MODE" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage('CPT_BCSL_VIEW_MODE'),
		"TYPE" => "LIST",
		"VALUES" => $arViewModeList,
		"MULTIPLE" => "N",
		"DEFAULT" => "LINE"
	),
	"SHOW_PARENT_NAME" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage('CPT_BCSL_SHOW_PARENT_NAME'),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	)
);
?>