<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "prokids";
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
		$wizard->solutionName = "prokids";
		parent::InitStep();
		

		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$siteID = $wizard->GetVar("siteID");
		
		if(COption::GetOptionString("store", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
			$this->SetNextStep("data_install");
		else
		{
			$this->SetNextStep("shop_settings");
		}

		$wizard->SetDefaultVars(
			Array(
				"siteName" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
				"siteTelephoneCode" => GetMessage("WIZ_COMPANY_TELEPHONE_CODE"),
				"siteTelephone" => GetMessage("WIZ_COMPANY_TELEPHONE_DEF"),
				"siteSchedule" => GetMessage("WIZ_COMPANY_SCHEDULE_DEFAULT"),
				"smallAdress" => GetMessage("WIZ_COMPANY_SMALL_ADDRESS_DEFAULT"),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),
			)
		);
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_NAME").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteName', array("id" => "siteName")).'</div>
				</div>
			</div>';
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephone">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label></h4>
			<label for="siteMetaDescription">'.GetMessage("WIZ_COMPANY_TELEPHONE_CODE_T").'</label>
			<div class="wizard-input-form-block-content" style="margin-top:7px;">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephoneCode', array("id" => "siteTelephoneCode")).'</div>
			</div>
			<label for="siteMetaDescription">'.GetMessage("WIZ_COMPANY_TELEPHONE_DEF_T").'</label>
			<div class="wizard-input-form-block-content" style="margin-top:7px;">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone")).'</div>
			</div>
		</div>';

		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteSchedule">'.GetMessage("WIZ_COMPANY_SCHEDULE").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteSchedule', array("id" => "siteSchedule")).'</div>
				</div>
			</div>';

		$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="smallAdress">'.GetMessage("WIZ_COMPANY_SMALL_ADDRESS").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'smallAdress', array("id" => "smallAdress")).'</div>
				</div>
			</div>';

		$this->content .= '
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
			</div>';

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
				"shopEmail" => COption::GetOptionString("store", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("store", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("store", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				"shopAdr" => COption::GetOptionString("store", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("store", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("store", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("store", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("store", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("store", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("store", "shopKS", "30101 810 4 0000 0000225", $siteID),
				"siteStamp" => COption::GetOptionString("store", "siteStamp", $siteStamp, $siteID),
				"importLocations" => Array(
					"del" => "",
					"add" => "Y",
				)
			)
		);
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$siteStamp = $wizard->GetVar("siteStamp", true);
		
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

		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_ADR").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'shopAdr', array("rows"=>"3", "id" => "shopAdr")).'</div>
			</div>
		</div>';		
/* import locations */
		$this->content .= '
		<div class="wizard-input-form-block">
			<b>'.GetMessage("WIZ_IMPORT_LOCATION_VNIMANIE").'</b><br />
			<h4>'.GetMessage("WIZ_IMPORT_LOCATION_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('locations_clear', 'Y', (array("id" => "locations_clear"))).' <label for="locations_clear">'.GetMessage("WIZ_IMPORT_LOCATION_DEL").'</label><br />
				</div>
			</div>
			'.GetMessage("WIZ_IMPORT_LOCATION_DISC_DEL");

		$this->content .= '<br /><br />
			<div class="wizard-input-form-block-content">';
			if(in_array(LANGUAGE_ID, array("ru", "ua")))
			{
				$this->content .=
					'<div class="wizard-input-form-field wizard-input-form-field-radio">'.
						$this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
						." <label for=\"loc_ussr\">".GetMessage('WSL_STEP2_GFILE_USSR')."</label>
					</div>";
				$this->content .=
					'<div class="wizard-input-form-field wizard-input-form-field-radio"">'.
						$this->ShowRadioField("locations_csv", "loc_ua.csv", array("id" => "loc_ua"))
						." <label for=\"loc_ua\">".GetMessage('WSL_STEP2_GFILE_UA')."</label>
					</div>";
				$this->content .=
					'<div class="wizard-input-form-field wizard-input-form-field-radio"">'.
						$this->ShowRadioField("locations_csv", "loc_kz.csv", array("id" => "loc_kz"))
						." <label for=\"loc_kz\">".GetMessage('WSL_STEP2_GFILE_KZ')."</label>
					</div>";
			}
			$this->content .=
				'<div class="wizard-input-form-field wizard-input-form-field-radio"">'.
					$this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
					." <label for=\"loc_usa\">".GetMessage('WSL_STEP2_GFILE_USA')."</label>
				</div>";
			$this->content .=
				'<div class="wizard-input-form-field wizard-input-form-field-radio"">'.
					$this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
					." <label for=\"loc_cntr\">".GetMessage('WSL_STEP2_GFILE_CNTR')."</label>
				</div>";
			$this->content .=
				'<div class="wizard-input-form-field wizard-input-form-field-radio"">'.
					$this->ShowRadioField("locations_csv", "", array("id" => "none"))
					." <label for=\"none\">".GetMessage('WSL_STEP2_GFILE_NONE')."</label>
				</div>";
			$this->content .= '<br />
				'.GetMessage("WIZ_IMPORT_LOCATION_DISC_ADD").'<br /><br />
				'.GetMessage("WIZ_IMPORT_LOCATION_VNIMANIE2").'
			</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="shopAdr">'.GetMessage("WIZ_SHOP_BANK_TITLE").'</label></h4>
		</div>
				<table class="data-table-no-border">
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
				</tr></table>';				
		$this->content .= '</div>';
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
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_PERSON_TYPE_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
					'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />
					
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
		if(LANGUAGE_ID == "ru")
		{
		$wizard->SetDefaultVars(
			Array(
				"paysystem" => Array(
					"cash" => "Y",						// Наличный расчет
					"cred" => "Y",						// Кредитная карта
					"webm" => "Y",						// Оплата в платежной системе Web Money
					"yand" => "Y",						// Оплата в платежной системе Яндекс.Деньги
					"sber" => "Y",						// Сбербанк
					"scht" => "Y",						// Счет
				),			
				"delivery" => Array(
					"pochta" => "Y",					// Po pochte
					"kurier" => "Y",					// Kurier
					"samovizov" => "Y",					// Samovizov
				)
			)
		);
	}
		else
		{
			$wizard->SetDefaultVars(
				Array(
					"paysystem" => Array(
						"cash" => "Y",					// Наличный расчет
						"cred" => "Y",					// Кредитная карта
						"webm" => "Y",					// Оплата в платежной системе Web Money
						"yand" => "Y",					// Оплата в платежной системе Яндекс.Деньги
						"sber" => "Y",					// Сбербанк
						"scht" => "Y",					// Счет
					),			
					"delivery" => Array(
						"pochta" => "Y",				// Po pochte
						"kurier" => "Y",				// Kurier
						"samovizov" => "Y",				// Samovizov
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
	}

	function ShowStep()
	{

		$wizard =& $this->GetWizard();
		$personType = $wizard->GetVar("personType");
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_PAY_SYSTEM_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemСash"))).' <label for="paysystemСash">'.GetMessage("WIZ_PAY_SYSTEM_CASH").'</label><br />
					'.$this->ShowCheckboxField('paysystem[cred]', 'Y', (array("id" => "paysystemCred"))).' <label for="paysystemCred">'.GetMessage("WIZ_PAY_SYSTEM_CRED").'</label><br />
					'.$this->ShowCheckboxField('paysystem[webm]', 'Y', (array("id" => "paysystemWebm"))).' <label for="paysystemWebm">'.GetMessage("WIZ_PAY_SYSTEM_WEBM").'</label><br />
					'.$this->ShowCheckboxField('paysystem[yand]', 'Y', (array("id" => "paysystemYand"))).' <label for="paysystemYand">'.GetMessage("WIZ_PAY_SYSTEM_YAND").'</label><br />
					'.$this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemSber"))).' <label for="paysystemSber">'.GetMessage("WIZ_PAY_SYSTEM_SBER").'</label><br />
					'.$this->ShowCheckboxField('paysystem[scht]', 'Y', (array("id" => "paysystemScht"))).' <label for="paysystemScht">'.GetMessage("WIZ_PAY_SYSTEM_SCHT").'</label><br />
				</div>
			</div>
			'.GetMessage("WIZ_PAY_SYSTEM").'
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("WIZ_DELIVERY_TITLE").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					'.$this->ShowCheckboxField('delivery[pochta]', 'Y', (array("id" => "deliveryPochta"))).' <label for="deliveryPochta">'.GetMessage("WIZ_DELIVERY_POCHTA").'</label><br />
					'.$this->ShowCheckboxField('delivery[kurier]', 'Y', (array("id" => "deliveryKerier"))).' <label for="deliveryKerier">'.GetMessage("WIZ_DELIVERY_KURIER").'</label><br />
					'.$this->ShowCheckboxField('delivery[samovizov]', 'Y', (array("id" => "deliverySamovizov"))).' <label for="deliverySamovizov">'.GetMessage("WIZ_DELIVERY_SAMOVIZOV").'</label><br />
				</div>
			</div>
			'.GetMessage("WIZ_DELIVERY").'
		</div>';
		$this->content .= '<div class="wizard-input-form-block">'.GetMessage("WIZ_DELIVERY_HINT").'</div>';

		$this->content .= '</div>';
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