<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
$(function(){
    $('.b-button__fast').click(function(){

       $('.b-fast_order').remove();
 
       $(this).parent().parent()
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Быстрый заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','154px') 
                         .css('left','70px')
                         .css('z-index','5'); 
 
       return false;
    });
	$('.b-button__fast_or').click(function(){

       $('.b-fast_order').remove();
 
       $(this).parent().parent()
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Быстрый заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','-115px') 
                         .css('left','300px')
                         .css('z-index','5'); 
 
       return false;
    });
	$('.b-button__fast_or_2').click(function(){

       $('.b-fast_order').remove();
 
       $(this).parent().parent()
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Быстрый заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','330px') 
                         .css('left','300px')
                         .css('z-index','5'); 
 
       return false;
    });
});  
</script>
<div class="b-nav-category m-cart">
				<div class="b-cart-mini__link js-cart__show" data-id="1">
   <?
   if (IntVal($arResult["NUM_PRODUCTS"])>0)
   {
      if (CModule::IncludeModule("sale"))
      {
         $arBasketItems = array();
         $dbBasketItems = CSaleBasket::GetList(
                 array(
                         "NAME" => "ASC",
                         "ID" => "ASC"
                     ),
                 array(
                         "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                         "LID" => SITE_ID,
                         "ORDER_ID" => "NULL"
                     ),
                 false,
                 false,
                 array("ID", "PRODUCT_ID", "QUANTITY", "PRICE")
             );
         while ($arItems = $dbBasketItems->Fetch())
         {
             if (strlen($arItems["CALLBACK_FUNC"]) > 0)
             {
                 CSaleBasket::UpdatePrice($arItems["ID"],
                                          $arItems["QUANTITY"]);
                 $arItems = CSaleBasket::GetByID($arItems["ID"]);
             }
             $arBasketItems[] = $arItems;
			 $arBID[] = $arItems['ID'];
         }
         $summ = 0;
         for ($i=0;$i<=$arResult["NUM_PRODUCTS"];$i++){      
            $summ = $summ + $arBasketItems[$i]["PRICE"]*$arBasketItems[$i]["QUANTITY"];
	    $count = $count + $arBasketItems[$i]["QUANTITY"];
         }
      }
	  $strBID = implode(',',$arBID);

      ?>
					<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="b-cart-mini">
						<div class="b-cart-mini__line"><b class="b-cart-mini__count"><?=ceil($count);?></b> товаров</div>
						<div class="b-cart-mini__line"><b class="b-cart-mini__count"><?=$summ?></b> руб.</div>
					</a>
<div class="b-cart__hidden" id="cart-1">
	<div class="b-cart-long clearfix">
		<div class="b-cart-long__text"><b><?=ceil($count);?></b> товаров на <b><?=$summ?></b> рублей</div>
		<div class="b-cart-long__button"><button class="b-button__delete js-cart__close"></button></div>
		<div class="b-cart-long__button"><a href="<?=$arParams["PATH_TO_BASKET"]?>" class="b-button" style="display: inline-block;">Перейти в корзину</a></div>
		<div class="b-cart-long__button"><button el='<?=$strBID?>' class="b-button__fast_or b-button m-orange">Быстрый заказ</button></div>
	</div>
<div class="b-slider-wrapper" id="slider-1">
	<a href="#" class="b-slider__control m-prev"  title="назад"></a>
	<div class="b-slider">
<?
$i=1;
foreach($arBasketItems as $arrItem):?>
<?if($i==1){?><div class="clearfix"><?}?>
<?$ar_res = CIBlockElement::GetList( Array("SORT"=>"ASC"), 
                                     Array("ID" => $arrItem["PRODUCT_ID"], 
                                           "IBLOCK_ID" => "1"),
                                     false , 
                                     false , 
                                     Array("ID","IBLOCK_ID", "NAME",
                                           "PREVIEW_PICTURE",
                                           "DETAIL_PAGE_URL", "PROPERTY_model",
                                           "PROPERTY_type", "PROPERTY_article"));   
$props = $ar_res->GetNext();
 
?>
<div class="b-slider__item">
	<div class="b-slider__text">
	<?if($props["PREVIEW_PICTURE"]!=""){?>
		<div data-id="#item-<?=$arElement['ID']?>" id="<?=$arElement['ID']?>" class="b-slider__image"><img src="<?=CFile::GetPath($props["PREVIEW_PICTURE"]);?>" alt="<?=$props["NAME"]?>" /></div>
	<?}else{?>
<div class="b-slider__image"><img border="0" src="/images/img-element__image.png"  alt="<?=$props["NAME"]?>" title="<?=$props["NAME"]?>" /></div>
<?}?>
	<div class="b-slider__link"><?=$props["PROPERTY_TYPE_VALUE"]?> <a href="<?=$props["DETAIL_PAGE_URL"]?>"><?=cutString($props["NAME"],28)?></a></div>
	<div class="b-slider__price"><?=$arrItem["PRICE"]?>.–</div>
</div>
<div class="b-slider__btn clearfix">
	<a href="#b-fast__order" class="b-button__fast b-show-fast__order">Быстрый<br>заказ</a>
	<a el='<?=$props['ID']?>' class="m-wishlist__add b-icon" href="#b-wishlist__add" title="<?echo GetMessage("WISHLIST")?>"></a>
	<a href="<?if(array_key_exists($props['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "#b-compare__added";}else{echo "#b-compare__add";}?>" id="<?=$props['ID']?>" rel="<?if(!array_key_exists($props['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "/catalogue/?action=ADD_TO_COMPARE_LIST&id=".$props['ID']."";}?>"  class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
</div>
</div>
<?if($i==3 or end($arBasketItems)==$arrItem){?></div><?$i=0;}?>  
<?
$i++;
endforeach;?>
	</div>
	<a href="#" class="b-slider__control m-next" title="вперед"></a>
</div>
<div class="b-cart-long m-cart-long__bottom clearfix">
	<div class="b-cart-long__button"><button class="b-button__delete js-cart__close"></button></div>
	<div class="b-cart-long__button"><a href="<?=$arParams["PATH_TO_BASKET"]?>" class="b-button" style="display: inline-block;">Перейти в корзину</a></div>
	<div class="b-cart-long__button"><button el='<?=$strBID?>' class="b-button__fast_or_2 b-button m-orange">Быстрый заказ</button></div>
</div>
<hr class="b-hr" />

      <?
   }
   else
   {
      ?>
<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="b-cart-mini">
         <div class="b-cart-mini__line"><?=$arResult["ERROR_MESSAGE"]?></div>
      </a><?
   }
   ?>
				</div>
