<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    CUtil::InitJSCore(array('popup'));

    CPageOption::SetOptionString("main", "nav_page_in_session", "N");

    /*************************************************************************
    Processing of received parameters
    *************************************************************************/
    if(!isset($arParams["CACHE_TIME"]))
        $arParams["CACHE_TIME"] = 36000000;

    $arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
    $arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

    $arParams["SECTION_ID"] = intval($arParams["~SECTION_ID"]);
    if($arParams["SECTION_ID"] > 0 && $arParams["SECTION_ID"]."" != $arParams["~SECTION_ID"])
    {
        ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
        @define("ERROR_404", "Y");
        if($arParams["SET_STATUS_404"]==="Y")
            CHTTP::SetStatus("404 Not Found");
        return;
    }

    if (!in_array($arParams["INCLUDE_SUBSECTIONS"], array('Y', 'A', 'N')))
        $arParams["INCLUDE_SUBSECTIONS"] = 'Y';
    $arParams["SHOW_ALL_WO_SECTION"] = $arParams["SHOW_ALL_WO_SECTION"]==="Y";

    if(strlen($arParams["ELEMENT_SORT_FIELD"])<=0)
        $arParams["ELEMENT_SORT_FIELD"]="sort";

    if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER"]))
        $arParams["ELEMENT_SORT_ORDER"]="asc";

    if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
    {
        $arrFilter = array();
    }
    else
    {
        global $$arParams["FILTER_NAME"];
        $arrFilter = ${$arParams["FILTER_NAME"]};
        if(!is_array($arrFilter))
            $arrFilter = array();
    }

    $arParams["SECTION_URL"]=trim($arParams["SECTION_URL"]);
    $arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
    $arParams["BASKET_URL"]=trim($arParams["BASKET_URL"]);
    if(strlen($arParams["BASKET_URL"])<=0)
        $arParams["BASKET_URL"] = "/personal/basket.php";

    $arParams["ACTION_VARIABLE"]=trim($arParams["ACTION_VARIABLE"]);
    if(strlen($arParams["ACTION_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"]))
        $arParams["ACTION_VARIABLE"] = "action";

    $arParams["PRODUCT_ID_VARIABLE"]=trim($arParams["PRODUCT_ID_VARIABLE"]);
    if(strlen($arParams["PRODUCT_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_ID_VARIABLE"]))
        $arParams["PRODUCT_ID_VARIABLE"] = "id";

    $arParams["PRODUCT_QUANTITY_VARIABLE"]=trim($arParams["PRODUCT_QUANTITY_VARIABLE"]);
    if(strlen($arParams["PRODUCT_QUANTITY_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_QUANTITY_VARIABLE"]))
        $arParams["PRODUCT_QUANTITY_VARIABLE"] = "quantity";

    $arParams["PRODUCT_PROPS_VARIABLE"]=trim($arParams["PRODUCT_PROPS_VARIABLE"]);
    if(strlen($arParams["PRODUCT_PROPS_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_PROPS_VARIABLE"]))
        $arParams["PRODUCT_PROPS_VARIABLE"] = "prop";

    $arParams["SECTION_ID_VARIABLE"]=trim($arParams["SECTION_ID_VARIABLE"]);
    if(strlen($arParams["SECTION_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SECTION_ID_VARIABLE"]))
        $arParams["SECTION_ID_VARIABLE"] = "SECTION_ID";

    $arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
    $arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]==="Y"; //Turn off by default
    $arParams["DISPLAY_COMPARE"] = $arParams["DISPLAY_COMPARE"]=="Y";

    $arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
    if($arParams["PAGE_ELEMENT_COUNT"]<=0)
        $arParams["PAGE_ELEMENT_COUNT"]=20;
    $arParams["LINE_ELEMENT_COUNT"] = intval($arParams["LINE_ELEMENT_COUNT"]);
    if($arParams["LINE_ELEMENT_COUNT"]<=0)
        $arParams["LINE_ELEMENT_COUNT"]=3;

    if(!is_array($arParams["PROPERTY_CODE"]))
        $arParams["PROPERTY_CODE"] = array();
    foreach($arParams["PROPERTY_CODE"] as $k=>$v)
        if($v==="")
            unset($arParams["PROPERTY_CODE"][$k]);

        if(!is_array($arParams["PRICE_CODE"]))
        $arParams["PRICE_CODE"] = array();
    $arParams["USE_PRICE_COUNT"] = $arParams["USE_PRICE_COUNT"]=="Y";
    $arParams["SHOW_PRICE_COUNT"] = intval($arParams["SHOW_PRICE_COUNT"]);
    if($arParams["SHOW_PRICE_COUNT"]<=0)
        $arParams["SHOW_PRICE_COUNT"]=1;
    $arParams["USE_PRODUCT_QUANTITY"] = $arParams["USE_PRODUCT_QUANTITY"]==="Y";

    if(!is_array($arParams["PRODUCT_PROPERTIES"]))
        $arParams["PRODUCT_PROPERTIES"] = array();
    foreach($arParams["PRODUCT_PROPERTIES"] as $k=>$v)
        if($v==="")
            unset($arParams["PRODUCT_PROPERTIES"][$k]);

        $arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
    $arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
    $arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
    $arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
    $arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
    $arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
    $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
    $arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

    $arNavParams = array(
        "nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
        "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
        "bShowAll" => $arParams["PAGER_SHOW_ALL"],
    );
    $arNavigation = CDBResult::GetNavParams($arNavParams);
    if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
        $arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];

    $arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
    if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
        $arParams["CACHE_TIME"] = 0;

    $arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

    $arParams['CONVERT_CURRENCY'] = (isset($arParams['CONVERT_CURRENCY']) && 'Y' == $arParams['CONVERT_CURRENCY'] ? 'Y' : 'N');
    $arParams['CURRENCY_ID'] = trim(strval($arParams['CURRENCY_ID']));
    if ('' == $arParams['CURRENCY_ID'])
    {
        $arParams['CONVERT_CURRENCY'] = 'N';
    }
    elseif ('N' == $arParams['CONVERT_CURRENCY'])
    {
        $arParams['CURRENCY_ID'] = '';
    }


    $strError = "";
    if (array_key_exists($arParams["ACTION_VARIABLE"], $_REQUEST) && array_key_exists($arParams["PRODUCT_ID_VARIABLE"], $_REQUEST))
    {
        if(array_key_exists($arParams["ACTION_VARIABLE"]."BUY", $_REQUEST))
            $action = "BUY";
        elseif(array_key_exists($arParams["ACTION_VARIABLE"]."ADD2BASKET", $_REQUEST))
            $action = "ADD2BASKET";
        else
            $action = strtoupper($_REQUEST[$arParams["ACTION_VARIABLE"]]);

        $productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
        if(($action == "ADD2BASKET" || $action == "BUY" || $action == "SUBSCRIBE_PRODUCT") && $productID > 0)
        {
            if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
            {
                if($arParams["USE_PRODUCT_QUANTITY"])
                    $QUANTITY = intval($_REQUEST[$arParams["PRODUCT_QUANTITY_VARIABLE"]]);
                if($QUANTITY <= 1)
                    $QUANTITY = 1;

                $product_properties = array();
                if(count($arParams["PRODUCT_PROPERTIES"]))
                {
                    if(is_array($_REQUEST[$arParams["PRODUCT_PROPS_VARIABLE"]]))
                    {
                        $product_properties = CIBlockPriceTools::CheckProductProperties(
                            $arParams["IBLOCK_ID"],
                            $productID,
                            $arParams["PRODUCT_PROPERTIES"],
                            $_REQUEST[$arParams["PRODUCT_PROPS_VARIABLE"]]
                        );
                        if(!is_array($product_properties))
                            $strError = GetMessage("CATALOG_ERROR2BASKET").".";
                    }
                    else
                    {
                        $strError = GetMessage("CATALOG_ERROR2BASKET").".";
                    }
                }


                $notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
                $arNotify = unserialize($notifyOption);

                if ($action == "SUBSCRIBE_PRODUCT" && $arNotify[SITE_ID]['use'] == 'Y')
                {
                    $arRewriteFields["SUBSCRIBE"] = "Y";
                    $arRewriteFields["CAN_BUY"] = "N";
                }

                if(!$strError && Add2BasketByProductID($productID, $QUANTITY, $arRewriteFields, $product_properties))
                {
                    if($action == "BUY")
                        LocalRedirect($arParams["BASKET_URL"]);
                    else
                        LocalRedirect($APPLICATION->GetCurPageParam("", array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
                }
                else
                {
                    if($ex = $GLOBALS["APPLICATION"]->GetException())
                        $strError = $ex->GetString();
                    else
                        $strError = GetMessage("CATALOG_ERROR2BASKET").".";
                }
            }
        }
    }
    if(strlen($strError)>0)
    {
        ShowError($strError);
        return;
    }
    /*************************************************************************
    Work with cache
    *************************************************************************/
    if($this->StartResultCache(false, array($arrFilter, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $arNavigation)))
    {
        $i = 0;
        if(!CModule::IncludeModule("iblock"))
        {
            $this->AbortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }

        global $CACHE_MANAGER;


        $arSelect = array();
        if(isset($arParams["SECTION_USER_FIELDS"]) && is_array($arParams["SECTION_USER_FIELDS"]))
        {
            foreach($arParams["SECTION_USER_FIELDS"] as $field)
                if(is_string($field) && preg_match("/^UF_/", $field))
                    $arSelect[] = $field;
        }
        if(preg_match("/^UF_/", $arParams["META_KEYWORDS"])) $arSelect[] = $arParams["META_KEYWORDS"];
        if(preg_match("/^UF_/", $arParams["META_DESCRIPTION"])) $arSelect[] = $arParams["META_DESCRIPTION"];
        if(preg_match("/^UF_/", $arParams["BROWSER_TITLE"])) $arSelect[] = $arParams["BROWSER_TITLE"];

        $arFilter = array(
            "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
            "IBLOCK_ACTIVE"=>"Y",
            "ACTIVE"=>"Y",
            "GLOBAL_ACTIVE"=>"Y",
        );

        $bSectionFound = false;
        //Hidden triky parameter USED to display linked
        //by default it is not set
        if($arParams["BY_LINK"]==="Y")
        {
            $arResult = array(
                "ID" => 0,
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            );
            $bSectionFound = true;
        }
        elseif(strlen($arParams["SECTION_CODE"]) > 0)
        {
            $arFilter["CODE"]=$arParams["SECTION_CODE"];
            $rsSection = CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);
            $rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
            $arResult = $rsSection->GetNext();
            if($arResult)
                $bSectionFound = true;
        }
        elseif($arParams["SECTION_ID"])
        {
            $arFilter["ID"]=$arParams["SECTION_ID"];
            $rsSection = CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);
            $rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
            $arResult = $rsSection->GetNext();
            if($arResult)
                $bSectionFound = true;
        }
        else
        {
            //Root section (no section filter)
            $arResult = array(
                "ID" => 0,
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            );
            $bSectionFound = true;
        }

        if(!$bSectionFound)
        {
            $this->AbortResultCache();
            ShowError(GetMessage("CATALOG_SECTION_NOT_FOUND"));
            @define("ERROR_404", "Y");
            if($arParams["SET_STATUS_404"]==="Y")
                CHTTP::SetStatus("404 Not Found");
            return;
        }
        elseif($arResult["ID"] > 0 && $arParams["ADD_SECTIONS_CHAIN"])
        {
            $arResult["PATH"] = array();
            $rsPath = GetIBlockSectionPath($arResult["IBLOCK_ID"], $arResult["ID"]);
            $rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
            while($arPath = $rsPath->GetNext())
            {
                $arResult["PATH"][]=$arPath;
            }
        }

        //This function returns array with prices description and access rights
        //in case catalog module n/a prices get values from element properties
        $arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);

        $arResult['CONVERT_CURRENCY'] = $arConvertParams;

        $arResult["PICTURE"] = CFile::GetFileArray($arResult["PICTURE"]);
        $arResult["DETAIL_PICTURE"] = CFile::GetFileArray($arResult["DETAIL_PICTURE"]);

        // list of the element fields that will be used in selection
        $arSelect = array(
            "ID",
            "NAME",
            "CODE",
            "DATE_CREATE",
            "ACTIVE_FROM",
            "ACTIVE_TO",
            "CREATED_BY",
            "IBLOCK_ID",
            "IBLOCK_SECTION_ID",
            "DETAIL_PAGE_URL",
            "DETAIL_TEXT",
            "DETAIL_TEXT_TYPE",
            "DETAIL_PICTURE",
            "PREVIEW_TEXT",
            "PREVIEW_TEXT_TYPE",
            "PREVIEW_PICTURE",
            "CATALOG_GROUP_1",
            "TAGS",
            "PROPERTY_*",
        );
        $arFilter = array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "IBLOCK_LID" => SITE_ID,
            "IBLOCK_ACTIVE" => "Y",
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y",
            "CHECK_PERMISSIONS" => "Y",
            "MIN_PERMISSION" => "R",
            "INCLUDE_SUBSECTIONS" => ($arParams["INCLUDE_SUBSECTIONS"] == 'N' ? 'N' : 'Y'),
        );
        if ($arParams["INCLUDE_SUBSECTIONS"] == 'A')
            $arFilter["SECTION_GLOBAL_ACTIVE"] = "Y";

        if($arParams["BY_LINK"]!=="Y")
        {
            if($arResult["ID"])
                $arFilter["SECTION_ID"] = $arResult["ID"];
            elseif(!$arParams["SHOW_ALL_WO_SECTION"])
                $arFilter["SECTION_ID"] = 0;
            else
            {
                if (is_set($arFilter, 'INCLUDE_SUBSECTIONS'))
                    unset($arFilter["INCLUDE_SUBSECTIONS"]);
                if (is_set($arFilter, 'SECTION_GLOBAL_ACTIVE'))
                    unset($arFilter["SECTION_GLOBAL_ACTIVE"]);
            }
        }


        //PRICES
        
        $arPriceTypeID = array();
        if(!$arParams["USE_PRICE_COUNT"])
        {
            foreach($arResult["PRICES"] as &$value)
            {
                $arSelect[] = $value["SELECT"];
                $arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
            }
            if (isset($value))
                unset($value);
        }
        else
        {
            foreach ($arResult["PRICES"] as &$value)
            {
                $arPriceTypeID[] = $value["ID"];
            }
            if (isset($value))
                unset($value);
        }


        $arSort = array(
            $arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
            "ID" => "DESC",
        );

        $arCurrencyList = array();

        //EXECUTE
        $rsElements = CIBlockElement::GetList($arSort, array_merge($arrFilter, $arFilter), false, $arNavParams, $arSelect);
        $rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
        if($arParams["BY_LINK"]!=="Y" && !$arParams["SHOW_ALL_WO_SECTION"])
            $rsElements->SetSectionContext($arResult);
        $arResult["ITEMS"] = array();
        while($obElement = $rsElements->GetNextElement())
        {
            $arItem = $obElement->GetFields();

            if($arResult["ID"])
                $arItem["IBLOCK_SECTION_ID"] = $arResult["ID"];

            $arButtons = CIBlock::GetPanelButtons(
                $arItem["IBLOCK_ID"],
                $arItem["ID"],
                $arResult["ID"],
                array("SECTION_BUTTONS"=>false, "SESSID"=>false, "CATALOG"=>true)
            );
            $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

            $arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
            $arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

            if(count($arParams["PROPERTY_CODE"]))
                $arItem["PROPERTIES"] = $obElement->GetProperties();
            elseif(count($arParams["PRODUCT_PROPERTIES"]))
                $arItem["PROPERTIES"] = $obElement->GetProperties();

            $arItem["DISPLAY_PROPERTIES"] = array();
            $goods_for_set = array();
            foreach($arParams["PROPERTY_CODE"] as $pid)
            {
                $prop = &$arItem["PROPERTIES"][$pid];
                if(
                    (is_array($prop["VALUE"]) && count($prop["VALUE"]) > 0)
                    || (!is_array($prop["VALUE"]) && strlen($prop["VALUE"]) > 0)
                )
                {

                    $goods_for_set = $prop["VALUE"];
                    $arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "catalog_out");
                }
            }


            //echo "<pre>", print_r($arItem["DISPLAY_PROPERTIES"]), "</pre>";die();
            $arItem["PRODUCT_PROPERTIES"] = CIBlockPriceTools::GetProductProperties(
                $arParams["IBLOCK_ID"],
                $arItem["ID"],
                $arParams["PRODUCT_PROPERTIES"],
                $arItem["PROPERTIES"]
            );





            $arItem["PRICE_MATRIX"] = false;
            $arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);

            $arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem);

            $arItem["BUY_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
            $arItem["ADD_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
            $arItem["COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arItem["ID"], array("action", "id")));
            $arItem["SUBSCRIBE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=SUBSCRIBE_PRODUCT&id=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

            $arItem["SECTION"]["PATH"] = array();
            if($arParams["BY_LINK"]==="Y")
            {
                $rsPath = GetIBlockSectionPath($arItem["IBLOCK_ID"], $arItem["IBLOCK_SECTION_ID"]);
                $rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
                while($arPath = $rsPath->GetNext())
                {
                    $arItem["SECTION"]["PATH"][]=$arPath;
                }
            }


            $arResult["ITEMS"][$i]=$arItem;
                        /*������ ������ ���������� ������*/
            $arResult["PRICES_GOODS"] = CIBlockPriceTools::GetCatalogPrices(1, $arParams["PRICE_CODE"]);
            foreach($arResult["PRICES_GOODS"] as &$value)
            {
                $arSelect_sets[] = $value["SELECT"];
                $arFilter_sets["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
            }

            if (isset($value))
                unset($value);

            $arSelect_sets = array(
                "ID",
                "NAME",
                "CODE",
                "DATE_CREATE",
                "ACTIVE_FROM",
                "ACTIVE_TO",
                "CREATED_BY",
                "IBLOCK_ID",
                "IBLOCK_SECTION_ID",
                "DETAIL_PAGE_URL",
                "DETAIL_TEXT",
                "DETAIL_TEXT_TYPE",
                "DETAIL_PICTURE",
                "PREVIEW_TEXT",
                "PREVIEW_TEXT_TYPE",
                "PREVIEW_PICTURE",
                "TAGS",
                "CATALOG_GROUP_1",
                "PROPERTY_*",
            );
            $arFilter_sets = array(
                "IBLOCK_ID" => 1,
                "ID" => $goods_for_set,
                "IBLOCK_LID" => SITE_ID,
                "IBLOCK_ACTIVE" => "Y",
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "CHECK_PERMISSIONS" => "Y",
                "MIN_PERMISSION" => "R",
                // "INCLUDE_SUBSECTIONS" => ($arParams["INCLUDE_SUBSECTIONS"] == 'N' ? 'N' : 'Y'),
            );

            //EXECUTE
            $rsElements_sets = CIBlockElement::GetList(array(), $arFilter_sets, false, false, $arSelect_sets);

            $arResult["GOOD"] = array();
            $arPriceTypeID = array();

            while($obElement_sets = $rsElements_sets->GetNextElement())
            {

                $arItem_sets = $obElement_sets->GetFields();
                $arItem_sets["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem_sets["PREVIEW_PICTURE"]);
                $arItem_sets["DETAIL_PICTURE"] = CFile::GetFileArray($arItem_sets["DETAIL_PICTURE"]);

                $arItem_sets["PROPERTIES"] = $obElement_sets->GetProperties();
                $arItem_sets["PRICE_MATRIX"] = false;
                $arItem_sets["PRICES"] = CIBlockPriceTools::GetItemPrices(1, $arResult["PRICES_GOODS"], $arItem_sets, $arParams['PRICE_VAT_INCLUDE'], false);

                $arItem_sets["CAN_BUY"] = CIBlockPriceTools::CanBuy(1, $arResult["PRICES_GOODS"], $arItem_sets);

                $arItem_sets["BUY_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem_sets["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
                $arItem_sets["ADD_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem_sets["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
                $arItem_sets["COMPARE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arItem_sets["ID"], array("action", "id")));

                $arResult["ITEMS"][$i]["GOOD"][]=$arItem_sets;
                //$arResult["ELEMENTS"][] = $arItem_sets["ID"];
            }
            $i++;
            /*---------------------------------------------------------------------------*/

            $arResult["ELEMENTS"][] = $arItem["ID"];
        }
        
        //echo "<pre>", print_r($arResult["ITEMS"]), "</pre>";



        $arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
        $arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
        $arResult["NAV_RESULT"] = $rsElements;



        $this->SetResultCacheKeys(array(
                "ID",
                "NAV_CACHED_DATA",
                $arParams["META_KEYWORDS"],
                $arParams["META_DESCRIPTION"],
                $arParams["BROWSER_TITLE"],
                "NAME",
                "PATH",
                "IBLOCK_SECTION_ID",
            ));

        $this->IncludeComponentTemplate();
    }

    $arTitleOptions = null;
    if($USER->IsAuthorized())
    {
        if(
            $APPLICATION->GetShowIncludeAreas()
            || $arParams["SET_TITLE"]
            || isset($arResult[$arParams["BROWSER_TITLE"]])
        )
        {
            if(CModule::IncludeModule("iblock"))
            {
                $UrlDeleteSectionButton = "";
                if($arResult["IBLOCK_SECTION_ID"] > 0)
                {
                    $rsSection = CIBlockSection::GetList(
                        array(),
                        array("=ID" => $arResult["IBLOCK_SECTION_ID"]),
                        false,
                        array("SECTION_PAGE_URL")
                    );
                    $rsSection->SetUrlTemplates("", $arParams["SECTION_URL"]);
                    $arSection = $rsSection->GetNext();
                    $UrlDeleteSectionButton = $arSection["SECTION_PAGE_URL"];
                }

                if(empty($UrlDeleteSectionButton))
                {
                    $url_template = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "LIST_PAGE_URL");
                    $arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"]);
                    $arIBlock["IBLOCK_CODE"] = $arIBlock["CODE"];
                    $UrlDeleteSectionButton = CIBlock::ReplaceDetailURL($url_template, $arIBlock, true, false);
                }

                $arReturnUrl = array(
                    "add_section" => (
                        strlen($arParams["SECTION_URL"])?
                        $arParams["SECTION_URL"]:
                        CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_PAGE_URL")
                    ),
                    "delete_section" => $UrlDeleteSectionButton,
                );
                $arButtons = CIBlock::GetPanelButtons(
                    $arParams["IBLOCK_ID"],
                    0,
                    $arResult["ID"],
                    array("RETURN_URL" =>  $arReturnUrl, "CATALOG"=>true)
                );

                if($APPLICATION->GetShowIncludeAreas())
                    $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

                if($arParams["SET_TITLE"] || isset($arResult[$arParams["BROWSER_TITLE"]]))
                {
                    $arTitleOptions = array(
                        'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_section"]["ACTION"],
                        'PUBLIC_EDIT_LINK' => $arButtons["edit"]["edit_section"]["ACTION"],
                        'COMPONENT_NAME' => $this->GetName(),
                    );
                }
            }
        }
    }

    $this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

    if(isset($arResult[$arParams["META_KEYWORDS"]]))
    {
        $val = $arResult[$arParams["META_KEYWORDS"]];
        if(is_array($val))
            $val = implode(" ", $val);
        $APPLICATION->SetPageProperty("keywords", $val);
    }

    if(isset($arResult[$arParams["META_DESCRIPTION"]]))
    {
        $val = $arResult[$arParams["META_DESCRIPTION"]];
        if(is_array($val))
            $val = implode(" ", $val);
        $APPLICATION->SetPageProperty("description", $val);
    }

    if ($arParams["SET_TITLE"] && isset($arResult["NAME"]))
        $APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);

    if(isset($arResult[$arParams["BROWSER_TITLE"]]))
    {
        $val = $arResult[$arParams["BROWSER_TITLE"]];
        if(is_array($val))
            $val = implode(" ", $val);
        $APPLICATION->SetPageProperty("title", $val, $arTitleOptions);
    }

    if($arParams["ADD_SECTIONS_CHAIN"] && isset($arResult["PATH"]) && is_array($arResult["PATH"]))
    {
        foreach($arResult["PATH"] as $arPath)
        {
            $APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
        }
    }

?>