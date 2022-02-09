<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arParams['IBLOCK_TYPE'])
    $arParams['IBLOCK_TYPE'] = 'catalog';

if(!$arParams["CACHE_TIME"])
    $arParams["CACHE_TIME"] = 3600000;

$arParams['CUR_DIR'] = $APPLICATION->GetCurDir();
 
if ($this->StartResultCache()){ 

  CModule::IncludeModule('iblock');  

    $res = CIBlock::GetList( Array( "SORT"=>"ASC"), 
                             Array( 'TYPE'=>$arParams['IBLOCK_TYPE'],
                                    'SITE_ID'=>SITE_ID, 
                                    'ACTIVE'=>'Y'),
                             false );
    
    while($ar_res = $res->Fetch())
         $arResult['IBLOCKS'][$ar_res['ID']]['IBLOCK'] = $ar_res; 
 
  $db_list = CIBlockSection::GetList(Array(),
                                     Array('IBLOCK_TYPE'=>$arParams['IBLOCK_TYPE']),
                                     false,
                                     array('SECTION_PAGE_URL', 'IBLOCK_ID', 'ID', 'NAME',
                                           'IBLOCK_SECTION_ID', 'DEPTH_LEVEL') );
 
  while($ar_result = $db_list->GetNext()){ 
      if($arParams['CUR_DIR'] == $ar_result['SECTION_PAGE_URL'])
          $arResult['SELECTED'] = $ar_result["IBLOCK_ID"];
      $arResult['IBLOCKS'][$ar_result["IBLOCK_ID"]]['SECTIONS'][] = $ar_result;
  }
   
  $this->IncludeComponentTemplate();

}