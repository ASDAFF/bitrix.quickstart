<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if (!defined("WIZARD_INSTALL_DEMO_DATA"))
	define("WIZARD_INSTALL_DEMO_DATA", false);
	
if(WIZARD_INSTALL_DEMO_DATA){
//	если устанавливать демо-данные, копируем все подряд из папки public
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID,
		WIZARD_SITE_PATH,
		$rewrite = true, 
		$recursive = true,
		$delete_after_copy = false
	);
		
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
		
	$wizard =& $this->GetWizard();
	$arReplace = Array(
		"COMPANY_NAME" => $wizard->GetVar("company_name"),
		"COMPANY_SLOGAN" => $wizard->GetVar("company_slogan"),
		"COMPANY_PHONE" => $wizard->GetVar("company_phone"),
		"COMPANY_ADRESS" => $wizard->GetVar("company_adress"),
		"COMPANY_EMAIL" => $wizard->GetVar("company_email"),
		"COMPANY_CONTROL_EMAIL" => $wizard->GetVar("company_control_email"),
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/company_name.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/slogan.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/phone.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/contacts.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/copy.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/contacts/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/contacts/quality/index.php", $arReplace);


	$arUrlRewrite = array();
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php")){
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}
	$arNewUrlRewrite = array(
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."services/#",	
			"RULE" => "", 
			"ID" => "bitrix:services".WIZARD_SITE_ID,
			"PATH" => WIZARD_SITE_DIR."services/index.php"), 
	);
	foreach ($arNewUrlRewrite as $arUrl){
		if (!in_array($arUrl, $arUrlRewrite)){
			CUrlRewriter::Add($arUrl);
		}
	}
}else{
	//	если нет, пытаемся скопировать форму обратной связи и контроля качества
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/contacts",
		WIZARD_SITE_PATH."/contacts",
		$rewrite = false, 
		$recursive = true,
		$delete_after_copy = false
	);
	
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/include",
		WIZARD_SITE_PATH."/include",
		$rewrite = true, 
		$recursive = true,
		$delete_after_copy = false
	);
	
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
	
	$wizard =& $this->GetWizard();
	$arReplace = Array(
		"COMPANY_NAME" => $wizard->GetVar("company_name"),
		"COMPANY_SLOGAN" => $wizard->GetVar("company_slogan"),
		"COMPANY_PHONE" => $wizard->GetVar("company_phone"),
		"COMPANY_ADRESS" => $wizard->GetVar("company_adress"),
		"COMPANY_EMAIL" => $wizard->GetVar("company_email"),
		"COMPANY_CONTROL_EMAIL" => $wizard->GetVar("company_control_email"),
	);
	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/company_name.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/slogan.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/phone.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/contacts.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/copy.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/contacts/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/contacts/quality/index.php", $arReplace);
}

?>