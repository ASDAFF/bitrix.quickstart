<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (is_array($arResult['DETAIL_PICTURE']) || count($arResult["MORE_PHOTO"])>0):?>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.tovar_gallery').fancybox({
				'transitionIn': 'elastic',
				'transitionOut': 'elastic',
				'speedIn': 600,
				'speedOut': 200,
				'overlayShow': false,
				'cyclic' : true,
				'padding': 20,
				'titlePosition': 'over',
				'onComplete': function() {
					$("#fancybox-title").css({ 'top': '100%', 'bottom': 'auto' });
				}
			});
		});
	</script>
<?endif;?>

<?
$numPrices = count($arParams["PRICE_CODE"]);

$sticker = "";
if (array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"]))
{
	foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
		if (array_key_exists($propertyCode, $arResult["PROPERTIES"]) && intval($arResult["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
		{
			$sticker .= "<li class=\"".ToLower($propertyCode)."\">".$arResult["PROPERTIES"][$propertyCode]["NAME"]."</li>";
		}
	if(!$arResult["CAN_BUY"])
		$sticker .= "<li class=\"out_of_order\">".GetMessage("CATALOG_NOT_AVAILABLE")."</li>";
}
?>
<div class="tovar_component_wrapper">
	<div class="tovar_top_block">
		<div class="tovar_image_block">
			<div class="tovar_top_panel">
				<?if ($sticker):?>
					<ul>
						<?=$sticker?>
					</ul>
				<?endif?>
			</div>

			<div id="tovar_img_block" class="sliderkit photosgallery-std">
				<div class="sliderkit-nav">
					<div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-prev"><a rel="nofollow" href="#"><span>Previous line</span></a></div>
					<div class="sliderkit-btn sliderkit-nav-btn sliderkit-nav-next"><a rel="nofollow" href="#"><span>Next line</span></a></div>
					<div class="sliderkit-nav-clip">
						<ul>
							<li class="sliderkit-selected">
								<?if(is_array($arResult["DETAIL_PICTURE"])):?>
									<a class="item_title" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>"><img class="item_img" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"  alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
								<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
									<a class="item_title" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>"><img class="item_img" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
								<?endif?>
							</li>
							<?if(count($arResult["MORE_PHOTO"])>0):?>
								<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
									<li>
										<a href="<?=$PHOTO['SRC']?>"  title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $PHOTO["NAME"])?>"><img src="<?=$PHOTO["SRC"]?>" alt="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $PHOTO["NAME"])?>" /></a>
									</li>
								<?endforeach?>
							<?endif?>
						</ul>
					</div>
				</div>
				<div class="sliderkit-panels">
					<div class="sliderkit-panel sliderkit-panel-active">
						<?if(is_array($arResult["DETAIL_PICTURE"])):?>
							<a class="tovar_gallery item_title" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>" rel="tovar_gallery2"><img class="item_img" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"  alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
						<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
							<a class="tovar_gallery item_title" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>" rel="tovar_gallery2"><img class="item_img" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
						<?endif?>
					</div>
					<?if(count($arResult["MORE_PHOTO"])>0):?>
						<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
							<div class="sliderkit-panel">
								<a class="tovar_gallery" href="<?=$PHOTO["SRC"]?>" rel="tovar_gallery2" title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $PHOTO["NAME"])?>"><img src="<?=$PHOTO["SRC"]?>" alt="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $PHOTO["NAME"])?>" /></a>
							</div>
						<?endforeach?>
					<?endif?>
				</div>
			</div>
		</div><!-- tovar_img_block - end -->
		<div class="tovar_buy_block">

