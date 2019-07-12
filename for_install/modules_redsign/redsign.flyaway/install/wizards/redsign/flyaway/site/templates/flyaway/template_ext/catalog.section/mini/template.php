<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(!is_array($arResult['ITEMS']) || count($arResult['ITEMS'])<1)
	return;
?>
<div class="product-viewed-list">
	
    <?php if(empty($arParams['HIDE_BLOCK_TITLE']) || $arParams['HIDE_BLOCK_TITLE'] !== 'Y'): ?>
    	<h2 class="product-content__title">
			<span class="secondLine">
				<?=($arParams['BLOCK_TITLE']? htmlspecialcharsbx($arParams['BLOCK_TITLE']) : Loc::getMessage('SGB_TPL_BLOCK_TITLE_DEFAULT'))?>
			</span>
		</h2>
    <? endif; ?>

	<div class = "products productsmini-owl-slider owlslider">
		<? foreach ($arResult['ITEMS'] as $arItem):
			if(empty($arItem['OFFERS'])){ $HAVE_OFFERS = false; $PRODUCT = &$arItem; } else { $HAVE_OFFERS = true; $PRODUCT = &$arItem['OFFERS'][0]; }
		?>
			<div class = "product-viewed-list__item item">
				<div class = "product-viewed-list__img">
					<a class="product-recom-list__item-url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
						if( isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src'])!='' ) {
							?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
						} else {
							?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
						}
					?></a>
				</div>
				<div class = "product-viewed-list__description">
					<a class="product-viewed-list__name text-fadeout" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a>
					<div class = "product-viewed-list__price">
						<?if( IntVal($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF'])>0 ) {
							?><div class="prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div><?
							?><div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
						} else {
							?><div class="prices__val prices__val_cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
						}?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		<? endforeach; ?>
	</div>
</div>
