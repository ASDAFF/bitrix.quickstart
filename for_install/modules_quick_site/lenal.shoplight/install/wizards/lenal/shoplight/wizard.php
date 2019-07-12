<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep {

    function InitStep() {
        parent::InitStep();

        $wizard = & $this->GetWizard();
        $wizard->solutionName = "shoplight";
    }

}

class SelectTemplateStep extends CSelectTemplateWizardStep {
    
}

class SelectThemeStep extends CSelectThemeWizardStep {
    
}

class SiteSettingsStep extends CSiteSettingsWizardStep {

    function InitStep() {
        $wizard = & $this->GetWizard();
        $wizard->solutionName = "shoplight";
        parent::InitStep();

        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetTitle(GetMessage("WIZ_STEP_SITE_SET"));

        $siteID = $wizard->GetVar("siteID");

        if (COption::GetOptionString("shoplight", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
            $this->SetNextStep("data_install");
        else {
            $this->SetNextStep("catalog_settings");
        }

        $siteLogo = $this->GetFileContentImgSrc(WIZARD_SITE_PATH . "include/company_logo.php", "/bitrix/wizards/lenal/shoplight/site/templates/shoplight/images/logo.png");

        $wizard->SetDefaultVars(
                Array(
                    "siteName" => $this->GetFileContent(WIZARD_SITE_PATH . "include/company_name.php", GetMessage("WIZ_COMPANY_NAME_DEF")),
                    "siteTelephone" => $this->GetFileContent(WIZARD_SITE_PATH . "include/company_support.php", GetMessage("WIZ_COMPANY_TELEPHONE_DEF")),
                    "siteCopy" => $this->GetFileContent(WIZARD_SITE_PATH . "include/copyright.php", GetMessage("WIZ_COMPANY_COPY_DEF")),
                    "siteLogo" => $siteLogo,
                    "siteMetaDescription" => GetMessage("wiz_site_desc"),
                    "siteMetaKeywords" => GetMessage("wiz_keywords"),
                //"installEshopApp" => COption::GetOptionString("shoplight", "installEshopApp", "Y", $siteID),	
                )
        );
    }

    function ShowStep() {
        $wizard = & $this->GetWizard();
        $siteLogo = $wizard->GetVar("siteLogo", true);
        $this->content .= '<div class="wizard-input-form">';

        $this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteName" class="wizard-input-title">' . GetMessage("WIZ_COMPANY_NAME") . '</label>
			' . $this->ShowInputField('text', 'siteName', array("id" => "siteName", "class" => "wizard-field")) . '
		</div>';
        $this->content .= '
		<div class="wizard-upload-img-block">
			<div class="wizard-catalog-title">' . GetMessage("WIZ_COMPANY_LOGO") . '</div>
			' . CFile::ShowImage($siteLogo, 215, 40, "border=0 vspace=5") . '<br/>'
                . $this->ShowFileField("siteLogo", Array(
                    "show_file_info" => "N",
                    "id" => "siteLogo",
                )) . '
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteTelephone" class="wizard-input-title">' . GetMessage("WIZ_COMPANY_TELEPHONE") . '</label>
			' . $this->ShowInputField('text', 'siteTelephone', array("id" => "siteTelephone", "class" => "wizard-field")) . '
		</div>';

        $this->content .= '
		<div class="wizard-input-form-block">
			<label for="siteCopy" class="wizard-input-title">' . GetMessage("WIZ_COMPANY_COPY") . '</label>
			' . $this->ShowInputField('textarea', 'siteCopy', array("rows" => "3", "id" => "siteCopy", "class" => "wizard-field")) . '
		</div>';

        $firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7) . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID"));
        $styleMeta = 'style="display:block"';
        if ($firstStep == "Y")
            $styleMeta = 'style="display:none"';

        $this->content .= '
		<div  id="bx_metadata" ' . $styleMeta . '>
			<div class="wizard-input-form-block">
				<div class="wizard-metadata-title">' . GetMessage("wiz_meta_data") . '</div>
				<label for="siteMetaDescription" class="wizard-input-title">' . GetMessage("wiz_meta_description") . '</label>
				' . $this->ShowInputField("textarea", "siteMetaDescription", Array("id" => "siteMetaDescription", "rows" => "3", "class" => "wizard-field")) . '
			</div>';
        $this->content .= '
			<div class="wizard-input-form-block">
				<label for="siteMetaKeywords" class="wizard-input-title">' . GetMessage("wiz_meta_keywords") . '</label><br>
				' . $this->ShowInputField('text', 'siteMetaKeywords', array("id" => "siteMetaKeywords", "class" => "wizard-field")) . '
			</div>
		</div>';

//install Demo data		
        if ($firstStep == "Y") {
            $this->content .= '
			<div class="wizard-input-form-block">
				' . $this->ShowCheckboxField(
                            "installDemoData", "Y", (array("id" => "installDemoData", "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"))
                    ) . '
				<label for="installDemoData">' . GetMessage("wiz_structure_data") . '</label>
			</div>';
        } else {
            $this->content .= $this->ShowHiddenField("installDemoData", "Y");
        }
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eshopapp") && LANGUAGE_ID == "ru")
            $this->content .= '
			<!--<div class="wizard-input-form-block">
				' . $this->ShowCheckboxField("installEshopApp", "Y", array("id" => "installEshopApp")) .
                    ' <label for="installEshopApp">' . GetMessage("wiz_install_eshopapp") . '</label>
			</div>-->';

        if (LANGUAGE_ID != "ru") {
            CModule::IncludeModule("catalog");
            $db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID" => '1', "BUY" => "Y", "GROUP_ID" => 2));
            if (!$db_res->Fetch()) {
                $this->content .= '
				<div class="wizard-input-form-block">
					<label for="shopAdr">' . GetMessage("WIZ_SHOP_PRICE_BASE_TITLE") . '</label>
					<div class="wizard-input-form-block-content">
						' . GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") . '<br><br>
						' . $this->ShowCheckboxField("installPriceBASE", "Y", (array("id" => "install-demo-data")))
                        . ' <label for="install-demo-data">' . GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2") . '</label><br />
						
					</div>
				</div>';
            }
        }

        $this->content .= '</div>';
    }

