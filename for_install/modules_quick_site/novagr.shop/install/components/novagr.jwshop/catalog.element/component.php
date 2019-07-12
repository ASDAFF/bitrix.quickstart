<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// some servers return ../index.php in path
$currentUri = (isset($arParams['COMPONENT_CURRENT_PAGE']) and strlen($arParams['COMPONENT_CURRENT_PAGE'])>0) ? $arParams['COMPONENT_CURRENT_PAGE'] : $APPLICATION->GetCurPage(false);
$arParams['COMPONENT_CURRENT_PAGE'] = $currentUri;

if( CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") ) {
} else {
	die(GetMessage("MODULES_NOT_INSTALLED"));
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if ($arParams["CATALOG_SUBSCRIBE_ENABLE"] != "Y") $arParams["CATALOG_SUBSCRIBE_ENABLE"] = "N";

if ($arParams["CATALOG_COMMENTS_ENABLE"] != "N") $arParams["CATALOG_COMMENTS_ENABLE"] = "Y";

$arParams['CAJAX'] = intval($_REQUEST['CAJAX']);

$arParams["SIZES_CODES"] = array(
		"REAL_BREAST_GRASP" => GetMessage("REAL_BREAST_GRASP"),
		"REAL_GRASP_WAIST" => GetMessage("REAL_GRASP_WAIST"),
		"REAL_GRASP_HIPS" => GetMessage("REAL_GRASP_HIPS"),
		"REAL_SHOULDER_LENGTH" => GetMessage("REAL_SHOULDER_LENGTH"),
		"REAL_BELT_LENGTH" => GetMessage("REAL_BELT_LENGTH"),
		"REAL_SLEEVE_LENGTH" => GetMessage("REAL_SLEEVE_LENGTH"),
		"REAL_SHOULDER_WIDTH" => GetMessage("REAL_SHOULDER_WIDTH"),
		"REAL_LENGTH_INNER_SE" => GetMessage("REAL_LENGTH_INNER_SE"),
		"REAL_GRASP_TROUSER_L" => GetMessage("REAL_GRASP_TROUSER_L"),
		"REAL_WIDTH_BAG" => GetMessage("REAL_WIDTH_BAG"),
		"REAL_TALL_BAG" => GetMessage("REAL_TALL_BAG"),
		"REAL_DEPTH" => GetMessage("REAL_DEPTH"),
		"REAL_LENGTH_HANDLE" => GetMessage("REAL_LENGTH_HANDLE"),
		"REAL_LENGTH_ACC" => GetMessage("REAL_LENGTH_ACC"),
		"REAL_WIDTH_ACC" => GetMessage("REAL_WIDTH_ACC"),
		"REAL_TALL_SHO" => GetMessage("REAL_TALL_SHO"),
		"REAL_LENGTH_INSOLE" => GetMessage("REAL_LENGTH_INSOLE"),
		"REAL_WIDTH_INSOLE" => GetMessage("REAL_WIDTH_INSOLE"),
		"REAL_HEIGHT_HEEL" => GetMessage("REAL_HEIGHT_HEEL"),
		"REAL_HEIGHT_PLATFORM" => GetMessage("REAL_HEIGHT_PLATFORM"),
		"REAL_GRASP_TOP" => GetMessage("REAL_GRASP_TOP"),
		"REAL_GRASP_HEAD" => GetMessage("REAL_GRASP_HEAD"),
		"REAL_LENGTH_GLO" => GetMessage("REAL_LENGTH_GLO"),
		"REAL_GRASP_PALM" => GetMessage("REAL_GRASP_PALM"),
		"REAL_GRASP_WRIST" => GetMessage("REAL_GRASP_WRIST"),
		"REAL_DIAMETER_DIAL" => GetMessage("REAL_DIAMETER_DIAL"),
		"REAL_LENGTH_CANE" => GetMessage("REAL_LENGTH_CANE"),
		"REAL_DIAMETER_DOME" => GetMessage("REAL_DIAMETER_DOME")
);

$arFilter = array("IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"]);

$arFilter['ACTIVE'] = "Y";
$arFilter['ACTIVE_DATE'] = "Y";
$arFilter['SECTION_GLOBAL_ACTIVE'] = "Y";

