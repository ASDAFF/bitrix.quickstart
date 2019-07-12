<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;
    
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/parents_questions.xml"; 
$iblockCode = "parents_questions_".WIZARD_SITE_ID; 
$iblockCodeOriginal = "parents_questions"; 
$iblockType = "services"; 

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
        print 'Error on create iblock "parents_questions"';
        return;
    }
	
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), ), 
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

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/parents/question/index.php", array("PARENTS_QUESTIONS_IBLOCK_ID" => $iblockID));

$needProperties = array('CONTACTS', 'SUBJECT');
$needPropertiesRequired = array('CONTACTS');

$arrProperties = array('NAME', 'PREVIEW_TEXT');
$arrPropertiesRequired = array('NAME', 'PREVIEW_TEXT');

$properties = CIBlockProperty::GetList(Array("sort"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockID));
while ($prop_fields = $properties->GetNext()){  
   if(in_array($prop_fields['CODE'], $needProperties)){
        $arrProperties[] = $prop_fields['ID'];
    }    
    if(in_array($prop_fields['CODE'], $needPropertiesRequired)){
        $arrPropertiesRequired[] = $prop_fields['ID'];
    }    
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/parents/question/index.php", array("PROPERTY_CODES" => var_export($arrProperties, true)));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/parents/question/index.php", array("PROPERTY_CODES_REQUIRED" => var_export($arrPropertiesRequired, true)));
?>
