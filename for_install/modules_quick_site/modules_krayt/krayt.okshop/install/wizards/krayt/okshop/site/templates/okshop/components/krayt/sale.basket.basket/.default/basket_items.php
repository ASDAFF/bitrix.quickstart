<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
echo ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;

if ($normalCount > 0):
?>
<div id="basket_items_list">
	<div class="bx_ordercart_order_table_container">
        <table id="basket_items">
			<thead>
				<tr>
					
					<?
					foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader) 
					{

						$arHeaders[] = $arHeader["id"];

						if (in_array($arHeader["id"], array("TYPE"))) // some header columns are shown differently
						{
							continue;
						}
						elseif ($arHeader["id"] == "PROPS")
						{
							$bPropsColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "DELAY")
						{
							$bDelayColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "DELETE")
						{
							$bDeleteColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "WEIGHT")
						{
							$bWeightColumn = true;
						}

						if ($arHeader["id"] == "NAME"):
						?>
							<td class="item" colspan="2" id="col_<?=getColumnId($arHeader)?>">
						<?
						elseif ($arHeader["id"] == "PRICE"):
						?>
							<td class="price" id="col_<?=getColumnId($arHeader)?>">
						<?
						else:
						?>    
                            <? if(getColumnId($arHeader) == "PROPERTY_EMARKET_PREVIEW_CH_VALUE") continue;?>                       
							<td class="custom" id="col_<?=getColumnId($arHeader)?>">
						<?
						endif;
						?>
							<?=getColumnName($arHeader)?>
							</td>
					<?
					}

					if ($bDeleteColumn || $bDelayColumn)
					{
						?>
						<td class="custom"><?=GetMessage("SALE_VSEGO")?></td>
						<?
					}
					?>
				</tr>
			</thead>

			<tbody>
				<?
				foreach ($arResult["GRID"]["ROWS"] as $k => $arItem)
				{

					if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y")
					{
				?>
					<tr id="<?=$arItem["ID"]?>">
						<?
						foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
						{
							if (in_array($arHeader["id"], array("PROPS", "DELAY", "DELETE", "TYPE", "PROPERTY_EMARKET_PREVIEW_CH_VALUE"))) // some values are not shown in columns in this template
								continue;

							if ($arHeader["id"] == "NAME")
							{
							?>
								<td class="itemphoto">
									<div class="bx_ordercart_photo_container">
										<?
                                        if (strlen($arItem["DETAIL_PICTURE_SRC"]) > 0):
											$url = $arItem["DETAIL_PICTURE_SRC"];
										else:
											$url = $templateFolder."/images/no_photo.png";
										endif;
										?>

										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) {?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?}?>
											<div class="bx_ordercart_photo" style="background-image:url('<?=$url?>')"></div>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) {?></a><?}?>
									</div>
									<?
									if (!empty($arItem["BRAND"]))
									{
										?>
										<div class="bx_ordercart_brand">
											<img alt="" src="<?=$arItem["BRAND"]?>" />
										</div>
										<?
									}
									?>
								</td>
								<td class="item">
									<h2 class="bx_ordercart_itemtitle">
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
											<?=$arItem["NAME"]?>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									</h2>
                                    <? if($arItem["PROPERTY_EMARKET_PREVIEW_CH_VALUE"]): ?>
                                        <div><?=$arItem["PROPERTY_EMARKET_PREVIEW_CH_VALUE"]?></div>
                                    <? endif; ?>
									<div class="bx_ordercart_itemart">
										<?
										if ($bPropsColumn):
											foreach ($arItem["PROPS"] as $val):

												if (is_array($arItem["SKU_DATA"]))
												{
													$bSkip = false;
													foreach ($arItem["SKU_DATA"] as $propId => $arProp)
													{
														if ($arProp["CODE"] == $val["CODE"])
														{
															$bSkip = true;
															break;
														}
													}
													if ($bSkip)
														continue;
												}

												echo $val["NAME"].":&nbsp;<span>".$val["VALUE"]."<span><br/>";
											endforeach;
										endif;
										?>
									</div>
									<?
									if (is_array($arItem["SKU_DATA"])):
										foreach ($arItem["SKU_DATA"] as $propId => $arProp):

											// is image property
											$isImgProperty = false;
											foreach ($arProp["VALUES"] as $id => $arVal)
											{
												if (isset($arVal["PICT"]) && !empty($arVal["PICT"]))
												{
													$isImgProperty = true;
													break;
												}
											}

											$full = (count($arProp["VALUES"]) > 5) ? "full" : "";

											if ($isImgProperty): // iblock element relation property
											?>
												<div class="bx_item_detail_scu_small_noadaptive <?=$full?>">

													<span class="bx_item_section_name_gray">
														<?=$arProp["NAME"]?>:
													</span>

													<div class="bx_scu_scroller_container">

														<div class="bx_scu">
															<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
																style="width: 200%; margin-left:0%;"
																class="sku_prop_list"
																>
																<?
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																	$selected = "";
																	foreach ($arItem["PROPS"] as $arItemProp):
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{
																			if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"])
																				$selected = "bx_active";
																		}
																	endforeach;
																?>
																	<li style="width:10%;"
																		class="sku_prop <?=$selected?>"
																		data-value-id="<?=$arSkuValue["XML_ID"]?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>"
																		>
																		<a href="javascript:void(0);">
																			<span style="background-image:url(<?=$arSkuValue["PICT"]["SRC"]?>)"></span>
																		</a>
																	</li>
																<?
																endforeach;
																?>
															</ul>
														</div>

														<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
														<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
													</div>

												</div>
											<?
											else:
											?>
												<div class="bx_item_detail_size_small_noadaptive <?=$full?>">

													<span class="bx_item_section_name_gray">
														<?=$arProp["NAME"]?>:
													</span>

													<div class="bx_size_scroller_container">
														<div class="bx_size">
															<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
																style="width: 200%; margin-left:0%;"
																class="sku_prop_list"
																>
																<?
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																	$selected = "";
																	foreach ($arItem["PROPS"] as $arItemProp):
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{
																			if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
																				$selected = "bx_active";
																		}
																	endforeach;
																?>
																	<li style="width:10%;"
																		class="sku_prop <?=$selected?>"
																		data-value-id="<?=$arSkuValue["NAME"]?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>"
																		>
																		<a href="javascript:void(0);"><?=$arSkuValue["NAME"]?></a>
																	</li>
																<?
																endforeach;
																?>
															</ul>
														</div>
														<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
														<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>);"></div>
													</div>

												</div>
											<?
											endif;
										endforeach;
									endif;
									?>
								</td>
							<?
							}
							elseif ($arHeader["id"] == "QUANTITY")
							{
							?>
								<td class="custom quantity">
                                    <?
									$ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 0;
                                    if(!$ratio)
                                    {
                                        $ratio = 1;
                                    }
                                    
       
									$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
									$useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
									?>
                                    <div class="controls-wrap">
            							<a href="javascript:void(0)" class="minus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$ratio?>, 'down', '<?=$useFloatQuantity?>');"></a>
            							<input
											type="text"
											size="3"
											id="QUANTITY_INPUT_<?=$arItem["ID"]?>"
											name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
											size="2"
											maxlength="18"
											min="0"
											<?=$max?>
											step="<?=$ratio?>"
											value="<?=$arItem["QUANTITY"]?>"
											onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', '<?=$ratio?>', '<?=$useFloatQuantity?>')"
										>
            							<a href="javascript:void(0)" class="plus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$ratio?>, 'up', '<?=$useFloatQuantity?>');"></a>
            						</div>
									<!-- quantity selector for mobile -->
									<?
									echo getQuantitySelectControl(
										"QUANTITY_SELECT_".$arItem["ID"],
										"QUANTITY_SELECT_".$arItem["ID"],
										$arItem["QUANTITY"],
										$arItem["AVAILABLE_QUANTITY"],
										$useFloatQuantity,
										$arItem["MEASURE_RATIO"],
										$arItem["MEASURE_TEXT"]
									);
									?>
									<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />
								</td>
							<?
							}
							elseif ($arHeader["id"] == "PRICE")
							{
							?>
								<td class="price">
									<?if (doubleval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
										<div class="current_price"><?=$arItem["PRICE_FORMATED"]?></div>
										<div class="old_price"><?=$arItem["FULL_PRICE_FORMATED"]?></div>
									<?else:?>
										<div class="current_price"><?=$arItem["PRICE_FORMATED"];?></div>
									<?endif?>
								</td>
							<?
							}
							elseif ($arHeader["id"] == "DISCOUNT")
							{
							?>
								<td class="custom">
									<span><?=getColumnName($arHeader)?>:</span>
									<?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
								</td>
							<?
							}
							elseif ($arHeader["id"] == "WEIGHT")
							{
							?>
								<td class="custom">
									<span><?=getColumnName($arHeader)?>:</span>
									<?=$arItem["WEIGHT_FORMATED"]?>
								</td>
							<?
							}
							else
							{
							?>
								<td class="custom">
									<span><?=getColumnName($arHeader)?>:</span>
									<?=$arItem[$arHeader["id"]]?>
								</td>
							<?
							}
						}

						if ($bDelayColumn || $bDeleteColumn):
						?>
							<td class="control">
								<?
                                echo $arItem["SUM"];
								if ($bDeleteColumn):
								?>
									<a class="remove" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>">&times;</a>
								<?
								endif;
								/*if ($bDelayColumn):
								?>
									<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delay"])?>"><?=GetMessage("SALE_DELAY")?></a>
								<?
								endif;*/
								?>
							</td>
						<?
						endif;
						?>
					</tr>
					<?
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>" />
	<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>" />
	<input type="hidden" id="QUANTITY_FLOAT" value="<?=$arParams["QUANTITY_FLOAT"]?>" />
	<input type="hidden" id="COUNT_DISCOUNT_4_ALL_QUANTITY" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="PRICE_VAT_SHOW_VALUE" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="HIDE_COUPON" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="USE_PREPAYMENT" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />

	<div class="bx_ordercart_order_pay">

		<?if ($arParams["HIDE_COUPON"] != "Y"):?>
			<div class="bx_ordercart_order_pay_left">
				<div class="bx_ordercart_coupon">
					<span><?=GetMessage("STB_COUPON_PROMT")?></span>
					<input type="text" id="COUPON" name="COUPON" value="<?=$arResult["COUPON"]?>" size="21" class="good"> <!-- "bad" if coupon is not valid -->
				</div>
			</div>
		<?endif;?>
        <div class="bx_ordercart_order_pay_top">
    		<div class="bx_ordercart_order_pay_right">
    			<table class="bx_ordercart_order_sum">
    				<?if ($bWeightColumn):?>
    					<tr>
    						<td class="custom_t1"><?=GetMessage("SALE_TOTAL_WEIGHT")?></td>
    						<td class="custom_t2" id="allWeight_FORMATED"><?=$arResult["allWeight_FORMATED"]?></td>
    					</tr>
    				<?endif;?>
    				<?if ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y"):?>
    					<tr>
    						<td><?echo GetMessage('SALE_VAT_EXCLUDED')?></td>
    						<td id="allSum_wVAT_FORMATED"><?=$arResult["allSum_wVAT_FORMATED"]?></td>
    					</tr>
    					<tr>
    						<td><?echo GetMessage('SALE_VAT_INCLUDED')?></td>
    						<td id="allVATSum_FORMATED"><?=$arResult["allVATSum_FORMATED"]?></td>
    					</tr>
    				<?endif;?>
    
    				<?if (doubleval($arResult["DISCOUNT_PRICE_ALL"]) > 0):?>
    					<tr>
    						<td class="fwb"><?=GetMessage("SALE_TOTAL")?></td>
    						<td class="fwb" id="allSum_FORMATED"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></td>
    					</tr>
    					<tr>
    						<td class="custom_t1"></td>
    						<td class="custom_t2" style="text-decoration:line-through; color:#828282;" id="PRICE_WITHOUT_DISCOUNT"><?=$arResult["PRICE_WITHOUT_DISCOUNT"]?></td>
    					</tr>
    				<?else:?>
    					<tr>
    						<td class="custom_t1 fwb"><?=GetMessage("SALE_TOTAL")?></td>
    						<td class="custom_t2 fwb" id="allSum_FORMATED"><?=$arResult["allSum_FORMATED"]?></td>
    					</tr>
    				<?endif;?>
    
    			</table>
    			<div style="clear:both;"></div>
    		</div>
        </div>
		<div style="clear:both;"></div>

		<div class="bx_ordercart_order_pay_center">
			<div style="float:left">
				<input type="submit" class="bt2" name="BasketRefresh" value="<?=GetMessage('SALE_REFRESH')?>">
                <a class="bt2" href="<?=$arUrls["delete_all"]?>"><?=GetMessage('SALE_CLEAR')?></a>
			</div>

			<?if ($arParams["USE_PREPAYMENT"] == "Y"):?>
				<?=$arResult["PREPAY_BUTTON"]?>
				<span><?=GetMessage("SALE_OR")?></span>
			<?endif;?>

			<a href="javascript:void(0)" onclick="checkOut();" class="checkout bg-blue-gradient"><?=GetMessage("SALE_ORDER")?></a>
		</div>
	</div>
</div>
<?
else:
?>
<div id="basket_items_list">
	<table>
		<tbody>
			<tr>
				<td colspan="<?=$numCells?>" style="text-align:center">
					<div class=""><?=GetMessage("SALE_NO_ITEMS");?></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?
endif;
?>