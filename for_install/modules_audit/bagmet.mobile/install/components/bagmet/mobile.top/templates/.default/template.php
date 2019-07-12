<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["ITEMS"]) > 0): ?>
	<?
	$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);
	?>
<?if ($arParams["FLAG_PROPERTY_CODE"]):?>
<div class="catalog_title catalog_title_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">
	<h2><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h2>
</div>
<?else:?>
	<?
	$arParams["FLAG_PROPERTY_CODE"] = rand();
	?>
<?endif?>

<div class="catalog_slider_wrapper" id="slider_wrapper_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">
	<ul id="slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">

<?foreach($arResult["ITEMS"] as $key => $arItem):
	if(is_array($arItem))
	{
		$sticker = "";
		if (array_key_exists("PROPERTIES", $arItem) && is_array($arItem["PROPERTIES"]))
		{
			foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
				if (array_key_exists($propertyCode, $arItem["PROPERTIES"]) && intval($arItem["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
				{
					$sticker .= "<li class=\"".ToLower($propertyCode)."\">".$arItem["PROPERTIES"][$propertyCode]["NAME"]."</li>";
				}
			if(!$arItem["CAN_BUY"])
				$sticker .= "<li class=\"out_of_order\">".GetMessage("CATALOG_NOT_AVAILABLE2")."</li>";
			//if(!(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])) && !$arItem["CAN_BUY"]
			//|| is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]) && $arItem["ALL_SKU_NOT_AVAILABLE"])
		}

		$bPicture = is_array($arItem["PREVIEW_IMG"]);
		?>
		<li class="catalog_item" itemscope itemtype = "http://schema.org/Product">
			<div class="catalog_item_content">
				<div class="catalog_item_top_block">
					<div class="catalog_item_top_panel">
						<?if ($sticker):?>
						<ul>
							<?=$sticker?>
						</ul>
						<?endif?>
					</div>

					<?if ($bPicture):?>
						<a class="catalog_item_content_a" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img itemprop="image" class="item_img" src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" /></a>
					<?endif?>

					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class='prices' itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">
					<?
					if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
					{
						if (count($arItem["OFFERS"]) > 1)
						{
						?>
							<span itemprop="price" class='price'>
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
											<span itemprop="price" class='price new_price'><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/>
											<span class='old_price'><?=$arPrice["PRINT_VALUE"]?></span>
											<?else:?>
											<span itemprop="price" class='price'><?=$arPrice["PRINT_VALUE"]?></span>
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
									<span itemprop="price" class='price new_price'><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/>
									<span class='old_price'><?=$arPrice["PRINT_VALUE"]?></span>
								<?else:?>
									<span itemprop="price" class='price'><?=$arPrice["PRINT_VALUE"]?></span>
								<?endif;
							endif;
						endforeach;
					}
					?>
					</a>
				</div>

				<div class="catalog_item_descr">
					<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><span itemprop = "name"><?=$arItem["NAME"]?></span></a></h4>
				</div>

				<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))
				{
					?>
					<a href="javascript:void(0)" class='catalog_buy_button' rel="nofollow" onclick="return addOffer2Cart(this, <?=CUtil::PhpToJsObject($arItem["SKU_OFFERS"])?>, '<?=CUtil::JSEscape($arItem["NAME"])?>', '<?=SITE_DIR?>personal/cart/')"><?echo GetMessage("CATALOG_BUY")?></a>
					<?if($arParams["DISPLAY_COMPARE"]):?>
						<noindex><a href="javascript:void(0)" class="catalog_item_compare" onclick="return addOffer2Cart(this, <?=CUtil::PhpToJsObject($arItem["SKU_OFFERS"])?>, '<?=CUtil::JSEscape($arItem["NAME"])?>', '<?=SITE_DIR?>personal/cart/')"><?=GetMessage("CATALOG_COMPARE")?></a></noindex>
					<?endif?>
					<?
				}
				else
				{
					?>
					<?if($arItem["CAN_BUY"]):?>
						<a href="<?echo $arItem["ADD_URL"]?>" class='catalog_buy_button' rel="nofollow" onclick="return add2basket(this, '<?=CUtil::JSEscape($arItem["NAME"])?>', '<?=SITE_DIR?>personal/cart/');"><?echo GetMessage("CATALOG_BUY")?></a>
					<?else:?>
						<?if ($USER->IsAuthorized()):?>
							<a href='<?=$arItem["SUBSCRIBE_URL"]?>' onclick="return add2subscribe(this, '<?=CUtil::JSEscape($arItem["NAME"])?>')" class='subscribe_to'><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<?else:?>
							<a href='javascript:void(0)' onclick="subscribePopup(this)" class='subscribe_to'><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<?endif?>
					<?endif?>

					<?if($arParams["DISPLAY_COMPARE"]):?>
						<noindex><a href="<?echo $arItem["COMPARE_URL"]?>" class="catalog_item_compare" onclick="return add2compare(this, '<?=$arItem["NAME"]?>', '<?=SITE_DIR."catalog/compare/"?>');"><?=GetMessage("CATALOG_COMPARE")?></a></noindex>
					<?endif?>
				<?
				}
				?>


			</div>
		</li>
<?
	}
endforeach;
?>
	</ul>
	<div class="clearfix"></div>
	<a class="prev" id="slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>_prev" href="#"><span>prev</span></a>
	<a class="next" id="slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>_next" href="#"><span>next</span></a>
	<div class="pagination" id="slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>_pag"></div>
</div>

<?elseif($USER->IsAdmin()):?>
	<h3 class="hitsale"><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h3>
	<div class="listitem-carousel">
		<?=GetMessage("CR_TITLE_NULL")?>
	</div>
<?endif;?>

<script type="text/javascript">
	$(document).ready(function() {
		$("#slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>").carouFredSel({
			circular: true,
			infinite: false,
			auto: false,
			width: "100%",
			align: false,
			prev: {
				button: "#slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>_prev",
				key: "left"
			},
			next: {
				button: "#slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>_next",
				key: "right"
			},
			pagination: "#slider_cat_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>_pag"
		});

		$("#slider_wrapper_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>").mouseenter(function(){
			$('.prev', this).css('visibility', 'visible');
			$('.next', this).css('visibility', 'visible');
		});
		$("#slider_wrapper_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>").mouseleave(function(){
			$('.prev', this).css('visibility', 'hidden');
			$('.next', this).css('visibility', 'hidden');
		});

		/* Catalog item hover - begin*/
		$('.catalog_item').mouseenter(function(){
			$('.catalog_item_compare', this).css({'visibility': 'visible'});
		});
		$('.catalog_item').mouseleave(function(){
			$('.catalog_item_compare', this).css({'visibility': 'hidden'});
		});
		/* Catalog item hover - end*/
	});
</script>
