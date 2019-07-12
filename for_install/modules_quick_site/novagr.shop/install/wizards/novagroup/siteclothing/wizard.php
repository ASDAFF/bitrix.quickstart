<?
use Bitrix\Seo\SitemapTable;
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
    function InitStep()
    {
        parent::InitStep();

        $wizard =& $this->GetWizard();
        $wizard->solutionName = "siteclothing";

        $this->SetNextStep("person_type");
    }
}

class PersonType extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("person_type");
        $this->SetTitle(GetMessage("WIZ_STEP_PT"));
        $this->SetNextStep("pay_system");
        //$this->SetPrevStep("shop_settings");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        //$this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

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
			<h4>' . GetMessage("WIZ_PERSON_TYPE_TITLE") . '</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					' . $this->ShowCheckboxField('personType[fiz]', 'Y', (array("id" => "personTypeF"))) . ' <label for="personTypeF">' . GetMessage("WIZ_PERSON_TYPE_FIZ") . '</label><br />
					' . $this->ShowCheckboxField('personType[ur]', 'Y', (array("id" => "personTypeU"))) . ' <label for="personTypeU">' . GetMessage("WIZ_PERSON_TYPE_UR") . '</label><br />';
        //	if ($shopLocalization == "ua")
        //		$this->content .= $this->ShowCheckboxField('personType[fiz_ua]', 'Y', (array("id" => "personTypeFua"))).' <label for="personTypeFua">'.GetMessage("WIZ_PERSON_TYPE_FIZ_UA").'</label><br />';
        $this->content .= '
				</div>
			</div>
			' . GetMessage("WIZ_PERSON_TYPE") . '
		</div>';
        $this->content .= '</div>';
    }

    function OnPostForm()
    {
        $wizard = & $this->GetWizard();
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
        if(defined("WIZARD_MODULE_LITE_VERSION") and WIZARD_MODULE_LITE_VERSION=="true")
            $this->SetNextStep("data_install");
        else
            $this->SetNextStep("catalog_step");
        $this->SetPrevStep("person_type");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));

        $wizard =& $this->GetWizard();
        if (LANGUAGE_ID == "ru") {

            $wizard->SetDefaultVars(
                Array(
                    "paysystem" => Array(
                        "cash" => "Y",
                        "sber" => "Y",
                        "bill" => "Y",
                        "collect" => "Y",
                    ),
                    "delivery" => Array(
                        "courier" => "Y",
                        "self" => "Y",
                        "russianpost" => "N",
                    )
                )
            );
        } else {
            $wizard->SetDefaultVars(
                Array(
                    "paysystem" => Array(
                        "cash" => "Y",
                        "paypal" => "Y",
                    ),
                    "delivery" => Array(
                        "courier" => "Y",
                        "self" => "Y",
                        "dhlusa" => "Y",
                        "ups" => "Y",
                    )
                )
            );
        }
    }

    function OnPostForm()
    {
        $wizard = & $this->GetWizard();
        $paysystem = $wizard->GetVar("paysystem");

        if (empty($paysystem["cash"]) && empty($paysystem["sber"]) && empty($paysystem["bill"]) && empty($paysystem["paypal"]))
            $this->SetError(GetMessage('WIZ_NO_PS'));

        COption::SetOptionString("sale", "product_reserve_condition", $wizard->GetVar("PRODUCT_RESERVE_CONDITION"));
        COption::SetOptionString("catalog", "enable_reservation", "Y");
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
            <h4>' . GetMessage("WIZ_PRODUCT_RESERVE_CONDITION_TITLE") . '</h4>
            <div class="wizard-input-form-block-content">
    			<div class="wizard-input-form-field wizard-input-form-field-checkbox">
                    ' . $this->ShowSelectField('PRODUCT_RESERVE_CONDITION', array(
                            "O" => GetMessage("LS_INSTALL_PRODUCT_RESERVE_CONDITION_O"),
                            "P" => GetMessage("LS_INSTALL_PRODUCT_RESERVE_CONDITION_P"),
                            "D" => GetMessage("LS_INSTALL_PRODUCT_RESERVE_CONDITION_D"),
                            "S" => GetMessage("LS_INSTALL_PRODUCT_RESERVE_CONDITION_S"),
                        ), array("id" => "productReserve")) . '
                    <label for="productReserve">' . GetMessage('LS_INSTALL_PRODUCT_RESERVE_CONDITION') . '</label>
					<br>
                </div>
            </div>
        </div>';


        $this->content .= '
		<div class="wizard-input-form-block">
			<h4>' . GetMessage("WIZ_PAY_SYSTEM_TITLE") . '</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					' . $this->ShowCheckboxField('paysystem[cash]', 'Y', (array("id" => "paysystemC"))) . ' <label for="paysystemC">' . GetMessage("WIZ_PAY_SYSTEM_C") . '</label><br />';
        if (LANGUAGE_ID == "ru") {
            /*	if($shopLocalization == "ua" && ($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y"))
                    $this->content .= $this->ShowCheckboxField('paysystem[oshad]', 'Y', (array("id" => "paysystemO"))).' <label for="paysystemS">'.GetMessage("WIZ_PAY_SYSTEM_O").'</label><br />';
                else*/
            if ($personType["fiz"] == "Y")
            {
                $this->content .= $this->ShowCheckboxField('paysystem[sber]', 'Y', (array("id" => "paysystemS"))) . ' <label for="paysystemS">' . GetMessage("WIZ_PAY_SYSTEM_S") . '</label><br />';
                $this->content .= $this->ShowCheckboxField('paysystem[collect]', 'Y', (array("id" => "paysystemCOL"))) . ' <label for="paysystemCOL">' . GetMessage("WIZ_PAY_SYSTEM_COL") . '</label><br />';
            }
            if ($personType["ur"] == "Y")
                $this->content .= $this->ShowCheckboxField('paysystem[bill]', 'Y', (array("id" => "paysystemB"))) . ' <label for="paysystemB">' . GetMessage("WIZ_PAY_SYSTEM_B") . '</label><br />';
        } else {
            $this->content .= $this->ShowCheckboxField('paysystem[paypal]', 'Y', (array("id" => "paysystemP"))) . ' <label for="paysystemP">PayPal</label><br />';
        }
        $this->content .= '</div>
			</div>
			' . GetMessage("WIZ_PAY_SYSTEM") . '
		</div>';
        $this->content .= '
		<div class="wizard-input-form-block">
			<h4>' . GetMessage("WIZ_DELIVERY_TITLE") . '</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					' . $this->ShowCheckboxField('delivery[courier]', 'Y', (array("id" => "deliveryC"))) . ' <label for="deliveryC">' . GetMessage("WIZ_DELIVERY_C") . '</label><br />
					' . $this->ShowCheckboxField('delivery[self]', 'Y', (array("id" => "deliveryS"))) . ' <label for="deliveryS">' . GetMessage("WIZ_DELIVERY_S") . '</label><br />';
        if (LANGUAGE_ID == "ru") {
            //if ($shopLocalization != "ua")
            $this->content .= $this->ShowCheckboxField('delivery[russianpost]', 'Y', (array("id" => "deliveryR"))) . ' <label for="deliveryR">' . GetMessage("WIZ_DELIVERY_R") . '</label><br />';
            $this->content .= $this->ShowCheckboxField('delivery[rus_post]', 'Y', (array("id" => "deliveryRR"))) . ' <label for="deliveryRR">' . GetMessage("WIZ_DELIVERY_RR") . '</label><br />';
            $this->content .= $this->ShowCheckboxField('delivery[rus_post_first]', 'Y', (array("id" => "deliveryFC"))) . ' <label for="deliveryFC">' . GetMessage("WIZ_DELIVERY_FC") . '</label><br />';
        } else {
            $this->content .= $this->ShowCheckboxField('delivery[dhlusa]', 'Y', (array("id" => "deliveryD"))) . ' <label for="deliveryD">DHL (USA)</label><br />';
            $this->content .= $this->ShowCheckboxField('delivery[ups]', 'Y', (array("id" => "deliveryU"))) . ' <label for="deliveryU">UPS</label><br />';
        }
        $this->content .= '
				</div>
			</div>
			' . GetMessage("WIZ_DELIVERY") . '
		</div>';

        $this->content .= '
		<div class="wizard-input-form-block">
			<h4>' . GetMessage("WIZ_LOCATION_TITLE") . '</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">';
        if (LANGUAGE_ID == "ru") {
            $this->content .= $this->ShowRadioField("locations_csv", "loc_ussr.csv", array("id" => "loc_ussr", "checked" => "checked"))
                . " <label for=\"loc_ussr\">" . GetMessage('WSL_STEP2_GFILE_USSR') . "</label><br />";
        }
        $this->content .= $this->ShowRadioField("locations_csv", "loc_usa.csv", array("id" => "loc_usa"))
            . " <label for=\"loc_usa\">" . GetMessage('WSL_STEP2_GFILE_USA') . "</label><br />";
        $this->content .= $this->ShowRadioField("locations_csv", "loc_cntr.csv", array("id" => "loc_cntr"))
            . " <label for=\"loc_cntr\">" . GetMessage('WSL_STEP2_GFILE_CNTR') . "</label><br />";
        $this->content .= $this->ShowRadioField("locations_csv", "", array("id" => "none"))
            . " <label for=\"none\">" . GetMessage('WSL_STEP2_GFILE_NONE') . "</label>";
        $this->content .= '
				</div>
			</div>
		</div>';

        $this->content .= '<div class="wizard-input-form-block">' . GetMessage("WIZ_DELIVERY_HINT") . '</div>';

        $this->content .= '</div>';

        //$this->content .= '<p style="padding:10px;color:white;background:#5E7CAD;margin-bottom:20px;">' . GetMessage('WARNING') . '</p>';
    }
}

