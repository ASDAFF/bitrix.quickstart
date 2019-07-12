<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeModuleLangFile(__FILE__);

if( CModule::IncludeModule("catalog") ) {
} else {
    die(GetMessage("NOVAGR_JWSHOP_NE_USTANOVLENY_MODUL"));
}
if(!CModule::IncludeModule("sale"))
{
    ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
    return;
}

CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
$arBasketItems = array();

$fUserID = CSaleBasket::GetBasketUserID(True);
$fUserID = IntVal($fUserID);
$productIDS = array();
$num_products = 0;


if (strlen($_REQUEST["TopBasketOrder"]) > 0)
{
    LocalRedirect($arParams["PATH_TO_ORDER"]);
}
/*
if (strlen($_REQUEST["BasketRefresh"]) > 0 || strlen($_REQUEST["BasketOrder"]) > 0 || strlen($_REQUEST["action"]) > 0)
{
    if(strlen($_REQUEST["action"]) > 0)
    {
        $id = IntVal($_REQUEST["id"]);
        if($id > 0)
        {
            $dbBasketItems = CSaleBasket::GetList(
                array(),
                array(
                    "FUSER_ID" => $fUserID,
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL",
                    "ID" => $id,
                ),
                false,
                false,
                array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "CURRENCY")
            );
            if ($arBasket = $dbBasketItems->Fetch())
            {


                if ($_REQUEST["action"] == "delete" )
                {

                    CSaleBasket::Delete($arBasket["ID"]);

                    // update user field for smart site catalog
                    global $USER;
                    $userID = $USER->GetID();
                    if ($userID > 0) {

                        $arRes = CIBlockElement::GetByID($arBasket["PRODUCT_ID"])->GetNext();
                        Novagroup_Classes_General_Basket::updateSizesColorsUserField($userID, $arRes["IBLOCK_ID"]);
                    }
                }
            }
        }
    }


    if (strlen($_REQUEST["BasketOrder"]) > 0)
    {
        LocalRedirect($arParams["PATH_TO_ORDER"]);
    }
    else
    {
        unset($_REQUEST["BasketRefresh"]);
        unset($_REQUEST["BasketOrder"]);
        LocalRedirect($APPLICATION->GetCurPage());
    }
}*/


if ($fUserID > 0)
{
    $dbBasketItems = CSaleBasket::GetList(
        array("DATE_INSERT"=>"DESC"),
        array(
            "FUSER_ID" => $fUserID, "LID" => SITE_ID, "ORDER_ID" => "NULL",
            "CAN_BUY" => "Y",  "SUBSCRIBE" => "N"
        ),
        false,
        false,
        array(
            "ID", "CALLBACK_FUNC", "MODULE", "DETAIL_PAGE_URL",
            "CURRENCY", "VAT_RATE", "DISCOUNT_PRICE",
            "PRODUCT_ID", "QUANTITY", "DELAY",
            "CAN_BUY", "PRICE", "WEIGHT")
    );
    while ($arItems = $dbBasketItems->Fetch()) {
        if($arItems["DELAY"]=="Y" && $arItems["CAN_BUY"] == "Y")
        {
            $arResult["DELAY"][] = $arItems;
        } else {
            //deb($arItems);
            //if ($arItems["DELAY"] == "N" && $arItems["CAN_BUY"] == "Y") {
            $arResult["SUM"] = $arResult["SUM"] + ($arItems["PRICE"]*$arItems["QUANTITY"]);
            $num_products += $arItems["QUANTITY"];
            //}
            $arItems["PRICE_VAT_VALUE"] = (($arItems["PRICE"] / ($arItems["VAT_RATE"] +1)) * $arItems["VAT_RATE"]);
            $arItems["PRICE_FORMATED"] = SaleFormatCurrency($arItems["PRICE"], $arItems["CURRENCY"]);

            if(DoubleVal($arItems["DISCOUNT_PRICE"]) > 0)
            {
                $arItems["DISCOUNT_PRICE_PERCENT"] = $arItems["DISCOUNT_PRICE"]*100 / ($arItems["DISCOUNT_PRICE"] + $arItems["PRICE"]);
                $arItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arItems["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
                $DISCOUNT_PRICE_ALL += $arItems["DISCOUNT_PRICE"] * $arItems["QUANTITY"];
                $arItems["FULL_PRICE"] = $arItems["DISCOUNT_PRICE"] + $arItems["PRICE"];
                $arItems["FULL_PRICE_FORMATED"] = SaleFormatCurrency($arItems["FULL_PRICE"], $arItems["CURRENCY"]);

            }

            $productIDS[] = $arItems["PRODUCT_ID"];
            $arBasketItems[] = $arItems;
        }
    }
}

$arResult["NUM_PRODUCTS"] = $num_products;

$baseCurrency = CCurrency::GetBaseCurrency();
$arResult["CURRENCY"] = getCurrencyAbbr($baseCurrency);
$arResult["SUM"] = number_format($arResult["SUM"], 0, ".", " ");

$arResult["ITEMS"]["AnDelCanBuy"] = $arBasketItems;

//deb($productIDS);
if (count($productIDS) > 0) {
    $dbAddProps = CIBlockElement::GetList(
        array(), array("ID" => $productIDS, "IBLOCK_ID"=>$arParams["OFFERS_IBLOCK_ID"]), false, false,
        array("ID", "PROPERTY_CML2_LINK", "PROPERTY_STD_SIZE", "PROPERTY_COLOR")
    );
    //deb("rrrrrrr");

    $arResult["PROPS"] = array();

    $colorIDS = array();
    $sizesIDS = array();

    while ($arAddProps = $dbAddProps->Fetch()) {
       // deb($arAddProps);

        $sizesIDS[] = $arAddProps["PROPERTY_STD_SIZE_VALUE"];
        $arResult["PROPS"][$arAddProps["ID"]]["SIZE"] = $arAddProps["PROPERTY_STD_SIZE_VALUE"];

        $colorIDS[] = $arAddProps["PROPERTY_COLOR_VALUE"];
        $arResult["PROPS"][$arAddProps["ID"]]["COLOR"] = $arAddProps["PROPERTY_COLOR_VALUE"];
        //$arResult["ITEMS"]["AnDelCanBuy"][$key]["SIZE"] = $arRes["STD_SIZE"]["VALUE"];

        $arResult["PROPS"][$arAddProps["ID"]]["CML2_LINK"] = $arAddProps["PROPERTY_CML2_LINK_VALUE"];

    }

    //deb($arResult["PROPS"]);
    // находим размеры
    $arResult['SIZES'] = array();
    $arSelect = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID' );
    $arFilter = array("ID" => $sizesIDS);
    $rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
    $count = $rsElement->SelectedRowsCount();
    while($data = $rsElement -> Fetch())
    {
        $arResult['SIZES'][$data["ID"]] = $data["NAME"];
    }
    //deb($arResult['SIZES']);
    // находим цвета
    $arResult['COLORS'] = array();
    $arSelect = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID', 'PREVIEW_PICTURE' );
    $arFilter = array("ID" => $colorIDS);
    $rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
    //$count = $rsElement->SelectedRowsCount();
    while($data = $rsElement -> Fetch())
    {
        $arResult['COLORS'][$data["ID"]]["PIC"] = $data["PREVIEW_PICTURE"];
        $arResult['COLORS'][$data["ID"]]["NAME"] = $data["NAME"];
    }
//deb($arResult['COLORS']);

}

// Печатаем массив, содержащий актуальную на текущий момент корзину
//deb($arBasketItems);
//echo "</pre>";


$this->IncludeComponentTemplate();
?>