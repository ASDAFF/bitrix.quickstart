<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>

<?
if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock")) {

    $productID = htmlspecialchars($_REQUEST["ID"]);
    $productPrice = htmlspecialchars($_REQUEST["PRICE"]);

    if ($_REQUEST['action'] == 'DELAY_ADD') {
        $res = CIBlockElement::GetList(Array(), array("ID" => $productID), false, Array(), array("NAME"));
        if ($ob = $res->Fetch()){
            $name = $ob['NAME'];
        }

//        $ar_res = CPrice::GetBasePrice($productID);

        $arFields = array(
            "PRODUCT_ID" => $productID,
            "PRICE" => $productPrice,
            "CURRENCY" => 'RUB',
            "QUANTITY" => 1,
            "LID" => LANG,
            "DELAY" => "Y",
            "NAME" => $name,
            "PRODUCT_PRICE_ID" => "1",
        );

        CSaleBasket::Add($arFields);

        $result = array(
            'STATUS' => 'OK'
        );
        echo CUtil::PhpToJSObject($result, false, true);
    }

    if ($_REQUEST['action'] == 'DELAY_DELETE') {
        $arFilter = array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL",
            "DELAY" => "Y",
            "PRODUCT_ID" => $productID,
        );

        $dbBasketItems = CSaleBasket::GetList(array(), $arFilter, false, false, array("ID"));
        if ($arItems = $dbBasketItems->Fetch()) {
            CSaleBasket::Delete($arItems['ID']);
        }
    }

    $cntDelay = array();
    $dbBasketItems = CSaleBasket::GetList(array(), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", "DELAY" => "Y"), false, false, array("ID"));
    while ($arItems = $dbBasketItems->Fetch()) {
        $cntDelay[] = $arItems;
    }
}
?>