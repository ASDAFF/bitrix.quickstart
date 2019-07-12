<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class UpdateTest
{
    /**
     * главное, чтобы метод, из которого вызывается GetCurrentModules() был объявлен как static
     * @return null|string
     */
    static function getUpdateList()
    {
        $count = 0;
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");
        if(class_exists("CUpdateClient") && CModule::IncludeModule("iblock"))
        {
            $errors = null;
            //$installedModules = CUpdateClient::GetCurrentModules($errors);
            $stableVersionsOnly = COption::GetOptionString('main', 'stable_versions_only', 'Y');
            $updateList = CUpdateClient::GetUpdatesList($errors, LANG, $stableVersionsOnly);

            if($updateList["CLIENT"][0]["@"]["LICENSE"] == GetMessage("V1RT_PERSONAL_FIRST_SITE"))
            {
                $res = CIBlock::GetList(array(), array(), false);
                while($ar_res = $res->Fetch())
                    $count++;
            }
        }
        if($count)
            return '<p><strong>'.GetMessage("V1RT_PERSONAL_WARNING").'</strong> '.GetMessage('V1RT_PERSONAL_EXISTS_IBLOCK', array("#COUNT#" => $count)).'</p>';
        else
            return null;
    }
}

class StartStep extends CWizardStep
{
    function InitStep()
    {
        parent::InitStep();
        $this->SetStepID("hello_start");
        $this->SetTitle(GetMessage("START_STEP_TITLE"));
        $this->SetSubTitle(GetMessage("START_STEP_SUBTITLE"));
        $this->SetNextStep(defined("WIZARD_DEFAULT_SITE_ID") ?  "select_template" : "select_site");
    }

