<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if(empty($arResult['ITEMS'])) return;?>
<div  id="klondike-slider" class="klondike-slider klondike-slider-<?=$arParams['COLOR_SCHEME']?>">
	<?foreach($arResult['ITEMS'] as $arItem){?>
		<div class="klondike-slide">
			<div class="klondike-slide-in">
				<div class="slide-header"><?=$arItem['NAME']?></div>
				<div class="slide-data">
					<?=$arItem['PREVIEW_TEXT'];?>

					<div class="slide-price">

						<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])){
							if (count($arItem["OFFERS"]) > 1){?>
								<span class="slide-price--item_price"><?echo GetMessage("CR_PRICE_OT").$arItem["PRINT_MIN_OFFER_PRICE"];?></span>
							<?}else{
								foreach($arItem["OFFERS"] as $arOffer):?>
									<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
										<?if($arPrice["CAN_ACCESS"]):?>
											<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
												<span class="slide-price--item_price slide-price--discount"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br>
												<span class="slide-price--old_price"><?=$arPrice["PRINT_VALUE"]?></span><br>
											<?else:?>
												<span class="slide-price--item_price"><?=$arPrice["PRINT_VALUE"]?></span>
											<?endif?>
										<?endif;?>
									<?endforeach;?>
								<?endforeach;
							}
						}else{ // if product doesn't have offers

							$numPrices = count($arParams["PRICE_CODE"]);
							foreach($arItem["PRICES"] as $code=>$arPrice):
								if($arPrice["CAN_ACCESS"]):?>
									<?if ($numPrices>1):?><div class="slide-price--price-title"><?=$arResult["PRICES"][$code]["TITLE"];?>:</div><?endif?>
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<span class="slide-price--item_price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
										<span class="slide-price--old_price"><?=$arPrice["PRINT_VALUE"]?></span>
									<?else:?>
										<span class="slide-price--item_price"><?=$arPrice["PRINT_VALUE"]?></span>
									<?endif;
								endif;
							endforeach;
						}?>
					</div>
				</div>

				<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="klondike-link"><?=GetMessage('CATALOG_MORE')?></a>

				<?if(!empty($arItem["IMG"])){?>
					<div class="klondike-img">
						<img src="<?=$arItem["IMG"]["src"]?>" width="<?=$arItem["IMG"]["width"]?>" height="<?=$arItem["IMG"]["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
					</div>
				<?}?>
			</div>
		</div>
	<?}?>
	<nav class="klondike-arrows">
		<span class="klondike-arrows-prev"></span>
		<span class="klondike-arrows-next"></span>
	</nav>
</div>
<script type="text/javascript">
	$(function() {
		$('#klondike-slider').cslider({
			<?if('N' == $arParams['AUTO_PLAY']){?>autoplay:false,<?}?>
			interval: <?=$arParams["INTERVAL"];?>,
		});
	});
</script>
