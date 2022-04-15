<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
 
function myAdd2BasketByProductID($PRODUCT_ID, $QUANTITY = 1, $arRewriteFields = array(), $arProductParams = false) {
    global $APPLICATION;

    /* for old use */
    if (false === $arProductParams) {
        $arProductParams = $arRewriteFields;
        $arRewriteFields = array();
    }

    $PRODUCT_ID = IntVal($PRODUCT_ID);
    if ($PRODUCT_ID <= 0) {
        $APPLICATION->ThrowException(GetMessage('CATALOG_ERR_EMPTY_PRODUCT_ID'), "EMPTY_PRODUCT_ID");
        return false;

        $QUANTITY = DoubleVal($QUANTITY);
        if ($QUANTITY <= 0)
            $QUANTITY = 1;

        if (!CModule::IncludeModule("sale")) {
            $APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_SALE_MODULE'), "NO_SALE_MODULE");
            return false;
        }
    }

    if (CModule::IncludeModule("statistic") && IntVal($_SESSION["SESS_SEARCHER_ID"]) > 0) {
        $APPLICATION->ThrowException(GetMessage('CATALOG_ERR_SESS_SEARCHER'), "SESS_SEARCHER");
        return false;
    }

    $arProduct = CCatalogProduct::GetByID($PRODUCT_ID);
    if ($arProduct === false) {
        $APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_PRODUCT'), "NO_PRODUCT");
        return false;
    }

    $CALLBACK_FUNC = "CatalogBasketCallback";

    //ADD PRODUCT TO SUBSCRIBE
    if ((isset($arRewriteFields["SUBSCRIBE"]) && $arRewriteFields["SUBSCRIBE"] == "Y")) {
        global $USER;

        if ($USER->IsAuthorized() && !isset($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()])) {
            $_SESSION["NOTIFY_PRODUCT"][$USER->GetID()] = array();
        }

        $arBuyerGroups = CUser::GetUserGroup($USER->GetID());
        $arPrice = CCatalogProduct::GetOptimalPrice($PRODUCT_ID, 1, $arBuyerGroups, "N", array(), SITE_ID, array());

        $arCallbackPrice = array(
            "PRICE" => $arPrice["DISCOUNT_PRICE"],
            "VAT_RATE" => 0,
            "CURRENCY" => CSaleLang::GetLangCurrency(SITE_ID),
            "QUANTITY" => 1
        );
    } else {
        $arRewriteFields["SUBSCRIBE"] = "N";

        if ($arProduct["CAN_BUY_ZERO"] != 'Y' && $arProduct["QUANTITY_TRACE"] == "Y" && DoubleVal($arProduct["QUANTITY"]) <= 0) {
            $APPLICATION->ThrowException(GetMessage('CATALOG_ERR_PRODUCT_RUN_OUT'), "PRODUCT_RUN_OUT");
            return false;
        }

        $arCallbackPrice = CSaleBasket::ReReadPrice($CALLBACK_FUNC, "catalog", $PRODUCT_ID, $QUANTITY);
        if (!is_array($arCallbackPrice) || empty($arCallbackPrice)) {
            $APPLICATION->ThrowException(GetMessage('CATALOG_PRODUCT_PRICE_NOT_FOUND'), "NO_PRODUCT_PRICE");
            return false;
        }
    }

    $dbIBlockElement = CIBlockElement::GetList(array(), array(
                "ID" => $PRODUCT_ID,
                "ACTIVE" => "Y",
                "ACTIVE_DATE" => "Y",
                "CHECK_PERMISSIONS" => "Y",
                "MIN_PERMISSION" => "R",
                    ), false, false, array(
                "ID",
                "IBLOCK_ID",
                "XML_ID",
                "NAME",
                "DETAIL_PAGE_URL",
            ));
    $arIBlockElement = $dbIBlockElement->GetNext();

    if ($arIBlockElement == false) {
        $APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_IBLOCK_ELEMENT'), "NO_IBLOCK_ELEMENT");
        return false;
    }

    $arProps = array();
 
    $dbIBlock = CIBlock::GetList(  array(), array("ID" => $arIBlockElement["IBLOCK_ID"])  );
    if ($arIBlock = $dbIBlock->Fetch()) {
        $arProps[] = array(
            "NAME" => "Catalog XML_ID",
            "CODE" => "CATALOG.XML_ID",
            "VALUE" => $arIBlock["XML_ID"]
        );
    }

    $arProps[] = array(
        "NAME" => "Product XML_ID",
        "CODE" => "PRODUCT.XML_ID",
        "VALUE" => $arIBlockElement["XML_ID"]
    );

    $arPrice = CPrice::GetByID($arCallbackPrice["PRODUCT_PRICE_ID"]);
 
    $arFields = array(
        "PRODUCT_ID" => $PRODUCT_ID,
        //     "DISCOUNT_PRICE" => 123,      //величина скидки
         //    "DISCOUNT_VALUE" => 50,
        //     "DISCOUNT_NAME" => '234',
        "PRODUCT_PRICE_ID" => $arCallbackPrice["PRODUCT_PRICE_ID"],
        "PRICE" => $arCallbackPrice["PRICE"], 
        "CURRENCY" => $arCallbackPrice["CURRENCY"],
        "WEIGHT" => $arProduct["WEIGHT"],
        "QUANTITY" => $QUANTITY,
        "LID" => SITE_ID,
        "DELAY" => "N",
        "CAN_BUY" => "Y",
        "NAME" => $arIBlockElement["~NAME"],
        "CALLBACK_FUNC" => $CALLBACK_FUNC,
        "MODULE" => "catalog",
        "NOTES" => $arPrice["CATALOG_GROUP_NAME"],
        "ORDER_CALLBACK_FUNC" => "CatalogBasketOrderCallback",
        "CANCEL_CALLBACK_FUNC" => "CatalogBasketCancelCallback",
        "PAY_CALLBACK_FUNC" => "CatalogPayOrderCallback",
        "DETAIL_PAGE_URL" => $arIBlockElement["DETAIL_PAGE_URL"],
        "CATALOG_XML_ID" => $arIBlock["XML_ID"],
        "PRODUCT_XML_ID" => $arIBlockElement["XML_ID"],
        "VAT_RATE" => $arCallbackPrice['VAT_RATE'],
        "SUBSCRIBE" => $arRewriteFields["SUBSCRIBE"]
    );
 
    if ($arProduct["CAN_BUY_ZERO"] != "Y" && $arProduct["QUANTITY_TRACE"] == "Y") {
        if (IntVal($arProduct["QUANTITY"]) - $QUANTITY < 0)
            $arFields["QUANTITY"] = DoubleVal($arProduct["QUANTITY"]);
    }

    if (is_array($arProductParams) && !empty($arProductParams)) {
        foreach ($arProductParams as &$arOneProductParams) {
            $arProps[] = array(
                "NAME" => $arOneProductParams["NAME"],
                "CODE" => $arOneProductParams["CODE"],
                "VALUE" => $arOneProductParams["VALUE"],
                "SORT" => $arOneProductParams["SORT"]
            );
        }
        if (isset($arOneProductParams))
            unset($arOneProductParams);
    }
    $arFields["PROPS"] = $arProps;

    if (is_array($arRewriteFields) && !empty($arRewriteFields)) {
        while (list($key, $value) = each($arRewriteFields))
            $arFields[$key] = $value;
    }

    $addres = CSaleBasket::Add($arFields);
    if ($addres) {
        if ((isset($arRewriteFields["SUBSCRIBE"]) && $arRewriteFields["SUBSCRIBE"] == "Y"))
            $_SESSION["NOTIFY_PRODUCT"][$USER->GetID()][$PRODUCT_ID] = $PRODUCT_ID;

        if (CModule::IncludeModule("statistic"))
            CStatistic::Set_Event("sale2basket", "catalog", $arFields["DETAIL_PAGE_URL"]);
    }

    return $addres;
}

if (($_REQUEST['action'] == "ADD2BASKET") && $_REQUEST['id'] > 0 &&
        CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")) {
    myAdd2BasketByProductID($_REQUEST['id']);
}

$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", ".default", array(
    "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
    "SHOW_PERSONAL_LINK" => "N"
        ), false, Array('')
);
