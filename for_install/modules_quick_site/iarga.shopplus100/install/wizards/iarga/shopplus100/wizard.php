<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");
define('WIZARD_TEMPLATE_ID','iarga.shopplus100.main');

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "shopplus100";
		$this->SetNextStep("site_settings");
	}
}


class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$this->SetStepID("site_settings");
		$wizard->solutionName = "shopplus100";
		parent::InitStep();

		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$wizard->SetDefaultVar("siteLogoSet", true); //Устанавливаем значение по умолчанию
		$siteID = $wizard->GetVar("siteID");
		
		if(COption::GetOptionString("shopplus100", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
			$this->SetNextStep("data_install");
		else
		{
			if(LANGUAGE_ID != "ru")
				$this->SetNextStep("pay_system");
			else
			$this->SetNextStep("shop_settings");
		}
		
		$templateID = $wizard->GetVar("templateID");
		
		$wizard->SetDefaultVars(Array("siteNameSet" => true));

		
		if($wizard->GetVar('siteLogoSet', true)){
			$themeID = $wizard->GetVar($templateID."_themeID");
			$siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH."inc/parts/company_logo.php", "/bitrix/wizards/bitrix/shopplus100/site/templates/store_light/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.jpg");
			if (!file_exists(WIZARD_SITE_PATH."inc/parts/logo.jpg"))
			$siteLogo = "/bitrix/wizards/bitrix/shopplus100/site/templates/store_light/themes/".$themeID."/lang/".LANGUAGE_ID."/logo.jpg";
			$wizard->SetDefaultVars(Array("siteLogo" => $siteLogo));
		}   
		$wizard->SetDefaultVars(
			Array(
				"siteName" => $this->GetFileContent(WIZARD_SITE_PATH."inc/parts/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
				"siteSchedule" => $this->GetFileContent(WIZARD_SITE_PATH."inc/parts/schedule.php", GetMessage("WIZ_COMPANY_SCHEDULE_DEF")),
				"siteTelephone" => $this->GetFileContent(WIZARD_SITE_PATH."inc/parts/telephone.php", GetMessage("WIZ_COMPANY_TELEPHONE_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."inc/parts/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"shopEmail" => COption::GetOptionString("shopplus100", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),
				
			)
		);
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		$this->content .= '<div class="wizard-input-form">';
		if($wizard->GetVar('siteNameSet', true)){
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_NAME").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteName', array("id" => "siteName")).'</div>
				</div>
			</div>';
		}
		
		if($wizard->GetVar('siteLogoSet', true)){
			$siteLogo = $wizard->GetVar("siteLogo", true);
	
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_LOGO").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.CFile::ShowImage($siteLogo, 280, 40, "border=0 vspace=15") . '<br>' . $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "siteLogo")).'</div>
				</div>
			</div>';
		}
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephone">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone")).'</div>
			</div>
		</div>';
		
		if(LANGUAGE_ID != "ru")
		{
			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail")).'</div>
				</div>
			</div>';	
		}
		/*$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteSchedule">'.GetMessage("WIZ_COMPANY_SCHEDULE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'siteSchedule', array("rows"=>"3", "id" => "siteSchedule")).'</div>
			</div>
		</div>';	
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteCopy">'.GetMessage("WIZ_COMPANY_COPY").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'siteCopy', array("rows"=>"3", "id" => "siteCopy")).'</div>
			</div>
		</div>';*/
/*---*/		
		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 
		$styleMeta = 'style="display:block"';
		if($firstStep == "Y") $styleMeta = 'style="display:none"';

		$this->content .= '
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
						$this->ShowInputField('text', 'siteMetaKeywords', array("id" => "siteMetaKeywords")).'</div>
				</div>
			</div>
		</div>';
		
//install Demo data		
		if($firstStep == "Y")
		{
			$this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-input-form-block-content">'.
						$this->ShowCheckboxField(
							"installDemoData", 
							"Y", 
							(array("id" => "installDemoData", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"))
						).
				'
				<label for="installDemoData">'.GetMessage("wiz_structure_data").'</label>
				</div>
			</div>';
			}
		else
		{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
		}
	/*	$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "", $wizard->GetVar("siteID")); 
		if(!empty($defaultTemplateID) && $defaultTemplateID != $wizard->GetVar("templateID")){
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteSchedule">'.GetMessage("WIZ_REWRITE_INDEX_DESC").'</label></h4>
				<div class="wizard-input-form-block-content">'.
						$this->ShowCheckboxField(
							"rewriteIndex", 
							"Y", 
							(array("id" => "rewriteIndex"))
						).
				'
				<label for="installDemoData">'.GetMessage("wiz_rewrite_index").'</label>
				</div>
			</div>';	
		}       */
		
		if(LANGUAGE_ID != "ru")
		{
			CModule::IncludeModule("catalog");
			$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
			if (!$db_res->Fetch())
			{
				$this->content .= '
				<div class="wizard-input-form-block">
					<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label></h4>
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
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 40, "max_width" => 280, "make_preview" => "Y"));
	}
}


class ShopSettings extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("shop_settings");
		$this->SetTitle(GetMessage("WIZ_STEP_SS"));
		$this->SetNextStep("person_type");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();

		$siteStamp =$wizard->GetPath()."/site/templates/minimal/images/pechat.gif";
		$siteID = $wizard->GetVar("siteID");
		
		$wizard->SetDefaultVars(
			Array(
				"shopLocalization" => COption::GetOptionString("shopplus100", "shopLocalization", "ru", $siteID),
				"shopEmail" => COption::GetOptionString("shopplus100", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("shopplus100", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("shopplus100", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				//"shopZip" => 101000,
				"shopAdr" => COption::GetOptionString("shopplus100", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("shopplus100", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("shopplus100", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("shopplus100", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("shopplus100", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("shopplus100", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("shopplus100", "shopKS", "30101 810 4 0000 0000225", $siteID),
				"siteStamp" => COption::GetOptionString("shopplus100", "siteStamp", $siteStamp, $siteID),

				"shopCompany_ua" => COption::GetOptionString("shopplus100", "shopCompany_ua", "", $siteID),
				"shopEGRPU_ua" =>  COption::GetOptionString("shopplus100", "shopCompany_ua", "", $siteID),
				"shopINN_ua" =>  COption::GetOptionString("shopplus100", "shopINN_ua", "", $siteID),
				"shopNDS_ua" =>  COption::GetOptionString("shopplus100", "shopNDS_ua", "", $siteID),
				"shopNS_ua" =>  COption::GetOptionString("shopplus100", "shopNS_ua", "", $siteID),
				"shopBank_ua" =>  COption::GetOptionString("shopplus100", "shopBank_ua", "", $siteID),
				"shopMFO_ua" =>  COption::GetOptionString("shopplus100", "shopMFO_ua", "", $siteID),
				"shopPlace_ua" =>  COption::GetOptionString("shopplus100", "shopPlace_ua", "", $siteID),
				"shopFIO_ua" =>  COption::GetOptionString("shopplus100", "shopFIO_ua", "", $siteID),
				"shopTax_ua" =>  COption::GetOptionString("shopplus100", "shopTax_ua", "", $siteID),

				"installPriceBASE" => COption::GetOptionString("shopplus100", "installPriceBASE", "Y", $siteID),
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteStamp = $wizard->GetVar("siteStamp", true);

		if (!CModule::IncludeModule("catalog"))
		{
			$this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_CATALOG")."</p>";
			$this->SetNextStep("shop_settings");
		}
		else
		{
			/*$this->content .=
				$this->ShowSelectField("shopLocalization", array("ru" => GetMessage("WIZ_SHOP_LOCALIZATION_RUSSIA"), "ua" => GetMessage("WIZ_SHOP_LOCALIZATION_UKRAINE")), array("onchange" => "langReload()", "id" => "localization_select"))
				.' <label for="shopLocalization">'.GetMessage("WIZ_SHOP_LOCALIZATION").'</label><br />';*/

			$this->content .= '<div class="wizard-input-form">';

			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail")).'</div>
				</div>
			</div>';	
			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopOfName">'.GetMessage("WIZ_SHOP_OF_NAME").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopOfName', array("id" => "shopOfName")).'</div>
				</div>
			</div>';			
	
			$this->content .= '<div class="wizard-input-form-block">
				<h4><label for="shopLocation">'.GetMessage("WIZ_SHOP_LOCATION").'</label></h4>';
				
			$this->content .= '
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopLocation', array("id" => "shopLocation")).'</div>
				</div>';
			$this->content .= '</div>';			
	
			/*$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_ADR").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'shopAdr', array("rows"=>"3", "id" => "shopAdr")).'</div>
				</div>
			</div>';*/


			$currentLocalization = $wizard->GetVar("shopLocalization");
			if (empty($currentLocalization))
				$currentLocalization = $wizard->GetDefaultVar("shopLocalization");
	 //ru
			$this->content .= '
			<div id="ru_bank_details" class="wizard-input-form-block" >
				<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_BANK_TITLE").'</label></h4>
				<table  class="data-table-no-border" >
					<tr>
						<th width="35%">'.GetMessage("WIZ_SHOP_INN").':</th>
						<td width="65%"><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopINN').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_KPP").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopKPP').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NS").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopNS').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANK").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopBANK').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANKREKV").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopBANKREKV').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_KS").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopKS').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_STAMP").':</th>
							<td><div class="">'.$this->ShowFileField("siteStamp", Array("show_file_info"=> "N", "id" => "siteStamp", "style" => "width: 90%; border: solid 1px #CECECE; background-color: #F5F5F5; padding: 3px;")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</div></td>
						</tr>
				</table>
			</div>
			';
	//ua
		/*	$this->content .= '
			<div id="ua_bank_details" class="wizard-input-form-block" style="display:'.(($currentLocalization == "ua") ? 'block':'none').'">
				<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_RECV_UA").'</label></h4>
				<p>'.GetMessage("WIZ_SHOP_RECV_UA_DESC").'</p>
				<table class="data-table-no-border" >
					<tr>
						<th width="35%">'.GetMessage("WIZ_SHOP_COMPANY_UA").':</th>
						<td width="65%"><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopCompany_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_EGRPU_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEGRPU_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_INN_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopINN_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NDS_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopNDS_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NS_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopNS_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANK_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopBank_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_MFO_UA").':</th>
							<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopMFO_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_PLACE_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopPlace_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_FIO_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopFIO_ua').'</div></td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_TAX_UA").':</th>
						<td><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopTax_ua').'</div></td>
					</tr>
				</table>
			</div>
			';   */

			if (CModule::IncludeModule("catalog"))
			{
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
				if (!$db_res->Fetch())
				{
					$this->content .= '
					<div class="wizard-input-form-block">
						<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label></h4>
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

			/*$this->content .= '
				<script>
					function langReload()
					{
			            var objSel = document.getElementById("localization_select");
			            var locSelected = objSel.options[objSel.selectedIndex].value;
			            document.getElementById("ru_bank_details").style.display = (locSelected == "ru") ? "block" : "none";
			            document.getElementById("ua_bank_details").style.display = (locSelected == "ua") ? "block" : "none";
			            document.getElementById("kz_bank_details").style.display = (locSelected == "kz") ? "block" : "none";
					}
				</script>
			';   */
		}
	}
	
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteStamp", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
	}

}

class PersonType extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("person_type");
		$this->SetTitle(GetMessage("WIZ_STEP_PT"));
		$this->SetNextStep("pay_system");
		$this->SetPrevStep("shop_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		if ($shopLocalization == "ua")
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"fiz_ua" => "Y",
						"ur" => "Y",
					)
				)
			);
		else
			$wizard->SetDefaultVars(
				Array(
					"personType" => Array(
						"fiz" => "Y",
						"ur" => "Y",
					)
				)
			);
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);

		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_PERSON_TYPE_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
					'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />';
			//	if ($shopLocalization == "ua")
			//		$this->content .= $this->ShowCheckboxField('personType[fiz_ua]', 'Y', (array("id" => "personTypeFua"))).' <label for="personTypeFua">'.GetMessage("WIZ_PERSON_TYPE_FIZ_UA").'</label><br />';
				$this->content .= '
				</div>
			</div>
			'.GetMessage("WIZ_PERSON_TYPE").'
		</div>';
		$this->content .= '</div>';
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$personType = $wizard->GetVar("personType");

		if (empty($personType["fiz"]) && empty($personType["ur"]))
			$this->SetError(GetMessage('WIZ_NO_PT'));
	}

}

class PaySystem extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("pay_system");
		$this->SetTitle(GetMessage("WIZ_STEP_PS"));
		$this->SetNextStep("data_install");
		if(LANGUAGE_ID != "ru")
			$this->SetPrevStep("site_settings");
		else
		$this->SetPrevStep("person_type");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
/*payer type
		if(LANGUAGE_ID == "ru")
		{
			$shopLocalization = $wizard->GetVar("shopLocalization", true);
			if ($shopLocalization == "ua")
				$wizard->SetDefaultVars(
					Array(
						"personType" => Array(
							"fiz" => "Y",
							"fiz_ua" => "Y",
							"ur" => "Y",
						)
					)
				);
			else
				$wizard->SetDefaultVars(
					Array(
						"personType" => Array(
							"fiz" => "Y",
							"ur" => "Y",
						)
					)
				);
		}
=====*/
		if(LANGUAGE_ID == "ru")
		{
			/*$shopLocalization = $wizard->GetVar("shopLocalization", true);

			if ($shopLocalization == "ua")
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"oshad" => "Y",
							"bill" => "Y",
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
						)
					)
				);
			else  */
				$wizard->SetDefaultVars(
					Array(
						"paysystem" => Array(
							"cash" => "Y",
							"sber" => "Y",
							"bill" => "Y",
						),
						"delivery" => Array(
							"courier" => "Y",
							"self" => "Y",
							"russianpost" => "N",
						)
					)
				);
		}
		else
		{
			$wizard->SetDefaultVars(
				Array(
					"paysystem" => Array(
						"cash" => "Y",	
						"paypal" => "Y",
					),			
					"delivery" => Array(
						"courier" => "Y",	
						"self" => "Y",
						"dhl" => "Y",
						"ups" => "Y",
					)
				)
			);
		}
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$paysystem = $wizard->GetVar("paysystem");

		if (empty($paysystem["cash"]) && empty($paysystem["sber"]) && empty($paysystem["bill"]) && empty($paysystem["paypal"]))
			$this->SetError(GetMessage('WIZ_NO_PS'));
/*payer type
		if(LANGUAGE_ID == "ru")
		{
			$personType = $wizard->GetVar("personType");

			if (empty($personType["fiz"]) && empty($personType["ur"]))
				$this->SetError(GetMessage('WIZ_NO_PT'));
		}
===*/
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$shopLocalization = $wizard->GetVar("shopLocalization", true);
/*payer type
		if(LANGUAGE_ID == "ru")
		{
			$this->content .= '<div class="wizard-input-form">';
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4>'.GetMessage("WIZ_PERSON_TYPE_TITLE").'</h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-checkbox">
						'.$this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
						'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />';
			if ($shopLocalization == "ua")
				$this->content .= $this->ShowCheckboxField('personType[fiz_ua]', 'Y', (array("id" => "personTypeFua"))).' <label for="personTypeFua">'.GetMessage("WIZ_PERSON_TYPE_FIZ_UA").'</label><br />';
			$this->content .= '
					</div>
				</div>
				'.GetMessage("WIZ_PERSON_TYPE").'
			</div>';
			$this->content .= '</div>';
		}
===*/
		$personType = $wizard->GetVar("personType");
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_PAY_SYSTEM_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).' <label for="paysystemC">'.GetMessage("WIZ_PAY_SYSTEM_C").'</label><br />';
				if(LANGUAGE_ID == "ru")
				{
				/*	if($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
						$this->content .= $this->ShowCheckboxField('paysystem[oshad]', 'Y', (array("id" => "paysystemO"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_O").'</label><br />';
					else*/if ($personType["fiz"] == "Y")
						$this->content .= $this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_S").'</label><br />';
					if($personType["ur"] == "Y")
						$this->content .= $this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))).' <label for="paysystemB">'.GetMessage("WIZ_PAY_SYSTEM_B").'</label><br />';
				}
				else
				{
					$this->content .= $this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))).' <label for="paysystemP">PayPal</label><br />';
				}
				$this->content .= '</div>
			</div>
			'.GetMessage("WIZ_PAY_SYSTEM").'
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_DELIVERY_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))).' <label for="deliveryC">'.GetMessage("WIZ_DELIVERY_C").'</label><br />
					'.$this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))).' <label for="deliveryS">'.GetMessage("WIZ_DELIVERY_S").'</label><br />';
					if(LANGUAGE_ID == "ru")
					{
						//if ($shopLocalization != "ua")
							$this->content .= $this->ShowCheckboxField('delivery[russianpost]', 'Y', (array("id" => "deliveryR"))).' <label for="deliveryR">'.GetMessage("WIZ_DELIVERY_R").'</label><br />';
					}
					else
					{
						$this->content .= $this->ShowCheckboxField('delivery[dhl]', 'Y', (array("id" => "deliveryD"))).' <label for="deliveryD">DHL</label><br />';
						$this->content .= $this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))).' <label for="deliveryU">UPS</label><br />';
					}
					$this->content .= '
				</div>
			</div>
			'.GetMessage("WIZ_DELIVERY").'
		</div>';

		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_LOCATION_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
		if(LANGUAGE_ID == "ru")
		{
			$this->content .= $this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
				." <label for=\"loc_ussr\">".GetMessage('WSL_STEP2_GFILE_USSR')."</label><br />";
		}
		$this->content .= $this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
			." <label for=\"loc_usa\">".GetMessage('WSL_STEP2_GFILE_USA')."</label><br />";
		$this->content .= $this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
			." <label for=\"loc_cntr\">".GetMessage('WSL_STEP2_GFILE_CNTR')."</label><br />";
		$this->content .= $this->ShowRadioField("locations_csv", "", array("id" => "none"))
			." <label for=\"none\">".GetMessage('WSL_STEP2_GFILE_NONE')."</label>";
					$this->content .= '
				</div>
			</div>
		</div>';

		$this->content .= '<div class="wizard-input-form-block">'.GetMessage("WIZ_DELIVERY_HINT").'</div>';

		$this->content .= '</div>';
	}
}
class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		if($_SESSION["BX_shopplus100_LOCATION"] == "Y")
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
		
		$this->content .= GetMessage("FINISH_STEP_CONTENT");
	
		
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");		
			
		
	}

}
?>