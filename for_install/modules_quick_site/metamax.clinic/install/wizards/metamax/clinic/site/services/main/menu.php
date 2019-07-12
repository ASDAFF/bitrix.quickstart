<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

COption::SetOptionString("fileman", "different_set", "Y");
CModule::IncludeModule('fileman');
$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);
if(!$arMenuTypes['main'])
	$arMenuTypes['main'] =  GetMessage("WIZ_MENU_MAIN");
if(!$arMenuTypes['sub'])
	$arMenuTypes['sub'] = GetMessage("WIZ_MENU_SUB");
	
SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
COption::SetOptionInt("fileman", "num_menu_param", 2, false ,WIZARD_SITE_ID);

$map_top_menu_type = str_replace(' ', '', COption::GetOptionString("main", "map_top_menu_type"));
$arMapTopMenu = explode(',',$map_top_menu_type);
if(!in_array("main", $arMapTopMenu))
	COption::SetOptionString("main", "map_top_menu_type", $map_top_menu_type.",main");

$map_left_menu_type = str_replace(' ', '', COption::GetOptionString("main", "map_left_menu_type"));
$arMapLeftMenu = explode(',',$map_left_menu_type);
if(!in_array("sub", $arMapLeftMenu))
	COption::SetOptionString("main", "map_left_menu_type", $map_left_menu_type.",sub");
?>