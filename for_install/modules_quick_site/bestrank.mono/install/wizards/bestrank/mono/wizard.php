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
		$wizard->solutionName = "mono";
	}
}

class SelectTemplateStep extends CSelectTemplateWizardStep
{
	function InitStep()
	{
		$this->SetStepID("template");
		$this->SetNextStep("theme");
		$this->SetTitle(GetMessage("WIZ_TEMPLATE_TITLE"));
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
		$this->SetNextStep("site_options");
		$this->SetTitle(GetMessage("WIZ_TEMPLATE_THEME"));
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("template");
	}
}


class SiteParameters extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();

		$siteID = $wizard->GetVar("siteID"); 


		$wizard->SetDefaultVars(Array("siteLogoSet" => true));

		$wizard->SetDefaultVars(
			Array(
				"EMAIL_FROM" => COption::GetOptionString("main", "EMAIL_FROM", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"siteName" => $this->GetFileContent(WIZARD_SITE_PATH."include/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
				"siteSchedule" => $this->GetFileContent(WIZARD_SITE_PATH."include/schedule.php", GetMessage("WIZ_COMPANY_SCHEDULE_DEF")),
				"siteTelephone" => $this->GetFileContent(WIZARD_SITE_PATH."include/telephone.php", GetMessage("WIZ_COMPANY_TELEPHONE_DEF")),
				"siteAddress" => $this->GetFileContent(WIZARD_SITE_PATH."include/address.php", GetMessage("WIZ_COMPANY_ADDRESS_DEF")),
				"siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH."include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
				"siteMetaDescription" => GetMessage("wiz_site_desc"),
				"siteMetaKeywords" => GetMessage("wiz_keywords"),
				"shopFacebook" => COption::GetOptionString("mono", "shopFacebook", "http://www.facebook.com/bestrankru", $siteID),
				"shopTwitter" => COption::GetOptionString("mono", "shopTwitter", "http://twitter.com/bestrankru", $siteID),
				"shopVk" => COption::GetOptionString("mono", "shopVK", "http://vk.com/bestrank", $siteID),


			)
		);

		$wizard->SetDefaultVars($def_site_vars);

		$this->SetStepID("site_options");
		$this->SetNextStep("search_iblocks");
		$this->SetTitle(GetMessage("WIZ_MAIN_PARAMETERS"));
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("theme");
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$siteID = $wizard->GetVar("siteID"); 
		//$this->content = $siteID." - ".WIZARD_TEMPLATE_RELATIVE_PATH." - ".LANGUAGE_ID;

		$this->content .= $this->ShowHiddenField("installDemoData","Y");

		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_NAME").":</h3>";
		$this->content .= $this->ShowInputField("text", "siteName",  array('style' => 'width:90%', "id" => "siteName"))."<br /><br />";



		if($wizard->GetVar('siteLogoSet', true)){
			$siteLogo = $wizard->GetVar("siteLogo", true);
			$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_LOGO").":</h3>";
			$this->content .= CFile::ShowImage($siteLogo, 280, 40, "border=0 vspace=15") . '<br>' . $this->ShowFileField("siteLogo", Array("show_file_info" => "Y", "id" => "siteLogo"))."<br /><br />";

		}



		$this->content .= "<h3>". GetMessage("WIZ_SHOP_PARAMETERS_EMAIL_FROM").":</h3>";
		$this->content .= $this->ShowInputField("text", "EMAIL_FROM",  array('style' => 'width:90%', "id" => "EMAIL_FROM"))."<br /><br />";


		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_SCHEDULE").":</h3>";
		$this->content .= $this->ShowInputField("textarea", "siteSchedule",  array('style' => 'width:90%', "id" => "siteSchedule"))."<br /><br />";


		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_PHONE").":</h3>";
		$this->content .= $this->ShowInputField("text", "siteTelephone",  array('style' => 'width:90%', "id" => "siteTelephone"))."<br /><br />";


		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_ADDRESS").":</h3>";
		$this->content .= $this->ShowInputField("textarea", "siteAddress",  array('style' => 'width:90%', "id" => "siteAddress"))."<br /><br />";

		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_COPY").":</h3>";
		$this->content .= $this->ShowInputField("textarea", "siteCopy",  array('style' => 'width:90%', "id" => "siteCopy"))."<br /><br />";


		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_META_DESCR").":</h3>";
		$this->content .= $this->ShowInputField("textarea", "siteMetaDescription",  array('style' => 'width:90%', "id" => "siteMetaDescription"))."<br /><br />";

		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_META_KEYWORDS").":</h3>";
		$this->content .= $this->ShowInputField("textarea", "siteMetaKeywords",  array('style' => 'width:90%', "id" => "siteMetaKeywords"))."<br /><br />";

		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_FB").":</h3>";
		$this->content .= $this->ShowInputField("text", "shopFacebook",  array('style' => 'width:90%', "id" => "shopFacebook"))."<br /><br />";

		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_TWITTER").":</h3>";
		$this->content .= $this->ShowInputField("text", "shopTwitter",  array('style' => 'width:90%', "id" => "shopTwitter"))."<br /><br />";


		$this->content .= "<h3>". GetMessage("WIZ_MAIN_PARAMETERS_SITE_VK").":</h3>";
		$this->content .= $this->ShowInputField("text", "shopVk",  array('style' => 'width:90%', "id" => "shopVk"))."<br /><br />";



		$formName = $wizard->GetFormName();
	}
	function OnPostForm()
	{
		$wizard =& $this->GetWizard();
		$res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 100, "max_width" => 240, "make_preview" => "Y"));
	}
}

