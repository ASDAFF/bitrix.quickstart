<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	"EXPERT_MODE_responsive" => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("EXPERT_MODE_responsive"),
		"TYPE"    => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "N"),

	"EXPERT_MODE_responsivebig"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("EXPERT_MODE_responsivebig"),
		"TYPE"    => "STRING",
		"DEFAULT" => "3"),
	"EXPERT_MODE_responsivemed"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("EXPERT_MODE_responsivemed"),
		"TYPE"    => "STRING",
		"DEFAULT" => "2"),
	"EXPERT_MODE_responsivemin"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("EXPERT_MODE_responsivemin"),
		"TYPE"    => "STRING",
		"DEFAULT" => "1"),
	"EXPERT_MODE_items"                      => array(
		"PARENT"  => "SIMPLE_SETTINGS",
		"NAME"    => GetMessage("EXPERT_MODE_items"),
		"TYPE"    => "STRING",
		"DEFAULT" => "1"),
	"EXPERT_MODE_autoWidth"                      => array(
		"PARENT"  => "EXPERT_MODE",
		"NAME"    => GetMessage("EXPERT_MODE_autoWidth"),
		"TYPE"    => "CHECKBOX",
		"DEFAULT" => "N"),

);

if ($arCurrentValues['EXPERT_MODE_responsive']!='Y'){
	unset($arTemplateParameters['EXPERT_MODE_responsivebig'],
		$arTemplateParameters['EXPERT_MODE_responsivemed'],
		$arTemplateParameters['EXPERT_MODE_responsivemin']
	);
}
if ($arCurrentValues['EXPERT_MODE_responsive']=='Y'){
	unset($arTemplateParameters['EXPERT_MODE_items']
);
}

if ($arCurrentValues['EXPERT_MODE_ON']!='Y'){
	unset($arTemplateParameters['EXPERT_MODE_autoWidth']
);
}
?>