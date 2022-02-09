<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    $i=0;
    foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
    {

    ?>
    <div class="b-cart__item clearfix">
        <?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
            <div class="b-cart__image"><img src="<?=CFile::GetPath($arBasketItems["PREVIEW_PICTURE"]);?>" alt="" /></div>
            <div class="b-cart__text">
                <? $ar_res = CIBlockElement::GetList( Array("SORT"=>"ASC"), Array("ID" => $arBasketItems["PRODUCT_ID"], "IBLOCK_ID" => "1"),false , false , Array("ID", "PROPERTY_model", "PROPERTY_type", "PROPERTY_article"))->GetNextElement();
                    $props = $ar_res->GetFields();
                ?>
                <div class="b-cart__link"><?=$props["PROPERTY_TYPE_VALUE"]?> 
                    <?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
                        ?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
                                endif;?>
                        <?=$arBasketItems["NAME"] ?> <?=$props["PROPERTY_MODEL_VALUE"]?> (<?=$props["PROPERTY_ARTICLE_VALUE"]?>)
                    <?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?></a><?endif;?>
                </div>
                <div class="b-cart__info"><?=$arBasketItems["PREVIEW_TEXT"]?></div>
            </div>
            <?endif;?>

        <div class="b-cart__price">
            <div class="clearfix">
                <?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
                    <div class="b-cart__price_left">
                        <div class="b-slider__price m-price__16px"><?=$arBasketItems["PRICE_FORMATED"]?></div>
                        <div class="b-slider__price_clearing">Безнал<br /><b>19 540.8.–</b></div>                            
                    </div>
                    <?endif;?>
                <?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
                    <div class="b-cart__price_right">
                        <button class="b-cart-count__btn m-dec" data-value="<?=$arBasketItems["PRODUCT_ID"]?>"></button>
                        <span class="b-cart-count__count" id="item_count_<?=$arBasketItems["PRODUCT_ID"]?>"><?=$arBasketItems["QUANTITY"]?></span>
                        <input type="hidden" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" id="item_hidden_<?=$arBasketItems["PRODUCT_ID"]?>">
                        <button class="b-cart-count__btn m-inc" data-value="<?=$arBasketItems["PRODUCT_ID"]?>"></button>
                    </div>
                    <?endif;?>                            
            </div>
        </div>
        <div class="b-cart__total">
            <div class="clearfix">
                <div class="b-cart__total_left">
                    <div class="b-slider__price m-price__16px"><?=$arBasketItems["PRICE_FORMATED"]?></div>
                    <div class="b-slider__price_clearing">Безнал<br /><b>19 540.8.–</b></div>                            
                </div>
                <div class="b-cart__total_right">
                    <?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
                        <a class="b-button__delete m-cart__delete" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"></a><br>
                        <?endif;?>
                </div>
            </div>
        </div>
    </div>
    <?
        $i++;
    }
?>


<div class="b-total clearfix">
    <div class="b-total__text"><h2>Итого</h2>без стоимости доставки</div>
    <div class="b-total__price">
        <div class="b-slider__price">509 990.–</div>
        <div class="b-slider__price_clearing">Безнал<br /><b>19 540.8.–</b></div>                            
    </div>
</div>
<div class="b-cart__btn m-btn__line clearfix">
                        <div class="b-cart__btn_right m-right">
                            <button class="b-button">Сохранить корзину</button>
                            <button class="b-button">Быстрый заказ</button>
                        </div>
                    </div>
 

