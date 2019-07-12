<?

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
    function InitStep()
    {
        parent::InitStep();

        $wizard =& $this->GetWizard();
        $wizard->solutionName = "landing001";

        $this->SetNextStep("site_settings");
    }

}



class SiteSettingsStep extends CSiteSettingsWizardStep 
{

	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "landing001";
		parent::InitStep(); 

		$this->SetTitle(GetMessage("wiz_settings"));
		$this->SetNextStep("data_install");
		$this->SetNextCaption(GetMessage("wiz_install"));

		$siteID = $wizard->GetVar("siteID");
		
		$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."include/company_logo.php", "/bitrix/wizards/bitrix/demo_community/site/templates/taby/images/logo.jpg");

		$siteLogoImg = "/bitrix/wizards/akropol/landing001/images/ru/logotip.png";
		$siteBgImg = "/bitrix/wizards/akropol/landing001/images/ru/bg03.jpg";

		global $USER;
		$AdminsMail=$USER->GetParam("EMAIL");


		$wizard->SetDefaultVars(
			Array(
				"siteName" => GetMessage("wiz_name"),
				"siteLogoImg" => $siteLogoImg,
				"siteDescription" => GetMessage("wiz_slogan"), 
				"siteSeoTitle" => GetMessage("wiz_name"), 
				"siteSeoDescription" => GetMessage("wiz_slogan"),
				"siteSeoKeywords" => GetMessage("wiz_keywords"),  
				"siteBgImg" => $siteBgImg,  
				"siteCopyright" => GetMessage("wisCopyright"), 
				"sitePhone" => GetMessage("sitePhone"),
				"admins_e_mail" => $AdminsMail,
			)
		);		
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogoImg", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 37, "max_width" => 300, "make_preview" => "Y"));
		$res = $this->SaveFile("siteBgImg", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 900, "max_width" => 1920, "make_preview" => "Y"));
		}
	
	function ShowStep()
	{
		
		$wizard =& $this->GetWizard();
		$siteLogo = $wizard->GetVar("siteLogo", true);
		$siteCopyright = $wizard->GetVar("siteCopyright", true);
		$this->content ='<div style="'.$siteBgImg.'"> </div>';
		$this->content .= '<div class="wizard-input-form">';
		
		
		$siteLogoImg = $wizard->GetVar("siteLogoImg", true);
		
		$this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-catalog-title">'.GetMessage("wiz_company_name").'</div>
			'.$this->ShowInputField('text', 'siteName', array("id" => "siteName", "class" => "wizard-field")).'
		</div>';
		
		$this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("wiz_site_logo_img").'</div>
				'.CFile::ShowImage($siteLogoImg, 300, 37, "border=0 vspace=15").'<br/>
				'.$this->ShowFileField("siteLogoImg", Array("show_file_info" => "N", "id" => "siteLogoImg")).'
		</div>';
		
		$this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-catalog-title">'.GetMessage("wiz_company_description").'</div>
			'.$this->ShowInputField('text', 'siteDescription', array("id" => "siteDescription", "class" => "wizard-field")).'
		</div>';
		
		
		//START Add PHONE
		$this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-catalog-title">'.GetMessage("wiz_company_phone_label").'</div>
			'.$this->ShowInputField('text', 'sitePhone', array("id" => "sitePhone", "class" => "wizard-field")).'
		</div>';
		//END Add PHONE


		//START Add MAIL
		$this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-catalog-title">'.GetMessage("wiz_company_email").'</div>
			'.$this->ShowInputField('text', 'admins_e_mail', array("id" => "admins_e_mail", "class" => "wizard-field")).'
		</div>';
		//END Add MAIL
		
		//START Add UP
		$this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-catalog-title">'.GetMessage("wiz_company_up").'</div>
			'.$this->ShowCheckboxField("upAction", "Y").' '.GetMessage("wiz_company_yes").'
		</div>';
		//END Add UP

		//START Add BG 
		$siteBgImg = $wizard->GetVar("siteBgImg", true);

		$this->content .= '<div class="wizard-upload-img-block"><div class="wizard-catalog-title">'.GetMessage("WIZ_HEADER_IMG").'</div>';
		$this->content .= CFile::ShowImage($siteBgImg, 419, 280, "border=0 vspace=15");
		$this->content .= "<br />".$this->ShowFileField("siteBgImg", Array("show_file_info" => "N", "id" => "siteBgImg")).'</div>';
		//END Add BG
		
			// START SEO BLOCK
			$this->content .= '
			<div class="wizard-metadata-title">'.GetMessage("wiz_seo").'</div>
			<div class="wizard-upload-img-block">
				<label for="siteMetaTitle" class="wizard-input-title">'.GetMessage("wiz_seo_title").'</label>
				'.$this->ShowInputField('text', 'siteSeoTitle', array("id" => "siteSeoTitle", "class" => "wizard-field")).'
			</div><div class="wizard-upload-img-block">
				<label for="siteMetaDescription" class="wizard-input-title">'.GetMessage("wiz_seo_description").'</label>
				'.$this->ShowInputField('text', 'siteSeoDescription', array("id" => "siteSeoDescription", "class" => "wizard-field")).'
			</div><div class="wizard-upload-img-block">
				<label for="siteMetaKeywords" class="wizard-input-title">'.GetMessage("wiz_seo_keywords").'</label>
				'.$this->ShowInputField('text', 'siteSeoKeywords', array("id" => "siteSeoKeywords", "class" => "wizard-field")).'
			</div>';
			// END SEO BLOCK

				
		$wizard->SetVar("siteCopyright", GetMessage("wisCopyright"));

	}

}




