<!-- item has sku-->
		<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>
			<div class="tovar_buy  tovar_buy_tovar_page">
				<?$firstOffer = false;?>
				<?foreach($arResult["SKU_OFFERS"] as $offerID => $arOffer):?>
					<div class="tovar_buy_content">
						<div class="tovar_sku<?if (!$firstOffer) {echo " active"; $arFirstOffer = $arOffer;}?>" onclick="$('.tovar_sku').removeClass('active');$(this).addClass('active'); return addTovarOffer2Cart(<?=CUtil::PhpToJsObject($arOffer)?>)">
							<ul>
								<?foreach($arOffer["PROPS"] as $key=>$arProp):?>
									<li><strong><?=$arProp["PROP_NAME"]?>: </strong><?=$arProp["PROP_VALUE"]?></li>
								<?endforeach;?>
							</ul>
							<div class='sku_prices'>
								<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
									<?if ($numPrices > 1):?><span class="price_name_sku"><?=$arResult["CAT_PRICES"][$code]["TITLE"]?></span><br><?endif?>
									<?if ($arPrice["DISCOUNT_PRICE"]):?>
										<span class='price_sku new_price_sku'><?=$arPrice["DISCOUNT_PRICE"]?></span><br>
										<span class='old_price_sku'><?=$arPrice["PRICE"]?></span><br>
									<?else:?>
										<span class='price_sku'><?=$arPrice["PRICE"]?></span><br>
									<?endif?>
								<?endforeach;?>
							</div>
							<div class="splitter"></div>
							<?if ($arParams["USE_COMPARE"] == "Y"):?>
								<a href="<?=$arOffer["COMPARE_URL"]?>" class="tovar_item_compare" title="<?=GetMessage("CT_BCE_CATALOG_COMPARE_TITLE")?>" onclick="return add2compare(this, '<?=$arResult["NAME"]?>', '<?=SITE_DIR."catalog/compare/"?>');"><?=GetMessage("CT_BCE_CATALOG_COMPARE")?></a>
							<?endif?>
						</div>
					</div>
					<?$firstOffer = true;?>
				<?endforeach;?>
				<div class="tovar_buy_content_btns">

					<a href='<?if ($arFirstOffer["CAN_BUY"]) echo $arFirstOffer["ADD_URL"]?>' class='tovar_buy_button' id="tovar_buy_offer_button" onclick='return add2basket(this, "<?=$arResult["NAME"]?>", "<?=SITE_DIR."personal/cart/"?>");' <?if (!$arFirstOffer["CAN_BUY"]):?>style="display:none"<?endif?>><?=GetMessage("CATALOG_BUY")?></a></br>
					<!--<a href='#' class='tovar_one_click_btn'> упить в 1 клик</a>-->
					<div id="tovar_subscribe_offer_button" <?if ($arFirstOffer["CAN_BUY"]):?>style="display:none"<?endif?>>
						<p><?=GetMessage("CATALOG_SUBSCRIBE_INFO")?></p>
						<a href='<?if (!$arFirstOffer["CAN_BUY"]) echo $arFirstOffer["SUBSCRIBE_URL"]?>' class='tovar_mail_button' id="item_mail_button" onclick="return add2subscribe(this, '<?=$arResult["NAME"]?>')" <?if (!$USER->IsAuthorized()):?>style="display:none"<?endif?>><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<a href='javascript:void(0)' class='tovar_mail_button unactive' onclick="return subscribePopup(this)" id="item_mail_button_unactive" <?if ($USER->IsAuthorized()):?>style="display:none"<?endif?>><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
					</div>

				</div>
			</div>
