<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-catalog-list"> 
<?
if($arResult["ITEMS"]){  
$icons = array('PRODUCT_DAY' => 'm-item-of-the-day',
               'SALE'        => 'm-item-sale', 
               'NEW'         => 'm-item-new',
               'TOP_SALES'   => 'm-item-hit',
               'RECOMMENDED' => 'm-item-recommended' );     
     
foreach($arResult["ITEMS"] as $cell=>$arElement){
    $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
      
if($cell % 3 == 0){?>
<div class="b-catalog-list__line clearfix">  
<?}?>

<div class="b-catalog-list_item<?
if($cell % 3 == 2){?> m-3n<?}
$show_product_icon = false;
foreach($icons as $icon_code => $class){
    if($arElement['PROPERTIES'][$icon_code]["VALUE_XML_ID"] == 'Y'){
         echo " {$class}"; $show_product_icon = true; break;
        }
   }

if(!$show_product_icon){
    foreach ($arElement["PRICES"] as $code => $arPrice){
        if ($arPrice["CAN_ACCESS"]){
            if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]) {
                 echo " m-item-sale"; $show_product_icon = true; break;
            }
        }
    }
}
 
?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>" >

<?if($show_product_icon){?>
<div class="b-product-icon"></div>
<?}?>
<div class="b-catalog-list_item__image"><a href="<?=$arElement['DETAIL_PAGE_URL'];?>"><img alt="" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>"></a></div>
      <div class="b-catalog-list_item__where clearfix">
<div class="b-where__left">
    
<?if($arElement['IN_COMPARE']!='Y'){?>    
<a title="Сравнить" class="b-where__icon add2compare_" data-id="<?=$arElement['ID'];?>" href="#">
<span>Cравнить</span></a>
<?} else {?>    
    <a href="#" class="b-where__icon add2compare_ m-compare__added"  data-id="<?=$arElement['ID'];?>" >
<span>Добавлен к сравнению</span></a>
    <?}?>
</div>
                        <div class="b-where__right">
                            <?
                           foreach($arElement['PROPERTIES']['SHOP']["VALUE_XML_ID"] as $k=>$shop){?>
                                <span  class="b-where__icon <?=$shop;?>"></span>
                            <?}?>
                        </div>
                </div>
                <div class="b-catalog-list_item__name" style="height: 41px;"><a href="<?=$arElement['DETAIL_PAGE_URL'];?>"><?=$arElement['NAME'];?></a></div>
                <div class="b-catalog-list_item__btn clearfix">
                        <div class="b-bth__right"> 
                            <?if($arElement["CAN_BUY"]){ ?> 
                            
   <?if($arElement['IN_BASKET']!='Y'){?>                         
     <button class="b-catalog-list_item__buy buy_" data-id="<?=$arElement['ID']?>"><span class="b-catalog-list_item__cart">Купить</span></button>
   <?} else {?>                         
 <button  class="b-catalog-list_item__buy buy_ m-in_basket"><span class="b-catalog-list_item__cart">добавлен<br>в корзину</span></button>
   <?}?>


  <?}?>
                        </div>
                        <div class="b-bth__right m-price"> 
<? foreach ($arElement["PRICES"] as $code => $arPrice): ?>
<? if ($arPrice["CAN_ACCESS"]): ?>
<? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
                           
    <div class="b-price__new"><span class="b-price"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?> </span></div>
    <div class="b-price__old"><span class="b-price__small"><?= $arPrice["PRINT_VALUE"] ?> </span></div>

 <? else: ?><span class="b-price"><?= $arPrice["PRINT_VALUE"] ?></span><? endif; ?>
<? endif; ?> 
<? endforeach; ?>


                        
                        </div>
                </div>
        </div>
      
    
<?if($cell % 3 == 2){?>
 </div> 
<?}?>
    
    
    
    <?
 
    } ?>
     
<?} else {?>    
  
   <p>Раздел пуст</p> 
     
<?}?>    
</div><!--/.b-catalog-list-->
 
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
 