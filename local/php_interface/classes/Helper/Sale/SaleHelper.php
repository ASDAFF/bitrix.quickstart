<?php
/**
 * Copyright (c) 10/2/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper\Sale;

CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

class SaleHelper
{
    /**
     * Delete all products from current user's basket
     * @return boolean
     */
    public static function resetBasket()
    {
        CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
        return true;
    }

    /**
     * Transfers basket from one user to another one
     *
     * @param integer $from
     * @param integer $to
     * @global object $DB
     * @return boolean
     */
    public static function transferBasket($from, $to)
    {
        global $DB;
        $from = intval($from);
        $to   = intval($to);
        CModule::IncludeModule('sale');

        if (($to>0) && (CSaleUser::GetList(array("ID"=>$to)))) {
            $deleteQuery =
                "DELETE FROM b_sale_basket WHERE FUSER_ID = ".$to." ";
            $updateQuery =
                "UPDATE b_sale_basket SET ".
                "    FUSER_ID = ".$to." ".
                "WHERE FUSER_ID = ".$from." ";
            $DB->Query($deleteQuery, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $DB->Query($updateQuery, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            return true;
        }
        return false;
    }

    /**
     * Get quantity of basket product's articuls
     *
     * @return integer
     */
    public static function GetBasketProductsQuantity()
    {
        CModule::IncludeModule('sale');
        $i = 0;

        $rsBasketProducts = CSaleBasket::GetList(
            array(),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SS_SHOP_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID")
        );
        while ($arItem = $rsBasketProducts->GetNext()) {
            $i++;
        }

        return $i;
    }

    /**
     * Checks product is in basket of current user and returns it's id
     *
     * @param integer $productID
     * @return boolean
     */
    public static function isProductInBasket($productID)
    {
        global $DB;
        CModule::IncludeModule('sale');

        $id      = intval($productID);
        $fuserID = CSaleBasket::GetBasketUserID();

        $strSql =
            "SELECT ID ".
            "FROM b_sale_basket ".
            "WHERE PRODUCT_ID = " . $id . " AND FUSER_ID = " . $fuserID . " AND ORDER_ID IS NULL" . "";
        $rsBasket = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

        if ($arBasket = $rsBasket->Fetch())
            return $arBasket["ID"] ? $arBasket["ID"] : false;
    }

    /**
     * Returns quantity of product in current user's basket
     *
     * @param integer $productID
     * @return float
     */
    public static function getProductInBasketParams($productID)
    {
        global $DB;
        CModule::IncludeModule('sale');

        $id      = intval($productID);
        $fuserID = CSaleBasket::GetBasketUserID();

        $strSql =
            "SELECT ID, QUANTITY ".
            "FROM b_sale_basket ".
            "WHERE PRODUCT_ID = " . $id . " AND FUSER_ID = " . $fuserID . " AND ORDER_ID IS NULL";
        $rsBasket = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

        if ($arBasket = $rsBasket->Fetch())
            return array(
                "ID"       => $arBasket["ID"],
                "QUANTITY" => floatval($arBasket["QUANTITY"])
            );

        return false;
    }
}