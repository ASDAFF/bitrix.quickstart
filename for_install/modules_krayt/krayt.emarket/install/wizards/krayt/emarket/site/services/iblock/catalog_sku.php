<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("catalog"))
	return;
$iblockXMLFileOffers = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/krayt/catalog_sku.xml";
$iblockXMLFilePricesOffers = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/krayt/catalog_sku_prices.xml";
    
$iblockCodeOffers = "emarket_offers".WIZARD_SITE_ID;
$iblockTypeOffers = "offers";
$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCodeOffers, "TYPE" => $iblockTypeOffers));
$IBLOCK_OFFERS_ID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$IBLOCK_OFFERS_ID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]);
		$IBLOCK_OFFERS_ID = false;
	}
}

if($IBLOCK_OFFERS_ID == false)
{
   
    $permissions = Array(
    			"1" => "X",
    			"2" => "R"
    		);
    $IBLOCK_OFFERS_ID = WizardServices::ImportIBlockFromXML(
    	$iblockXMLFileOffers,
    	"emarket_offers",
    	$iblockTypeOffers,
    	WIZARD_SITE_ID,
    	$permissions
    );
    $iblockID1 = WizardServices::ImportIBlockFromXML(
    	$iblockXMLFilePricesOffers,
    	"emarket_offers",
    	$iblockTypeOffers."_prices",
    	WIZARD_SITE_ID,
    	$permissions
    );  
}else
{
	$arSites = array();
	$db_res = CIBlock::GetSite($IBLOCK_OFFERS_ID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"];
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($IBLOCK_OFFERS_ID, array("LID" => $arSites));
	}
}
$_SESSION["WIZARD_OFFERS_IBLOCK_ID"] = $IBLOCK_OFFERS_ID;
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_SKU_IBLOCK_ID" => $IBLOCK_OFFERS_ID));

if(intval($_SESSION["WIZARD_CATALOG_IBLOCK_ID"]) <= 0)
{
        if(!$IBLOCK_CATALOG_ID)
    {
        $rsIBlock = CIBlock::GetList(array(), array("CODE" => "emarket", "TYPE" => 'catalog'));
        $IBLOCK_CATALOG_ID = false;
        if ($arIBlock = $rsIBlock->Fetch())
        {
        	$IBLOCK_CATALOG_ID = $arIBlock["ID"];
        }
    }
    
    $_SESSION["WIZARD_CATALOG_IBLOCK_ID"] = $IBLOCK_CATALOG_ID;
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("SITE_DIR" => WIZARD_SITE_DIR));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/search/index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
    WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."catalog/", Array("SITE_DIR" => WIZARD_SITE_DIR));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/ajax/get_compare.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
    CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/emarket_emarket/header.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.catalog.menu_ext.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
}