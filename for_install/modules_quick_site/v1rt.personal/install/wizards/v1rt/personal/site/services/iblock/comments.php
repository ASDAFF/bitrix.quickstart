<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;
$wizard =& $this->GetWizard();

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/comments.xml"; 
$iblockCode = "comments_".WIZARD_SITE_ID;
$iblockType = "personal"; 

//Узнаем ID инфоблока с новостями
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => $iblockType, "CODE" => "news_".WIZARD_SITE_ID));
if($arIBlock = $rsIBlock->Fetch())
	$newsID = $arIBlock["ID"];

unset($rsIBlock);
$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 

if($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if(WIZARD_INSTALL_DEMO_DATA)
	{
        if($wizard->GetVar("rewriteIBlock") == "Y")
        {
            if(CIBlock::Delete($arIBlock["ID"])) 
                $iblockID = false;
        }
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
		$permissions[$arGroup["ID"]] = 'W';
    
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"comments",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if($iblockID <= 0)
		return;
	$iblockCode = "comments_".WIZARD_SITE_ID;
    
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array( 
            'IBLOCK_SECTION' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '', 
            ), 
            'ACTIVE' => array(
                'IS_REQUIRED' => 'Y', 
                'DEFAULT_VALUE' => 'Y',
            ), 
            'ACTIVE_FROM' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '=today',
            ), 
            'ACTIVE_TO' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '',
            ), 
            'SORT' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '',
            ),
            'NAME' => array(
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => '',
            ), 
            'PREVIEW_PICTURE' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => array(
                    'FROM_DETAIL' => 'N', 
                    'SCALE' => 'N', 
                    'WIDTH' => '', 
                    'HEIGHT' => '', 
                    'IGNORE_ERRORS' => 'N',
                ), 
            ), 
            'PREVIEW_TEXT_TYPE' => array( 
                'IS_REQUIRED' => 'Y', 
                'DEFAULT_VALUE' => 'text',
            ), 
            'PREVIEW_TEXT' => array( 
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '', 
            ), 
            'DETAIL_PICTURE' => array( 
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => array(
                    'SCALE' => 'N', 
                    'WIDTH' => '', 
                    'HEIGHT' => '', 
                    'IGNORE_ERRORS' => 'N', 
                ), 
            ), 
            'DETAIL_TEXT_TYPE' => array( 
                'IS_REQUIRED' => 'Y', 
                'DEFAULT_VALUE' => 'text', 
            ), 
            'DETAIL_TEXT' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '', 
            ), 
            'XML_ID' => array( 
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '', 
            ), 
            'CODE' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '', 
            ), 
            'TAGS' => array(
                'IS_REQUIRED' => 'N', 
                'DEFAULT_VALUE' => '', 
            ), 
        ), 
		"CODE" => $iblockCode, 
		"XML_ID" => $iblockCode,
		"NAME" => $iblock->GetArrayByID($iblockID, "NAME")
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

if($iblockID > 0 && $newsID > 0)
{
    $ID = 0;
    
    $arSelect = Array("ID");
    $arFilter = Array("IBLOCK_ID"=>intval($newsID), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
    if($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $ID = $arFields["ID"];
    }
    $el = new CIBlockElement;
    $PROP = array();
    $PROP["ID_RECORD"] = $ID;
    
    $arLoadProductArray = Array(
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID"      => $iblockID,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => GetMessage("V1RT_COMMENT_NAME_1"),
        "ACTIVE"         => "Y",
        "PREVIEW_TEXT"   => GetMessage("V1RT_COMMENT_TEXT_1")
    );
    $el->Add($arLoadProductArray);

    $arLoadProductArray = Array(
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID"      => $iblockID,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => GetMessage("V1RT_COMMENT_NAME_2"),
        "ACTIVE"         => "Y",
        "PREVIEW_TEXT"   => GetMessage("V1RT_COMMENT_TEXT_2")
    );
    $el->Add($arLoadProductArray);
}

CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."_".WIZARD_SITE_ID."/header.php", array("COMMENTS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/.default/components/bitrix/news/records.v2/bitrix/news.detail/.default/component_epilog.php", array("COMMENTS_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/templates/.default/components/bitrix/news/records/bitrix/news.detail/.default/component_epilog.php", array("COMMENTS_IBLOCK_ID" => $iblockID));
?>
