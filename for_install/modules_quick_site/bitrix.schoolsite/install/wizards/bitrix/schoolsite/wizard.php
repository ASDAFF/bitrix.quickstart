<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
    function InitStep()
    {
        parent::InitStep();

        $wizard =& $this->GetWizard();
        $wizard->solutionName = "schoolsite";
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
        $this->SetStepID("site_settings");
        $this->SetTitle(GetMessage("wiz_settings"));
        $this->SetSubTitle(GetMessage("wiz_settings"));
        $this->SetNextStep("data_install");
        $this->SetPrevStep("select_theme");
        $this->SetNextCaption(GetMessage("wiz_install"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

        $wizard =& $this->GetWizard();
        $wizard->SetDefaultVars(
            Array(
                "siteName" => COption::GetOptionString("main", "site_name", GetMessage("wiz_slogan")),
                "siteLogo" => COption::GetOptionString("main", "wizard_site_logo", ""),
                "siteBackground" => COption::GetOptionString("main", "wizard_site_background", ""),
                "schoolAddress" => COption::GetOptionString("main", "wizard_school_address", GetMessage("wiz_school_address_def")),
                "schoolPhone" => COption::GetOptionString("main", "wizard_school_phone", GetMessage("wiz_school_phone_def")),
                "schoolEmail" => COption::GetOptionString("main", "wizard_school_email", GetMessage("wiz_school_email_def")),
                "siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),  
            )
        );
    }

    function OnPostForm()
    {
        $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 110, "max_width" => 170, "make_preview" => "Y"));
        COption::SetOptionString("main", "wizard_site_logo", "");
        $this->SaveFile("siteBackground", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 200, "max_width" => 975, "make_preview" => "Y"));
        COption::SetOptionString("main", "wizard_site_background", "");
        
        $wizard =& $this->GetWizard();
        if ($wizard->IsNextButtonClick())
        {
            COption::SetOptionString("main", "site_name", str_replace(Array("<"), Array("&lt;"), $wizard->GetVar("siteName")));

           // COption::SetOptionString("main", "wizard_demo_data", "N");
        }
    }

    function ShowStep()
    {
        $wizard =& $this->GetWizard();
        $templateID = $wizard->GetVar("templateID");

        $this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';

        $this->content .= '<tr><td>';
        $this->content .= '<label for="site-name">'.GetMessage("wiz_company_name").'</label><br />';
        $this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-name", "style" => "width:90%"));
        $this->content .= '</tr></td>';
  
        $this->content .= '<tr><td><br /></td></tr>';
  
        $this->content .= '<tr><td>';
        $this->content .= '<label for="school-address">'.GetMessage("wiz_school_address").'</label><br />';
        $this->content .= $this->ShowInputField("text", "schoolAddress", Array("id" => "school-address", "style" => "width:90%"));
        $this->content .= '</tr></td>';
  
        $this->content .= '<tr><td><br /></td></tr>';
  
        $this->content .= '<tr><td>';
        $this->content .= '<label for="school-phone">'.GetMessage("wiz_school_phone").'</label><br />';
        $this->content .= $this->ShowInputField("text", "schoolPhone", Array("id" => "school-phone", "style" => "width:90%"));
        $this->content .= '</tr></td>';

        $this->content .= '<tr><td><br /></td></tr>';
  
        $this->content .= '<tr><td>';
        $this->content .= '<label for="school-email">'.GetMessage("wiz_school_email").'</label><br />';
        $this->content .= $this->ShowInputField("text", "schoolEmail", Array("id" => "school-email", "style" => "width:90%"));
        $this->content .= '</tr></td>';

        $this->content .= '<tr><td><br /></td></tr>';

        $fileID = COption::GetOptionString("main", "wizard_site_logo", "");
        if (intval($fileID) > 0)
            $wizard->SetVar("siteLogo", $fileID);
        $siteLogo = $wizard->GetVar("siteLogo");

        $this->content .= '<tr><td>';
        $logo_label = GetMessage("wiz_company_logo");
        if($templateID == "school_modern")
            $logo_label .= " (170x110)";
        elseif($templateID == "school_light")
            $logo_label .= " (135x100)";
        elseif($templateID == "school_urban")
       $logo_label .= " (135x100)";
        $this->content .= '<label for="site-logo">'.$logo_label.'</label><br />';
        $this->content .= $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));
        $this->content .= "<br />".CFile::ShowImage($siteLogo, 200, 200, "border=0", "", true);
        $this->content .= '</td></tr>';

        $this->content .= '<tr><td>&nbsp;</td></tr>';
  
        $fileID = COption::GetOptionString("main", "wizard_site_background", "");
        if (intval($fileID) > 0)
            $wizard->SetVar("siteBackground", $fileID);
        $siteBackground = $wizard->GetVar("siteBackground");
  
        if($templateID == "school_modern")
          {
           $this->content .= '<tr><td>';
                 $this->content .= '<label for="site-background">'.GetMessage("wiz_site_background").'</label><br />';
                 $this->content .= $this->ShowFileField("siteBackground", Array("show_file_info" => "N", "id" => "site-background"));
                 $this->content .= "<br />".CFile::ShowImage($siteBackground, 200, 200, "border=0", "", true);
                 $this->content .= '</td></tr>';

                 $this->content .= '<tr><td>&nbsp;</td></tr>';
          }

        //$this->content .= '</td></tr>';
        $this->content .= '<tr><td><br /></td></tr>';

		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 

		$styleMeta = 'style="display:block"';
		if($firstStep == "Y") $styleMeta = 'style="display:none"';
		
		$this->content .= '<tr><td><br /></td></tr>';
		$this->content .= '<tr><td>
		<div  id="bx_metadata" '.$styleMeta.'>
			<div class="wizard-input-form-block">
				<h4 style="margin-top:0"><label for="siteMetaDescription">'.GetMessage("wiz_meta_data").'</label></h4>
				<label for="siteMetaDescription">'.GetMessage("wiz_meta_description").'</label>
				<div class="wizard-input-form-block-content" style="margin-top:7px;">
					<div class="wizard-input-form-field wizard-input-form-field-textarea">'.
						$this->ShowInputField("textarea", "siteMetaDescription", Array("id" => "siteMetaDescription", "style" => "width:100%", "rows"=>"3")).'</div>
				</div>
			</div>';
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteMetaKeywords">'.GetMessage("wiz_meta_keywords").'</label><br>
				<div class="wizard-input-form-block-content" style="margin-top:7px;">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.
						$this->ShowInputField('text', 'siteMetaKeywords', array("id" => "siteMetaKeywords", "style" => "background-color:#fff;width:100% !important")).'</div>
				</div>
			</div>
		</div>';
		
		if($firstStep == "Y")
		{
			$this->content .= '<tr><td style="padding-bottom:3px;">';
			$this->content .= $this->ShowCheckboxField("installDemoData", "Y", 
				(array("id" => "install-demo-data", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}")));
			$this->content .= '<label for="install-demo-data">'.GetMessage("wiz_structure_data").'</label><br />';
			$this->content .= '</td></tr>';
			
			$this->content .= '<tr><td>&nbsp;</td></tr>';
		}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
		}
        $this->content .= '</table>';


        $formName = $wizard->GetFormName();
        $installCaption = $this->GetNextCaption();
        $nextCaption = GetMessage("NEXT_BUTTON");

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
