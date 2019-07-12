<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<div class="search_title">
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<?//echo $arCategory["TITLE"]?>
			<?if($category_id === "all"):?>
				<hr />
				<div class="search_title_all all_result">
					<div class="search_title_all_element">
						<span class="search_title_all_title"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></span>
					</div>
					<div style="clear:both;"></div>
				</div>
			<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
				$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];?>
				<div class="search_title_all_block">
					<?if (is_array($arElement["PICTURE"])):?>
						<img class="search_title_image" src="<?echo $arElement["PICTURE"]["src"]?>" />
					<?endif;?>
					<div class="search_title_element">
						<a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
						<?php
						/*
						foreach($arElement["PRICES"] as $code=>$arPrice)
						{
							if ($arPrice["MIN_PRICE"] != "Y")
								continue;

							if($arPrice["CAN_ACCESS"])
							{
								if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
									<div class="search_title_price">
										<?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
										<span class="search_title_price_old"><?=$arPrice["PRINT_VALUE"]?></span>
									</div>
								<?else:?>
									<div class="search_title_price"><?=$arPrice["PRINT_VALUE"]?></div>
								<?endif;
							}
							if ($arPrice["MIN_PRICE"] == "Y")
								break;
						}
						*/
						?>
					</div>
					<div style="clear:both;"></div>
				</div>
			<?else:?>
				<div class="search_title_item_block others_result">
					<div class="search_title_img_element"></div>
					<div class="search_title_item_element">
						<a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
					</div>
					<div style="clear:both;"></div>
				</div>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
</div>