class CatalogStep extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("catalog_step");
        $this->SetTitle(GetMessage("WIZ_STEP_CS"));
        $this->SetNextStep("data_install");
        $this->SetPrevStep("pay_system");
        $this->SetNextCaption(GetMessage("NEXT_BUTTON"));
        $this->SetPrevCaption(GetMessage("PREVIOUS_BUTTON"));
    }

    function ShowStep()
    {
        $this->content .= '
        <div class="wizard-input-form">
		<div class="wizard-input-form-block">
			<h4>' . GetMessage('DH_INSTALL_SERVICES') . '</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">
					' . $this->ShowCheckboxField('services[subscribe]', 'Y', (array("id" => "servicesSubscribe", "checked" => "checked"))) . '
					<label for="servicesSubscribe">' . GetMessage('LS_INSTALL_SERVICE_SUBSCRIBE') . '</label>
					<br>
				</div>
			</div>
            ' . GetMessage('DT_SELECT_SERVICES') . '
		</div>
		</div>
    ';
    }

    function OnPostForm()
    {
        $wizard = & $this->GetWizard();
        $services = $wizard->GetVar("services");
        if (is_array($services)) {
            foreach ($services as $service => $value) {
                COption::SetOptionString('novagr.shop', "service_{$service}", $value);
            }
        }
    }

}

