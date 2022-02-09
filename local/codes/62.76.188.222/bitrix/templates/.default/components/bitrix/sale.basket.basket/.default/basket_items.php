<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
 
  <?if(count($arResult["ITEMS"]["AnDelCanBuy"])){?> <table class="b-basket-table">
            <thead>
                <tr>
                    <td>Название</td>
                    <td></td>
                    <td>Наличие</td>
                    <td>Цена</td>
                    <td>Количество</td>
                    <td>Стоимость</td>
                    <td>Действие</td>
                </tr>
            </thead>
            <tbody>
                <?foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems){?>
                <tr>
                    <td class="b-basket-table__image"><a href="<?=$arBasketItems["DETAIL_PAGE_URL"];?>"><img alt="" src="<?=$arBasketItems["PICTURE"]['src'];?>"></a></td>
                    <td class="b-basket-table__name"><a href="<?=$arBasketItems["DETAIL_PAGE_URL"];?>"><?=$arBasketItems["NAME"] ?></a></td>
                    <td class="b-basket-table__where">
               
     <?
   
     foreach($arBasketItems['SHOP'] as $k=>$shop){?>
        <span title="<?=$shop["VALUE_ENUM"]?>" class="b-where__icon <?=$shop["VALUE_XML_ID"]?>"></span>
    <?}?>
                   
                        
                    </td> 
                    <td class="b-basket-table__price"><span class="b-price m-no_margin"><?=$arBasketItems["PRICE_FORMATED"]?></span></td>
                    <td class="b-basket-table__count">
                        <span class="b-basket-item-count clearfix">
                            <button data-id="<?=$arBasketItems["ID"]?>" class="b-basket-item-count__btn m-dec">−</button>
                            <input type="text" name="QUANTITY_<?=$arBasketItems["ID"]?>" value="<?=$arBasketItems["QUANTITY"]?>" id="QUANTITY_<?=$arBasketItems["ID"]?>" class="b-basket-item-count__text">
                            <button data-id="<?=$arBasketItems["ID"]?>" class="b-basket-item-count__btn m-inc">+</button>
                        </span>
                    </td>
                    <td class="b-basket-table__total"><span class="b-price m-no_margin"><?=$arBasketItems["COST"]?></span></td>
                    <td class="b-basket-table__action">
                        <a class="b-basket-link__wishlist delay_" data-id="<?=$arBasketItems["ID"]?>" href="#">Отложить</a>
                        <a title="Удалить" class="b-basket-link__del" data-id="<?=$arBasketItems["ID"]?>" href="#"></a>
                    </td> 
                </tr>
           <?}?>
            </tbody>
        </table>

        <div class="b-total-price clearfix">
            <div class="b-total-price-coupon">
                <input type="text" name="COUPON" value="<?=$arResult["COUPON"]?>" placeholder="код купона для скидки" class="b-text"></div>
            <div class="b-total-price-text">
                <span class="b-total-price-text__text">Общая стоимость:</span>
                <span class="b-price"><?=$arResult["allSum_FORMATED"]?></span>
            </div>
        </div>     


        <div class="b-checkout-button">
            <input type="submit" class="b-button" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton2"></div>
  <?} else {
      
      ?>

<p>Готовых к заказу товаров нет</p>

<? } ?>  