</div>
<div class="b-popup m-popup__orange" id="b-compare__add">
	<div class="b-popup__wrapper">
		<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
		<a class="b-button__fast_n" href="/catalogue/compare.php">Перейти к сравнению</a>
	</div>
</div>
<div class="b-popup m-popup__orange" id="b-compare__added">
	<div class="b-popup__wrapper">
		<h2 class="b-popup-compare__h2">Товар уже добавлен к сравнению.</h2>
		<a class="b-button__fast_n" href="/catalogue/compare.php">Перейти к сравнению</a>
	</div>
</div>
<div class="b-popup m-popup__orange" id="b-wishlist__add">
	<div class="b-popup__wrapper">
		<div class="b-wishlist__select">
			<select name="cat" id="cat_list">
			<?	if($USER->GetID()){
					$arFilter = Array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', 'CREATED_BY'=>$USER->GetID());
					$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('ID',"NAME"));
					while($ar_result = $db_list->GetNext())
					{?>
						<option value="<?=$ar_result['ID']?>"><?=$ar_result['NAME']?></option>
					<?}
				}
			
			?>
			</select>
		</div>
		<div class="b-login__user"><input type="text" id='new_wish_field' class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
		<div class="clearfix"><a id='wishlist_add_el' el='3' class="b-button__fast_n">OK</a></div>
	</div>
</div>