<!-- item doesn't have sku-->
		<?else:?>
			<div class="tovar_buy tovar_buy_tovar_page">
				<div class="tovar_buy_content">
					<div class="tovar_without_sku">
						<ul>
							<li><strong><?=GetMessage("CATALOG_ITEM")?>: </strong><?=$arResult["NAME"]?></li>
							<?if(!$arResult["CAN_BUY"]):?>
							<li class="sku_out_of_order"><span><?=GetMessage("CATALOG_NOT_AVAILABLE")?></span></li>
							<?endif?>
						</ul>
						<div class='sku_prices'>
							<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
								<?if($arPrice["CAN_ACCESS"]):?>
									<?if(count($arParams["PRICE_CODE"]) > 1):?><span class="price_name_catalog"><?=$arResult["CAT_PRICES"][$code]["TITLE"];?></span><?endif?>
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<span class='price_sku new_price_sku'><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
										<span class='old_price_sku'><?=$arPrice["PRINT_VALUE"]?></span>
									<?else:?>
										<span class='price_sku'><?=$arPrice["PRINT_VALUE"]?></span>
									<?endif?>
								<?endif;?>
							<?endforeach;?>
						</div>
						<div class="splitter"></div>
						<?if ($arParams["USE_COMPARE"] == "Y"):?>
							<a href="<?=$arResult["COMPARE_URL"]?>" class="tovar_item_compare" title="<?=GetMessage("CT_BCE_CATALOG_COMPARE_TITLE")?>" onclick="return add2compare(this, '<?=$arResult["NAME"]?>', '<?=SITE_DIR."catalog/compare/"?>');"><?=GetMessage("CT_BCE_CATALOG_COMPARE")?></a>
						<?endif?>
					</div>
				</div>
				<div class="tovar_buy_content_btns">
					<?if($arResult["CAN_BUY"]):?>
						<a href='<?=$arResult["ADD_URL"]?>' onclick='return add2basket(this, "<?=$arResult["NAME"]?>", "<?=SITE_DIR."personal/cart/"?>");' class='tovar_buy_button'><?=GetMessage("CATALOG_BUY")?></a>
						<!--<a href='#' class='tovar_one_click_btn'> упить в 1 клик</a>-->
					<?else:?>
						<p><?=GetMessage("CATALOG_SUBSCRIBE_INFO")?></p>
						<?if ($USER->IsAuthorized()):?>
							<a href='<?=$arResult["SUBSCRIBE_URL"]?>' onclick="return add2subscribe(this, '<?=$arResult["NAME"]?>')" class='tovar_mail_button'><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<?else:?>
							<a href='javascript:void(0)' onclick="subscribePopup(this)" class='tovar_mail_button unactive'><?=GetMessage("CATALOG_SUBSCRIBE")?></a>
						<?endif?>
					<?endif?>
				</div>
			</div>
		<?endif?>

			<div class="tovar_row_like_btns">
				<p><?=GetMessage("CATALOG_ELEMENT_LIKE")?></p>
				<script type="text/javascript">(function() {
						if(window.pluso) if(typeof window.pluso.start == "function") return;
						var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
						s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
						s.src = ('https:' == d.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
						var h=d[g]('head')[0] || d[g]('body')[0];
						h.appendChild(s);
					})();</script>
				<div class="pluso" data-options="medium,round,line,horizontal,counter,theme=04" data-services="vkontakte,facebook,twitter,google" data-background="transparent"></div>
			</div>

		</div>
	</div>

	<div class="splitter"></div>

	<div class="tovar_center_block">
		<div class="tovar_descr">
			<?if($arResult["DETAIL_TEXT"]):?>
				<div class="tovar_descr_title"><h2><?=GetMessage("CATALOG_PROPERTY_DESCR")?></h2></div>
				<div class="tovar_descr_text"><?=$arResult["DETAIL_TEXT"]?></div>
			<?elseif($arResult["PREVIEW_TEXT"]):?>
				<div class="tovar_descr_title"><h2><?=GetMessage("CATALOG_PROPERTY_DESCR")?></h2></div>
				<div class="tovar_descr_text"><?=$arResult["PREVIEW_TEXT"]?></div>
			<?endif;?>

			<?if (count($arResult["DISPLAY_PROPERTIES"]) > 0):
				$arPropertyRecommend = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"];
				unset($arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]);
				unset($arResult["DISPLAY_PROPERTIES"]["ACCESSORIES"]);
				unset($arResult["DISPLAY_PROPERTIES"]["SAME_GOODS"]);
				if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):?>
					<div class="tovar_descr_title"><h2><?=GetMessage("CATALOG_PROPERTY_PROPS")?></h2></div>
					<ul class="tovar_char">
						<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
							<li>
								<ul>
									<li class="tovar_char_row tovar_char_row_name"><?=$arProperty["NAME"]?></li>
									<li class="tovar_char_row tovar_char_row_value">
										<?if(is_array($arProperty["DISPLAY_VALUE"])):
											echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
										else:
											echo $arProperty["DISPLAY_VALUE"];?>
										<?endif?>
									</li>
								</ul>
							</li>
						<?endforeach?>
					</ul>
				<?endif?>
			<?endif?>
		</div>
	</div>
</div><!-- // tovar_component_wrapper-->

<script type="text/javascript">

	function addTovarOffer2Cart(arOffer)
	{
		if (arOffer["CAN_BUY"])
		{
			$("#tovar_buy_offer_button").css("display", "inline-block");
			$("#tovar_subscribe_offer_button").css("display", "none");
			$("#tovar_buy_offer_button").attr("href", arOffer["ADD_URL"]);
		}
		else
		{
			$("#tovar_buy_offer_button").css("display", "none");
			$("#tovar_subscribe_offer_button").css("display", "inline-block");

			if (BX.message["USER_ID"] > 0)
			{
				$("#tovar_subscribe_offer_button #item_mail_button").attr("href", arOffer["SUBSCRIBE_URL"]);
				$("#tovar_subscribe_offer_button #item_mail_button").css("display", "inline-block");
				$("#tovar_subscribe_offer_button #item_mail_button_unactive").css("display", "none");
			}
			else
			{
				$("#tovar_subscribe_offer_button #item_mail_button").css("display", "none");
				$("#tovar_subscribe_offer_button #item_mail_button_unactive").css("display", "inline-block");
			}
		}
	}

	$(window).load(function(){ //$(window).load() must be used instead of $(document).ready() because of Webkit compatibility
		$("#tovar_img_block").sliderkit({
			mousewheel:true,
			shownavitems:4,
			auto:false,
			circular:true,
			navscrollatend:true,
			panelfx: "none"
		});
	});
</script>