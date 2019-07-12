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

$arParams['NUMBER'] = (intval($arParams['NUMBER'])>0)?$arParams['NUMBER']:0;
$arParams['TYPE_CODE'] = (strlen($arParams['TYPE_CODE'])>0)?$arParams['TYPE_CODE']:0;
$arParams['RUBRIC_CODE'] = (strlen($arParams['RUBRIC_CODE'])>0)?$arParams['RUBRIC_CODE']:0;

$arParams['DETAIL_IMAGE_MEDIUM_WIDTH'] = (intval($arParams['DETAIL_IMAGE_MEDIUM_WIDTH']) > 0)?intval($arParams['DETAIL_IMAGE_MEDIUM_WIDTH']):290;
$arParams['DETAIL_IMAGE_MEDIUM_HEIGHT'] = (intval($arParams['DETAIL_IMAGE_MEDIUM_HEIGHT']) > 0)?intval($arParams['DETAIL_IMAGE_MEDIUM_HEIGHT']):218;
$arParams['DETAIL_IMAGE_BIG_WIDTH'] = (intval($arParams['DETAIL_IMAGE_BIG_WIDTH']) > 0)?intval($arParams['DETAIL_IMAGE_BIG_WIDTH']):800;
$arParams['DETAIL_IMAGE_BIG_HEIGHT'] = (intval($arParams['DETAIL_IMAGE_BIG_HEIGHT']) > 0)?intval($arParams['DETAIL_IMAGE_BIG_HEIGHT']):600;

if ($this->StartResultCache($arParams['CACHE_TIME'], md5(serialize($arParams))))
{
    $oCatalogElement = new SmartRealt_CatalogElement();
    $oCatalogElementPhoto = new SmartRealt_CatalogElementPhoto();
    $oRubric = new SmartRealt_Rubric();
    
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
    
    $arFilter = array(
        'Number' => $arParams['NUMBER'],
        'TypeId' => explode(';', $arRubric['TypeId']),
        'TransactionType' => strtoupper($arRubric['TransactionType']),
        'SectionId' => $arRubric['SectionId'],
        'EstateMarket' => $arRubric['EstateMarket'],
        'Status' => 'PUBLISH',
        'Deleted' => 'N',
    );

    $rsCatalogElement = $oCatalogElement->GetList($arFilter);
            
    if ($arCatalogElement = $rsCatalogElement->Fetch())
    {
        $arCatalogElement['Address'] = SmartRealt_CatalogElement::GetAddress($arCatalogElement);
        $arCatalogElement['Price'] = SmartRealt_CatalogElement::FormatPrice($arCatalogElement, false);
        $arCatalogElement['PricePerMetr'] = SmartRealt_CatalogElement::FormatPricePerMetr($arCatalogElement, false);
        $arCatalogElement['Description'] = str_replace("\n", "<br>", $arCatalogElement['Description']); 

        $rsPhoto = $oCatalogElementPhoto->GetList(array('CatalogElementId' => $arCatalogElement['Id'], 'Deleted' => 'N'), array('Sort' => 'asc'));
        $arCatalogElement['PhotoCount'] = $rsPhoto->SelectedRowsCount();
        
        if ($arCatalogElement['PhotoCount'] > 0)
        {
            $arCatalogElement['PhotoMedium'] = array();
            $arCatalogElement['PhotoBig'] = array();
        }

        while ($arPhoto = $rsPhoto->Fetch())
        {
            $arCatalogElement['PhotoMedium'][] = CFile::ResizeImageGet($arPhoto['FileId'], array('width'=>$arParams['DETAIL_IMAGE_MEDIUM_WIDTH'], 'height'=>$arParams['DETAIL_IMAGE_MEDIUM_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $arCatalogElement['PhotoBig'][] = CFile::ResizeImageGet($arPhoto['FileId'], array('width'=>$arParams['DETAIL_IMAGE_BIG_WIDTH'], 'height'=>$arParams['DETAIL_IMAGE_BIG_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        }

        $arResult['CATALOG_LIST_URL'] = SmartRealt_CatalogElement::GetListUrl($arRubric['RubricGroupCode'], $arRubric['Code']);
        
        $arResult['TITLE'] = $arCatalogElement['SectionFullNameSign'].' '.$arCatalogElement['Address'];
        $arResult['arProperties'] = SmartRealt_CatalogElement::GetCatalogElementProperties();
        
        $arResult['arObject'] = $arCatalogElement;    
    }
    else
    {
        LocalRedirect('/404.php');
        return;
    }

    $this->IncludeComponentTemplate();
}
?>
