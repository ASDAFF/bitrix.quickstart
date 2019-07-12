<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "windows";
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
		$wizard->solutionName = "windows";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_name.php", "/bitrix/wizards/likeit/windows/site/templates/windows/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.png");
		if (!file_exists(WIZARD_SITE_PATH."include/logo.png"))
			$siteLogo = "/bitrix/wizards/likeit/windows/site/templates/windows/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.png";

		$wizard->SetDefaultVars(
			Array(
				"siteLogo" => $siteLogo,
				"siteSlogan" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_slogan.php", GetMessage("WIZ_COMPANY_SLOGAN_DEF")),
				"siteAddress" => $this->GetFileContent(WIZARD_SITE_PATH."include/contacts_address_footer.php", GetMessage("WIZ_COMPANY_COPY_DEF_ADDR")),
				"sitePhone" => $this->GetFileContent(WIZARD_SITE_PATH."include/contacts_phone_footer.php", GetMessage("WIZ_COMPANY_COPY_DEF_PHONE")),
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
		$this->content .= CFile::ShowImage($siteLogo, 209, 61, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"))."</div>";

		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_SLOGAN").'</div>';
		$this->content .= $this->ShowInputField("textarea", "siteSlogan", Array("id" => "site-slogan", "class" => "wizard-field", "rows"=>"3"))."</div>";

		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_COMPANY_COPY").'</div>'.GetMessage("wiz_address").'<br />';
		$this->content .= $this->ShowInputField("textarea", "siteAddress", Array("id" => "site-address", "class" => "wizard-field", "rows"=>"3"))."<br /><br />".GetMessage("wiz_phone")."<br />";
		$this->content .= $this->ShowInputField("text", "sitePhone", Array("id" => "site-phone", "class" => "wizard-field"))."</div>";


		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID"));

		$styleMeta = 'style="display:block"';
		if($firstStep == "Y") $styleMeta = 'style="display:none"';

		$this->content .= '
		<div  id="bx_metadata" '.$styleMeta.'>
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
				(array("id" => "install-demo-data", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}")));
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
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 210, "max_width" => 60, "make_preview" => "Y"));
//        COption::SetOptionString("main", "wizard_site_logo", $res, "", $wizard->GetVar("siteID"));
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