    function ShowStep()
    {
        $this->content = '<p>'.GetMessage('HELLO_START').'</p>';
        /**
         * Посмотрим, нужно ли запускать исправление ошибок
         */
        $arErrors = 0;
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/v1rt.personal/files/"))
            $arErrors++;
        
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/v1rt.personal/install/bitrix/"))
            $arErrors++;
        
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/v1rt.personal/install/classes/"))
            $arErrors++;
        
        $tpl = array();
        $defaultTplV1 = array("personal_green", "personal_blue");
        $defaultTplV2 = array("personal_v2_black", "personal_v2_green", "personal_v2_yellow");
        $sites = CSite::GetList();
        while($site = $sites->Fetch())
        {
            $rsTemplates = CSite::GetTemplateList($site["LID"]);
            while($arTemplate = $rsTemplates->Fetch())
               $tpl[] = $arTemplate["TEMPLATE"];
        }
        if(count($tpl))
        {
            $tpl = array_unique($tpl);
            foreach($tpl as $nameTemplateSite)
            {
                if(array_search(substr($nameTemplateSite, 0, 17), $defaultTplV2) !== false || array_search(substr($nameTemplateSite, 0, 18), $defaultTplV2) !== false)
                {
                    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$nameTemplateSite."/"))
                    {
                        $arTpl[$nameTemplateSite] = "Y";
                        $arErrors++;
                    }
                }
                if(array_search(substr($nameTemplateSite, 0, 13), $defaultTplV1) !== false || array_search(substr($nameTemplateSite, 0, 14), $defaultTplV1) !== false)
                {
                    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$nameTemplateSite."/"))
                    {
                        $arTpl[$nameTemplateSite] = "Y";
                        $arErrors++;
                    }
                }
            }
        }
        
        foreach($defaultTplV1 as $t)
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$t."/"))
            {
                if($arTpl[$t] != "Y")
                    $arErrors++;
            }
        }
        unset($t);
        foreach($defaultTplV2 as $t)
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$t."/"))
            {
                if($arTpl[$t] != "Y")
                    $arErrors++;
            }
        }
        //Сообщим о том, что надо бы сделать бекап сайта и запустить мастер исправления ошибок
        if($arErrors > 0)
        {
            $this->content .= '<p>'.GetMessage('MASTER_CORRECTION').'</p>';
        }
        /**
         * Проверка редакции и наличия свободного места для ИБ
         * @since 2.1.6
         */
        /*
        $count = 0;
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");
        if(class_exists("CUpdateClient") && CModule::IncludeModule("iblock"))
        {
            $errorMessage = "";
            $stableVersionsOnly = COption::GetOptionString("main", "stable_versions_only", "Y");
            $a = new CUpdateClient();
            $arUpdateList = $a->GetUpdatesList($errorMessage);
            
            if($arUpdateList["CLIENT"][0]["@"]["LICENSE"] == GetMessage("V1RT_PERSONAL_FIRST_SITE"))
            {
                $res = CIBlock::GetList(array(), array(), false);
                while($ar_res = $res->Fetch())
                    $count++;
            }
        }
        if($count)
            $this->content .= '<p><strong>'.GetMessage("V1RT_PERSONAL_WARNING").'</strong> '.GetMessage('V1RT_PERSONAL_EXISTS_IBLOCK', array("#COUNT#" => $count)).'</p>';
        */
        $t = new UpdateTest();
        $str = $t->getUpdateList();
        if (!is_null($str)) {
            $this->content .= $str;
        }
    }
}

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "personal";
        
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
		$wizard->solutionName = "personal";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");
        $templatePath = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$templateID."/";
        $wizardTemplatePath = "/bitrix/wizards/v1rt/personal/site/templates/".$templateID;
        if($templateID == "personal_v2")
            $siteLogo = "/bitrix/wizards/v1rt/personal/site/templates/personal_v2/themes/".$themeID."/images/logo.png";
        else
            $siteLogo = "/bitrix/wizards/v1rt/personal/site/templates/personal/images/logo.png";
		
		$wizard->SetDefaultVars(
			Array(
				"siteLogo" => $siteLogo,
				"siteCopyrightText" => $this->GetFileContent(WIZARD_TEMPLATE_ABSOLUTE_PATH."include_areas/inc.copyright.php", GetMessage("WIZ_COPYRIGHT_DEF")),
                "siteContactText" => $this->GetFileContent(WIZARD_TEMPLATE_ABSOLUTE_PATH."include_areas/inc.contact.php", GetMessage("WIZ_CONTACT_DEF")),
                "siteTwitterText" => GetMessage("WIZ_TWITTER_DEF"),
			)
		);
	}

	function ShowStep()
	{
        $wizard =& $this->GetWizard();
        $templateID = $wizard->GetVar("templateID");
        $themeID = $wizard->GetVar($templateID."_themeID");
        $templatePath = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$templateID."/";
        $wizardTemplatePath = "/bitrix/wizards/v1rt/personal/site/templates/".$templateID;

	    $arSelectBlogSection = array(
            GetMessage("WIZ_HEADER_1"),
            GetMessage("WIZ_HEADER_2"),
            GetMessage("WIZ_HEADER_3"),
        );
        
        $arSelectTypeHeader = array(
            GetMessage("WIZ_TYPE_HEADER_1"),
            GetMessage("WIZ_TYPE_HEADER_2"),
            GetMessage("WIZ_TYPE_HEADER_3"),
        );
        
		$wizard =& $this->GetWizard();
        
        $siteLogo = $wizard->GetVar("siteLogo", true);
        if($templateID == "personal_v2")
            $siteLogoShow = CFile::ShowImage($siteLogo, 303, 40);
        else
            $siteLogoShow = CFile::ShowImage($siteLogo, 240, 64);

		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
        $this->content .= '<tr><td><h2>'.GetMessage("SETTINGS_MAIN").'</h2></tr></td>';
        $this->content .= '<tr><td>';
        $this->content .= '<strong>'.GetMessage("WIZ_LOGO").'</strong><br />';
        $this->content .= $siteLogoShow."<br/>";
        $this->content .= $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "site-logo"));
        if($wizard->GetVar("templateID") == "personal_v2")
            $this->content .= '<p style="margin-top:0!important;">'.GetMessage("WIZ_LOGO_DESC_V2").'</p>';
        else
            $this->content .= '<p style="margin-top:0!important;">'.GetMessage("WIZ_LOGO_DESC").'</p>';
        $this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td>';
		$this->content .= '<strong>'.GetMessage("WIZ_REWRITE_INFORMATION").'</strong><br />';
		$this->content .= $this->ShowCheckboxField("rewriteIBlock", "Y", array("id" => "rewriteIBlock", "checked" => "checked")).'<lable for="rewriteIBlock">'.GetMessage("WIZ_REWRITE_INFORMATION_IBLOCK").'</label><br/>';
        $this->content .= $this->ShowCheckboxField("rewriteMedia", "Y", array("id" => "rewriteMedia", "checked" => "checked")).'<lable for="rewriteMedia">'.GetMessage("WIZ_REWRITE_INFORMATION_MEDIA").'</label><br/><br/>';
		$this->content .= GetMessage("WIZ_REWRITE_INFORMATION_DESC").'</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';

        if(CModule::IncludeModule("search"))
        {
            $this->content .= '<tr><td>';
            $this->content .= '<label for="reIndex"><strong>'.GetMessage("V1RT_PERSONAL_REINDEX").'</strong></label><br />';
            $this->content .= $this->ShowCheckboxField("reIndex", "Y", array("id" => "reIndex", "checked" => "checked"));
            $this->content .= '</tr></td>';
            $this->content .= '<tr><td><br /></td></tr>';
        }
        
        //Header site
        if($wizard->GetVar("templateID") != "personal_v2")
        {
            $this->content .= '<tr><td>';
    		$this->content .= '<label for="TypeHeader"><strong>'.GetMessage("WIZ_TYPE_HEADER").'</strong></label><br />';
    		$this->content .= $this->ShowSelectField("siteTypeHeaderText", $arSelectTypeHeader, Array("id" => "TypeHeader", "style" => "width:100%"));
    		$this->content .= '</tr></td>';
    		$this->content .= '<tr><td><br /></td></tr>';
        }
        else
        {
            $this->content .= CWizardStep::ShowHiddenField("siteTypeHeaderText", 0);
        }
        
        //Blog name
        $this->content .= '<tr><td>';
		$this->content .= '<label for="blogSection"><strong>'.GetMessage("WIZ_HEADER").'</strong></label><br />';
		$this->content .= $this->ShowSelectField("siteBlogSectionText", $arSelectBlogSection, Array("id" => "blogSection", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td><h2>'.GetMessage("SETTINGS_FOOTER").'</h2></tr></td>';
        
        //Copyright
		$this->content .= '<tr><td>';
		$this->content .= '<label for="copytext"><strong>'.GetMessage("WIZ_COPYRIGHT").'</strong></label><br />';
		$this->content .= $this->ShowInputField("textarea", "siteCopyrightText", Array("id" => "copytext", "style" => "width:100%", "rows"=>"3"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td><h2>'.GetMessage("SETTINGS_CONTACTS").'</h2></tr></td>';
        
        //Phone
        $this->content .= '<tr><td>';
		$this->content .= '<label for="phone"><strong>'.GetMessage("WIZ_PHONE").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "sitePhoneText", Array("id" => "phone", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        //Email
        $this->content .= '<tr><td>';
		$this->content .= '<label for="email"><strong>'.GetMessage("WIZ_EMAIL").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "siteEmailText", Array("id" => "email", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        //VK page
        $this->content .= '<tr><td>';
		$this->content .= '<label for="vkpage"><strong>'.GetMessage("WIZ_VKPAGE").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "siteVKText", Array("id" => "vkpage", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        //FB page
        $this->content .= '<tr><td>';
		$this->content .= '<label for="fbpage"><strong>'.GetMessage("WIZ_FBPAGE").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "siteFBText", Array("id" => "fbpage", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td><h2>'.GetMessage("SETTINGS_TWITTER").'</h2></tr></td>';
        
        //Twitter login
        $this->content .= '<tr><td>';
		$this->content .= '<label for="twitter"><strong>'.GetMessage("WIZ_TWITTER").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "siteTwitterText", Array("id" => "twitter", "style" => "width:100%"));
        $this->content .= '<p style="margin-top:0!important;">'.GetMessage("WIZ_TWITTER_DESC").'</p>';
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td>';
		$this->content .= '<label for="v1rt_personal_twitter_consumer_key"><strong>'.GetMessage("V1RT_PERSONAL_TWITTER_CONSUMER_KEY").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "v1rt_personal_twitter_consumer_key", Array("id" => "v1rt_personal_twitter_consumer_key", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td>';
		$this->content .= '<label for="v1rt_personal_twitter_consumer_secret"><strong>'.GetMessage("V1RT_PERSONAL_TWITTER_CONSUMER_SECRET").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "v1rt_personal_twitter_consumer_secret", Array("id" => "v1rt_personal_twitter_consumer_secret", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td>';
		$this->content .= '<label for="v1rt_personal_twitter_user_token"><strong>'.GetMessage("V1RT_PERSONAL_TWITTER_USER_TOKEN").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "v1rt_personal_twitter_user_token", Array("id" => "v1rt_personal_twitter_user_token", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';
        
        $this->content .= '<tr><td>';
		$this->content .= '<label for="v1rt_personal_twitter_user_secret"><strong>'.GetMessage("V1RT_PERSONAL_TWITTER_USER_SECRET").'</strong></label><br />';
		$this->content .= $this->ShowInputField("text", "v1rt_personal_twitter_user_secret", Array("id" => "v1rt_personal_twitter_user_secret", "style" => "width:100%"));
		$this->content .= '</tr></td>';
		$this->content .= '<tr><td><br /></td></tr>';

		$this->content .= '</table>';
                
		$this->content .= $this->ShowHiddenField("installDemoData","Y");
		
		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
        $wizard =& $this->GetWizard();
        $templateID = $wizard->GetVar("templateID");
        $themeID = $wizard->GetVar($templateID."_themeID");
        $templatePath = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$templateID."/";
        $wizardTemplatePath = "/bitrix/wizards/v1rt/personal/site/templates/".$templateID;

        if($templateID == "personal_v2")
            $res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 40, "max_width" => 303));
        else
            $res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 64, "max_width" => 240));
        //Храним ID
        if($res)
            $wizard->SetVar("siteLogo", $res);
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