<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{

	function InitStep()
	{
		$this->SetStepID("select_site");
		$this->SetTitle(GetMessage("SELECT_SITE_TITLE"));
		$this->SetSubTitle(GetMessage("SELECT_SITE_SUBTITLE"));
		$this->SetNextStep("select_template");
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));

		$wizard =& $this->GetWizard();
		$wizard->solutionName = "store_hh";
	}

	function OnPostForm()
	{
		$wizard =& $this->GetWizard();

		if ($wizard->IsNextButtonClick())
		{
			$siteID = $wizard->GetVar("siteID");
			$siteFolder = str_replace(array("\\", "///", "//"), "/", "/".$wizard->GetVar("siteFolder")."/");
			$siteNewID = $wizard->GetVar("siteNewID");
			$createSite = $wizard->GetVar("createSite");
			
			if ($createSite == "Y")
			{
				if (strlen($siteNewID) != 2)
				{
					$this->SetError(GetMessage("wiz_site_id_error"));
					return;
				}
				$rsSites = CSite::GetList($by="sort", $order="desc", array());
				while($arSite = $rsSites->Fetch())
				{
					if (trim($arSite["DIR"], "/") == trim($siteFolder, "/"))
					{
						$this->SetError(GetMessage("wiz_site_folder_already_exists"));
						$bError = true;
					}

					if ($arSite["ID"] == trim($siteNewID))
					{
						$this->SetError(GetMessage("wiz_site_id_already_exists"));
						$bError = true;
					}
				}
				if ($bError)
					return; 
				$wizard->SetVar("siteID", $siteNewID);
				$wizard->SetVar("siteCreate", "Y"); 
				$wizard->SetVar("siteFolder", $siteFolder); 
			}
			elseif (strlen($siteID) > 0)
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
		}
	}

	function ShowStep()
	{
		$storeSiteID='bt';
		$wizard =& $this->GetWizard();
		$arSites = array(); 
		$arSitesSelect = array(); 
		$db_res = CSite::GetList($by="sort", $order="desc", array('ID'=>$storeSiteID));
		if ($db_res && $res = $db_res->GetNext())
		{
			do 
			{
				$arSites[$res["ID"]] = $res; 
				$arSitesSelect[$res["ID"]] = '['.$res["ID"].'] '.$res["NAME"];
			} while ($res = $db_res->GetNext()); 
		}
		
		$createSite = $wizard->GetVar("createSite"); 
		$createSite = ($createSite == "Y" ? "Y" : "N"); 
		
		
$this->content = 
'<script type="text/javascript">
function SelectCreateSite(element, solutionId)
{
	var container = document.getElementById("solutions-container");
	var nodes = container.childNodes;
	for (var i = 0; i < nodes.length; i++)
	{
		if (!nodes[i].className)
			continue;
		nodes[i].className = "solution-item";
	}
	element.className = "solution-item solution-item-selected";
	var check = document.getElementById("createSite" + solutionId);
	if (check)
		check.checked = true;
}
</script>';
		$this->content .= '<div id="solutions-container">';
			$this->content .= "<div onclick=\"SelectCreateSite(this, 'N');\" ";
				$this->content .= 'class="solution-item'.($createSite != "Y" ? " solution-item-selected" : "").'">'; 
				$this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				$this->content .= '<div class="solution-inner-item">'; 
					$this->content .= $this->ShowRadioField("createSite", "N", (array("id" => "createSiteN", "class" => "solution-radio") + 
						($createSite != "Y" ? array("checked" => "checked") : array()))); 
					$this->content .= '<h4>'.GetMessage("wiz_site_existing").'</h4>'; 
				if (count($arSites) < 2)
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title").' '.implode("", $arSitesSelect).'</p>'; 
				else
				{
					$this->content .= '<p>'.GetMessage("wiz_site_existing_title");
					$this->content .= "<br />". $this->ShowSelectField("siteID", $arSitesSelect)."</p>";
				}
				$this->content .= '</div>'; 
				$this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			$this->content .= '</div>';
		// if (count($arSites) < COption::GetOptionInt("main", "PARAM_MAX_SITES", 100) || COption::GetOptionInt("main", "PARAM_MAX_SITES", 100) <= 0)
		// {
			// $this->content .= "<div onclick=\"SelectCreateSite(this, 'Y');\" ";
				// $this->content .= 'class="solution-item'.($createSite == "Y" ? " solution-item-selected" : "").'">'; 
				// $this->content .= '<b class="r3"></b><b class="r1"></b><b class="r1"></b>'; 
				// $this->content .= '<div class="solution-inner-item">'; 
					// $this->content .= $this->ShowRadioField("createSite", "Y", (array("id" => "createSiteY", "class" => "solution-radio") + 
						// ($createSite == "Y" ? array("checked" => "checked") : array()))); 
					// $this->content .= '<h4>'.GetMessage("wiz_site_new").'</h4>'; 
					// $this->content .= '<p>';
						// $this->content .= str_replace(
							// array(
								// "#SITE_ID#", 
								// "#SITE_DIR#"), 
							// array(
								// $this->ShowInputField("text", "siteNewID", array("size" => 2, "maxlength" => 2, "id" => "siteNewID")), 
								// $this->ShowInputField("text", "siteFolder", array("id" => "siteFolder"))), 
							// GetMessage("wiz_site_new_title")); 
					// $this->content .= '</p>'; 
				// $this->content .= '</div>'; 
				// $this->content .= '<b class="r1"></b><b class="r1"></b><b class="r3"></b>'; 
			// $this->content .= '</div>';
		// }
		$this->content .= '</div>';
	}

	// function InitStep()
	// {
		// parent::InitStep();

		// $wizard =& $this->GetWizard();
		// $wizard->solutionName = "store";
	// }
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
		$wizard->solutionName = "store_hh";
		parent::InitStep();
		

		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

		$siteID = $wizard->GetVar("siteID");
		
		if(COption::GetOptionString("bitrix.household", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
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
		
		$defVars=array(
			"siteName" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
			"siteTelephone" => $this->GetFileContent(WIZARD_SITE_PATH."include/telephone.php", GetMessage("WIZ_COMPANY_TELEPHONE_DEF")),
			"siteTelephoneCode" => $this->GetFileContent(WIZARD_SITE_PATH."include/code.php", GetMessage("WIZ_COMPANY_CODE_DEF")),
			"siteICQ" => $this->GetFileContent(WIZARD_SITE_PATH."include/icq.php", '123-456-789'),
			"shopEmail" => COption::GetOptionString("household", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
			"siteMetaDescription" => GetMessage("wiz_site_desc"),
			"siteMetaKeywords" => GetMessage("wiz_keywords"),
		);
		$defVars['siteTelephone']=strip_tags($defVars['siteTelephone']);
		$defVars['siteICQ']=strip_tags($defVars['siteICQ']);
		$tmp=strpos($defVars['siteTelephone'],')');
		if ($tmp)
		{
			$defVars['siteTelephoneCode']=substr($defVars['siteTelephone'],1,$tmp-1);
			$defVars['siteTelephone']=substr($defVars['siteTelephone'],$tmp+2);
		}
		$wizard->SetDefaultVars($defVars);
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
		
//		if($wizard->GetVar('siteLogoSet', true)){
			$siteLogo = $wizard->GetVar("siteLogo", true);
	
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4><label for="siteName">'.GetMessage("WIZ_COMPANY_LOGO").'</label></h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.CFile::ShowImage($siteLogo, 208, 53, "border=0 vspace=15") . '<br>' . $this->ShowFileField("siteLogo", Array("show_file_info" => "N", "id" => "siteLogo")).'</div>
				</div>
			</div>';
//		}
		
		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteTelephoneCode">'.GetMessage("WIZ_COMPANY_CODE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephoneCode', array("id" => "siteTelephoneCode")).'</div>
			</div>
			<h4><label for="siteTelephone">'.GetMessage("WIZ_COMPANY_TELEPHONE").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone")).'</div>
			</div>
		</div>';

		$this->content .= '
		<div class="wizard-input-form-block">
			<h4><label for="siteICQ">'.GetMessage("WIZ_COMPANY_ICQ").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'siteICQ', array("id" => "siteICQ")).'</div>
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
		$defaultTemplateID = COption::GetOptionString("main", "wizard_template_id", "", $wizard->GetVar("siteID")); 
		if(!empty($defaultTemplateID) && $defaultTemplateID != $wizard->GetVar("templateID")){
			$this->content .= '
			<div class="wizard-input-form-block">
				<h4>'.GetMessage("WIZ_REWRITE_INDEX_DESC").'</h4>
				<div class="wizard-input-form-block-content">'.
						$this->ShowCheckboxField(
							"rewriteIndex", 
							"Y", 
							(array("id" => "rewriteIndex"))
						).
				'
				<label for="rewriteIndex">'.GetMessage("wiz_rewrite_index").'</label>
				</div>
			</div>';	
		}
		$this->content .= '</div>';
	}
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$LogoID = $wizard->GetVar("siteLogo");
		define(WIZARD_SITE_LOGO,$LogoID);
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 53, "max_width" => 208, "make_preview" => "Y"));
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
				"shopEmail" => COption::GetOptionString("household", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("household", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("household", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				//"shopZip" => 101000,
				"shopAdr" => COption::GetOptionString("household", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("household", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("household", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("household", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("household", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("household", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("household", "shopKS", "30101 810 4 0000 0000225", $siteID),
				"siteStamp" => COption::GetOptionString("household", "siteStamp", $siteStamp, $siteID),
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
				</tr>				
				<tr>
					<th>'.GetMessage("WIZ_SHOP_STAMP").':</th>
						<td><div class="">'.$this->ShowFileField("siteStamp", Array("show_file_info"=> "N", "id" => "siteStamp", "style" => "width: 90%; border: solid 1px #CECECE; background-color: #F5F5F5; padding: 3px;")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</div></td>
				</tr>
				</table>
		';	
		
		$wizShopPriceBase = false;
		CModule::IncludeModule("catalog");
		$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
		if ($ar_res = $db_res->Fetch())
		{
		   $wizShopPriceBase = true;
		}
		if(!$wizShopPriceBase){
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
		
		COption::SetOptionString("main", "wizard_solution", 'bitrix.household:store', false, $siteID); 
		
		$this->content .= GetMessage("FINISH_STEP_CONTENT");
		
		if ($wizard->GetVar("installDemoData") == "Y")
			$this->content .= GetMessage("FINISH_STEP_REINDEX");		
			
		
	}

}
?>