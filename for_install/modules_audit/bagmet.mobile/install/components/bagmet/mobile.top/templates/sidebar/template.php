<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0): ?>
<div class="same_items">
	<div class="same_items_title"><h3><?=$arParams["TITLE"]?></h3></div>
	<ul>
<?foreach($arResult["ITEMS"] as $key => $arItem):
	if(is_array($arItem))
	{
		$bPicture = is_array($arItem["PREVIEW_IMG"]);
		?>
		<li>
			<div class="same_item_content">
				<div class="same_item_top_block">
					<?if ($bPicture):?>
						<a class="same_item_content_a" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img itemprop="image" src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" /></a>
					<?endif?>

					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class='same_item_prices'>
					<?
					if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
					{
						if (count($arItem["OFFERS"]) > 1)
						{
						?>
							<span class='price'>
						<?
							echo GetMessage("CR_PRICE_OT");
							echo $arItem["PRINT_MIN_OFFER_PRICE"];
						?>
							</span>
						<?
						}
						else
						{
							$numPrices = count($arParams["PRICE_CODE"]);
							foreach($arItem["OFFERS"] as $arOffer):?>
								<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
									<?if($arPrice["CAN_ACCESS"]):?>
										<?if ($numPrices>1):?><span class="price_name_catalog"><?=$arResult["PRICES"][$code]["TITLE"];?></span><?endif?>
										<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
											<span class='same_item_price same_item_new_price'><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/>
											<span class='same_item_old_price'><?=$arPrice["PRINT_VALUE"]?></span>
											<?else:?>
											<span class='same_item_price'><?=$arPrice["PRINT_VALUE"]?></span>
										<?endif?>
									<?endif;?>
								<?endforeach;?>
							<?endforeach;
						}
					}
					else // if product doesn't have offers
					{
						$numPrices = count($arParams["PRICE_CODE"]);
						foreach($arItem["PRICES"] as $code=>$arPrice):
							if($arPrice["CAN_ACCESS"]):?>
								<?if ($numPrices>1):?><span class="price_name_catalog"><?=$arResult["PRICES"][$code]["TITLE"];?></span><?endif?>
								<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
									<span class='same_item_price same_item_new_price'><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/>
									<span class='same_item_old_price'><?=$arPrice["PRINT_VALUE"]?></span>
								<?else:?>
									<span class='same_item_price'><?=$arPrice["PRINT_VALUE"]?></span>
								<?endif;
							endif;
						endforeach;
					}
					?>
					</a>
				</div>

				<div class="same_item_descr">
					<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a></h4>
				</div>
			</div>
		</li>
<?
	}
endforeach;
?>
	</ul>
</div>
<?endif;?>

