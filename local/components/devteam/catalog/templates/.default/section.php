<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<aside class="b-sidebar">
    <?
    $APPLICATION->IncludeComponent(
         "devteam:catalog.sections.list", "", Array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"], 
        "SECTION_ID" => $arParams['SECTION_ID'],
            )
    );
    ?> 
    <div class="b-sidebar-filter m-sidebar-block"> 
<?   
$GLOBALS['arrFilter'] = $APPLICATION->IncludeComponent(
                            "devteam:catalog.filter",
                            "",
                            Array(  "SECTION_ID" => $arParams['SECTION_ID'],
                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                                    "FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
                                    "PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"], 
                            ),
                            $component
                        ); 
?>
    </div>
</aside><!--/.b-sidebar--> 
<section class="b-content">
    <div class="b-catalog-sort clearfix">
        <div class="b-catalog-sort-name">
            <span class="b-catalog-sort__text">Сортировать по:</span> 
   <?foreach(array("name"=>"названию",
                   "price"=>"цене" ) as $code=>$text){?> 
            <a href="?sort=<?=$code?>&order=<?
            if($arParams['ELEMENT_SORT_FIELD'] == $code){
                    if($arParams["ELEMENT_SORT_ORDER"] == "asc")
                        echo "desc";
                    else   
                        echo "asc";
                } else   
                    echo "desc"; 
                ?>" class="b-catalog-sort__link m-sort <?if($arParams['ELEMENT_SORT_FIELD'] == $code){?> b-catalog-sort__active <?} if($arParams["ELEMENT_SORT_ORDER"] == "asc"){?> m-catalog-sort__up <?}?>"><span><?=$text;?></span></a>
  
   <? } ?>         
    
        </div>
        <div class="b-catalog-sort-count">
            <select name="" class="b-chosen__no-text">
                <option value="15"<?if($_SESSION['CATALOG_CNT']==15){?> selected<?}?>>15</option>
                <option value="30"<?if($_SESSION['CATALOG_CNT']==30){?> selected<?}?>>30</option>
                <option value="45"<?if($_SESSION['CATALOG_CNT']==45){?> selected<?}?>>45</option> 
            </select>
        </div>
    <div class="b-catalog-sort-list">
        <a class="b-catalog-sort__link-list <?if($arParams['CATALOG_SECTION_TEMPLATE'] == 'grid'){?> active<?}?>" href="?view=grid"></a>
        <a class="b-catalog-sort__link-list m-image-list <?if($arParams['CATALOG_SECTION_TEMPLATE'] == 'list'){?> active<?}?>" href="?view=list"></a>
        <a class="b-catalog-sort__link-list m-list <?if($arParams['CATALOG_SECTION_TEMPLATE'] == 'table'){?> active<?}?>" href="?view=table"></a>
    </div> 
    </div> 
 <?        
     $APPLICATION->IncludeComponent(
           "devteam:compare.added", "", 
             Array( "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"]
                  )
         );

    
$realSortFields =  array('name' => 'NAME',
                         'price' => 'CATALOG_PRICE_2' );
   
$property_codes = array('PRODUCT_DAY');
$APPLICATION->IncludeComponent(
    "devteam:catalog.section", 
    $arParams['CATALOG_SECTION_TEMPLATE'], 
    Array(
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"], 
    "ELEMENT_SORT_FIELD" =>  $realSortFields[$arParams["ELEMENT_SORT_FIELD"]],
    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
    "PROPERTY_CODE" => $property_codes,
    "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
    "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
    "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
    "BASKET_URL" => $arParams["BASKET_URL"],
    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
    "FILTER_NAME" => "arrFilter",
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
    "SET_TITLE" => $arParams["SET_TITLE"],
    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
    "PAGE_ELEMENT_COUNT" => $_SESSION['CATALOG_CNT'],//$arParams["PAGE_ELEMENT_COUNT"], 
    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
    "PRICE_CODE" => $arParams["PRICE_CODE"],
    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
    "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
    "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
    "PAGER_TITLE" => $arParams["PAGER_TITLE"],
    "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
    "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
    "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
    "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
    "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
    "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
      "ADD_SECTIONS_CHAIN"=>'Y'
     ), $component
);
?>           
 
</section>