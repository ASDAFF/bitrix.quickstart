<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
	function InitStep()
	{
		$this->SetStepID("select_site");
		$this->SetTitle(GetMessage("SELECT_SITE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_SITE_SUBTITLE"));
		$this->SetNextStep("site_settings");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "store.mobile_hh";
		$wizard->SetDefaultVars(
			Array(
				"siteFolder" => "m",
				"siteID" => $_REQUEST["site_id"],
			)
		);

	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$siteID = $wizard->GetVar("siteID", true);
			$siteFolder = str_replace(array("\\", "///", "//"), "/", "/".$wizard->GetVar("siteFolder")."/");
			
			if (strlen($siteID) > 0)
			{
				$db_res = CSite::GetList($by="sort", $order="desc", array("LID" => $siteID));
				if (!($db_res && $res = $db_res->Fetch()))
					$this->SetError(GetMessage("wiz_site_id_not_exists_error"));
				return;
			}
			else
			{
				$siteID = WizardServices::GetCurrentSiteID();
				$wizard->SetVar("siteID", $siteID);
			}
			if(strlen($siteFolder) <= 0 || $siteFolder == "/")
			{
				$this->SetError(GetMessage("wiz_site_id_site_folder_error"));
				return false;
			}
			else
			{
				$wizard->SetVar("siteFolder", $siteFolder);
			}
		}
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$arSites = array(); 
		$arSitesSelect = array(); 
		$db_res = CSite::GetList($by="sort", $order="desc", array());
		if ($db_res && $res = $db_res->GetNext())
		{
			do 
			{
				$site = $res["ID"];
				$arSites[$res["ID"]] = $res; 
				$arSitesSelect[$res["ID"]] = '['.$res["ID"].'] '.$res["NAME"];
			} while ($res = $db_res->GetNext()); 
		}
		$siteID = $wizard->GetVar("siteID", true);
		if(strlen($site) <= 0)
			$siteID = $site;
		$this->content .= '<div id="solutions-container">';
			$this->content .= "<div ";
				$this->content .= 'class="solution-item'.($createSite != "Y" ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
				if (count($arSites) < 2)
				{
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title").' '.implode("", $arSitesSelect).'</p>'; 
					$this->content .= '<input type="hidden" name="siteID" value="'.$siteID.'">'; 
				}
				else
				{
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title");
					$this->content .= ": ". $this->ShowSelectField("siteID", $arSitesSelect)."</p>";
				}
				$this->content .= '<p>';
					$this->content .= str_replace(
						"#SITE_DIR#", 
						$this->ShowInputField("text", "siteFolder", array("id" => "siteFolder")), 
						GetMessage("wiz_site_sol_folder")); 
				$this->content .= '</p>'; 
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</div>';
		
		$this->content .= '</div>';
	}
}


class SiteSettingsStep extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "store.mobile_hh";
		parent::InitStep();
		
		$siteID = $wizard->GetVar("siteID", true);
		if(COption::GetOptionString("bitrix.household", "wizard_installed", "N", $siteID) != "Y")
		{
			$this->SetError(GetMessage("wiz_site_no_store_error"));
			return false;
		}

		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$this->SetNextStep("data_install");
		$this->SetPrevStep("select_site");

		$siteName = COption::GetOptionString("store", "siteName", GetMessage("WIZ_COMPANY_NAME_DEF"), $siteID);
		if(strlen($siteName) <= 0)
			$siteName = GetMessage("WIZ_COMPANY_NAME_DEF");
		$wizard->SetDefaultVars(
			Array(
				"siteName" => $siteName,
				"siteTelephone" => COption::GetOptionString("store", "siteTelephone", GetMessage("WIZ_COMPANY_TELEPHONE_DEF"), $siteID),
				"shopEmail" => COption::GetOptionString("store", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"siteLogo" => "/bitrix/wizards/bitrix/store.mobile/site/public/".LANGUAGE_ID."/images/logo.jpg",
			)
		);
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteLogo = $wizard->GetVar("siteLogo", true);
		
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
			<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_LOGO").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.CFile::ShowImage($siteLogo, 280, 40, "border=0 vspace=15") . '<br>' . $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "siteLogo")).'</div>
			</div>
		</div>';
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephone">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone")).'</div>
			</div>
		</div>';
		$this->content .= '<div class="wizard-input-form-block">
			<h4><label for="shopEmail">'.GetMessage("WIZ_SHOP_EMAIL").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail")).'</div>
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

class DataInstallStep extends CDataInstallWizardStep
{
	function CorrectServices(&$arServices)
	{
		$wizard =& $this->GetWizard();
		if($wizard->GetVar("installDemoData") != "Y")
		{
		}
	}
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$serviceID = $wizard->GetVar("nextStep");
		$serviceStage = $wizard->GetVar("nextStepStage");

		if ($serviceID == "finish")
		{
			$wizard->SetCurrentStep("finish");
			return;
		}
	
	
		$arServices = WizardServices::GetServices($_SERVER["DOCUMENT_ROOT"].$wizard->GetPath(), "/site/services/");

		$this->CorrectServices($arServices);

		if ($serviceStage == "skip")
			$success = true;
		else
			$success = $this->InstallService($serviceID, $serviceStage);

		list($nextService, $nextServiceStage, $stepsComplete, $status) = $this->GetNextStep($arServices, $serviceID, $serviceStage);

		if ($nextService == "finish")
		{
			$formName = $wizard->GetFormName();
			$response = "window.ajaxForm.StopAjax(); window.ajaxForm.SetStatus('100'); window.ajaxForm.Post('".$nextService."', '".$nextServiceStage."','".$status."');";
 
		}
		else
		{
			$arServiceID = array_keys($arServices);
			$lastService = array_pop($arServiceID);
			$stepsCount = $arServices[$lastService]["POSITION"];
			if (array_key_exists("STAGES", $arServices[$lastService]) && is_array($arServices[$lastService]))
				$stepsCount += count($arServices[$lastService]["STAGES"])-1;

			$percent = round($stepsComplete/$stepsCount * 100);
			$response = "window.ajaxForm.SetStatus('".$percent."'); window.ajaxForm.Post('".$nextService."', '".$nextServiceStage."','".$status."');";
		}
		die("[response]".$response."[/response]");
		
	}
	
}

class FinishStep extends CFinishWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$wizard->solutionName = "store.mobile_hh";
		parent::InitStep();
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
		$rsSites = CSite::GetByID($siteID);
		$siteDir = "/"; 
		if ($arSite = $rsSites->Fetch())
			$siteDir = $arSite["DIR"]; 

		$siteFolder = $wizard->GetVar("siteFolder");
		$wizard->SetFormActionScript(str_replace("//", "/", $siteDir.$siteFolder."/?finish"));
		
		COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID); 
		
		$this->content .= GetMessage("FINISH_STEP_CONTENT");
		
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");		
		
	}

}
?>