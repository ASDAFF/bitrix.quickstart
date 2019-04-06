<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    if(!isset($arParams["CACHE_TIME"]))
        $arParams["CACHE_TIME"] = 300;

    $arParams['WIDTH'] = (is_numeric($arParams['WIDTH']) && $arParams['WIDTH']>0) ? (int) $arParams['WIDTH'] : "600";
    $arParams['HEIGHT'] = (is_numeric($arParams['HEIGHT']) && $arParams['HEIGHT']>0) ? (int) $arParams['HEIGHT'] : "400";

    $arParams['WIDTH_IMAGE'] = ($arParams['WIDTH_IMAGE']>0) ? (int) $arParams['WIDTH_IMAGE'] : 160;
    $arParams['HEIGHT_IMAGE'] = ($arParams['HEIGHT_IMAGE']>0) ? (int) $arParams['HEIGHT_IMAGE'] : 120;

    global $USER;
    if($this->StartResultCache(false, $USER->GetGroups()))
    {
        if(!CModule::IncludeModule("aqw.video"))
        {
            $this->AbortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }

        $this->IncludeComponentTemplate();
    }

?>