class DataInstallStep extends CDataInstallWizardStep
{

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
        global $USER;
        $wizard =& $this->GetWizard();

        $siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));

        if (strlen($siteID) > 0 and is_object($USER) and method_exists($USER, 'GetEmail')) {
            $obSite = new CSite();
            $t = $obSite->Update($siteID, array(
                'EMAIL' => $USER->GetEmail(),
                'NAME' => GetMessage('wiz_site_name'),
                'SERVER_NAME' => $this->getSiteUrl()
            ));
        };

        $rsSites = CSite::GetByID($siteID);
        $siteDir = SITE_DIR;
        if ($arSite = $rsSites->Fetch())
            $siteDir = $arSite["DIR"];

        $wizard->SetFormActionScript(str_replace("//", "/", $siteDir . "/?finish"));

        $this->CreateNewIndex();

        COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID);

		$this->content .= GetMessage("FINISH_STEP_COMPOSITE");
		$this->content .= '<br/><center><a href="/bitrix/admin/composite.php?lang=ru">'.CFile::ShowImage("/bitrix/wizards/akropol/landing001/images/ru/composite.gif", 600, 250, "border=0 vspace=15").'</a></center>';
		$this->content .= '<center><b><a href="/bitrix/admin/composite.php?lang=ru">'.GetMessage("FINISH_STEP_COMPOSITE_LINK").'</a></b></center>';
		
		$this->content .= '<br/><br/><b>'.GetMessage("FINISH_STEP_CONTENT").'</b>';
		
        if ($wizard->GetVar("installDemoData") == "Y")
            $this->content .= GetMessage("FINISH_STEP_REINDEX");


    }

    function getSiteUrl()
    {
        $PARSE_HOST = parse_url(getenv('HTTP_HOST'));
        if (isset($PARSE_HOST['port']) and $PARSE_HOST['port'] == '80') {
            $HOST = $PARSE_HOST['host'];
        }
        elseif (isset($PARSE_HOST['port']) and $PARSE_HOST['port'] == '443') {
            $HOST = $PARSE_HOST['host'];
        }
        elseif(isset($PARSE_HOST['port'])) {
            $HOST = $PARSE_HOST['host'] . ":" . $PARSE_HOST['port'];
        } else {
            $HOST = $PARSE_HOST['host'];
        }
        return $HOST;
    }
}

?>