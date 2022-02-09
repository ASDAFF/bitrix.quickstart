<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!$_REQUEST["ajax"] && @$_REQUEST["ajax"] != "Y"){?>
<div class="b-catalog-list">
<?}?>
<script>
$(function(){
    $('.b-button__fast').live('click', function(){
 	console.log($(this).css('left'))
       $('.b-fast_order').remove();
 
       $(this)
              .parent() 
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Вам перезвонит оператор и оформит заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','-145px') 
                         .css('left','65px')
                         .css('z-index','5'); 
 
       return false;
    });
});  
</script>
<ul class="quick_view">
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?$full_name = $arElement['NAME']." ".$arElement['PROPERTIES']['model']['VALUE']." (".$arElement['PROPERTIES']['article']['VALUE'].")";?>
		<div class="b-catalog-list__item clearfix" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
			
			<div class="b-catalog-list__image"><img class="js-hover__detail" data-id="#item-<?=$arElement['ID']?>" border="0" src='<?=($arElement["PREVIEW_PICTURE"]["SRC"]?$arElement["PREVIEW_PICTURE"]["SRC"]:"/images/img-element__image.png")?>' width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="image-<?=$arElement['ID']?>"/></div>
		
			<div class="b-catalog-list__text">
				<div class="b-catalog-list__link" data-id="#item-<?=$arElement['ID']?>"><?=$arElement['PROPERTIES']['type']['VALUE']?> <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$full_name?></a><span class="b-rating"><span style="width: <?=($arElement['PROPERTIES']['rating']['VALUE']*20)?>%"></span></span></div>
				<div class="b-catalog-list__info"><?=$arElement['PREVIEW_TEXT']?></div>
				<div class="clearfix">
					<div class="b-catalog-list__price">
						<div class="b-slider__price"><?=$arElement["PRICES"]["price"]["PRINT_VALUE_NOVAT"]?></div>
						<?if($arElement["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]):?>
						<div class="b-slider__price_clearing">Безнал <b><?=$arElement["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]?></b></div>
						<?endif;?>
					</div>
					<div class="b-catalog-list__btn">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
Array(),
false
);?>
						<button class="b-button__fast" el='<?=$arElement['ID']?>'><?echo GetMessage("FUST_ORDER")?></button>
						<?if($arParams["DISPLAY_COMPARE"]):?>
						<noindex>
							<a href="<?if(array_key_exists($arElement['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "#b-compare__added";}else{echo "#b-compare__add";}?>" id="<?=$arElement['ID']?>" rel="<?if(1 || !array_key_exists($arElement['ID'], $_SESSION[CATALOG_COMPARE_LIST][1][ITEMS])){echo "/catalogue/?action=ADD_TO_COMPARE_LIST&id=".$arElement['ID']."";}?>"  class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
						</noindex>
						<?endif?>
						<a el='<?=$arElement['ID']?>' class="m-wishlist__add" href="#b-wishlist__add"><span class="b-icon" title="<?echo GetMessage("WISHLIST")?>"></span></a>
						<?if($arElement["CAN_BUY"]):?>
							<noindex>
							<a class="b-icon m-icon__buy" id="<?=$arElement['ID']?>" href="<?echo $arElement["ADD_URL"]?>" rel="nofollow" title="<?echo GetMessage("CATALOG_ADD")?>"></a>
							</noindex>
						<?endif;?>
					</div>
				</div>
			</div>
			<div id="item-<?=$arElement['ID']?>" class="b-item-detail">
				<div class="b-item-detail__image"><img src='<?=($arElement["PREVIEW_PICTURE"]["SRC"]?$arElement["PREVIEW_PICTURE"]["SRC"]:"/images/img-element__image.png")?>' alt="" /></div>
				<div class="b-slider__price m-item-detail__center"><?=$arElement["PRICES"]["price"]["PRINT_VALUE"]?></div>
				<div class="b-catalog-list__link"><?=$arElement['PROPERTIES']['type']['VALUE']?> <?=$full_name?></div>
				<div class="b-catalog-list__info"><?=$arElement["PREVIEW_TEXT"]?></div>
			</div>
		</div>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
		</ul>
<div class="b-popup m-popup__orange" id="b-compare__add">
		<div class="b-popup__wrapper">
			<h2 class="b-popup-compare__h2">Товар добавлен к сравнению.</h2>
			<a class="b-button__fast_n" onclick='location.href=/catalogue/compare.php' href="/catalogue/compare.php">Сравнить товары</a>
		</div>
</div>
	<div class="b-popup m-popup__orange" id="b-compare__added">
		<div class="b-popup__wrapper">
			<h2 class="b-popup-compare__h2">Товар уже добавлен к сравнению.</h2>
			<a class="b-button__fast_n" href="/catalogue/compare.php">Перейти к сравнению</a>
		</div>
	</div>
<!--div class="b-popup m-popup__orange" id="b-wishlist__add">
		<div class="b-popup__wrapper">
			<div class="b-wishlist__select">
				<select name="cat" id="cat_list">
				<? if($USER->GetID()):
					$arFilter = Array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', 'CREATED_BY'=>$USER->GetID());
					$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('ID',"NAME"));
					while($ar_result = $db_list->GetNext())
					{?>
						<option value="<?=$ar_result['ID']?>"><?=$ar_result['NAME']?></option>
					<?}
				
				endif;?>
				</select>
			</div>
			<div class="b-login__user"><input type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><a id='wishlist_add_el' el='3' class="b-button__fast_n">OK</a></div>
		</div>
	</div-->
<?if(!$_REQUEST["ajax"] && @$_REQUEST["ajax"] != "Y"){?>		
</div>
<?}?>
  <?if($arResult["NAV_STRING"]) {?>
				<div class="b-show_more">
					<a href="<?=$arResult["NAV_STRING"]?>&ajax=Y" id="catlistnavnext" class="b-button m-small__btn"><span>Показать следующие <?=$arParams["PAGE_ELEMENT_COUNT"]?> товаров</span></a>
				</div>
  <?}?>