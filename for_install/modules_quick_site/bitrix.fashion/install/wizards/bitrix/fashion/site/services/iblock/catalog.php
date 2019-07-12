<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock")||!CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("fashion", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/ru/models.xml";
$iblockXMLFileItems = WIZARD_SERVICE_RELATIVE_PATH."/xml/ru/items.xml";

$iblockType = "catalog";
$iblockCode = "models_".WIZARD_SITE_ID;
$iblockCodeItems = "items_".WIZARD_SITE_ID;

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"];

	if (WIZARD_INSTALL_DEMO_DATA)
	{
		$arCatalog = CCatalog::GetByIDExt($arIBlock["ID"]);
		if (is_array($arCatalog) && (in_array($arCatalog['CATALOG_TYPE'],array('P','X'))) == true)
		{
			CCatalog::UnLinkSKUIBlock($arIBlock["ID"]); // unlink offers iblock
			CIBlock::Delete($arCatalog['OFFERS_IBLOCK_ID']); // delete offers iblock
		}
		CIBlock::Delete($arIBlock["ID"]); // delete cur. iblock

		$iblockID = false;
		COption::SetOptionString("sitefashion", "demo_deleted", "N", "", WIZARD_SITE_ID);
	}
}

// dvs!!
$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCodeItems, "TYPE" => $iblockType));
$iblockID1 = false;
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID1 = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID1 = false;
	}
}
// dvs!!

// Price add
CModule::IncludeModule("catalog");
$dbResultList = CCatalogGroup::GetList(Array(), Array("CODE" => "BASE"));
if(!($dbResultList->Fetch()))
{
	$arFields = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage->Fetch())
	{
		WizardServices::IncludeServiceLang("catalog.php", $arLanguage["ID"]);
		$arFields["USER_LANG"][$arLanguage["ID"]] = GetMessage("WIZ_PRICE_NAME");
	}
	$arFields["BASE"] = "Y";
	$arFields["SORT"] = 100;
	$arFields["NAME"] = "BASE";
	$arFields["USER_GROUP"] = Array(1, 2);
	$arFields["USER_GROUP_BUY"] = Array(1, 2);
	CCatalogGroup::Add($arFields);
}


if($iblockID == false)
{
	$permissions = Array(
			"1" => "X",
			"2" => "R"
	);

	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"models",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	// old
	/*
	// Items add
	$iblockID1 = WizardServices::ImportIBlockFromXML(
		$iblockXMLFileItems,
		"items",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);
	*/
	
	
	/* */
	$ABS_FILE_NAME = $_SERVER["DOCUMENT_ROOT"].$iblockXMLFileItems;
	$WORK_DIR_NAME = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/ru/";

	$NS = array(
		 "IBLOCK_TYPE" => $iblockType,
		 "LID" => Array(WIZARD_SITE_ID),
		 "URL_DATA_FILE" => $ABS_FILE_NAME,
		 "ACTION" => "D",
		 "PREVIEW" => "N",
	  );

	$obXMLFile = new CIBlockXMLFile;

	$_SESSION["BX_CML2_IMPORT"] = array("SECTION_MAP" => false, "PRICES_MAP" => false);
	CIBlockXMLFile::DropTemporaryTables();
	CIBlockCMLImport::CheckIfFileIsCML($ABS_FILE_NAME);
	CIBlockXMLFile::CreateTemporaryTables();
	
	if(file_exists($ABS_FILE_NAME) && is_file($ABS_FILE_NAME) && ($fp = fopen($ABS_FILE_NAME, "rb")))
	{
		$obXMLFile->ReadXMLToDatabase($fp, $NS);
		fclose($fp);
	}

	CIBlockXMLFile::IndexTemporaryTables();

	$obCatalog = new CIBlockCMLImport;
	$obCatalog->Init($NS, $WORK_DIR_NAME, true, $NS["PREVIEW"], false, true);
	$result = $obCatalog->ImportMetaData(1, $NS["IBLOCK_TYPE"], $NS["LID"]);
	$obCatalog->ImportSections();
	$obCatalog->DeactivateSections("A");
	$obCatalog->SectionsResort();
	$obCatalog->Init($NS, $WORK_DIR_NAME, true, $NS["PREVIEW"], false, true);
	$obCatalog->ReadCatalogData($_SESSION["BX_CML2_IMPORT"]["SECTION_MAP"], $_SESSION["BX_CML2_IMPORT"]["PRICES_MAP"]);
	$result = $obCatalog->ImportElements();
	$iblockID1 = $NS['IBLOCK_ID'];
	/* */
	

	if ($iblockID < 1||$iblockID1 < 1){
		return;
	}

	$iModels = $iblockID;
	$iGoods = $iblockID1;

   	$dbProp = CIBlockProperty::GetList(array(),	array('CODE' => 'model', 'IBLOCK_ID' => $iGoods));
	$arProp = $dbProp->Fetch();

	$arSKU = array(
		'IBLOCK_ID' => $iGoods,
		'PRODUCT_IBLOCK_ID' => $iModels,
		'SKU_PROPERTY_ID' => $arProp['ID'],
	);

	$obCat = new CCatalog;
	$arCatalog = CCatalog::GetByID($iGoods);
	if($arCatalog)
		$obCat->Update($iGoods, $arSKU);
	else
		$obCat->Add($arSKU);

	//IBlock fields
	$iblock = new CIBlock;

	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), ),
		"CODE" => "models",
		"XML_ID" => $iblockCode
	);
	$iblock->Update($iblockID, $arFields);

	$arFields = Array(
		"ACTIVE" => "Y",
		"CODE" => "items",
		"XML_ID" => $iblockCodeItems
	);
	$iblock->Update($iblockID1, $arFields);

}
else
{
	$arSites = array();
	$db_res = CIBlock::GetSite($iblockID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"];
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array("LID" => $arSites));
		$iblock->Update($iblockID1, array("LID" => $arSites));
	}
}

