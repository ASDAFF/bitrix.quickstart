<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH.'/xml/_all/brands-'.LANGUAGE_ID.'.xml';
$iblockCode = 'brands';
$iblockCodeWizPrefix = '_redsign_gopro';
$iblockXmlID = $iblockCode.'_'.WIZARD_SITE_ID;
$iblockType = 'presscenter';
$iblockID = false;

$rsIBlock = CIBlock::GetList(array(), array('CODE' => $iblockCode, 'XML_ID' => $iblockXmlID, 'TYPE' => $iblockType));
if($rsIBlock && $arIBlock = $rsIBlock->Fetch()){
	$iblockID = $arIBlock['ID'];
}

if($iblockID == false){
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		$iblockCode.$iblockCodeWizPrefix,
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			'1' => 'X',
			'2' => 'R'
		)
	);

	if($iblockID < 1)
	{
		$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $iblockType, 'CODE' => $iblockCode.$iblockCodeWizPrefix, 'XML_ID' => $iblockXmlID ));
		if($arIBlock = $rsIBlock->Fetch())
		{
			$arrIBlockIDs[$iblockCode] = $arIBlock['ID'];
		}
		$iblockID = $arrIBlockIDs[$iblockCode];
	}

	if($iblockID < 1)
		return;

	//IBlock fields settings
	$iblock = new CIBlock;
	$arFields = Array(
		'ACTIVE' => 'Y',
		'FIELDS' => array (
			'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ),
			'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ),
			'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'Y', 'SCALE' => 'Y', 'WIDTH' => '1000', 'HEIGHT' => '800', 'IGNORE_ERRORS' => 'N', ), ),
			'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ),
			'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'Y', 'WIDTH' => '1000', 'HEIGHT' => '800', 'IGNORE_ERRORS' => 'N', ), ),
			'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ),
			'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
			'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => '100', 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N' ), ),
			'SECTION_CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => '100', 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N' ), ),
			'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
		),
		'CODE' => $iblockCode,
		'XML_ID' => $iblockXmlID,
		'WF_TYPE' => 'N',
		'NAME' => $iblock->GetArrayByID($iblockID, 'NAME')
	);
	$iblock->Update($iblockID, $arFields);
	
	// HL prop brands
	$arNewFields = array();
	$res = CIBlockProperty::GetByID('BRAND',$iblockID);
	if($arFields = $res->GetNext())
	{
		$arNewFields['USER_TYPE'] = $arFields['USER_TYPE'];
		$arNewFields['USER_TYPE_SETTINGS']['TABLE_NAME'] = 'prokids_brand_reference';
		
		$ibp = new CIBlockProperty;
		if(!$ibp->Update($arFields['ID'], $arNewFields))
		{
			// ERROR
		}
	}
	
	// propeties with sections bind
	$arrFilter1 = array(
		array(
			'IBLOCK_TYPE' => 'catalog',
			'IBLOCK_CODE' => 'catalog',
			'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
		),
	);
	foreach($arrFilter1 as $filter1){
		$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $filter1['IBLOCK_TYPE'], 'CODE' => $filter1['IBLOCK_CODE'], 'XML_ID' => $filter1['IBLOCK_XML_ID'] ));
		if($arIBlock = $rsIBlock->Fetch()){
			$code1 = $filter1['IBLOCK_CODE'];
			$arrIBlockIDs[$code1] = $arIBlock['ID'];
		}
	}
	$arNewFields = array();
	$res = CIBlockProperty::GetByID('SECTIONS',$iblockID);
	if($arFields = $res->GetNext()) {
		$arNewFields['USER_TYPE'] = $arFields['USER_TYPE'];
		$arNewFields['USER_TYPE_SETTINGS']['PROPERTY_LINK_IBLOCK_TYPE_ID'] = 'catalog';
		$arNewFields['USER_TYPE_SETTINGS']['PROPERTY_LINK_IBLOCK_ID'] = $arrIBlockIDs['catalog'];
		
		$ibp = new CIBlockProperty;
		if(!$ibp->Update($arFields['ID'], $arNewFields)) {
			// ERROR
		}
	}
}
else{
	$arSites = array();
	$db_res = CIBlock::GetSite($iblockID);
	while($res = $db_res->Fetch())
		$arSites[] = $res['LID'];
	if(!in_array(WIZARD_SITE_ID, $arSites)){
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array('LID' => $arSites));
	}
}