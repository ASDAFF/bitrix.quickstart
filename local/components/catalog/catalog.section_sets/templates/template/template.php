<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? //echo "<pre>", print_r($arResult), "</pre>";?>
<script>
$(function(){
    $('.b-button__fast').live('click', function(){
 
       $('.b-fast_order').remove();
 
       $('#tabs_list').append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Вам перезвонит оператор и оформит заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','-535px') 
                         .css('left','530px')
                         .css('z-index','5').css('height','81px').css('position','relative');
 
       return false;
    });
});  
</script>

<div id='tabs_list' class='b-tab-main'>
<div class="b-tab-head">
<? foreach ($arResult["ITEMS"] as $key => $item) {
        //$i = 1;
        ?>
        <a href="#<?=$key?>" class="b-tab-head__link <?if ($key == 0): echo 'active'; endif;?>"><?=$item["NAME"]?></a>
        <?
        //echo "<pre>", print_r($item), "</pre>";
}
?>
  </div>
<button style='display:none' class="b-button__fast" id="b-fast_order"></button>
<? foreach ($arResult["ITEMS"] as $key => $item) {
        $i = 1;
        //echo "<pre>", print_r($key), "</pre>";
?>
<?$ar_res = CIBlockElement::GetList( Array("SORT"=>"ASC"), 
                                     Array("ID" => $item["PRODUCT_ID"], 
                                           "IBLOCK_ID" => "1"),
                                     false , 
                                     false , 
                                     Array("ID","IBLOCK_ID", "NAME",
                                           "PREVIEW_PICTURE",
                                           "DETAIL_PAGE_URL", "PROPERTY_model",
                                           "PROPERTY_type", "PROPERTY_article"));   
$props = $ar_res->GetNext();
 
?>
    <div id="<?=$key?>" class="b-tab__body <?if ($key == 0): echo 'active'; endif;?>">
        <div class="b-tab">
            <div class="b-set clearfix">
                <div class="b-set-left">
                    <!-- в будущем для каждого класса m-set__item-N позицию -->
                    <!-- нужно определять ручками, так как блоки могут быть разными -->
                    <? foreach ($arResult["ITEMS"][$key]["GOOD"] as $good) {
                            //echo "<pre>", print_r($good["PRICES"]["price"]), "</pre>";?>
                        <div class="b-slider__item b-set-item m-set__item-<?=$i;?>">
                            <div class="b-slider__text">
<?if(is_array($good["PREVIEW_PICTURE"])):?>
 <div class="b-slider__image"><img data-id="#item-<?=$good['ID']?>" src="<?=$good["PREVIEW_PICTURE"]["SRC"]?>" alt="" /></div>
<?else:?>
<div class="b-slider__image"><img data-id="#item-<?=$good['ID']?>" border="0" src="/images/img-element__image.png"  alt="<?=$good["NAME"]?>" title="<?=$good["NAME"]?>" /></div>
<?endif?>
                               
                                <div class="b-slider__link"><?=$props["PROPERTY_TYPE_VALUE"]?> <a href="<?=$good["DETAIL_PAGE_URL"]?>"><?=cutString($good["NAME"],27)?></a></div>
                                <div class="b-slider__price"><?=$good["PRICES"]["price"]["PRINT_VALUE"]?></div>
                            </div>


                            <div class="b-slider__btn clearfix">
                                <div class="fust_order" style="display:none;">
                                    <form action="/includes/fust_order.php" name="fust_order" method="post">
                                        <div class="b-footer-form">
                                            <input type="text" class="b-footer-form__text" placeholder="Быстрый заказ" name="phone"/>
                                            <input type="hidden" name="order" value=""/>
                                            <label>Быстрый заказ</label>                        
                                            <input type="submit" value="send" id="fust_order-submit"/>
                                        </div>
                                    </form>
                                </div>
                                <button class="b-button__fast" id="b-fast_order">Быстрый<br/>заказ</button>
                                <a el='<?=$good['ID']?>' class="m-wishlist__add b-icon" href="#b-wishlist__add" title="<?echo GetMessage("WISHLIST")?>"></a>
                                <?if($good["CAN_BUY"]):?>
                                    <noindex>
                                        <a class="b-icon m-icon__buy" id="<?=$good['ID']?>" href="<?echo $good["ADD_URL"]?>" rel="nofollow" title="<?echo GetMessage("CATALOG_ADD")?>"></a>
                                    </noindex>
                                    <?endif;?>
                                <?//if($arParams["DISPLAY_COMPARE"]):?>
                                    <noindex>
                                        <a href="<?if(array_key_exists($good['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "#b-compare__added";}else{echo "#b-compare__add";}?>" id="<?=$good['ID']?>" rel="<?if(!array_key_exists($arElement['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "/catalogue/?action=ADD_TO_COMPARE_LIST&id=".$good['ID']."";}?>"  class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
                                    </noindex>
                                    <?//endif?>
                            </div>

                        </div>
                        <?$i++;}?>
                </div>
                <div class="b-set-right">
                    <div class="b-set-buy__all">Купить весь набор <button class="b-button__fast m-orange">Быстрый<br>заказ</button></div>
                    <div class="b-set-buy_one">
                    <? foreach ($arResult["ITEMS"][$key]["GOOD"] as $good) {?>
                        <div class="b-set-buy_one__item"><?=$good["NAME"]?>&nbsp;<?=$good["PRICES"]["price"]["PRINT_VALUE"]?></div>
                        <?}?>
                    </div>
                    <div class="b-set-buy_price">
                    <?if ($item["PRICES"]["price"]["DISCOUNT_VALUE"] < $item["PRICES"]["price"]["VALUE"]):?>
                        <div class="b-set-buy_price__old">
                            <div class="b-detail-sidebar__old_price"><span><?=$item["PRICES"]["price"]["PRINT_VALUE"];?></span></div>
                        </div>
                        <div class="b-set-buy_price__new"><?=$item["PRICES"]["price"]["PRINT_DISCOUNT_VALUE"];?></div>
                    <?else:?>
                    <div class="b-set-buy_price__new"><?=$item["PRICES"]["price"]["PRINT_VALUE"];?></div>
                    <?endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?}?>
</div>
<div class="b-popup m-popup__orange" id="b-compare__added">
	<div class="b-popup__wrapper">
		<h2 class="b-popup-compare__h2">Товар уже добавлен к сравнению.</h2>
		<a class="b-button__fast_n" href="/catalogue/compare.php">Перейти к сравнению</a>
	</div>
</div>
<!--div class="b-popup m-popup__orange" id="b-wishlist__add">
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
			<div class="b-login__user"><input id='new_wish_field' type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><a id='wishlist_add_el' el='3' class="b-button__fast_n">OK</a></div>
		</div>
	</div-->