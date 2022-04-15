<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$in_basket = array();
CModule::IncludeModule('sale');

$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
                ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
                ),
        false,
        false,
        array()
        ); 

while($el = $dbBasketItems->Fetch())
    $in_basket[] = $el["PRODUCT_ID"];
 

foreach($arResult["ITEMS"] as &$arElement){
 
    if(in_array($arElement['ID'], $in_basket)){
        $arElement['IN_BASKET'] = 'Y'; 
    }
 
    if(in_compare($arElement['ID']))
        $arElement['IN_COMPARE'] = 'Y';

}
