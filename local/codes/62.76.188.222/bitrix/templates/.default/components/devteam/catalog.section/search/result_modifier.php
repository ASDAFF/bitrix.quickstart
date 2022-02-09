<?php
CModule::IncludeModule('iblock');

foreach($arResult["ITEMS"] as  &$arElement){
    
   $res = CIBlockSection::GetByID($arElement["IBLOCK_SECTION_ID"]);
    if($ar_res = $res->GetNext()) {

    $arElement['DETAIL_PAGE_URL'] = str_replace('/catalog/category/', 
                                                '/catalog/detail/',
                   $ar_res["SECTION_PAGE_URL"] . $arElement['CODE'] . '/');
   $arElement['SECTION_PAGE_URL'] =  $ar_res["SECTION_PAGE_URL"];
        $arElement['PATH_NAME'] = $ar_res['NAME'];
    }
    
    
}