<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "avtoservice";
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
		$wizard->solutionName = "avtoservice";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

				
		$wizard->SetDefaultVars(
			Array(
				"siteAddressText" => $this->GetFileContent(WIZARD_SITE_PATH."include/ulica.php", GetMessage("WIZ_ADDRESS_DEF")),
				"sitePhoneText" => $this->GetFileContent(WIZARD_SITE_PATH."include/telefon.php", GetMessage("WIZ_PHONE_DEF")),
				"siteWorktimeText" => $this->GetFileContent(WIZARD_SITE_PATH."include/regim.php", GetMessage("WIZ_WORKTIME_DEF")),
				"siteCopyrightText" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COPYRIGHT_DEF")),
				)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="address">'.GetMessage("WIZ_ADDRESS").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteAddressText", Array("id" => "address", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</td></tr>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="phone">'.GetMessage("WIZ_PHONE").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "sitePhoneText", Array("id" => "phone", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</td></tr>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="worktime">'.GetMessage("WIZ_WORKTIME").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteWorktimeText", Array("id" => "worktime", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</td></tr>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '<tr><td>';
		$this->content .= '<label for="copytext">'.GetMessage("WIZ_COPYRIGHT").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCopyrightText", Array("id" => "copytext", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</td></tr>';

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