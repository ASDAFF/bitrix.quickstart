<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

WizardServices::IncludeServiceLang("menu.php", $lang);
CModule::IncludeModule('fileman');
$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);

$arMenuTypes['left'] = GetMessage('WIZ_MENU_left');
$arMenuTypes['top'] = GetMessage('WIZ_MENU_top');
$arMenuTypes['top_menu'] = GetMessage('WIZ_MENU_top_menu');
$arMenuTypes['catalog'] = GetMessage('WIZ_MENU_catalog');

SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
COption::SetOptionInt("fileman", "num_menu_param", 2, false ,WIZARD_SITE_ID);
