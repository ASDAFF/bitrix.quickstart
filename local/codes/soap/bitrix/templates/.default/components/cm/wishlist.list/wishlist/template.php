<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//pr($arResult['IS_CREATOR']);

?>
<script>
$(function(){
    $('button.b-button__fast').live('click', function(){

       $('.b-fast_order').remove();
 
       $(this).parent()
              .parent() 
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Быстрый заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','154px') 
                         .css('left','70px')
                         .css('z-index','5'); 
 
       return false;
    });
});  
</script>

	<h3 class="b-detail__h3 m-wishlist__h3">

		<?=($arResult['SECTION_CODE']?$arResult['SECTION']['NAME']:'Общий список');?>
		<span cat='<?=$arResult['SECTION']['ID']?>' class="b-sidebar-wishlist__count">
			<?=(count($arResult['ITEMS'])?count($arResult['ITEMS']):"")?>
		</span>
			<?=($arResult['SECTION']['NAME']&&$arResult['IS_CREATOR']=='Y'&&$arResult['SECTION_CODE']?'<a href="#" class="b-wishlist__rename">Переименовать</a>':'')?>
	</h3>
<div style="z-index:100;top: 281px; left: 70%;" class="b-popup m-popup__orange" id="b-wishlist__rename">
		<div class="b-popup__wrapper">
		<div class="b-login__user"><input type="text" id='wishlist_rename_field' class="b-cart-field__input" placeholder="Новое название" value=""></div>
		<div class="clearfix"><button id="wishlist_rename_cat" cat="<?=$arResult['SECTION']['ID']?>" class="b-button__fast_n">OK</button></div>
		</div>
	</div>
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%3 == 0):?>
		<div class="b-slider-wrapper">
		<div class="b-slider clearfix">
		<?endif;?>
<?$full_name = $arElement['NAME']." ".$arElement['PROPERTIES']['model']['VALUE']." (".$arElement['PROPERTIES']['article']['VALUE'].")";?>
<?$full_name = cutString($full_name,27)?>
<div class="b-slider__item" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
	<div class="b-slider__text">
		<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
		<div class="b-slider__image"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" data-id="#item-<?=$arElement['ID']?>" id="<?=$arElement['ID']?>"/></div>
		<?else:?>
<div class="b-slider__image"><img border="0" src="/images/img-element__image.png"  alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></div>
<?endif?>
		<div class="b-slider__link"><?=$arElement['PROPERTIES']['type']['VALUE']?> <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$full_name?></a></div>
		<div class="b-slider__price"><?=$arElement["PRICE"]?></div>
	</div> 
	<div class="b-slider__btn clearfix">
			<div class="fust_order" style="display:none;">
				<form action="/includes/fust_order.php" name="fust_order" method="post">
					<div class="b-footer-form">
						<input type="text" class="b-footer-form__text" placeholder="<?echo GetMessage("FUST_ORDER_PHONE")?>" name="phone"/>
						<input type="hidden" name="order" value=""/>
						<label><?echo GetMessage("FUST_ORDER_TEXT")?></label>
						<input type="submit" value="send" id="fust_order-submit"/>
					</div>
				</form>
			</div>
		<button el='<?=$arElement["ID"]?>' class="b-button__fast"><?echo GetMessage("FUST_ORDER")?></button>
		<!--span class="b-icon" title="<?echo GetMessage("WISHLIST")?>"></span-->
				<?if($arParams["DISPLAY_COMPARE"]):?>
					<noindex>
	<a href="<?if(array_key_exists($arElement['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "#b-compare__added";}else{echo "#b-compare__add";}?>" id="<?=$arElement['ID']?>" rel="<?if(!array_key_exists($arElement['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "/catalogue/?action=ADD_TO_COMPARE_LIST&id=".$arElement['ID']."";}?>"  class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
</noindex>

				<?endif?>

<a id="<?=$arElement["ID"]?>" class="b-icon m-icon__buy" title="В корзину" rel="<?=$arElement["DETAIL_PAGE_URL"]?>?action=ADD2BASKET&id=<?=$arElement["ID"]?>" href="javascript:void(0);"></a>

	</div>
<?if($arResult['IS_CREATOR']=='Y'):?><button class="b-button__delete b-delete_element m-cart__delete m-wishlist-item__delete" cat='<?=$arResult['SECTION']['ID']?>' el='<?=$arElement['ID']?>'></button><?endif;?>

</div>
		<?$cell++;
		if($cell%3 == 0):?>
					</div>
				</div>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		<?if($cell%3 != 0):?>
					</div>
				</div>
		<?endif?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
<div class="b-popup m-popup__orange" id="b-compare__add">
	<div class="b-popup__wrapper">
		<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
		<a class="b-button__fast" href="/catalogue/compare.php">Перейти к сравнению</a>
	</div>
</div>
<div class="b-popup m-popup__orange" id="b-compare__added">
	<div class="b-popup__wrapper">
		<h2 class="b-popup-compare__h2">Товар уже добавлен к сравнению.</h2>
		<a class="b-button__fast" href="/catalogue/compare.php">Перейти к сравнению</a>
	</div>
</div>

