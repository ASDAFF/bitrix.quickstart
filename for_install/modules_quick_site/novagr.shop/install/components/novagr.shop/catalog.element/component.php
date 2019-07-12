<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// some servers return ../index.php in path
$currentUri = (isset($arParams['COMPONENT_CURRENT_PAGE']) and strlen($arParams['COMPONENT_CURRENT_PAGE'])>0) ? $arParams['COMPONENT_CURRENT_PAGE'] : $APPLICATION->GetCurPage(false);
$arParams['COMPONENT_CURRENT_PAGE'] = $currentUri;

if( CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale") ) {
} else {
	die(GetMessage("MODULES_NOT_INSTALLED"));
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if ($arParams["CATALOG_COMMENTS_ENABLE"] != "N") $arParams["CATALOG_COMMENTS_ENABLE"] = "Y";

if ($arParams["CATALOG_SUBSCRIBE_ENABLE"] != "Y") $arParams["CATALOG_SUBSCRIBE_ENABLE"] = "N";

if ($arParams["LANDING_PAGE"] != "Y") $arParams["LANDING_PAGE"] = "N";

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
    $arParams['SECTION_CODE'] = $_REQUEST['secid'];
    $arParams['ELEMENT_CODE'] = $_REQUEST['elmid'];
}

if ($arParams['ELEMENT_ID'] > 0) {

    $elemFlag = true;

} elseif (!empty($arParams['SECTION_CODE']) and !empty($arParams['ELEMENT_CODE'] ))  {
    // мы в дет. карточке
    $elemFlag = true;
    $arResult['SECTION_CODE'] = $arParams['SECTION_CODE'];
    //$arParams['ELEMENT_CODE'] = $arParams['ELEMENT_CODE'];
} else {

    $page404flag = true;
}

