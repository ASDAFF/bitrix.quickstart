<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "bejet_site";
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
		$wizard->solutionName = "bejet_site";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$wizard->SetDefaultVars(
			Array(
                "siteName" => $this->GetFileContent(WIZARD_SITE_PATH."include_areas/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
				"siteSlogan" => $this->GetFileContent(WIZARD_SITE_PATH."include_areas/slogan.php", GetMessage("WIZ_COMPANY_SLOGAN_DEF")),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$siteLogo = $wizard->GetVar("siteLogo", true);

		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';

        /*$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_NAME").'</label><br />';
		$this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-slogan", "style" => "width:100%"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';*/

		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_SLOGAN").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteSlogan", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("text", "sitePhone", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_ADDRESS").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteAddress", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '</table>';

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
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