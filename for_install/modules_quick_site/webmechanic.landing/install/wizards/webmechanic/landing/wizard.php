<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "webmechanic.landing";
		//$this->SetNextStep("select_theme");
		$this->SetNextStep("select_template");

	}
}

class SelectTemplateStep extends CSelectTemplateWizardStep
{

	function InitStep()
	{
		$this->SetStepID("select_template");
		$this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));
		//$this->SetPrevStep("welcome_step");
		$this->SetNextStep("select_theme");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));

		/*$wizard->SetDefaultVars(
         Array(
            'siteName' => GetMessage('WIZ_SETTINGS_SITE_NAME_DEFAULT'),
            'siteMetaTitle' => GetMessage('WIZ_SETTINGS_TITLE_DEFAULT'),
            'siteMetaDescription' => GetMessage('WIZ_SETTINGS_DESCRIPTION_DEFAULT'),
            'siteMetaKeywords' => GetMessage('WIZ_SETTINGS_KEYWORDS_DEFAULT')  
         )
      	);*/

	}
}

class SelectThemeStep extends CSelectThemeWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_theme");
		$this->SetTitle(GetMessage("SELECT_THEME_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_THEME_SUBTITLE"));
		$this->SetPrevStep("select_template");
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetNextStep("install_data");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$templateID = $wizard->GetVar("templateID");
			$themeVarName = $templateID."_themeID";
			//var_dump($themeVarName);exit();
			$themeID = $wizard->GetVar($themeVarName);

			$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
			//$arThemes = WizardServices::GetThemes($templatesPath."/themes");
			$arThemes = WizardServices::GetThemes($wizard->GetPath()."/site/themes");

			if (!array_key_exists($themeID, $arThemes))
				$this->SetError(GetMessage("wiz_template_color"));
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$templateID = $wizard->GetVar("templateID");

		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		
		//var_dump($templatesPath);

		//$arThemes = WizardServices::GetThemes($templatesPath."/themes");
		$arThemes = WizardServices::GetThemes($wizard->GetPath()."/site/themes");

		//var_dump($wizard->GetPath()."/site/themes");
		//print_r($arThemes);

		if (empty($arThemes))
			return;

		$themeVarName = $templateID."_themeID";
		$ThemeID = $wizard->GetVar($templateID."_themeID");

		if(isset($ThemeID) && array_key_exists($ThemeID, $arThemes)){
			$defaultThemeID = $ThemeID;
			$wizard->SetDefaultVar($themeVarName, $ThemeID);
		} else {
			$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "", $wizard->GetVar("siteID"));

			if (!(strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes)))
			{
				$defaultThemeID = COption::GetOptionString("main", "wizard_".$templateID."_theme_id", "");
				if (strlen($defaultThemeID) > 0 && array_key_exists($defaultThemeID, $arThemes))
					$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
				else
					$defaultThemeID = "";
			}
		}

		$this->content =
			'
			<style type="text/css">
			  .solution-item-wrapper {
			  	text-align: center;
			  	padding-right: 10px;
			  }

			  .solution-item-wrapper .solution-item-selected{
			  	background: #eee;
				color: #000;
				border-radius: 3px;
			  }

			  .solution-item-wrapper a {
			  	display: block;
			  	padding: 5px;
			  }

			  .solution-item-wrapper .solution-inner-item {
			  	margin-bottom: 5px;
			  }
			</style>

			<script type="text/javascript">
				
				function SelectTheme(element, solutionId, imageUrl)
				{
					var container = document.getElementById("solutions-container");
					var anchors = container.getElementsByTagName("A");
					for (var i = 0; i < anchors.length; i++)
					{
						if (anchors[i].parentNode.parentNode.parentNode.parentNode.parentNode != container)
							continue;
						anchors[i].className = "solution-item solution-picture-item";
					}
					element.className = "solution-item  solution-picture-item solution-item-selected";
					var hidden = document.getElementById("selected-solution");
					if (!hidden)
					{
						hidden = document.createElement("INPUT");
						hidden.type = "hidden"
						hidden.id = "selected-solution";
						hidden.name = "selected-solution";
						container.appendChild(hidden);
					}
					hidden.value = solutionId;
				
				}
				</script>'.
				'<div id="html_container">'.
				'<div style="overflow: hidden; margin:0 auto;text-align:center" id="solutions-container">';
				
		$arDefaultTheme = array("blue");
		$arThemesOrder = array("blue", "green", "orange");
		
		$this->content .= '<table><tr>';
		foreach ($arThemesOrder as $themeID)
		{
			$arTheme = $arThemes[$themeID];
			if ($defaultThemeID == "")
			{
				$defaultThemeID = $themeID;
				$wizard->SetDefaultVar($themeVarName, $defaultThemeID);
			}
			if ($defaultThemeID == $themeID)
				$arDefaultTheme = $arTheme;
				
			$this->content .= 
				'<td class="solution-item-wrapper">'.
				'<a ondblclick="SubmitForm(\'next\'); return false;" onclick="SelectTheme(this, \''.$themeID.'\', \''.$arTheme["SCREENSHOT"].'\'); return false;" '.
					'href="javascript:void(0);" class="solution-item solution-picture-item'.($defaultThemeID == $themeID ? " solution-item-selected" : "").'">'.
					'<div class="solution-inner-item">'.
					CFile::ShowImage($arTheme["PREVIEW"], 70, 70, ' border="0" class="solution-image"').
					'</div>'.
					'<div class="solution-description">'.$arTheme["NAME"].'</div>'.
					'</a>'
				.'</td>';	
		}
		$this->content .= '</tr></table>';

		$this->content .= $this->ShowHiddenField($themeVarName, $defaultThemeID, array("id" => "selected-solution"));
		$this->content .=
			'</div>';
	}
}

class DataInstallStep extends CDataInstallWizardStep
{
	function InitStep()
	{
		$this->SetStepID("install_data");
		$this->SetTitle(GetMessage("wiz_install_data"));
		$this->SetSubTitle(GetMessage("wiz_install_data"));
		//$this->SetPrevStep("select_template");
		$this->SetNextStep("finish");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}
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