<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "redsign.monopoly";
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
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		
		$proactive = COption::GetOptionString("statistic", "DEFENCE_ON", "N");
		if ($proactive == "Y")
		{
			COption::SetOptionString("statistic", "DEFENCE_ON", "N");
			$wizard->SetVar("proactive", "Y");
		}
		else
		{
			$wizard->SetVar("proactive", "N");			
		}

		if ($wizard->IsNextButtonClick())
		{
			$arTemplates = array("monop");

			$templateID = $wizard->GetVar("wizTemplateID");

			if (!in_array($templateID, $arTemplates))
				$this->SetError(GetMessage("wiz_template"));

			if (in_array($templateID,  array("monop")))
				$wizard->SetVar("templateID", "monop");
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$templatesPath = WizardServices::GetTemplatesPath($wizard->GetPath()."/site");
		$arTemplates = WizardServices::GetTemplates($templatesPath);

		$arTemplateOrder = array();

		if (in_array("monop", array_keys($arTemplates)))
		{
			$arTemplateOrder[] = "monop";
		}

		$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "monop", $wizard->GetVar("siteID"));
		if (!in_array($defaultTemplateID, array("monop"))) $defaultTemplateID = "monop";
		$wizard->SetDefaultVar("wizTemplateID", $defaultTemplateID);

		$arTemplateInfo = array(
			"monop" => array(
				"NAME" => GetMessage("WIZ_TEMPLATE_ADAPT_HORIZONTAL"),
				"DESCRIPTION" => "",
				"PREVIEW" => $wizard->GetPath()."/site/templates/monop/img/".LANGUAGE_ID."/preview.gif",
				"SCREENSHOT" => $wizard->GetPath()."/site/templates/monop/img/".LANGUAGE_ID."/screen.gif",
			),
		);

	//	$this->content .= "<input type='hidden' value='monop' name='templateID' id='templateID'>";//$this->ShowInputField('hidden', 'templateID', array("id" => "templateID", "value" => "monop"));

		global $SHOWIMAGEFIRST;
		$SHOWIMAGEFIRST = true;

		$this->content .= '<div class="inst-template-list-block">';
		foreach ($arTemplateOrder as $templateID)
		{
			$arTemplate = $arTemplateInfo[$templateID];

			if (!$arTemplate)
				continue;

			$this->content .= '<div class="inst-template-description">';
			$this->content .= $this->ShowRadioField("wizTemplateID", $templateID, Array("id" => $templateID, "class" => "inst-template-list-inp"));

			global $SHOWIMAGEFIRST;
			$SHOWIMAGEFIRST = true;

			if ($arTemplate["SCREENSHOT"] && $arTemplate["PREVIEW"])
				$this->content .= CFile::Show2Images($arTemplate["PREVIEW"], $arTemplate["SCREENSHOT"], 150, 150, ' class="inst-template-list-img"');
			else
				$this->content .= CFile::ShowImage($arTemplate["SCREENSHOT"], 150, 150, ' class="inst-template-list-img"', "", true);

			$this->content .= '<label for="'.$templateID.'" class="inst-template-list-label">'.$arTemplate["NAME"]."</label>";
			$this->content .= "</div>";
		}

		$this->content .= "</div>";
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
		$wizard->solutionName = "redsign.monopoly";
		parent::InitStep();

		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$siteID = $wizard->GetVar("siteID");
		$isWizardInstalled = COption::GetOptionString("redsign.monopoly", "wizard_installed", "N", $siteID) == "Y";

		if(COption::GetOptionString("redsign.monopoly", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
			$this->SetNextStep("data_install");
		else
		{
			$this->SetNextStep("shop_settings");
		}

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$wizard->SetDefaultVars(Array(
			"siteLogo" => file_exists(WIZARD_SITE_PATH."include/logo.png") ? WIZARD_SITE_DIR."include/logo.png" : ($isWizardInstalled ? "" : "/bitrix/wizards/redsign/monopoly/site/templates/monop/img/logo.png"),
			"siteLogoRetina" => file_exists(WIZARD_SITE_PATH."include/logo_retina.png") ? WIZARD_SITE_DIR."include/logo_retina.png" : ($isWizardInstalled ? "" : "/bitrix/wizards/redsign/monopoly/site/templates/monop/img/logo.png"),
			//"siteLogoMobile" => file_exists(WIZARD_SITE_PATH."include/logo_mobile.png") ? WIZARD_SITE_DIR."include/logo_mobile.png" : ($isWizardInstalled ? "" : "/bitrix/wizards/redsign/monopoly/site/templates/monop/themes/".$themeID."/images/logo_mobile.png"),
			//"siteLogoMobileRetina" => file_exists(WIZARD_SITE_PATH."include/logo_mobile_retina.png") ? WIZARD_SITE_DIR."include/logo_mobile_retina.png" : ($isWizardInstalled ? "" : "/bitrix/wizards/redsign/monopoly/site/templates/monop/themes/".$themeID."/images/logo_mobile_retina.png")
		));

		$wizard->SetDefaultVars(
			Array(
				"siteName" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
				"siteSchedule" => $this->GetFileContent(WIZARD_SITE_PATH."include/schedule.php", GetMessage("WIZ_COMPANY_SCHEDULE_DEF")),
				"siteTelephone" => $this->GetFileContent(WIZARD_SITE_PATH."include/telephone.php", GetMessage("WIZ_COMPANY_TELEPHONE_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"shopEmail" => COption::GetOptionString("redsign.monopoly", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),
				//"installEshopApp" => COption::GetOptionString("redsign.monopoly", "installEshopApp", "Y", $siteID),
			)
		);
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$this->content .= '<div class="wizard-input-form">';

		$this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteName" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_NAME").'</label>
			'.$this->ShowInputField('text', 'siteName', array("id" => "siteName", "class" => "wizard-field")).'
		</div>';
//logo --
		$siteLogo = $wizard->GetVar("siteLogo", true);
		//$this->content .= "<div style='margin: 5px 0 5px 0;'>".CFile::ShowImage($siteLogo, 0, 0, "border=0 id=\"site-logo-image\"".($wizard->GetVar("useSiteLogo", true) != "Y" ? " class=\"disabled\"" : ""), "", true)."</div>";
		$this->content .= '
		<div class="wizard-input-form-block" style="background-color: #f4f5f6;   width: 571px; padding: 10px">
			<label for="siteLogo">'.GetMessage("WIZ_COMPANY_LOGO").'</label><br/>';
			$this->content .= CFile::ShowImage($siteLogo, 215, 50, "border=0 vspace=15");
			$this->content .= "<br/>".$this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "siteLogo")).
		'</div>';

        
		$siteLogoRetina = $wizard->GetVar("siteLogoRetina", true);
		$this->content .= '
		<div class="wizard-input-form-block"  style="background-color: #f4f5f6;   width: 571px; padding: 10px">
			<label for="siteLogoRetina">'.GetMessage("WIZ_COMPANY_LOGO_RETINA").'</label><br/>'.
			CFile::ShowImage($siteLogoRetina, 430, 100, "border=0 vspace=15").'<br/>'.
			$this->ShowFileField("siteLogoRetina", Array("show_file_info" => "N", "id" => "siteLogoRetina")).
		'</div>';
/*
		$siteLogoMobile = $wizard->GetVar("siteLogoMobile", true);
		$this->content .= '
		<div class="wizard-input-form-block"  style="background-color: #f4f5f6;   width: 571px; padding: 10px">
			<label for="siteLogoMobile">'.GetMessage("WIZ_COMPANY_LOGO_MOBILE").'</label><br/>'.
			CFile::ShowImage($siteLogoMobile, 225, 40, "border=0 vspace=15").'<br/>'.
			$this->ShowFileField("siteLogoMobile", Array("show_file_info" => "N", "id" => "siteLogoMobile")).
		'</div>';

		$siteLogoMobileRetina = $wizard->GetVar("siteLogoMobileRetina", true);
		$this->content .= '
		<div class="wizard-input-form-block"  style="background-color: #f4f5f6;   width: 571px; padding: 10px">
			<label for="siteLogoMobileRetina">'.GetMessage("WIZ_COMPANY_LOGO_MOBILE_RETINA").'</label><br/>'.
			CFile::ShowImage($siteLogoMobileRetina, 450, 80, "border=0 vspace=15").'<br/>'.
			$this->ShowFileField("siteLogoMobileRetina", Array("show_file_info" => "N", "id" => "siteLogoMobileRetina")).
		'</div>';
*/
//-- logo
		$this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteTelephone" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label>
			'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone", "class" => "wizard-field")).'
		</div>';
		
		if(LANGUAGE_ID != "ru")
		{
			$this->content .= '<div class="wizard-input-form-block">
				<label for="shopEmail" class="wizard-input-title">'.GetMessage("WIZ_SHOP_EMAIL").'</label>
				'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail", "class" => "wizard-field")).'
			</div>';	
		}
		$this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteSchedule" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_SCHEDULE").'</label>
			'.$this->ShowInputField('textarea', 'siteSchedule', array("rows"=>"3", "id" => "siteSchedule", "class" => "wizard-field")).'
		</div>';	
		$this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteCopy" class="wizard-input-title">'.GetMessage("WIZ_COMPANY_COPY").'</label>
			'.$this->ShowInputField('textarea', 'siteCopy', array("rows"=>"3", "id" => "siteCopy", "class" => "wizard-field")).'
		</div>';

		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 
		$styleMeta = 'style="display:block"';
		if($firstStep == "Y") $styleMeta = 'style="display:none"';

		$this->content .= '
		<div  id="bx_metadata" '.$styleMeta.'>
			<div class="wizard-input-form-block">
				<div class="wizard-metadata-title">'.GetMessage("wiz_meta_data").'</div>
				<label for="siteMetaDescription" class="wizard-input-title">'.GetMessage("wiz_meta_description").'</label>
				'.$this->ShowInputField("textarea", "siteMetaDescription", Array("id" => "siteMetaDescription", "rows"=>"3", "class" => "wizard-field")).'
			</div>';
		$this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteMetaKeywords" class="wizard-input-title">'.GetMessage("wiz_meta_keywords").'</label><br>
				'.$this->ShowInputField('text', 'siteMetaKeywords', array("id" => "siteMetaKeywords", "class" => "wizard-field")).'
			</div>
		</div>';
		
