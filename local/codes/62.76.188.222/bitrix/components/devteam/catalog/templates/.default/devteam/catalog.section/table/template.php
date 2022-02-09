<div class="b-catalog-list">
<?if($arResult["ITEMS"]){?>
<div class="b-catalog-list_table">
<table class="b-catalog-table">
<thead>
<tr>
    <td class="b-catalog-table__name">Наименование</td>
    <td class="b-catalog-table__price">Цена</td>
    <td>Гар.</td>
    <td class="b-catalog-table__where">Наличие</td>
    <td></td>
</tr>
</thead>
<tbody>
<?foreach($arResult["ITEMS"] as $cell=>$arElement){
$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
?>    
<tr id="<?=$this->GetEditAreaId($arElement['ID']);?>">
<td>
<a class="b-catalog-table__link" href="<?=$arElement['DETAIL_PAGE_URL'];?>"><?=$arElement['NAME'];?></a>
<div class="b-catalog-table__text">
    <?
    $props = "";
    $n = 0; 
    foreach($arElement['DISPLAY_PROPERTIES'] as $key => $prop){
        $props .= $prop["VALUE"];
        if($n++ < count($arElement['DISPLAY_PROPERTIES']) -1 ){
           $props .=  '; ';
        } 
    }
    if(strlen($props))
        echo "({$props})"; 
    ?>
    </div>
</td>
<td>
<? foreach ($arElement["PRICES"] as $code => $arPrice): ?>
<? if ($arPrice["CAN_ACCESS"]): ?>
<? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
<div class="b-price__new"><span class="b-price m-price__list"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></span></div>
<div class="b-price__old"><span class="b-price__small"><?= $arPrice["PRINT_VALUE"] ?></span></div>
<? else: ?><span class="b-price"><?= $arPrice["PRINT_VALUE"] ?></span><? endif; ?>
</p>
<? endif; ?>
<? endforeach; ?>

        </td>
        <td class="b-catalog-table__top">1 год</td>
        <td class="b-catalog-table__top">
<?foreach($arElement['PROPERTIES']['SHOP']["VALUE_XML_ID"] as $k=>$shop){?>
                    <span class="b-where__icon <?=$shop;?>"></span>
                <?}?>

<?if($arElement['IN_COMPARE']!='Y'){?>                          
<a style="width:20px;"  title="Сравнить" class="b-where__icon add2compare_" data-id="<?=$arElement['ID'];?>" href="#"></a>
<?} else {?>    
<a style="width:20px;" href="#" class="b-where__icon add2compare_ m-compare__added" data-id="<?=$arElement['ID'];?>" ></a>
<?}?>

        </td>
        <td>
            <?if($arElement["CAN_BUY"]){ ?> 
        <?if($arElement['IN_BASKET']!='Y'){?>   
            <button class="b-button buy_" data-id="<?=$arElement['ID']?>"><span class="b-catalog-list_item__cart">Купить</span></button>
           <? } else { ?>
            <button class="b-button buy_ m-in_basket"><span class="b-catalog-list_item__cart">добавлен<br>в корзину</span></button>
        <? } ?> 
<?}?>
  </td>
</tr>
 <?}?>
</tbody>
</table>
</div>
<?} else {?>
<p>Раздел пуст</p> 
<? } ?>
</div> 
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<?=$arResult["NAV_STRING"]?>
<?endif;?>