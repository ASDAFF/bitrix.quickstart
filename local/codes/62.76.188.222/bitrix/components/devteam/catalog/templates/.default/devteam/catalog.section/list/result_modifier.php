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

$maxW = 105;
$maxH = 100;
 
foreach($arResult["ITEMS"] as &$arElement){
    if($arElement['PREVIEW_PICTURE']['ID']){
        $file = CFile::ResizeImageGet($arElement['PREVIEW_PICTURE'],
                array('width'=>$maxW, 'height'=>$maxH),
                BX_RESIZE_IMAGE_PROPORTIONAL,
                true);                
        $arElement['PREVIEW_PICTURE']['SRC'] = $file['src'];
        $arElement['PREVIEW_PICTURE']['WIDTH'] = $file['width']; 
        $arElement['PREVIEW_PICTURE']['HEIGHT'] = $file['height']; 
    } 
    
    if(in_compare($arElement['ID'])){
        $arElement['IN_COMPARE'] = 'Y';
    }
    
    
    if(in_array($arElement['ID'], $in_basket)){
        $arElement['IN_BASKET'] = 'Y'; 
    }
    
}  
