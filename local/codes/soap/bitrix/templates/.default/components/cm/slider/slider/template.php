<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<section class="b-content"> 
 <div class="b-tab-main">
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
                <div class="b-slider-wrapper" id="b-slider_<?=$j;?>">
                    <a href="#" class="b-slider__control m-prev"  title="назад"></a>
                    <div class="b-slider">                
                        <?
                        $APPLICATION->IncludeComponent(  "bitrix:eshop.catalog.top", "", Array(
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
 <div class="b-popup m-popup__orange" id="b-wishlist__add">
		<div class="b-popup__wrapper">
			<div class="b-wishlist__select">
				<select name="cat" id="cat_list">
				<?	if($USER->GetID()){
						$arFilter = Array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', 'CREATED_BY'=>$USER->GetID());
						$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('ID',"NAME"));
						while($ar_result = $db_list->GetNext())
						{?>
							<option value="<?=$ar_result['ID']?>"><?=$ar_result['NAME']?></option>
						<?}
					}
				
				?>
				</select>
			</div>
			<div class="b-login__user"><input type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><a id='wishlist_add_el' el='3' class="b-button__fast">OK</a></div>
		</div>
	</div>
</section>  