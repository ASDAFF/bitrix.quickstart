<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$cnt = 5; // количество шкал 


CModule::IncludeModule('catalog');

$res = CCatalogStoreProduct::GetList(
       array(), array('PRODUCT_ID' => $arResult['ID']), false, false, array('*')
);
 
while ($store = $res->Fetch()) {
    $arResult['STORE'][] = $store;
    if($store["AMOUNT"] > $max)
        $max = $store["AMOUNT"];
    $storesarr[] = $store['STORE_ID'];
}

$stores = CCatalogStore::GetList(array(), array('ID' => $storesarr), false, false, array('*'));
while ($store = $stores->Fetch()) 
    foreach($arResult['STORE'] as &$el)
        if($el["STORE_ID"] == $store['ID'])
            $el['SCHEDULE'] = $store['SCHEDULE'];


foreach($arResult['STORE'] as &$el)
   $el["AMOUNT_%"] = intval($el["AMOUNT"] * 100 / $max  / (100 / $cnt));
 