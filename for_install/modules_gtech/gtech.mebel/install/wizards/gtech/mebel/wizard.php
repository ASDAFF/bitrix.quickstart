<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$this->SetStepID("start");
		$this->SetNextStep("template");
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "mebel";
	}
}


class SelectTemplateStep extends CSelectTemplateWizardStep
{
	function InitStep()
	{
		$this->SetStepID("template");
		$this->SetNextStep("theme");
		$this->SetTitle("Выбор шаблона сайта");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("start");
	}
}

class SelectThemeStep extends CSelectThemeWizardStep
{
	function InitStep()
	{
		$this->SetStepID("theme");
		$this->SetNextStep("options");
		$this->SetTitle("Выбор цветовой схемы сайта");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("template");
	}
}

class InputOptionsStep extends CWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();
		$def_site_vars = array('EMAIL_FROM' => COption::GetOptionString('main','email_from'));
		$wizard->SetDefaultVars($def_site_vars);

		$this->SetStepID("options");
		$this->SetNextStep("person_type");
		$this->SetTitle("Укажите обязательные настройки сайта");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("theme");
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);

		$style_text = array('size' => '0', 'style' => 'width:150px');
		$this->content = "Введите e-mail для форм обратной связи сайта: ";
		$this->content .= $this->ShowInputField("text", "EMAIL_FROM", $style_text + array("id" => "EMAIL_FROM"));
		$formName = $wizard->GetFormName();
	}
}

class PersonType extends CWizardStep
{
	function InitStep()
	{
		$this->SetStepID("person_type");
		$this->SetTitle(GetMessage("WIZ_STEP_PT"));
		$this->SetNextStep("pay_system");
		$this->SetPrevStep("options");
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
		$this->SetNextStep("install");
		if(LANGUAGE_ID != "ru")
			$this->SetPrevStep("person_type");
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
					'.$this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).' <label for="paysystemC">'.GetMessage("WIZ_PAY_SYSTEM_C").'</label><br />';
				if(LANGUAGE_ID == "ru")
				{
				if($personType["fiz"] == "Y")
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
		$this->content .= '<div class="wizard-input-form-block">'.GetMessage("WIZ_DELIVERY_HINT").'</div>';

		$this->content .= '</div>';
	}
}

class DataInstallStep extends CDataInstallWizardStep
{
	function InitStep()
	{
		$this->SetStepID("install");
		$this->SetTitle("Установка данных сайта");
	}
}

class FinishStep extends CFinishWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		COption::SetOptionString("main","email_from",$wizard->GetVar('EMAIL_FROM'));

		$this->SetStepID("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextStep("finish");
		$this->SetNextCaption("Перейти на сайт");
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