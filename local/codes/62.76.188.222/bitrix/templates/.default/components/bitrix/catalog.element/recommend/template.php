 <div class="b-catalog-list_item__image"><a href="<?=$arResult["DETAIL_PAGE_URL"]?>"><img alt="<?=$arResult["NAME"]?>" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>"></a></div>
        <div class="b-catalog-list_item__where clearfix"> 
        <div class="b-where__right">  <? 
           if($arResult['PROPERTIES']['SHOP']['VALUE_XML_ID'])
               $arResult['PROPERTIES']['SHOP']['VALUE_XML_ID'] = (array) $arResult['PROPERTIES']['SHOP']['VALUE_XML_ID'];
           foreach($arResult['PROPERTIES']['SHOP']['VALUE_XML_ID'] as $k=>$shop){?>
               <span class="b-where__icon <?=$shop;?>"></span>
           <?}?>
        </div> 
        </div> 
        <div class="b-catalog-list_item__name" style="height: 41px;"><a href="<?=$arResult["DETAIL_PAGE_URL"]?>"><?=$arResult["NAME"]?></a></div>
        <div class="b-catalog-list_item__btn clearfix">
            <div class="b-bth__right">
        <?if($arResult['IN_BASKET']!='Y'){?>                          
     <button class="b-catalog-list_item__buy buy_" data-id="<?=$arResult['ID']?>"><span class="b-catalog-list_item__cart">Купить</span></button>
   <?} else {?>                         
 <button class="b-catalog-list_item__buy buy_ m-in_basket"><span class="b-catalog-list_item__cart">добавлен<br>в корзину</span></button>
   <?}?>  </div>
 <div class="b-bth__right m-price"> 
<?  
foreach ($arResult["PRICES"] as $code => $arPrice): ?>
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