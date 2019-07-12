<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockType = 'sw_catalog';
$arIblockContent = array(
	'sw_category' => array (
		'FILE' => WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/sw_catalog/category.xml",
		'FIELDS' => array(
			// elements
			'IBLOCK_SECTION' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'ACTIVE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'Y'
			),
			'ACTIVE_FROM' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => '=today',
			),
			'ACTIVE_TO' => array (
				'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''
			),
			'SORT' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'NAME' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => ''
			),
			'PREVIEW_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'FROM_DETAIL' => 'N',
					'SCALE' => 'Y',
					'WIDTH' => '620',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 100,
					'DELETE_WITH_DETAIL' => 'N',
					'UPDATE_WITH_DETAIL' => 'N'
				)
			),
			'PREVIEW_TEXT_TYPE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'html'
			),
			'PREVIEW_TEXT' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'DETAIL_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'SCALE' => 'N',
					'WIDTH' => '',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 95
				)
			),
			'DETAIL_TEXT_TYPE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'html'
			),
			'DETAIL_TEXT' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'XML_ID' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'CODE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => array (
					'UNIQUE' => 'Y',
					'TRANSLITERATION' => 'Y',
					'TRANS_LEN' => 100,
					'TRANS_CASE' => 'L',
					'TRANS_SPACE' => '_',
					'TRANS_OTHER' => '_',
					'TRANS_EAT' => 'Y',
					'USE_GOOGLE' => 'N',
				)
			),
			'TAGS' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			
			// sections
			'SECTION_NAME' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => ''
			),
			'SECTION_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'FROM_DETAIL' => 'N',
					'SCALE' => 'N',
					'WIDTH' => '',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 95,
					'DELETE_WITH_DETAIL' => 'N',
					'UPDATE_WITH_DETAIL' => 'N'
				)
			),
			'SECTION_DESCRIPTION_TYPE' => array (
				'IS_REQUIRED' => 'Y',
				'DEFAULT_VALUE' => 'text'
			),
			'SECTION_DESCRIPTION' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'SECTION_DETAIL_PICTURE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'SCALE' => 'N',
					'WIDTH' => '',
					'HEIGHT' => '',
					'IGNORE_ERRORS' => 'N',
					'METHOD' => 'resample',
					'COMPRESSION' => 95
				)
			),
			'SECTION_XML_ID' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => ''
			),
			'SECTION_CODE' => array (
				'IS_REQUIRED' => 'N',
				'DEFAULT_VALUE' => array (
					'UNIQUE' => 'N',
					'TRANSLITERATION' => 'N',
					'TRANS_LEN' => 100,
					'TRANS_CASE' => 'L',
					'TRANS_SPACE' => '_',
					'TRANS_OTHER' => '_',
					'TRANS_EAT' => 'Y',
					'USE_GOOGLE' => 'N'
				)
			)
		),
		'FORM' => array(),
		'LIST' => array()
	),
);

foreach ($arIblockContent as $iblockCode => $iblockFields) {
	if (COption::GetOptionString("softeffect.storesoftware", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA){
		if ($wizard->GetVar('rewriteIndex', true)) {
			$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode.'_'.WIZARD_SITE_ID, "TYPE" => $iblockType));
			$iblockID = false; 
			if ($arIBlock = $rsIBlock->Fetch()) {
				CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/admin-config/config.php", array($iblockCode => $arIBlock['ID']));
			}
		}
		return;
	}
	
	$iblockXMLFile = $iblockFields['FILE']; 
	
	$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode.'_'.WIZARD_SITE_ID, "TYPE" => $iblockType));
	$iblockID = false; 
	if ($arIBlock = $rsIBlock->Fetch()) {
		$iblockID = $arIBlock["ID"]; 
		if (WIZARD_INSTALL_DEMO_DATA) {
			CIBlock::Delete($arIBlock["ID"]); 
			$iblockID = false; 
		}
	}
	
	if($iblockID == false) {
		$permissions = Array(
			"1" => "X",
			"2" => "R"
		);
		
		$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
		if($arGroup = $dbGroup -> Fetch()) {
			$permissions[$arGroup["ID"]] = 'W';
		};
		$iblockID = WizardServices::ImportIBlockFromXML(
			$iblockXMLFile,
			$iblockCode,
			$iblockType,
			WIZARD_SITE_ID,
			$permissions
		);
	
		if ($iblockID < 1) return;
		
		//IBlock fields
		$iblock = new CIBlock;
		$arFields = Array(
			"ACTIVE" => "Y",
			"FIELDS" => $iblockFields['FIELDS'],
			"CODE" => $iblockCode, 
			"XML_ID" => $iblockCode.'_'.WIZARD_SITE_ID,
		);
		
		$iblock->Update($iblockID, $arFields);
	} else {
		$arSites = array(); 
		$db_res = CIBlock::GetSite($iblockID);
		while ($res = $db_res->Fetch())
			$arSites[] = $res["LID"]; 
		
		if (!in_array(WIZARD_SITE_ID, $arSites)) {
			$arSites[] = WIZARD_SITE_ID;
			$iblock = new CIBlock;
			$iblock->Update($iblockID, array("LID" => $arSites));
		}
	}
	
	/*$dbSite = CSite::GetByID(WIZARD_SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$lang = $arSite["LANGUAGE_ID"];
	if(strlen($lang) <= 0)
		$lang = "ru";
		
	WizardServices::IncludeServiceLang("content.php", $lang);
	CUserOptions::SetOption("form", "form_element_".$iblockID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_NEWS_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_NEWS_2").'--,--ACTIVE_FROM--#--'.GetMessage("WZD_OPTION_NEWS_3").'--,--NAME--#--'.GetMessage("WZD_OPTION_NEWS_5").'--,--CODE--#--'.GetMessage("WZD_OPTION_NEWS_6").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_NEWS_8").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_NEWS_10").'--;--', ));
	CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'NAME,ACTIVE,DATE_ACTIVE_FROM', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));*/

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/admin-config/config.php", array($iblockCode => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array($iblockCode => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/category.php", array($iblockCode => $iblockID));
	CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."/footer.php", array($iblockCode => $iblockID));
}
?>