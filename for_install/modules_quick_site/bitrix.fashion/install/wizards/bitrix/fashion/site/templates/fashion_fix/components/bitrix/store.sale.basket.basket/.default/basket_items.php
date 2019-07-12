<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo GetMessage("TEST_END");
	return;
}?>
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
<div id="id-cart-list" class="cart-items">
    <table class="cart-items" cellspacing="0">
    <thead>
        <tr>
            <?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
                <td colspan="2" class="cart-item-name"><?= GetMessage("SALE_NAME")?></td>
            <?endif;?>
            <?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-quantity"><?= GetMessage("SALE_QUANTITY")?></td>
            <?endif;?>
            <?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-price"><?= GetMessage("SALE_PRICE")?></td>
            <?endif;?>
            <?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-price"><?= GetMessage("SALE_VAT")?></td>
            <?endif;?>
            <?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></td>
            <?endif;?>
            <?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></td>
            <?endif;?>
            <?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-weight"><?= GetMessage("SALE_WEIGHT")?></td>
            <?endif;?>
            <td class="cart-item-amount">
                <?= GetMessage("TOTAL_PRICE")?>
            </td>
        </tr>
    </thead>
    <tbody>
    <?
    $i=0;
    foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
    {
        ?>
        <tr>

            <?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-image">
                    <div class="wrap">
                        <?if ($arBasketItems["OFFER"]["models_hit"] || $arBasketItems["OFFER"]["models_new"] || $arBasketItems["OFFER"]["models_sale"]) {?>
                        <ul class="shortcuts">
                            <?if ($arBasketItems["OFFER"]["models_hit"]) {?><li class="hit show"><?=GetMessage("HIT")?></li><?}?>
                            <?if ($arBasketItems["OFFER"]["models_new"]) {?><li class="new show"><?=GetMessage("NEW")?></li><?}?>
                            <?if ($arBasketItems["OFFER"]["models_sale"]) {?><li class="discount show"><?=GetMessage("SALE")?></li><?}?>
                        </ul>
                        <?}?>
                        <?$img = CFile::ResizeImageGet($arBasketItems["OFFER"]["PROPERTY_ITEM_MORE_PHOTO_VALUE"], array('width'=>150, 'height'=>192), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                        <div class="image"><?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0){?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"></a><?}?><img itemprop="image" src="<?=$img["src"]?>" width="<?=$img["width"]?>" height="<?=$img["height"]?>" alt="<?=$arBasketItems["NAME"]?>" /></div>
                    </div>
                </td>
                <td class="cart-item-name">
                    <h3><?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0){?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?}?><?=$arBasketItems["NAME"]?><?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0){?></a><?}?></h3>
                    <?if (strlen($arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_DETAIL_PICTURE"]) > 0) {
                        $colorImg = CFile::ResizeImageGet($arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_DETAIL_PICTURE"], array('width'=>24, 'height'=>16), BX_RESIZE_IMAGE_EXACT, true);
                        ?>
                    <p><?=GetMessage("SIZE")?> <?=$arBasketItems["OFFER"]["PROPERTY_ITEM_SIZE_NAME"]?>, <?=GetMessage("COLOR")?> <span class="color" title="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" style="background-color:#<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_PROPERTY_HEX_VALUE"]?>"><img src="<?=$colorImg["src"]?>" title="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" alt="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" width="<?=$colorImg["width"]?>" height="<?=$colorImg["height"]?>" /></span></p>
                    <?} else {?>
                    <p><?=GetMessage("SIZE")?> <?=$arBasketItems["OFFER"]["PROPERTY_ITEM_SIZE_NAME"]?>, <?=GetMessage("COLOR")?> <span class="color" title="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" style="background-color:#<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_PROPERTY_HEX_VALUE"]?>"></span></p>
                    <?}?>
                </td>
            <?endif;?>
            <?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-quantity">
                    <input maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3">
                    <?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
                    <br /><br /><a class="cart-delete-item" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"><?=GetMessage("SALE_DELETE")?></a>
                    <?endif;?>
                </td>
            <?endif;?>
            <?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-price">
                    <?if ($arBasketItems["OFFER"]["models_sale"]) {?>
                    <span class="oldprice"><?=CSiteFashionStore::formatMoney($arBasketItems["BASE_PRICE"]["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></span><br/>
                    <span class="newprice"><?=CSiteFashionStore::formatMoney($arBasketItems["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></span>
                    <?} else {?>
                    <?=CSiteFashionStore::formatMoney($arBasketItems["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></span>
                    <?}?>
                </td>
            <?endif;?>
            <?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-price"><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
            <?endif;?>
            <?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-type"><?=$arBasketItems["NOTES"]?></td>
            <?endif;?>
            <?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-discount"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
            <?endif;?>
            <?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-weight"><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
            <?endif;?>
            <td class="cart-item-amount">
                <?=CSiteFashionStore::formatMoney($arBasketItems["TOTAL"])?> <span class="rub"><?=GetMessage("RUB")?></span>
            </td>
        </tr>
        <?
        $i++;
    }
    ?>
    </tbody>
    </table>
    <div class="cart-ordering">
        <div class="cart-order-amount">
            <?if ($arResult["TOTAL_SUM"] > $arResult["allSum"]):?>
            <p class="order-discount"><?=GetMessage("TOTAL_DISCOUNT")?> <?=CSiteFashionStore::formatMoney($arResult["TOTAL_SUM"] - $arResult["allSum"])?> <span class="rub"><?=GetMessage("RUB")?></span></p>
            <?endif;?>
            <p class="order-amount"><?=GetMessage("TOTAL")?> <?=CSiteFashionStore::formatMoney($arResult["allSum"])?> <span class="rub"><?=GetMessage("RUB")?></span></p>
        </div>
        <div class="cart-buttons">
            <input type="hidden" value="<?echo GetMessage("SALE_UPDATE")?>" name="BasketRefresh" />
            <input type="submit" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder" id="basketOrderButton2" />
        </div>
    </div>
</div>

<?if ($arParams["HIDE_COUPON"] != "Y"):?>
<div class="cart-ordering">
    <div class="cart-code">
        <input <?if(empty($arResult["COUPON"])):?>onclick="if (this.value=='<?=GetMessage("SALE_COUPON_VAL")?>')this.value=''" onblur="if (this.value=='')this.value='<?=GetMessage("SALE_COUPON_VAL")?>'"<?endif;?> value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("SALE_COUPON_VAL")?><?endif;?>" name="COUPON">
    </div>
</div>
<?endif;?>
<script>$("#id-cart-list input[type='text']").change(function(){$("#cart-form").submit();});</script>
<?else:
    echo ShowNote(GetMessage("SALE_NO_ACTIVE_PRD"));
endif;?>