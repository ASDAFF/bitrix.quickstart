<?php

abstract class Novagroup_Classes_Abstract_Fashion extends Novagroup_Classes_Abstract_IBlock
{

    protected $catalogPath = "#SITE_DIR#imageries/", $catalogName = "Образы", $catalogIBlockCode = "imageries";
    protected $fashionID;
    protected $arSelect = array(
        'ID',
        'NAME',
        'IBLOCK_ID',
        'CODE',
        'PROPERTY_PHOTOS',
        'PROPERTY_EVENT.NAME',
        'PROPERTY_SEAZON.NAME',
        'PROPERTY_PRODUCTS',
        'PROPERTY_PRODUCTS.IBLOCK_ID',
        'PREVIEW_TEXT',
        'DETAIL_TEXT'
    );
    protected $priceCode = false;
    protected $priceID = 1; // id price type

    function __construct($fashionID=0, $priceID = false)
    {
        $this->fashionID = $fashionID;
        if ($priceID != false) $this->priceID = $priceID;
    }

    function getCatalogPath()
    {
        return str_replace('#SITE_DIR#',SITE_DIR,$this->catalogPath);
    }

    function getCatalogName()
    {
        return $this->catalogName;
    }

    function getCatalogIBlockCode()
    {
        return $this->catalogIBlockCode;
    }

    function setPageProperties()
    {
        Novagroup_Classes_General_Main::setTitle($this->getCatalogName());
    }

    function addChainItems()
    {
        Novagroup_Classes_General_Main::AddChainItem($this->getCatalogName(), $this->getCatalogPath());
    }

    function getElement($ID)
    {
        $arFilter['IBLOCK_ID'] = (int)$this->fashionID;
        $arFilter['ID'] = (int)$ID;
        return parent::getElement(array(), $arFilter, false , false, $this->arSelect);
    }

    function getPriceByElement($ID)
    {
       // deb($this->priceID);
        $image = $this->getElement($ID);
        $minPrice = $oldPrice = $arCurrency = $from = array();
        if (is_array($image['PROPERTY_PRODUCTS_VALUE']))
        {
            foreach ($image['PROPERTY_PRODUCTS_VALUE'] as $product)
            {
                $CatalogPrices = new Novagroup_Classes_General_CatalogPrice($product,$image['PROPERTY_PRODUCTS_IBLOCK_ID'], $this->priceID);
                $prices = $CatalogPrices->getPrice();
                $minPrice[] = $prices['~PRICE'];
                $oldPrice[] = (isset($prices['~OLD_PRICE'])) ? $prices['~OLD_PRICE'] : $prices['~PRICE'];
                $arCurrency = $prices["CURRENCY"];
                if(isset($prices['FROM'])) $from[] = $prices['FROM'];
            }
        }

        if (empty($minPrice)) return null;

        $minPrice = array_sum($minPrice);
        $oldPrice = array_sum($oldPrice);
        $currencyPrice = $arCurrency;

        $data = array();
        $data['CURRENCY_DISPLAY'] = getCurrencyAbbr($currencyPrice);
        $data['FROM'] = (count($from)>0) ? "от " : null ;
        $data['~PRICE'] = $minPrice;
        $data['PRICE'] = number_format($minPrice, 0, ".", " ");
        $data['OLD_PRICE'] = ($minPrice<>$oldPrice) ? number_format($oldPrice, 0, ".", " ") : null;
        $data['PRINT_PRICE'] = FormatCurrency($minPrice, $currencyPrice );
        $data['PRINT_OLD_PRICE'] = ($minPrice<>$oldPrice) ? FormatCurrency($oldPrice, $currencyPrice ) : null;
        return $data;
    }

}