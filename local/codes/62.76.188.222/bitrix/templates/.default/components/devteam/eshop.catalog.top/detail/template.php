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
           <?
           if($arItem['PROPERTIES']['SHOP']['VALUE_XML_ID'])
               $arItem['PROPERTIES']['SHOP']['VALUE_XML_ID'] = (array) $arItem['PROPERTIES']['SHOP']['VALUE_XML_ID'];
           foreach($arItem['PROPERTIES']['SHOP']['VALUE_XML_ID'] as $k=>$shop){?>
               <span class="b-where__icon <?=$shop;?>"></span>
           <?}?>
      </div> 
        </div>
        <div class="b-catalog-list_item__name" style="height: 41px;"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
        <div class="b-catalog-list_item__btn clearfix">
            <div class="b-bth__right">
                 <?if($arItem['IN_BASKET']!='Y'){?>                         
     <button class="b-catalog-list_item__buy buy_" data-id="<?=$arItem['ID']?>"><span class="b-catalog-list_item__cart">Купить</span></button>
   <?} else {?>                         
 <button class="b-catalog-list_item__buy buy_ m-in_basket"><span class="b-catalog-list_item__cart">добавлен<br>в корзину</span></button>
   <?}?>  </div>
 <div class="b-bth__right m-price"> 
<? 
foreach ($arItem["PRICES"] as $code => $arPrice): ?>
<? if ($arPrice["CAN_ACCESS"]): ?>
<? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
<div class="b-price__new"><span class="b-price"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?> </span></div>
<div class="b-price__old"><span class="b-price__small"><?= $arPrice["PRINT_VALUE"] ?> </span></div>
<? else: ?>
<span class="b-price"><?= $arPrice["PRINT_VALUE"] ?></span>
 <? endif; ?>
<? endif; ?> 
<? endforeach; ?>
 </div>
   </div>
 </div>
 <?}?>
</div> 
</div> 