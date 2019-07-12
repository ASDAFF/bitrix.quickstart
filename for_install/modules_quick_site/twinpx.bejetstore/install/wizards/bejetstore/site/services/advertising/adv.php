<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

if(CModule::IncludeModule("advertising")){

    $arrWEEKDAY = Array
    (
        "SUNDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            ),

        "MONDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            ),

        "TUESDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            ),

        "WEDNESDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            ),

        "THURSDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            ),

        "FRIDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            ),

        "SATURDAY" => Array
            (
                "0" => "0",
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5",
                "6" => "6",
                "7" => "7",
                "8" => "8",
                "9" => "9",
                "10" => "10",
                "11" => "11",
                "12" => "12",
                "13" => "13",
                "14" => "14",
                "15" => "15",
                "16" => "16",
                "17" => "17",
                "18" => "18",
                "19" => "19",
                "20" => "20",
                "21" => "21",
                "22" => "22",
                "23" => "23"
            )

    );

    $rsContract = CAdvContract::GetList($by, $order, array("SITE" => array("0" => WIZARD_SITE_ID)));
    if($arContract = $rsContract->Fetch()){
        $CONTRACT_ID = $arContract["ID"];
    }else{
        
        $rsSites = CSite::GetByID(WIZARD_SITE_ID);
        $arSite = $rsSites->Fetch();

        $arFields = array(
            "ACTIVE" => "Y",
           // "NAME" => "SS",
            //"DESCRIPTION" => "SS",
            "ADMIN_COMMENTS" => "",
            "WEIGHT" => 100,
            "SORT" => 10,
            "MAX_SHOW_COUNT" => "",
            "MAX_CLICK_COUNT" => "",
            "MAX_VISITOR_COUNT" => "",
            "DATE_SHOW_FROM" => "",
            "DATE_SHOW_TO" => "",
            "DEFAULT_STATUS_SID" => "PUBLISHED",
            "arrSHOW_PAGE" => Array(),
            "arrNOT_SHOW_PAGE" => array(),
            "arrTYPE" => Array("ALL"),
            "arrWEEKDAY" => $arrWEEKDAY,
            "arrUSER_VIEW" => "",
            "arrUSER_ADD" => "",
            "arrUSER_EDIT" => "",
            "arrSITE" => array($arSite["LID"]),
        );
        
        if ($ID = CAdvContract::Set($arFields,$ID,"N")){
            $CONTRACT_ID = $ID;
        }
    }

    //add banner type
    $arFields = array(
        "SID"              => "carousel",
        "ACTIVE"            => "Y",
        "SORT"            => 100,
        "NAME"            => GetMessage("ADV_BANNER_TYPE_NAME"),
        "DESCRIPTION"      => GetMessage("ADV_BANNER_TYPE_DESC")
    );
    if ($SID = CAdvType::Set($arFields, $OLD_SID))
    {
        if (strlen($strError) > 0)
        {
            echo $strError;
            return;
        }      

        #Add banner 1

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_1"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "carousel",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_1"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_1"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_1"),
            "URL_TARGET"        => "_blank",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );

        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }

        #Add banner 2

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_2"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "carousel",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_2"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_2"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_2"),
            "URL_TARGET"        => "_blank",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }
    }

    //add banner type
    $arFields = array(
        "SID"              => "index",
        "ACTIVE"            => "Y",
        "SORT"            => 100,
        "NAME"            => GetMessage("ADV_BANNER_TYPE_2_NAME"),
        "DESCRIPTION"      => GetMessage("ADV_BANNER_TYPE_2_DESC")
    );
    if ($SID = CAdvType::Set($arFields, $OLD_SID))
    {
        if (strlen($strError) > 0)
        {
            echo $strError;
            return;
        }

        #Add small banner 1

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_3"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_3"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_3"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_3"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }

        #Add small banner 2

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_4"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_4"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_4"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_4"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }

        #Add small banner 3

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_11"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_11"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_11"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_11"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }
    }

    //add banner type
    $arFields = array(
        "SID"              => "carousel_mobile",
        "ACTIVE"            => "Y",
        "SORT"            => 100,
        "NAME"            => GetMessage("ADV_BANNER_TYPE_3_NAME"),
        "DESCRIPTION"      => GetMessage("ADV_BANNER_TYPE_3_DESC")
    );
    if ($SID = CAdvType::Set($arFields, $OLD_SID))
    {
        if (strlen($strError) > 0)
        {
            echo $strError;
            return;
        }

        #Add small banner 1

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_5"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "carousel_mobile",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_5"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_5"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_5"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }

        #Add small banner 2

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_6"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "carousel_mobile",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_6"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_6"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_6"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }
    }

    //add banner type
    $arFields = array(
        "SID"              => "wide_index",
        "ACTIVE"            => "Y",
        "SORT"            => 100,
        "NAME"            => GetMessage("ADV_BANNER_TYPE_4_NAME"),
        "DESCRIPTION"      => GetMessage("ADV_BANNER_TYPE_4_DESC")
    );
    if ($SID = CAdvType::Set($arFields, $OLD_SID))
    {
        if (strlen($strError) > 0)
        {
            echo $strError;
            return;
        }

        #Add small banner 1

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_7"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "wide_index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_7"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_7"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_7"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }

        #Add small banner 2

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_9"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "wide_index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_9"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_9"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_9"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }
    }

    //add banner type
    $arFields = array(
        "SID"              => "square_index",
        "ACTIVE"            => "Y",
        "SORT"            => 100,
        "NAME"            => GetMessage("ADV_BANNER_TYPE_5_NAME"),
        "DESCRIPTION"      => GetMessage("ADV_BANNER_TYPE_5_DESC")
    );
    if ($SID = CAdvType::Set($arFields, $OLD_SID))
    {
        if (strlen($strError) > 0)
        {
            echo $strError;
            return;
        }

        #Add small banner 1

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_8"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "square_index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_8"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_8"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_8"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }

        #Add small banner 2

        $arrIMAGE_ID = CFile::MakeFileArray(WIZARD_SERVICE_RELATIVE_PATH."/images/".GetMessage("ADV_BANNER_IMG_10"));
        $arrIMAGE_ID["MODULE_ID"] = "advertising";
        
        $arFields = array(
            "CONTRACT_ID"      => $CONTRACT_ID,
            "TYPE_SID"        => "square_index",
            "STATUS_SID"        => "PUBLISHED",
            "NAME"            => GetMessage("ADV_BANNER_NAME_10"),
            "ACTIVE"        => "Y",
            "arrSITE"       => array("0" => WIZARD_SITE_ID),
            "WEIGHT"        => 100,
            "arrIMAGE_ID"      => $arrIMAGE_ID,
            "IMAGE_ALT"      => GetMessage("ADV_BANNER_IMG_ALT_10"),
            "URL"              => WIZARD_SITE_DIR.GetMessage("ADV_BANNER_LINK_10"),
            "URL_TARGET"        => "_self",
            "AD_TYPE" => "image",
            "STAT_EVENT_1" => "banner",
            "STAT_EVENT_2" => "click",
            "STAT_EVENT_3" => "#CONTRACT_ID# / [#BANNER_ID#] [#TYPE_SID#] #BANNER_NAME#",
            "arrWEEKDAY" => $arrWEEKDAY
        );
        if ($ID = CAdvBanner::Set($arFields))
        {
            if (strlen($strError) > 0)
            {
                 echo $strError;
                 return;
            }
        }
    }

}else{

    //add infoblock
    if(!CModule::IncludeModule("iblock"))
    return;
    
    $arType = Array(
        "ID" => "banners",
        "SECTIONS" => "N",
        "IN_RSS" => "N",
        "SORT" => 400,
        "LANG" => Array(),
    );

    $arLanguages = Array();
    $rsLanguage = CLanguage::GetList($by, $order, array());
    while($arLanguage = $rsLanguage->Fetch())
        $arLanguages[] = $arLanguage["LID"];

    $iblockType = new CIBlockType;

    $dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
    if(!$dbType->Fetch()){
        foreach($arLanguages as $languageID)
        {
            WizardServices::IncludeServiceLang("type.php", $languageID);

            $code = strtoupper($arType["ID"]);
            $arType["LANG"][$languageID]["NAME"] = GetMessage($code."_TYPE_NAME");
            $arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");

            if ($arType["SECTIONS"] == "Y")
                $arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code."_SECTION_NAME");
        }

        $iblockType->Add($arType);
    }

    $iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/banners.xml";
    $iblockCode = "bejetstore_banners_".WIZARD_SITE_ID;
    $iblockType = "banners"; 

    $rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
    $iblockID = false; 
    if ($arIBlock = $rsIBlock->Fetch())
    {
        $iblockID = $arIBlock["ID"]; 
        if (WIZARD_INSTALL_DEMO_DATA)
        {
            CIBlock::Delete($arIBlock["ID"]); 
            $iblockID = false; 
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
        {
            $permissions[$arGroup["ID"]] = 'W';
        };
        $iblockID = WizardServices::ImportIBlockFromXML(
            $iblockXMLFile,
            "bejetstore_banners",
            $iblockType,
            WIZARD_SITE_ID,
            $permissions
        );

        if ($iblockID < 1)
            return;
        
        //IBlock fields
        $iblock = new CIBlock;
        $arFields = Array(
            "ACTIVE" => "Y",
            "FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), ),
            "CODE" => "banners", 
            "XML_ID" => $iblockCode,
            //"NAME" => "[".WIZARD_SITE_ID."] ".$iblock->GetArrayByID($iblockID, "NAME")
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
    $dbSite = CSite::GetByID(WIZARD_SITE_ID);
    if($arSite = $dbSite -> Fetch())
        $lang = $arSite["LANGUAGE_ID"];
    if(strlen($lang) <= 0)
        $lang = "ru";

    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/banner.php", array("BANNERS_IBLOCK_ID" => $iblockID));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/small_banners.php", array("BANNERS_IBLOCK_ID" => $iblockID));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/wide_square_banners.php", array("BANNERS_IBLOCK_ID" => $iblockID));
}
?>