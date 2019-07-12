<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;
	
if (WIZARD_INSTALL_DEMO_DATA)
{
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID,
		WIZARD_SITE_PATH,
		$rewrite = true, 
		$recursive = true,
				$delete_after_copy = false
	);
	
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
	
	$arUrlRewrite = array(); 
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
	{
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}
	$arNewUrlRewrite = array(
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."news/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:news",
			"PATH"	=>	 WIZARD_SITE_DIR."news/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."action/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:news",
			"PATH"	=>	 WIZARD_SITE_DIR."action/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/appliance/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/appliance/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/builtin/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/builtin/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/home/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/home/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/producer/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/producer/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/refrigerators/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/refrigerators/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/stoves/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/stoves/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/washing/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 WIZARD_SITE_DIR."catalog/washing/index.php",
			), 
		array(
			"CONDITION"	=>	"#^".WIZARD_SITE_DIR."personal/order/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:sale.personal.order",
			"PATH"	=>	 WIZARD_SITE_DIR."personal/order/index.php",
			), 
	);
	foreach ($arNewUrlRewrite as $arUrl)
	{
		if (!in_array($arUrl, $arUrlRewrite))
		{
			CUrlRewriter::Add($arUrl);
		}
	}
}
else
{
	CopyDirFiles(
		WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID.'/include/',
		WIZARD_SITE_PATH.'/include/',
		$rewrite = true, 
		$recursive = true,
				$delete_after_copy = false
	);
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH.'/includes/', Array("SITE_DIR" => WIZARD_SITE_DIR));
}

if ($wizard->GetVar("installDemoData")<>"Y")
{	
	// unlink(WIZARD_SITE_PATH."/includes/default_logo.png");
	// copy($_SERVER['DOCUMENT_ROOT'].$wizard->GetVar("install_logo") , WIZARD_SITE_PATH."/includes/default_logo.png");
	
	
	$wizard = &$this->GetWizard();
	$sitePhoneCode=$wizard->GetVar("siteTelephoneCode");
	$sitePhone=$wizard->GetVar("siteTelephone");
	$s_name=$wizard->GetVar("siteName");
	$siteICQ=$wizard->GetVar("siteICQ");
	$fio_main=$wizard->GetVar("fiomain");

	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));
	
	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/company_name.php", Array("COMPANY_NAME" => $s_name));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/telephone.php", Array("S_PHONE" => $sitePhone));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/telephone.php", Array("CODE" => $sitePhoneCode));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/icq.php", Array("SITE_ICQ" => $siteICQ));
	
	if (WIZARD_TEMPLATE_ID!='household_first')
		{
			copy(WIZARD_SITE_PATH."/index2.php" , WIZARD_SITE_PATH."/_index.php");
			copy(WIZARD_SITE_PATH."/novelty2.php" , WIZARD_SITE_PATH."/novelty.php");
			copy(WIZARD_SITE_PATH."/hit2.php" , WIZARD_SITE_PATH."/hit.php");
			copy(WIZARD_SITE_PATH."/bestprice2.php" , WIZARD_SITE_PATH."/bestprice.php");
		}
		else
		{
			copy(WIZARD_SITE_PATH."/index1.php" , WIZARD_SITE_PATH."/_index.php");
			copy(WIZARD_SITE_PATH."/novelty1.php" , WIZARD_SITE_PATH."/novelty.php");
			copy(WIZARD_SITE_PATH."/hit1.php" , WIZARD_SITE_PATH."/hit.php");
			copy(WIZARD_SITE_PATH."/bestprice1.php" , WIZARD_SITE_PATH."/bestprice.php");
			
		}
				
	return;
}

$wizard = &$this->GetWizard();
	$wizard = &$this->GetWizard();
	$sitePhoneCode=$wizard->GetVar("siteTelephoneCode");
	$sitePhone=$wizard->GetVar("siteTelephone");
	$s_name=$wizard->GetVar("siteName");
	$siteICQ=$wizard->GetVar("siteICQ");
	$fio_main=$wizard->GetVar("fiomain");

	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));


	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/company_name.php", Array("COMPANY_NAME" => $s_name));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/telephone.php", Array("S_PHONE" => $sitePhone));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/telephone.php", Array("CODE" => $sitePhoneCode));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/icq.php", Array("SITE_ICQ" => $siteICQ));


unlink(WIZARD_SITE_PATH."/includes/default_logo.png");
copy($_SERVER['DOCUMENT_ROOT'].$wizard->GetVar("install_logo") , WIZARD_SITE_PATH."/includes/default_logo.png");

		if (WIZARD_TEMPLATE_ID!='household_first')
		{
			copy(WIZARD_SITE_PATH."/index2.php" , WIZARD_SITE_PATH."/_index.php");
			copy(WIZARD_SITE_PATH."/novelty2.php" , WIZARD_SITE_PATH."/novelty.php");
			copy(WIZARD_SITE_PATH."/hit2.php" , WIZARD_SITE_PATH."/hit.php");
			copy(WIZARD_SITE_PATH."/bestprice2.php" , WIZARD_SITE_PATH."/bestprice.php");
		}
		else
		{
			copy(WIZARD_SITE_PATH."/index1.php" , WIZARD_SITE_PATH."/_index.php");
			copy(WIZARD_SITE_PATH."/novelty1.php" , WIZARD_SITE_PATH."/novelty.php");
			copy(WIZARD_SITE_PATH."/hit1.php" , WIZARD_SITE_PATH."/hit.php");
			copy(WIZARD_SITE_PATH."/bestprice1.php" , WIZARD_SITE_PATH."/bestprice.php");
			
		}
			
					
?>
