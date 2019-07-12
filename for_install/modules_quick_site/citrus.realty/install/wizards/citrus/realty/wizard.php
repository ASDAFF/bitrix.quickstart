<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "citrus_realty";
		$this->SetNextStep("select_theme");
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();

		$wizard->solutionName = "citrus_realty";
		parent::InitStep();

	}
	
	function ShowStep() 
	{
		$wizard =& $this->GetWizard();
		if (!CModule::IncludeModule('citrus.realty'))
		{
			$this->SetPrevStep(false);

			$this->SetStepID("finish");
			$this->SetNextStep("finish");
			$this->SetNextCaption(GetMessage("FINISH_WIZARD"));		
			$this->SetError(GetMessage("MODULE_INCLUDE_ERROR"));
			$wizard->SetFormActionScript('/bitrix/admin/');
			
		}
		else
		{
			$tpl = COption::GetOptionString("main", "wizard_template_id", "citrus_realestate", $wizard->GetVar("siteID"));
			$wizard->SetVar('templateID', $tpl);
			parent::ShowStep();
		}
	}
}

class SelectThemeStep extends CSelectThemeWizardStep
{

}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$this->SetStepID("site_settings");
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "citrus_realty";
		parent::InitStep();

		$wizard->SetVar('templateID', "citrus_realestate");

		$this->SetNextStep("data_install");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$site = CSite::GetByID($wizard->GetVar('siteID'))->Fetch();
		$email = $site["EMAIL"] ? $site["EMAIL"] : COption::GetOptionString("main", "email_from", "N");

		try
		{
			$officeInfo = \Citrus\Realty\Helper::getOfficeInfo();
		}
		catch (Exception $e)
		{
			$officeInfo = false;
		}
		$wizard->SetDefaultVars(
			Array(
				"siteName" => str_replace(GetMessage("_WIZ_REPLACE_DEFAULT_SITE_NAME"), GetMessage("WIZ_DEFAULT_SITE_NAME"), $site["NAME"]),
				"siteEMail" => strlen($email) > 0 ? $email : 'info@' . $_SERVER["SERVER_NAME"],
				"siteTelephone" => is_array($officeInfo) ? $officeInfo["PROPERTY_PHONES_VALUE"][0] : GetMessage("WIZ_COMPANY_TELEPHONE_DEF"),
				"siteAddress" => is_array($officeInfo) ? $officeInfo["PROPERTY_ADDRESS_VALUE"] : GetMessage("WIZ_COMPANY_ADDRESS_DEF"),
			)
		);
	}

	function ShowStep()
	{
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_NAME").'<span style=color:red>*</span></label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteName', array("style" => "width:100%", "id" => "siteName")).'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephone">'.GetMessage("WIZ_COMPANY_TELEPHONE").'<span style=color:red>*</span></label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("style" => "width:100%", "id" => "siteTelephone")).'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteEMail">'.GetMessage("WIZ_COMPANY_EMAIL").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteEMail', array("style" => "width:100%", "id" => "siteEMail")).'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteAddress">'.GetMessage("WIZ_COMPANY_ADDRESS").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'siteAddress', array("style" => "width:100%", "rows"=>"2", "id" => "siteAddress")).'</div>
			</div>
		</div>';
		$this->content .= '<span style=color:red>*</span>' . GetMessage("WIZ_REQUIRED_FIELDS") . '</div>';

		ob_start();
		?>
		<div class="inst-note-block inst-note-block-yellow">
			<div class="inst-note-block-icon"></div>
			<div class="inst-note-block-label" style="font-size: 14px;"><?=GetMessage("WIZ_CONTACT_NOTE")?></div>
			<div class="inst-note-block-text" style="clear: left;"><br><?=GetMessage("WIZ_CONTACT_NOTE_TEXT")?></div>
		</div>
		<?
		$this->content = $this->content . ob_get_contents();
		ob_end_clean();

	}
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		if ($wizard->IsNextButtonClick())
		{
			if (!strlen($wizard->GetVar("siteName")) or !strlen($wizard->GetVar("siteTelephone")))
			{
				$this->SetError(GetMessage("WIZ_SITE_OF_ERROR"));
				$wizard->SetCurrentStep("site_settings");
			}
		}
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