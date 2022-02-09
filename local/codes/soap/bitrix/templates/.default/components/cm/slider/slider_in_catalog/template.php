<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

    <div class="b-tab-head">
        <?  
        $arrStyles = array(1 => 'one', 'two', 'three');
    
        foreach ($arParams['PROPERTY_CODE'] as $code) { 
            $j++;
            ?>
            <a href="#b-slide__<?=$arrStyles[$j];?>" class="b-tab-head__link<?if($j==1){?> active<?}?>" data-slider="Y"><?=$arResult['PROPS'][$code]['NAME']?></a> 
            <?}?>
    </div>
    <script type="text/javascript">
        $(function() {
            var slide_last = 0, slide_length = $("#b-slider_1").find(".b-slider").children().length;
            $("#b-slider_1").slides({
                container: "b-slider",
                prev: "m-prev",
                next: "m-next",
                paginationClass: "b-pager",
                animationStart: function(i) { 
                    if(slide_last == slide_length && i == "next") {
                        $(".b-tab-head__link").eq(1).click();
                    }
                },
                animationComplete: function(i) {
                    slide_last = i;
                }
            });
        });
    </script> 
    <?
    $j = 0;
    foreach ($arParams['PROPERTY_CODE'] as $code) {
        $j++;
        ?>
        <div id="b-slide__<?=$arrStyles[$j];?>" class="b-tab__body<?if($j==1){?> active<?}?>"> 
            <div class="b-tab"> 
                <div class="b-slider-wrapper m-small-slider" id="b-slider_<?=$j;?>">
                    <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                    <div class="b-slider">                
                        <?
                        $APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "slider_catalog_inner", array(
	"IBLOCK_TYPE_ID" => "catalog",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ELEMENT_SORT_FIELD" => "RAND",
	"ELEMENT_SORT_ORDER" => "asc",
	"ELEMENT_COUNT" => "30",
	"FLAG_PROPERTY_CODE" => $code,
	"OFFERS_LIMIT" => "0",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"DISPLAY_COMPARE" => "N",
	"PRICE_CODE" => array(
		0 => "price",
		1 => "clearing",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	)
	),
	false
);
                        ?> 
                    </div>
                    <a href="#" class="b-slider__control m-next" title="вперед"></a>
                </div>
            </div>
        </div>
    <? } ?>                     