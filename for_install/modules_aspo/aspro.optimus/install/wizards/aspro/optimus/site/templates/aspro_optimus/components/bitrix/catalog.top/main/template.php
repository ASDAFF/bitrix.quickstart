<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<? $this->setFrameMode( true ); ?>
<?
$sliderID  = "specials_slider_wrapp_".$this->randString();
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
<?if($arResult["ITEMS"]):?>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
	$totalCount = COptimus::GetTotalCount($arItem);
	$arQuantityData = COptimus::GetQuantityArray($totalCount);
	$arItem["FRONT_CATALOG"]="Y";
	
	$strMeasure='';
	if($arItem["OFFERS"]){
		$strMeasure=$arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
	}else{
		if (($arParams["SHOW_MEASURE"]=="Y")&&($arItem["CATALOG_MEASURE"])){
			$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
			$strMeasure=$arMeasure["SYMBOL_RUS"];
		}
	}
	?>
	<li id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="catalog_item">
		<div class="image_wrapper_block">
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb">
				<?if($arItem["PROPERTIES"]["HIT"]["VALUE"]){?>
					<div class="stickers">
						<?if (is_array($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
							<?foreach($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"] as $key=>$class){?>
								<div><div class="sticker_<?=strtolower($class);?>"><?=$arItem["PROPERTIES"]["HIT"]["VALUE"][$key]?></div></div>
							<?}?>
						<?endif;?>
						<?if($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
							<div><div class="sticker_sale_text"><?=$arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
						<?}?>
					</div>
				<?}?>
				<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
					<div class="like_icons">
						<?if($arItem["CAN_BUY"] && empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
							<div class="wish_item_button">
								<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>"><i></i></span>
								<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>"><i></i></span>
							</div>
						<?endif;?>
						<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
							<div class="compare_item_button">
								<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
								<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
							</div>
						<?endif;?>
					</div>
				<?endif;?>
				<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
				<?elseif(!empty($arItem["DETAIL_PICTURE"])):?>
					<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 170, "height" => 170), BX_RESIZE_IMAGE_PROPORTIONAL, true );?>
					<img src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
				<?else:?>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
				<?endif;?>
			</a>
		</div>
		<div class="item_info">
			<div class="item-title">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a>
			</div>
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
			<div class="cost prices clearfix">
				<?if($arItem["OFFERS"]):?>
					<?$minPrice = false;
					if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE']))
						$minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
					
					if($minPrice["VALUE"]>$minPrice["DISCOUNT_VALUE"] && $arParams["SHOW_OLD_PRICE"]=="Y"){?>
						<div class="price"><?=GetMessage("CATALOG_FROM");?> <?=$minPrice["PRINT_DISCOUNT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?></div>
						<div class="price discount">
							<span><?=$minPrice["PRINT_VALUE"];?></span>
						</div>
						<?/*if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
							<div class="sale_block">
								<?$percent=round(($minPrice["DISCOUNT_DIFF"]/$minPrice["VALUE"])*100, 2);?>
								<?if($percent && $percent<100){?>
									<div class="value">-<?=$percent;?>%</div>
								<?}?>
								<div class="text"><?=GetMessage("CATALOG_ECONOMY");?> <?=$minPrice["PRINT_DISCOUNT_DIFF"];?></div>
								<div class="clearfix"></div>
							</div>
						<?}*/?>
					<?}else{?>
						<div class="price"><?=GetMessage("CATALOG_FROM");?> <?=$minPrice['PRINT_DISCOUNT_VALUE'];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?></div>
					<?}?>
				<?elseif($arItem["PRICES"]):?>
					<?
					$arCountPricesCanAccess = 0;
					foreach($arItem["PRICES"] as $key => $arPrice){
						if($arPrice["CAN_ACCESS"]){
							++$arCountPricesCanAccess;
						}
					}?>
					<?foreach($arItem["PRICES"] as $key => $arPrice):?>
						<?if($arPrice["CAN_ACCESS"]):
							$percent=0;?>
							<?$price = CPrice::GetByID($arPrice["ID"]);?>
							<?if($arCountPricesCanAccess > 1):?>
								<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
							<?endif;?>
							<?if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] && $arParams["SHOW_OLD_PRICE"]=="Y"):?>
								<div class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?></div>
								<div class="price discount">
									<span><?=$arPrice["PRINT_VALUE"];?></span>
								</div>
								<?/*if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
									<div class="sale_block">
										<?$percent=round(($arPrice["DISCOUNT_DIFF"]/$arPrice["VALUE"])*100, 2);?>
										<?if($percent && $percent<100){?>
											<div class="value">-<?=$percent;?>%</div>
										<?}?>
										<div class="text"><?=GetMessage("CATALOG_ECONOMY");?> <?=$arPrice["PRINT_DISCOUNT_DIFF"];?></div>
										<div class="clearfix"></div>
									</div>
								<?}*/?>
							<?else:?>
								<div class="price"><?=$arPrice["PRINT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?>/<?=$strMeasure?><?endif;?></div>
							<?endif;?>
						<?endif;?>
					<?endforeach;?>
				<?endif;?>
			</div>
			<?$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);?>
			<div class="buttons_block clearfix">
				<?=$arAddToBasketData["HTML"]?>
			</div>
		</div>
	</li>
<?endforeach;?>
<?else:?>
	<div class="empty_items"></div>
	<script type="text/javascript">			
		$('.top_blocks li[data-code=BEST]').remove();
		$('.tabs_content tab[data-code=BEST]').remove();
		if(!$('.slider_navigation.top li').length){
			$('.tab_slider_wrapp.best_block').remove();
		}
		if($('.bottom_slider').length){
			if($('.empty_items').length){
				$('.empty_items').each(function(){
					var index=$(this).closest('.tab').index();
					$('.top_blocks .tabs>li:eq('+index+')').remove();
					$('.tabs_content .tab:eq('+index+')').remove();
				})				
				$('.tabs_content .tab.cur').trigger('click');
			}
		}
	</script>
<?endif;?>