    function OnPostForm() {
        $wizard = & $this->GetWizard();
        $res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 40, "max_width" => 220, "make_preview" => "Y"));
    }

}

class CatalogSettings extends CWizardStep {

    function InitStep() {
        $this->SetStepID("catalog_settings");
        $this->SetTitle(GetMessage("WIZ_STEP_CT"));
        if (LANGUAGE_ID != "ru")
            $this->SetNextStep("pay_system");
        else
            $this->SetNextStep("shop_settings");
        $this->SetPrevStep("site_settings");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

        $wizard = & $this->GetWizard();
        $siteID = $wizard->GetVar("siteID");

        $subscribe = COption::GetOptionString("sale", "subscribe_prod", "");
        $arSubscribe = unserialize($subscribe);

        $wizard->SetDefaultVars(
                Array(
                    "catalogSubscribe" => (isset($arSubscribe[$siteID])) ? ($arSubscribe[$siteID]['use'] == "Y" ? "Y" : false) : "Y", //COption::GetOptionString("shoplight", "catalogSubscribe", "Y", $siteID),
                    "catalogView" => COption::GetOptionString("shoplight", "catalogView", "list", $siteID),
                    "useStoreControl" => COption::GetOptionString("catalog", "default_use_store_control", "Y"),
                    "productReserveCondition" => COption::GetOptionString("sale", "product_reserve_condition", "P")
                )
        );
    }

    function ShowStep() {
        $wizard = & $this->GetWizard();

        $this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-catalog-title">' . GetMessage("WIZ_CATALOG_USE_STORE_CONTROL") . '</div>
				<div>
					<div class="wizard-catalog-form-item">
						' . $this->ShowCheckboxField("useStoreControl", "Y", array("id" => "use-store-control"))
                . '<label for="use-store-control">' . GetMessage("WIZ_STORE_CONTROL") . '</label>
					</div>';

        $arConditions = array(
            "O" => GetMessage("SALE_PRODUCT_RESERVE_1_ORDER"),
            "P" => GetMessage("SALE_PRODUCT_RESERVE_2_PAYMENT"),
            "D" => GetMessage("SALE_PRODUCT_RESERVE_3_DELIVERY"),
            "S" => GetMessage("SALE_PRODUCT_RESERVE_4_DEDUCTION")
        );


        foreach ($arConditions as $conditionID => $conditionName) {
            $arReserveConditions[$conditionID] = $conditionName;
        }
        $this->content .= '
			<div class="wizard-catalog-form-item">'
                . $this->ShowSelectField("productReserveCondition", $arReserveConditions) .
                '<label>' . GetMessage("SALE_PRODUCT_RESERVE_CONDITION") . '</label>
			</div>';
        $this->content .= '</div>
			</div>';

        $this->content .= '<script>
			function ImgShw(ID, width, height, alt)
			{
				var scroll = "no";
				var top=0, left=0;
				if(width > screen.width-10 || height > screen.height-28) scroll = "yes";
				if(height < screen.height-28) top = Math.floor((screen.height - height)/2-14);
				if(width < screen.width-10) left = Math.floor((screen.width - width)/2-5);
				width = Math.min(width, screen.width-10);
				height = Math.min(height, screen.height-28);
				var wnd = window.open("","","scrollbars="+scroll+",resizable=yes,width="+width+",height="+height+",left="+left+",top="+top);
				wnd.document.write(
					"<html><head>"+
						"<"+"script type=\"text/javascript\">"+
						"function KeyPress()"+
						"{"+
						"	if(window.event.keyCode == 27) "+
						"		window.close();"+
						"}"+
						"</"+"script>"+
						"<title></title></head>"+
						"<body topmargin=\"0\" leftmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" onKeyPress=\"KeyPress()\">"+
						"<img src=\""+ID+"\" border=\"0\" alt=\""+alt+"\" />"+
						"</body></html>"
				);
				wnd.document.close();
			}
		</script>';
    }

