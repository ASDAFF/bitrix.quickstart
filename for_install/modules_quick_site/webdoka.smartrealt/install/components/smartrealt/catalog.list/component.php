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

$arParams['TYPE_CODE'] = (strlen($arParams['TYPE_CODE']) > 0)?$arParams['TYPE_CODE']:array();
$arParams['RUBRIC_CODE'] = (strlen($arParams['RUBRIC_CODE'])>0)?$arParams['RUBRIC_CODE']:'';
$arParams['PAGE'] = (intval($arParams['PAGE']) > 0)?intval($arParams['PAGE']):1;
$arParams['COUNT_ON_PAGE'] = (intval($arParams['COUNT_ON_PAGE']) > 0)?intval($arParams['COUNT_ON_PAGE']):20; 
$arParams['SORT_BY_DEF'] = (strlen($arParams['SORT_BY_DEF']) > 0)?$arParams['SORT_BY_DEF']:'Price';
$arParams['SORT_ORDER_DEF'] = (strlen($arParams['SORT_ORDER_DEF']) > 0)?$arParams['SORT_ORDER_DEF']:'asc';
$arParams['SORT_BY'] = (strlen($arParams['SORT_BY']) > 0)?$arParams['SORT_BY']:$arParams['SORT_BY_DEF'];
$arParams['SORT_ORDER'] = (strlen($arParams['SORT_ORDER']) > 0)?$arParams['SORT_ORDER']:$arParams['SORT_ORDER_DEF'];
$arParams['SORT_BY'] = (strlen($_GET['SortBy']) > 0)?$_GET['SortBy']:$arParams['SORT_BY'];
$arParams['SORT_ORDER'] = (strlen($_GET['SortOrder']) > 0)?$_GET['SortOrder']:$arParams['SORT_ORDER'];

$arParams['LIST_IMAGE_WIDTH'] = (intval($arParams['LIST_IMAGE_WIDTH']) > 0)?intval($arParams['LIST_IMAGE_WIDTH']):74;
$arParams['LIST_IMAGE_HEIGHT'] = (intval($arParams['LIST_IMAGE_HEIGHT']) > 0)?intval($arParams['LIST_IMAGE_HEIGHT']):56;

$oRubric = new SmartRealt_Rubric();
$rsRubric = $oRubric->GetList(array(
        'Active' => 'Y',
        'RubricGroupCode' => $arParams['TYPE_CODE'],
        'Code' => $arParams['RUBRIC_CODE']
    ));

if (!($arRubric = $rsRubric->Fetch()))
{
    LocalRedirect('/404.php');
    //ShowError(GetMessage('SMARTREALT_RUBRIC_NOT_FOUND'));
    return;   
}

