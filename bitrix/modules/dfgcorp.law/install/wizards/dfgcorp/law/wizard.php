<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "corp";
        $this->SetStepID("select_site");
        $this->SetTitle(GetMessage("SELECT_SITE"));
        $this->SetSubTitle('');
        $this->SetNextStep("select_template");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$arSites = array(); 
		$arSitesSelect = array(); 
		$db_res = CSite::GetList($by="sort", $order="desc", array("ACTIVE" => "Y"));
		if ($db_res && $res = $db_res->GetNext())
		{
			do 
			{
				$arSites[$res["ID"]] = $res; 
				$arSitesSelect[$res["ID"]] = '['.$res["ID"].'] '.$res["NAME"];
			} while ($res = $db_res->GetNext()); 
		}
		
		$createSite = $wizard->GetVar("createSite"); 
		$createSite = ($createSite == "Y" ? "Y" : "N"); 
		$this->content = 
'<script type="text/javascript">
function SelectCreateSite(element, solutionId)
{
	var container = document.getElementById("solutions-container");
	var nodes = container.childNodes;
	for (var i = 0; i < nodes.length; i++)
	{
		if (!nodes[i].className)
			continue;
		nodes[i].className = "solution-item";
	}
	element.className = "solution-item solution-item-selected";
	var check = document.getElementById("createSite" + solutionId);
	if (check)
		check.checked = true;
}
</script>';
		$this->content .= 
		'<style>
			.option{padding-left:10px;font-weight:bold;}
			.new_site_info{width: 200px;display: block;float: left;}
		</style>';
		
		$this->content .= '<div id="solutions-container">';
			$this->content .= "<div onclick=\"SelectCreateSite(this, 'N');\" ";
				$this->content .= 'class="solution-item'.($createSite != "Y" ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
					$this->content .= $this->ShowRadioField("createSite", "N", (array("id" => "createSiteN", "class" => "solution-radio") + 
						($createSite != "Y" ? array("checked" => "checked") : array()))); 
					$this->content .= '<label class="option" for="createSiteN">'.GetMessage("wiz_site_existing").'</label>'; 
				if (count($arSites) < 2)
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title").' '.implode("", $arSitesSelect).'</p>'; 
				else
				{
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title");
					$this->content .= "<br />". $this->ShowSelectField("siteID", $arSitesSelect)."</p>";
				}
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</div>';
		if (count($arSites) < COption::GetOptionInt("main", "PARAM_MAX_SITES", 100) || COption::GetOptionInt("main", "PARAM_MAX_SITES", 100) <= 0)
		{
			$this->content .= "<div onclick=\"SelectCreateSite(this, 'Y');\" ";
				$this->content .= 'class="solution-item'.($createSite == "Y" ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
					$this->content .= $this->ShowRadioField("createSite", "Y", (array("id" => "createSiteY", "class" => "solution-radio") + 
						($createSite == "Y" ? array("checked" => "checked") : array()))); 
					$this->content .= '<label class="option" for="createSiteY">'.GetMessage("wiz_site_new").'</label>'; 
					$this->content .= '<p>';
						$this->content .= str_replace(
							array(
								"#SITE_ID#", 
								"#SITE_DIR#"), 
							array(
								$this->ShowInputField("text", "siteNewID", array("size" => 2, "maxlength" => 2, "id" => "siteNewID")), 
								$this->ShowInputField("text", "siteFolder", array("id" => "siteFolder"))), 
							GetMessage("wiz_site_new_title")); 
					$this->content .= '</p>'; 
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</div>';
		}
		$this->content .= '</div>';
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
	function InitStep()
    {
        $this->SetStepID("select_template");
        $this->SetTitle(GetMessage("SELECT_TEMPLATE_TITLE"));
        $this->SetSubTitle(GetMessage("SELECT_TEMPLATE_SUBTITLE"));
        if (!defined("WIZARD_DEFAULT_SITE_ID"))
        {
            $this->SetPrevStep("select_site");
            $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
        }
        else
        {
            $wizard =& $this->GetWizard(); 
            $wizard->SetVar("siteID", WIZARD_DEFAULT_SITE_ID); 
        }

        $this->SetNextStep("select_theme");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
    }

    function OnPostForm()
    {
        $wizard =& $this->GetWizard();

        if ($wizard->IsNextButtonClick())
        {
            $templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
            $arTemplates = WizardServices::GetTemplates($templatesPath);

            $templateID = $wizard->GetVar("templateID");

            if (!array_key_exists($templateID, $arTemplates))
                $this->SetError(GetMessage("wiz_template"));
        }
    }

    function ShowStep()
    {
        $wizard =& $this->GetWizard();

        $templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
        $arTemplates = WizardServices::GetTemplates($templatesPath);

        if (empty($arTemplates))
            return;

        $templateID = $wizard->GetVar("templateID");
        if(isset($templateID) && array_key_exists($templateID, $arTemplates)){
        
            $defaultTemplateID = $templateID;
            $wizard->SetDefaultVar("templateID", $templateID);
            
        } else {
        
            $defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "", $wizard->GetVar("siteID")); 
            if (!(strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates)))
            {
                if (strlen($defaultTemplateID) > 0 && array_key_exists($defaultTemplateID, $arTemplates))
                    $wizard->SetDefaultVar("templateID", $defaultTemplateID);
                else
                    $defaultTemplateID = "";
            }
        }

        global $SHOWIMAGEFIRST;
        $SHOWIMAGEFIRST = true;
        
        $this->content .= '<div id="solutions-container" class="inst-template-list-block">';
        foreach ($arTemplates as $templateID => $arTemplate)
        {
            if ($defaultTemplateID == "")
            {
                $defaultTemplateID = $templateID;
                $wizard->SetDefaultVar("templateID", $defaultTemplateID);
            }

            $this->content .= '<div class="inst-template-description">';
            $this->content .= $this->ShowRadioField("templateID", $templateID, Array("id" => $templateID, "class" => "inst-template-list-inp"));
            if ($arTemplate["SCREENSHOT"] && $arTemplate["PREVIEW"])
                $this->content .= CFile::Show2Images($arTemplate["PREVIEW"], $arTemplate["SCREENSHOT"], 150, 150, ' class="inst-template-list-img"');
            else
                $this->content .= CFile::ShowImage($arTemplate["SCREENSHOT"], 150, 150, ' class="inst-template-list-img"', "", true);

            $this->content .= '<label for="'.$templateID.'" class="inst-template-list-label">'.$arTemplate["NAME"].'<p style="font-size: 14px;font-weight: normal;font-style: italic;">'.$arTemplate["DESCRIPTION"].'</p></label>';
            $this->content .= "</div>";

        }
        
        $this->content .= '</div>'; 
    }
}

