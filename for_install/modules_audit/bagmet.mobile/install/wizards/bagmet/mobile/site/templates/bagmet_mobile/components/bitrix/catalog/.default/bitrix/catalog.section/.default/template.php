<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["DISPLAY_TOP_PAGER"] && !empty($arResult["NAV_STRING"])):?>
<div class="paging_top">
	<?=$arResult["NAV_STRING"]?>
</div>
<?endif;?>

<div class="catalog">
	<ul>
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

		$sticker = "";
		if (array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"]))
		{
			foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
				if (array_key_exists($propertyCode, $arElement["PROPERTIES"]) && intval($arElement["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
				{
					$sticker .= "<li class=\"".ToLower($propertyCode)."\">".$arElement["PROPERTIES"][$propertyCode]["NAME"]."</li>";
				}
			if(!$arElement["CAN_BUY"])
				$sticker .= "<li class=\"out_of_order\">".GetMessage("CATALOG_NOT_AVAILABLE")."</li>";
		}
		?>
		<li class="catalog_item" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
			<div class="catalog_item_content">
				<div class="catalog_item_top_block">
					<div class="catalog_item_top_panel">
						<?if ($sticker):?>
						<ul>
						<?=$sticker?>
						</ul>
						<?endif?>
					</div>
					<a class="catalog_item_content_a" href="<?=$arElement["DETAIL_PAGE_URL"]?>">
						<?if(is_array($arElement["PREVIEW_IMG"])):?>
							<img class="item_img" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
						<?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
							<img class="item_img" src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
						<?endif?>
					</a>

					<a class='prices'>
						<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
							<?/*foreach($arElement["OFFERS"] as $arOffer):?>
								<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
									<?if($arPrice["CAN_ACCESS"]):?>
										<p><?=$arResult["PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
										<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
											<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
										<?else:?>
											<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
										<?endif?>
										</p>
									<?endif;?>
								<?endforeach;?>
							<?endforeach;*/?>
							<p class='price'><?=GetMessage("CATALOG_OFFER_FROM")?><?=$arElement["MIN_PRODUCT_OFFER_PRICE_PRINT"]?></p>
						<?else:?>
							<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
								<?if($arPrice["CAN_ACCESS"]):?>
									<?if(count($arParams["PRICE_CODE"]) > 1):?><span class="price_name_catalog"><?=$arResult["PRICES"][$code]["TITLE"];?></span><?endif?>
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<span itemprop="price" class='price new_price'><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/>
										<span class='old_price'><?=$arPrice["PRINT_VALUE"]?></span>
									<?else:?>
										<span itemprop="price" class='price'><?=$arPrice["PRINT_VALUE"]?></span>
									<?endif;?>
								<?endif;?>
							<?endforeach;?>
						<?endif?>
					</a>
				</div>
				<div class="catalog_item_descr">
					<h4><a class="item_title" href="<?=$arElement["DETAIL_PAGE_URL"]?>" title="<?=$arElement["NAME"]?>"><?=$arElement["NAME"]?></a></h4>
				</div>

				<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
					<a href="javascript:void(0)" class='catalog_buy_button' rel="nofollow" onclick="return addOffer2Cart(this, <?=CUtil::PhpToJsObject($arElement["SKU_OFFERS"])?>, '<?=CUtil::JSEscape($arElement["NAME"])?>', '<?=SITE_DIR?>personal/cart/')"><?echo GetMessage("CATALOG_BUY")?></a>
					<?if ($arParams["DISPLAY_COMPARE"] == "Y"):?>
						<a href="javascript:void(0)" class="catalog_item_compare" title="<?=GetMessage("CATALOG_COMPARE_DESCR")?>" onclick="return addOffer2Cart(this, <?=CUtil::PhpToJsObject($arElement["SKU_OFFERS"])?>, '<?=CUtil::JSEscape($arElement["NAME"])?>', '<?=SITE_DIR?>personal/cart/')"><?=GetMessage("CATALOG_COMPARE")?></a>
					<?endif?>
				<?else:?>
					<?if($arElement["CAN_BUY"]):?>
						<a href="<?echo $arElement["ADD_URL"]?>" class='catalog_buy_button' rel="nofollow" onclick="return add2basket(this, '<?=CUtil::JSEscape($arElement["NAME"])?>', '<?=SITE_DIR?>personal/cart/');"><?echo GetMessage("CATALOG_BUY")?></a>
					<?else:?>
						<?if ($USER->IsAuthorized()):?>
							<a href='<?=$arElement["SUBSCRIBE_URL"]?>' onclick="return add2subscribe(this, '<?=CUtil::JSEscape($arElement["NAME"])?>')" class='subscribe_to'><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<?else:?>
							<a href='javascript:void(0)' onclick="subscribePopup(this)" class='subscribe_to'><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<?endif?>
					<?endif?>
					<?if ($arParams["DISPLAY_COMPARE"] == "Y"):?>
						<a href="<?=$arElement["COMPARE_URL"]?>" class="catalog_item_compare" title="<?=GetMessage("CATALOG_COMPARE_DESCR")?>" onclick="return add2compare(this, '<?=$arElement["NAME"]?>', '<?=SITE_DIR."catalog/compare/"?>');"><?=GetMessage("CATALOG_COMPARE")?></a>
					<?endif?>
				<?endif?>
				<?/*if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
					<?foreach($arElement["OFFERS"] as $arOffer):?>
						<?if($arOffer["CAN_BUY"]):?>
								<noindex>
								<a href="<?echo $arOffer["ADD_URL"]?>" class='catalog_buy_button' rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
								</noindex>
						<?elseif(count($arResult["PRICES"]) > 0):?>
							<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
							<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
								"NOTIFY_ID" => $arOffer['ID'],
								"NOTIFY_URL" => htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]),
								"NOTIFY_USE_CAPTHA" => "N"
								),
								false
							);?>
						<?endif?>
						</p>
					<?endforeach;?>
				<?else:?>
					<?if($arElement["CAN_BUY"]):?>
						<a href="<?echo $arElement["ADD_URL"]?>" class='catalog_buy_button' rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
					<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
						<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
						<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
								"NOTIFY_ID" => $arElement['ID'],
								"NOTIFY_URL" => htmlspecialcharsback($arElement["SUBSCRIBE_URL"]),
								"NOTIFY_USE_CAPTHA" => "N"
								),
								false
							);?>
					<?endif?>
				<?endif*/?>


			<?/*if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
				<?foreach($arElement["OFFERS"] as $arOffer):?>
					<?foreach($arParams["OFFERS_FIELD_CODE"] as $field_code):?>
						<small><?echo GetMessage("IBLOCK_FIELD_".$field_code)?>:&nbsp;<?
								echo $arOffer[$field_code];?></small><br />
					<?endforeach;?>
					<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
						<?if($arPrice["CAN_ACCESS"]):?>
							<p><?=$arResult["PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
							<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
								<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
							<?else:?>
								<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
							<?endif?>
							</p>
						<?endif;?>
					<?endforeach;?>
					<p>
					<?if($arParams["DISPLAY_COMPARE"]):?>
						<noindex>
						<a href="<?echo $arOffer["COMPARE_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_COMPARE")?></a>&nbsp;
						</noindex>
					<?endif?>
					<?if($arOffer["CAN_BUY"]):?>
							<noindex>
							<a href="<?echo $arOffer["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
							&nbsp;<a href="<?echo $arOffer["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
							</noindex>
					<?elseif(count($arResult["PRICES"]) > 0):?>
						<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
						<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
							"NOTIFY_ID" => $arOffer['ID'],
							"NOTIFY_URL" => htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]),
							"NOTIFY_USE_CAPTHA" => "N"
							),
							false
						);?>
					<?endif?>
					</p>
				<?endforeach;?>
			<?else:?>
				<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
					<?if($arPrice["CAN_ACCESS"]):?>
						<p><?=$arResult["PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
						<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
							<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
						<?else:?><span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span><?endif;?>
						</p>
					<?endif;?>
				<?endforeach;?>
				<?if($arParams["DISPLAY_COMPARE"]):?>
					<noindex>
					<a href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_COMPARE")?></a>&nbsp;
					</noindex>
				<?endif?>
				<?if($arElement["CAN_BUY"]):?>
					<a href="<?echo $arElement["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>&nbsp;<a href="<?echo $arElement["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
				<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
					<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
					<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
							"NOTIFY_ID" => $arElement['ID'],
							"NOTIFY_URL" => htmlspecialcharsback($arElement["SUBSCRIBE_URL"]),
							"NOTIFY_USE_CAPTHA" => "N"
							),
							false
						);?>
				<?endif?>
			<?endif*/?>
			</div>
		</li>
	<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
	</ul>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"] && !empty($arResult["NAV_STRING"])):?>
<div class="paging_bottom">
	<?=$arResult["NAV_STRING"]?>
</div>
<?endif;?>
<script type="text/javascript">
	/* Catalog item hover - begin*/
	$('.catalog_item').mouseenter(function(){
		$('.catalog_item_compare', this).css({'visibility': 'visible'});
	});
	$('.catalog_item').mouseleave(function(){
		$('.catalog_item_compare', this).css({'visibility': 'hidden'});
	});
	/* Catalog item hover - end*/
</script>