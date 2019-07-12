<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();


if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;
//check type price site
$dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array("NAME" => "BASE")
    );
 if($dbPriceType->SelectedRowsCount() <= 0)
 {      
    $listGroup = array();
    $rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array()); // выбираем группы        
    while($uGr = $rsGroups->Fetch()) :
       $listGroup[] = $uGr['ID'];	
    endwhile;
    
    $arFields = array(
               "NAME" => "BASE",
               "BASE" => "Y",
               "SORT" => 100,
               "USER_GROUP" => $listGroup,   // видят цены члены групп 2 и 4
               "USER_GROUP_BUY" => $listGroup,  // покупают по этой цене
                                              // только члены группы 2
               "USER_LANG" => array(
                  "ru" => "Base",
                  "en" => "Base"
                  )
            );
            
    $ID = CCatalogGroup::Add($arFields);
 }   
$iblockXMLFile = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/krayt/catalog.xml";
$iblockXMLFilePrices = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/krayt/catalog_prices.xml";

$iblockCode = "emarket_".WIZARD_SITE_ID;
$iblockType = "catalog";
$permissions = Array(
		"1" => "X",
		"2" => "R"
	);
$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$IBLOCK_CATALOG_ID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$IBLOCK_CATALOG_ID = $arIBlock["ID"];
}
if ($IBLOCK_CATALOG_ID)
{
	$boolFlag = true;
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_CATALOG_ID);
	if (!empty($arSKU))
	{
		$boolFlag = CCatalog::UnLinkSKUIBlock($IBLOCK_CATALOG_ID);
		if (!$boolFlag)
		{
			$strError = "";
			if ($ex = $APPLICATION->GetException())
			{
				$strError = $ex->GetString();
			}
			else
			{
				$strError = "Couldn't unlink iblocks";
			}
			//die($strError);
		}
		$boolFlag = CIBlock::Delete($arSKU['IBLOCK_ID']);
		if (!$boolFlag)
		{
			$strError = "";
			if ($ex = $APPLICATION->GetException())
			{
				$strError = $ex->GetString();
			}
			else
			{
				$strError = "Couldn't delete offers iblock";
			}
			//die($strError);
		}
	}
	if ($boolFlag)
	{
		$boolFlag = CIBlock::Delete($IBLOCK_CATALOG_ID);
		if (!$boolFlag)
		{
			$strError = "";
			if ($ex = $APPLICATION->GetException())
			{
				$strError = $ex->GetString();
			}
			else
			{
				$strError = "Couldn't delete catalog iblock";
			}
			//die($strError);
		}
	}
	if ($boolFlag)
	{
		$IBLOCK_CATALOG_ID = false;
	}
} 
    
if($IBLOCK_CATALOG_ID == false)
{     
	set_time_limit(100);
    $IBLOCK_CATALOG_ID = WizardServices::ImportIBlockFromXML(
    	$iblockXMLFile,
    	"emarket",
    	'catalog',
    	WIZARD_SITE_ID,
    	$permissions
    );
    $IBLOCK_CATALOG_ID1 = WizardServices::ImportIBlockFromXML(
    	$iblockXMLFilePrices,
    	"emarket",
    	"catalog_prices",
    	WIZARD_SITE_ID,
    	$permissions
    );
}
else
{
	$arSites = array();
	$db_res = CIBlock::GetSite($IBLOCK_CATALOG_ID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"];
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($IBLOCK_CATALOG_ID, array("LID" => $arSites));
	}
}
if(!$IBLOCK_CATALOG_ID)
{
    $rsIBlock = CIBlock::GetList(array(), array("CODE" => "emarket", "TYPE" => $iblockType));
    $IBLOCK_CATALOG_ID = false;
    if ($arIBlock = $rsIBlock->Fetch())
    {
    	$IBLOCK_CATALOG_ID = $arIBlock["ID"];
    }
}



$_SESSION["WIZARD_CATALOG_IBLOCK_ID"] = $IBLOCK_CATALOG_ID;
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/search/index.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."catalog/", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/ajax/get_compare.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/okshop/header.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/okshop/footer.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.catalog.menu_ext.php", array("CATALOG_IBLOCK_ID" => $IBLOCK_CATALOG_ID));