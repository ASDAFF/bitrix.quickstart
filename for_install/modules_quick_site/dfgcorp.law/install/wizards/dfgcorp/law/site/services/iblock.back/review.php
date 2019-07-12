<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

//Library
include_once(dirname(__FILE__)."/iblock_tools.php");
	
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/030_clients_review_ru.xml"; 
$xml_dir = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID; 


$iblockCode = "review"; 
$iblockType = "clients";

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 

$_SESSION["DEMO_IBLOCK_BOOKS"] = CIBlockCMLImport::GetIBlockByXML_ID($iblockCode);
//Import XML
if($_SESSION["DEMO_IBLOCK_BOOKS"] === false)
{
	$iblock_id = DEMO_IBlock_ImportXML($xml_dir, "030_clients_review_ru.xml", WIZARD_SITE_ID, false, false);
}

	$rsIBlock = CIBlock::GetList(	
		Array(), 	
		Array(		
			'TYPE'=>'clients',
			//'SITE_ID'=>WIZARD_SITE_ID,
			'ACTIVE'=>'Y',
			"CODE"=>'review'
			), 
		true);
	
	$arReplace = array();
	echo $arReplace["SITE_ID"] = WIZARD_SITE_DIR;
	if($arIBlock = $rsIBlock->Fetch()){	
		$arReplace["REVIEW_IBLOCK_ID"] = $arIBlock["ID"];
		$ib = new CIBlock;
		$arFields = Array(
			"SITE_ID" => WIZARD_SITE_ID,
		);
		$res = $ib->Update($arIBlock["ID"], $arFields);
	}
	
	$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
	CWizardUtil::ReplaceMacros($bitrixTemplateDir . '/footer.php', $arReplace);
?>