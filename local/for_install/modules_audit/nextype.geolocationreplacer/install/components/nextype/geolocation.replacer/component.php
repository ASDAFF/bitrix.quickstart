<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/nextype.geolocationreplacer/classes/sxgeo.php";

if (!empty($arParams['CITIES']))
{

    if ( ! $arResult['CITY'] = $APPLICATION->get_cookie('NT_GEOLOCATION_CITY') )
    {
        mb_internal_encoding("cp-1251");
        $SxGeoObj = new SxGeo($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/nextype.geolocationreplacer/classes/sxgeo.dat", SXGEO_BATCH | SXGEO_MEMORY);
        $arCity = $SxGeoObj->getCityFull(SxGeo::getUserIP());
        if ($arCity['city'])
        {
            $arResult['CITY'] = $arCity['city']['name_ru'];
            $APPLICATION->set_cookie('NT_GEOLOCATION_CITY', $arResult['CITY']);
        }
        if ($arCity['region'])
        {
            $arResult['REGION'] = $arCity['region']['name_ru'];
            $APPLICATION->set_cookie('NT_GEOLOCATION_REGION', $arResult['REGION']);
        }
        
    }

    $arCities = \Bitrix\Main\Web\Json::decode(base64_decode($arParams['CITIES']), true);
    $arResult['CITY'] = Bitrix\Main\Text\Encoding::convertEncodingToCurrent($arResult['CITY']);
    $arResult['REGION'] = Bitrix\Main\Text\Encoding::convertEncodingToCurrent($APPLICATION->get_cookie('NT_GEOLOCATION_REGION'));
        
    $findCity = "*";
    $findRegion = "*";

    foreach ($arCities as $key=>$arVal)
    {
        $arCities[$key]['city'] = trim(strtolower($arVal['city']));
        $arCities[$key]['region'] = trim(strtolower($arVal['region']));

        
        
        if ($arCities[$key]['city'] == strtolower($arResult['CITY']))
            $findCity = $arCities[$key]['city'];
        
        if ($arCities[$key]['region'] == strtolower($arResult['REGION']))
            $findRegion = $arCities[$key]['region'];

        if ($arVal['city'] == "*" && ( empty($arVal['region']) || $arVal['region'] == "*"))
            $arVal['type'] = 3;
        elseif ($arVal['city'] == "*" && !empty($arVal['region']))
            $arVal['type'] = 2;
        else
            $arVal['type'] = 1;
        
    }

    usort($arCities, function ($a, $b) {
        if ($a['type'] == $b['type']) {
            return 0;
        }
        return ($a['type'] < $b['type']) ? -1 : 1;
    });

    foreach ($arCities as $arVal)
    {
        if ($arVal['city'] == $findCity && $arVal['region'] == $findRegion)
        {
            $arResult['TEXT'] = $arVal['text'];
        }
            
    }

    $arResult['CITIES'] = $arCities;


    if ($arParams['RETURN'] == "Y")
        return $arResult;

}

$this->IncludeComponentTemplate();