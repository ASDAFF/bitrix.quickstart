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

$arParams['PAGE'] = (intval($arParams['PAGE']) > 0)?intval($arParams['PAGE']):1;                              
$arParams['COUNT_ON_PAGE'] = (intval($arParams['COUNT_ON_PAGE']) > 0)?intval($arParams['COUNT_ON_PAGE']):SmartRealt_Common::GetElementCountonPage();

$arParams['SORT_BY_DEF'] = (strlen($arParams['SORT_BY_DEF']) > 0)?$arParams['SORT_BY_DEF']:'PriceCurrencyRate';
$arParams['SORT_ORDER_DEF'] = (strlen($arParams['SORT_ORDER_DEF']) > 0)?$arParams['SORT_ORDER_DEF']:'asc';   
$arParams['SORT_BY'] = (strlen($arParams['SORT_BY']) > 0)?$arParams['SORT_BY']:$arParams['SORT_BY_DEF'];
$arParams['SORT_ORDER'] = (strlen($arParams['SORT_ORDER']) > 0)?$arParams['SORT_ORDER']:$arParams['SORT_ORDER_DEF'];
$arParams['SORT_BY'] = (strlen($_GET['SortBy']) > 0)?$_GET['SortBy']:$arParams['SORT_BY'];
$arParams['SORT_ORDER'] = (strlen($_GET['SortOrder']) > 0)?$_GET['SortOrder']:$arParams['SORT_ORDER'];

$arParams['MAP_ID'] = (strlen($arParams['MAP_ID']) > 0)?$arParams['MAP_ID']:'smartrealt_map';
$arParams['MAP_WIDTH'] = (intval($arParams['MAP_WIDTH']) > 0)?$arParams['MAP_WIDTH']:320;
$arParams['MAP_HEIGHT'] = (intval($arParams['MAP_HEIGHT']) > 0)?$arParams['MAP_HEIGHT']:320;

$arParams['CATALOG_DETAIL_URL'] = SmartRealt_Options::GetDetailUrl();
$arParams['SEF_FOLDER'] = SmartRealt_Options::GetSEFFolder();

$arParams['MAP_TYPE'] = SmartRealt_Options::GetMapType(); 
$arParams['IS_YANDEX'] = $arParams['MAP_TYPE'] == 'yandex'; 

//Получим параметры Url
$arVariables = array();
SmartRealt_Url::Parse($arVariables);
$arParams = array_merge($arParams, $arVariables);

$arParams['RUBRIC_CODE'] = $arParams['TRANSACTION_TYPE'];

$oRubric = new SmartRealt_Rubric();
$rsRubric = $oRubric->GetList(array(
        'Active' => 'Y',
        'RubricGroupCode' => $arParams['TYPE_CODE'],
        'Code' => $arParams['RUBRIC_CODE']
    ));

//Если рубрика не найдена
if (!($arRubric = $rsRubric->Fetch()))
{
    ShowError(GetMessage('SMARTREALT_RUBRIC_NOT_FOUND'));
    return;   
}

$arParams['TYPE_ID'] = $arRubric['TypeId'];

//Инициализируем фильтр
$arParams['FILTER'] = SmartRealt_Filter::GetFilter();                     

