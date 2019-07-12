<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

	CModule::IncludeModule('fileman');
	$arMenuTypes = array();
	$arMenuTypes['left'] = GetMessage("MLIFE_ASZ_WIZ_MENU_1");
	$arMenuTypes['lefto'] = GetMessage("MLIFE_ASZ_WIZ_MENU_3");
	$arMenuTypes['topmenu'] = GetMessage("MLIFE_WIZ_MENU_2");
	
	SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
	COption::SetOptionInt("fileman", "num_menu_param", 2, false ,WIZARD_SITE_ID);

?>