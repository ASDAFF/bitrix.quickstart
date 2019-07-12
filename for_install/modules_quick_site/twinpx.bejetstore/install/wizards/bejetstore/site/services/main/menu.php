<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

	CModule::IncludeModule('fileman');
	$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);
	
	if(!isset($arMenuTypes['bottom']))
		$arMenuTypes['bottom'] = GetMessage("WIZ_MENU_BOTTOM");

	if(!isset($arMenuTypes['bottom_second']))
		$arMenuTypes['bottom_second'] = GetMessage("WIZ_MENU_BOTTOM_SECOND");

	if(!isset($arMenuTypes['bottom_third']))
		$arMenuTypes['bottom_third'] = GetMessage("WIZ_MENU_BOTTOM_THIRD");

	if(!isset($arMenuTypes['bottom_social']))
		$arMenuTypes['bottom_social'] = GetMessage("WIZ_MENU_SOCIAL");

	if(!isset($arMenuTypes['catalog']))
		$arMenuTypes['catalog'] = GetMessage("WIZ_MENU_CATALOG");

	if(!isset($arMenuTypes['type']))
		$arMenuTypes['type'] = GetMessage("WIZ_MENU_TYPE");

	if(!isset($arMenuTypes['brand']))
		$arMenuTypes['brand'] = GetMessage("WIZ_MENU_BRAND");

	if(!isset($arMenuTypes['payment']))
		$arMenuTypes['payment'] = GetMessage("WIZ_MENU_PAYMENT");

	if(!isset($arMenuTypes['campaign']))
		$arMenuTypes['campaign'] = GetMessage("WIZ_MENU_CAMPAIGN");

	if(!isset($arMenuTypes['personal_menu_auth']))
		$arMenuTypes['personal_menu_auth'] = GetMessage("WIZ_MENU_PERSONAL_AUTH");

	if(!isset($arMenuTypes['personal_menu_not_auth']))
		$arMenuTypes['personal_menu_not_auth'] = GetMessage("WIZ_MENU_PERSONAL_NOT_AUTH");

	/*if($wizard->GetVar("templateID") == "store_light"){
		if($arMenuTypes['top'] && $arMenuTypes['top'] == GetMessage("WIZ_MENU_TOP_DEFAULT"))
			$arMenuTypes['top'] =  GetMessage("WIZ_MENU_LIGHT_TOP");
	} 
	else if($wizard->GetVar("changeTemplate") == "Y" && $wizard->GetVar("templateID") == "store_minimal"){
		if($arMenuTypes['top'] && $arMenuTypes['top'] == GetMessage("WIZ_MENU_LIGHT_TOP"))
			$arMenuTypes['top'] =  GetMessage("WIZ_MENU_TOP_DEFAULT");
	}                        */
	
	SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
	COption::SetOptionInt("fileman", "num_menu_param", 2, false ,WIZARD_SITE_ID);

?>