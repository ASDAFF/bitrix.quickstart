<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

if($this->StartResultCache()){
    CModule::IncludeModule('iblock');
 
    foreach ($arParams['PROPERTY_CODE'] as $code) {
        $properties = CIBlockProperty::GetList(Array(), Array("ACTIVE" => "Y", "CODE" => $code, "IBLOCK_ID" => $arParams['IBLOCK_ID']));
        while ($prop_fields = $properties->GetNext())
            $arResult['PROPS'][$code] = $prop_fields;
    }
 
    $this->IncludeComponentTemplate();
}