<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule("iblock"))
	return;
include_once(dirname(__FILE__)."/iblock_tools.php");
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/040_slider_slides_ru.xml"; 
$xml_dir = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID; 
$iblockCode = "slides-dfg"; 
$iblockType = "slider";
$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
$_SESSION["DEMO_IBLOCK_SLIDER"] = CIBlockCMLImport::GetIBlockByXML_ID($iblockCode);

$iblock_id = $_SESSION["DEMO_IBLOCK_SLIDER"];

if($_SESSION["DEMO_IBLOCK_SLIDER"] === false){
	$iblock_id = DEMO_IBlock_ImportXML($xml_dir, "040_slider_slides-dfg_ru.xml", WIZARD_SITE_ID, false, false);
}
	$rsIBlock = CIBlock::GetList(	
		Array(), 	
		Array(		
			'TYPE'=>'slider',
			//'SITE_ID'=>WIZARD_SITE_ID,
			'ACTIVE'=>'Y',
			"CODE"=>'slides'
			), 
		true);
	$arReplace = array();
	echo $arReplace["SITE_ID"] = WIZARD_SITE_DIR;
	if($arIBlock = $rsIBlock->Fetch()){	
		$iblock_id = $arIBlock["ID"];		
	}
	$arReplace["SLIDER_IBLOCK_ID"] = $iblock_id;
	$ib = new CIBlock;
	$arFields = Array(
		"SITE_ID" => WIZARD_SITE_ID,
	);
	$res = $ib->Update($arIBlock["ID"], $arFields);
	$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
	CWizardUtil::ReplaceMacros($bitrixTemplateDir . '/header.php', $arReplace);
?>


