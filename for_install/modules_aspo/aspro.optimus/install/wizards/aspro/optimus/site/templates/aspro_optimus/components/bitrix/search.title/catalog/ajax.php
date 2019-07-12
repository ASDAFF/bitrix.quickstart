<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult["CATEGORIES"])):?>
	<table class="title-search-result">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
			<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<tr>
				<?if($category_id === "all"):?>
					<td class="title-search-all" colspan="<?=($arParams["SHOW_PREVIEW"]=="Y") ? '3' : '2'?>" >
						<a href="<?=$arItem["URL"]?>"><span class="text"><?=$arItem["NAME"]?></span><span class="icon"><i></i></span></a>
					</td>
				<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
					$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
				?>
					<?if ($arParams["SHOW_PREVIEW"]=="Y"):?>
						<td class="picture">
							<?if (is_array($arElement["PICTURE"])):?>
								<img class="item_preview" align="left" src="<?=$arElement["PICTURE"]["src"]?>" width="<?=$arElement["PICTURE"]["width"]?>" height="<?=$arElement["PICTURE"]["height"]?>">
							<?else:?>
								<img align="left" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" width="38" height="38">
							<?endif;?>
						</td>
					<?endif;?>
					<td class="main">
						<div class="item-text">
							<a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></a>
							<?if ($arParams["SHOW_ANOUNCE"]=="Y"):?><p class="title-search-preview"><?=$arElement["PREVIEW_TEXT"];?></p><?endif;?>
						</div>
						<div class="price cost prices">
							<div class="title-search-price">
								<?if($arElement["MIN_PRICE"]){?>
									<?if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] < $arElement["MIN_PRICE"]["VALUE"]):?>
										<div class="price"><?=$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?></div>
										<div class="price discount">
											<strike><?=$arElement["MIN_PRICE"]["PRINT_VALUE"]?></strike>
										</div>
									<?else:?>
										<div class="price"><?=$arElement["MIN_PRICE"]["PRINT_VALUE"]?></div>
									<?endif;?>
								<?}else{?>
									<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
										<?if($arPrice["CAN_ACCESS"]):?>
											<?if (count($arElement["PRICES"])>1):?>
												<div class="price_name"><?=$arResult["PRICES"][$code]["TITLE"];?></div>
											<?endif;?>
											<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
												<div class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></div>
												<div class="price discount">
													<strike><?=$arPrice["PRINT_VALUE"]?></strike>
												</div>
											<?else:?>
												<div class="price"><?=$arPrice["PRINT_VALUE"]?></div>
											<?endif;?>
										<?endif;?>
									<?endforeach;?>
								<?}?>
							</div>
						</div>
					</td>
				<?elseif(isset($arItem["ICON"])):?>
					<td class="main" colspan="<?=($arParams["SHOW_PREVIEW"]=="Y") ? '3' : '2'?>">
						<div class="item-text">
							<a href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></a>
						</div>
					</td>
				<?endif;?>
			</tr>
			<?endforeach;?>
		<?endforeach;?>
	</table>
<?endif;?>
