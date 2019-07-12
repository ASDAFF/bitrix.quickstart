<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID("start");
		$this->SetNextStep("template");
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "spectech";
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
	function InitStep()
	{
		$this->SetStepID("template");
		$this->SetNextStep("theme");
		$this->SetTitle(GetMessage("SELECT_TEMPLATE"));
		$this->SetPrevCaption(GetMessage("MOVE_BACK"));
		$this->SetNextCaption(GetMessage("MOVE_FORWARD"));
		$this->SetPrevStep("start");
	}
}

class SelectThemeStep extends CSelectThemeWizardStep
{
	function InitStep()
	{
		$this->SetStepID("theme");
		$this->SetNextStep("options");
		$this->SetTitle(GetMessage("SELECT_COLOR_SCHEME"));
		$this->SetPrevCaption(GetMessage("MOVE_BACK"));
		$this->SetNextCaption(GetMessage("MOVE_FORWARD"));
		$this->SetPrevStep("template");
	}
}

class InputOptionsStep extends CWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();
		$def_site_vars = array('EMAIL_FROM' => COption::GetOptionString('main','email_from'));
		$wizard->SetDefaultVars($def_site_vars);

		$this->SetStepID("options");
		$this->SetNextStep("install");
		$this->SetTitle(GetMessage("ENTER_SETTINGS"));
		$this->SetPrevCaption(GetMessage("MOVE_BACK"));
		$this->SetNextCaption(GetMessage("INSTALL_SOLUTION"));
		$this->SetPrevStep("theme");
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);

		$style_text = array('size' => '0', 'style' => 'width:150px');
		$this->content = GetMessage("ENETER_EMAIL");
		$this->content .= $this->ShowInputField("text", "EMAIL_FROM", $style_text + array("id" => "EMAIL_FROM"));
		$formName = $wizard->GetFormName();
	}
}

class DataInstallStep extends CDataInstallWizardStep
{
	function InitStep()
	{
		$this->SetStepID("install");
		$this->SetTitle(GetMessage("DATA_INSTALLATION"));
	}
}

class FinishStep extends CFinishWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		COption::SetOptionString("main","email_from",$wizard->GetVar('EMAIL_FROM'));

		$this->SetStepID("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextStep("finish");
		$this->SetNextCaption(GetMessage("GO_TO_SITE"));
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		$siteDir = "/";
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"];

		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir."/?finish"));

		$this->CreateNewIndex();

		COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID);

		$this->content .= GetMessage("FINISH_STEP_CONTENT");

		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");
	}
}
?>