class SearchIblocks extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();

		$siteID = $wizard->GetVar("siteID"); 

		$wizard->SetDefaultVars($def_site_vars);

		$this->SetStepID("search_iblocks");
		$this->SetNextStep("shop_options");
		$this->SetTitle(GetMessage("WIZ_SEARCH_IBLOCKS"));
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("site_options");
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();

		$siteID = $wizard->GetVar("siteID"); 



		if(CModule::IncludeModule('iblock')){

			$suffix = 'bestrank_mono';
			$iblocks = array(
				'news_'.$suffix=>array('type'=>'news', 'macros'=>'IBLOCK_ID_NEWS'),
				'manufacturers_'.$suffix=>array('type'=>'services', 'macros'=>'IBLOCK_ID_MANUFACTURERS', 'PROPERTY_LINKED'=>'MANUFACTURER'),
				'catalog_'.$suffix=>array('type'=>'catalog', 'macros'=>'IBLOCK_ID_CATALOG', 'PROPERTY_LINKED'=>'RECOMMEND'),
			);
			$wizard->SetVar("module_iblocks", serialize($iblocks));

			$ibArray = array();
			foreach($iblocks as $ibCode=>$iblock) {
				$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $ibCode."_".$siteID, "TYPE" => $iblock['type'] ));
				if ($arIBlock = $rsIBlock->Fetch())
				{
					$ibArray[$arIBlock["ID"]] = array("NAME"=> $arIBlock["NAME"], "ibCode"=>$ibCode); 
				}
			}

			if(count($ibArray)==0){
				$this->content .= "<p>".GetMessage("WIZ_SEARCH_IBLOCKS_NO_IBLOCKS_INSTALLED")."</p>";
				$this->content .= "<script>setTimeout('SubmitForm(\'next\')', 5000);</script>";
			}else{
				$this->content .= "<p>".GetMessage("WIZ_SEARCH_IBLOCKS_REPLACE_IBLOCKS")."</p>";
				foreach($ibArray as $ib=>$nm){
					$this->content .= $this->ShowCheckboxField('iblock_to_rewrite_'.$nm["ibCode"], 'Y', (array("id" => 'iblock_to_rewrite_'.$nm["ibCode"]))).' <label for="iblock_to_rewrite_'.$nm["ibCode"].'">'.$nm["NAME"].'</label><br />';
				}
			}



		} else {
			$this->SetError(GetMessage('WIZ_SEARCH_IBLOCKS_NO_IBLOCKS'));
		}

		$formName = $wizard->GetFormName();
	}


}

