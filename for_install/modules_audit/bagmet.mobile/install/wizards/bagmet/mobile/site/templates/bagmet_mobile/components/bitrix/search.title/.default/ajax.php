<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["CATEGORIES"])):?>
<ul class="search_results">
	<li class="search_list">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<ul class="search_category">
			<?if ($category_id !== "all"):?>
				<li class="search_category_title"><span><?echo $arCategory["TITLE"]?></li>
				<li class="search_items">
					<ul>
					<?foreach($arCategory["ITEMS"] as $i => $arItem):?>

						<?if($category_id === "all"):?>
							<li><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></li>
						<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
							$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
							?>
							<li><?
								if (is_array($arElement["PICTURE"])):?>
									<div class="search_item_img"><a href="<?echo $arItem["URL"]?>" width="<?echo $arElement["PICTURE"]["width"]?>"><img src="<?echo $arElement["PICTURE"]["src"]?>" width="<?echo $arElement["PICTURE"]["width"]?>" height="<?echo $arElement["PICTURE"]["height"]?>"></a></div>
								<?endif;?>
								<a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
								<!--<p class="title-search-preview"><?echo $arElement["PREVIEW_TEXT"];?></p>-->
								<br/>
								<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
									<?if($arPrice["CAN_ACCESS"]):?>
										<span class="search_prices"><span class="same_item_price_name_catalog"><?=$arResult["PRICES"][$code]["TITLE"];?>:</span>&nbsp;&nbsp;
											<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
												<span class="same_item_price same_item_new_price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br/>
												<span class="same_item_old_price"><?=$arPrice["PRINT_VALUE"]?></span><br/>
											<?else:?>
												<span class="same_item_price"><?=$arPrice["PRINT_VALUE"]?></span><br/>
											<?endif;?>
										</span>
										<br/>
									<?endif;?>
								<?endforeach;?>
								<div class="splitter"></div>
							</li>
						<?else:?>
							<!--<li><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></li>-->
						<?endif;?>

					<?endforeach;?>
					</ul>
				</li>
			<?else:?>
				<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
					<li class="search_show_all"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></li>
				<?endforeach;?>
			<?endif?>
		</ul>
		<?endforeach;?>
	</li>
</ul>
<?endif;?>