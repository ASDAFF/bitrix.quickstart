<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 23.07.13
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 */

class Novagroup_Classes_General_CatalogPrice extends Novagroup_Classes_Abstract_CatalogPrice {

    protected $getPricesWithDiscount = true, $offersArray;
    static $offersIBlockID = null;

    function setPricesWithDiscount($flag)
    {
        $this->getPricesWithDiscount = (bool)$flag;
    }

    function getOffersIblockID()
    {
        if(self::$offersIBlockID==null)
        {
            self::$offersIBlockID = CIBlockPriceTools::GetOffersIBlock($this->catalogID);
        }
        return self::$offersIBlockID;
    }

    function getAllPricesWithDiscount()
    {
        $elementID = (is_array($this->elementID)) ? $this->elementID : array($this->elementID);
        return $this->offersArray = CIBlockPriceTools::GetOffersArray(
            $this->catalogID
            , $elementID
            , array("ID" => "DESC")
            , array("NAME", "PROPERTY_STD_SIZE.ID", "PROPERTY_COLOR.ID", "PROPERTY_COLOR.SORT", "PROPERTY_COLOR.PREVIEW_PICTURE","PROPERTY_COLOR.NAME")
            , array()
            , 0
            , $this->getCatalogPrices()
            , 1
            , array()
        );
    }

    function getLastOffersResult()
    {
        return $this->offersArray;
    }

    function getPriceByColor($colorID)
    {
        $prices = array();
        //$getBase = $this->getBaseGroup();
        //$BASE = $getBase[0];

        if ($this->priceCode != false) {
            $BASE = $this->priceCode;
        } else {
            $getBase = $this->getBaseGroup();
            $BASE = $getBase[0];
        }

        $offersArray = (count($this->getLastOffersResult())>0) ? $this->getLastOffersResult() : $this->getAllPrices();
        foreach($offersArray as $offer)
        {
            if($colorID == $offer['PROPERTY_COLOR_ID'])
            {
                $prices['discount'][] = $offer['PRICES'][$BASE]['DISCOUNT_VALUE_VAT'];
                $prices['old'][] = $offer['PRICES'][$BASE]['VALUE_VAT'];
                $prices['print_old'][$offer['PRICES'][$BASE]['VALUE_VAT']] = $offer['PRICES'][$BASE]['PRINT_VALUE_VAT'];
                $prices['print_discount'][$offer['PRICES'][$BASE]['DISCOUNT_VALUE_VAT']] = $offer['PRICES'][$BASE]['PRINT_DISCOUNT_VALUE_VAT'];
            }
        }
        $prices['discount_max'] = max($prices['discount']);
        //$prices['discount'] = min($prices['discount']);
        $prices['discount'] = $this->minInPriceArr($prices['discount']);
        $prices['old'] = min($prices['old']);

        $prices['FROM'] = ($prices['discount'] <> $prices['discount_max']) ? "от " : null;
        if($prices['discount'] == $prices['old'])
        {
            $prices['PRINT_OLD_PRICE'] = null;
        } else {
            $prices['PRINT_OLD_PRICE'] = $prices['print_old'][$prices['old']];
        }
        $prices['PRINT_PRICE'] = $prices['print_discount'][$prices['discount']];
        return $prices;
    }

    function minInPriceArr($array) {

        $firstElemFound = false;
        for ($i = 0; $i <= count($array); $i++)
        {
            if ($array[$i] > 0 && $firstElemFound == false) {
                $min = $array[$i];
                $firstElemFound = true;
            }
            if ($array[$i] > 0 && $firstElemFound == true && $array[$i] < $min) {
                $min = $array[$i];
            }
        }
        if ($firstElemFound == false) $min=0;
        return $min;
    }

