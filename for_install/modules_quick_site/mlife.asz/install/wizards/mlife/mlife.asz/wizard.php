<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "mlife.asz";
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
		$wizard->solutionName = "mlife.asz";
		parent::InitStep();
		
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("MLIFE_ASZ_WIZ_STEP1"));
		
		$siteID = $wizard->GetVar("siteID");
		
		$this->SetNextStep("catalog_settings");
		
		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");
		
		$wizard->SetDefaultVars(
			Array(
				"siteName" => GetMessage("MLIFE_ASZ_WIZ_SITENAME"),
				"sitePhone" => "+375 25 777-77-77",
				"siteEmail" => "mlife_development@mail.ru",
				"siteSkype" => "mlife_development",
				"siteCopy" => GetMessage("MLIFE_ASZ_WIZ_KONT_COPY"),
				"siteMetaDescription" => "Meta Description",
				"siteMetaKeywords" => "Meta Keywords"
			)
		);

	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		//kontakt
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">
		<tr><td style="padding:10px 0;text-align:left;text-transform:uppercase;font-weight:bold;color:blue;">'.GetMessage("MLIFE_ASZ_WIZ_KONT").'</tr></td>
		</table>';
		
		//название сайта
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-name" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_NAME").'</label>';
		$this->content .= $this->ShowInputField("text", "siteName", Array("id" => "site-name", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//телефон
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-phone" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_PHONE").'</label>';
		$this->content .= $this->ShowInputField("text", "sitePhone", Array("id" => "site-phone", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//email
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-email" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_ADRESS").'</label>';
		$this->content .= $this->ShowInputField("text", "siteEmail", Array("id" => "site-email", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//Skype
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-skype" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">Skype</label>';
		$this->content .= $this->ShowInputField("text", "siteSkype", Array("id" => "site-skype", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//siteMetaDescription
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-meta1" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">Meta Description</label>';
		$this->content .= $this->ShowInputField("text", "siteMetaDescription", Array("id" => "site-meta1", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//siteMetaKeywords
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-meta2" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">site MetaKeywords</label>';
		$this->content .= $this->ShowInputField("text", "siteMetaKeywords", Array("id" => "site-meta2", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		//siteCopy
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="site-copy" style="color:#000000;display:block;width:100%;padding-bottom:5px;font-weight:bold;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_COPY").'</label>';
		$this->content .= $this->ShowInputField("text", "siteCopy", Array("id" => "site-copy", "style" => "width:100%;font-style:normal;"));
		$this->content .= '</tr></td>';
		$this->content .= '</table>';

		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
	}
}

class SiteSettingsCatalogStep extends CWizardStep
{

	function InitStep()
	{
		$this->SetStepID("catalog_settings");
		$this->SetTitle(GetMessage("MLIFE_ASZ_WIZ_STEP2"));
		$this->SetNextStep("catalog_deliverypay");
		$this->SetPrevStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");

		$wizard->SetDefaultVars(
			Array(
				"catalogCurency" => "BUR",
			)
		);
		
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		
		//kontakt
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">
		<tr><td style="padding:10px 0;text-align:left;text-transform:uppercase;font-weight:bold;color:blue;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_FUNC").'</tr></td>
		</table>';
		
		//валюты
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:10px;">';
		$this->content .= '<tr><td>';
		$this->content .= '<label for="catalogCurency" style="color:#000000;display:block;width:50%;padding-bottom:5px;font-weight:bold;float:left;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_CAT_CUR").'</label>';
		$this->content .= '<select id="catalogCurency" name="__wiz_catalogCurency">
			<option value="BUR">BUR</option>
			<option value="RUB">RUB</option>
			<option value="USD">USD</option>
		</select>';
		$this->content .= '</tr></td>';
		$this->content .= '</table>';
		
		
	}
	
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
	}
	
}

class SiteSettingsDeliveryPay extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("catalog_deliverypay");
		$this->SetTitle(GetMessage("MLIFE_ASZ_WIZ_STEP3"));
		$this->SetNextStep("data_install_options");
		$this->SetPrevStep("catalog_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		$wizard->SetDefaultVars(
			Array(
				"paysystem" => Array(
						"cash" => "Y",
				),
				"delivery" => Array(
						"self" => "Y",
				)
			)
		);
		
		
		
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$personType = $wizard->GetVar("personType");
		
	//	$this->content .=$wizard->GetVar("catalogCurency")."---";
		
		//Доставка
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">
		<tr><td style="padding:10px 0;text-align:left;text-transform:uppercase;font-weight:bold;color:blue;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_PAYSYS").'</tr></td>
		</table>';
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					<div class="wizard-catalog-form-item">
						'.$this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))).
						' <label for="deliveryS">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_PAYSYS_2").'</label>
					</div>';
					$this->content .= '
				</div>
			</div>
		</div>';
		
		//Оплата
		$this->content .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:15px;">
		<tr><td style="padding:10px 0;text-align:left;text-transform:uppercase;font-weight:bold;color:blue;">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_PAYSYS_4").'</tr></td>
		</table>';
		
		$this->content .= '<div class="wizard-input-form">';
		$this->content .= '
		<div class="wizard-input-form-block">
			<div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					<div class="wizard-catalog-form-item">
						'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).
						' <label for="paysystemC">'.GetMessage("MLIFE_ASZ_WIZ_TITLE_PAYSYS_5").'</label>
					</div>';
				$this->content .= '</div>
			</div>
		</div>';
		
	}
	
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$paysystem = $wizard->GetVar("paysystem");
		$delivery = $wizard->GetVar("delivery");
		if (empty($paysystem["cash"]))
			$this->SetError(GetMessage("MLIFE_ASZ_WIZ_ERROROPL"));
		if (empty($delivery["courier"]) && empty($delivery["self"]))
			$this->SetError(GetMessage("MLIFE_ASZ_WIZ_ERROROPL2"));
	}
	
}


class DataInstallOptions extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("data_install_options");
		$this->SetTitle(GetMessage("MLIFE_ASZ_WIZ_STEP4"));
		$this->SetNextStep("data_install");
		$this->SetPrevStep("catalog_deliverypay");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

		$wizard =& $this->GetWizard();
		$siteID = $wizard->GetVar("siteID");
		
		
		
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		//демо данные
		$firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7)  . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID")); 
		if($firstStep == "Y")
		{
		
			$this->content .= '
			<div class="wizard-input-form-block">
				'.$this->ShowCheckboxField(
							"installDemoData", 
							"Y", 
							(array("id" => "installDemoData", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"))
						).'
				<label for="installDemoData">'.GetMessage("MLIFE_ASZ_WIZ_DEMO_TITLE").'</label>
			</div>';
		
		}
		else{
			$this->content .= $this->ShowHiddenField("installDemoData","Y");
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
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");
	}

}
?>