<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<?$arParams["BASKET_ITEMS"]=($arParams["BASKET_ITEMS"] ? $arParams["BASKET_ITEMS"] : array());?>
	<?if($arParams["AJAX_REQUEST"]=="N"){?>
	<table class="module_products_list">
		<tbody>
	<?}?>
			<?$arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);?>
			<?foreach($arResult["ITEMS"]  as $arItem){
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
				$totalCount = COptimus::GetTotalCount($arItem);
				$arQuantityData = COptimus::GetQuantityArray($totalCount);

				$strMeasure = '';
				if(!$arItem["OFFERS"] || $arParams['TYPE_SKU'] === 'TYPE_2'){
					if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
						$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
						$strMeasure = $arMeasure["SYMBOL_RUS"];
					}
					$arItem["OFFERS_MORE"]="Y";
				}
				elseif($arItem["OFFERS"]){
					$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
					$arItem["OFFERS_MORE"]="Y";
				}
				?>
				<tr class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<td class="foto-cell">
						<div class="image_wrapper_block">
							<?
							$a_alt=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] );
							$a_title=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] );
							?>
							<?if( !empty($arItem["DETAIL_PICTURE"]) || !empty($arItem["PREVIEW_PICTURE"]) ){?>
								<?
								$picture=($arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : $arItem["DETAIL_PICTURE"]);
								$img_preview = CFile::ResizeImageGet( $picture, array( "width" => 50, "height" => 50 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);?>
								<?if ($arParams["LIST_DISPLAY_POPUP_IMAGE"]=="Y"){?>
									<a class="popup_image fancy" href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" title="<?=$a_title;?>">
								<?}?>
									<img src="<?=$img_preview["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
								<?if ($arParams["LIST_DISPLAY_POPUP_IMAGE"]=="Y"){?>
									</a>
								<?}?>
							<?}else{?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
							<?}?>
						</div>
					</td>
					<td class="item-name-cell">
						<div class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
						<?if($arParams["SHOW_RATING"] == "Y"):?>
							<div class="rating">
								<?$APPLICATION->IncludeComponent(
								   "bitrix:iblock.vote",
								   "element_rating_front",
								   Array(
									  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
									  "IBLOCK_ID" => $arItem["IBLOCK_ID"],
									  "ELEMENT_ID" =>$arItem["ID"],
									  "MAX_VOTE" => 5,
									  "VOTE_NAMES" => array(),
									  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
									  "CACHE_TIME" => $arParams["CACHE_TIME"],
									  "DISPLAY_AS_RATING" => 'vote_avg'
								   ),
								   $component, array("HIDE_ICONS" =>"Y")
								);?>
							</div>
						<?endif;?>
						<?=$arQuantityData["HTML"];?>
					</td>
					<td class="price-cell">
						<div class="cost prices clearfix">
							<?if( count( $arItem["OFFERS"] ) > 0 ){?>
								<?$minPrice = false;
								if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
									$minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
								$prefix='';
								if('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] =='table'){
									$prefix=GetMessage("CATALOG_FROM");
								}
								if($minPrice["VALUE"]>$minPrice["DISCOUNT_VALUE"]){?>
									<div class="price">
										<?if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
											<?=$prefix;?> <?=$minPrice["PRINT_DISCOUNT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?>
										<?endif;?>
									</div>
									<div class="price discount">
										<span><?=$minPrice["PRINT_VALUE"];?></span>
									</div>
								<?}else{?>
									<div class="price">
										<?if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
											<?=$prefix;?> <?=$minPrice['PRINT_DISCOUNT_VALUE'];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?>
										<?endif;?>
									</div>
								<?}?>
							<?}else{?>
								<?
								$arCountPricesCanAccess = 0;
								$min_price_id=0;
								foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} }
								?>
								<?foreach($arItem["PRICES"] as $key => $arPrice){?>
									<?if($arPrice["CAN_ACCESS"]){
										$percent=0;
										if($arPrice["MIN_PRICE"]=="Y"){
											$min_price_id=$arPrice["PRICE_ID"];
										}?>
										<?$price = CPrice::GetByID($arPrice["ID"]);?>
										<?if($arCountPricesCanAccess > 1):?>
											<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
										<?endif;?>
										<?if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"]){?>
											<div class="price">
												<?if(strlen($arPrice["PRINT_DISCOUNT_VALUE"])):?>
													<?=$arPrice["PRINT_DISCOUNT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?>
												<?endif;?>
											</div>
											<div class="price discount">
												<span><?=$arPrice["PRINT_VALUE"];?></span>
											</div>
										<?}else{?>
											<div class="price">
												<?if(strlen($arPrice["PRINT_VALUE"])):?>
													<?=$arPrice["PRINT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?>
												<?endif;?>
											</div>
										<?}?>
									<?}?>
								<?}?>
							<?}?>							
						</div>

						<div class="basket_props_block" id="bx_basket_div_<?=$arItem["ID"];?>" style="display: none;">
							<?if (!empty($arItem['PRODUCT_PROPERTIES_FILL'])){
								foreach ($arItem['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){?>
									<input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
									<?if (isset($arItem['PRODUCT_PROPERTIES'][$propID]))
										unset($arItem['PRODUCT_PROPERTIES'][$propID]);
								}
							}
							$arItem["EMPTY_PROPS_JS"]="Y";
							$emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
							if (!$emptyProductProperties){
								$arItem["EMPTY_PROPS_JS"]="N";?>
								<div class="wrapper">
									<table>
										<?foreach ($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo){?>
											<tr>
												<td><? echo $arItem['PROPERTIES'][$propID]['NAME']; ?></td>
												<td>
													<?if('L' == $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE']	&& 'C' == $arItem['PROPERTIES'][$propID]['LIST_TYPE']){
														foreach($propInfo['VALUES'] as $valueID => $value){?>
															<label>
																<input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
															</label>
														<?}
													}else{?>
														<select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
															foreach($propInfo['VALUES'] as $valueID => $value){?>
																<option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
															<?}?>
														</select>
													<?}?>
												</td>
											</tr>
										<?}?>
									</table>
								</div>
								<?
							}?>
						</div>
						<?$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, array(), 'small', $arParams);?>
						<div class="adaptive_button_buy">
							<!--noindex-->
								<?=$arAddToBasketData["HTML"]?>
							<!--/noindex-->
						</div>				
					</td>
					<td class="but-cell item_<?=$arItem["ID"]?>">
						<div class="counter_wrapp">
							<?if($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && !count($arItem["OFFERS"]) && $arAddToBasketData["ACTION"] == "ADD" && $arItem["CAN_BUY"]):?>
								<div class="counter_block" data-item="<?=$arItem["ID"];?>" <?=(in_array($arItem["ID"], $arParams["BASKET_ITEMS"]) ? "style='display: none;'" : "");?>>
									<span class="minus">-</span>
									<input type="text" class="text" name="count_items" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
									<span class="plus" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
								</div>
							<?endif;?>
							<div class="button_block <?=(in_array($arItem["ID"], $arParams["BASKET_ITEMS"])  || $arAddToBasketData["ACTION"] == "ORDER" || !$arItem["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] ? "wide" : "");?>">
								<!--noindex-->
									<?=$arAddToBasketData["HTML"]?>
								<!--/noindex-->
							</div>
						</div>
					</td>
					<?if((!$arItem["OFFERS"] && $arParams["DISPLAY_WISH_BUTTONS"] != "N" ) || ($arParams["DISPLAY_COMPARE"] == "Y")):?>
						<td class="like_icons <?=(((!$arItem["OFFERS"] && $arParams["DISPLAY_WISH_BUTTONS"] != "N" && $arItem["CAN_BUY"]) && ($arParams["DISPLAY_COMPARE"] == "Y")) ? " full" : "")?>">
							<div class="wrapp_stockers">
								<div class="like_icons">
									<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
										<?if(!$arItem["OFFERS"]):?>
											<div class="wish_item_button">
												<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
												<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
											</div>
										<?elseif($arItem["OFFERS"]):?>
											<?foreach($arItem["OFFERS"] as $arOffer):?>
												<?if($arOffer['CAN_BUY']):?>
													<div class="wish_item_button o_<?=$arOffer["ID"];?>" style="display: none;">
														<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$arOffer["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
														<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$arOffer["ID"]?>" data-iblock="<?=$arOffer["IBLOCK_ID"]?>"><i></i></span>
													</div>
												<?endif;?>
											<?endforeach;?>
										<?endif;?>
									<?endif;?>
									<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
										<?if(!$arItem["OFFERS"] || $arParams["TYPE_SKU"] !== 'TYPE_1'):?>
											<div class="compare_item_button">
												<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
												<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
											</div>
										<?elseif($arItem["OFFERS"]):?>
											<?foreach($arItem["OFFERS"] as $arOffer):?>
												<div class="compare_item_button o_<?=$arOffer["ID"];?>" style="display: none;">
													<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arOffer["ID"]?>" ><i></i></span>
													<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arOffer["ID"]?>"><i></i></span>
												</div>
											<?endforeach;?>
										<?endif;?>
									<?endif;?>
								</div>
							</div>
						</td>
					<?endif;?>
				</tr>
			<?}?>
	<?if($arParams["AJAX_REQUEST"]=="N"){?>
		</tbody>
	</table>
		<script>
			$(document).ready(function(){
				$('.sort_header').fadeIn();
			})
		</script>
	<?}?>
	<?if($arParams["AJAX_REQUEST"]=="Y"){?>
		<div class="wrap_nav">
		<tr <?=($arResult["NavPageCount"]>1 ? "" : "style='display: none;'");?>><td>
	<?}?>

		<div>
		<div class="bottom_nav <?=$arParams["DISPLAY_TYPE"];?>" <?=($arParams["AJAX_REQUEST"]=="Y"  && $arResult["NavPageCount"]<=1 ? "style='display: none; '" : "");?>>
			<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
		</div>
		</div>

	<?if($arParams["AJAX_REQUEST"]=="Y"){?>
		</td></tr>
		</div>
	<?}?>
	<script type="text/javascript">
		$('.module_products_list').removeClass('errors');
	</script>
<?}else{?>
	<?if($arParams["AJAX_REQUEST"]!="Y"){?>
		<table class="module_products_list errors">
		<tbody>
		<tr><td>
	<?}?>
		<script type="text/javascript">
			$('.module_products_list').addClass('errors');
		</script>
		<div class="module_products_list_b">
			<div class="no_goods">
				<div class="no_products">
					<div class="wrap_text_empty">
						<?if($_REQUEST["set_filter"]){?>
							<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products_filter.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
						<?}else{?>
							<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
						<?}?>
					</div>
				</div>
				<?if($_REQUEST["set_filter"]){?>
					<span class="button wide"><?=GetMessage('RESET_FILTERS');?></span>
				<?}?>
			</div>
		</div>
		<?if($arParams["AJAX_REQUEST"]!="Y"){?>
		</td></tr>
		</tbody>
		</table>
	<?}?>
<?}?>