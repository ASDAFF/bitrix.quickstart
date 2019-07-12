<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (CModule::IncludeModule("iblock")) {
} else {
    die(GetMessage("MODULES_NOT_INSTALLED"));
}

global $USER;
$arParams['USER_EMAIL'] = $USER->GetEmail();

if (isset($_POST) and $_POST["CAJAX"] == 1) {
    $arParams['REQUEST'] = $requests = Novagroup_Classes_General_Main::getRequest();
    $orders = new Novagroup_Classes_General_QuickOrder((int)$arParams['ORDER_LIST_IBLOCK_ID'], (int)$arParams['ORDER_PRODUCT_IBLOCK_ID']);

    $itemsCart = array();

    if (CModule::IncludeModule('sale') && CModule::IncludeModule('catalog')) {

        $dbBasketItems = CSaleBasket::GetList(
            array("ID" => "ASC"),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE",
                "PRODUCT_ID", "QUANTITY", "DELAY",
                "CAN_BUY", "PRICE", "WEIGHT")
        );
        while ($arItems = $dbBasketItems->Fetch()) {
            if (strlen($arItems["CALLBACK_FUNC"]) > 0) {
                CSaleBasket::UpdatePrice($arItems["ID"],
                    $arItems["CALLBACK_FUNC"],
                    $arItems["MODULE"],
                    $arItems["PRODUCT_ID"],
                    $arItems["QUANTITY"]);
                $arItems = CSaleBasket::GetByID($arItems["ID"]);
            }
            if ($arItems['DELAY'] == "Y") continue;

            $fields = array();
            $res = CIBlockElement::GetByID($arItems["PRODUCT_ID"]);
            if ($ar_res = $res->GetNextElement()) {
                $GetProperties = $ar_res->GetProperties();
                $fields['sizeId'] = $GetProperties['STD_SIZE']['VALUE'];
                $fields['colorId'] = $GetProperties['COLOR']['VALUE'];
                $fields['productId'] = $GetProperties['CML2_LINK']['VALUE'];

            }
            $fields['quantity'] = $arItems['QUANTITY'];
            $result = $orders->addOrderProduct($fields);
            $itemsCart[$arItems['ID']] = $arItems['ID'];
        }
    }

    $orders->addOrder($requests);
    if ($orders->hasErrors()) {
        $templateName = "error";
        $arResult['ERROR'] = $orders->getErrors();
    } else {
        //clear shop cart
        foreach($itemsCart as $item)
        {
            if($item>0)
            {
                CSaleBasket::Delete($item);
            }
        }
        //set template
        $templateName = "ok";
    }
} else {
    $templateName = null;
}
$this->IncludeComponentTemplate($templateName);