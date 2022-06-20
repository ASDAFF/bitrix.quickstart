<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
<div class="header_slider">
	<div id="slides">
		<div class="slides_container">
		<?
		foreach($arResult["ITEMS"] as $key => $arItem)
		{
		?>
            <div class="slide R2D2" itemscope itemtype = "http://schema.org/Product">
                <div class="slider_img">
                    <div class="w"></div>
                    <div class="s1"></div>
                    <div class="s2"></div>
					<?if (strlen($arItem["DETAIL_PICTURE"]["SRC"])>0):?>
						<div class="photo">
							<table>
								<tr>
									<td><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="item_img" itemprop="image" src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"/></a></td>
								</tr>
							</table>
							<!-- Special -->
							<?if (in_array($arParams["FLAG_PROPERTY_CODE"], array("SPECIALOFFER","SPECIALOFFER","NEWPRODUCT"))):?>
							<div class="specialoffer"><?=GetMessage($arParams["FLAG_PROPERTY_CODE"]."_TITLE")?></div>
							<?endif?>
							<!-- // Special -->
						</div>
					<?else:?>
						<div class="photo">
							<table>
								<tr><td><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="no-photo-div-big" style="height:170px; width:170px;"></div></a>
								</td></td>
							</table>
						</div>
					<?endif?>
                </div>
                <div class="info">
                    <h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="item_title" title="<?=$arItem["NAME"]?>"><span itemprop = "name"><?=$arItem["NAME"]?></span></a></h2>
                    <p><span itemprop = "description"><?=mb_substr(strip_tags($arItem["PREVIEW_TEXT"]), 0, 260); ?></span></p><br/>
                    <!-- 445 max -->
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="more"><?=GetMessage("CATALOG_MORE")?></a>
                    <table class="buy">
                        <tr>
                            <td>
								<span itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">
								<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
								{
									if (count($arItem["OFFERS"]) > 1)
                                    {
                                    ?>
                                        <span itemprop = "price" class="item_price">
                                    <?
										echo GetMessage("CR_PRICE_OT").$arItem["PRINT_MIN_OFFER_PRICE"];
                                    ?>
                                        </span>
                                    <?
                                    }
                                    else
									{
										foreach($arItem["OFFERS"] as $arOffer):?>
											<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
												<?if($arPrice["CAN_ACCESS"]):?>
													<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
														<span itemprop = "price" class="item_price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br>
                                                        <span class="old_price"><?=$arPrice["PRINT_VALUE"]?></span><br>
														<?else:?>
														<span itemprop = "price" class="item_price"><?=$arPrice["PRINT_VALUE"]?></span>
														<?endif?>
													<?endif;?>
												<?endforeach;?>
											<?endforeach;
									}
								}
								else // if product doesn't have offers   
									if(count($arItem["PRICES"])>0 && $arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] == $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE']):
										foreach($arItem["PRICES"] as $code=>$arPrice):
											if($arPrice["CAN_ACCESS"]):
												?>
												<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
												<?=$arPrice["PRINT_DISCOUNT_VALUE"]?><br>
                                                <span itemprop = "price" class="old_price"><?=$arPrice["PRINT_VALUE"]?></span>
												<?else:?>
                                                <span itemprop = "price"><?=$arPrice["PRINT_VALUE"]?></span>
												<?endif;?>
												<?
											endif;
										endforeach;
									else:
										$price_from = '';
										if($arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] > $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'])
										{
											$price_from = GetMessage("CR_PRICE_OT");
										}
										CModule::IncludeModule("sale")
										?>
										<?=$price_from?><?=FormatCurrency($arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'], CSaleLang::GetLangCurrency(SITE_ID))?>
										<?
									endif;
									/*if ($arItem["PRICE"]["DISCOUNT_PRICE"] < $arItem["PRICE"]["PRICE"]["PRICE"]):?>
										<?=$arItem["PRICE"]["DISCOUNT_PRICE_FORMAT"]?>
										<br /><span itemprop = "price" class="old_price"><?=$arItem["PRICE"]["PRICE_FORMAT"]?></span>
									<?else:?>
										<?=$arItem["PRICE"]["PRICE_FORMAT"]?>
									<?endif*/?>
								</span>
                            </td>
                            <td>
								<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))
								{
									?>
									<a href="javascript:void(0)" class="bt3 addtoCart" id="catalog_add2cart_offer_link_<?=$arItem['ID']?>" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arItem["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arItem["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'cart');"><span></span><?echo GetMessage("CATALOG_ADD")?></a>
									<?
								}
								else
								{
									?>
									<?if ($arItem["CAN_BUY"]):?>
										<noindex><a href="<?=$arItem["ADD_URL"]?>" class="bt3 addtoCart" rel="nofollow" onclick="return addToCart(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', 'noCart');" id="catalog_add2cart_link_<?=$arItem['ID']?>"><span></span><?=GetMessage("CATALOG_ADD")?></a></noindex>
									<?elseif ($arNotify[SITE_ID]['use'] == 'Y'):?>
										<?if ($USER->IsAuthorized()):?>
											<noindex><a href="<?echo $arItem["SUBSCRIBE_URL"]?>" rel="nofollow" class="bt2" onclick="return addToSubscribe(this, '<?=GetMessage("CATALOG_IN_SUBSCRIBE")?>');" id="catalog_add2cart_link_<?=$arItem['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
										<?else:?>
											<noindex><a href="javascript:vpid(0)" rel="nofollow" class="bt2" onclick="showAuthForSubscribe(this, <?=$arItem['ID']?>, '<?echo $arItem["SUBSCRIBE_URL"]?>')" id="catalog_add2cart_link_<?=$arItem['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
										<?endif;?>
									<?endif?>
								<?
								}
								?>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
		<?
		}
		?>
		</div>
		<a href="#" class="prev"></a>
		<a href="#" class="next"></a>
	</div>
</div>