// colors & sizes link
$arProp4Link = array("item_color"=>"color", "item_size"=>"sizes");

$dbProp = CIBlockProperty::GetList(array(), array('CODE' => 'item_%', 'IBLOCK_ID' => $iblockID1));
while($arProp = $dbProp->Fetch()){
	if(!array_key_exists($arProp['CODE'], $arProp4Link)) continue;

	$rsIBlock = CIBlock::GetList(array(), array("CODE" => $arProp4Link[$arProp['CODE']], "XML_ID" => $arProp4Link[$arProp['CODE']]."_".WIZARD_SITE_ID, "TYPE" => 'catalog'));
    if($arIBlock = $rsIBlock->Fetch()){
		$arFieldsUpdate = Array(
			"LINK_IBLOCK_ID" => $arIBlock['ID'],
			"IBLOCK_ID" => $iblockID1
		);

		$ibp = new CIBlockProperty;
		if(!$ibp->Update($arProp['ID'], $arFieldsUpdate))
			return;
	}
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID, "CATALOG_ITEMS_IBLOCK_ID" => $iblockID1));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brands/index.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brands/.catalog_inc.menu_ext.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.catalog.menu_ext.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/.catalog_inc.menu_ext.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/sect_inc.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/brands/sect_inc.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/ajax/index.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));

if(WIZARD_TEMPLATE_ID=="fashion_fix")
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/".WIZARD_TEMPLATE_ID."_index_".WIZARD_THEME_ID."/header.php", array("CATALOG_MODELS_IBLOCK_ID" => $iblockID));

// user_prop create
$arProperties = Array(
	'UF_WISHLIST' => array(
		'ENTITY_ID' => 'USER',
		'FIELD_NAME' => 'UF_WISHLIST',
		'USER_TYPE_ID' => 'iblock_element',
		'XML_ID' => '',
		'SORT' => 100,
		'MULTIPLE' => 'Y',
		'MANDATORY' => 'N',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'N',
		'SETTINGS' => array(
			'DISPLAY' => 'LIST',
			'LIST_HEIGHT' => 5,
			'IBLOCK_ID' => $iblockID1,
			'DEFAULT_VALUE' => '',
			'ACTIVE_FILTER' => 'N',
		)
	)
);

$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by="name", $order="asc", array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

foreach ($arProperties as $arProperty)
{
	$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => $arProperty["ENTITY_ID"], "FIELD_NAME" => $arProperty["FIELD_NAME"]));
	if ($dbRes->Fetch())
		continue;

	$arLabelNames = Array();
	foreach($arLanguages as $languageID)
	{
		WizardServices::IncludeServiceLang("property_name.php", $languageID);
		$arLabelNames[$languageID] = GetMessage($arProperty["FIELD_NAME"]);
	}

	$arProperty["EDIT_FORM_LABEL"] = $arLabelNames;
	$arProperty["LIST_COLUMN_LABEL"] = $arLabelNames;
	$arProperty["LIST_FILTER_LABEL"] = $arLabelNames;

	$userType = new CUserTypeEntity();
	$success = (bool)$userType->Add($arProperty);
}
?>