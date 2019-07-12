<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="order-item">
    <div class="order-title">
        <div class="order-title-inner">
            <span><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></span>
        </div>
    </div>
    <div class="order-info">
<table class="cart-items">
    <thead><tr>
        <td class="cart-item-name"><?=GetMessage("SOA_TEMPL_SUM_NAME")?></td>
        <td class="cart-item-price"><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?></td>
        <td class="cart-item-quantity"><?=GetMessage("SOA_TEMPL_SUM_QUANTITY")?></td>
        <td class="cart-item-price"><?=GetMessage("SOA_TEMPL_SUM_PRICE")?></td>
    </tr></thead>
    <tbody>
    <?
    foreach($arResult["BASKET_ITEMS"] as $arBasketItems)
    {
        ?>
        <tr>
            <td class="cart-item-name">
                <?=$arBasketItems["NAME"]?>
                <?if (strlen($arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_DETAIL_PICTURE"]) > 0) {
                        $colorImg = CFile::ResizeImageGet($arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_DETAIL_PICTURE"], array('width'=>24, 'height'=>16), BX_RESIZE_IMAGE_EXACT, true);
                    ?>
                <p><?=GetMessage("SIZE")?> <?=$arBasketItems["OFFER"]["PROPERTY_ITEM_SIZE_NAME"]?>, <?=GetMessage("COLOR")?> <span class="color" title="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" style="background-color:#<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_PROPERTY_HEX_VALUE"]?>"><img src="<?=$colorImg["src"]?>" title="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" alt="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" width="<?=$colorImg["width"]?>" height="<?=$colorImg["height"]?>" /></span></p>
                <?} else {?>
                <p><?=GetMessage("SIZE")?> <?=$arBasketItems["OFFER"]["PROPERTY_ITEM_SIZE_NAME"]?>, <?=GetMessage("COLOR")?> <span class="color" title="<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" style="background-color:#<?=$arBasketItems["OFFER"]["PROPERTY_ITEM_COLOR_PROPERTY_HEX_VALUE"]?>"></span></p>
                <?}?>
            </td>
            <td class="cart-item-price"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
            <td class="cart-item-quantity"><?=$arBasketItems["QUANTITY"]?></td>
            <td class="cart-item-price"><?=$arBasketItems["PRICE_FORMATED"]?></td>
        </tr>
        <?
    }
    ?></tbody>
    <tfoot><tr>
        <td align="right"><?=GetMessage("SOA_TEMPL_SUM_SUMMARY")?></td>
        <td align="right"><?=$arResult["ORDER_PRICE_FORMATED"]?></td>
        <td></td><td></td>
    </tr>
    <?
    if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
    {
        ?>
        <tr>
            <td align="right"><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?><?if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0):?> (<?echo $arResult["DISCOUNT_PERCENT_FORMATED"];?>)<?endif;?>:</td>
            <td align="right"><?echo $arResult["DISCOUNT_PRICE_FORMATED"]?></td>
            <td></td><td></td>
        </tr>
        <?
    }
    if(!empty($arResult["arTaxList"]))
    {
        foreach($arResult["arTaxList"] as $val)
        {
            ?>
            <tr>
                <td align="right"><?=$val["NAME"]?> <?=$val["VALUE_FORMATED"]?>:</td>
                <td align="right"><?=$val["VALUE_MONEY_FORMATED"]?></td>
                <td></td><td></td>
            </tr>
            <?
        }
    }
    if (doubleval($arResult["DELIVERY_PRICE"]) > 0)
    {
        ?>
        <tr>
            <td align="right"><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></td>
            <td align="right"><?=$arResult["DELIVERY_PRICE_FORMATED"]?></td>
            <td></td><td></td>
        </tr>
        <?
    }
    ?>
    <tr>
        <td align="right"><b><?=GetMessage("SOA_TEMPL_SUM_IT")?></b></td>
        <td align="right"><b><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></b></td>
        <td></td><td></td>
    </tr>
    <?
    if (strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0)
    {
        ?>
        <tr>
            <td align="right"><b><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></b></td>
            <td align="right"><?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?></td>
            <td></td><td></td>
        </tr>
        <?
    }
    ?></tfoot>
</table>

    </div>
</div>

<div class="order-item">
    <div class="order-title">
        <div class="order-title-inner">
            <span><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?></span>
        </div>
    </div>
    <div class="order-info">
        <div align="center">
            <textarea rows="7" style="width:100%" name="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
        </div>
    </div>
</div>