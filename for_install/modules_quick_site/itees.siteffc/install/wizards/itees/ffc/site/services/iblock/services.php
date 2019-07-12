<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;
	
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/services.xml";
$iblockCode = "services_".WIZARD_SITE_ID; 
$iblockType = "services"; 
$iblockID = false; 

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
if($rsIBlock && $arIBlock = $rsIBlock->Fetch()){
	$iblockID = $arIBlock["ID"];
}

if ($iblockID == false){
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"services",
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
		"ACTIVE" => "Y",
		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), ), 
		"CODE" => $iblockCode, 
		"XML_ID" => $iblockCode,
		"NAME" => "[".WIZARD_SITE_ID."] ".$iblock->GetArrayByID($iblockID, "NAME")
	);
	$iblock->Update($iblockID, $arFields);
	
	//	deactivate sections
	$wizard =& $this->GetWizard();
	$arCompanyServices = $wizard->GetVar("company_services");
	foreach($arCompanyServices as $key=>$service){
		$arCompanyServices[$key] = "c_service_".$service;
	}
	$arFilter = array("IBLOCK_ID"=>$iblockID);
	$db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter);
	$arSections = array();
	while($ar_result = $db_list->GetNext()){
		$arSections[] = $ar_result;
	}
	
	foreach($arSections as $section){
		$bs = new CIBlockSection;
		if(in_array($section["CODE"], $arCompanyServices)){
			$arFields = array("ACTIVE" => "Y");
		}else{
			$arFields = array("ACTIVE" => "N");
		}
		$bs->Update($section["ID"], $arFields);
	}
	
}

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/services/index.php", array("SERVICES_IBLOCK_ID" => $iblockID, "SERVICES_SEF_FOLDER" => WIZARD_SITE_DIR."services/"));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", array("SERVICES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array("SERVICES_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/services.php", array("SERVICES_IBLOCK_ID" => $iblockID));
?>