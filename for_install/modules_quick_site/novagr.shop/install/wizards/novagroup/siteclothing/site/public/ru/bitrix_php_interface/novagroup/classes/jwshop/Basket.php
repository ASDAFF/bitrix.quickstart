<?php

class Novagroup_Classes_General_Basket extends  Novagroup_Classes_Abstract_Basket {

    // возвращает размеры и цвета которые были в заказах
    static public function getBasketSizesColors($userID, $iblockID) {
        if (CModule::IncludeModule("sale"))
        {
            $basketProductsIDS = array();
            $ordersProductsIDS = array();
            // get products in basket
            $basketFilter = array(
                "ORDER_ID" => "NULL", "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID
            );

            $dbBasket = CSaleBasket::GetList(
                array("NAME" => "ASC"),
                $basketFilter, false,
                array('nTopCount' => 50)

            );
            while ($arBasket = $dbBasket->Fetch())
            {
                $basketProductsIDS[] = $arBasket["PRODUCT_ID"];
            }

            // get products in orders
            $arFilter = Array("USER_ID" => $userID, "LID" => SITE_ID);
            $dbSales = CSaleOrder::GetList(
                array("DATE_INSERT" => "DESC"),
                $arFilter,
                false,
                array('nTopCount' => 50),
                array("ID")
            );

            $ordersIDS = array();
            while ($arSales = $dbSales->Fetch())
            {
                $ordersIDS[] = $arSales["ID"];
            }

            if (count($ordersIDS)) {
                $basketOrdersFilter = array("ORDER_ID" => $ordersIDS);
                //$basketOrdersFilter = array("ORDER_ID" => $userID, "LID" => SITE_ID);
                $dbBasket = CSaleBasket::GetList(
                     array("NAME" => "ASC"),
                    $basketOrdersFilter
                );
                while ($arBasket = $dbBasket->Fetch())
                {
                    if (!in_array($arBasket["PRODUCT_ID"], $basketProductsIDS))
                        $ordersProductsIDS[] = $arBasket["PRODUCT_ID"];
                }
            }

            $basketIDS = array_merge($basketProductsIDS, $ordersProductsIDS);
            //$basketIDS = array_merge($basketProductsIDS);
            if (count($basketIDS) > 0) {
                $arResult = array();
                $arFilter = array('IBLOCK_ID' => $iblockID, "ID" => $basketIDS);
                $arSelect = array( 'ID', 'NAME', 'PROPERTY_STD_SIZE', 'PROPERTY_COLOR_STONE' );

                $rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
                while ($data = $rsElement -> Fetch())
                {
                    if (in_array($data["ID"], $basketProductsIDS)) {
                        // products in basket in priority
                        if (!empty($data["PROPERTY_STD_SIZE_VALUE"])) {
                            $arResult["SIZES"]["BASKET"][$data["PROPERTY_STD_SIZE_VALUE"]] = $arResult["SIZES"]["BASKET"][$data["PROPERTY_STD_SIZE_VALUE"]]+1;
                        }
                        if (!empty($data["PROPERTY_COLOR_STONE_VALUE"])) {
                            $arResult["COLORS"]["BASKET"][$data["PROPERTY_COLOR_STONE_VALUE"]] = $arResult["COLORS"]["BASKET"][$data["PROPERTY_COLOR_STONE_VALUE"]]+1;
                        }

                    } else {

                        if (!empty($data["PROPERTY_STD_SIZE_VALUE"])) {
                            $arResult["SIZES"]["ORDERS"][$data["PROPERTY_STD_SIZE_VALUE"]] = $arResult["SIZES"]["ORDERS"][$data["PROPERTY_STD_SIZE_VALUE"]]+1;
                        }
                        if (!empty($data["PROPERTY_COLOR_STONE_VALUE"])) {
                            $arResult["COLORS"]["ORDERS"][$data["PROPERTY_COLOR_STONE_VALUE"]] = $arResult["COLORS"]["ORDERS"][$data["PROPERTY_COLOR_STONE_VALUE"]]+1;
                        }
                    }
                }
                return $arResult;
            }
        }
        return false;
    }

}