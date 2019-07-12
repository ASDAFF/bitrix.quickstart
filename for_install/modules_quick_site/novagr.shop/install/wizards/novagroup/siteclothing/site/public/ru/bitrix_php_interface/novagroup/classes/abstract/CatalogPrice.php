<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 23.07.13
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_CatalogPrice extends Novagroup_Classes_Abstract_Catalog
{
    protected $elementID, $catalogID, $getPricesWithDiscount = true, $offersArray;
    static $baseGroup = null, $catalogPrices = null, $allPrices = null, $offersIBlockID = null, $optGroup = null, $optUserGroup = null;
    public $priceID = 1; // id price type
    public $priceCode = false;
    public $offersIB = false;

    function __construct($elementID, $catalogID, $priceID = false, $offersIB = false)
    {
        $this->elementID = $elementID;
        $this->catalogID = (int)$catalogID;
        $this->checkInstalledModule();
        if ($priceID != false) $this->priceID = $priceID;
        if ($offersIB != false) $this->offersIB = $offersIB;
    }

    function getBaseGroup()
    {
        $basePrice = self::__getBaseGroup();
        return $basePrice["NAME"];
    }

    function __getBaseGroup()
    {
        if(is_array(self::$baseGroup))
        {
            return self::$baseGroup;
        } else {
            self::$baseGroup = CCatalogGroup::GetBaseGroup();
            return self::$baseGroup;
        }
    }

    function getOptGroup()
    {
        if(is_array(self::$optGroup))
        {
            return self::$optGroup;
        } else {
            $catalogGroup = new CCatalogGroup();
            $getList = $catalogGroup->GetList(array(),array("NAME"=>"opt"));
            if($item =  $getList->Fetch())
            {
                self::$optGroup = $item;
                return self::$optGroup;
            }
            return false;
        }
    }

    function getOptUserGroup()
    {
        if(is_array(self::$optUserGroup))
        {
            return self::$optUserGroup;
        } else {
            $rsGroups = CGroup::GetList ($by = "c_sort", $order = "asc", Array ("STRING_ID" => "opt"));
            self::$optUserGroup = $rsGroups->Fetch();
            return self::$optUserGroup;
        }
    }

    function getCatalogPrices()
    {
        if(is_array(self::$catalogPrices))
        {
            return self::$catalogPrices;
        } else {

            if ($this->priceCode == false) {
                $needPrice = $this->getBaseGroup();
            } else {
                $needPrice = array($this->priceCode);
            }

            self::$catalogPrices = CIBlockPriceTools::GetCatalogPrices($this->catalogID, $needPrice);
            return self::$catalogPrices;
        }
    }
}