    function OnPostForm() {
        $wizard = & $this->GetWizard();
    }

}

class ShopSettings extends CWizardStep {

    function InitStep() {
        $this->SetStepID("shop_settings");
        $this->SetTitle(GetMessage("WIZ_STEP_SS"));
        $this->SetNextStep("person_type");
        $this->SetPrevStep("catalog_settings");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

        $wizard = & $this->GetWizard();

        $siteStamp = $wizard->GetPath() . "/site/templates/minimal/images/pechat.gif";
        $siteID = $wizard->GetVar("siteID");

        $wizard->SetDefaultVars(
                Array(
                    "shopLocalization" => COption::GetOptionString("shoplight", "shopLocalization", "ru", $siteID),
                    "shopEmail" => COption::GetOptionString("shoplight", "shopEmail", "sale@" . $_SERVER["SERVER_NAME"], $siteID),
                    "shopOfName" => COption::GetOptionString("shoplight", "shopOfName", GetMessage("WIZ_SHOP_OF_NAME_DEF"), $siteID),
                    "shopLocation" => COption::GetOptionString("shoplight", "shopLocation", GetMessage("WIZ_SHOP_LOCATION_DEF"), $siteID),
                    //"shopZip" => 101000,
                    "shopAdr" => COption::GetOptionString("shoplight", "shopAdr", GetMessage("WIZ_SHOP_ADR_DEF"), $siteID),
                    "shopINN" => COption::GetOptionString("shoplight", "shopINN", "1234567890", $siteID),
                    "shopKPP" => COption::GetOptionString("shoplight", "shopKPP", "123456789", $siteID),
                    "shopNS" => COption::GetOptionString("shoplight", "shopNS", "0000 0000 0000 0000 0000", $siteID),
                    "shopBANK" => COption::GetOptionString("shoplight", "shopBANK", GetMessage("WIZ_SHOP_BANK_DEF"), $siteID),
                    "shopBANKREKV" => COption::GetOptionString("shoplight", "shopBANKREKV", GetMessage("WIZ_SHOP_BANKREKV_DEF"), $siteID),
                    "shopKS" => COption::GetOptionString("shoplight", "shopKS", "30101 810 4 0000 0000225", $siteID),
                    "siteStamp" => COption::GetOptionString("shoplight", "siteStamp", $siteStamp, $siteID),
                    //"shopCompany_ua" => COption::GetOptionString("shoplight", "shopCompany_ua", "", $siteID),
                    "shopOfName_ua" => COption::GetOptionString("shoplight", "shopOfName_ua", GetMessage("WIZ_SHOP_OF_NAME_DEF_UA"), $siteID),
                    "shopLocation_ua" => COption::GetOptionString("shoplight", "shopLocation_ua", GetMessage("WIZ_SHOP_LOCATION_DEF_UA"), $siteID),
                    "shopAdr_ua" => COption::GetOptionString("shoplight", "shopAdr_ua", GetMessage("WIZ_SHOP_ADR_DEF_UA"), $siteID),
                    "shopEGRPU_ua" => COption::GetOptionString("shoplight", "shopEGRPU_ua", "", $siteID),
                    "shopINN_ua" => COption::GetOptionString("shoplight", "shopINN_ua", "", $siteID),
                    "shopNDS_ua" => COption::GetOptionString("shoplight", "shopNDS_ua", "", $siteID),
                    "shopNS_ua" => COption::GetOptionString("shoplight", "shopNS_ua", "", $siteID),
                    "shopBank_ua" => COption::GetOptionString("shoplight", "shopBank_ua", "", $siteID),
                    "shopMFO_ua" => COption::GetOptionString("shoplight", "shopMFO_ua", "", $siteID),
                    "shopPlace_ua" => COption::GetOptionString("shoplight", "shopPlace_ua", "", $siteID),
                    "shopFIO_ua" => COption::GetOptionString("shoplight", "shopFIO_ua", "", $siteID),
                    "shopTax_ua" => COption::GetOptionString("shoplight", "shopTax_ua", "", $siteID),
                    "installPriceBASE" => COption::GetOptionString("shoplight", "installPriceBASE", "Y", $siteID),
                )
        );
    }

