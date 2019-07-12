<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");

if(!CModule::IncludeModule("iblock"))
	return;
	
	$arFields = Array(
    'ID'=>'looks',
    'SECTIONS'=>'Y',
    'IN_RSS'=>'N',
    'SORT'=>500,
    'LANG'=>Array(
        'ru'=>Array(
            'NAME'=>'Lookbook',
            'SECTION_NAME'=>'Looks',
            'ELEMENT_NAME'=>'Elements'
            )
        )
    );

	$obBlocktype = new CIBlockType;
	$DB->StartTransaction();
	$res = $obBlocktype->Add($arFields);
	if(!$res)
	{
	   $DB->Rollback();
	   echo 'Error: '.$obBlocktype->LAST_ERROR.'<br>';
	}
	else
	   $DB->Commit();
	
	
	
	$iblockID = WizardServices::ImportIBlockFromXML(
     WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/looks.xml", 
     "LOOKS", 
     "looks", 
     WIZARD_SITE_ID, 
     $permissions = Array(
         "1" => "X",
         "2" => "R",
     )
);
	
//WizardServices::IncludeServiceLang("brand.php", $lang);

//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brand/index.php", array("BRANDS_IBLOCK_ID" => $iblockID));
//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brand/detail.php", array("BRANDS_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_DIR."bitrix/components/bejetstore/looks.section.list/templates/.default/result_modifier.php", array("LOOKS_BLOCK" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."lookbook/index.php", array("LOOKS_BLOCK" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."lookbook/detail.php", array("LOOKS_BLOCK" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."lookbook/section.php", array("LOOKS_BLOCK" => $iblockID));

	
	$rsLooks = CIBlock::GetList(array(),array("CODE" => "clothes", "SITE_ID" => WIZARD_SITE_ID));
	if($arCatalog= $rsLooks->Fetch())
	{			
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."lookbook/index.php", array("CATALOG_IBLOCK_ID" => $arCatalog["ID"]));
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."lookbook/detail.php", array("CATALOG_IBLOCK_ID" => $arCatalog["ID"]));
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."lookbook/section.php", array("CATALOG_IBLOCK_ID" => $arCatalog["ID"]));		
	}
	
	
//CWizardUtil::ReplaceMacros(WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/catalog.xml", array("BRANDS_IBLOCK_ID" => $iblockID));
?>