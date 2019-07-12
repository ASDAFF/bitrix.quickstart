<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0): ?>
<?
		$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
		$arNotify = unserialize($notifyOption);
?>

<div class="p-block">
	<h2><? if ($arParams["DISPLAY_BLOCK_ICO"]): ?><img src="<?=SITE_TEMPLATE_PATH?>/images/<?=ToLower($arParams["DISPLAY_BLOCK_ICO"])?>" alt="" /><? endif; ?><?=($arParams["DISPLAY_BLOCK_TITLE"])?></h2>

	<ul class="p-list" id="plist-<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">

	<?foreach($arResult["ITEMS"] as $key => $arItem):

		if(is_array($arItem))
		{

			$bPicture = is_array($arItem["PREVIEW_IMG"]);
			?>

			<li itemscope itemtype = "http://schema.org/Product">
				<div class="dataitem-<?=$arItem["ID"]?> item<?if(!(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])) && !$arItem["CAN_BUY"]):?> unavailable<?endif?>">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
						<span class="img">
							<?if ($bPicture):?>
								<img itemprop="image" src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>" width="<?=$arItem["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" />
							<?else:?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/blank.gif" width="140" height="140" />
							<?endif?>
						</span>
						<span class="lnk"><span itemprop="name"><?=$arItem["NAME"]?></span></span>
					</a>
                    <small><?= GetMessage("ARTICUL");?> <?=$arItem['PROPERTIES']["ARTNUMBER"]['VALUE'];?></small>


					<span class="price">
						<span class="p-wrap" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">

							<?
								if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
								{
									$oldPrice = 0;
									$price = 0;

									if (count($arItem["OFFERS"]) > 1)
									{
										$price = $arItem["MIN_OFFER_PRICE"];
										?>
										<strong pprice="<?= $arItem["MIN_OFFER_PRICE"]; ?>" itemprop = "price">
											<?= GetMessage("CR_PRICE_OT")."&nbsp;".$arItem["MIN_OFFER_PRICE"];?>
											<span class="rubl">A</span>
										</strong>
										<?
									}
									else
									{
										foreach($arItem["OFFERS"] as $arOffer):?>
											<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
												<?if($arPrice["CAN_ACCESS"]):?>
													<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): $oldPrice = $arPrice["VALUE"]; $price = $arItem["DISCOUNT_VALUE"]; ?>
														<strong pprice="<?= $arPrice["DISCOUNT_VALUE"]; ?>" itemprop = "discount-price">
															<?=$arPrice["DISCOUNT_VALUE"]?>
															<span class="rubl">A</span>
														</strong>
													<?else:  $price = $arItem["VALUE"]; ?>
														<strong pprice="<?= $arPrice["VALUE"]; ?>" itemprop = "price">
															<?=$arPrice["VALUE"]?>
															<span class="rubl">A</span>
														</strong>
													<?endif?>
												<?endif;?>
											<?endforeach;?>
										<?endforeach;
									}
								}
								else // if product doesn't have offers
								{
									foreach($arItem["PRICES"] as $code=>$arPrice):
									    if($arPrice["CAN_ACCESS"]):
										?>
										    <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): $oldPrice = $arPrice["VALUE"]; $price = $arItem["DISCOUNT_VALUE"]; ?>
										    	<strong pprice="<?= $arPrice["DISCOUNT_VALUE"]; ?>" itemprop = "price">
													<?=$arPrice["DISCOUNT_VALUE"]?>
													<span class="rubl">A</span>
												</strong>
										    <?else: $price = $arItem["VALUE"];?>
										    	<strong pprice="<?= $arPrice["VALUE"]; ?>" itemprop = "price">
													<?=$arPrice["VALUE"]?>
													<span class="rubl">A</span>
												</strong>
										    <?endif;?>
										<?
									    endif;
									endforeach;
								}
							?>

							<span class="pay"><a rel="id<?= $arItem["ID"]; ?>"><?= GetMessage("CATALOG_TO_CART");?></a></span>
						</span>
					</span>

					<br />
					<? /* <span class="quick-pay" onclick="getPopup('quick-pay', this, true)">Быстрая покупка</span> */ ?>
	                <? if ($oldPrice): ?><s><?= $oldPrice; ?> <span class="rubl">A</span></s><? endif; ?>
				</div>
			</li>


			<?
		}
		endforeach;
	?>
	</ul>


</div>

<? endif; ?>