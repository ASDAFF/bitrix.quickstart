<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function get_and_resize_($val, $maxW = 50, $maxH = 50){
    return array_merge( CFile::ResizeImageGet($val, 
                                              array('width'=>$maxW,
                                                    'height'=>$maxH ),
                                              BX_RESIZE_IMAGE_PROPORTIONAL,
                                              true), 
                        CFile::GetFileArray($val)  ); 
} 

if($arResult['DETAIL_PICTURE']['ID'])
    $arResult['PICTURES'][] = get_and_resize_($arResult['DETAIL_PICTURE']['ID']);
elseif($arResult['PREVIEW_PICTURE']['ID'])
    $arResult['PICTURES'][] = get_and_resize_($arResult['PREVIEW_PICTURE']['ID']);  

foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $val)
    $arResult['PICTURES'][] = get_and_resize_($val);

$system_props = array('FORUM_MESSAGE_CNT', 'FORUM_TOPIC_ID', 'SHOP',
                      'TOP_SALES', 'RECOMMENDED', 'NEW', 'SALE', 'PRODUCT_DAY');

foreach($arResult['DISPLAY_PROPERTIES'] as $pid => $arr)
    if(in_array($arr["CODE"], $system_props))
         unset($arResult['DISPLAY_PROPERTIES'][$pid]);

$in_basket = array();
CModule::IncludeModule('sale');

$dbBasketItems = CSaleBasket::GetList(
                array(
            "NAME" => "ASC",
            "ID" => "ASC"
                ), array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL"
                ), false, false, array()
);
while($el = $dbBasketItems->Fetch())
    $in_basket[] = $el["PRODUCT_ID"];

if(in_array($arResult['ID'], $in_basket))
        $arResult['IN_BASKET'] = 'Y';
 