class SelectThemeStep extends CSelectThemeWizardStep
{

}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "corp_services";
		$this->SetTitle(GetMessage("SITE_SETTIGN"));

		$this->SetStepID("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetNextStep("data_install");
		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");
		$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_name.php", "/bitrix/wizards/bitrix/corp_services/site/templates/corp_services/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.gif");
		if (!file_exists(WIZARD_SITE_PATH."include/logo.gif"))
			$siteLogo = "/bitrix/wizards/bitrix/corp_services/site/templates/corp_services/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.gif";
			
		$siteBanner = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/banner.php", "/bitrix/wizards/bitrix/corp_services/site/templates/corp_services/images/banner.png");
		
		$wizard->SetDefaultVars(
			Array(
				"siteLogo" => $siteLogo,
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"sitePhones" => GetMessage("wiz_site_phones"),
				"siteEmail" => GetMessage("wiz_site_email"),
				"siteAddress" => GetMessage("wiz_site_address"),
				"siteWorkTime" => GetMessage("wiz_site_worktime"),
				"siteServices" => GetMessage("wiz_site_services"),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),  
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);

		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_LOGO").'</div>';
		$this->content .= CFile::ShowImage($siteLogo, 190, 70, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo")).'</div>';

		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_PHONES").'</div>';
		$this->content .= $this->ShowInputField("textarea", "sitePhones", Array("id" => "site-phones", "class" => "wizard-field", "rows"=>"3")).'</div>';
		
		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_EMAIL").'</div>';
		$this->content .= $this->ShowInputField("textarea", "siteEmail", Array("id" => "site-email", "class" => "wizard-field", "rows"=>"3")).'</div>';
		
		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_ADDRESS").'</div>';
		$this->content .= $this->ShowInputField("textarea", "siteAddress", Array("id" => "site-address", "class" => "wizard-field", "rows"=>"3")).'</div>';
		
		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_WORKTIME").'</div>';
		$this->content .= $this->ShowInputField("textarea", "siteWorkTime", Array("id" => "site-worktime", "class" => "wizard-field", "rows"=>"3")).'</div>';
		
		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_SERVICES").'</div>';
		$this->content .= $this->ShowInputField("textarea", "siteServices", Array("id" => "site-service-list", "class" => "wizard-field", "rows"=>"8")).'</div>';
		
		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_COPY").'</div>';
		$this->content .= $this->ShowInputField("textarea", "siteCopy", Array("id" => "site-copy", "class" => "wizard-field", "rows"=>"3")).'</div>';

		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 

		$styleMeta = 'style="display:block"';
		if($firstStep == "Y") $styleMeta = 'style="display:block"';

		$this->content .= '
		<div id="bx_metadata" '.$styleMeta.'>
			<div class="wizard-input-form-block">
				<div class="wizard-metadata-title">'.GetMessage("wiz_meta_data").'</div>
				<div class="wizard-upload-img-block">
					<label for="siteMetaDescription" class="wizard-input-title">'.GetMessage("wiz_meta_description").'</label>
					'.$this->ShowInputField("textarea", "siteMetaDescription", Array("id" => "siteMetaDescription", "class" => "wizard-field", "rows"=>"3")).'
				</div>';
			$this->content .= '
				<div class="wizard-upload-img-block">
					<label for="siteMetaKeywords" class="wizard-input-title">'.GetMessage("wiz_meta_keywords").'</label><br>
					'.$this->ShowInputField('text', 'siteMetaKeywords', array("id" => "siteMetaKeywords", "class" => "wizard-field")).'
				</div>
			</div>
		</div>';
		
		if($firstStep == "Y")
		{

			$this->content .= $this->ShowCheckboxField("installDemoData", "Y", 
				(array("checked"=>"checked", "id" => "install-demo-data", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}")));
			$this->content .= '<label for="install-demo-data">'.GetMessage("wiz_structure_data").'</label><br />';

		}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
		}

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 71, "max_width" => 244, "make_preview" => "Y"));
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