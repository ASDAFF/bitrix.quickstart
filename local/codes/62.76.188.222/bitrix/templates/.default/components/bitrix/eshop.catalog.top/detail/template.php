<div class="b-detail-recommended">
<h3 class="b-h3 m-recommended">Рекомендуемые товары</h3>
<div class="b-catalog-list__line clearfix">
 
    
<?foreach($arResult["ITEMS"] as $key => $arItem){   ?>
    <div class="b-catalog-list_item<?if($key == count($arResult["ITEMS"]) -1){?> m-3n<?}?>">
        <div class="b-catalog-list_item__image"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img alt="" src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>"></a></div>
        <div class="b-catalog-list_item__where clearfix">
                <div class="b-where__left">
                        <span title="че то надо написать" class="b-where__icon"></span>
                </div>
                <div class="b-where__right">
                        <span title="че то надо написать" class="b-where__icon m-r"></span>
                        <span title="че то надо написать" class="b-where__icon m-p"></span>
                </div>
        </div>
        <div class="b-catalog-list_item__name" style="height: 41px;"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
        <div class="b-catalog-list_item__btn clearfix">
                <div class="b-bth__right">
                        <button class="b-catalog-list_item__buy buy_" data-id="<?=$arItem["ID"]?>"><span class="b-catalog-list_item__cart">Купить</span></button>
                </div>
                <div class="b-bth__right m-price">
                        <span class="b-price">12 333</span>
                </div>
        </div>
    </div>
    <?}?>
</div><!--/.b-catalog-list__line-->
</div>