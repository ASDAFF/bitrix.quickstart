<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!$arParams['ELEMENT_ID'])
    return;

IF(!$arParams['CNT'])
    $arParams['CNT'] = 4;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

if ($this->StartResultCache())
    {
    if(!CModule::IncludeModule("iblock")){
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
    }

    $arSelect = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_RECOMMENDED_LIST");

    $arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], 
                      "ID"=>$arParams['ELEMENT_ID'] );

    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>1), $arSelect);

    if($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();

        foreach($arProps['RECOMMENDED_LIST']['VALUE'] as $id){
            if($x++>=$arParams['CNT'])
                break;

            $ar_res = CIBlockElement::GetByID($id)->GetNext();
            $arResult['ITEMS'][] = $ar_res;
        }
    }

    $this->IncludeComponentTemplate();
}