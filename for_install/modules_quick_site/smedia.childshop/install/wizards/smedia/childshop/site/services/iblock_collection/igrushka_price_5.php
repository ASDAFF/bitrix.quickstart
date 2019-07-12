<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;	

if (COption::GetOptionString('smedia.childshop', 'catalog_installed', 'N',WIZARD_SITE_ID)!=='Y') {
	$iblockCode = "igrushka_".WIZARD_SITE_ID; 
	$iblockType = "catalog"; 
	$iblockID = false;
	$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/'.LANGUAGE_ID."/igrushka_5.xml"; 
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		'sm_igrushka_tmp',
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "W",
			"2" => "R",
		),
		true
	);
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
		'ACTIVE' => 'Y',
		'NAME' => $iblockName,
		'FIELDS' => array(
			'IBLOCK_SECTION' => array(
					'IS_REQUIRED' => 'Y',
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
					'IS_REQUIRED' => 'Y',
					'DEFAULT_VALUE' => array(
							'UNIQUE' => 'Y',
							'TRANSLITERATION' => 'Y',
							'TRANS_LEN' => '100',
							'TRANS_CASE' => 'L',
							'TRANS_SPACE' => '_',
							'TRANS_OTHER' => '_',
							'TRANS_EAT' => 'Y',
							'USE_GOOGLE' => 'Y',
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
					'IS_REQUIRED' => 'Y',
					'DEFAULT_VALUE' => array(
							'UNIQUE' => 'Y',
							'TRANSLITERATION' => 'Y',
							'TRANS_LEN' => '100',
							'TRANS_CASE' => 'L',
							'TRANS_SPACE' => '_',
							'TRANS_OTHER' => '_',
							'TRANS_EAT' => 'Y',
							'USE_GOOGLE' => 'Y',
							),
					),
			),
		'DESCRIPTION' => '',
		'DESCRIPTION_TYPE' => 'html',
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
		'RIGHTS_MODE' => '',
		'VERSION' => '1',
		'LAST_CONV_ELEMENT' => '0',
		'SOCNET_GROUP_ID' => '',
		'EDIT_FILE_BEFORE' => '',
		'EDIT_FILE_AFTER' => '',
	);

	
	$iblock->Update($iblockID, $arFields);
}
?>
