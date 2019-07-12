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

$arParams['CATALOG_LIST_URL'] = SmartRealt_Options::GetListUrl();
$arParams['SEF_FOLDER'] = SmartRealt_Options::GetSEFFolder();

if ($this->StartResultCache($arParams['CACHE_TIME'], md5(serialize($arResult).serialize($arParams))))
{
    $oRubricGroup = new SmartRealt_RubricGroup();
    $oRubric = new SmartRealt_Rubric();
    $oCatalogElement = new SmartRealt_CatalogElement();
    
    $arResult['arRubrics'] = array();    
    $rsRubricGroups = $oRubricGroup->GetList(array('Active' => 'Y'), array('Sort' => 'asc'));
    while ($arRubricGroup = $rsRubricGroups->Fetch())
    {
        $arResult['arRubrics'][$arRubricGroup['Id']] = array(
            'Name' => $arRubricGroup['Name'],
            'Code' => $arRubricGroup['Code'],
            'arElements' => array());
    }
    
    $rsRubrics = $oRubric->GetList(array('Active' => 'Y'), array('Sort' => 'asc'));
    while ($arRubric = $rsRubrics->Fetch())
    {
        $arObjectTypeIds = explode(';', $arRubric['TypeId']);
        $sRubricGroupCode = $arResult['arRubrics'][$arRubric['RubricGroupId']]['Code'];
        $sRubricCode = $arRubric['Code'];

        $arElementFilter = array(
                'TypeId' => $arObjectTypeIds,
                'SectionId' => $arRubric['SectionId'],
                'TransactionType' => $arRubric['TransactionType'],
                'EstateMarket' => $arRubric['EstateMarket']
            );
            
        $iCount = $oCatalogElement->GetCount($arElementFilter);
        
        $sListUrl = str_replace(array('#TYPE_CODE#', '#TRANSACTION_TYPE#'), array($sRubricGroupCode, strtolower($sRubricCode)), $arParams['SEF_FOLDER'].$arParams['CATALOG_LIST_URL']);
        
        $arResult['arRubrics'][$arRubric['RubricGroupId']]['arElements'][] = array('Name' => $arRubric['Name'],
                                                                                    'Count' => $iCount, 
                                                                                    'ListUrl' => $sListUrl
                                                                                    );
    }
    
    $this->IncludeComponentTemplate();
}
?>