    function getAllPricesWithoutDiscount()
    {
        $items = array();
        $arOffersIBlock = $this->getOffersIblockID();
        //$getBase = $this->getBaseGroup();
        //$BASE = $getBase[0];
        if ($this->priceCode != false) {
            $BASE = $this->priceCode;
        } else {
            $getBase = $this->getBaseGroup();
            $BASE = $getBase[0];
        }

        if($arOffersIBlock)
        {
            $arFilter = array(
                "IBLOCK_ID" => $arOffersIBlock["OFFERS_IBLOCK_ID"],
                "PROPERTY_".$arOffersIBlock["OFFERS_PROPERTY_ID"] => $this->elementID,
                "ACTIVE" => "Y",
                "ACTIVE_DATE" => "Y",
                ">CATALOG_QUANTITY" => 0,
                '>PROPERTY_COLOR.ID' => 0
            );
            $rsOffers = CIBlockElement::GetList(array(), $arFilter);
            while($item = $rsOffers->Fetch())
            {
                $db_res = CPrice::GetList(
                    array(),
                    array(
                        "PRODUCT_ID" => $item['ID'],
                    )
                );
                if ($ar_res = $db_res->Fetch()) {
                    $item['PRICES'][$BASE]['VALUE_NOVAT'] = $item['PRICES'][$BASE]['DISCOUNT_VALUE_NOVAT'] = $ar_res['PRICE'];
                    $item['PRICES'][$BASE]['PRINT_VALUE_NOVAT'] = $item['PRICES'][$BASE]['PRINT_DISCOUNT_VALUE_NOVAT'] = CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
                    $items[] = $item;
                }
            }
        }
        return $this->offersArray = $items;
    }

    function getAllPrices()
    {
        return ($this->getPricesWithDiscount === true) ? $this->getAllPricesWithDiscount() : $this->getAllPricesWithoutDiscount();
    }

    function getPrice()
    {
        $arPrices = $arDiscountPrices = $arCurrency = $offerPrice = array();

        if ($this->priceID != false) {
            $pr = CCatalogGroup::GetByID($this->priceID);
            $priceCode = $pr["NAME"];
            if ($this->priceCode == false) $this->priceCode = $priceCode;
        } else {
            $getBase = $this->getBaseGroup();
            $priceCode =  $getBase[0];
        }

        //$BASE = $getBase[0];
        $BASE = $priceCode;

        if(is_array($arOffers = $this->getAllPrices()))
        {
            foreach ($arOffers as $key => $arOffer)
            {
                if ($arOffer["CATALOG_QUANTITY"] > 0) {
                    $arPrices[$arOffer["PRICES"][$BASE]["VALUE_NOVAT"]] = $arOffer["PRICES"][$BASE]["VALUE_NOVAT"];
                    $arDiscountPrices[$arOffer["PRICES"][$BASE]["DISCOUNT_VALUE_NOVAT"]] = $arOffer["PRICES"][$BASE]["DISCOUNT_VALUE_NOVAT"];
                    $arCurrency[$arOffer["PRICES"][$BASE]["VALUE_NOVAT"]] = $arOffer["CATALOG_CURRENCY_".$this->priceID];
                    $arOfferByPrice[$arOffer["PRICES"][$BASE]["VALUE_NOVAT"]] = $arOffer;
                    $arOfferByPrice[$arOffer["PRICES"][$BASE]["DISCOUNT_VALUE_NOVAT"]] = $arOffer;
                }
            }
        }
        if (empty($arPrices)) return null;

        $minPrice = min($arDiscountPrices);
        $oldPrice = min($arPrices);
        $currencyPrice = $arCurrency[$oldPrice];
        $offerPrice = $arOfferByPrice[$oldPrice];

        $data = array();
        $data['PRICE_NAME'] = $offerPrice['CATALOG_GROUP_NAME_'.$this->priceID];
        $data['CURRENCY_DISPLAY'] = getCurrencyAbbr($currencyPrice);
        $data['CURRENCY'] = $currencyPrice;
        $data['FROM'] = (count($arPrices)>1 || count($arDiscountPrices)>1) ? "от " : null ;
        $data['~PRICE'] = $minPrice;
        $data['PRICE'] = number_format($minPrice, 0, ".", " ");
        $data['~OLD_PRICE'] = ($minPrice<>$oldPrice) ? $oldPrice : null;
        $data['OLD_PRICE'] = ($minPrice<>$oldPrice) ? number_format($oldPrice, 0, ".", " ") : null;
        //$data['PRINT_PRICE'] = $arOfferByPrice[$minPrice]["PRICES"][$BASE]["PRINT_DISCOUNT_VALUE_NOVAT"];
        $data['PRINT_PRICE'] = $arOfferByPrice[$minPrice]["PRICES"][$BASE]["PRINT_DISCOUNT_VALUE"];

        $data['PRINT_OLD_PRICE'] = ($minPrice<>$oldPrice) ? $arOfferByPrice[$oldPrice]["PRICES"][$BASE]["PRINT_VALUE_VAT"] : null;
        return $data;
    }
}