$arParams['TYPE_ID'] = explode(';', $arRubric['TypeId']);
$arParams['FILTER'] = SmartRealt_Filter::GetFilter($arRubric['TypeId']);
$arResult['arRubric'] = $arRubric;

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
    );
    
    if (!empty($arRubric['EstateMarket']))
    {
        $arFilter['EstateMarket'] = $arRubric['EstateMarket'];
    }
    
    if (is_array($arParams['FILTER']) && count($arParams['FILTER']) > 0)
    {
        $arFilter = array_merge($arParams['FILTER'], $arFilter);    
    }

    $rsCatalogElement = $oCatalogElement->GetList($arFilter, array($arParams['SORT_BY'] => $arParams['SORT_ORDER']));
    $rsCatalogElement->NavStart($arParams['COUNT_ON_PAGE']); 
    while ($arCatalogElement = $rsCatalogElement->GetNext())
    {
        $arCatalogElement['DetailUrl'] = SmartRealt_CatalogElement::GetDetailUrl($arCatalogElement, $arParams['TYPE_CODE'], $arParams['RUBRIC_CODE']);
        $arCatalogElement['Address'] = SmartRealt_CatalogElement::GetAddress($arCatalogElement);
        $arCatalogElement['Price'] = SmartRealt_CatalogElement::FormatPrice($arCatalogElement);
        
        $rsPhoto = $oCatalogElementPhoto->GetList(array('CatalogElementId' => $arCatalogElement['Id'], 'Deleted' => 'N'), array('Sort' => 'asc'));
        $arCatalogElement['PhotoCount'] = $rsPhoto->SelectedRowsCount();
        if ($arPhoto = $rsPhoto->Fetch())
        {
            $arCatalogElement['Photo'] = CFile::ResizeImageGet($arPhoto['FileId'], array('width'=>$arParams['LIST_IMAGE_WIDTH'], 'height'=>$arParams['LIST_IMAGE_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        }
        
        $arResult['arItems'][] = $arCatalogElement;    
    }
    $arResult['rsItems'] = $rsCatalogElement;

    $arResult['arTableFileds'] = array(
        "Number" => array("sName" => "#", "sCssClass" => "number"),
        "Photo" => array("sName" => GetMessage('SMARTREALT_PHOTO'), "sCssClass" => "photo"),
        "Street" => array("sName" => GetMessage('SMARTREALT_ADDRESS'), "sCssClass" => "address"),
        "RoomQuantity" => array("sName" => GetMessage('SMARTREALT_ROOMS'), "sCssClass" => "roomQuantity", "arTypeId" => array(2)),
        "Floor" => array("sName" => GetMessage('SMARTREALT_FLOOR'), "sCssClass" => "floor", "arTypeId" => array(1,2)),
        "FloorQuantity" => array("sName" => GetMessage('SMARTREALT_FLOOR_QUANTITY'), "sCssClass" => "floorQuantity", "arTypeId" => array(4,5,6,19,20)),
        "GeneralArea" => array("sName" => GetMessage('SMARTREALT_AREA'), "sCssClass" => "generalArea"),
        "LandArea" => array("sName" => GetMessage('SMARTREALT_LAND_AREA'), "sCssClass" => "landArea", "arTypeId" => array(4,5,6,19)),
        "Price" => array("sName" => GetMessage('SMARTREALT_PRICE'), "sCssClass" => "price"),
    );
    
    $arResult['bNotFound'] = !(count($arResult['arItems']) > 0);
    
    $arResult['sRubricName'] = $arRubric['Name'];
    $arResult['bSetFilter'] = SmartRealt_Filter::IsSetFilter($arRubric['TypeId']);
    
    $arResult['TITLE'] = $arParams['TITLE'] ? $arParams['TITLE'] : $arResult['sRubricName'];
    $arResult['PAGE_TITLE'] = strlen($arRubric['PageTitle']) >0 ? $arRubric['PageTitle'] : $arResult['TITLE'];
    
    $this->IncludeComponentTemplate();
}

function GetColumnSortLink($sColumnName)
{
    global $APPLICATION;
    
    if ($sColumnName == 'Photo')
        return;
    
    $sBy = isset($_GET['SortBy']) ? $_GET['SortBy'] : '';
    $sOrder = isset($_GET['SortOrder']) ? $_GET['SortOrder'] : '';

    if ($sColumnName == 'Price')
        $sColumnName = 'PriceCurrencyRate';
    
    if ($sBy == $sColumnName)
    {
        $sOrder = ($sOrder == 'desc') ? 'asc' : 'desc';
    }
    else
    {
        $sBy = $sColumnName;
        $sOrder = 'asc';
    }
    
    return $APPLICATION->GetCurPageParam(sprintf('SortBy=%s&SortOrder=%s', $sBy, $sOrder), array('SortBy', 'SortOrder'));
} 

function GetColumnSortCSSClassName($sColumnName)
{
    global $APPLICATION;
    
    $sBy = isset($_GET['SortBy']) ? $_GET['SortBy'] : '';
    $sOrder = isset($_GET['SortOrder']) ? $_GET['SortOrder'] : '';
    
    if ($sBy == $sColumnName)
    {
        return ($sOrder == 'desc') ? 'desc' : 'asc';
    }
}

function IsColumnSort($sColumnName)
{
    $sBy = isset($_GET['SortBy']) ? $_GET['SortBy'] : '';
    $sOrder = isset($_GET['SortOrder']) ? $_GET['SortOrder'] : '';
    
    return $sBy == $sColumnName;
}
?>
