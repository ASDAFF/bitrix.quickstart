<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;



$iblocks = array();
$i= $wizard->GetVar("module_iblocks"); 


$iblocks = unserialize($i);
//print_r($iblocks );die();

$arrReplace = array();

foreach($iblocks as $ibCode=>$iblock) {

	$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/".$ibCode.".xml"; 
	$iblockCode = $ibCode."_".WIZARD_SITE_ID; 
	$iblockType=$iblock['type'];
	$ibToRewrite = false;
	
	$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType ));
	$iblockID = false; 
	if ($arIBlock = $rsIBlock->Fetch())
	{
		$iblocks[$ibCode]['iblock_id_old'] = $arIBlock["ID"]; 
		$ibToRewrite = $wizard->GetVar("iblock_to_rewrite_".$ibCode);
		$iblockID = $arIBlock["ID"]; 
		if ($ibToRewrite=="Y")
		{
			$iblocks[$ibCode]['iblock_rewritten'] = "Y"; 
			CIBlock::Delete($arIBlock["ID"]); 
			$iblockID = false; 
		}

	}


	
	if($iblockID == false )
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
			$iblockCode,
			$iblockType,
			WIZARD_SITE_ID,
			$permissions
		);
	
		if ($iblockID < 1)
			return;

		//IBlock fields
		$iblockEx = new CIBlock;
		$arFields = Array(
			"ACTIVE" => "Y",
			"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), ),
			"CODE" => $iblockCode, 
			"XML_ID" => $iblockCode,
	
		);
		
		$iblockEx->Update($iblockID, $arFields);
	}
	else{
		$arSites = array(); 


		$db_res = CIBlock::GetSite($iblockID);
		while ($res = $db_res->Fetch()) {
			$arSites[] = $res["LID"];
		} 


		if (!in_array(WIZARD_SITE_ID, $arSites))
		{
			$arSites[] = WIZARD_SITE_ID;
			$iblockEx = new CIBlock;
			$iblockEx->Update($iblockID, array("LID" => $arSites));
		}

	}

	$dbSite = CSite::GetByID(WIZARD_SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$lang = $arSite["LANGUAGE_ID"];
	if(strlen($lang) <= 0)
		$lang = "ru";
		
	//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/news/index.php", array("IBLOCK_ID_NEWS" => $iblockID));
	$iblocks[$ibCode]['iblock_id'] = $iblockID; 
	$arrReplace[$iblock["macros"]]=$iblockID;
}



$CATALOG_IBLOCK_ID=$iblocks['catalog_bestrank_mono']['iblock_id'];
$props=array();

$specialofferPropID=0;
$specialofferPropVarY=0;


$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$CATALOG_IBLOCK_ID, "XML_ID"=>"Y" ));
while($enum_fields = $property_enums->GetNext())
{
	if(!in_array($enum_fields["PROPERTY_CODE"], array("PRODUCERS", "SPECIALOFFER", "NEWPRODUCT")) )continue;
	$props[$enum_fields["ID"]] = $enum_fields["PROPERTY_ID"];

	if($enum_fields["PROPERTY_CODE"]=="SPECIALOFFER") {
		$specialofferPropID=$enum_fields["PROPERTY_ID"];
		$specialofferPropVarY=$enum_fields["ID"];
	}

	$arrReplace["PROPERTY_ID_".$enum_fields["PROPERTY_CODE"]] = $enum_fields["PROPERTY_ID"];
	$arrReplace["CRC32_".$enum_fields["PROPERTY_CODE"]] = abs(crc32($enum_fields["ID"]));
}
//echo  "specialofferPropID: ".$specialofferPropID."; specialofferPropVarY: ".$specialofferPropVarY;

//electrobritvy razdel
if (CModule::IncludeModule("iblock"))
{
	$dbSect = CIBlockSection::GetList(Array("rand"=>"asc"), Array("IBLOCK_TYPE" => "catalog", "IBLOCK_ID"=>$CATALOG_IBLOCK_ID, "CODE" => "elektrobritvy", "IBLOCK_SITE_ID" => WIZARD_SITE_ID));
	if ($arSect = $dbSect->Fetch())
		$arrReplace["SECTION_ID_ELECTROBRITVY"] = $arSect["ID"];
	else
		$arrReplace["SECTION_ID_ELECTROBRITVY"] = "false";
}


//demo discount
if(CModule::IncludeModule("catalog")) {
    $dbDiscount = CCatalogDiscount::GetList(array(), Array("SITE_ID" => WIZARD_SITE_ID));
    if(!($dbDiscount->Fetch()))
    {
        if (CModule::IncludeModule("iblock"))
        {
            $dbSect = CIBlockSection::GetList(Array("rand"=>"asc"), Array("IBLOCK_TYPE" => "catalog", "IBLOCK_ID"=>$CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "IBLOCK_SITE_ID" => WIZARD_SITE_ID));
            if ($arSect = $dbSect->Fetch())
                $discountSectId = $arSect["ID"];
        }
        $dbSite = CSite::GetByID(WIZARD_SITE_ID);
        if($arSite = $dbSite -> Fetch())
            $lang = $arSite["LANGUAGE_ID"];
        $defCurrency = "EUR";
        if($lang == "ru")
            $defCurrency = "RUB";
        elseif($lang == "en")
            $defCurrency = "USD";
        $arF = Array (
            "SITE_ID" => WIZARD_SITE_ID,
            "ACTIVE" => "Y",
            "RENEWAL" => "N",
            "NAME" => GetMessage("WIZ_DISCOUNT_SALE"),
            "SORT" => 100,
            "MAX_DISCOUNT" => 0,
            "VALUE_TYPE" => "P",
            "VALUE" => 10,
            "CURRENCY" => $defCurrency,
            "CONDITIONS" => Array ( 
                "CLASS_ID" => "CondGroup", 
                "DATA" => Array("All" => "OR", "True" => "True"),
                "CHILDREN" => Array("0" => Array("CLASS_ID" => "CondIBProp:".$CATALOG_IBLOCK_ID.":".$specialofferPropID, "DATA" => Array("logic" => "Equal", "value" => $specialofferPropVarY)))
            )
        );
        CCatalogDiscount::Add($arF);
    }
}


//print_r($arrReplace); die();

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, $arrReplace);
$wizard->SetVar("module_iblocks", serialize($iblocks));


?>