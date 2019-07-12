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

$arParams['COUNT'] = (intval($arParams['COUNT']) > 0)?intval($arParams['COUNT']):0;
$arParams['SECTION_ID'] = (intval($arParams['SECTION_ID']) > 0)?intval($arParams['SECTION_ID']):'';
$arParams['ESTATE_MARKET'] = (strlen($arParams['ESTATE_MARKET']) > 0)?$arParams['ESTATE_MARKET']:'';
$arParams['TRANSACTION_TYPE'] = (strlen($arParams['TRANSACTION_TYPE']) > 0)?$arParams['TRANSACTION_TYPE']:'';
$arParams['CATALOG_TOP_LIST_URL'] = (strlen($arParams['CATALOG_TOP_LIST_URL']) > 0)?$arParams['CATALOG_TOP_LIST_URL']:'';
$arParams['CATALOG_DETAIL_URL'] = SmartRealt_Options::GetDetailUrl();

$arParams['SORT_RAND'] = $arParams['SORT_RAND'] == "N"?"N":"Y"; 
$arParams['SORT_BY_DEF'] = (strlen($arParams['SORT_BY_DEF']) > 0)?$arParams['SORT_BY_DEF']:'PriceCurrencyRate';
$arParams['SORT_ORDER_DEF'] = (strlen($arParams['SORT_ORDER_DEF']) > 0)?$arParams['SORT_ORDER_DEF']:'asc';   
$arParams['SORT_BY'] = (strlen($arParams['SORT_BY']) > 0)?$arParams['SORT_BY']:$arParams['SORT_BY_DEF'];
$arParams['SORT_ORDER'] = (strlen($arParams['SORT_ORDER']) > 0)?$arParams['SORT_ORDER']:$arParams['SORT_ORDER_DEF'];

$arParams['LIST_IMAGE_WIDTH'] = (intval($arParams['LIST_IMAGE_WIDTH']) > 0)?intval($arParams['LIST_IMAGE_WIDTH']):74;
$arParams['LIST_IMAGE_HEIGHT'] = (intval($arParams['LIST_IMAGE_HEIGHT']) > 0)?intval($arParams['LIST_IMAGE_HEIGHT']):56;

if ($this->StartResultCache($arParams['CACHE_TIME'], md5(serialize($arResult).serialize($arParams))))
{
    $oRubric = new SmartRealt_Rubric();                           
    $oCatalogElement = new SmartRealt_CatalogElement();
    $oCatalogElementPhoto = new SmartRealt_CatalogElementPhoto();
    
    $rsRubrics = $oRubric->GetList(array('Active' => 'Y'), array(), array('Code'));
    $arTypeCodes = array();
    while ($arRubric = $rsRubrics->Fetch())
    {
        $arTypeIds = explode(';', $arRubric['TypeId']);
        
        foreach ($arTypeIds as $iTypeId)
            $arTypeCodes[$iTypeId] = $arRubric['Code'];
    }
                                                           
    
    $arFilter = array('SpecialOffer' => 'Y', 
                        'TransactionType' => $arParams['TRANSACTION_TYPE'], 
                        'EstateMarket' => $arParams['ESTATE_MARKET'], 
                        'SectionId' => $arParams['SECTION_ID'], 
                        'Deleted' => 'N',
                        'Status' => 'PUBLISH',
    );
                        
                        
    if ($arParams['COUNT'] > 0)
    {
        $arFilter['Limit'] = array(0, $arParams['COUNT']);
    }
                   
    if (count($arParams['TYPE']) > 0)
    {
        $arFilter['TypeId'] = $arParams['TYPE'];
    }
    
    $arSort = array();
    if ($arParams['SORT_RAND'] == "Y")
        $arSort = array('Rand' => 'asc'); 
    else
        $arSort = array($arParams['SORT_BY'] => $arParams['SORT_ORDER']); 
    
    $rsCatalogElement = $oCatalogElement->GetList($arFilter, $arSort);

    while ($arCatalogElement = $rsCatalogElement->Fetch())
    {
        $arCatalogElement['DetailUrl'] = SmartRealt_CatalogElement::GetDetailUrl($arCatalogElement);
        $arCatalogElement['Address'] = SmartRealt_CatalogElement::GetAddress($arCatalogElement);
        $arCatalogElement['Price'] = SmartRealt_CatalogElement::FormatPrice($arCatalogElement);
        
        $rsPhoto = $oCatalogElementPhoto->GetList(array('CatalogElementId' => $arCatalogElement['Id'], 'Deleted' => 'N'), array('Sort' => 'asc'));
        $arCatalogElement['ObjectPhotoCount'] = $rsPhoto->SelectedRowsCount();
        if ($arPhoto = $rsPhoto->Fetch())
        {
            $arCatalogElement['PhotoFileId'] = $arPhoto['FileId'];                                          
        }
        
        $arResult['arItems'][] = $arCatalogElement;
    }
    
    $arResult['arTableFileds'] = array(
        "Number" => array("sName" => "#", "sCssClass" => "number"),
        "Photo" => array("sName" => GetMessage('SMARTREALT_PHOTO'), "sCssClass" => "photo"),
        "Address" => array("sName" => GetMessage('SMARTREALT_ADDRESS'), "sCssClass" => "address"),
        "RoomQuantity" => array("sName" => GetMessage('SMARTREALT_ROOMS'), "sCssClass" => "roomQuantity", "arTypeId" => array(2)),
        "Floor" => array("sName" => GetMessage('SMARTREALT_FLOOR'), "sCssClass" => "floor", "arTypeId" => array(1,2)),
        "GeneralArea" => array("sName" => GetMessage('SMARTREALT_AREA'), "sCssClass" => "generalArea"),
        "LandArea" => array("sName" => GetMessage('SMARTREALT_LAND_AREA'), "sCssClass" => "landArea", "arTypeId" => array(4,5,6,19)),
        "Price" => array("sName" => GetMessage('SMARTREALT_PRICE'), "sCssClass" => "price"),
    );
    
    $arResult['bNotFound'] = !(count($arResult['arItems']) > 0);
    
    $this->IncludeComponentTemplate();
}
?>
