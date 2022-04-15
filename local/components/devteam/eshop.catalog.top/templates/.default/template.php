<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
  
  if(!count($arResult["ITEMS"])) return;

?><div class="b-detail-recommended">
<h3 class="b-h3 m-recommended">Рекомендуемые товары</h3>
<div class="b-catalog-list__line clearfix">
<?foreach($arResult["ITEMS"] as $key => $arItem){   ?>
<div class="b-catalog-list_item<?if($key == count($arResult["ITEMS"]) -1){?> m-3n<?}?>">
<? if ($arItem['ID']) { 
   $APPLICATION->IncludeComponent( 
         "bitrix:catalog.element", 
         "recommend", 
         Array( 
            "IBLOCK_TYPE" => 'catalog',
            "IBLOCK_ID" =>  $arItem["IBLOCK_ID"], 
            "ELEMENT_ID" => $arItem['ID'],
            "ELEMENT_CODE" => "",
            "SECTION_ID" => "",
            "SECTION_CODE" => "",
            "SECTION_URL" => "",
            "DETAIL_URL" => "",
            "BASKET_URL" => "/personal/basket.php",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "META_KEYWORDS" => "-", 
            "META_DESCRIPTION" => "-",
            "BROWSER_TITLE" => "-",
            "SET_TITLE" => "N",
            "SET_STATUS_404" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "PROPERTY_CODE" => array(),
            "OFFERS_LIMIT" => "0",
            "PRICE_CODE" => array("BASE", "PRICE"),
            "USE_PRICE_COUNT" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "PRICE_VAT_INCLUDE" => "Y",
            "PRICE_VAT_SHOW_VALUE" => "N",
            "PRODUCT_PROPERTIES" => array(),
            "USE_PRODUCT_QUANTITY" => "N",
            "LINK_IBLOCK_TYPE" => "",
            "LINK_IBLOCK_ID" => "",
            "LINK_PROPERTY_SID" => "",
            "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "N",
            "USE_ELEMENT_COUNTER" => "N",
            "CONVERT_CURRENCY" => "N"
         ) 
     ); 
 } 
 
 
 ?></div><?}?></div></div>