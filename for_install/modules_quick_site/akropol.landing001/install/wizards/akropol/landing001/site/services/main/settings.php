<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;

	
	
// START IMPORT PAGE PROPERTIES
COption::SetOptionString("fileman", "propstypes", serialize(array(
	"telephone"=>GetMessage("MAIN_OPT_TELEPHONE"),
	"Headerlogo"=>GetMessage("MAIN_OPT_HEADERLOGO")
)), false, $siteID);
// END IMPORT PAGE PROPERTIES


// START IMPORT MENU TYPES
	$arMenuTypes = GetMenuTypes();
	$arMenuTypes["top"] = GetMessage("MAIN_OPT_MENU_TOP");
	SetMenuTypes($arMenuTypes, WIZARD_SITE_ID);
// END IMPORT MENU TYPES


?>