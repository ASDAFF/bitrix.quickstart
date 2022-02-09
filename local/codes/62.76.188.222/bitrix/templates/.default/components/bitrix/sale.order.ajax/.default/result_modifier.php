<?php

CModule::IncludeModule('iblock');

foreach($arResult["BASKET_ITEMS"] as &$item){
    
    $res = CIBlockElement::GetByID($item["PRODUCT_ID"])->GetNext();
    if($res)
        $item["DETAIL_PAGE_URL"] = $res["DETAIL_PAGE_URL"];
    
    
}