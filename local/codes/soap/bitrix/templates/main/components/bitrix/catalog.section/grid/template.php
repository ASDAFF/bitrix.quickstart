<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-catalog-list">
<ul class="quick_view">
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<div class="b-slider-wrapper">
		<div class="b-slider clearfix">
		<?endif;?>
<?$full_name = $arElement['NAME']." ".$arElement['PROPERTIES']['model']['VALUE']." (".$arElement['PROPERTIES']['article']['VALUE'].")";?>
<div class="b-slider__item" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
	<div class="b-slider__text">
		<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
		<div class="b-slider__image"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></div>
		<?endif?>
		<div class="b-slider__link"><?=$arElement['PROPERTIES']['type']['VALUE']?> <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$full_name?></a></div>
		<div class="b-slider__price"><?=$arElement["PRICES"]["price"]["PRINT_VALUE_NOVAT"]?></div>
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
		<button class="b-button__fast" id="b-fast_order"><?echo GetMessage("FUST_ORDER")?></button>
		<a el='<?=$arElement['ID']?>' href="#b-wishlist__add" class="b-icon m-wishlist__add" title="<?echo GetMessage("WISHLIST")?>"></a>
				<?if($arParams["DISPLAY_COMPARE"]):?>
					<noindex>
					<a href="<?//echo $arElement["COMPARE_URL"]?>#b-compare__add" rel="nofollow" class="b-icon m-icon__compare m-compare__add" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
					</noindex>
				<?endif?>
	</div>
</div>
		<?$cell++;
		if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
					</div>
				</div>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
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
			<button class="b-button__fast">Сравнить товары</button>
		</div>
	</div>
	<div class="b-popup m-popup__orange" id="b-wishlist__add">
		<div class="b-popup__wrapper">
			<div class="b-wishlist__select">
				<select name="cat" id="cat_list">
				<?
					$arFilter = Array('IBLOCK_ID'=>2, 'GLOBAL_ACTIVE'=>'Y', 'CREATED_BY'=>$USER->GetID());
					$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array('ID',"NAME"));
					while($ar_result = $db_list->GetNext())
					{?>
						<option value="<?=$ar_result['ID']?>"><?=$ar_result['NAME']?></option>
					<?}
				
				?>
				</select>
			</div>
			<div class="b-login__user"><input type="text" class="b-cart-field__input" placeholder="Новый вишлист" value="" /></div>
			<div class="clearfix"><button id='wishlist_add_el' el='3' class="b-button__fast">OK</button></div>
		</div>
	</div>
</div>