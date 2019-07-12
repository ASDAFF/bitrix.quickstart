<?php

class Novagroup_Classes_Abstract_Handler
{

    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        $aGlobalMenu["global_menu_novagr"] = array(
            "menu_id" => "novagr",
            "page_icon" => "store_title_icon",
            "index_icon" => "store_page_icon",
            "text" => "TrendyList",
            "title" => "TrendyList",
            "sort" => 1000,
            "items_id" => "global_menu_novagr",
            "help_section" => "novagr",
            "items" => array()
        );
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS('/local/themes/.default/novagroup.css');
    }

    /**
     * событие вызывается только в публичной части сайта,
     * (в административном интерфейсе ничего не вызывается, если это потребуется, то нужно вынести событие в модуль)
     */
    function OnEpilogEventAddHandler()
    {

        // работа с сео только в публичной части сайта
        if (defined("ADMIN_SECTION") || ADMIN_SECTION === true) return;
        global $APPLICATION;

        Novagroup_Classes_General_Search::addChainItems();
        Novagroup_Classes_General_Search::setPageProperties();

        $arrForCache = Novagroup_Classes_General_Main::getSeoArr();

        //if (!empty($arrForCache["seoArr"]['DETAIL_TEXT']))
        //	$APPLICATION->SetPageProperty("seo_text", $arrForCache["seoArr"]['DETAIL_TEXT']);
        if (!empty($arrForCache["seoArr"]['TITLE']))
            $APPLICATION->SetPageProperty("title", $arrForCache["seoArr"]['TITLE']);
        if (!empty($arrForCache["seoArr"]['KEYWORDS']))
            $APPLICATION->SetPageProperty("keywords", $arrForCache["seoArr"]['KEYWORDS']);
        if (!empty($arrForCache["seoArr"]['DESCRIPTION']))
            $APPLICATION->SetPageProperty("description", $arrForCache["seoArr"]['DESCRIPTION']);
    }

    function OnGetDiscountHandler($intProductID, $intIBlockID, $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, $boolSKU, $boolGetIDS)
    {
        $action = new Novagroup_Classes_General_TimeToBuy($intProductID, $intIBlockID);
        if ($action->checkAction()) {
            $getAction = $action->getAction();
            return $arDiscount = array(
                array(
                    'ID' => null,
                    'TYPE' => '0',
                    'SITE_ID' => SITE_ID,
                    'ACTIVE' => 'Y',
                    'ACTIVE_FROM' => null,
                    'ACTIVE_TO' => null,
                    'RENEWAL' => 'N',
                    'NAME' => 'Time To Buy',
                    'SORT' => '100',
                    'MAX_DISCOUNT' => '0.0000',
                    'VALUE_TYPE' => 'P',
                    'VALUE' => number_format($getAction['PROPERTY_DISCOUNT_VALUE'], 4, '.', ''),
                    'CURRENCY' => 'RUB',
                    'PRIORITY' => '1',
                    'LAST_DISCOUNT' => 'Y',
                    'COUPON' => null,
                    'COUPON_ONE_TIME' => null,
                    'COUPON_ACTIVE' => null,
                )
            );
        } else {
            return true;
        }
    }

}