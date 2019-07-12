<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 23.07.13
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 */

class Novagroup_Classes_General_CatalogOffers extends Novagroup_Classes_Abstract_CatalogOffers {

    protected $showQuantityNull = false;

    function __construct($catalogID, $offersID, $showQuantityNull = false)
    {
        $this->showQuantityNull = $showQuantityNull;
        parent::__construct($catalogID, $offersID);
    }

   
    function getOffersFilter()
    {
        $arOfferFilter = $this->arParams['arOfferRequest'];
        $arOfferFilter['IBLOCK_ID'] = $this->offersID;
        $arOfferFilter['>PROPERTY_COLOR.ID'] = 0;
        if ($this->showQuantityNull == false) {
            $arOfferFilter[">CATALOG_QUANTITY"] = 0;
        }
        return $arOfferFilter;
    }
	
}