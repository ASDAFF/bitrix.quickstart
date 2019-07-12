<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();


if(!CModule::IncludeModule("iblock"))
	return;
include_once(dirname(__FILE__)."/iblock_tools.php");
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/070_faq_faq_ru.xml"; 
$xml_dir = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID; 
$iblockCode = "faq-dfg"; 
$iblockType = "faq";
$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
$_SESSION["DEMO_IBLOCK_FAQ"] = CIBlockCMLImport::GetIBlockByXML_ID($iblockCode);
$iblock_id = $_SESSION["DEMO_IBLOCK_FAQ"];
if($_SESSION["DEMO_IBLOCK_FAQ"] === false){
	$iblock_id = DEMO_IBlock_ImportXML($xml_dir, "070_faq_faq-dfg_ru.xml", WIZARD_SITE_ID, false, false);
}
	$rsIBlock = CIBlock::GetList(	
		Array(), 	
		Array(		
			'TYPE'=>'faq',
			//'SITE_ID'=>WIZARD_SITE_ID,
			'ACTIVE'=>'Y',
			"CODE"=>'faq'
			), 
		true);
	$arReplace = array();
	echo $arReplace["SITE_ID"] = WIZARD_SITE_DIR;
	if($arIBlock = $rsIBlock->Fetch()){	
		$iblock_id = $arIBlock["ID"];
	}
	$arReplace["FAQ_IBLOCK_ID"] = $iblock_id;
	$ib = new CIBlock;
	$arFields = Array(
		"SITE_ID" => WIZARD_SITE_ID,
	);
	$res = $ib->Update($arIBlock["ID"], $arFields);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."faq/index.php", $arReplace);
?>


