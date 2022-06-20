<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)   die();

$arParams['PLAY'] = intval($arParams['PLAY']);
if(!$arParams['PLAY']) 
    $arParams['PLAY'] = 3000;

$arParams["TYPE"] = (isset($arParams["TYPE"]) ? trim($arParams["TYPE"]) : "");

if($arParams["NOINDEX"] <> "Y")
    $arParams["NOINDEX"] = "N";

if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && 
    COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
    $arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
else
    $arParams["CACHE_TIME"] = 0;

if ($this->StartResultCache()){

    if(!CModule::IncludeModule("advertising"))
        return;

    $rsBanners = CAdvBanner::GetList($by, $order, 
            Array("TYPE_SID" => $arParams["TYPE"]), 
            $is_filtered, "N"); 
    
    while($arBanner = $rsBanners->GetNext()) {
        
        $arBanner['CODE_'] = CAdvBanner::GetHTML($arBanner, ($arParams["NOINDEX"] == "Y"));
        
        $arResult['BANNERS'][] = $arBanner;
    }
    
    $this->IncludeComponentTemplate();
}
 