<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<!-- SLIDER START -->
<!-- data-timer="2500" - время в м.сек -->
<div class="slider-element" data-timer="5500">
<!-- TABS START -->
    <div class="b-tab-head">
	<!-- PHP GENERATE CONTENT START -->
        <?foreach ($arParams['PROPERTY_CODE'] as $code){ 
            $j++;?>
            <a href="#" class="b-tab-head__link<?if($j==1){?> active<?}?>"><?=$arResult['PROPS'][$code]['NAME']?></a> 
        <?}?>
	<!-- PHP GENERATE CONTENT END -->
    </div>
	<!-- TABS END -->

	<!-- PHP GENERATE TAB START -->
    <?
    $j = 0;
    foreach ($arParams['PROPERTY_CODE'] as $code) {
        $j++;
        ?>
        <div class="b-tab__body<?if($j==1){?> active<?}?>"> 
            <div class="b-tab"> 
                <div class="b-slider-wrapper">
                    <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                    <div class="b-slider">                
                        <?
                        $APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "", Array(
                            "DISPLAY_IMG_WIDTH" => "215",
                            "DISPLAY_IMG_HEIGHT" => "197",
                            "SHARPEN" => "30", 
                            "IBLOCK_TYPE_ID" => $arParams['IBLOCK_TYPE'],
                            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                            "ELEMENT_SORT_FIELD" => "RAND",
                            "ELEMENT_SORT_ORDER" => "asc",
                            "ACTION_VARIABLE" => "action",
                            "PRODUCT_ID_VARIABLE" => "id",
                            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                            "PRODUCT_PROPS_VARIABLE" => "prop",
                            "SECTION_ID_VARIABLE" => "SECTION_ID",
                            "DISPLAY_COMPARE" => "N",
                            "ELEMENT_COUNT" => "30", 
                            "FLAG_PROPERTY_CODE" => $code,
                            "OFFERS_LIMIT" => "0",
                            "PRICE_CODE" => array('price', 'clearing'), 
                            "USE_PRICE_COUNT" => "N",  
                            "SHOW_PRICE_COUNT" => "1", 
                            "PRICE_VAT_INCLUDE" => "Y", 
                            "PRODUCT_PROPERTIES" => array(), 
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "3600",
                            "CACHE_GROUPS" => "Y" )
                        );
                        ?> 
                    </div>
                    <a href="#" class="b-slider__control m-next" title="вперед"></a>
                </div>
            </div>
        </div>
    <? } ?>                     
</div>