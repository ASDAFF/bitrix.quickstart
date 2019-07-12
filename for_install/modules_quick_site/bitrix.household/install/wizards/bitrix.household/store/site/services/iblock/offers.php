<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;
	

$iblockID=0;
$c=1;

$wizard =&$this->GetWizard();

$iblockCode = "refr_offers_".WIZARD_SITE_ID; 
$iblockType = "offers"; 
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
}

if ($wizard->GetVar("installDemoData")<>"Y")
{
	return;
}
$add_path=$wizard->GetVar("install_data");
$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/refr_offers.xml'; 
$iblockXMLFilePrice =WIZARD_SERVICE_RELATIVE_PATH.'/xml/refr_offers_prices.xml'; 


if ($iblockID == false)
{
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"tmp_household_refr_offers",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
			"2" => "R"
		)
	);
	
	if ($iblockID < 1)
		return;
	$iblockID2 = WizardServices::ImportIBlockFromXML(
		$iblockXMLFilePrice,
		"tmp_household_refr_offers",
		$iblockType."_prices",
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
			"2" => "R"
		)
	);
	
	
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


function SaveLog($Str){
	$file=$_SERVER['DOCUMENT_ROOT'].'/log.php';
	$Str=print_r($Str,true);
	$Str=$Str."\n";
	if (!$handle = fopen($file, 'a')) {
	}
	if (fwrite($handle, $Str) === FALSE) {
	}
}

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$arFilter = Array("IBLOCK_CODE" => $iblockCode, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
  SaveLog('<Log It>');
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  SaveLog($arFields);
}

/*global $DB;

$DB->Query("select id from chtozafignja", false);*/

//$wizard->SetVar("OfferID", $iblockID);
COption::SetOptionString("bitrix.household", 'OffersID', $iblockID);
?>
