<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "minilanding";
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
		$wizard->solutionName = "minilanding";
		parent::InitStep();
		
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("MLIFE_MINILANDING_WIZ_STEP1"));
		$this->SetNextStep("data_install");
		
		$siteID = $wizard->GetVar("siteID");
		
		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");
		
		$wizard =& $this->GetWizard();
		
		$wizard->SetDefaultVars(
			Array(
				"siteEmail" => "info@minilanding.ru",
				"sitePhone" => "<b>+7 (000)</b> 000-00-00",
				"siteTextTop" => GetMessage("MLIFE_MINILANDING_WIZ_TEXT1"),
				"siteTextBottom" => GetMessage("MLIFE_MINILANDING_WIZ_TEXT2"),
				)
		);
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		$wizard =& $this->GetWizard();
		
		//название сайта
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text-top" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_MINILANDING_WIZ_TITLE_TEXT1").'</label>';
		$this->content .= $this->ShowInputField("text", "siteTextTop", Array("id" => "site-text-top", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//название сайта
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text-bottom" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_MINILANDING_WIZ_TITLE_TEXT2").'</label>';
		$this->content .= $this->ShowInputField("text", "siteTextBottom", Array("id" => "site-text-bottom", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//телефон
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-phone" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_MINILANDING_WIZ_TITLE_PHONE").'</label>';
		$this->content .= $this->ShowInputField("text", "sitePhone", Array("id" => "site-phone", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//email
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-email" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">Email</label>';
		$this->content .= $this->ShowInputField("text", "siteEmail", Array("id" => "site-email", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
	}
}

class DataInstallOptions extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("data_install_options");
		$this->SetTitle(GetMessage("MLIFE_MINILANDING_WIZ_STEP4"));
		$this->SetNextStep("data_install");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		
		
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		//демо данные
		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 
		if($firstStep == "Y")
		{
		
			$this->content .= '
			<div class="wizard-input-form-block">
				'.$this->ShowCheckboxField(
							"installDemoData", 
							"Y", 
							(array("id" => "installDemoData", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"))
						).'
				<label for="installDemoData">'.GetMessage("MLIFE_MINILANDING_WIZ_DEMO_TITLE").'</label>
			</div>';
		
		}
		else{
			$this->content .= GetMessage("MLIFE_MINILANDING_WIZ_DEMO_TITLE_OK");
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
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
	function InitStep()
	{
		$this->SetStepID("finish");
		$this->SetNextStep("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextCaption(GetMessage("wiz_go"));  
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();		   
		if ($wizard->GetVar("proactive") == "Y")
			COption::SetOptionString("statistic", "DEFENCE_ON", "Y");
		
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		$siteDir = "/"; 
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"]; 

		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir."/?finish"));

		$this->CreateNewIndex();
		
		COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID);

		$this->content .=
			'<table class="wizard-completion-table">
				<tr>
					<td class="wizard-completion-cell">'
						.GetMessage("FINISH_STEP_CONTENT").
					'</td>
				</tr>
			</table>';
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");
	}
}
?>