//install Demo data		
		if($firstStep == "Y")
		{
			$this->content .= '
			<div class="wizard-input-form-block"'.(LANGUAGE_ID != "ru" ? ' style="display:none"' : '').'>
				'.$this->ShowCheckboxField(
							"installDemoData", 
							"Y", 
							(array("id" => "installDemoData", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"))
						).'
				<label for="installDemoData">'.GetMessage("wiz_structure_data").'</label>
			</div>';
		}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
		}
        /*
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/eshopapp") && LANGUAGE_ID == "ru")
			$this->content .= '
			<div class="wizard-input-form-block">
				'.$this->ShowCheckboxField("installEshopApp", "Y", array("id"=>"installEshopApp")).
				' <label for="installEshopApp">'.GetMessage("wiz_install_eshopapp").'</label>
			</div>';
        */
		if (CModule::IncludeModule("catalog"))
		{
			$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
			if (!$db_res->Fetch())
			{
				$this->content .= '
				<div class="wizard-input-form-block">
					<label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label>
					<div class="wizard-input-form-block-content">
						'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
						'. $this->ShowCheckboxField("installPriceBASE", "Y",
						(array("id" => "install-demo-data")))
						. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />

					</div>
				</div>';
			}
		}

		$this->content .= '</div>';
	}
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 150, "max_width" => 500, "make_preview" => "Y"));
		//$res = $this->SaveFile("siteLogoRetina", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 150, "max_width" => 500, "make_preview" => "Y"));
		//$res = $this->SaveFile("siteLogoMobile", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 150, "max_width" => 500, "make_preview" => "Y"));
		//$res = $this->SaveFile("siteLogoMobileRetina", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 150, "max_width" => 500, "make_preview" => "Y"));
	}
}