if ($this->StartResultCache($arParams['CACHE_TIME'], md5(serialize($arResult).serialize($arParams))))
{
    $oCatalogElement = new SmartRealt_CatalogElement();
    $oCatalogElementPhoto = new SmartRealt_CatalogElementPhoto();

    $arFilter = array(
        'TypeId' => explode(';', $arRubric['TypeId']), 
        'TransactionType' => $arRubric['TransactionType'],
        'SectionId' => $arRubric['SectionId'],     
        'Status' => 'PUBLISH',
        'Deleted' => 'N',
        'Number' => $arParams['NUMBER'],
    );
    //echo "<pre>".print_r($arFilter)."</pre>";
              
    if (!empty($arRubric['EstateMarket']))
    {
        $arFilter['EstateMarket'] = $arRubric['EstateMarket'];
    }
    
    //Смерджим фидьтры
    if (is_array($arParams['FILTER']) && count($arParams['FILTER']) > 0)
    {
        $arFilter = array_merge($arParams['FILTER'], $arFilter);    
    }

    $rsCatalogElement = $oCatalogElement->GetList($arFilter, array($arParams['SORT_BY'] => $arParams['SORT_ORDER']));

    $rsCatalogElement->NavStart($arParams['COUNT_ON_PAGE'], false, $arParams['PAGE']); 
    while ($arCatalogElement = $rsCatalogElement->GetNext())
    {
        $Latitude = $arParams['IS_YANDEX'] && $arCatalogElement['LatitudeYandex']?$arCatalogElement['LatitudeYandex']:$arCatalogElement['Latitude'];
        $Longitude = $arParams['IS_YANDEX'] && $arCatalogElement['LongitudeYandex']?$arCatalogElement['LongitudeYandex']:$arCatalogElement['Longitude'];
        $Zoom = $arParams['IS_YANDEX'] && $arCatalogElement['ZoomYandex']?$arCatalogElement['ZoomYandex']:$arCatalogElement['Zoom'];
        
        if (strlen($Latitude) == 0 || strlen($Longitude) == 0)
            continue;
        
        $Latitude = str_replace(',', '.', $Latitude);
        $Longitude = str_replace(',', '.', $Longitude);

        $arCatalogElement['Address'] = SmartRealt_CatalogElement::GetAddress($arCatalogElement);
        $arCatalogElement['DetailUrl'] = SmartRealt_CatalogElement::GetDetailUrl($arCatalogElement, $arParams['TYPE_CODE'], $arParams['RUBRIC_CODE']);
        
        $rsPhoto = $oCatalogElementPhoto->GetList(array('CatalogElementId' => $arCatalogElement['Id'], 'Deleted' => 'N'), array('Sort' => 'asc'));
        $arCatalogElement['ObjectPhotoCount'] = $rsPhoto->SelectedRowsCount();
        if ($arPhoto = $rsPhoto->Fetch())
        {
            $arCatalogElement['Photo'] = CFile::ResizeImageGet($arPhoto['FileId'], array('width'=>74, 'height'=>56), BX_RESIZE_IMAGE_PROPORTIONAL, true);                                          
        }
        
        $sPhotoHtml = '';
        if (count($arCatalogElement['Photo']) > 0)
        {
            $sPhotoHtml = sprintf('<img src="%s" alt=""/>', $arCatalogElement['Photo']['src']);
        }
        
        $sArea = SmartRealt_CatalogElement::GetAreaString($arCatalogElement);
        $sPrice = SmartRealt_CatalogElement::FormatPrice($arCatalogElement);

        $arResult['arItems'][] = array(
            'Latitude' => $Latitude, 
            'Longitude' => $Longitude,
            'Zoom' => $Zoom,
            'SectionFullNameSign' => $arCatalogElement['SectionFullNameSign'],
            'Address' => $arCatalogElement['Address'],
            'Info' => str_replace(array("\n", "\r"), "", sprintf('<div class="infoWindow">
                                    %s
                                    <div class="params">
                                        <div class="section">%s</div>  
                                        <div class="address">%s</div> 
                                        <div class="area"><label>%s:</label>%s</div>
                                        <div class="price"><label>%s:</label>%s</div>
                                        <div class="more"><a href="%s">%s</a> &#187;</div>
                                    </div>
                                </div>', 
                                $sPhotoHtml, 
                                $arCatalogElement['SectionFullNameSign'], 
                                $arCatalogElement['Address'], 
                                GetMessage('SMARTREALT_AREA'),
                                $sArea,
                                GetMessage('SMARTREALT_PRICE'),
                                $sPrice,
                                $arCatalogElement['DetailUrl'],
                                GetMessage('SMARTREALT_SHOW_MORE')
                        ))
            );    
    }
    
    if (!defined('BX_UTF') || !BX_UTF) 
    {
        $arResult['arItems'] = SmartRealt_Common::IconvArray('cp1251', 'utf-8', $arResult['arItems']);
    }

    $arResult['rsItems'] = $rsCatalogElement;

    $arResult['bNotFound'] = !(count($arResult['arItems']) > 0);
    
    $this->IncludeComponentTemplate();
}
?>