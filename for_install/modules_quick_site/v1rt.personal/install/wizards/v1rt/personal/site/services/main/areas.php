<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

function ___writeToAreasFile($fn, $text)
{
	if(file_exists($fn) && !is_writable($abs_path) && defined("BX_FILE_PERMISSIONS"))
		@chmod($abs_path, BX_FILE_PERMISSIONS);

	$fd = @fopen($fn, "wb");
	if(!$fd)
		return false;

	if(false === fwrite($fd, $text))
	{
		fclose($fd);
		return false;
	}

	fclose($fd);

	if(defined("BX_FILE_PERMISSIONS"))
		@chmod($fn, BX_FILE_PERMISSIONS);
}

//$siteLogo = $wizard->GetVar("siteLogo");
$sWizardTemplatePath = WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID."/";

/*
if($siteLogo > 0)
{
	$file = CFile::GetByID($siteLogo);
	if($zr = $file->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
        if(file_exists($strOldFile))
        {
            @unlink(WIZARD_SITE_PATH."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/images/logo.png");
            @copy($strOldFile, WIZARD_SITE_PATH."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/images/logo.".end(explode(".", $zr["FILE_NAME"])));
            CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("EXT" => end(explode(".", $zr["FILE_NAME"]))));
    		CFile::Delete($siteLogo);
        }
	}
}
else
{
    CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("EXT" => "png"));
}
*/

CheckDirPath(WIZARD_TEMPLATE_ABSOLUTE_PATH."/include_areas/");

$wizard =& $this->GetWizard();
___writeToAreasFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/include_areas/inc.copyright.php", $wizard->GetVar("siteCopyrightText"));

COption::SetOptionString("v1rt.personal", "v1rt_personal_twitter", strip_tags($wizard->GetVar("siteTwitterText")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_phone", strip_tags($wizard->GetVar("sitePhoneText")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_email", strip_tags($wizard->GetVar("siteEmailText")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_vk", strip_tags($wizard->GetVar("siteVKText")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_fb", strip_tags($wizard->GetVar("siteFBText")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_twitter_consumer_key", strip_tags($wizard->GetVar("v1rt_personal_twitter_consumer_key")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_twitter_consumer_secret", strip_tags($wizard->GetVar("v1rt_personal_twitter_consumer_secret")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_twitter_user_token", strip_tags($wizard->GetVar("v1rt_personal_twitter_user_token")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_twitter_user_secret", strip_tags($wizard->GetVar("v1rt_personal_twitter_user_secret")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_type_header", strip_tags($wizard->GetVar("siteTypeHeaderText")));
COption::SetOptionString("v1rt.personal", "v1rt_personal_header_image", "/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/images/header1.jpg");
COption::SetOptionString("v1rt.personal", "v1rt_personal_demo_data", "Y");
COption::SetOptionString("v1rt.personal", "v1rt_personal_site_id", WIZARD_SITE_ID);
COption::SetOptionString("fileman", "show_untitled_styles", "Y");

//Название раздела устанавливаем
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."news/index.php", array("NAME_SECTION" => GetMessage("NAME_SECTION_TYPE_".$wizard->GetVar("siteBlogSectionText"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top.menu.php", array("NAME_SECTION" => GetMessage("NAME_SECTION_TYPE_".$wizard->GetVar("siteBlogSectionText"))));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("NAME_SECTION_LAST" => '<?=GetMessage("NAME_SECTION_LAST_TYPE_' . $wizard->GetVar("siteBlogSectionText") . '")?>'));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("NAME_SECTION_ALL" => '<?=GetMessage("NAME_SECTION_ALL_TYPE_' . $wizard->GetVar("siteBlogSectionText") . '")?>'));
?>