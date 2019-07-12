<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		parent::InitStep();

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "taxi";
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
		$wizard->solutionName = "taxi";
		parent::InitStep();

		$templateID = $wizard->GetVar("templateID");
		$themeID = $wizard->GetVar($templateID."_themeID");

		$siteLogo = "/bitrix/wizards/colors3/taxi/site/templates/taxi/images/logo.gif";

		
//----------------------------------------------------------------------		
		$wizard->SetDefaultVars(Array("siteNameSet" => true));
			
		$wizard->SetDefaultVars(
				Array(
						"siteNameCity" => GetMessage("WIZ_TEMPLATE_FILED1DESCR"),
						"siteCodeCity" => GetMessage("WIZ_TEMPLATE_FILED2DESCR"),
						"siteTelephone" => GetMessage("WIZ_TEMPLATE_FILED3DESCR"),
						//"siteMap" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
						"siteNameTaxi" => GetMessage("WIZ_TEMPLATE_FIELD4DESCR"),
						"siteDescription" => GetMessage("WIZ_TEMPLATE_DESCRIPTION"),					
				)
		);
//--------------------------------------------------------------------		
		
		
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
				
		$siteLogo = $wizard->GetVar("siteLogo", true);

		//$this->content .= $this->ShowHiddenField("installDemoData","Y");
		
		
//----------------------------------------------------------------------------		
		$this->content .= '<div class="wizard-input-form">';
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteNameCity">'.GetMessage("WIZ_TEMPLATE_FIELD1").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteNameCity', array("id" => "siteNameCity")).'</div>
			</div>
		</div>';
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteCodeCity">'.GetMessage("WIZ_TEMPLATE_FIELD2").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteCodeCity', array("id" => "siteCodeCity")).'</div>
			</div>
		</div>';
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephone">'.GetMessage("WIZ_TEMPLATE_FIELD3").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone")).'</div>
			</div>
		</div>';
		
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteName">'.GetMessage("WIZ_TEMPLATE_FIELD4").'</label></h4>
			<div>
				<div class="wizard-catalog-form-item">
					'. $this->ShowRadioField("siteMap", "select", (array("id" => "siteMapYandex")))
					.'<label for="catalog-sku-select">'.GetMessage("WIZ_MAP_YANDEX").'</label><br />'
					.'<p>'.GetMessage("WIZ_MAP_YANDEX_DESCR").'</p>
				</div>
				<div class="wizard-catalog-form-item">
					'. $this->ShowRadioField("siteMap", "list", (array("id" => "siteMapGoogle")))
					.'<label for="catalog-sku-list">'.GetMessage("WIZ_MAP_GOOGLE").'</label><br />'
					.'<p>'.GetMessage("WIZ_MAP_GOOGLE_DESCR").'</p>
				</div>
			</div>
		</div>';
		
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteNameTaxi">'.GetMessage("WIZ_TEMPLATE_FIELD5").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteNameTaxi', array("id" => "siteNameTaxi")).'</div>
			</div>
		</div>';
						
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteDescription">'.GetMessage("WIZ_TEMPLATE_FIELD6").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'siteDescription', array("rows"=>"3", "id" => "siteDescription")).'</div>
			</div>
		</div>';
		
		
		
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
		$this->content .= $this->ShowHiddenField("installDemoData","Y");
		
				
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
//---------------------------------------------------------------
		
		
		$formName = $wizard->GetFormName();
		$installCaption = $this->GetNextCaption();
		$nextCaption = GetMessage("NEXT_BUTTON");
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();							
		
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