<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule('fileman');
$arMenuTypes = GetMenuTypes(WIZARD_SITE_ID);

$arMenuTypes = array(
	'left' => GetMessage("WIZ_MENU_LEFT"),
	'top' => GetMessage("WIZ_MENU_TOP"),
	'bottom' => GetMessage("WIZ_MENU_BOTTOM"),
	'top_super' => GetMessage("WIZ_MENU_TOP_SUPER"),
);

SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
COption::SetOptionInt("fileman", "num_menu_param", 1, false ,WIZARD_SITE_ID);
?>