<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockCode = "info_".WIZARD_SITE_ID; 
$iblockType = "news"; 
$iblockID = false;
$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/'.LANGUAGE_ID."/info.xml"; 

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType,'SITE_ID'=>WIZARD_SITE_ID));
if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
{
	$iblockID=$arIBlock['ID'];
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."_index.php", Array("info_IBLOCK_ID" => $iblockID));

	$arSites = array(); 
	$db_res = CIBlock::GetSite($iblockID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"]; 
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID; 
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array("LID" => $arSites));
	}
	return;
}

if ($iblockID == false)
{
	$findGr=array();
	$arrGrId=array();
	foreach($findGr as $grCode)
	{
		$filter = Array
		(   
			"STRING_ID"  => $grCode // до 10 пользователей
		);
		$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); // выбираем группы
		if($arGroup=$rsGroups->GetNext())
			$arrGrId[$arGroup['STRING_ID']]=$arGroup['ID'];
	}
	
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"sm_info_tmp",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
"2" => "R",

		)		
	);
	
	if ($iblockID < 1)
		return;
		
	$arProperties = Array();
	$arrPropID=array();
	foreach ($arProperties as $propertyName)
	{
		$arrPropID[$propertyName] = 0;
		$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockID, "CODE" => $propertyName));
		if ($arProperty = $properties->Fetch())
			$arrPropID[$propertyName] = $arProperty["ID"];
	}
	
	
				
}

if($iblockID)
{
	$rsIBlock = CIBlock::GetList(array(), array("ID" => $iblockID));
	if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
		$iblockName='['.WIZARD_SITE_ID.'] '.$arIBlock['NAME'];
	//IBlock fields
	$iblock = new CIBlock;
		
	$arFields =
				Array(
				"CODE" => $iblockCode, 
				"XML_ID" => $iblockCode,
				'EXTERNAL_ID' => $iblockCode,
				'NAME' => $iblockName,
				'ACTIVE' => 'Y',
				'FIELDS' => array(
						'IBLOCK_SECTION' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'ACTIVE' => array(
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => 'Y',
								),
						'ACTIVE_FROM' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '=today',
								),
						'ACTIVE_TO' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'SORT' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '0',
								),
						'NAME' => array(
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => '',
								),
						'PREVIEW_PICTURE' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => array(
										'FROM_DETAIL' => 'N',
										'SCALE' => 'N',
										'WIDTH' => '',
										'HEIGHT' => '',
										'IGNORE_ERRORS' => 'N',
										'METHOD' => 'resample',
										'COMPRESSION' => '95',
										'DELETE_WITH_DETAIL' => 'N',
										'UPDATE_WITH_DETAIL' => 'N',
										),
								),
						'PREVIEW_TEXT_TYPE' => array(
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => 'text',
								),
						'PREVIEW_TEXT' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'DETAIL_PICTURE' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => array(
										'SCALE' => 'N',
										'WIDTH' => '',
										'HEIGHT' => '',
										'IGNORE_ERRORS' => 'N',
										'METHOD' => 'resample',
										'COMPRESSION' => '95',
										),
								),
						'DETAIL_TEXT_TYPE' => array(
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => 'text',
								),
						'DETAIL_TEXT' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'XML_ID' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'CODE' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => array(
										'UNIQUE' => 'N',
										'TRANSLITERATION' => 'N',
										'TRANS_LEN' => '100',
										'TRANS_CASE' => 'L',
										'TRANS_SPACE' => '-',
										'TRANS_OTHER' => '-',
										'TRANS_EAT' => 'Y',
										'USE_GOOGLE' => 'N',
										),
								),
						'TAGS' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'SECTION_NAME' => array(
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => '',
								),
						'SECTION_PICTURE' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => array(
										'FROM_DETAIL' => 'N',
										'SCALE' => 'N',
										'WIDTH' => '',
										'HEIGHT' => '',
										'IGNORE_ERRORS' => 'N',
										'METHOD' => 'resample',
										'COMPRESSION' => '95',
										'DELETE_WITH_DETAIL' => 'N',
										'UPDATE_WITH_DETAIL' => 'N',
										),
								),
						'SECTION_DESCRIPTION_TYPE' => array(
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => 'text',
								),
						'SECTION_DESCRIPTION' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'SECTION_DETAIL_PICTURE' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => array(
										'SCALE' => 'N',
										'WIDTH' => '',
										'HEIGHT' => '',
										'IGNORE_ERRORS' => 'N',
										'METHOD' => 'resample',
										'COMPRESSION' => '95',
										),
								),
						'SECTION_XML_ID' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => '',
								),
						'SECTION_CODE' => array(
								'IS_REQUIRED' => 'N',
								'DEFAULT_VALUE' => array(
										'UNIQUE' => 'N',
										'TRANSLITERATION' => 'N',
										'TRANS_LEN' => '100',
										'TRANS_CASE' => 'L',
										'TRANS_SPACE' => '-',
										'TRANS_OTHER' => '-',
										'TRANS_EAT' => 'Y',
										'USE_GOOGLE' => 'N',
										),
								),
						),
				'DESCRIPTION' => '',
				'DESCRIPTION_TYPE' => 'text',
				'RSS_TTL' => '24',
				'RSS_ACTIVE' => 'N',
				'RSS_FILE_ACTIVE' => 'N',
				'RSS_FILE_LIMIT' => '10',
				'RSS_FILE_DAYS' => '7',
				'RSS_YANDEX_ACTIVE' => 'N',
				'TMP_ID' => '',
				'BIZPROC' => 'N',
				'SECTION_CHOOSER' => 'L',
				'LIST_MODE' => '',
				'RIGHTS_MODE' => '',
				'VERSION' => '1',
				'LAST_CONV_ELEMENT' => '0',
				'SOCNET_GROUP_ID' => '',
				'EDIT_FILE_BEFORE' => '',
				'EDIT_FILE_AFTER' => '',
				);

	
	$iblock->Update($iblockID, $arFields);
	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."_index.php", Array("info_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."/info/index.php", Array("info_IBLOCK_ID" => $iblockID));

}
?>
