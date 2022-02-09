<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h3 class="b-h3 m-checkout__h3">Состав заказа</h3>
<table class="b-basket-table">
    <thead>
        <tr>
            <td>Название</td>
            <td>Свойства</td>
            <td>Скидка</td>
            <td>Вес</td>
            <td>Количество</td>
            <td>Цена</td>
        </tr>
    </thead>
    <tbody> 
        <? foreach ($arResult["BASKET_ITEMS"] as $arBasketItems) {?>
            <tr>
                <td><a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>"><?=$arBasketItems["NAME"] ?></a></td>
                <td><?
        foreach ($arBasketItems["PROPS"] as $val) {
            echo $val["NAME"] . ": " . $val["VALUE"] . "<br />";
        }
            ?></td>
                <td><?= $arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"] ?></td>
                <td><?= $arBasketItems["WEIGHT_FORMATED"] ?></td>
                <td><?= $arBasketItems["QUANTITY"] ?></td>
                <td><span class="b-price m-no_margin"><?= $arBasketItems["PRICE_FORMATED"] ?></span></td>
            </tr>
        <? } ?>
    </tbody>
</table>
 
	<div class="b-total-price clearfix">
            <div class="b-total-price-text">
                <table class="b-total-price__table">
                    <tbody><tr>
                            <td class="b-total-price__title">Общий вес:</td>
                            <td class="b-total-price__value"><?= $arResult["ORDER_WEIGHT_FORMATED"] ?></td>
                        </tr>
                        <tr>
                            <td class="b-total-price__title">Товаров на:</td>
                            <td class="b-total-price__value"><?= $arResult["ORDER_PRICE_FORMATED"] ?></td>
                        </tr>
                        <? if (doubleval($arResult["DELIVERY_PRICE"]) > 0) {
                            ?>
                            <tr>
                                <td class="b-total-price__title">Доставка:</td>
                                <td class="b-total-price__value"><?= $arResult["DELIVERY_PRICE_FORMATED"] ?></td>
                            </tr><? } ?>

                        <tr>
                            <td class="b-total-price__title">Итого:</td>
                            <td class="b-total-price__value"><span class="b-price m-no_margin"><?= $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?></span></td>
                        </tr>
                    </tbody></table>
            </div>
        </div> 