    function ShowStep() {
        $wizard = & $this->GetWizard();
        $siteStamp = $wizard->GetVar("siteStamp", true);

        if (!CModule::IncludeModule("catalog")) {
            $this->content .= "<p style='color:red'>" . GetMessage("WIZ_NO_MODULE_CATALOG") . "</p>";
            $this->SetNextStep("shop_settings");
        } else {
            $this->content .=
                    '<div class="wizard-catalog-title">' . GetMessage("WIZ_SHOP_LOCALIZATION") . '</div>
				<div class="wizard-input-form-block" >' .
                    $this->ShowSelectField("shopLocalization", array("ru" => GetMessage("WIZ_SHOP_LOCALIZATION_RUSSIA"), "ua" => GetMessage("WIZ_SHOP_LOCALIZATION_UKRAINE"), "kz" => GetMessage("WIZ_SHOP_LOCALIZATION_KAZAKHSTAN")), array("onchange" => "langReload()", "id" => "localization_select", "class" => "wizard-field", "style" => "padding:0 0 0 15px")) . '
				</div>';

            $currentLocalization = $wizard->GetVar("shopLocalization");
            if (empty($currentLocalization))
                $currentLocalization = $wizard->GetDefaultVar("shopLocalization");

            $this->content .= '<div class="wizard-catalog-title">' . GetMessage("WIZ_STEP_SS") . '</div>
				<div class="wizard-input-form">';

            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopEmail">' . GetMessage("WIZ_SHOP_EMAIL") . '</label>
					' . $this->ShowInputField('text', 'shopEmail', array("id" => "shopEmail", "class" => "wizard-field")) . '
				</div>';

            //ru
            $this->content .= '<div id="ru_bank_details" class="wizard-input-form-block" style="display:' . (($currentLocalization == "ru" || $currentLocalization == "kz") ? 'block' : 'none') . '">
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopOfName">' . GetMessage("WIZ_SHOP_OF_NAME") . '</label>'
                    . $this->ShowInputField('text', 'shopOfName', array("id" => "shopOfName", "class" => "wizard-field")) . '
				</div>';

            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopLocation">' . GetMessage("WIZ_SHOP_LOCATION") . '</label>'
                    . $this->ShowInputField('text', 'shopLocation', array("id" => "shopLocation", "class" => "wizard-field")) . '
				</div>';

            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopAdr">' . GetMessage("WIZ_SHOP_ADR") . '</label>'
                    . $this->ShowInputField('textarea', 'shopAdr', array("rows" => "3", "id" => "shopAdr", "class" => "wizard-field")) . '
				</div>';

            $this->content .= '
				<div class="wizard-catalog-title">' . GetMessage("WIZ_SHOP_BANK_TITLE") . '</div>
				<table class="wizard-input-table">
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_INN") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopINN', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_KPP") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopKPP', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_NS") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopNS', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_BANK") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopBANK', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_BANKREKV") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopBANKREKV', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_KS") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopKS', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_STAMP") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowFileField("siteStamp", Array("show_file_info" => "N", "id" => "siteStamp")) . '<br />' . CFile::ShowImage($siteStamp, 75, 75, "border=0 vspace=5", false, false) . '</td>
					</tr>
				</table>
			</div><!--ru-->
			';
            //ua
            /*
              <tr>
              <th width="35%">'.GetMessage("WIZ_SHOP_COMPANY_UA").':</th>
              <td width="65%"><div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'shopCompany_ua').'</div></td>
              </tr>
             */
            $this->content .= '<div id="ua_bank_details" class="wizard-input-form-block" style="display:' . (($currentLocalization == "ua") ? 'block' : 'none') . '">
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopOfName_ua">' . GetMessage("WIZ_SHOP_OF_NAME") . '</label>'
                    . $this->ShowInputField('text', 'shopOfName_ua', array("id" => "shopOfName_ua", "class" => "wizard-field")) . '
					<p style="color:grey; margin: 3px 0 7px;">' . GetMessage("WIZ_SHOP_OF_NAME_DESCR_UA") . '</p>
				</div>';

