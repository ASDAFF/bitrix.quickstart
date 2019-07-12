<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class StartStep extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("hello_start");
        $this->SetTitle(GetMessage("START_STEP_TITLE"));
        $this->SetSubTitle(GetMessage("START_STEP_SUBTITLE"));
        $this->SetNextStep("license_agreement");
        //$this->SetNextCaption(GetMessage("LICENSE_AGREE"));
    }

    function ShowStep()
    {
        $text = GetMessage('HELLO_START');
        $this->content = <<<HTML
<div>{$text}</div>
HTML;
;
    }
}

class LicenseStep extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("license_agreement");
        $this->SetTitle(GetMessage("LICENSE_STEP_TITLE"));
        $this->SetSubTitle(GetMessage("LICENSE_STEP_SUBTITLE"));
        $this->SetNextStep(defined("WIZARD_DEFAULT_SITE_ID") ?  "select_template":"select_site");
        $this->SetNextCaption(GetMessage("LICENSE_AGREE"));
        $this->SetPrevStep("hello_start");
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
    }

    function ShowStep()
    {
        $licenseFile = $_SERVER['DOCUMENT_ROOT']."/bitrix/wizards/webdoka/smartrealt/lang/ru/license.html";
        $agreement = file_get_contents($licenseFile);
//        $agreement = GetMessage('LICENSE_AGREEMENT').WIZARD_RELATIVE_PATH;
        $this->content = <<<HTML
<div class="license">{$agreement}</div>
HTML;
;
    }
}

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "smartrealt";

        $this->SetPrevStep("license_agreement");
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
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
		$wizard->solutionName = "smartrealt";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

        $templatePath = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$templateID."/";
        $wizardTemplatePath = "/bitrix/wizards/webdoka/smartrealt/site/templates/".$templateID;

        $siteLogo = $this->GetFileContentImgSrc($templatePath."include/logo.php", $wizardTemplatePath."/themes/".$themeID."/images/logo.png");
        if (!file_exists($templatePath."include/logo.gif"))
            $siteLogo = $wizardTemplatePath."/themes/".$themeID."/images/logo.png";

        $siteImage = $this->GetFileContentImgSrc($templatePath."include/site_image.php", $wizardTemplatePath."/themes/".$themeID."/images/im1.png");
        /*if (!file_exists($templatePath."include/logo.gif"))
            $siteImage = $wizardTemplatePath."/themes/".$themeID."/images/im1.png";*/

        // проверим существование таблиц с объектами
        $tableExist = 'N';
        global $DB;
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = '".$DB->DBName."' and table_name = 'smartrealt_catalog_element' LIMIT 1";
        $rs = $DB->Query($sql);
        if ($rs->SelectedRowsCount() == 1)
        {
            $sql = "SELECT count(*) as cnt from smartrealt_catalog_element";
            $rs = $DB->Query($sql);
            if ($ar = $rs->Fetch())
            {
                if ($ar['cnt'] > 0)
                {
                    $tableExist = 'Y';
                }
            }
        }
        /*$sql = "SHOW TABLES";
        $rs = $DB->Query($sql);
        while ($ar = $rs->Fetch())
        {
            $tbl = array_pop($ar);
            if ($tbl == 'smartrealt_catalog_element')
            {
                $tableExist = 'Y';
            }
        }*/

		$wizard->SetDefaultVars(
			Array(
                "phoneCode" => GetMessage('defaul_phone_code'),
                "phoneNumber" => GetMessage('defaul_phone_number'),
                "siteLogo" => $siteLogo,
                "siteImage" => $siteImage,
                "siteCopy" => GetMessage('default_site_copy'),
                "tableExist" => $tableExist,
				/*"siteLogo" => $siteLogo,
				"siteSlogan" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_slogan.php", GetMessage("WIZ_COMPANY_SLOGAN_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"), */
			)
		);	
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
        $siteLogo = $wizard->GetVar("siteLogo", true);
        $siteLogoShow = CFile::ShowImage($siteLogo, 170, 60, "border=0 vspace=15");
        $siteImage = $wizard->GetVar("siteImage", true);
        $siteImageShow = CFile::ShowImage($siteImage, 320, 200, "border=0 vspace=15");

        $tableExist = $wizard->GetVar("tableExist", 'N');

        $licenseMess = GetMessage('wiz_license');
        $noLicenseMess = GetMessage('wiz_no_license');
        $getLicenseMess = GetMessage('wiz_get_license');
        $installNewsMess = GetMessage('wiz_install_news');
        $installTextPagesMess = GetMessage('wiz_install_text_pages');
        $installDataMess = GetMessage('wiz_install_demo_data');
        $registrationMess = GetMessage('register_to_license');
        $phoneMess = GetMessage('your_phone');
        $siteCopyMess = GetMessage('your_site_copy');
        $logoMess = GetMessage('your_logo');
        $siteImageMess = GetMessage('your_site_image');

        $this->content = <<<HTML
            <table width="100%" cellspacing="10">
                <tr>
                    <td style="width:200px;"><b>{$licenseMess}:</b></td>
                    <td>
                    {$this->ShowInputField("text", "license_key", Array("id" => "license_key", "style" => "width:100%"))}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>{$noLicenseMess} {$getLicenseMess}</td>
                </tr>
                <tr>
                    <td>{$installTextPagesMess}</td>
                    <td>{$this->ShowCheckboxField("install_pages", "Y", Array("id" => "install_pages", "checked"=>"checked"))}</td>
                </tr>
                <tr>
                    <td>{$installDataMess}</td>
                    <td>{$this->ShowCheckboxField("installDemoData", "Y", Array("id" => "installDemoData", "checked"=>"checked"))}</td>
                </tr>
                <tr>
                    <td>{$phoneMess}</td>
                    <td>
                        {$this->ShowInputField("text", "phoneCode", Array("id" => "phoneCode", "style"=>"width: 50px;"))}
                        {$this->ShowInputField("text", "phoneNumber", Array("id" => "phoneNumber", "style"=>"width: 200px;"))}
                    </td>
                </tr>
                <tr>
                    <td>{$siteCopyMess}</td>
                    <td>
                        {$this->ShowInputField("textarea", "siteCopy", Array("id" => "siteCopy", "style" => "width:100%", "rows"=>"3"))}
                    </td>
                </tr>
                <tr>
                    <td>{$logoMess}</td>
                    <td>
                        {$siteLogoShow}
                        <br>
                        {$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"))}
                    </td>
                </tr>
                <tr>
                    <td>{$siteImageMess}</td>
                    <td>
                        {$siteImageShow}
                        <br>
                        {$this->ShowFileField("siteImage", Array("show_file_info" => "N", "id" => "site-image"))}
                    </td>
                </tr>
            </table>

            {$this->ShowHiddenField("install_news","Y")}
            {$this->ShowHiddenField("tableExist", $tableExist)}

            <div id="regForm" style="display:none;">
            <a class="close" href="#">&times;</a>
            <h2>{$registrationMess}</h2>
            <iframe src="http://soap.smartrealt.com/registration/" frameborder="0"></iframe>
            </div>
HTML;


		/*$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-logo">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br />';
		$this->content .= CFile::ShowImage($siteLogo, 209, 61, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /><br /><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-slogan">'.GetMessage("WIZ_COMPANY_SLOGAN").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteSlogan", Array("id" => "site-slogan", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';

		$this->content .= '<tr><td><br /></td></tr>';
		
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-copy">'.GetMessage("WIZ_COMPANY_COPY").'</label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCopy", Array("id" => "site-copy", "style" => "width:100%", "rows"=>"3"));
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
					<div class="wizard-input-form-field wizard-input-form-field-text">'.
						$this->ShowInputField("textarea", "siteMetaDescription", Array("id" => "siteMetaDescription", "style" => "width:100%", "rows"=>"3")).'</div>
				</div>
			</div>';
			$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteMetaKeywords">'.GetMessage("wiz_meta_keywords").'</label><br>
				<div class="wizard-input-form-block-content" style="margin-top:7px;">
					<div class="wizard-input-form-field wizard-input-form-field-textarea">'.
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

		$this->content .= '</table>';*/

		/*$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");*/
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

        $key = $wizard->GetVar("license_key");
        $bError = false;
        /*if (strlen($key)!==36)
        {
            $bError = true;
            $this->SetError(GetMessage('EMPTY_LICENSE_KEY_ERROR'), "license_key");
        }*/

        if (!$bError)
        {
            $res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 60, "max_width" => 170, "make_preview" => "Y"));
            $res = $this->SaveFile("siteImage", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 200, "max_width" => 320, "make_preview" => "Y"));
    //		COption::SetOptionString("main", "wizard_site_logo", $res, "", $wizard->GetVar("siteID"));
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

        $this->content .= '<p>'.GetMessage("DOWNLOAD_SMARTREALT_EXE").'</p>';
        $this->content .= '<p><a href="http://soft.smartrealt.com/setup.exe" target="blank"><b>'.GetMessage("DOWNLOAD_SMARTREALT_URL").'<b></a></p>';

        if ($wizard->GetVar("installDemoData") == "Y")
            $this->content .= GetMessage("FINISH_STEP_REINDEX");

    }
}
?>