class ShopParameters extends CSiteSettingsWizardStep
{
	function InitStep()
	{
		parent::InitStep();
		$wizard = $this->GetWizard();

		$siteID = $wizard->GetVar("siteID"); 


		$siteStamp =$wizard->GetPath()."/site/templates/minimal/images/pechat.gif";

		$wizard->SetDefaultVars(
			Array(

				"location_zip" => COption::GetOptionString("sale", "location_zip", "111111", $siteID),


				"shopLocalization" => COption::GetOptionString("mono", "shopLocalization", "ru", $siteID),
				"shopEmail" => COption::GetOptionString("mono", "shopEmail", "sale@".$_SERVER["SERVER_NAME"], $siteID),
				"shopOfName" => COption::GetOptionString("mono", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
				"shopLocation" => COption::GetOptionString("mono", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
				"shopAdr" => COption::GetOptionString("mono", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
				"shopINN" => COption::GetOptionString("mono", "shopINN", "1234567890", $siteID),
				"shopKPP" => COption::GetOptionString("mono", "shopKPP", "123456789", $siteID),
				"shopNS" => COption::GetOptionString("mono", "shopNS", "0000 0000 0000 0000 0000", $siteID),
				"shopBANK" => COption::GetOptionString("mono", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
				"shopBANKREKV" => COption::GetOptionString("mono", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
				"shopKS" => COption::GetOptionString("mono", "shopKS", "00000 000 0 0000 0000000", $siteID),
				"siteStamp" => COption::GetOptionString("mono", "siteStamp", $siteStamp, $siteID),

				"installPriceBASE" => COption::GetOptionString("mono", "installPriceBASE", "Y", $siteID),
			



			)
		);

		$wizard->SetDefaultVars($def_site_vars);

		$this->SetStepID("shop_options");
		$this->SetNextStep("person_type");
		$this->SetTitle(GetMessage("WIZ_SHOP_PARAMETERS"));
		$this->SetNextCaption(GetMessage("NEXT_BUTTON"));
		$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
		$this->SetPrevStep("search_iblocks");
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$siteStamp = $wizard->GetVar("siteStamp", true);

		$siteID = $wizard->GetVar("siteID"); 

	        if (!CModule::IncludeModule("catalog"))
	        {
	            $this->content .= "<p style='color:red'>".GetMessage("WIZ_NO_MODULE_CATALOG")."</p>";
	            $this->SetNextStep("shop_settings");
	        }
	        else
	        {



			$this->content .= "<h3>". GetMessage("WIZ_SHOP_PARAMETERS_INDEX").":</h3>";
			$this->content .= $this->ShowInputField("text", "location_zip",  array('style' => 'width:90%', "id" => "location_zip"))."<br /><br />";
	
	
			$this->content .= '<h3>'.GetMessage("WIZ_SHOP_EMAIL").'</h3>';
			$this->content .= $this->ShowInputField('text', 'shopEmail', array('style' => 'width:90%', "id" => "shopEmail"));
	
	
			$this->content .= '<h3>'.GetMessage("WIZ_SHOP_OF_NAME").'</h3>';
			$this->content .= $this->ShowInputField('text', 'shopOfName', array('style' => 'width:90%', "id" => "shopOfName"));
	
			$this->content .= '<h3>'.GetMessage("WIZ_SHOP_LOCATION").'</h3>';
			$this->content .= $this->ShowInputField('text', 'shopLocation', array('style' => 'width:90%', "id" => "shopLocation"));
	
			$this->content .= '<h3>'.GetMessage("WIZ_SHOP_ADR").'</h3>';
			$this->content .= $this->ShowInputField('textarea', 'shopAdr', array('style' => 'width:90%', "rows"=>"5", "id" => "shopAdr"));
	 
			$this->content .= '<h3>'.GetMessage("WIZ_SHOP_BANK_TITLE").'</h3>
				<table  class="data-table-no-border" >
					<tr>
						<th width="35%">'.GetMessage("WIZ_SHOP_INN").':</th>
						<td width="65%">'.$this->ShowInputField('text', 'shopINN',  array('style' => 'width:90%')).'</td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_KPP").':</th>
						<td>'.$this->ShowInputField('text', 'shopKPP',  array('style' => 'width:90%')).'</td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_NS").':</th>
						<td>'.$this->ShowInputField('text', 'shopNS',  array('style' => 'width:90%')).'</td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANK").':</th>
						<td>'.$this->ShowInputField('text', 'shopBANK',  array('style' => 'width:90%')).'</td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_BANKREKV").':</th>
						<td>'.$this->ShowInputField('text', 'shopBANKREKV',  array('style' => 'width:90%')).' </td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_KS").':</th>
						<td>'.$this->ShowInputField('text', 'shopKS',  array('style' => 'width:90%')).' </td>
					</tr>
					<tr>
						<th>'.GetMessage("WIZ_SHOP_STAMP").':</th>
							<td> '.$this->ShowFileField("siteStamp", Array("show_file_info"=> "N", "id" => "siteStamp", "style" => "width: 90%; ")).'<br />'.CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false).'</td>
						</tr>
				</table>

			';
		}

			if (CModule::IncludeModule("catalog"))
			{
				$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y", "GROUP_ID"=>2));
				if (!$db_res->Fetch())
				{
					$this->content .= '
						<h3><label for="shopAdr">'.GetMessage("WIZ_SHOP_PRICE_BASE_TITLE").'</label></h3>
							'. GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") .'<br><br>
							'. $this->ShowCheckboxField("installPriceBASE", "Y",
							(array("id" => "install-demo-data")))
							. ' <label for="install-demo-data">'.GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2").'</label><br />

					';
				}
			}


		$formName = $wizard->GetFormName();
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
		$this->SetPrevStep("shop_options");
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

		$this->content .= "<h3>". GetMessage("WIZ_PERSON_TYPE_TITLE").":</h3>";
		$this->content .= $this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))).' <label for="personTypeF">'.GetMessage("WIZ_PERSON_TYPE_FIZ").'</label><br />
				'.$this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))).' <label for="personTypeU">'.GetMessage("WIZ_PERSON_TYPE_UR").'</label><br />';
		$this->content .= '<p>'.GetMessage("WIZ_PERSON_TYPE").'</p>';
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
		$currentLocalization = $wizard->GetVar("shopLocalization", true);
		if (empty($currentLocalization))
			$currentLocalization = $wizard->GetDefaultVar("shopLocalization");

		$siteID = $wizard->GetVar("siteID"); 

		$personType = $wizard->GetVar("personType");
		
		$this->content .='<h3>'.GetMessage("WIZ_PAY_SYSTEM_TITLE").'</h3>';
		$this->content .= $this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))).' <label for="paysystemC">'.GetMessage("WIZ_PAY_SYSTEM_C").'</label><br />';

				if(LANGUAGE_ID == "ru")
				{
					if ($personType["fiz"] == "Y")
						$this->content .= $this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_S").'</label><br />';
					if($personType["ur"] == "Y")
						$this->content .= $this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))).' <label for="paysystemB">'.GetMessage("WIZ_PAY_SYSTEM_B").'</label><br />';
				}
				else
				{
					$this->content .= $this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))).' <label for="paysystemP">PayPal</label><br />';
				}

		$this->content .= '<p>'.GetMessage("WIZ_PAY_SYSTEM").'</p>';


		$this->content .= '<h3>'.GetMessage("WIZ_DELIVERY_TITLE").'</h3>';
		$this->content .= $this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))).' <label for="deliveryC">'.GetMessage("WIZ_DELIVERY_C").'</label><br />';
		$this->content .= $this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))).' <label for="deliveryS">'.GetMessage("WIZ_DELIVERY_S").'</label><br />';
					if(LANGUAGE_ID == "ru")
					{
							$this->content .= $this->ShowCheckboxField('delivery[russianpost]', 'Y', (array("id" => "deliveryR"))).' <label for="deliveryR">'.GetMessage("WIZ_DELIVERY_R").'</label><br />';
					}
					else
					{
						$this->content .= $this->ShowCheckboxField('delivery[dhl]', 'Y', (array("id" => "deliveryD"))).' <label for="deliveryD">DHL</label><br />';
						$this->content .= $this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))).' <label for="deliveryU">UPS</label><br />';
					}
		$this->content .= '<p>'.GetMessage("WIZ_DELIVERY").'</p>';

		$this->content .= '<h3>'.GetMessage("WIZ_LOCATION_TITLE").'</h3>';
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

		$this->content .= '<p>'.GetMessage("WIZ_DELIVERY_HINT").'</p>';

	}
}



class DataInstallStep extends CDataInstallWizardStep
{
	function InitStep()
	{
		$this->SetStepID("data_install");
		$this->SetTitle(GetMessage("WIZ_STEP_INSTALL"));
	}
}



class FinishStep extends CFinishWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		COption::SetOptionString("main","email_from",$wizard->GetVar('EMAIL_FROM'));

		$this->SetStepID("finish");
		$this->SetNextStep("finish");
		$this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
		$this->SetNextCaption(GetMessage("wiz_go"));  
	}

	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		   
		
		$siteID = $wizard->GetVar("siteID"); 

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