if($arParams['SET_TITLE']!=='N')
{
    $APPLICATION->AddChainItem(GetMessage("CATALOG_LABEL"), SITE_DIR."catalog/");
}


$elemFlag = false;

if (isset($_REQUEST['secid']) and isset($_REQUEST['elmid']))  {
    // detail card
    $elemFlag = true;
    $arResult['SECTION_CODE'] = $_REQUEST['secid'];
    $arParams['ELEMENT_CODE'] = $_REQUEST['elmid'];
} else {
    $page404flag = true;
}
	

if ($page404flag == true) {
	
	//$arResult['ELEMENTS'] - пустой - в шаблоне выведена 404 ошибка
	
} else {

    $arrayGroupCanEdit = array(1);
    if (!empty($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"]))
        $arrayGroupCanEdit[] = $arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"];

    /// If the user is an sale_administrator show a pencil to edit
    $arParams['SHOW_EDIT_BUTTON'] = "N";
    if ( CSite::InGroup( $arrayGroupCanEdit )) $arParams['SHOW_EDIT_BUTTON'] = "Y";

    $returnFlag = false;

    /**
     * @var CBitrixComponent $this
     */
    if ( $this -> StartResultCache( ) )
    {

        if (!empty($arResult['SECTION_CODE'])) {
            // get the properties for the current section
            $arSelectSections = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID', "DEPTH_LEVEL" );
            $arFilterSections = array(
                "CODE" => $arResult['SECTION_CODE'], "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"]
            );

            $arFilterSections['ACTIVE'] = "Y";
            $rsSection = CIBlockSection::GetList(
                Array("SORT"=>"ASC"), $arFilterSections, false, $arSelectSections
            );
            if ($currentSection = $rsSection -> Fetch()) { }

            // get the tree sections for this section
            if ($currentSection["DEPTH_LEVEL"]>0) {

                $arNavSection = array();
                $nav = CIBlockSection::GetNavChain(false, $currentSection["ID"]);

                while ($arNav = $nav->GetNext())
                {
                    $arNavSection[] = $arNav;
                }
            }
        }

        if ( $arParams['ELEMENT_ID'] > 0 )
            $arFilter['ID'] = $arParams['ELEMENT_ID'];
        if (!empty($arParams['ELEMENT_CODE']))
            $arFilter['CODE'] = $arParams['ELEMENT_CODE'];

        $arSelect = array(
            'IBLOCK_ID',
            'IBLOCK_NAME',
            'IBLOCK_SECTION_ID',
            'ID',
            'NAME',
            'DETAIL_PICTURE',
            'DETAIL_PAGE_URL',
            'DETAIL_TEXT',
            "PROPERTY_TITLE",
            "PROPERTY_HEADER1",
            "PROPERTY_KEYWORDS",
            "PROPERTY_META_DESCRIPTION",
            "PROPERTY_COLLECTION.NAME",
        );


        require(dirname(__FILE__) . "/getElement.php");

        require(dirname(__FILE__) . "/getProps.php");

        /**
         * сортировка цветов по индексу
         */
        if (is_array($arResult["CURRENT_ELEMENT"]["COLORS"]))
        {
            $arSelect = Array("ID", "NAME", "IBLOCK_ID","SORT","PROPERTY_class_stone_color");
            $arFilter = Array("ID" => $arResult["CURRENT_ELEMENT"]["COLORS"]);
            $res = CIBlockElement::GetList(Array('sort'=>'asc'), $arFilter, false, Array("nPageSize"=>50), $arSelect);

            $CURRENT_ELEMENT_COLORS = $CURRENT_ELEMENT_COLORS_DATA = $COLORS = array();
            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $CURRENT_ELEMENT_COLORS_DATA[] = $arFields;
            }

            $arResult["CURRENT_ELEMENT"]["COLORS"] = $CURRENT_ELEMENT_COLORS_DATA;
        }

        $filename = NOVAGR_JSWSHOP_MODULE_DIR . "/comments.txt";

        if (!file_exists($filename)) {
            $CommentsOn = "1";
        } else {
            $content = unserialize(file_get_contents($filename));
            $CommentsOn = (int)$content["on"];
        }
        if ($arParams["CATALOG_COMMENTS_ENABLE"] == "N") $CommentsOn = "0";

        //countPreviewPictureId = count($PREVIEW_PICTURE_ID);
        $countPreviewPictureId = count($colorsPreviewPictures);

        if ( $countPreviewPictureId > 0 ) {
            $arFilter = "";
            if ($countPreviewPictureId > 0)
                foreach ($colorsPreviewPictures as $val) $arFilter .= $val.",";

            $rsFile = CFile::GetList(false, array('@ID' => $arFilter));
            while ($data = $rsFile -> GetNext())
            {
                $PICTURE_SRC[$data['ID']]
                    = "/upload/".$data['SUBDIR']."/".$data['FILE_NAME'];
            }

            if (isset($PREVIEW_PICTURE_ID))
                foreach ($PREVIEW_PICTURE_ID as $key => $val)
                    $arResult['PREVIEW_PICTURE'][$key] = $PICTURE_SRC[$val];

        }

        // получаем данные для текстовых полей ( таблица размеров, текст о доставке)
        $arFilter = array(
            'ACTIVE' => "Y",
            'IBLOCK_ID' => $arParams["ARTICLES_IBLOCK_ID"],
            array(
                "LOGIC" => "OR",
                array('CODE' => 'delivery'),
                array('CODE' => 'tablitsa-razmerov'),
            )
        );

        $arSelect = array( 'ID', 'NAME', 'CODE', 'DETAIL_TEXT' );
        $rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
        while ($data = $rsElement -> Fetch())
        {
            $arResult[$data["CODE"]] = $data["DETAIL_TEXT"];
        }

        // save variables to cache
        $arrPreviewPicture = $arResult["PREVIEW_PICTURE"];
        $arrElementsColorsPhotos = $arResult["ELEMENT_COLORS_PHOTOS"];
        $arOffers = $arResult["OFFERS"];
        $elemID = $arResult["ID"];
        $elemIblockID = $arResult["IBLOCK_ID"];
        $elemSectionID = $arResult["IBLOCK_SECTION_ID"];
        $arCurrentElement = $arResult["CURRENT_ELEMENT"];
        $arMixData = $arResult["mixData"];
        $deliveryText = $arResult["delivery"];
        $sizeTableText = $arResult["tablitsa-razmerov"];
        $useMorePhotoFlag = $arResult["USE_MORE_PHOTO"];
        $detailPicture = $arResult["DETAIL_PICTURE"];
        $arElementsPhotos = $arResult["ELEMENT_PHOTO"];
        $detailPictureMinSrc = $arResult["DETAIL_PICTURE_MIN_SRC"];
        $detailPictureArr = $arResult["DETAIL_PICTURE_ARR"];
        $basePriceCode = $arResult["BASE_PRICE_CODE"];
        $detailPageUrl = $arResult["DETAIL_PAGE_URL"];

        $arResult['CACHE_DATA'] = array(
            "arResult" => $arResult,
            "arElement" => $arElement,
            "CommentsOn" => $CommentsOn,
            "previewPicture" => $arrPreviewPicture,
            "detailPictureMinSrc" => $detailPictureMinSrc,
            "detailPictureArr" => $detailPictureArr,
            "arBasePriceCode" => $basePriceCode,
            "arOffers" => $arOffers,
            "arCurrentElement" => $arCurrentElement,/*
                "maxCountSize" => $maxCountSize,
                "maxCountColor" => $maxCountColor,*/
            "elemID" => $elemID,
            "elemIblockID" => $elemIblockID,
            "elemSectionID" => $elemSectionID,
            "useMorePhotoFlag" => $useMorePhotoFlag,
            "arrElementsColorsPhotos" => $arrElementsColorsPhotos,
            "arElementsPhotos" => $arElementsPhotos,/*
                "arElementsPhotosMiddle" => $arElementsPhotosMiddle,*/
            "arMixData" => $arMixData,
            "returnFlag" => $returnFlag,
            "deliveryText" => $deliveryText,
            "sizeTableText" => $sizeTableText,
            "arNavSection" => $arNavSection,
            "detailPicture" => $detailPicture,
            "detailPageUrl" => $detailPageUrl,
            /* "detailImages" => $detailImages,
             "detailCardView" => $detailCardView,*/
        );

        global $CACHE_MANAGER;
        $CACHE_MANAGER->StartTagCache($this->getCachePath());
        $CACHE_MANAGER->RegisterTag("catalog.elemenet");
        $CACHE_MANAGER->EndTagCache();

        $this->SetResultCacheKeys(array(
            "CACHE_DATA"
        ));

    } // end work with cache
    $this->EndResultCache();

    $CACHE_DATA = $arResult['CACHE_DATA'];
    $arResult = $arResult['CACHE_DATA']["arResult"];
    $arResult['CACHE_DATA'] = $CACHE_DATA;
    $arElement = $arResult['CACHE_DATA']["arElement"];
    $CommentsOn = $arResult['CACHE_DATA']["CommentsOn"];
    $arrPreviewPicture = $arResult['CACHE_DATA']["previewPicture"];
    $detailPictureMinSrc = $arResult['CACHE_DATA']["detailPictureMinSrc"];
    $detailPictureArr = $arResult['CACHE_DATA']["detailPictureArr"];
    $basePriceCode = $arResult['CACHE_DATA']["arBasePriceCode"];
    $arOffers = $arResult['CACHE_DATA']["arOffers"];
    $arCurrentElement = $arResult['CACHE_DATA']["arCurrentElement"];
    $elemID = $arResult['CACHE_DATA']["elemID"];
    $elemIblockID = $arResult['CACHE_DATA']["elemIblockID"];
    $elemSectionID = $arResult['CACHE_DATA']["elemSectionID"];
    $useMorePhotoFlag = $arResult['CACHE_DATA']["useMorePhotoFlag"];
    $arrElementsColorsPhotos = $arResult['CACHE_DATA']["arrElementsColorsPhotos"];
    $arElementsPhotos = $arResult['CACHE_DATA']["arElementsPhotos"];
    $arMixData = $arResult['CACHE_DATA']["arMixData"];
    $returnFlag = $arResult['CACHE_DATA']["returnFlag"];
    $deliveryText = $arResult['CACHE_DATA']["deliveryText"];
    $sizeTableText = $arResult['CACHE_DATA']["sizeTableText"];
    $arNavSection = $arResult['CACHE_DATA']["arNavSection"];
    $detailPicture = $arResult['CACHE_DATA']["detailPicture"];
    $detailPageUrl = $arResult['CACHE_DATA']["detailPageUrl"];

    $arResult["DETAIL_PAGE_URL"] = $detailPageUrl;
    $arResult["DETAIL_PICTURE_MIN_SRC"] = $detailPictureMinSrc;
    $arResult["DETAIL_PICTURE_ARR"] = $detailPictureArr;
    $arResult["delivery"] = $deliveryText;
    $arResult["tablitsa-razmerov"] = $sizeTableText;
    $arResult["ELEMENT"] = $arElement;
    $arResult["COMMENTS_ON"] = $CommentsOn;
    $arResult["OFFERS"] = $arOffers;
    $arResult["CURRENT_ELEMENT"] = $arCurrentElement;
    $arResult["ID"] = $elemID;
    $arResult["IBLOCK_ID"] = $elemIblockID;
    $arResult["IBLOCK_SECTION_ID"] = $elemSectionID;
    $arResult["USE_MORE_PHOTO"] = $useMorePhotoFlag;
    $arResult["DETAIL_PICTURE"] = $detailPicture;
    $arResult["mixData"] = $arMixData;
    $arResult["ELEMENT_COLORS_PHOTOS"] = $arrElementsColorsPhotos;
    $arResult["ELEMENT_PHOTO"] = $arElementsPhotos;
    $arResult["PREVIEW_PICTURE"] = $arrPreviewPicture;
    $arResult["BASE_PRICE_CODE"] = $basePriceCode;

    $countSections = count($arNavSection);

    if (trim($arResult["ELEMENT"]["PROPERTY_HEADER1_VALUE"]) <> "") {
        $arResult["ELEMENT"]["NAME"] = $arResult["ELEMENT"]["PROPERTY_HEADER1_VALUE"];
    }

    // making bread crumbs
    if ($countSections > 0) {
        $i = 1;
        foreach ($arNavSection as $section)
        {
            // the last element
            if ($i >= $countSections && $elemFlag == false) {

                $linkSection = '';
            } else {
                $linkSection = SITE_DIR."catalog/" .$section['CODE'] ."/";

            }
            if ($arParams['SET_TITLE']!=='N' and trim($section['NAME'])<>"")
            {
                if ($countSections == $i) {
                    $APPLICATION->AddChainItem($section['NAME'], $linkSection);
                } else {
                    $APPLICATION->AddChainItem($section['NAME']);
                }
            }
            ++$i;
        }
    }
    if (!empty($arResult['ELEMENT']['NAME'])) $APPLICATION->AddChainItem($arResult['ELEMENT']['NAME'], false);

    $userID = $USER->getID();
    $arResult["SIZES_COLORS"] = array();

    if (count($arResult["CURRENT_ELEMENT"]["STD_SIZE"]) > 0) {

        if ($userID > 0 ) {
            $arResult["USER_ID"] = $userID;

            $arResult["SIZES_COLORS"] = Novagroup_Classes_General_Basket::getBasketSizesColors($userID, $arParams['CATALOG_OFFERS_IBLOCK_ID']);

            $arResult["SIZES_COLORS"] = unserialize($APPLICATION->get_cookie("SIZES_COLORS"));

            if (empty($arResult["SIZES_COLORS"])) {
                $arFilter = array('ID' => $userID);
                $dbRes = CUser::GetList(
                    $by = 'ID', $order = 'ASC', $arFilter, array("SELECT"=>array("UF_SIZES_COLORS"))
                );
                if ($arRes = $dbRes->Fetch()) {
                    $arResult["SIZES_COLORS"] = unserialize($arRes["UF_SIZES_COLORS"]);
                }
            }

            // ищем наиболее часто встречаемые размеры и цвета в корзине и в заказах
            // приоритет у корзины
            $maxCurrentSizeCountBasket = 0;
            $maxCurrentSizeCount = 0;
            $j = 0;
            $arResult["MAX_COUNT_SIZE"] = 0;
            $basketSizeFound = false;
            foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $sizeID => $size)
            {
                // find size for smart site
                if ($j==0) {
                    $arResult["MAX_COUNT_SIZE"] = $sizeID;
                }

                if (!empty($arResult["SIZES_COLORS"]["SIZES"]["BASKET"][$sizeID])) {
                    $currentSizeCountBasket = $arResult["SIZES_COLORS"]["SIZES"]["BASKET"][$sizeID];
                    if ($currentSizeCountBasket >= $maxCurrentSizeCountBasket) {
                        $maxCurrentSizeCountBasket = $currentSizeCountBasket;
                        $arResult["MAX_COUNT_SIZE"] = $sizeID;
                        $basketSizeFound = true;
                    }
                }
                if ($basketSizeFound == false) {
                    if (!empty($arResult["SIZES_COLORS"]["SIZES"]["ORDERS"][$sizeID])) {
                        $currentSizeCount = $arResult["SIZES_COLORS"]["SIZES"]["ORDERS"][$sizeID];
                        if ($currentSizeCount >= $maxCurrentSizeCount) {
                            $maxCurrentSizeCount = $currentSizeCount;
                            $arResult["MAX_COUNT_SIZE"] = $sizeID;
                        }
                    }
                }
                $j++;
            }

            // finding the most common color

            if ($arResult["MAX_COUNT_SIZE"] > 0) {

                $maxCurrentColorCount = 0;
                $maxCurrentColorCountBasket = 0;
                $j = 0;
                $arResult["MAX_COUNT_COLOR"] = 0;
                $basketColorFound = false;
                foreach($arResult["OFFERS"] as $arOffer) {

                    if ($arOffer["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"] == $arResult["MAX_COUNT_SIZE"])
                    {
                        $currentColorCount = $arResult["SIZES_COLORS"]["COLORS"]["BASKET"][$arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]];
                        if (!empty($currentColorCount))
                        {
                            $currentColorCountBasket = $currentColorCount;
                            if ($currentColorCountBasket >= $maxCurrentColorCountBasket) {
                                $maxCurrentColorCountBasket = $currentColorCountBasket;
                                $arResult["MAX_COUNT_COLOR"] = $arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"];
                                $basketColorFound = true;
                            }
                        }
                        if ($basketColorFound == false) {
                            $currentColorCount = $arResult["SIZES_COLORS"]["COLORS"]["ORDERS"][$arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]];
                            if (!empty($currentColorCount))
                            {
                                if ($currentColorCount >= $maxCurrentColorCount) {
                                    $maxCurrentColorCount = $currentColorCount;
                                    $arResult["MAX_COUNT_COLOR"] = $arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"];
                                }
                            }
                        }
                    }
                    $j++;
                }
            }
        } else {

            $arResult["USER_ID"] = 0;
        }

    } // end if ($arResult["CURRENT_ELEMENT"]["STD_SIZE"]) {



