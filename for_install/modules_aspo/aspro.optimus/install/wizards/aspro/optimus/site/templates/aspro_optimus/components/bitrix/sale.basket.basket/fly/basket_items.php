<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	//echo ShowError($arResult["ERROR_MESSAGE"]);
	$bDelayColumn  = false;
	$bDeleteColumn = false;
	$bWeightColumn = false;
	$bPropsColumn  = false;
	$rowCols = 0;
	if ($normalCount > 0):
	global $arBasketItems;
?>

<div class="module-cart">
	<div class="goods" style="overflow-x: hidden !important;">
		<?if(isset($arResult["ITEMS_IBLOCK_ID"])){?>
			<div class="iblockid" data-iblockid="<?=$arResult["ITEMS_IBLOCK_ID"];?>"></div>
		<?}?>
		<table class="colored" height="100%" width="100%" id="basket_items">
			<thead>
				<tr>
					<?
						foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
						{
							if ($arHeader["id"] == "DELETE"){$bDeleteColumn = true;}	
							if ($arHeader["id"] == "TYPE"){$bTypeColumn = true;}
							if ($arHeader["id"] == "QUANTITY"){$bQuantityColumn = true;}
							if ($arHeader["id"] == "DISCOUNT"){$bDiscountColumn = true;}
						}
					?>
					<?foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
						if (in_array($arHeader["id"], array("TYPE", "DISCOUNT"))) {continue;} // some header columns are shown differently
						elseif ($arHeader["id"] == "PROPS"){$bPropsColumn = true; continue;}
						elseif ($arHeader["id"] == "DELAY"){$bDelayColumn = true; continue;}
						elseif ($arHeader["id"] == "WEIGHT"){ $bWeightColumn = true;}
						elseif ($arHeader["id"] == "DELETE"){ continue;}
						if ($arHeader["id"] == "NAME"):?>
							<td class="thumb-cell"></td><td class="name-th">
						<?else:?><td class="<?=strToLower($arHeader["id"])?>-th"><?endif;?><?=getColumnName($arHeader)?></td>
					<?endforeach;?>
					<?if ($bDelayColumn):?><td class="delay-cell"></td><?endif;?>
					<?if ($bDeleteColumn):?><td class="remove-cell"></td><?endif;?>
				</tr>
			</thead>

			<tbody>
				<?foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):
					$currency = $arItem["CURRENCY"];
					if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
					$arBasketItems[]=$arItem["PRODUCT_ID"];?>
					<tr data-id="<?=$arItem["ID"]?>" product-id="<?=$arItem["PRODUCT_ID"]?>" data-iblockid="<?=$arItem["IBLOCK_ID"]?>"  <?if($arItem["QUANTITY"]>$arItem["AVAILABLE_QUANTITY"]):?>data-error="no_amounth"<?endif;?>>
						<?foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
							if (in_array($arHeader["id"], array("PROPS", "DELAY", "DELETE", "TYPE", "DISCOUNT"))) continue; // some values are not shown in columns in this template
							if ($arHeader["id"] == "NAME"):
							?>
								<td class="thumb-cell">
									<?if( strlen($arItem["PREVIEW_PICTURE"]["SRC"])>0 ){?>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><?endif;?>
											<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=(is_array($arItem["PREVIEW_PICTURE"]["ALT"])?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=(is_array($arItem["PREVIEW_PICTURE"]["TITLE"])?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									<?}elseif( strlen($arItem["DETAIL_PICTURE"]["SRC"])>0 ){?>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><?endif;?>
											<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=(is_array($arItem["DETAIL_PICTURE"]["ALT"])?$arItem["DETAIL_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=(is_array($arItem["DETAIL_PICTURE"]["TITLE"])?$arItem["DETAIL_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									<?}else{?>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><?endif;?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" width="70" height="70" />
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									<?}?>	
									<?if (!empty($arItem["BRAND"])):?><div class="ordercart_brand"><img src="<?=$arItem["BRAND"]?>" /></div><?endif;?>
								</td>
								<td class="name-cell" style="padding-left:0; padding-right:0;">
									<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?endif;?><?=$arItem["NAME"]?><?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?><br />
									<?if ($bPropsColumn):?>	
										<div class="item_props">
											<? foreach ($arItem["PROPS"] as $val) {
													if (is_array($arItem["SKU_DATA"])) {
														$bSkip = false;
														foreach ($arItem["SKU_DATA"] as $propId => $arProp) { if ($arProp["CODE"] == $val["CODE"]) { $bSkip = true; break; } } 
														if ($bSkip) continue;
													} echo '<span class="item_prop"><span class="name">'.$val["NAME"].':&nbsp;</span><span class="property_value">'.$val["VALUE"].'</span></span>';
												}?>
										</div>
									<?endif;?>
									<?if (is_array($arItem["SKU_DATA"]) && $arItem["PROPS"]):
										foreach ($arItem["SKU_DATA"] as $propId => $arProp):
											$isImgProperty = false; // is image property
											foreach ($arProp["VALUES"] as $id => $arVal) { if (isset($arVal["PICT"]) && !empty($arVal["PICT"])) { $isImgProperty = true; break; } }
											$full = (count($arProp["VALUES"]) > 5) ? "full" : "";
											if ($isImgProperty): // iblock element relation property
											?>
												<div class="bx_item_detail_scu_small_noadaptive <?=$full?>">
													<span class="titles"><?=$arProp["NAME"]?>:</span>
													<div class="bx_scu_scroller_container">
														<div class="bx_scu values">
															<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>">
															<?foreach ($arProp["VALUES"] as $valueId => $arSkuValue){
																$selected = "";
																foreach ($arItem["PROPS"] as $arItemProp) { 
																	if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{ if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"]) $selected = "class=\"bx_active\""; }
																};?>
																<li <?=$selected?>>
																	<span><?=$arSkuValue["NAME"]?></span>
																</li>
															<?}?>
															</ul>
														</div>
													</div>
												</div>
											<?else:?>
												<div class="bx_item_detail_size_small_noadaptive <?=$full?>">
													<span class="titles">
														<?=$arProp["NAME"]?>:
													</span>

													<div class="bx_size_scroller_container">
														<div class="bx_size values">
															<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>">
																<?foreach ($arProp["VALUES"] as $valueId => $arSkuValue) {
																	$selected = "";
																	foreach ($arItem["PROPS"] as $arItemProp) {
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"]) 
																		{ if ($arItemProp["VALUE"] == $arSkuValue["NAME"]) $selected = "class=\"bx_active\""; }
																	}?>
																	<li <?=$selected?>><span><?=$arSkuValue["NAME"]?></span></li>
																<?}?>
															</ul>
														</div>
													</div>
												</div>
											<?endif;
										endforeach;
									endif;
									?>
								</td>
							<?elseif ($arHeader["id"] == "QUANTITY"):?>
								<td class="count-cell" style="vertical-align: top !important; padding-left: 2px; padding-right: 2px;">
									<div class="counter_block basket">
										<?
											$ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 1;
											$tmp_ratio=0;
											$tmp_ratio+=$ratio;
											$float_ratio=is_double($tmp_ratio);

											$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
											if (!isset($arItem["MEASURE_RATIO"])){
												$arItem["MEASURE_RATIO"] = 1;
											}
										?>
										<?if (isset($arItem["AVAILABLE_QUANTITY"]) /*&& floatval($arItem["AVAILABLE_QUANTITY"]) != 0*/ && !CSaleBasketHelper::isSetParent($arItem)):?><span onclick="setQuantity('<?=$arItem["ID"]?>', '<?=$arItem["MEASURE_RATIO"]?>', 'down')" class="minus">-</span><?endif;?>										
										<input
											type="text"
											class="text" 
											id="QUANTITY_INPUT_<?=$arItem["ID"]?>"
											name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
											size="2"
											data-id="<?=$arItem["ID"];?>" 
											data-float_ratio="<?=$float_ratio;?>" 
											maxlength="18"
											min="0"
											<?=$max?>
											step="<?=$ratio?>"
											value="<?=$arItem["QUANTITY"]?>"
											onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', '<?=$ratio?>')"
										>	
										<?if (isset($arItem["AVAILABLE_QUANTITY"]) /*&& floatval($arItem["AVAILABLE_QUANTITY"]) != 0*/ && !CSaleBasketHelper::isSetParent($arItem)):?><span onclick="setQuantity('<?=$arItem["ID"]?>', '<?=$arItem["MEASURE_RATIO"]?>', 'up')" class="plus">+</span><?endif;?>
									</div>
									<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" /> 
									<?if($arItem["QUANTITY"]>$arItem["AVAILABLE_QUANTITY"]):?><div class="error"><?=GetMessage("NO_NEED_AMMOUNT")?></div><?endif;?>
								</td>
							<?elseif ($arHeader["id"] == "SUMM"):?>
								<td class="summ-cell"><div class="cost prices"><div class="price"><?=$arItem["SUMM_FORMATED"];?></div></div></td>
							<?elseif ($arHeader["id"] == "PRICE"):?>
								<td class="cost-cell <?=( $bTypeColumn ? 'notes' : '' );?>">
									<div class="cost prices clearfix">
										<?if (strlen($arItem["NOTES"]) > 0 && $bTypeColumn):?>
											<div class="price_name"><?=$arItem["NOTES"]?></div>
										<?endif;?>
										<?if( doubleval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0 && $bDiscountColumn ){?>
											<div class="price"><?=$arItem["PRICE_FORMATED"]?></div>
											<div class="price discount"><strike><?=$arItem["FULL_PRICE_FORMATED"]?></strike></div>
											<input type="hidden" name="item_price_<?=$arItem["ID"]?>" value="<?=$arItem["PRICE"]?>" />
											<input type="hidden" name="item_price_discount_<?=$arItem["ID"]?>" value="<?=$arItem["FULL_PRICE"]?>" />
											<div class="sale_block">
												<?if($arItem["DISCOUNT_PRICE_PERCENT"] && $arItem["DISCOUNT_PRICE_PERCENT"]<100){?>
													<div class="value">-<?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"];?></div>
												<?}?>
												<div class="text"><?=GetMessage("ECONOMY")?> <?=SaleFormatCurrency(round($arItem["DISCOUNT_PRICE"]), $arItem["CURRENCY"]);?></div>
												<div class="clearfix"></div>
											</div>
										<?}else{?>
											<div class="price"><?=$arItem["PRICE_FORMATED"];?></div>
											<input type="hidden" name="item_price_<?=$arItem["ID"]?>" value="<?=$arItem["PRICE"]?>" />
										<?}?>
										<input type="hidden" name="item_summ_<?=$arItem["ID"]?>" value="<?=$arItem["PRICE"]*$arItem["QUANTITY"]?>" />
									</div>
								</td>
							<?elseif ($arHeader["id"] == "WEIGHT"):?>
								<td class="weight-cell"><?=$arItem["WEIGHT_FORMATED"]?></td>
							<?else:?>
								<td class="cell"><?=$arItem[$arHeader["id"]]?></td>
							<?endif;?>
						<?endforeach;?>

						<?if ($bDelayColumn ):?>
							<td class="delay-cell delay">
								<a class="wish_item" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delay"])?>">
									<span class="icon" title="<?=GetMessage("SALE_DELAY");?>"><i></i></span>
								</a>
							</td>
						<?endif;?>
						<?if ($bDeleteColumn):?>
							<td class="remove-cell"><a class="remove" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>" title="<?=GetMessage("SALE_DELETE")?>"><i></i></a></td>
						<?endif;?>
					</tr>
					<?
					endif;
				endforeach;
				?>
				<?
					$arTotal = array();
					if ($bWeightColumn) { $arTotal["WEIGHT"]["NAME"] = GetMessage("SALE_TOTAL_WEIGHT"); $arTotal["WEIGHT"]["VALUE"] = $arResult["allWeight_FORMATED"];}
					if ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") 
					{ 
						$arTotal["VAT_EXCLUDED"]["NAME"] = GetMessage("SALE_VAT_EXCLUDED"); $arTotal["VAT_EXCLUDED"]["VALUE"] = $arResult["allSum_wVAT_FORMATED"];
						$arTotal["VAT_INCLUDED"]["NAME"] = GetMessage("SALE_VAT_INCLUDED"); $arTotal["VAT_INCLUDED"]["VALUE"] = $arResult["allVATSum_FORMATED"];
					}
					if (doubleval($arResult["DISCOUNT_PRICE_ALL"]) > 0)
					{
						$arTotal["PRICE"]["NAME"] = GetMessage("SALE_TOTAL"); 
						$arTotal["PRICE"]["VALUES"]["ALL"] = str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"]);
						$arTotal["PRICE"]["VALUES"]["WITHOUT_DISCOUNT"] = $arResult["PRICE_WITHOUT_DISCOUNT"];
					}
					else
					{
						$arTotal["PRICE"]["NAME"] = GetMessage("SALE_TOTAL"); 
						$arTotal["PRICE"]["VALUES"]["ALL"] = $arResult["allSum_FORMATED"];
					}			
				?>
			</tbody>
		</table>
	</div>
	<?$arError = COptimus::checkAllowDelivery($arResult["allSum"],$currency);?>
	<div class="itog">
		<table class="colored fixed" height="100%" width="100%">
			<?$totalCols = 3 + ($arParams["AJAX_MODE_CUSTOM"] != "Y" ? 1 : 0) + ($arParams["SHOW_FULL_ORDER_BUTTON"] == "Y" && !$arError["ERROR"] ? 1 : 0)?>
			<tfoot>				
				<tr data-id="total_row">
					<td colspan="<?=($totalCols - 1)?>" class="row_titles">
						<?if($arError["ERROR"]){?>
							<div class="icon_error_block"><?=$arError["TEXT"];?></div>
						<?}?>
						<?foreach($arTotal as $key => $value):?>
							<?if ($value["VALUES"] && $value["NAME"]):?><div class="item_title"><?=$value["NAME"]?></div><?endif;?>
						<?endforeach;?>
					</td>
					<td class="row_values">
						<div class="wrap_prices">
							<?foreach($arTotal as $key => $value):?>
								<?if ($value["VALUES"] && $value["NAME"]):?>
									<?if ($key=="PRICE"):?>
										<?if ($arResult["DISCOUNT_PRICE_ALL"]):?>
											<div data-type="price_discount">
												<span class="price"><?=$value["VALUES"]["ALL"];?></span>
												<div class="price discount"><strike><?=$value["VALUES"]["WITHOUT_DISCOUNT"];?></strike></div>
											</div>
										<?else:?>
											<div  data-type="price_normal"><span class="price"><?=$arResult["allSum_FORMATED"];?></span></div>
										<?endif;?>
									<?elseif ($value["VALUE"]):?>
										<div data-type="<?=strToLower($key)?>"><span class="price"><?=$value["VALUE"]?></span></div>
									<?endif;?>
								<?endif;?>
							<?endforeach;?>
						</div>
					</td>
				</tr>
				<tr data-id="total_buttons" class="bottom_btn">
					<td>
						<div class="basket_close">
							<span class="button transparent grey_br sbold close"><span><?=GetMessage("SALE_BACK")?></span></span>
							<div class="description"><?=GetMessage("SALE_BACK_DESCRIPTION");?></div>
						</div>
					</td>
					<?if ($arParams["AJAX_MODE_CUSTOM"]!="Y"):?>
						<td>
							<div class="basket_update clearfix">
								<button type="submit"  name="BasketRefresh" class="button grey sbold refresh"><span><?=GetMessage("SALE_REFRESH")?></span></button>
								<div class="description"><?=GetMessage("SALE_REFRESH_DESCRIPTION");?></div>
							</div>
						</td>
					<?endif;?>
					<td class="back_btn">
						<?if(!$arError["ERROR"] && \Bitrix\Main\Config\Option::get("aspro.optimus", "SHOW_ONECLICKBUY_ON_BASKET_PAGE", "Y") == "Y"){?>
							<div class="basket_back">
								<div class="wrap_button">
									<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="button transparent sbold"><span><?=GetMessage("GO_TO_BASKET")?></span></a>
								</div>
								<div class="description"><?=GetMessage("SALE_TO_BASKET_DESCRIPTION");?></div>
							</div>
						<?}?>
					</td>
					<?if ($arParams["SHOW_FULL_ORDER_BUTTON"]=="Y" && !$arError["ERROR"]):?>
						<td>
							<div class="basket_checkout clearfix">
								<a data-href="<?=$arParams["PATH_TO_ORDER"];?>" href="<?=$arParams["PATH_TO_ORDER"];?>" class="button transparent sbold checkout"><span><?=GetMessage("SALE_ORDER")?></span></a>
								<div class="description"><?=GetMessage("SALE_ORDER_DESCRIPTION");?></div>
							</div>
						</td>
					<?endif;?>
					<td width="19%">
						<?if(!$arError["ERROR"]){?>
							<?if (\Bitrix\Main\Config\Option::get("aspro.optimus", "SHOW_ONECLICKBUY_ON_BASKET_PAGE", "Y") == "Y"):?>
								<div class="basket_fast_order clearfix">
									<a onclick="oneClickBuyBasket()" class="button short sbold fast_order"><span><?=GetMessage("SALE_FAST_ORDER")?></span></a>
									<div class="description"><?=GetMessage("SALE_FAST_ORDER_DESCRIPTION");?></div>
								</div>
							<?else:?>
								<div class="wrap_button">
									<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="button transparent sbold"><span><?=GetMessage("GO_TO_BASKET")?></span></a>
								</div>
								<div class="description"><?=GetMessage("SALE_TO_BASKET_DESCRIPTION");?></div>
							<?endif;?>
						<?}else{?>
							<div class="basket_back">
								<div class="wrap_button">
									<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="button transparent sbold"><span><?=GetMessage("GO_TO_BASKET")?></span></a>
								</div>
								<div class="description"><?=GetMessage("SALE_TO_BASKET_DESCRIPTION");?></div>
							</div>
						<?}?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?else:?>
	<div class="cart_empty">
		<table cellspacing="0" cellpadding="0" width="100%" border="0"><tr><td class="img_wrapp">
			<div class="img">
				<img src="<?=SITE_TEMPLATE_PATH?>/images/empty_cart.png" alt="<?=GetMessage("BASKET_EMPTY")?>" />
			</div>
		</td><td>
			<div class="text">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/empty_fly_cart.php", Array(), Array("MODE"      => "html", "NAME"      => GetMessage("SALE_BASKET_EMPTY"),));?>
			</div>
		</td></tr></table>
		<div class="clearboth"></div>
	</div>
<?endif;?>
<div class="one_click_buy_basket_frame"></div>