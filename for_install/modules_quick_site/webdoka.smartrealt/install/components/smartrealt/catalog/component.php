<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('webdoka.smartrealt'))
{
    ShowError(GetMessage('SMARTREALT_MODULE_NOT_INSTALL'));
    return;
}

$arParams['SET_TITLE'] = $arParams['SET_TITLE']?$arParams['SET_TITLE']:'Y';             
$arParams['FILTER_NAME'] = SmartRealt_Filter::GetFilterName();

$arParams['COUNT_ON_PAGE'] = (intval($arParams['COUNT_ON_PAGE']) > 0)?intval($arParams['COUNT_ON_PAGE']):SmartRealt_Common::GetElementCountonPage();         
$arParams['SORT_BY_DEF'] = (strlen($arParams['SORT_BY_DEF']) > 0)?$arParams['SORT_BY_DEF']:'PriceCurrencyRate';
$arParams['SORT_ORDER_DEF'] = (strlen($arParams['SORT_ORDER_DEF']) > 0)?$arParams['SORT_ORDER_DEF']:'asc';
$arParams['SORT_BY'] = (strlen($arParams['SORT_BY']) > 0)?$arParams['SORT_BY']:$arParams['SORT_BY_DEF'];
$arParams['SORT_ORDER'] = (strlen($arParams['SORT_ORDER']) > 0)?$arParams['SORT_ORDER']:$arParams['SORT_ORDER_DEF'];

$arDefaultUrlTemplates404 = array(
    "list" => COption::GetOptionString('webdoka.smartrealt', 'CATALOG_LIST_URL', SMARTREALT_CATALOG_LIST_URL_DEF),
    "element" => COption::GetOptionString('webdoka.smartrealt', 'CATALOG_DETAIL_URL', SMARTREALT_CATALOG_DETAIL_URL_DEF),
);

if (strlen($arParams["SEF_URL_TEMPLATES"]['list'])==0)
    unset($arParams["SEF_URL_TEMPLATES"]['list']);

if (strlen($arParams["SEF_URL_TEMPLATES"]['element'])==0)
    unset($arParams["SEF_URL_TEMPLATES"]['element']);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
    "TYPE_CODE",
    "TRANSACTION_TYPE",
    "NUMBER",
);                   

if($arParams["SEF_MODE"] == "Y")
{
    $arVariables = array();

    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

    $componentPage = CComponentEngine::ParseComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );

    if (!$componentPage)
    {
        LocalRedirect('/404.php');
        return;
    }

    CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
    $arResult = array(
        "FOLDER" => $arParams["SEF_FOLDER"],
        "URL_TEMPLATES" => $arUrlTemplates,
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases
    );
}
else
{
    $arVariables = array();

    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
    CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

    $componentPage = "";

    if(strlen($arVariables["TYPE_CODE"]) > 0 && strlen($arVariables["TRANSACTION_TYPE"]) > 0)
        $componentPage = "list";
    elseif (strlen($arVariables["TYPE_CODE"]) > 0 && strlen($arVariables["TRANSACTION_TYPE"]) > 0 && intval($arVariables["NUMBER"]) > 0)
        $componentPage = "element";
    
    if (!$componentPage)
    {
        LocalRedirect('/404.php');
        return;
    }

    $arResult = array(
        "FOLDER" => "",
        "URL_TEMPLATES" => Array(
            "list" => htmlspecialchars($APPLICATION->GetCurPage())."?".$arVariableAliases["TYPE_CODE"]."=#TYPE_CODE#"."&".$arVariableAliases["TRANSACTION_TYPE"]."=#TRANSACTION_TYPE#",
            "element" => htmlspecialchars($APPLICATION->GetCurPage())."?".$arVariableAliases["TYPE_CODE"]."=#TYPE_CODE#"."&".$arVariableAliases["TRANSACTION_TYPE"]."=#TRANSACTION_TYPE#"."&".$arVariableAliases["NUMBER"]."=#NUMBER#",
        ),
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases
    );
}

$this->IncludeComponentTemplate($componentPage);
?>