$arReturnUrl = array(
    "add_element" => CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "DETAIL_PAGE_URL"),
    "delete_element" => $linkSection
);

$arButtons = CIBlock::GetPanelButtons(
    $arResult["IBLOCK_ID"],
    $arResult["ID"],
    $arResult["IBLOCK_SECTION_ID"],
    Array(
        "RETURN_URL" => $arReturnUrl,
        "SECTION_BUTTONS" => false,
    )
);

//установка мета-данных страницы
if ($arParams['SET_TITLE'] <> 'N') {
    /**
     * get seo templates
     */
    $rsSeoData = new \Bitrix\Iblock\InheritedProperty\ElementValues($arParams["CATALOG_IBLOCK_ID"], $arResult['ID']);
    $arResult["IPROPERTY_VALUES"] = $rsSeoData->getValues();

    /**
     * find and set title
     */
    if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) <> "") {
        $browserTitle = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"];
    } elseif (trim($arResult['ELEMENT']['PROPERTY_TITLE_VALUE']) <> "") {
        $browserTitle = $arResult['ELEMENT']['PROPERTY_TITLE_VALUE'];
    } else {
        $browserTitle = $arResult['ELEMENT']['NAME'];
    }
    Novagroup_Classes_General_Main::setTitle($browserTitle);

    /**
     * find and set keywords
     */
    if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]) <> "") {
        $metaKeywords = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"];
    } elseif (trim($arResult['ELEMENT']['PROPERTY_KEYWORDS_VALUE']) <> "") {
        $metaKeywords = $arResult['ELEMENT']['PROPERTY_KEYWORDS_VALUE'];
    } else {
        $metaKeywords = "";
    }
    Novagroup_Classes_General_Main::setKeywords($metaKeywords);

    /**
     * find and set description
     */
    if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]) <> "") {
        $metaDescription = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"];
    } elseif (trim($arResult['ELEMENT']['PROPERTY_META_DESCRIPTION_VALUE']) <> "") {
        $metaDescription = $arResult['ELEMENT']['PROPERTY_META_DESCRIPTION_VALUE'];
    } else {
        $metaDescription = "";
    }
    Novagroup_Classes_General_Main::setDescription($metaDescription);
}

if(($_REQUEST['CAJAX'] != 1) && !empty($arElement['NAME'])){
    $arFields = array(
        "PRODUCT_ID" => $arElement['ID'],
        "LID" => SITE_ID,
        "NAME" => $arElement['NAME'],
        "IBLOCK_ID" => $arElement["IBLOCK_ID"]
    );
    $result = CSaleViewedProduct::Add($arFields);
}

$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));


    if ($returnFlag == true) {
        @define("ERROR_404", "Y");
        @define("SEARCH_NOT_FOUND", "N");
        $arResult['SEARCH_NOT_FOUND'] = "N";
        $this -> IncludeComponentTemplate('notfound');
        return;
    } else {
        $this->IncludeComponentTemplate();
    }

    if($arResult['SEARCH_NOT_FOUND']=="Y")
    {
        @define("SEARCH_NOT_FOUND", "Y");
    }

}
?>