if ($page404flag == true) {
	
	//$arResult['ELEMENT'] empty  - return 404 error
	
} else {
	
	$arrayGroupCanEdit = array(1);
	if (!empty($arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"])) $arrayGroupCanEdit[] = $arParams["INET_MAGAZ_ADMIN_USER_GROUP_ID"];
	
	/// If the user is an sale_administrator show a pencil to edit
	$arParams['SHOW_EDIT_BUTTON'] = "N";

    global $USER;
    $arUserGroups = $USER->GetUserGroupArray();

    // для быстр. просмотра не показываем кнопку редактирования
    if ($_REQUEST["CAJAX"] != 1) {

        if (count(array_intersect($arUserGroups, $arrayGroupCanEdit))>0)
            $arParams['SHOW_EDIT_BUTTON'] = "Y";
    }
    $arResult['OPT_USER'] = 0;
    if (!empty($arParams["OPT_GROUP_ID"])) {
        if (in_array($arParams["OPT_GROUP_ID"], $arUserGroups)) {
            $arResult['OPT_USER'] = 1;
        }
    }

    $returnFlag = false;

    /**
     * @var CBitrixComponent $this
     */
    if ( $this -> StartResultCache(false, $USER->GetGroups()))
    //if ( 1==1)
    {
        if (!empty($arResult['SECTION_CODE'])) {
            // get the properties for the current section
            $arSelectSections = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID', "DEPTH_LEVEL" );
            $arFilterSections = array("CODE" => $arResult['SECTION_CODE'], "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"]);

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

        if ( $arParams["ELEMENT_ID"] > 0 )
            $arFilter["ID"] = $arParams["ELEMENT_ID"];

        if (!empty($arParams["ELEMENT_CODE"]))
            $arFilter["CODE"] = $arParams["ELEMENT_CODE"];

        $arSelect = array(
            "IBLOCK_ID",
            "IBLOCK_NAME",
            "IBLOCK_SECTION_ID",
            "ID",
            "NAME",
            "DETAIL_PICTURE",
            "DETAIL_PAGE_URL",
            "DETAIL_TEXT",
            "PROPERTY_TITLE",
            "PROPERTY_HEADER1",
            "PROPERTY_KEYWORDS",
            "PROPERTY_META_DESCRIPTION"
        );

        require(dirname(__FILE__) . "/getElement.php");

        if(CModule::IncludeModule("novagr.shop"))
        {
            // check if comments turn on
            $filename = $_SERVER["DOCUMENT_ROOT"]  . "/local/modules/novagr.shop/comments.txt";

            if (!file_exists($filename)) {

                $CommentsOn = "1";

            } else {
                $content = unserialize(file_get_contents($filename));
                $CommentsOn = (int)$content["on"];
            }
        } else {
            $CommentsOn = "0";
        }
        if ($arParams["CATALOG_COMMENTS_ENABLE"] == "N") $CommentsOn = "0";

        require(dirname(__FILE__) . "/getProps.php");

        $countPreviewPictureId = count($colorsPreviewPictures);

        if ( $countPreviewPictureId > 0 ) {
            $arFilter = "";
            foreach ($colorsPreviewPictures as $val) $arFilter .= $val.",";

            $rsFile = CFile::GetList(false, array("@ID" => $arFilter));
            while ($data = $rsFile -> GetNext())
            {
                $PICTURE_SRC[$data["ID"]]
                    = "/upload/".$data["SUBDIR"]."/".$data["FILE_NAME"];
            }

            foreach ($colorsPreviewPictures as $key => $val)
                $arResult["PREVIEW_PICTURE"][$key] = $PICTURE_SRC[$val];

        }

        // get data for text fields (sizes table , the text of delivery)
        $arFilter = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $arParams["ARTICLES_IBLOCK_ID"],
            array(
                "LOGIC" => "OR",
                array("CODE" => "delivery"),
                array("CODE" => "tablitsa-razmerov"),
            )
        );

        $arSelect = array( "ID", "NAME", "CODE", "DETAIL_TEXT" );
        $rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
        while ($data = $rsElement -> Fetch())
        {
            $arResult[$data["CODE"]] = $data["DETAIL_TEXT"];
        }

        /**
         * сортировка цветов по индексу
         */
        if (is_array($arResult["CURRENT_ELEMENT"]["COLORS"])) {
            $CURRENT_ELEMENT_COLORS = array();
            foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $color) {
                $arElementColor = GetIBlockElement($color);
                $CURRENT_ELEMENT_COLORS[$color] = $arElementColor['SORT'];
            }
            asort($CURRENT_ELEMENT_COLORS);

            $arResult["CURRENT_ELEMENT"]["COLORS"] = $CURRENT_ELEMENT_COLORS;
            $CURRENT_ELEMENT_COLORS = array();
            foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $key => $color) {
                $CURRENT_ELEMENT_COLORS[] = $key;
            }
            $arResult["CURRENT_ELEMENT"]["COLORS"] = $CURRENT_ELEMENT_COLORS;
        }


        /*cписок фотографий товара*/
        $arResult["SOC_IMAGES"] = array();

        //картинка по умолчанию, если ничего не найдено
        $catalogPhotos = new Novagroup_Classes_General_CatalogPhoto($arResult['ID'],$arResult['IBLOCK_ID']);
        $catalogPhoto = $catalogPhotos->getPhoto();
        $noPhotoPath = CFile::GetPath($catalogPhoto['PHOTO']);

        //заполнение массива фотографий
        foreach ($arResult["OFFERS"] as $item) {
            $colorId = $item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"];

            $curPhotosSmall = array();
            $curPhotosMiddle = array();
            $curPhotosBig = array();
            $curPhotosBigHeight = array();

            if (!empty($arResult["DETAIL_PICTURE"]) && $arResult["USE_MORE_PHOTO"] == true) {

                $curPhotosSmall[] = $arResult["DETAIL_PICTURE_MIN_SRC"];
                $curPhotosBig[] = $arResult["DETAIL_PICTURE_ARR"]["SRC"];
                $curPhotosBigHeight[] = $arResult["DETAIL_PICTURE_ARR"]["HEIGHT"];
                $curPhotosMiddle[] = $arResult["DETAIL_PICTURE_ARR_MIDDLE"]["src"];

            }
            if ($arResult["USE_MORE_PHOTO"] == true) {
                if (count($arResult["ELEMENT_MORE_PHOTO"]))
                    foreach ($arResult["ELEMENT_MORE_PHOTO"] as $photoId => $photo) {

                        $curPhotosSmall[] = $arResult['PREVIEW_PICTURE'][$photoId];
                        $curPhotosBig[] = $photo["src"];
                        $curPhotosBigHeight[] = $photo["height"];
                        $curPhotosMiddle[] = $arResult["ELEMENT_MORE_PHOTO_MIDDLE"][$photoId]["src"];

                    }
            } elseif (count($arResult["ELEMENT_COLORS_PHOTOS"][$colorId]) == 0) {

                // if there is nophoto then we show a  dummy-photo
                $curPhotosSmall[] = $noPhotoPath;
                $curPhotosBig[] = $noPhotoPath;
                $curPhotosMiddle[] = $noPhotoPath;
                $curPhotosBigHeight[] = 0;

            } else {
                foreach ($arResult["ELEMENT_COLORS_PHOTOS"][$colorId] as $photoId) {

                    $curPhotosSmall[] = $arResult['PREVIEW_PICTURE'][$photoId];
                    $curPhotosBig[] = $arResult["ELEMENT_PHOTO"][$photoId]["src"];
                    $curPhotosBigHeight[] = $arResult["ELEMENT_PHOTO"][$photoId]["height"];
                    $curPhotosMiddle[] = $arResult["ELEMENT_PHOTO_MIDDLE"][$photoId]["src"];

                }
            }
            $SOC_IMAGES = array();
            $SOC_IMAGES['curPhotosSmall'] = $curPhotosSmall;
            $SOC_IMAGES['curPhotosBig'] = $curPhotosBig;
            $SOC_IMAGES['curPhotosMiddle'] = $curPhotosMiddle;
            $SOC_IMAGES['curPhotosBigHeight'] = $curPhotosBigHeight;

            $arResult["SOC_IMAGES"][$item['ID']] = $SOC_IMAGES;
        }
        foreach ($arResult["SOC_IMAGES"] as $SOC_IMAGES ) {
            foreach ($SOC_IMAGES['curPhotosBigHeight'] as $key=>$height)
            {
                if ($height > 0 and isset($SOC_IMAGES['curPhotosBig'][$key])) {
                    $arResult['SOC_PHOTO'] = $SOC_IMAGES['curPhotosBig'][$key];
                    break;
                }
            }
            if (isset($arResult['SOC_PHOTO'])) break;
        }
        $arResult['SOC_PHOTO'] = (isset( $arResult['SOC_PHOTO'] )) ? $arResult['SOC_PHOTO'] : null;
        $arResult['DETAIL_IMAGES'] = $arResult["SOC_IMAGES"];

        // save variables to cache
        //$detailCardView = COption::GetOptionString("main", "detail_card", "1");

        $arResult['CACHE_DATA'] = array(
            "arResult" => $arResult,
            "arElement" => $arElement,
            "CommentsOn" => $CommentsOn,
            "previewPicture" => $arResult["PREVIEW_PICTURE"],
            "arCurPriceCode" => $arResult["CUR_PRICE_CODE"],
            "arOffers" => $arResult["OFFERS"],
            "arCurrentElement" => $arResult["CURRENT_ELEMENT"],
            "maxCountSize" => $arResult["MAX_COUNT_SIZE"],
            "maxCountColor" => $arResult["MAX_COUNT_COLOR"],
            "elemID" => $arResult["ID"],
            "elemIblockID" => $arResult["IBLOCK_ID"],
            "elemSectionID" => $arResult["IBLOCK_SECTION_ID"],
            "useMorePhotoFlag" => $arResult["USE_MORE_PHOTO"],
            "arrElementsColorsPhotos" => $arResult["ELEMENT_COLORS_PHOTOS"],
            "arElementsPhotos" => $arResult["ELEMENT_PHOTO"],
            "arElementsPhotosMiddle" => $arResult["ELEMENT_PHOTO_MIDDLE"],
            "arMixData" => $arResult["mixData"],
            "returnFlag" => $returnFlag,
            "deliveryText" => $arResult["delivery"],
            "sizeTableText" => $arResult["tablitsa-razmerov"],
            "arNavSection" => $arNavSection,
            "detailImages" =>  $arResult['DETAIL_IMAGES'],
           /* "detailCardView" => $detailCardView,*/
        );

        $this->SetResultCacheKeys(array(
            "CACHE_DATA"
        ));

    } // end work with cache
    $this->EndResultCache();

    $CACHE_DATA = $arResult['CACHE_DATA'];
    $arResult = $arResult['CACHE_DATA']["arResult"];
    $arResult['CACHE_DATA'] = $CACHE_DATA;

    $returnFlag = $arResult['CACHE_DATA']["returnFlag"];
    $arNavSection = $arResult['CACHE_DATA']["arNavSection"];

    //$detailCardView = $arResult['CACHE_DATA']["detailCardView"];
    $arResult["DETAIL_CARD_VIEW"] = COption::GetOptionString("main", "detail_card", "1");

    $arResult["ELEMENT"] = $arResult['CACHE_DATA']["arElement"];
    $arResult["COMMENTS_ON"] = $arResult['CACHE_DATA']["CommentsOn"];
    $arResult["PREVIEW_PICTURE"] = $arResult['CACHE_DATA']["previewPicture"];
    $arResult["CUR_PRICE_CODE"] = $arResult['CACHE_DATA']["arCurPriceCode"];
    $arResult["OFFERS"] = $arResult['CACHE_DATA']["arOffers"];
    $arResult["CURRENT_ELEMENT"] = $arResult['CACHE_DATA']["arCurrentElement"];
    $arResult["MAX_COUNT_SIZE"] = $arResult['CACHE_DATA']["maxCountSize"];
    $arResult["MAX_COUNT_COLOR"] = $arResult['CACHE_DATA']["maxCountColor"];
    $arResult["ID"] = $arResult['CACHE_DATA']["elemID"];
    $arResult["IBLOCK_ID"] = $arResult['CACHE_DATA']["elemIblockID"];
    $arResult["IBLOCK_SECTION_ID"] = $arResult['CACHE_DATA']["elemSectionID"];
    $arResult["USE_MORE_PHOTO"] = $arResult['CACHE_DATA']["useMorePhotoFlag"];
    $arResult["ELEMENT_COLORS_PHOTOS"] = $arResult['CACHE_DATA']["arrElementsColorsPhotos"];
    $arResult["ELEMENT_PHOTO"] = $arResult['CACHE_DATA']["arElementsPhotos"];;
    $arResult["ELEMENT_PHOTO_MIDDLE"] = $arResult['CACHE_DATA']["arElementsPhotosMiddle"];
    $arResult["mixData"] = $arResult['CACHE_DATA']["arMixData"];
    $arResult["delivery"] = $arResult['CACHE_DATA']["deliveryText"];
    $arResult["tablitsa-razmerov"] = $arResult['CACHE_DATA']["sizeTableText"];
    $arResult["DETAIL_IMAGES"] = $arResult['CACHE_DATA']["detailImages"];

    if (trim($arResult["ELEMENT"]["PROPERTY_HEADER1_VALUE"]) <> "") {
        $arResult["ELEMENT"]["NAME"] = $arResult["ELEMENT"]["PROPERTY_HEADER1_VALUE"];
    }

    $countSections = count($arNavSection);

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
                if ($section["DEPTH_LEVEL"] > 2 || $countSections == $i) {
                    $APPLICATION->AddChainItem($section['NAME'], $linkSection);
                } else {
                    $APPLICATION->AddChainItem($section['NAME']);
                }
                //$APPLICATION->AddChainItem($section['NAME'], $linkSection);
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

            //$arResult["SIZES_COLORS"] = Novagroup_Classes_General_Basket::getBasketSizesColors($userID, $arParams['CATALOG_OFFERS_IBLOCK_ID']);

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
            
        } else {

            $arResult["USER_ID"] = 0;
        }

        // sort an array of sizes from small to large
        foreach ($arResult["CURRENT_ELEMENT"]["STD_SIZE"] as $key => $size) {
            $arResult["CURRENT_ELEMENT"]["STD_SIZE"][$key]['SORT'] = $arResult['mixData'][$key]['SORT'];
        }
        uasort($arResult["CURRENT_ELEMENT"]["STD_SIZE"], 'novagr_main_sort');

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
    }


$arReturnUrl = array(
    "add_element" => CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "DETAIL_PAGE_URL"),
    "delete_element" => $linkSection,
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
    }

    $this->IncludeComponentTemplate();

    if($arResult['SEARCH_NOT_FOUND']=="Y")
    {
        @define("SEARCH_NOT_FOUND", "Y");
    }

}
?>