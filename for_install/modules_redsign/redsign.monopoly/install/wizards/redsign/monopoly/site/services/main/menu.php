<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

    WizardServices::IncludeServiceLang("menu.php", $lang);
	CModule::IncludeModule('fileman');
	$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);
	
	/*if($wizard->GetVar("templateID") == "store_light"){
		if($arMenuTypes['top'] && $arMenuTypes['top'] == GetMessage("WIZ_MENU_TOP_DEFAULT"))
			$arMenuTypes['top'] =  GetMessage("WIZ_MENU_LIGHT_TOP");
	} 
	else if($wizard->GetVar("changeTemplate") == "Y" && $wizard->GetVar("templateID") == "store_minimal"){
		if($arMenuTypes['top'] && $arMenuTypes['top'] == GetMessage("WIZ_MENU_LIGHT_TOP"))
			$arMenuTypes['top'] =  GetMessage("WIZ_MENU_TOP_DEFAULT");
	}                        */

	$arMenuTypes['top'] = GetMessage("WIZ_MENU_TOP");
	$arMenuTypes['topsub'] = GetMessage("WIZ_MENU_TOPSUB");

	SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
	COption::SetOptionInt("fileman", "num_menu_param", 2, false ,WIZARD_SITE_ID);

?>