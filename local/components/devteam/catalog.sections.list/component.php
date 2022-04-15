<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 
 
if(!$arParams["CACHE_TIME"])
    $arParams["CACHE_TIME"] = 3600000;
  
if($this->StartResultCache()){

    CModule::IncludeModule('iblock');
 
    $ar_res = CIBlockSection::GetByID($arParams['SECTION_ID'])->GetNext(); 
    
    if($ar_res['DEPTH_LEVEL'] == 2){
        $db_list = CIBlockSection::GetList(Array("SORT" => "ASC"), 
                                           Array('IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                 'ACTIVE' => 'Y',
                                                 'CHECK_PERMISSIONS' => 'N',
                                                 'SECTION_ID' => $ar_res['IBLOCK_SECTION_ID']),
                                           true);
        while ($ar_result = $db_list->GetNext())
             $arResult['SECTIONS'][] = $ar_result;

        $ar_res = CIBlockSection::GetByID($ar_res['IBLOCK_SECTION_ID'])->GetNext(); 
        $arResult['SECTION'] = $ar_res;
    } else {
        $db_list = CIBlockSection::GetList(Array("SORT" => "ASC"), 
                                           Array('IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                 'ACTIVE' => 'Y',
                                                 'CHECK_PERMISSIONS' => 'N',
                                                 'SECTION_ID' => $ar_res['ID']),
                                           true);
        while ($ar_result = $db_list->GetNext())
             $arResult['SECTIONS'][] = $ar_result;
      
        $arResult['SECTION'] = $ar_res;
    }
    
    $this->IncludeComponentTemplate();
}