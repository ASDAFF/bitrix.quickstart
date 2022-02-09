<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
$(function(){
    $('button.b-button__fast').live('click', function(){

 	
       $('.b-fast_order').remove();
 
       $(this).parent()
              .parent() 
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Вам перезвонит оператор и оформит заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','154px') 
                         .css('left','70px')
                         .css('z-index','5'); 
 
       return false;
    });
});  
</script>
<button style='display:none' class="b-button__fast" id="b-fast_order">Быстрый  <br>заказ</button>
<div class="b-catalog-list" style='margin: 20px -30px;'>
<ul class="quick_view">
		<?foreach($arResult as $cell=>$arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%3 == 0):?>
		<div class="b-slider-wrapper">
		<div class="b-slider clearfix">
		<?endif;?>
		<?
		$arFilter = Array(
		   "IBLOCK_ID"=>1, 
		   "ACTIVE"=>"Y", 
			"ID"=>$arItem['ID']
		   );
		$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, Array("ID","PROPERTY_type","PROPERTY_model","PROPERTY_article"));
		while($ar_fields = $res->GetNext())
		{
			$arItem['PROPERTY_TYPE_VALUE'] = $ar_fields['PROPERTY_TYPE_VALUE'];
			$arItem['PROPERTY_ARTICLE_VALUE'] = $ar_fields['PROPERTY_ARTICLE_VALUE'];
			$arItem['PROPERTY_MODEL_VALUE'] = $ar_fields['PROPERTY_MODEL_VALUE'];
		}
		
		?>
<?$full_name = $arItem['NAME']." ".$arItem['PROPERTY_MODEL_VALUE']." (".$arItem['PROPERTY_ARTICLE_VALUE'].")";?>
<div class="b-slider__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
	<div class="b-slider__text">
		
		<div class="b-slider__image"><img border="0" src='<?=($arItem["PICTURE"]['src']?$arItem["PICTURE"]['src']:"/images/img-element__image.png")?>' width="<?=$arItem["PICTURE"]["WIDTH"]?>" height="<?=$arItem["PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></div>
		
		<div class="b-slider__link"><?=$arItem['PROPERTY_TYPE_VALUE']?> <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=cutString($full_name, 28)?></a></div>
		<div class="b-slider__price"><?=$arItem["PRICE_FORMATED"]?></div>
	</div> 
	<div class="b-slider__btn clearfix">
			<div class="fust_order" style="display:none;">
				<form action="/includes/fust_order.php" name="fust_order" method="post">
					<div class="b-footer-form">
						<input type="text" class="b-footer-form__text" placeholder="<?echo GetMessage("FUST_ORDER_PHONE")?>" name="phone"/>
						<input type="hidden" name="order" value=""/>
						<label>Быстрый заказ</label>						
						<input type="submit" value="send" id="fust_order-submit"/>
					</div>
				</form>
			</div>
		<button class="b-button__fast" el='<?=$arItem['ID']?>' id="b-fast_order">Быстрый  <br>заказ</button>
		<a el='<?=$arItem['ID']?>' href="#b-wishlist__add" class="b-icon m-wishlist__add" title="Добавить в вишлист"></a>

					<noindex>
					<a href="<?//echo $arItem["COMPARE_URL"]?>#b-compare__add" rel="/catalogue/?action=ADD_TO_COMPARE_LIST&id=<?=$arItem['ID']?>&temp=" class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
					</noindex>

	</div>
</div>
		<?$cell++;
		if($cell%3 == 0):?>
					</div>
				</div>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arItem):?>

		<?if($cell%3 != 0):?>
					</div>
				</div>
		<?endif?>
</ul>
  <?if($arResult["NAV_STRING"]) {?>
				<div class="b-show_more">
					<a href="<?=$arResult["NAV_STRING"]?>&ajax=Y" id="catlistnavnext" class="b-button m-small__btn"><span>Показать следующие <?=$arParams["PAGE_ELEMENT_COUNT"]?> товаров</span></a>
				</div>
  <?}?>
	<div class="b-popup m-popup__orange" id="b-compare__add">
		<div class="b-popup__wrapper">
			<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
			<a class="b-button__fast" href="/catalogue/compare.php">Сравнить товары</a>
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
			<div class="b-login__user"><input type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><a id='wishlist_add_el' el='3' class="b-button__fast">OK</a></div>
		</div>
	</div>
</div>