            $this->content .= '<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopLocation_ua">' . GetMessage("WIZ_SHOP_LOCATION") . '</label>'
                    . $this->ShowInputField('text', 'shopLocation_ua', array("id" => "shopLocation_ua", "class" => "wizard-field")) . '
					<p style="color:grey; margin: 3px 0 7px;">' . GetMessage("WIZ_SHOP_LOCATION_DESCR_UA") . '</p>
				</div>';


            $this->content .= '
				<div class="wizard-input-form-block">
					<label class="wizard-input-title" for="shopAdr_ua">' . GetMessage("WIZ_SHOP_ADR") . '</label>' .
                    $this->ShowInputField('textarea', 'shopAdr_ua', array("rows" => "3", "id" => "shopAdr_ua", "class" => "wizard-field")) . '
					<p style="color:grey; margin: 3px 0 7px;">' . GetMessage("WIZ_SHOP_ADR_DESCR_UA") . '</p>
				</div>';

            $this->content .= '
				<div class="wizard-catalog-title">' . GetMessage("WIZ_SHOP_RECV_UA") . '</div>
				<p>' . GetMessage("WIZ_SHOP_RECV_UA_DESC") . '</p>
				<table class="wizard-input-table">
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_EGRPU_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopEGRPU_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_INN_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopINN_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_NDS_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopNDS_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_NS_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopNS_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_BANK_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopBank_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_MFO_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopMFO_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_PLACE_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopPlace_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_FIO_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopFIO_ua', array("class" => "wizard-field")) . '</td>
					</tr>
					<tr>
						<td class="wizard-input-table-left">' . GetMessage("WIZ_SHOP_TAX_UA") . ':</td>
						<td class="wizard-input-table-right">' . $this->ShowInputField('text', 'shopTax_ua', array("class" => "wizard-field")) . '</td>
					</tr>
				</table>
			</div>
			';

            if (CModule::IncludeModule("catalog")) {
                $db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID" => '1', "BUY" => "Y", "GROUP_ID" => 2));
                if (!$db_res->Fetch()) {
                    $this->content .= '
					<div class="wizard-input-form-block">
						<div class="wizard-catalog-title">' . GetMessage("WIZ_SHOP_PRICE_BASE_TITLE") . '</div>
						<div class="wizard-input-form-block-content">
							' . GetMessage("WIZ_SHOP_PRICE_BASE_TEXT1") . '<br><br>
							' . $this->ShowCheckboxField("installPriceBASE", "Y", (array("id" => "install-demo-data")))
                            . ' <label for="install-demo-data">' . GetMessage("WIZ_SHOP_PRICE_BASE_TEXT2") . '</label><br />

						</div>
					</div>';
                }
            }

            $this->content .= '</div>';

            $this->content .= '
				<script>
					function langReload()
					{
						var objSel = document.getElementById("localization_select");
						var locSelected = objSel.options[objSel.selectedIndex].value;
						document.getElementById("ru_bank_details").style.display = (locSelected == "ru" || locSelected == "kz") ? "block" : "none";
						document.getElementById("ua_bank_details").style.display = (locSelected == "ua") ? "block" : "none";
						/*document.getElementById("kz_bank_details").style.display = (locSelected == "kz") ? "block" : "none";*/
					}
				</script>
			';
        }
    }

    function OnPostForm() {
        $wizard = & $this->GetWizard();
        $res = $this->SaveFile("siteStamp", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 70, "max_width" => 190, "make_preview" => "Y"));
    }

}

class PersonType extends CWizardStep {

