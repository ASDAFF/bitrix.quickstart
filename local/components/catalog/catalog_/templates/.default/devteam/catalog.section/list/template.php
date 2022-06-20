<div class="b-catalog-list"> 
<?foreach($arResult["ITEMS"] as $cell=>$arElement){
    $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
    ?>  
<div class="b-catalog-list_list clearfix" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
    <div class="b-catalog-list_list__image"><a href="<?=$arElement['DETAIL_PAGE_URL'];?>"><img alt="<?=$arElement['NAME'];?>" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>"></a></div>
    <div class="b-catalog-list_list__info"> 
            <a href="<?=$arElement['DETAIL_PAGE_URL'];?>"><?=$arElement['NAME'];?></a>
            <div>
                <?
                $n = 0; 
                foreach($arElement['DISPLAY_PROPERTIES'] as $key => $prop){?>
                     <?=$prop["NAME"]?>: &mdash; <?=$prop["VALUE"]?><?if($n++ < count($arElement['DISPLAY_PROPERTIES']) -1 ){?>, <?}?> 
                <?}?>
          
            </div>
    </div>
    <div class="b-catalog-list_list__where">
          <?foreach($arElement['PROPERTIES']['SHOP']["VALUE_XML_ID"] as $k=>$shop){?>
                                <span class="b-where__icon <?=$shop;?>"></span>
                            <?}?> 
<?if($arElement['IN_COMPARE']!='Y'){?>                          
   <a title="Сравнить" class="b-where__icon add2compare_" data-id="<?=$arElement['ID'];?>" href="#"></a>
<?} else {?>    
    <a href="#" class="b-where__icon add2compare_ m-compare__added" data-id="<?=$arElement['ID'];?>" ></a>
    <?}?>
                                
                                
                                 
          
    </div>
    <div class="b-catalog-list_list__price">
        
        
<? foreach ($arElement["PRICES"] as $code => $arPrice): ?>
<? if ($arPrice["CAN_ACCESS"]): ?>
<? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
        
<div class="b-price__new"><span class="b-price m-price__list"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></span></div>
<div class="b-price__old"><span class="b-price__small"><?= $arPrice["PRINT_VALUE"] ?></span></div>
  
<? else: ?><span class="b-price m-price__list"><?= $arPrice["PRINT_VALUE"] ?></span><? endif; ?>
 
<? endif; ?>
<? endforeach; ?>
    <?if($arElement["CAN_BUY"]){ ?> 

   <?if($arElement['IN_BASKET']!='Y'){?>  
       <button class="b-catalog-list_item__buy m-bnt__list buy_" data-id="<?=$arElement['ID']?>"><span class="b-catalog-list_item__cart">Купить</span></button>
        <?} else {?> 
       <button class="b-catalog-list_item__buy m-bnt__list buy_ m-in_basket"><span class="b-catalog-list_item__cart">добавлен<br>в корзину</span></button>
    <?}?>
       
 <?}?>
           
    </div>
</div>
<?}?> 
   
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>