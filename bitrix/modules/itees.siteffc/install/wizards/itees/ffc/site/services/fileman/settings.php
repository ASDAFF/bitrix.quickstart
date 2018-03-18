<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$arReplace = array(
	"MAIN_PAGE_TITLE" => GetMessage('MAIN_PAGE_TITLE'),
	"EMAIL_TITLE" => GetMessage('EMAIL_TITLE'),
	"SITEMAP_TITLE" => GetMessage('SITEMAP_TITLE'),
	"FEEDBACK_TITLE" => GetMessage('FEEDBACK_TITLE'),
	"ONLINEFORM_TITLE" => GetMessage('ONLINEFORM_TITLE'),
	"QUALITY_CONTROL_TITLE" => GetMessage('QUALITY_CONTROL_TITLE'),
	"COMPANY_INFO_TITLE" => GetMessage('COMPANY_INFO_TITLE'),
	"WIZARD_BUTTON_DESCRIPTION" => GetMessage('WIZARD_BUTTON_DESCRIPTION'),
	"WIZARD_BUTTON_NAME" => GetMessage('WIZARD_BUTTON_NAME')
);

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID;
WizardServices::ReplaceMacrosRecursive($bitrixTemplateDir, $arReplace);

$mainBitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/main_".WIZARD_SITE_ID;
WizardServices::ReplaceMacrosRecursive($mainBitrixTemplateDir, $arReplace);
	
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_SITE_ID."/footer.php", array("ORDERFORM_ACTIVE" => "N"));
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/main_".WIZARD_SITE_ID."/footer.php", array("ORDERFORM_ACTIVE" => "N"));

?>