    function InitStep() {
        $this->SetStepID("person_type");
        $this->SetTitle(GetMessage("WIZ_STEP_PT"));
        $this->SetNextStep("pay_system");
        $this->SetPrevStep("shop_settings");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

        $wizard = & $this->GetWizard();
        $shopLocalization = $wizard->GetVar("shopLocalization", true);
        $siteID = $wizard->GetVar("siteID");

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
                            "fiz" => COption::GetOptionString("shoplight", "personTypeFiz", "Y", $siteID),
                            "ur" => COption::GetOptionString("shoplight", "personTypeUr", "Y", $siteID),
                        )
                    )
            );
    }

    function ShowStep() {

        $wizard = & $this->GetWizard();
        $shopLocalization = $wizard->GetVar("shopLocalization", true);

        $this->content .= '<div class="wizard-input-form">';
        $this->content .= '
		<div class="wizard-input-form-block">
			<!--<div class="wizard-catalog-title">' . GetMessage("WIZ_PERSON_TYPE_TITLE") . '</div>-->
			<div style="padding-top:15px">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					<div class="wizard-catalog-form-item">
						' . $this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))) .
                ' <label for="personTypeF">' . GetMessage("WIZ_PERSON_TYPE_FIZ") . '</label><br />
					</div>
					<div class="wizard-catalog-form-item">
						' . $this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))) .
                ' <label for="personTypeU">' . GetMessage("WIZ_PERSON_TYPE_UR") . '</label><br />
					</div>';
        if ($shopLocalization == "ua")
            $this->content .=
                    '<div class="wizard-catalog-form-item">'
                    . $this->ShowCheckboxField('personType[fiz_ua]', 'Y', (array("id" => "personTypeFua"))) .
                    ' <label for="personTypeFua">' . GetMessage("WIZ_PERSON_TYPE_FIZ_UA") . '</label>
					</div>';
        $this->content .= '
				</div>
			</div>
			<div class="wizard-catalog-form-item">' . GetMessage("WIZ_PERSON_TYPE") . '<div>
		</div>';
        $this->content .= '</div>';
    }

    function OnPostForm() {
        $wizard = &$this->GetWizard();
        $personType = $wizard->GetVar("personType");

        if (empty($personType["fiz"]) && empty($personType["ur"]))
            $this->SetError(GetMessage('WIZ_NO_PT'));
    }

}

class PaySystem extends CWizardStep {

