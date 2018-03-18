<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule("iblock"))
	return;
include_once(dirname(__FILE__)."/iblock_tools.php");
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/050_requests_user-request_ru.xml"; 
$xml_dir = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID; 
$iblockCode = "user-request-dfg"; 
$iblockType = "requests";


$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
$_SESSION["DEMO_IBLOCK_FEEDBACK"] = CIBlockCMLImport::GetIBlockByXML_ID($iblockCode);

$iblock_id = $_SESSION["DEMO_IBLOCK_FEEDBACK"];
if($_SESSION["DEMO_IBLOCK_FEEDBACK"] === false){
	$iblock_id = DEMO_IBlock_ImportXML($xml_dir, "050_requests_user-request-dfg_ru.xml", WIZARD_SITE_ID, false, false);
}
	$rsIBlock = CIBlock::GetList(	
		Array(), 	
		Array(		
			'TYPE'=>$iblockType,
			//'SITE_ID'=>WIZARD_SITE_ID,
			'ACTIVE'=>'Y',
			"CODE"=>$iblockCode
			), 
		true);


	$arReplace = array();
	echo $arReplace["SITE_ID"] = WIZARD_SITE_DIR;
	if($arIBlock = $rsIBlock->Fetch()){	
		$iblock_id = $arIBlock["ID"];
	}
	
	$arReplace["FEEDBACK_IBLOCK_ID"] = $iblock_id;
	$ib = new CIBlock;
	$arFields = Array(
		"SITE_ID" => WIZARD_SITE_ID,
	);
	$res = $ib->Update($iblock_id, $arFields);


	if(intval($arReplace["FEEDBACK_IBLOCK_ID"])>0){
	
		$rsProperty = CIBlockProperty::GetByID("PHONE", $arReplace["FEEDBACK_IBLOCK_ID"], $iblockCode);
		if($arProperty = $rsProperty->GetNext())
			$arReplace["PROPERTY_PHONE"] = $arProperty['ID'];


		$rsProperty = CIBlockProperty::GetByID("REQUEST_SERVICE", $arReplace["FEEDBACK_IBLOCK_ID"], $iblockCode);
		if($arProperty = $rsProperty->GetNext()){
			$arReplace["PROPERTY_REQUEST_SERVICE"] = $arProperty['ID'];
			
			$wizard =& $this->GetWizard();
			$Services = $wizard->GetVar("siteServices");
			if(strlen($Services)>0){
				$arServices = explode("\n", $Services);
				//CIBlockPropertyEnum::UpdateEnum($arProperty['ID'], $arServices);
				foreach($arServices as $service){
					$newPropertyEnum = new CIBlockPropertyEnum;
					$newPropertyEnum->Add(Array('PROPERTY_ID'=>$arProperty['ID'], 'VALUE'=>$service));
				}
				
			}else{
				$newPropertyEnum = new CIBlockPropertyEnum;
				$newPropertyEnum->Add(Array('PROPERTY_ID'=>$arProperty['ID'], 'VALUE'=>GetMessage("NULL_SERVICE_NAME")));
			}
		}


		$rsProperty = CIBlockProperty::GetByID("REQUEST", $arReplace["FEEDBACK_IBLOCK_ID"], $iblockCode);
		if($arProperty = $rsProperty->GetNext())
			$arReplace["PROPERTY_REQUEST"] = $arProperty['ID'];

		$rsProperty = CIBlockProperty::GetByID("REQUEST_TYPE", $arReplace["FEEDBACK_IBLOCK_ID"], $iblockCode);
		if($arProperty = $rsProperty->GetNext())
			$arReplace["PROPERTY_HIDE_ID"] = $arProperty['ID'];


		$db_enum_list = CIBlockProperty::GetPropertyEnum("REQUEST_TYPE", Array(), Array("IBLOCK_ID"=>$arReplace["FEEDBACK_IBLOCK_ID"]));
		while($arPropertyEnum = $db_enum_list->GetNext()){ 
			$arReplace["PROPERTY_HIDE_".strtoupper($arPropertyEnum["XML_ID"])] = $arPropertyEnum["ID"];
		}


		$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
		CWizardUtil::ReplaceMacros($bitrixTemplateDir . '/header.php', $arReplace);
		CWizardUtil::ReplaceMacros($bitrixTemplateDir . '/footer.php', $arReplace);
	}
?>


