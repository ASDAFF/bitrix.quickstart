<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

    
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/video.xml"; 
$iblockCode = "video_".WIZARD_SITE_ID; 
$iblockCodeOriginal = "video"; 
$iblockType = "photos"; 

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
    $iblockID = $arIBlock["ID"]; 
    if (WIZARD_INSTALL_DEMO_DATA)
    {
        CIBlock::Delete($arIBlock["ID"]); 
        $iblockID = false; 
    }
}

if($iblockID == false)
{
    $permissions = Array(
            "1" => "X",
            "2" => "R"
        );
    $dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
    if($arGroup = $dbGroup -> Fetch())
    {
        $permissions[$arGroup["ID"]] = 'W';
    };
    $iblockID = WizardServices::ImportIBlockFromXML(
        $iblockXMLFile,
        $iblockCodeOriginal,
        $iblockType,
        WIZARD_SITE_ID,
        $permissions
    );

    if ($iblockID < 1) {
        print 'Error on create iblock "video"';
        return;
    }

      
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"CODE" => $iblockCode, 
		"XML_ID" => $iblockCode,
		"NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
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

$typePropertyID = 0;
$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockID, "CODE" => "video"));
if ($arProperty = $properties->Fetch())
    {$typePropertyID = $arProperty["ID"];}
    
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/mediagallery/video/index.php",
	Array(
		"VIDEO_IBLOCK_ID" => $iblockID,
		"VIDEO_PROPERTY_ID" => $typePropertyID
	)
    
        
);
?>
