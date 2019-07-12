<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('webdoka.smartrealt'))
{
    ShowError(GetMessage('SMARTREALT_MODULE_NOT_INSTALL'));
    return;
}

$oRubric = new SmartRealt_Rubric(); 
$oCatalogElement = new SmartRealt_CatalogElement(); 

//Получим параметры Url
$arVariables = array();
SmartRealt_Url::Parse($arVariables);
$arParams = array_merge($arParams, $arVariables);
$arParams['RUBRIC_CODE'] = $arParams['TRANSACTION_TYPE'];

$rsRubric = $oRubric->GetList(array(
        'Active' => 'Y',
        'RubricGroupCode' => $arParams['TYPE_CODE'],    
        'Code' => $arParams['RUBRIC_CODE'],    
    ));

if (!($arRubric = $rsRubric->Fetch()))
{
    ShowError(GetMessage('SMARTREALT_RUBRIC_NOT_FOUND'));
    return;   
}

//Инициализируем фильтр
$arParams['FILTER'] = SmartRealt_Filter::GetFilter($arRubric['TypeId']);
$arParams['FILTER_NAME'] = SmartRealt_Filter::GetFilterName();

$arParams['FILTER']['TypeId'] = $arRubric['TypeId']; 
$arParams['FILTER']['TransactionType'] = strtoupper($arRubric['TransactionType']); 
$arParams['FILTER']['SectionId'] = $arRubric['SectionId'];

if (!empty($arRubric['EstateMarket']))
{
    $arParams['FILTER']['EstateMarket'] = $arRubric['EstateMarket']; 
    $arResult['bEstateMarketHide'] = true;
}

if (!isset($arParams['FILTER']['LocationType']))
        $arParams['FILTER']['LocationType'] = 'City';
        
$arResult['bSetFilter'] = SmartRealt_Filter::IsSetFilter($arRubric['TypeId']);
$arResult['CATALOG_LIST_URL'] = SmartRealt_CatalogElement::GetListUrl($arRubric['RubricGroupCode'], $arRubric['Code'])."#h1";

if ($this->StartResultCache($arParams['CACHE_TIME'], md5(serialize($arResult).serialize($arParams))))
{
    $rsRubric = $oRubric->GetList(array('Active' => 'Y'), array('Sort' => 'asc'), array('TypeId'));
    
    while($arRubric = $rsRubric->Fetch())
    {                          
        $arTypeIds = explode(';', $arRubric['TypeId']);

        if (count($arTypeIds) > 0 && strlen($arRubric['TypeName']) > 0)
            $arResult['arTypes'][implode(";", $arTypeIds)] = $arRubric['TypeName'];

        if (count($arTypeIds) > 0 && strlen($arRubric['TypeCode']) > 0)
            $arResult['arTypeCodes'][implode(";", $arTypeIds)] = $arRubric['TypeCode'];
    }
    
    $arResult['arRoomQuantity'] = array(1=>1,2=>2,3=>3,4=>4,5=>'5+');
    
    //Города и районы города
    $rsCityTowns = $oCatalogElement->GetList(array('Status' => 'PUBLISH'), array('TownName' => 'asc'), array('CityId', 'TownId'));

    $arResult['arTownByCity'] = array();
    while ($arCityTown = $rsCityTowns->Fetch())
    {
        if (strlen($arCityTown['TownName']) > 0)
            $arResult['arTownByCity'][$arCityTown['CityId']][$arCityTown['TownId']] = $arCityTown['TownName'];
    }
    
    //Районы города
    $rsCityAreas = $oCatalogElement->GetList(array('Status' => 'PUBLISH'), array('CityAreaName' => 'asc'), array('CityId', 'CityAreaId'));
    $arResult['arCities'] = array();
    $arResult['arCityAreas'] = array();
    while ($arCityArea = $rsCityAreas->Fetch())
    {
        if (strlen($arCityArea['CityName']) > 0)
            $arResult['arCities'][$arCityArea['CityId']] = $arCityArea['CityName'];
        
        if (strlen($arCityArea['CityAreaName']) > 0)
            $arResult['arCityAreas'][$arCityArea['CityId']][$arCityArea['CityAreaId']] = $arCityArea['CityAreaName'];
    }
    
    //Регионы и населенные пункты
    $rsRegionAreas = $oCatalogElement->GetList(array('Status' => 'PUBLISH'), array('RegionAreaName' => 'asc', 'TownName' => 'asc'), array('RegionAreaId', 'TownId'));
    $arResult['arRegionAreas'] = array();
    $arResult['arTownByRegionArea'] = array();
    while ($arRegionArea = $rsRegionAreas->Fetch())
    {                    
        if (strlen($arRegionArea['RegionAreaName']) > 0)
            $arResult['arRegionAreas'][$arRegionArea['RegionAreaId']] = $arRegionArea['RegionAreaName'];
        
        if (strlen($arRegionArea['TownName']) > 0)
            $arResult['arTownByRegionArea'][$arRegionArea['RegionAreaId']][$arRegionArea['TownId']] = $arRegionArea['TownName'];
    }
    
    if (!defined('BX_UTF') || !BX_UTF)
    {
        $arResult['jsTownByRegionArea'] = json_encode(SmartRealt_Common::IconvArray("WINDOWS-1251", "UTF-8", $arResult['arTownByRegionArea']));
        $arResult['jsTownByRegionArea'] = iconv("UTF-8", "WINDOWS-1251", $arResult['jsTownByRegionArea']);
        $arResult['jsCityAreas'] = json_encode(SmartRealt_Common::IconvArray("WINDOWS-1251", "UTF-8", $arResult['arCityAreas']));
        $arResult['jsTownByRegionArea'] = iconv("UTF-8", "WINDOWS-1251", $arResult['jsCityAreas']);
        $arResult['jsTownByCity'] = json_encode(SmartRealt_Common::IconvArray("WINDOWS-1251", "UTF-8", $arResult['arTownByCity']));
        $arResult['jsTownByRegionArea'] = iconv("UTF-8", "WINDOWS-1251", $arResult['jsTownByCity']);
    }
    else
    {
        $arResult['jsTownByRegionArea'] = json_encode($arResult['arTownByRegionArea']);
        $arResult['jsTownByRegionArea'] = $arResult['jsTownByRegionArea'];
        $arResult['jsCityAreas'] = json_encode($arResult['arCityAreas']);
        $arResult['jsTownByRegionArea'] = $arResult['jsCityAreas'];
        $arResult['jsTownByCity'] = json_encode($arResult['arTownByCity']);
        $arResult['jsTownByRegionArea'] = $arResult['jsTownByCity'];
    }
    
    $arResult['bCityAreaHide'] = (count($arResult['arCityAreas'][$arParams['FILTER']['CityId']]) == 0 || strlen($arParams['FILTER']['CityId']) == 0);
    
    $this->IncludeComponentTemplate();
}
?>
