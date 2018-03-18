<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "clinic";
	}
}

class SelectTemplateStep extends CSelectTemplateWizardStep
{
}

class SelectThemeStep extends CSelectThemeWizardStep
{
}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "clinic";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$siteLogo = "/bitrix/wizards/metamax/clinic/site/templates/clinic/images/logo.gif";
		//$siteBanner = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/banner.php", "/bitrix/wizards/bitrix/corp_services/site/templates/corp_services/images/banner.png");
		
		$wizard->SetDefaultVars(
			Array(
				"siteLogo" => $siteLogo,
				"siteClinicNameText" => $this->GetFileContent(WIZARD_SITE_PATH."include/clinic_name.php", GetMessage("WIZ_CLINIC_NAME_DEF")),
				"siteAddressText" => $this->GetFileContent(WIZARD_SITE_PATH."include/address.php", GetMessage("WIZ_ADDRESS_DEF")),
				"sitePhoneText" => $this->GetFileContent(WIZARD_SITE_PATH."include/phone.php", GetMessage("WIZ_PHONE_DEF")),
				"siteWorktimeText" => $this->GetFileContent(WIZARD_SITE_PATH."include/worktime.php", GetMessage("WIZ_WORKTIME_DEF")),
				"siteCopyrightText" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COPYRIGHT_DEF")),
				"siteBannerText" => $this->GetFileContent(WIZARD_SITE_PATH."include/banner.php", GetMessage("WIZ_BANNER_DEF")),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);

		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="clinic-name">'.GetMessage("WIZ_CLINIC_NAME").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteClinicNameText", Array("id" => "clinic-name", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="address">'.GetMessage("WIZ_ADDRESS").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteAddressText", Array("id" => "address", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="phone">'.GetMessage("WIZ_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "sitePhoneText", Array("id" => "phone", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="worktime">'.GetMessage("WIZ_WORKTIME").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteWorktimeText", Array("id" => "worktime", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="banner">'.GetMessage("WIZ_BANNER").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteBannerText", Array("id" => "banner", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="copytext">'.GetMessage("WIZ_COPYRIGHT").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCopyrightText", Array("id" => "copytext", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '</table>';
		$this->content .= $this->ShowHiddenField("installDemoData","Y");
		
		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
//		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
//		$res = $this->SaveFile("siteBanner", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 600, "max_width" => 600, "make_preview" => "Y"));
//		COption::SetOptionString("main", "wizard_site_logo", $res, "", $wizard->GetVar("siteID")); 
	}
}

class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		$wizard =& $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y")
		{
		}
	}
}

class FinishStep extends CFinishWizardStep
{
}
?>