class ShopSettings extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("shop_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_SS"));
		$this->SetNextStep("data_install");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();

		$siteStamp =$wizard->GetPath()."/site/templates/minimal/images/pechat.gif";
		$siteID = $wizard->GetVar("siteID");
		
		$wizard->SetDefaultVars(
			Array(
				"shopLocalization" => COption::GetOptionString("redsign.monopoly", "shopLocalization", "ru", $siteID),
				"shopEmail" => COption::GetOptionString("redsign.monopoly", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("redsign.monopoly", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("redsign.monopoly", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				//"shopZip" => 101000,
				"shopAdr" => COption::GetOptionString("redsign.monopoly", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				//"shopINN" => COption::GetOptionString("redsign.monopoly", "shopINN", "1234567890", $siteID),
				//"shopKPP" => COption::GetOptionString("redsign.monopoly", "shopKPP", "123456789", $siteID),
				// "shopNS" => COption::GetOptionString("redsign.monopoly", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				// "shopBANK" => COption::GetOptionString("redsign.monopoly", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				// "shopBANKREKV" => COption::GetOptionString("redsign.monopoly", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				// "shopKS" => COption::GetOptionString("redsign.monopoly", "shopKS", "30101 810 4 0000 0000225", $siteID),
				// "siteStamp" => COption::GetOptionString("redsign.monopoly", "siteStamp", $siteStamp, $siteID),

				//"shopCompany_ua" => COption::GetOptionString("redsign.monopoly", "shopCompany_ua", "", $siteID),
				// "shopOfName_ua" => COption::GetOptionString("redsign.monopoly", "shopOfName_ua", GetMessage("WIZ_SHOP_OF_NAME_DEF_UA"), $siteID),
				// "shopLocation_ua" => COption::GetOptionString("redsign.monopoly", "shopLocation_ua", GetMessage("WIZ_SHOP_LOCATION_DEF_UA"), $siteID),
				// "shopAdr_ua" => COption::GetOptionString("redsign.monopoly", "shopAdr_ua", GetMessage("WIZ_SHOP_ADR_DEF_UA"), $siteID),
				// "shopEGRPU_ua" =>  COption::GetOptionString("redsign.monopoly", "shopEGRPU_ua", "", $siteID),
				// "shopINN_ua" =>  COption::GetOptionString("redsign.monopoly", "shopINN_ua", "", $siteID),
				// "shopNDS_ua" =>  COption::GetOptionString("redsign.monopoly", "shopNDS_ua", "", $siteID),
				// "shopNS_ua" =>  COption::GetOptionString("redsign.monopoly", "shopNS_ua", "", $siteID),
				// "shopBank_ua" =>  COption::GetOptionString("redsign.monopoly", "shopBank_ua", "", $siteID),
				// "shopMFO_ua" =>  COption::GetOptionString("redsign.monopoly", "shopMFO_ua", "", $siteID),
				// "shopPlace_ua" =>  COption::GetOptionString("redsign.monopoly", "shopPlace_ua", "", $siteID),
				// "shopFIO_ua" =>  COption::GetOptionString("redsign.monopoly", "shopFIO_ua", "", $siteID),
				// "shopTax_ua" =>  COption::GetOptionString("redsign.monopoly", "shopTax_ua", "", $siteID),

				// "installPriceBASE" => COption::GetOptionString("redsign.monopoly", "installPriceBASE", "Y", $siteID),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteStamp = $wizard->GetVar("siteStamp", true);

			$this->content .=
				'<div class="wizard-catalog-title">'.GetMessage("WIZ_SHOP_LOCALIZATION").'</div>
				<div class="wizard-input-form-block" >'.
					$this->ShowSelectField("shopLocalization", array(
						"ru" => GetMessage("WIZ_SHOP_LOCALIZATION_RUSSIA"),
						"ua" => GetMessage("WIZ_SHOP_LOCALIZATION_UKRAINE"),
						"kz" => GetMessage("WIZ_SHOP_LOCALIZATION_KAZAKHSTAN"),
						"bl" => GetMessage("WIZ_SHOP_LOCALIZATION_BELORUSSIA")
					), array("onchange" => "langReload()", "id" => "localization_select","class" => "wizard-field", "style"=>"padding:0 0 0 15px")).'
				</div>';

			$currentLocalization = $wizard->GetVar("shopLocalization");
			if (empty($currentLocalization))
				$currentLocalization = $wizard->GetDefaultVar("shopLocalization");

			$this->content .= '<div class="wizard-catalog-title">'.GetMessage("WIZ_STEP_SS").'</div>
				<div class="wizard-input-form">';

			$this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label>
					'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail", "class" => "wizard-field")).'
				</div>';

			//ru
			$this->content .= '<div id="ru_bank_details" class="wizard-input-form-block" style="display:'.(($currentLocalization == "ru" || $currentLocalization == "kz" || $currentLocalization == "bl") ? 'block':'none').'">
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopOfName">'.GetMessage("WIZ_SHOP_OF_NAME").'</label>'
					.$this->ShowInputField('text', 'shopOfName', array("id" => "shopOfName", "class" => "wizard-field")).'
				</div>';
	
			$this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopLocation">'.GetMessage("WIZ_SHOP_LOCATION").'</label>'
					.$this->ShowInputField('text', 'shopLocation', array("id" => "shopLocation", "class" => "wizard-field")).'
				</div>';
	
			$this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopAdr">'.GetMessage("WIZ_SHOP_ADR").'</label>'
					.$this->ShowInputField('textarea', 'shopAdr', array("rows"=>"3", "id" => "shopAdr", "class" => "wizard-field")).'
				</div>';

			/*	
			$this->content .= '
				<div class="wizard-catalog-title">'.GetMessage("WIZ_SHOP_BANK_TITLE").'</div>
				<table class="wizard-input-table">
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_INN").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopINN', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_KPP").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopKPP', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_NS").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNS', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANK").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBANK', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANKREKV").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBANKREKV', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_KS").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopKS', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_STAMP").':</td>
						<td class="wizard-input-table-right">'.$this->ShowFileField("siteStamp", Array("show_file_info"=> "N", "id" => "siteStamp")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</td>
					</tr>
				</table>
			</div><!--ru-->
			';
	//ua
			$this->content .= '<div id="ua_bank_details" class="wizard-input-form-block" style="display:'.(($currentLocalization == "ua") ? 'block':'none').'">
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopOfName_ua">'.GetMessage("WIZ_SHOP_OF_NAME").'</label>'
					.$this->ShowInputField('text', 'shopOfName_ua', array("id" => "shopOfName_ua", "class" => "wizard-field")).'
					<p style="color:grey; margin: 3px 0 7px;">'.GetMessage("WIZ_SHOP_OF_NAME_DESCR_UA").'</p>
				</div>';

			$this->content .= '<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopLocation_ua">'.GetMessage("WIZ_SHOP_LOCATION").'</label>'
					.$this->ShowInputField('text', 'shopLocation_ua', array("id" => "shopLocation_ua", "class" => "wizard-field")).'
					<p style="color:grey; margin: 3px 0 7px;">'.GetMessage("WIZ_SHOP_LOCATION_DESCR_UA").'</p>
				</div>';


			$this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopAdr_ua">'.GetMessage("WIZ_SHOP_ADR").'</label>'.
					$this->ShowInputField('textarea', 'shopAdr_ua', array("rows"=>"3", "id" => "shopAdr_ua", "class" => "wizard-field")).'
					<p style="color:grey; margin: 3px 0 7px;">'.GetMessage("WIZ_SHOP_ADR_DESCR_UA").'</p>
				</div>';

			$this->content .= '
				<div class="wizard-catalog-title">'.GetMessage("WIZ_SHOP_RECV_UA").'</div>
				<p>'.GetMessage("WIZ_SHOP_RECV_UA_DESC").'</p>
				<table class="wizard-input-table">
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_EGRPU_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopEGRPU_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_INN_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopINN_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_NDS_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNDS_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_NS_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopNS_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_BANK_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopBank_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_MFO_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopMFO_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_PLACE_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopPlace_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_FIO_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopFIO_ua', array("class" => "wizard-field")).'</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">'.GetMessage("WIZ_SHOP_TAX_UA").':</td>
						<td class="wizard-input-table-right">'.$this->ShowInputField('text', 'shopTax_ua', array("class" => "wizard-field")).'</td>
					</tr>
				</table>
			</div>
			';

			if (CModule::IncludeModule("catalog"))
			{
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
				if (!$db_res->Fetch())
				{
					$this->content .= '
					<div class="wizard-input-form-block">
						<div class="wizard-catalog-title">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</div>
						<div class="wizard-input-form-block-content">
							'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
							'. $this->ShowCheckboxField("installPriceBASE", "Y",
							(array("id" => "install-demo-data")))
							. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />

						</div>
					</div>';
				}
			}
			*/
			$this->content .= '</div>';

			$this->content .= '
				<script>
					function langReload()
					{
						var objSel = document.getElementById("localization_select");
						var locSelected = objSel.options[objSel.selectedIndex].value;
						document.getElementById("ru_bank_details").style.display = (locSelected == "ru" || locSelected == "kz" || locSelected == "bl") ? "block" : "none";
						document.getElementById("ua_bank_details").style.display = (locSelected == "ua") ? "block" : "none";
						/*document.getElementById("kz_bank_details").style.display = (locSelected == "kz") ? "block" : "none";*/
					}
				</script>
			';
	}
	
	function OnPostForm()
	{
		// $wizard =& $this->GetWizard();
		// $res = $this->SaveFile("siteStamp", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
	}

}

class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		if($_SESSION["BX_ESHOP_LOCATION"] == "Y")
			$this->repeatCurrentService = true;
		else
			$this->repeatCurrentService = false;

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
	//	$this->content .= "<br clear=\"all\"><a href=\"/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&site_id=".$siteID."&wizardName=redsign:monopoly.mobile&".bitrix_sessid_get()."\" class=\"button-next\"><span id=\"next-button-caption\">".GetMessage("wizard_store_mobile")."</span></a><br>";
		
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");		
			
		
	}

}
?>