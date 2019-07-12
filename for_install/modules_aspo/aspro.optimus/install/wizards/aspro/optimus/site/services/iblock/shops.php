<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

$iblockShortCODE = "shops";
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/".$iblockShortCODE.".xml";
$iblockTYPE = "aspro_optimus_content";
$iblockXMLID = "aspro_optimus_".$iblockShortCODE."_".WIZARD_SITE_ID;
$iblockCODE = "aspro_optimus_".$iblockShortCODE;
$iblockID = false;

set_time_limit(0);

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockXMLID, "TYPE" => $iblockTYPE));
if ($arIBlock = $rsIBlock->Fetch()) {
	$iblockID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA) {
		// delete if already exist & need install demo
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
	}
}

if(WIZARD_INSTALL_DEMO_DATA){
	if(!$iblockID){
		// add new iblock
		$permissions = array("1" => "X", "2" => "R");
		$dbGroup = CGroup::GetList($by = "", $order = "", array("STRING_ID" => "content_editor"));
		if($arGroup = $dbGroup->Fetch()){
			$permissions[$arGroup["ID"]] = "W";
		};
		
		// replace macros IN_XML_SITE_ID & IN_XML_SITE_DIR in xml file - for correct url links to site
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back")){
			@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile);
		}
		@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back");
		CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_SITE_DIR" => WIZARD_SITE_DIR));
		CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_SITE_ID" => WIZARD_SITE_ID));
		$iblockID = WizardServices::ImportIBlockFromXML($iblockXMLFile, $iblockCODE, $iblockTYPE, WIZARD_SITE_ID, $permissions);
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back")){
			@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile);
		}
		if ($iblockID < 1)	return;
			
		// iblock fields
		$iblock = new CIBlock;
		$arFields = array(
			"ACTIVE" => "Y",
			"CODE" => $iblockCODE,
			"XML_ID" => $iblockXMLID,
			"FIELDS" => array(
				"IBLOCK_SECTION" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "Array",
				),
				"ACTIVE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE"=> "Y",
				),
				"ACTIVE_FROM" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"ACTIVE_TO" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"SORT" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "0",
				), 
				"NAME" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "",
				), 
				"PREVIEW_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"FROM_DETAIL" => "Y",
						"SCALE" => "Y",
						"WIDTH" => "100",
						"HEIGHT" => "69",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 95,
						"DELETE_WITH_DETAIL" => "Y",
						"UPDATE_WITH_DETAIL" => "Y",
					),
				), 
				"PREVIEW_TEXT_TYPE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "html",
				), 
				"PREVIEW_TEXT" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				), 
				"DETAIL_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"SCALE" => "Y",
						"WIDTH" => "2000",
						"HEIGHT" => "2000",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 95,
					),
				), 
				"DETAIL_TEXT_TYPE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "html",
				), 
				"DETAIL_TEXT" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				), 
				"XML_ID" =>  array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				), 
				"CODE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"UNIQUE" => "N",
						"TRANSLITERATION" => "N",
						"TRANS_LEN" => 100,
						"TRANS_CASE" => "L",
						"TRANS_SPACE" => "-",
						"TRANS_OTHER" => "-",
						"TRANS_EAT" => "Y",
						"USE_GOOGLE" => "N",
					),
				),
				"TAGS" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				), 
				"SECTION_NAME" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "",
				), 
				"SECTION_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"FROM_DETAIL" => "N",
						"SCALE" => "N",
						"WIDTH" => "",
						"HEIGHT" => "",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 95,
						"DELETE_WITH_DETAIL" => "N",
						"UPDATE_WITH_DETAIL" => "N",
					),
				), 
				"SECTION_DESCRIPTION_TYPE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "text",
				), 
				"SECTION_DESCRIPTION" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				), 
				"SECTION_DETAIL_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"SCALE" => "Y",
						"WIDTH" => "2000",
						"HEIGHT" => "2000",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 95,
					),
				), 
				"SECTION_XML_ID" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				), 
				"SECTION_CODE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"UNIQUE" => "N",
						"TRANSLITERATION" => "N",
						"TRANS_LEN" => 100,
						"TRANS_CASE" => "L",
						"TRANS_SPACE" => "-",
						"TRANS_OTHER" => "-",
						"TRANS_EAT" => "Y",
						"USE_GOOGLE" => "N",
					),
				), 
			),
		);
		
		$iblock->Update($iblockID, $arFields);
		
		// get DB charset
		$sql='SHOW VARIABLES LIKE "character_set_database";';
		if(method_exists('\Bitrix\Main\Application', 'getConnection')){
			$db=\Bitrix\Main\Application::getConnection();
			$arResult = $db->query($sql)->fetch();
			$isUTF8 = $arResult['Value'] == 'utf8';
		}elseif(defined("BX_USE_MYSQLI") && BX_USE_MYSQLI === true){
			if($result = @mysqli_query($sql)){
				$arResult = mysql_fetch_row($result);
				$isUTF8 = $arResult[1] == 'utf8';
			}
		}elseif($result = @mysql_query($sql)){
			$arResult = mysql_fetch_row($result);
			$isUTF8 = $arResult[1] == 'utf8';
		}
		
		// check store user field UF_METRO & UF_MORE_PHOTOS
		if (!$arUserFieldMetro = CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'CAT_STORE', 'FIELD_NAME' => 'UF_METRO'))->Fetch()) {
			$ob = new CUserTypeEntity();
				$arFields = array(
				'ENTITY_ID' => 'CAT_STORE',
				'FIELD_NAME' => 'UF_METRO',
				'USER_TYPE_ID' => 'string',
				'XML_ID' => '',
				'SORT' => 100,
				'MULTIPLE' => 'Y',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				"SETTINGS" => array(
					'DEFAULT_VALUE' => '',
					'SIZE'          => '20',
					'ROWS'          => '1',
					'MIN_LENGTH'    => '0',
					'MAX_LENGTH'    => '0',
					'REGEXP'        => '',
				),
				'EDIT_FORM_LABEL'   => array(
					'en'    => 'Metro',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Метро') : 'Метро'),
				),
				'LIST_COLUMN_LABEL' => array(
					'en'    => 'Metro',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Метро') : 'Метро'),
				),
				'LIST_FILTER_LABEL' => array(
					'en'    => 'Metro',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Метро') : 'Метро'),
				),
				'ERROR_MESSAGE'     => array(
					'en'    => '',
					'ru'    => '',
				),
				'HELP_MESSAGE'      => array(
					'en'    => 'Metro',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Метро') : 'Метро'),
				),
			);
			$FIELD_ID = $ob->Add($arFields);
		}
		if (!$arUserFieldMetro = CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'CAT_STORE', 'FIELD_NAME' => 'UF_MORE_PHOTOS'))->Fetch()) {
			$ob = new CUserTypeEntity();
				$arFields = array(
				'ENTITY_ID' => 'CAT_STORE',
				'FIELD_NAME' => 'UF_MORE_PHOTOS',
				'USER_TYPE_ID' => 'file',
				'XML_ID' => '',
				'SORT' => 100,
				'MULTIPLE' => 'Y',
				'MANDATORY' => 'N',
				'SHOW_FILTER' => 'Y',
				'SHOW_IN_LIST' => 'Y',
				'EDIT_IN_LIST' => 'Y',
				'IS_SEARCHABLE' => 'N',
				"SETTINGS" => array(
					'SIZE'          => '20',
					'LIST_WIDTH'          => '0',
					'LIST_HEIGHT'    => '0',
					'MAX_SHOW_SIZE'    => '200',
					'MAX_ALLOWED_SIZE'        => '200',
					'EXTENSIONS'        => 'jpg, gif, bmp, png, jpeg',
				),
				'EDIT_FORM_LABEL'   => array(
					'en'    => 'Gallery',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Галерея') : 'Галерея'),
				),
				'LIST_COLUMN_LABEL' => array(
					'en'    => 'Gallery',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Галерея') : 'Галерея'),
				),
				'LIST_FILTER_LABEL' => array(
					'en'    => 'Gallery',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Галерея') : 'Галерея'),
				),
				'ERROR_MESSAGE'     => array(
					'en'    => '',
					'ru'    => '',
				),
				'HELP_MESSAGE'      => array(
					'en'    => 'Gallery',
					'ru'    => ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Галерея') : 'Галерея'),
				),
			);
			$FIELD_ID = $ob->Add($arFields);
		}
		
	}
	else{
		// attach iblock to site
		$arSites = array(); 
		$db_res = CIBlock::GetSite($iblockID);
		while ($res = $db_res->Fetch())
			$arSites[] = $res["LID"]; 
		if (!in_array(WIZARD_SITE_ID, $arSites)){
			$arSites[] = WIZARD_SITE_ID;
			$iblock = new CIBlock;
			$iblock->Update($iblockID, array("LID" => $arSites));
		}
	}

	// iblock user fields
	$dbSite = CSite::GetByID(WIZARD_SITE_ID);
	if($arSite = $dbSite -> Fetch()) $lang = $arSite["LANGUAGE_ID"];
	if(!strlen($lang)) $lang = "ru";
	WizardServices::IncludeServiceLang("editform_useroptions.php", $lang);
	$arProperty = array();
	$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockID));
	while($arProp = $dbProperty->Fetch())
		$arProperty[$arProp["CODE"]] = $arProp["ID"];

	// edit form user oprions
	CUserOptions::SetOption("form", "form_element_".$iblockID, array(
		"tabs" => 'edit1--#--'.GetMessage("WZD_OPTION_90").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_2").'--,--SORT--#--'.GetMessage("WZD_OPTION_44").'--,--NAME--#--'.GetMessage("WZD_OPTION_54").'--,--PROPERTY_'.$arProperty["ADDRESS"].'--#--'.GetMessage("WZD_OPTION_224").'--,--PROPERTY_'.$arProperty["PHONE"].'--#--'.GetMessage("WZD_OPTION_94").'--,--PROPERTY_'.$arProperty["EMAIL"].'--#--'.GetMessage("WZD_OPTION_96").'--,--PROPERTY_'.$arProperty["SCHEDULE"].'--#--'.GetMessage("WZD_OPTION_226").'--,--PROPERTY_'.$arProperty["METRO"].'--#--'.GetMessage("WZD_OPTION_228").'--,--PREVIEW_PICTURE--#--'.GetMessage("WZD_OPTION_172").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_174").'--,--PROPERTY_'.$arProperty["MORE_PHOTOS"].'--#--'.GetMessage("WZD_OPTION_12").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_16").'--,--PROPERTY_'.$arProperty["MAP"].'--#--'.GetMessage("WZD_OPTION_230").'--,--PROPERTY_'.$arProperty["STORE_ID"].'--#--'.GetMessage("WZD_OPTION_232").'--;--edit14--#--'.GetMessage("WZD_OPTION_18").'--,--IPROPERTY_TEMPLATES_ELEMENT_META_TITLE--#--'.GetMessage("WZD_OPTION_20").'--,--IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS--#--'.GetMessage("WZD_OPTION_22").'--,--IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION--#--'.GetMessage("WZD_OPTION_24").'--,--IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE--#--'.GetMessage("WZD_OPTION_188").'--,--IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE--#--'.GetMessage("WZD_OPTION_28").'--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT--#--'.GetMessage("WZD_OPTION_30").'--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE--#--'.GetMessage("WZD_OPTION_32").'--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME--#--'.GetMessage("WZD_OPTION_34").'--,--IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_36").'--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT--#--'.GetMessage("WZD_OPTION_30").'--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE--#--'.GetMessage("WZD_OPTION_32").'--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME--#--'.GetMessage("WZD_OPTION_34").'--,--SEO_ADDITIONAL--#--'.GetMessage("WZD_OPTION_84").'--,--TAGS--#--'.GetMessage("WZD_OPTION_46").'--;--;--',
	));
	// list user options
	CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockTYPE.".".$iblockID), array(
		'columns' => 'NAME,PROPERTY_'.$arProperty["ADDRESS"].',PREVIEW_PICTURE,PROPERTY_'.$arProperty["PHONE"].',PROPERTY_'.$arProperty["EMAIL"].',PROPERTY_'.$arProperty["SCHEDULE"].',PROPERTY_'.$arProperty["METRO"].',PROPERTY_'.$arProperty["MAP"].',PROPERTY_'.$arProperty["STORE_ID"].',ACTIVE,SORT,ID', 'by' => 'sort', 'order' => 'asc', 'page_size' => '20',
	));
}

if($iblockID){
	// replace macros IBLOCK_TYPE & IBLOCK_ID & IBLOCK_CODE
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_SHOPS_TYPE" => $iblockTYPE));
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_SHOPS_ID" => $iblockID));
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_SHOPS_CODE" => $iblockCODE));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_SHOPS_TYPE" => $iblockTYPE));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_SHOPS_ID" => $iblockID));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_SHOPS_CODE" => $iblockCODE));
}
?>
