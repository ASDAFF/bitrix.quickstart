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

    function getFilterRows()
    {

        if(
            isset($this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE'])
            ||
            isset($this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE'])
            ||
            isset($this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE'])
        )
        {
            $arSpecial[0] ['LOGIC'] = "OR";
        }
        if( isset($this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_SPECIALOFFER_VALUE'] = $this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE'];
            unset($this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE']);
        }
        if( isset($this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_NEWPRODUCT_VALUE'] = $this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE'];
            unset($this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE']);
        }
        if( isset($this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_SALELEADER_VALUE'] = $this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE'];
            unset($this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE']);
        }

        $arFilter = $this->arParams['arFilterRequest'];
        if( !empty($arSpecial) ) $arFilter = array_merge($arSpecial, $arFilter);
        $arFilter['INCLUDE_SUBSECTIONS'] = "Y";
        $arFilter['IBLOCK_ID'] = $this->catalogID;
        $arFilter['SECTION_GLOBAL_ACTIVE'] = "Y";

        $arOfferFilter = $this->getOffersFilter();

        $arFilter['ID'] = parent::SubQuery(
            'PROPERTY_CML2_LINK',
            $arOfferFilter
        );
        return $arFilter;
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

    public static function GetOffersArray($arFilter, $arElementID, $arOrder, $arSelectFields, $arSelectProperties, $limit, $arPrices, $vat_include, $arCurrencyParams = array(), $USER_ID = 0, $LID = SITE_ID, $offersIB)
    {
        $arResult = array();

        $boolCheckPermissions = false;
        $boolHideNotAvailable = false;
        $IBLOCK_ID = 0;
        if (!empty($arFilter) && is_array($arFilter))
        {
            if (isset($arFilter['IBLOCK_ID']))
                $IBLOCK_ID = $arFilter['IBLOCK_ID'];
            if (isset($arFilter['HIDE_NOT_AVAILABLE']))
                $boolHideNotAvailable = 'Y' === $arFilter['HIDE_NOT_AVAILABLE'];
            if (isset($arFilter['CHECK_PERMISSIONS']))
                $boolCheckPermissions = 'Y' === $arFilter['CHECK_PERMISSIONS'];
        }
        else
        {
            $IBLOCK_ID = $arFilter;
        }

        //$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($IBLOCK_ID);

        if($offersIB)
        {
            //$arDefaultMeasure = CIBlockPriceTools::GetDefaultMeasure();

            $limit = intval($limit);
            if (0 > $limit)
                $limit = 0;

            if(!isset($arOrder["ID"]))
                $arOrder["ID"] = "DESC";

            $arFilter = array(
                "IBLOCK_ID" => $offersIB,
                "PROPERTY_CML2_LINK" => $arElementID,
                "ACTIVE" => "Y",
                "ACTIVE_DATE" => "Y",
            );
            if ($boolHideNotAvailable)
                $arFilter['CATALOG_AVAILABLE'] = 'Y';
            if ($boolCheckPermissions)
            {
                $arFilter['CHECK_PERMISSIONS'] = "Y";
                $arFilter['MIN_PERMISSION'] = "R";
            }

            $arSelect = array(
                "ID" => 1,
                "IBLOCK_ID" => 1,
                "PROPERTY_CML2_LINK" => 1,
                "CATALOG_QUANTITY" => 1
            );
            //if(!$arParams["USE_PRICE_COUNT"])
            {
                foreach($arPrices as $value)
                {
                    if (!$value['CAN_VIEW'] && !$value['CAN_BUY'])
                        continue;
                    $arSelect[$value["SELECT"]] = 1;
                }
            }

            foreach($arSelectFields as $code)
                $arSelect[$code] = 1; //mark to select
            if (!isset($arSelect['PREVIEW_PICTURE']))
                $arSelect['PREVIEW_PICTURE'] = 1;
            if (!isset($arSelect['DETAIL_PICTURE']))
                $arSelect['DETAIL_PICTURE'] = 1;

            $boolPropCacheExist = method_exists('CCatalogDiscount', 'SetProductPropertiesCache');
            $arOfferIDs = array();
            $arMeasureMap = array();
            $arKeyMap = array();
            $intKey = 0;
            $arOffersPerElement = array();
            $rsOffers = CIBlockElement::GetList($arOrder, $arFilter, false, false, array_keys($arSelect));
            while($obOffer = $rsOffers->GetNextElement())
            {

                $arOffer = $obOffer->GetFields();
                $arOffer['ID'] = intval($arOffer['ID']);
                $element_id = $arOffer["PROPERTY_CML2_LINK_VALUE"];
                //No more than limit offers per element
                if($limit > 0)
                {
                    $arOffersPerElement[$element_id]++;
                    if($arOffersPerElement[$element_id] > $limit)
                        continue;
                }

                if($element_id > 0)
                {
                    $arOffer["LINK_ELEMENT_ID"] = $element_id;
                    $arOffer["PROPERTIES"] = array();
                    $arOffer["DISPLAY_PROPERTIES"] = array();
/*
                    if(!empty($arSelectProperties))
                    {
                        $arOffer["PROPERTIES"] = $obOffer->GetProperties();
                        if ($boolPropCacheExist)
                            CCatalogDiscount::SetProductPropertiesCache($arOffer['ID'], $arOffer["PROPERTIES"]);
                        foreach($arSelectProperties as $pid)
                        {
                            if (!isset($arOffer["PROPERTIES"][$pid]))
                                continue;
                            $prop = &$arOffer["PROPERTIES"][$pid];
                            $boolArr = is_array($prop["VALUE"]);
                            if(
                                ($boolArr && !empty($prop["VALUE"])) ||
                                (!$boolArr && strlen($prop["VALUE"])>0))
                            {
                                $arOffer["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arOffer, $prop, "catalog_out");
                            }
                        }
                    }*/
                    //$arOffer['CATALOG_MEASURE_NAME'] = $arDefaultMeasure['SYMBOL_RUS'];
                    //$arOffer['~CATALOG_MEASURE_NAME'] = $arDefaultMeasure['SYMBOL_RUS'];
                    $arOffer["CATALOG_MEASURE_RATIO"] = 1;
                    if (!isset($arOffer['CATALOG_MEASURE']))
                        $arOffer['CATALOG_MEASURE'] = 0;
                    $arOffer['CATALOG_MEASURE'] = intval($arOffer['CATALOG_MEASURE']);
                    if (0 > $arOffer['CATALOG_MEASURE'])
                        $arOffer['CATALOG_MEASURE'] = 0;
                    if (0 < $arOffer['CATALOG_MEASURE'])
                    {
                        if (!isset($arMeasureMap[$arOffer['CATALOG_MEASURE']]))
                            $arMeasureMap[$arOffer['CATALOG_MEASURE']] = array();
                        $arMeasureMap[$arOffer['CATALOG_MEASURE']][] = $intKey;
                    }

                    $arOfferIDs[] = $arOffer['ID'];
                    $arKeyMap[$arOffer['ID']] = $intKey;
                    $arResult[$intKey] = $arOffer;
                    $intKey++;
                }
            }

/*
            if (!empty($arKeyMap))
            {
                $rsRatios = CCatalogMeasureRatio::getList(
                    array(),
                    array('@PRODUCT_ID' => array_keys($arKeyMap)),
                    false,
                    false,
                    array('PRODUCT_ID', 'RATIO')
                );
                while ($arRatio = $rsRatios->Fetch())
                {
                    if (isset($arKeyMap[$arRatio['PRODUCT_ID']]))
                    {
                        $intRatio = intval($arRatio['RATIO']);
                        $dblRatio = doubleval($arRatio['RATIO']);
                        $arResult[$arKeyMap[$arRatio['PRODUCT_ID']]]['CATALOG_MEASURE_RATIO'] = ($dblRatio > $intRatio ? $dblRatio : $intRatio);
                    }
                }
            }
*/
            if (!empty($arOfferIDs))
            {
                if (method_exists('CCatalogDiscount', 'SetProductSectionsCache'))
                    CCatalogDiscount::SetProductSectionsCache($arOfferIDs);
                foreach ($arResult as &$arOffer)
                {
                    $arOffer['MIN_PRICE'] = false;

                    $arOffer["PRICES"] = CIBlockPriceTools::GetItemPrices($offersIB, $arPrices, $arOffer, $vat_include, $arCurrencyParams, $USER_ID, $LID);
                    if (!empty($arOffer["PRICES"]))
                    {
                        foreach ($arOffer['PRICES'] as &$arOnePrice)
                        {
                            if ('Y' == $arOnePrice['MIN_PRICE'])
                            {
                                $arOffer['MIN_PRICE'] = $arOnePrice;
                                break;
                            }
                        }
                        unset($arOnePrice);
                    }
                   // $arOffer["CAN_BUY"] = CIBlockPriceTools::CanBuy($offersIB, $arPrices, $arOffer);
                }
                if (isset($arOffer))
                    unset($arOffer);
            }

            /*
            if (!empty($arMeasureMap))
            {
                $rsMeasures = CCatalogMeasure::getList(
                    array(),
                    array('@ID' => array_keys($arMeasureMap)),
                    false,
                    false,
                    array()
                );
                while ($arMeasure = $rsMeasures->GetNext())
                {
                    $arMeasure['ID'] = intval($arMeasure['ID']);
                    if (isset($arMeasureMap[$arMeasure['ID']]) && !empty($arMeasureMap[$arMeasure['ID']]))
                    {
                        foreach ($arMeasureMap[$arMeasure['ID']] as &$intOneKey)
                        {
                            $arResult[$intOneKey]['CATALOG_MEASURE_NAME'] = $arMeasure['SYMBOL_RUS'];
                            $arResult[$intOneKey]['~CATALOG_MEASURE_NAME'] = $arMeasure['~SYMBOL_RUS'];
                        }
                        unset($intOneKey);
                    }
                }
            }*/
        }

        return $arResult;
    }
}