<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule("iblock"))
	return;
include_once(dirname(__FILE__)."/iblock_tools.php");

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/020_news_content-news_ru.xml"; 
$xml_dir = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID; 
$iblockCode = "content-news-dfg"; 
$iblockType = "news";
$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
$_SESSION["DEMO_IBLOCK_NEWS"] = CIBlockCMLImport::GetIBlockByXML_ID($iblockCode);
$iblock_id = $_SESSION["DEMO_IBLOCK_NEWS"];
if($_SESSION["DEMO_IBLOCK_NEWS"] === false){
 	$iblock_id = DEMO_IBlock_ImportXML($xml_dir, "020_news_content-news-dfg_ru.xml", WIZARD_SITE_ID, false, false);
}

	$rsIBlock = CIBlock::GetList(	
		Array(), 	
		Array(		
			'TYPE'=>'news',
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

	$arReplace["NEWS_IBLOCK_ID"] = $iblock_id;
	$ib = new CIBlock;
	$arFields = Array(
		"SITE_ID" => WIZARD_SITE_ID,
	);
	$res = $ib->Update($iblock_id, $arFields);
	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."news/index.php", $arReplace);
	CUrlRewriter::Add(array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."news/index.php"
	));
?>


