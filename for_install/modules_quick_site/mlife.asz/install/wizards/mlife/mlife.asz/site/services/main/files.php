<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");
	
if(COption::GetOptionString("ribpir", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	$wizard =& $this->GetWizard();
	return;
}

//копирование файлов
if(WIZARD_TEMPLATE_ID=="mlife_aszsuper"){
	$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public_super/".LANGUAGE_ID."/"); 
}else{
	$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/");
}
$handle = @opendir($path);
if ($handle)
{
	while ($file = readdir($handle))
	{
		if (in_array($file, array(".", ".."))) 
			continue;
		CopyDirFiles(
			$path.$file,
			WIZARD_SITE_PATH."/".$file,
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false,
			$exclude = "bitrix"
		);

			DeleteDirFilesEx(WIZARD_SITE_DIR."/_index_.php");
		
	}
}

//макросы
WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".left.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".topmenu.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/.lefto.menu_ext.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/foot_left.php", Array("SITE_DIR" => WIZARD_SITE_DIR, "SITE_NAME" => $wizard->GetVar("siteName")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/logo.php", Array("SITE_DIR" => WIZARD_SITE_DIR, "SITE_NAME" => $wizard->GetVar("siteName")));

if(WIZARD_TEMPLATE_ID=="mlife_aszsuper"){
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.bottomshap.menu_ext.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.bottomshap.menu_ext.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/logo_super.php", Array("SITE_DIR" => WIZARD_SITE_DIR, "SITE_NAME" => $wizard->GetVar("siteName")));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".zakaz.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
}

//правила обработки адресов
$arUrlRewrite = array(); 
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
array(
	"CONDITION"	=>	"#^".WIZARD_SITE_DIR."catalog/#",
	"RULE"	=>	"",
	"ID"	=>	"mlife:asz.multicatalog",
	"PATH"	=>	 WIZARD_SITE_DIR."catalog/index.php",
	), 
);

foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
	}
}

$wizard =& $this->GetWizard();

//добавление мета тегов
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", Array("SITE_NAME" => $wizard->GetVar("siteName"), "SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array(
	"META_DESC" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription")),
	"META_KEY" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords")),
	"SITE_NAME" => htmlspecialcharsbx($wizard->GetVar("siteName"))
));


?>