    function InitStep() {
        $this->SetStepID("pay_system");
        $this->SetTitle(GetMessage("WIZ_STEP_PS"));
        $this->SetNextStep("data_install");
        if (LANGUAGE_ID != "ru")
            $this->SetPrevStep("catalog_settings");
        else
            $this->SetPrevStep("person_type");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

        $wizard = & $this->GetWizard();

        if (LANGUAGE_ID == "ru") {
            $shopLocalization = $wizard->GetVar("shopLocalization", true);

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
            else
                $wizard->SetDefaultVars(
                        Array(
                            "paysystem" => Array(
                                "cash" => "Y",
                                "sber" => "Y",
                                "bill" => "Y",
                                "collect" => "Y"  //cash on delivery
                            ),
                            "delivery" => Array(
                                "courier" => "Y",
                                "self" => "Y",
                                "russianpost" => "N",
                                "rus_post" => "N",
                                "rus_post_first" => "N",
                                "ua_post" => "N",
                                "kaz_post" => "N"
                            )
                        )
                );
        }
        else {
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

    function OnPostForm() {
        $wizard = &$this->GetWizard();
        $paysystem = $wizard->GetVar("paysystem");

        if (
                empty($paysystem["cash"]) && empty($paysystem["sber"]) && empty($paysystem["bill"]) && empty($paysystem["paypal"]) && empty($paysystem["oshad"]) && empty($paysystem["collect"])
        )
            $this->SetError(GetMessage('WIZ_NO_PS'));
        /* payer type
          if(LANGUAGE_ID == "ru")
          {
          $personType = $wizard->GetVar("personType");

          if (empty($personType["fiz"]) && empty($personType["ur"]))
          $this->SetError(GetMessage('WIZ_NO_PT'));
          }
          === */
    }

    function ShowStep() {

        $wizard = & $this->GetWizard();
        $shopLocalization = $wizard->GetVar("shopLocalization", true);

        $personType = $wizard->GetVar("personType");

        $arAutoDeliveries = array();
        if (CModule::IncludeModule("sale")) {
            $dbDelivery = CSaleDeliveryHandler::GetList();
            while ($arDelivery = $dbDelivery->Fetch()) {
                $arAutoDeliveries[$arDelivery["SID"]] = $arDelivery["ACTIVE"];
            }
        }
        $siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));

        $this->content .= '<div class="wizard-input-form">';
        $this->content .= '
		<div class="wizard-input-form-block">
			<div class="wizard-catalog-title">' . GetMessage("WIZ_PAY_SYSTEM_TITLE") . '</div>
			<div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					<div class="wizard-catalog-form-item">
						' . $this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))) .
                ' <label for="paysystemC">' . GetMessage("WIZ_PAY_SYSTEM_C") . '</label>
					</div>';

        if (LANGUAGE_ID == "ru") {
            if ($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
                $this->content .=
                        '<div class="wizard-catalog-form-item">' .
                        $this->ShowCheckboxField('paysystem[oshad]', 'Y', (array("id" => "paysystemO"))) .
                        ' <label for="paysystemS">' . GetMessage("WIZ_PAY_SYSTEM_O") . '</label>
							</div>';
            if ($shopLocalization == "ru") {
                if ($personType["fiz"] == "Y")
                    $this->content .=
                            '<div class="wizard-catalog-form-item">' .
                            $this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))) .
                            ' <label for="paysystemS">' . GetMessage("WIZ_PAY_SYSTEM_S") . '</label>
								</div>';
                if ($personType["fiz"] == "Y" || $personType["ur"] == "Y")
                    $this->content .=
                            '<div class="wizard-catalog-form-item">' .
                            $this->ShowCheckboxField('paysystem[collect]', 'Y', (array("id" => "paysystemCOL"))) .
                            ' <label for="paysystemCOL">' . GetMessage("WIZ_PAY_SYSTEM_COL") . '</label>
								</div>';
            }
            if ($personType["ur"] == "Y") {
                $this->content .=
                        '<div class="wizard-catalog-form-item">' .
                        $this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))) .
                        ' <label for="paysystemB">';
                if ($shopLocalization == "ua")
                    $this->content .= GetMessage("WIZ_PAY_SYSTEM_B_UA");
                else
                    $this->content .= GetMessage("WIZ_PAY_SYSTEM_B");
                $this->content .= '</label>
							</div>';
            }
        }
        else {
            $this->content .=
                    '<div class="wizard-catalog-form-item">' .
                    $this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))) .
                    ' <label for="paysystemP">PayPal</label>
						</div>';
        }
        $this->content .= '</div>
			</div>
			<div class="wizard-catalog-form-item">' . GetMessage("WIZ_PAY_SYSTEM") . '</div>
		</div>';
        if (
                LANGUAGE_ID != "ru" ||
                LANGUAGE_ID == "ru" &&
                (
                COption::GetOptionString("shoplight", "wizard_installed", "N", $siteID) != "Y" || $shopLocalization == "ru" && ($arAutoDeliveries["russianpost"] != "Y" || $arAutoDeliveries["rus_post"] != "Y" || $arAutoDeliveries["rus_post_first"] != "Y") || $shopLocalization == "ua" && ($arAutoDeliveries["ua_post"] != "Y") || $shopLocalization == "kz" && ($arAutoDeliveries["kaz_post"] != "Y")
                )
        ) {
            $this->content .= '
			<div class="wizard-input-form-block">
				<div class="wizard-catalog-title">' . GetMessage("WIZ_DELIVERY_TITLE") . '</div>
				<div>
					<div class="wizard-input-form-field wizard-input-form-field-checkbox">';

            if (COption::GetOptionString("shoplight", "wizard_installed", "N", $siteID) != "Y") {
                $this->content .= '<div class="wizard-catalog-form-item">
								' . $this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))) .
                        ' <label for="deliveryC">' . GetMessage("WIZ_DELIVERY_C") . '</label>
							</div>
							<div class="wizard-catalog-form-item">
								' . $this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))) .
                        ' <label for="deliveryS">' . GetMessage("WIZ_DELIVERY_S") . '</label>
							</div>';
            }
            if (LANGUAGE_ID == "ru") {

                if ($shopLocalization == "ru") {
                    if ($arAutoDeliveries["russianpost"] != "Y")
                        $this->content .=
                                '<div class="wizard-catalog-form-item">' .
                                $this->ShowCheckboxField('delivery[russianpost]', 'Y', (array("id" => "deliveryR"))) .
                                ' <label for="deliveryR">' . GetMessage("WIZ_DELIVERY_R") . '</label>
										</div>';
                    if ($arAutoDeliveries["rus_post"] != "Y")
                        $this->content .=
                                '<div class="wizard-catalog-form-item">' .
                                $this->ShowCheckboxField('delivery[rus_post]', 'Y', (array("id" => "deliveryR2"))) .
                                ' <label for="deliveryR2">' . GetMessage("WIZ_DELIVERY_R2") . '</label>
										</div>';
                    if ($arAutoDeliveries["rus_post_first"] != "Y")
                        $this->content .=
                                '<div class="wizard-catalog-form-item">' .
                                $this->ShowCheckboxField('delivery[rus_post_first]', 'Y', (array("id" => "deliveryRF"))) .
                                ' <label for="deliveryRF">' . GetMessage("WIZ_DELIVERY_RF") . '</label>
										</div>';
                }
                elseif ($shopLocalization == "ua") {
                    if ($arAutoDeliveries["ua_post"] != "Y")
                        $this->content .=
                                '<div class="wizard-catalog-form-item">' .
                                $this->ShowCheckboxField('delivery[ua_post]', 'Y', (array("id" => "deliveryU"))) .
                                ' <label for="deliveryU">' . GetMessage("WIZ_DELIVERY_UA") . '</label>
										</div>';
                }
                elseif ($shopLocalization == "kz") {
                    if ($arAutoDeliveries["kaz_post"] != "Y")
                        $this->content .=
                                '<div class="wizard-catalog-form-item">' .
                                $this->ShowCheckboxField('delivery[kaz_post]', 'Y', (array("id" => "deliveryK"))) .
                                ' <label for="deliveryK">' . GetMessage("WIZ_DELIVERY_KZ") . '</label>
										</div>';
                }
            }
            else {
                $this->content .=
                        '<div class="wizard-catalog-form-item">' .
                        $this->ShowCheckboxField('delivery[dhl]', 'Y', (array("id" => "deliveryD"))) .
                        ' <label for="deliveryD">DHL</label>
								</div>';
                $this->content .=
                        '<div class="wizard-catalog-form-item">' .
                        $this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))) .
                        ' <label for="deliveryU">UPS</label>
								</div>';
            }
            $this->content .= '
					</div>
				</div>
				<div class="wizard-catalog-form-item">' . GetMessage("WIZ_DELIVERY") . '</div>
			</div>';
        }

        $this->content .= '
		<div>
			<div class="wizard-catalog-title">' . GetMessage("WIZ_LOCATION_TITLE") . '</div>
			<div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
        if (in_array(LANGUAGE_ID, array("ru", "ua"))) {
            $this->content .=
                    '<div class="wizard-catalog-form-item">' .
                    $this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
                    . " <label for=\"loc_ussr\">" . GetMessage('WSL_STEP2_GFILE_USSR') . "</label>
				</div>";
            $this->content .=
                    '<div class="wizard-catalog-form-item">' .
                    $this->ShowRadioField("locations_csv", "loc_ua.csv", array("id" => "loc_ua"))
                    . " <label for=\"loc_ua\">" . GetMessage('WSL_STEP2_GFILE_UA') . "</label>
				</div>";
            $this->content .=
                    '<div class="wizard-catalog-form-item">' .
                    $this->ShowRadioField("locations_csv", "loc_kz.csv", array("id" => "loc_kz"))
                    . " <label for=\"loc_kz\">" . GetMessage('WSL_STEP2_GFILE_KZ') . "</label>
				</div>";
        }
        $this->content .=
                '<div class="wizard-catalog-form-item">' .
                $this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
                . " <label for=\"loc_usa\">" . GetMessage('WSL_STEP2_GFILE_USA') . "</label>
			</div>";
        $this->content .=
                '<div class="wizard-catalog-form-item">' .
                $this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
                . " <label for=\"loc_cntr\">" . GetMessage('WSL_STEP2_GFILE_CNTR') . "</label>
			</div>";
        $this->content .=
                '<div class="wizard-catalog-form-item">' .
                $this->ShowRadioField("locations_csv", "", array("id" => "none"))
                . " <label for=\"none\">" . GetMessage('WSL_STEP2_GFILE_NONE') . "</label>
			</div>";

        $this->content .= '
				</div>
			</div>
		</div>';

        $this->content .= '<div class="wizard-catalog-form-item">' . GetMessage("WIZ_DELIVERY_HINT") . '</div>';

        $this->content .= '</div>';
    }

}

class DataInstallStep extends CDataInstallWizardStep {

    function CorrectServices(&$arServices) {
        if ($_SESSION["BX_ESHOP_LOCATION"] == "Y")
            $this->repeatCurrentService = true;
        else
            $this->repeatCurrentService = false;

        $wizard = & $this->GetWizard();
        if ($wizard->GetVar("installDemoData") != "Y") {
            
        }
    }

}

class FinishStep extends CFinishWizardStep {
    
}

?>