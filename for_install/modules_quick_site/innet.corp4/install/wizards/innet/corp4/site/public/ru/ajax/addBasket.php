<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("iblock")) {
    if (isset($_REQUEST["id"])) {
        $id = htmlspecialchars($_REQUEST["id"]);
        $quantity = $_REQUEST["quantity"] ? $_REQUEST["quantity"] : 1;

        $res = CIBlockElement::GetList(Array(), Array("ID" => $id), false, Array());
        if ($ob = $res->GetNextElement()) {
//            $arFields = $ob->GetFields();
            $GetProperties = $ob->GetProperties();

            foreach ($GetProperties as $keyp => $prop) {
                if (!empty($prop['VALUE'])) {
                    if ($prop['CODE'] == 'COLOR_REF' || $prop['CODE'] == 'SIZES_SHOES' || $prop['CODE'] == 'SIZES_CLOTHES') {
                        $arFields[] = array(
                            "NAME" => $prop["NAME"],
                            "CODE" => $prop["CODE"],
                            "VALUE" => $prop['VALUE'],
                        );
                    }
                }
            }

            $baskID = Add2BasketByProductID($id, $quantity, $arFields);
        }


        /*$arBasketItems = array();
        $quantityBasket = '';
        $sumBasket = '';

        $dbBasketItems = CSaleBasket::GetList(
            array("NAME" => "ASC", "ID" => "ASC"),
            array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
            false,
            false,
            array("ID", "MODULE", "PRODUCT_ID", "QUANTITY", "CAN_BUY", "PRICE")
        );
        while ($arItems = $dbBasketItems->Fetch()) {
            $arItems = CSaleBasket::GetByID($arItems["ID"]);
            $arBasketItems[] = $arItems;
            $quantityBasket += $arItems['QUANTITY'];
            $sumBasket += $arItems['PRICE'] * $arItems['QUANTITY'];
        }*/
        $cntBasketItems = CSaleBasket::GetList(
            array(),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            array()
        );

        $arResult = array(
            'QUANTITY_BASKET' => $cntBasketItems,
//            'SUMM_BASKET' => $sumBasket,
        );

        echo json_encode($arResult);
    }

    //$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "header", Array(), false);
}
?>