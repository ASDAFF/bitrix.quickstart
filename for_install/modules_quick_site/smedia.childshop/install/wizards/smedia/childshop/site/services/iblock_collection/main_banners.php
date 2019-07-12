<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockCode = "main_banners_".WIZARD_SITE_ID; 
$iblockType = "services"; 
$iblockID = false;
$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/'.LANGUAGE_ID."/main_banners.xml"; 

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
{
	$iblockID=$arIBlock['ID'];
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."_index.php", Array("main_banners_IBLOCK_ID" => $iblockID));

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
		    "STRING_ID"  => $grCode 
		);
		$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); // 㼡鱠檠䱳𐰻
		if($arGroup=$rsGroups->GetNext())
			$arrGrId[$arGroup['STRING_ID']]=$arGroup['ID'];
	}
	
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"main_banners",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
"2" => "R",

		)		
	);
	
	if ($iblockID < 1)
		return;
		
	$arProperties = Array("LINK",);
	$arrPropID=array();
	foreach ($arProperties as $propertyName)
	{
		$arrPropID[$propertyName] = 0;
		$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockID, "CODE" => $propertyName));
		if ($arProperty = $properties->Fetch())
			$arrPropID[$propertyName] = $arProperty["ID"];
	}
	
	
	WizardServices::SetIBlockFormSettings($iblockID, Array ( 'tabs' => 'edit1--#--'.GetMessage('iblock_main_banners_edit1').'--,--ACTIVE--#--'.GetMessage('iblock_main_banners_ACTIVE').'--,--ACTIVE_FROM--#--'.GetMessage('iblock_main_banners_ACTIVE_FROM').'--,--ACTIVE_TO--#--'.GetMessage('iblock_main_banners_ACTIVE_TO').'--,--NAME--#--*'.GetMessage('iblock_main_banners_NAME').'--,--PREVIEW_PICTURE--#--'.GetMessage('iblock_main_banners_PREVIEW_PICTURE').'--,--PROPERTY_'.$arrPropID['LINK'].'--#--'.GetMessage('iblock_main_banners_LINK').'--;--', ));
			
}

if($iblockID)
{
	//IBlock fields
	$iblock = new CIBlock;
		
	$arFields =
				Array(
				'XML_ID' => $iblockCode,
				'EXTERNAL_ID' => $iblockCode,
				'CODE' => $iblockCode,
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
								'DEFAULT_VALUE' => '',
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
								'IS_REQUIRED' => 'Y',
								'DEFAULT_VALUE' => array(
										'FROM_DETAIL' => 'N',
										'SCALE' => 'Y',
										'WIDTH' => '676',
										'HEIGHT' => '400',
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
				'RSS_ACTIVE' => 'Y',
				'RSS_FILE_ACTIVE' => 'N',
				'RSS_FILE_LIMIT' => '',
				'RSS_FILE_DAYS' => '',
				'RSS_YANDEX_ACTIVE' => 'N',
				'TMP_ID' => '',
				'BIZPROC' => 'N',
				'SECTION_CHOOSER' => 'L',
				'LIST_MODE' => '',
				'RIGHTS_MODE' => 'S',
				'VERSION' => '1',
				'LAST_CONV_ELEMENT' => '0',
				'SOCNET_GROUP_ID' => '',
				'EDIT_FILE_BEFORE' => '',
				'EDIT_FILE_AFTER' => '',
				);

	
	$iblock->Update($iblockID, $arFields);
	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."_index.php", Array("main_banners_IBLOCK_ID" => $iblockID));

}
?>
