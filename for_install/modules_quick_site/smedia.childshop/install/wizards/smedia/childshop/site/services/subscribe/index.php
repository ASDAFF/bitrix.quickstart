<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("subscribe") || !CModule::IncludeModule("iblock"))
	return;

//Copy template
CopyDirFiles(
	WIZARD_SERVICE_ABSOLUTE_PATH."/templates/".LANGUAGE_ID."/",
	$_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/subscribe/templates/",
	$rewrite = false,
	$recursive = true
);
$subscribeMacros=array(
	Array(
			'TEMPLATE' => 'store_news_s1',
			),

);
foreach($subscribeMacros as $macros)
{
	$IBlockID = "";
	if($macros['IBLOCK_CODE'])
	{
		$dbIBlock = CIBlock::GetList(Array(), Array("CODE" => $macros['IBLOCK_CODE']));	
		if ($arIBlock = $dbIBlock->Fetch())		
			$IBlockID = $arIBlock["ID"];			
	}
	CWizardUtil::ReplaceMacros(
			$_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/subscribe/templates/".$macros['TEMPLATE']."/template.php",
			Array(
				"IBLOCK_ID" => $IBlockID,
				"SITE_ID" => WIZARD_SITE_ID,
			)
	);
}
$obRubric = new CRubric;
$obPosting = new CPosting();
$arRubrics=array(
		Array(
			'NAME' => GetMessage('rubric_1_NAME'),
			'SORT' => '100',
			'ACTIVE' => 'Y',
			'DESCRIPTION' => GetMessage('rubric_1_DESCRIPTION'),
			'AUTO' => 'Y',
			'VISIBLE' => 'Y',
			'FROM_FIELD' => 'sheshukova@smedia.ru',
			'DAYS_OF_MONTH' => '',
			'DAYS_OF_WEEK' => '1,2,3,4,5,6,7',
			'TIMES_OF_DAY' => '05:00',
			'TEMPLATE' => 'bitrix/php_interface/subscribe/templates/store_news_s1',
			'POSTING' => array(
					),
			),

);
foreach($arRubrics as $rubric)
{
	$rsRubric = CRubric::GetList(Array(), Array("NAME" => $rubric['NAME'], "LID" => WIZARD_SITE_ID));
	if(!$rsRubric->Fetch())
	{
		$rubric['LID']=WIZARD_SITE_ID;
		$rubric['FROM_FIELD']=COption::GetOptionString("main", "email_from", "admin@".$_SERVER["SERVER_NAME"]);
		if($rubric['AUTO']=="Y")
			$rubric["LAST_EXECUTED"]= ConvertTimeStamp(false, "FULL");
		$arToAdd=$rubric;
		unset($arToAdd['POSTING']);
		$ID = $obRubric->Add($rubric);
		if($ID && $rubric['POSTING'])
		{
			foreach($rubric['POSTING'] as $posting)
			{
				$posting['CHARSET']=LANG_CHARSET;
				$posting["RUB_ID"]	= Array($ID);
				$posting["FROM_FIELD"] = COption::GetOptionString("main", "email_from", "admin@".$_SERVER["SERVER_NAME"]);
				$posting["TO_FIELD"] = COption::GetOptionString("main", "email_from", "admin@".$_SERVER["SERVER_NAME"]);
				$obPosting->Add($posting);
			}
		}
	}
}
?>