class DataInstallStep extends CDataInstallWizardStep
{
    function CorrectServices(&$arServices)
    {
        if ($_SESSION["BX_ESHOP_LOCATION"] == "Y")
            $this->repeatCurrentService = true;
        else
            $this->repeatCurrentService = false;

        $wizard =& $this->GetWizard();
        if ($wizard->GetVar("installDemoData") != "Y") {
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
        global $USER;
        $wizard =& $this->GetWizard();

        $siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));

        if ($wizard->GetVar("proactive") == "Y")
            COption::SetOptionString("statistic", "DEFENCE_ON", "Y");

        $dbconnFile = getenv('DOCUMENT_ROOT') . BX_ROOT . '/php_interface/dbconn.php';
        if (file_exists($dbconnFile) && is_readable($dbconnFile) && is_writable($dbconnFile)) {
            $dbconnNewContent = '<? define("BX_DISABLE_INDEX_PAGE", true); ?>';
            file_put_contents($dbconnFile, $dbconnNewContent, FILE_APPEND);
        }

        if (strlen($siteID) > 0 and is_object($USER) and method_exists($USER, 'GetEmail')) {
            $obSite = new CSite();
            $t = $obSite->Update($siteID, array(
                'EMAIL' => $USER->GetEmail(),
                'NAME' => GetMessage('wiz_site_name'),
                'SERVER_NAME' => $this->getSiteUrl()
            ));
        };
        $rsSites = CSite::GetByID($siteID);
        $siteDir = SITE_DIR;
        if ($arSite = $rsSites->Fetch())
            $siteDir = $arSite["DIR"];

