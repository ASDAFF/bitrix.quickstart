<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockID=0;
$c=1;

$add_path=$wizard->GetVar("install_data");
$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/faq.xml'; 
$iblockCode = "faq_".WIZARD_SITE_ID; 
$iblockType = "services"; 
$iblockID = false;

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]); 
		$iblockID = false; 
	}
	else
	{
		if(!$iblockID)
		{
			$rsIBlock = CIBlock::GetList(array(), array("CODE" => "faq", "TYPE" => $iblockType));
			if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())		
				$iblockID = $arIBlock["ID"]; 
		}
		$arFilter = Array
		(
			'IBLOCK_ID'=>$iblockID, 
			'ACTIVE'=>'Y', 
			'DEPTH_LEVEL'=>1
		);	
		$dep_res = CIBlockSection::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter);
		while($dep_result = $dep_res->GetNext())
		    $faqSection=$dep_result['ID'];
		
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."about/faq/index.php", Array("SERVICES_IBLOCK_ID" => $iblockID));
	        CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."about/faq/index.php", Array("SERVICES_IBLOCK_SECTION" => $faqSection));
	}
}

if ($iblockID == false)
{
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"tmp_household_faq",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
			"2" => "R"
		)
	);
	
	if ($iblockID < 1)
		return;
	
	$iblock = new CIBlock;
		
	$arFields = Array(
				'XML_ID' => $iblockCode,
				'EXTERNAL_ID' => $iblockCode,
				'CODE' => $iblockCode,			
			);
	
	$iblock->Update($iblockID, $arFields);
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
	}
}
$arFilter = Array
	(
		'IBLOCK_ID'=>$iblockID, 
		'ACTIVE'=>'Y', 
		'DEPTH_LEVEL'=>1
	);

$dep_res = CIBlockSection::GetList(Array("SORT"=>"ASC", "PROPERTY_PRIORITY"=>"ASC"), $arFilter);
while($dep_result = $dep_res->GetNext())
    $faqSection=$dep_result['ID'];

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."about/faq/index.php", Array("SERVICES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."about/faq/index.php", Array("SERVICES_IBLOCK_SECTION" => $faqSection));

?>