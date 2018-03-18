<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule("iblock"))
	return;
include_once(dirname(__FILE__)."/iblock_tools.php");

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/010_articles_article-list_ru.xml"; 
$xml_dir = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID; 
$iblockCode = "article-list-dfg"; 
$iblockType = "articles";
$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
$_SESSION["DEMO_IBLOCK_ARTICLES"] = CIBlockCMLImport::GetIBlockByXML_ID($iblockCode);
$iblock_id = $_SESSION["DEMO_IBLOCK_ARTICLES"];
if($_SESSION["DEMO_IBLOCK_ARTICLES"] === false){
	$iblock_id = DEMO_IBlock_ImportXML($xml_dir, "010_articles_article-list-dfg_ru.xml", WIZARD_SITE_ID, false, false);
	//if($iblock_id > 0){
		CUrlRewriter::Add(array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."articles/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."articles/index.php"
		));
	//}
}
	$arReplace = array();
	echo $arReplace["SITE_ID"] = WIZARD_SITE_DIR;
	$rsIBlock = CIBlock::GetList(	
		Array(), 	
		Array(		
			'TYPE'=>$iblockType,
			//'SITE_ID'=>WIZARD_SITE_ID,
			'ACTIVE'=>'Y',
			"CODE"=>$iblockCode
			), 
	true);


	if($arIBlock = $rsIBlock->Fetch()){	
		$iblock_id = $arIBlock["ID"];
	}
	$arReplace["ARTICLES_IBLOCK_ID"] = $iblock_id;
	$ib = new CIBlock;
	$arFields = Array(
		"SITE_ID" => WIZARD_SITE_ID,
	);
	$res = $ib->Update($arIBlock["ID"], $arFields);
	$path = str_replace('//', '/', WIZARD_SITE_PATH);
	CWizardUtil::ReplaceMacros($path."articles/index.php", $arReplace);
?>


