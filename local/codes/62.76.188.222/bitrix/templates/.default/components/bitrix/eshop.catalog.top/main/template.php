<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?foreach($arResult["ITEMS"] as $key => $arItem){   ?>
<div class="b-catalog-list_item <?=$arParams['CLASS']?>">
            <div class="b-product-icon"></div>
            <div class="b-catalog-list_item__image"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" /></a></div>
            <div class="b-catalog-list_item__where clearfix">
                    <div class="b-where__left">
                            <span class="b-where__icon" title="че то надо написать"></span>
                    </div>
                    <div class="b-where__right">
                            <span class="b-where__icon m-r" title="че то надо написать"></span>
                            <span class="b-where__icon m-p" title="че то надо написать"></span>
                    </div>
            </div>
            <div class="b-catalog-list_item__name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
            <div class="b-catalog-list_item__btn clearfix">
                    <div class="b-bth__right">
                            <button class="b-catalog-list_item__buy buy_" data-id="<?=$arItem["ID"]?>"><span class="b-catalog-list_item__cart">Купить</span></button>
                    </div>
                    <div class="b-bth__right m-price">
                       <?          foreach($arItem["PRICES"] as $code=>$arPrice):
                        if($arPrice["CAN_ACCESS"]):
                            ?> 
                                <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?> <span class="b-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span> 
                                <?else:?> <span class="b-price"><?=$arPrice["PRINT_VALUE"]?></span> <?endif;?>
                            <?
                        endif;
                    endforeach;
                    ?>
                           
                    </div>
            </div>
    </div>
<?}?>