<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
		<button class="b-button__fast"><?echo GetMessage("FUST_ORDER")?></button>
		<span class="b-icon" title="<?echo GetMessage("WISHLIST")?>"></span>
				<?if($arParams["DISPLAY_COMPARE"]):?>
					<noindex>
					<a href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow" class="b-icon m-icon__compare" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
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
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
