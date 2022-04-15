<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?// echo "<pre>", print_r($arResult), "</pre>";?>
<? foreach ($arResult["ITEMS"] as $key => $item) {
        $i = 1;?>
    <div class="b-tab-body">
        <div class="b-tab">
            <div class="b-set clearfix">
                <div class="b-set-left">
                    <!-- в будущем для каждого класса m-set__item-N позицию -->
                    <!-- нужно определять ручками, так как блоки могут быть разными -->
                    <? foreach ($arResult["ITEMS"][$key]["GOOD"] as $good) {
                            //echo "<pre>", print_r($good["PRICES"]["price"]), "</pre>";?>
                        <div class="b-slider__item b-set-item m-set__item-<?=$i;?>">
                            <div class="b-slider__text">
                                <div class="b-slider__image"><img src="<?=$good["PREVIEW_PICTURE"]["SRC"]?>" alt="" /></div>
                                <div class="b-slider__link"><a href="<?=$good["DETAIL_PAGE_URL"]?>"><?=$good["NAME"]?></a></div>
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
                                <span class="b-icon" title="<?echo GetMessage("WISHLIST")?>"></span>
                                <?if($good["CAN_BUY"]):?>
                                    <noindex>
                                        <a class="b-icon m-icon__buy" id="<?=$good['ID']?>" href="<?echo $good["ADD_URL"]?>" rel="nofollow" title="<?echo GetMessage("CATALOG_ADD")?>"></a>
                                    </noindex>
                                    <?endif;?>
                                <?if($arParams["DISPLAY_COMPARE"]):?>
                                    <noindex>
                                        <a href="<?echo $good["COMPARE_URL"]?>#b-compare__add" rel="nofollow" class="b-icon m-icon__compare" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
                                    </noindex>
                                    <?endif?>
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
                        <div class="b-set-buy_price__old">
                            <div class="b-detail-sidebar__old_price"><span>120 00</span>.–</div>
                        </div>
                        <div class="b-set-buy_price__new"><?=$item["PRICES"]["price"]["PRINT_VALUE"];?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?}?>
