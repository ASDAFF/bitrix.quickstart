<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arParams['IBLOCK_TYPE'])
    $arParams['IBLOCK_TYPE'] = 'catalog';

if(!$arParams["CACHE_TIME"])
    $arParams["CACHE_TIME"] = 3600000;

if(!$arParams["SECTION_ID"])
    return;
 

if ($this->StartResultCache()){ 

  CModule::IncludeModule('iblock');  
 
  $res = CIBlockSection::GetByID($arParams["SECTION_ID"])->GetNext();
        $arResult["DEPTH_LEVEL"] = $res["DEPTH_LEVEL"];
 
  $arFilter = Array('IBLOCK_ID'=>$res['IBLOCK_ID'], 'DEPTH_LEVEL'=>1);
  $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
  while($ar_result = $db_list->GetNext()){
      $arResult['LEVEL_1'][] = $ar_result;
  }        
        
   
  $arResult['SECTION'] = $res;
  
  if($arResult["DEPTH_LEVEL"] == 2){

    $arFilter = Array('IBLOCK_ID'=>$res['IBLOCK_ID'], "SECTION_ID"=>$res["IBLOCK_SECTION_ID"]);
    $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
    while($ar_result = $db_list->GetNext()){
        $arResult['LEVEL_2'][] = $ar_result;
    } 

   $arResult['PARENT_SECTION'] = CIBlockSection::GetByID($res["IBLOCK_SECTION_ID"])->GetNext();
  }
 

 //prent($arResult['PARENT_SECTION']);
      
  $this->IncludeComponentTemplate();

}