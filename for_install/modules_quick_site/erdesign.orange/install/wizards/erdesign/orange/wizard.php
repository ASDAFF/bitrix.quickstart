<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "orange";
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
		$wizard->solutionName = "orange";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_name.php", "/bitrix/wizards/erdesign/orange/site/templates/orange/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.gif");
		if (!file_exists(WIZARD_SITE_PATH."include/logo.gif"))
			$siteLogo = "/bitrix/wizards/erdesign/orange/site/templates/orange/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.gif";
			
		$siteBanner = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/banner.php", "/bitrix/wizards/erdesign/orange/site/templates/orange/images/banner.png");
		
		$wizard->SetDefaultVars(
			Array(
				"siteName" => GetMessage("WIZ_NAME_TEXT_DEFAULT"),
				"siteTel" => GetMessage("WIZ_BANNER_TEXT_DEFAULT"),
				"siteHome" => GetMessage("WIZ_NAME_HOME_DEFAULT"),
					"siteOfic" => GetMessage("WIZ_NAME_OFIC_DEFAULT"),
					"siteCountry" => GetMessage("WIZ_NAME_COUNTRY_DEFAULT"),
					"siteSity" => GetMessage("WIZ_NAME_SITY_DEFAULT"),
					"siteIndex" => GetMessage("WIZ_NAME_INDEX_DEFAULT"),
					"siteEmail" => GetMessage("WIZ_NAME_EMAIL_DEFAULT"),
				"siteSlogan" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_slogan.php", GetMessage("WIZ_COMPANY_SLOGAN_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),  
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);

		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_NAME_TEXT").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteName", Array("id" => "site-name", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
		$this->content .= '<tr><td><br /></td></tr>';

		
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_BANNER_TEXT").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteTel", Array("id" => "site-text", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_NAME_HOME").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteHome", Array("id" => "site-adress", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_NAME_OFIC").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteOfic", Array("id" => "site-adress", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_NAME_COUNTRY").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCountry", Array("id" => "site-adress", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_NAME_SITY").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteSity", Array("id" => "site-adress", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_NAME_INDEX").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteIndex", Array("id" => "site-adress", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
		$this->content .= '<tr><td><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-text">'.GetMessage("WIZ_EMAIL_INDEX").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteEmail", Array("id" => "site-adress", "style" => "width:100%", "rows"=>"2"));
		$this->content .= '</tr></td>';
		
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

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
		$res = $this->SaveFile("siteBanner", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 600, "max_width" => 600, "make_preview" => "Y"));
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