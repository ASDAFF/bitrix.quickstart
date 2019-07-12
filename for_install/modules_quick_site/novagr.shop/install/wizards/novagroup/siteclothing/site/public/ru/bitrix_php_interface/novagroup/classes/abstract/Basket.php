<?php

abstract class Novagroup_Classes_Abstract_Basket
{
    function __construct()
    {
        $this->checkInstalledModule();
    }

    function checkInstalledModule()
    {
        if( !CModule::IncludeModule("sale") ) die("sale module is not installed");
    }

    function getOrderPropertyByCode($code)
    {
        $GetList = CSaleOrderProps::GetList(
            array(),
            array('CODE'=>$code)
        );
        $arOrderProps = array();
        while ($arProp = $GetList->Fetch())
        {
            $arOrderProps[] = $arProp;
        }
        return $arOrderProps;
    }

    function getDeliveryByName($name)
    {
        $GetList = $this->getDelivery();
        $GetListByName = array();
        foreach($GetList as $item)
        {
            if($item['NAME']==$name) $GetListByName[] = $item;
        }
        return $GetListByName;
    }

    function getDelivery()
    {
        $GetList = CSaleDelivery::GetList(
            array(),
            array("LID"=>SITE_ID,"ACTIVE"=>"Y")
        );
        $arDelivery = array();
        while ($arProp = $GetList->Fetch())
        {
            $arDelivery[] = $arProp;
        }
        return $arDelivery;
    }

    function checkCurrentDeliveryByName($name,$request)
    {
        $GetList = $this->getDelivery();
        if(isset($request['DELIVERY_ID']) and $request['DELIVERY_ID']>0)
        {
            foreach($GetList as $item)
            {
                if($item['ID']==$request['DELIVERY_ID'] and $item['NAME']==$name) return true;
            }
        }
        if(isset($GetList[0]['NAME']) and $GetList[0]['NAME']==$name) return true;
        return false;
    }

    // заполняет инфу о цветах и размерах в заказах в польз. поле UF_SIZES_COLORS
    static public function updateSizesColorsUserField($userID, $iblockID) {

        $arSizesColors = Novagroup_Classes_General_Basket::getBasketSizesColors($userID, $iblockID);
        $arSizesColors = serialize($arSizesColors);
        $user = new CUser;
        $fields = Array("UF_SIZES_COLORS" => $arSizesColors);
        $user->Update($userID, $fields);
        // записываем в куку на 14 дней
        global $APPLICATION;
        $APPLICATION->set_cookie("SIZES_COLORS", $arSizesColors, time()+60*60*24*14, "/");
    }


    static public function makeDetailLink($params = array()) {

        if ($params["sourceURL"] == "#PRODUCT_URL#") {

            $params["sourceURL"] = self::getLinkToProduct($params["productID"]);
        }

        $result = $params["sourceURL"];
        if (!empty($params["sizeID"])) {
            $result .= "?cs=".$params["sizeID"];
            if (!empty($params["colorID"]) && !empty($params["productID"])) {
                $result .= "&#color-".$params["colorID"]."-".$params["productID"];
            }
        }
        return $result;
    }

    function getLinkToProduct($productID) {

        $obCache = new CPHPCache();
        $cacheLifetime = 86400*7;
        $cacheID = $productID;
        $cachePath = '/productUrls';
        if ( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) )
        {
            $vars = $obCache->GetVars();
            $result = $vars["productUrl".$productID];

        }
        elseif ( $obCache->StartDataCache()  )
        {
            $result = "/catalog/";

            $arSelect = array( 'ID', 'NAME', 'CODE', 'IBLOCK_ID', 'IBLOCK_SECTION_ID' );
            $arFilter = array("ID" => $productID);
            $rsElement = CIBlockElement::GetList(
                Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect
            );

            if ($data = $rsElement -> Fetch())
            {
                if (!empty($data["IBLOCK_SECTION_ID"])) {
                    $arSelect = array( 'ID', 'NAME', 'CODE', 'IBLOCK_ID' );
                    $arFilter = array("ID" => $data["IBLOCK_SECTION_ID"]);
                    $rsSection = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, $arSelect);
                    if ($data2 = $rsSection -> Fetch())
                    {
                        $result = "/catalog/".$data2["CODE"]."/".$data["CODE"]."/";
                    }
                }
            }

            $obCache->EndDataCache(array('productUrl'.$productID => $result));
        }

        return $result;
    }

}