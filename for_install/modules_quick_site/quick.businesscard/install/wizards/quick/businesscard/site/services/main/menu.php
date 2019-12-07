<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();CModule::IncludeModule("fileman");COption::SetOptionString("fileman","different_set","Y");COption::SetOptionInt("fileman","num_menu_param",2,false,WIZARD_SITE_ID);$arMenuTypes=GetMenuTypes(WIZARD_SITE_ID);if(!$arMenuTypes["top"])
$arMenuTypes["top"]=GetMessage("WIZ_MENU_TOP");if(!$arMenuTypes["left"])
$arMenuTypes["left"]=GetMessage("WIZ_MENU_LEFT");if(!$arMenuTypes["right"])
$arMenuTypes["right"]=GetMessage("WIZ_MENU_RIGHT");if(!$arMenuTypes["bottom"])
$arMenuTypes["bottom"]=GetMessage("WIZ_MENU_BOTTOM");if(!$arMenuTypes["podmenu"])
$arMenuTypes["podmenu"]=GetMessage("WIZ_MENU_PODMENU");SetMenuTypes($arMenuTypes,WIZARD_SITE_ID);?>