        $wizard->SetFormActionScript(str_replace("//", "/", $siteDir . "/?finish"));

        $this->CreateNewIndex();

        $this->addUrlRewrite();

        $this->addSitemap();

        COption::SetOptionString("main", "CAPTCHA_textAngel_1", "-10");
        COption::SetOptionString("main", "CAPTCHA_textAngel_2", "10");
        COption::SetOptionString("main", "CAPTCHA_textDistance_1", "-1");
        COption::SetOptionString("main", "CAPTCHA_textDistance_2", "-5");
        COption::SetOptionString("main", "CAPTCHA_letters", "1234567890");
        COption::SetOptionString("main", "CAPTCHA_presets", "0");

        COption::SetOptionString("main","detail_card", 2);
        COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID);

        $this->content .= GetMessage("FINISH_STEP_CONTENT");
        //$this->content .= "<br clear=\"all\"><a href=\"/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&site_id=".$siteID."&wizardName=bitrix:eshop.mobile&".bitrix_sessid_get()."\" class=\"button-next\"><span id=\"next-button-caption\">".GetMessage("wizard_store_mobile")."</span></a>";

        if ($wizard->GetVar("installDemoData") == "Y")
            $this->content .= GetMessage("FINISH_STEP_REINDEX");


    }

    function addUrlRewrite()
    {
        $arUrlRewrite = array(
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "catalog/([0-9a-zA-Z_-]+)/([0-9a-zA-Z_-]+)/.*#",
                "RULE" => "secid=$1&elmid=$2",
                "ID" => "",
                "PATH" => WIZARD_SITE_DIR . "catalog/detail.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "catalog/([0-9a-zA-Z_-]+)/.*#",
                "RULE" => "secid=$1",
                "ID" => "",
                "PATH" => WIZARD_SITE_DIR . "catalog/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "brands/([0-9a-zA-Z_-]+)/.*#",
                "RULE" => "elmid=$1",
                "ID" => "",
                "PATH" => WIZARD_SITE_DIR . "brands/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "imageries/([0-9a-zA-Z_-]+)/.*#",
                "RULE" => "elmid=$1",
                "ID" => "",
                "PATH" => WIZARD_SITE_DIR . "imageries/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "cabinet/order/#",
                "RULE" => "",
                "ID" => "bitrix:sale.personal.order",
                "PATH" => WIZARD_SITE_DIR . "cabinet/order/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "blogs/#",
                "RULE" => "",
                "ID" => "bitrix:news",
                "PATH" => WIZARD_SITE_DIR . "blogs/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "news/#",
                "RULE" => "",
                "ID" => "bitrix:news",
                "PATH" => WIZARD_SITE_DIR . "news/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^" . WIZARD_SITE_DIR . "cabinet/#",
                "RULE" => "",
                "ID" => "novagr.shop:cabinet",
                "PATH" => WIZARD_SITE_DIR . "cabinet/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
            array(
                "CONDITION" => "#^". WIZARD_SITE_DIR ."product/([0-9a-zA-Z_-]+)/.*#",
                "RULE" => "elem_code=\$1",
                "ID" => "",
                "PATH" => WIZARD_SITE_DIR . "product/index.php",
                "SITE_ID" => WIZARD_SITE_ID
            ),
        );

        foreach ($arUrlRewrite as $arFields) {
            CUrlRewriter::Add($arFields);
        }
        CUrlRewriter::RecurseIndex(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR), 20);
    }

    function addSitemap()
    {
        if (CModule::IncludeModule('seo') && CModule::IncludeModule('iblock')) {

            $MAP_ID = 0;

            $siteMap = new SitemapTable();
            $geSitemaptList = $siteMap->getList()->fetchAll();
            foreach($geSitemaptList as $map)
            {
                if($map['SITE_ID'] == WIZARD_SITE_ID)
                {
                    $MAP_ID = $map['ID'];
                }
            }

            $blockList = CIBlock::GetList(array("ID"));
            while ($iblock = $blockList->Fetch()) {
                if (in_array($iblock['CODE'], array("novagr_standard_products", "novagr_standard_images", "blogs", "news"))) {
                    $IBLOCK_ACTIVE[$iblock['ID']] = "Y";
                    $IBLOCK_LIST[$iblock['ID']] = "Y";
                    $IBLOCK_SECTION[$iblock['ID']] = "Y";
                    $IBLOCK_ELEMENT[$iblock['ID']] = "Y";
                } else {
                    $IBLOCK_ACTIVE[$iblock['ID']] = "N";
                    $IBLOCK_LIST[$iblock['ID']] = "N";
                    $IBLOCK_SECTION[$iblock['ID']] = "N";
                    $IBLOCK_ELEMENT[$iblock['ID']] = "N";
                }
            }


            $arSitemapSettings = Array(
                "FILE_MASK" => "*.php,*.html",
                "ROBOTS" => "Y",
                "logical" => "Y",
                "DIR" => Array(
                    "/" => "Y",
                    "/auth" => "N",
                    "/cabinet" => "N",
                    "/managers-cabinet" => "N",
                    "/pay_result" => "N"
                ),
                "FILE" => Array(),
                "PROTO" => 0,
                "DOMAIN" => $this->getSiteUrl(),
                "FILENAME_INDEX" => "sitemap.xml",
                "FILENAME_FILES" => "sitemap_files.xml",
                "FILENAME_IBLOCK" => "sitemap_iblock_#IBLOCK_ID#.xml",
                "IBLOCK_ACTIVE" => $IBLOCK_ACTIVE,
                "IBLOCK_LIST" => $IBLOCK_LIST,
                "IBLOCK_SECTION" => $IBLOCK_SECTION,
                "IBLOCK_ELEMENT" => $IBLOCK_ELEMENT,
                "IBLOCK_SECTION_SECTION" => '',
                "IBLOCK_SECTION_ELEMENT" => '',
                "FILE_MASK_REGEXP" => '/^(.*?\.php|.*?\.html)$/i'
            );

            $arSiteMapFields = array(
                'NAME' => GetMessage('SITEMAP_SETTINGS'),
                'ACTIVE' => 'Y',
                'SITE_ID' => WIZARD_SITE_ID,
                'SETTINGS' => serialize($arSitemapSettings)
            );

            if($MAP_ID > 0)
            {
                $result = SitemapTable::update($MAP_ID, $arSiteMapFields);
            }
            else
            {
                $result = SitemapTable::add($arSiteMapFields);
            }
            return $result;
        }
    }

    function getSiteUrl()
    {
        $PARSE_HOST = parse_url(getenv('HTTP_HOST'));
        if (isset($PARSE_HOST['port']) and $PARSE_HOST['port'] == '80') {
            $HOST = $PARSE_HOST['host'];
        }
        elseif (isset($PARSE_HOST['port']) and $PARSE_HOST['port'] == '443') {
            $HOST = $PARSE_HOST['host'];
        }
        elseif(isset($PARSE_HOST['port'])) {
            $HOST = $PARSE_HOST['host'] . ":" . $PARSE_HOST['port'];
        } else {
            $HOST = $PARSE_HOST['host'];
        }
        return $HOST;
    }
}

?>