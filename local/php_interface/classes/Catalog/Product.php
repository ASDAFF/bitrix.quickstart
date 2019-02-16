<?php
/**
 * User: graymur
 * Date: 01.10.13
 * Time: 16:38
 */

namespace Catalog;

class Product extends \Block\ObjectBlock
{
    private $price = null;

    function getOffers()
    {
        if (!class_exists('\CCatalogSKU'))
        {
            \CModule::IncludeModule('catalog');
        }
        
        $retval = array();

        $arInfo = \CCatalogSKU::GetInfoByProductIBlock($this->iblock_id);

        if (is_array($arInfo))
        {
            $retval = \Block\Getter::instance()->setFilter(array(
                    'IBLOCK_ID' => $arInfo['IBLOCK_ID'],
                    'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $this->id
            ))
                ->setClassName('\Catalog\Offer')
                ->get();
        }

        return empty_array($retval) ? false : $retval;
    }

    function getBasketId()
    {
        return $this->id;
    }

    function getOfferBasketId()
    {
        $offers = $this->getOffers();
        return $offers ? $offers[0]->id : $this->id;
    }

    public function getOldPrice()
    {
        $priceObj = $this->getPriceObj();
        return $priceObj['PRICE']['PRICE'];
    }

    public function getCurrency()
    {
        $priceObj = $this->getPriceObj();
        return $priceObj['PRICE']['CURRENCY'];
    }

    public function getPrice()
    {
        $priceObj = $this->getPriceObj();
        return $priceObj['DISCOUNT_PRICE'];
    }

    public function getDiscountPrice()
    {
        return $this->getPrice();
    }

    public function hasDiscount()
    {
        $priceObj = $this->getPriceObj();
        return $priceObj['PRICE']['PRICE'] > $priceObj['DISCOUNT_PRICE'];
    }

    public function getDiscountPercent()
    {
        return $this->hasDiscount() ? 100 - round(100 * $this->getPrice() / $this->getOldPrice()) : false;
    }

    public function isForSale()
    {
        $price = $this->getPriceObj();
        return (bool) ($price['DISCOUNT_PRICE'] && $this->getQuantity());
    }

    public function getQuantity()
    {
        if($this->CATALOG_QUANTITY === null)
        {
            $this->fillCatalogData();
        }
        return $this->CATALOG_QUANTITY;
    }

    protected function getPriceObj()
    {
        if($this->price === null)
        {
            $this->price = \CCatalogProduct::GetOptimalPrice($this->ID);
//            echo '<pre>'.print_r($this->price,true).'</pre>';
        }

        return $this->price;
    }

    protected function fillCatalogData()
    {
        static $result = array();
        if(!isset($result[$this->ID]))
        {
            $ar = \Block\Getter::instance()
                ->setSelectFields(array('ID', 'CATALOG_QUANTITY'))
                ->getArrayById($this->ID);
            foreach($ar as $key=>$val)
            {
                if($this->data[$key] === null) $this->data[$key] = $val;
            }
            $result[$this->ID] = true;
        }
    }
}
