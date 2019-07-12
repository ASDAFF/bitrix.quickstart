<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
		<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
			<div class="goods-gallery">
				<?if(is_array($arResult["DETAIL_PICTURE_280"])):?>
					<div class="big-img" style="background: #ffffff url('<?=$arResult["DETAIL_PICTURE_280"]["SRC"]?>') center center no-repeat;">
						<a class="cloud-zoom" rel="position: 'right', adjustX: 5, adjustY: 5" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>">
							<img src="<?=$arResult["DETAIL_PICTURE_280"]["SRC"]?>" alt="" />
						</a>
					</div>
				<?elseif(is_array($arResult["DETAIL_PICTURE"])):?>
					<div class="big-img" style="background: #ffffff url('<?=$arResult["DETAIL_PICTURE"]["SRC"]?>') center center no-repeat;">
						<a class="cloud-zoom" rel="position: 'right', adjustX: 5, adjustY: 5" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>">
							<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="" />
						</a>
					</div>				<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
					<div class="big-img" style="background: #ffffff url('<?=$arResult['PREVIEW_PICTURE']['SRC']?>') center center no-repeat;">
						<a class="cloud-zoom" rel="position: 'right', adjustX: 5, adjustY: 5" href="<?=$arResult['PREVIEW_PICTURE']['SRC']?>">
							<img src="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" alt="" />
						</a>
					</div>
				<?endif?>

				<?if(count($arResult["MORE_PHOTO"])>0):?>
					<div class="gallery">
						<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
							<div class="item" data-image="<?=$PHOTO["PREVIEW"]['SRCMID']?>" data-big_image="<?=$PHOTO["PREVIEW"]['SRCBIG']?>" style="background: #ffffff url('<?=$PHOTO["PREVIEW"]['SRC']?>') center center no-repeat;"></div>
						<?endforeach?>
					</div>
				<?endif;?>



			</div>
		<?endif;?>
			<div class="goods-info">
				<?foreach($arResult["DISPLAY_PROPERTIES"] as $k => $v){
					if($k != "SIZE" && $k != "RECOMMEND" && $k != "SEX"){
						?><span style="color: grey;"><?=$v["NAME"]?>:</span> <?
						if(is_array($v["DISPLAY_VALUE"])){
							foreach($v["DISPLAY_VALUE"] as $key =>$val){
								?><?=($key > 0 ? "," : "")?> <?=$val?><?
							}
						}else{?>
							<?=$v["DISPLAY_VALUE"]?>
						<?}
						?><br /><?
					}
				}?>


				<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
					<?if($arPrice["CAN_ACCESS"]):?>
						<p><b><?=$arResult["CAT_PRICES"][$code]["TITLE"];?></b>
						<?if($arParams["PRICE_VAT_SHOW_VALUE"] && ($arPrice["VATRATE_VALUE"] > 0)):?>
							<?if($arParams["PRICE_VAT_INCLUDE"]):?>
								(<?echo GetMessage("CATALOG_PRICE_VAT")?>)
							<?else:?>
								(<?echo GetMessage("CATALOG_PRICE_NOVAT")?>)
							<?endif?>
						<?endif;?>:&nbsp;
						<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
							<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
							<?if($arParams["PRICE_VAT_SHOW_VALUE"]):?><br />
								<?=GetMessage("CATALOG_VAT")?>:&nbsp;&nbsp;<span class="catalog-vat catalog-price"><?=$arPrice["DISCOUNT_VATRATE_VALUE"] > 0 ? $arPrice["PRINT_DISCOUNT_VATRATE_VALUE"] : GetMessage("CATALOG_NO_VAT")?></span>
							<?endif;?>
						<?else:?>
							<span class="price"><?=$arPrice["PRINT_VALUE"]?></span>
							<?if($arParams["PRICE_VAT_SHOW_VALUE"]):?><br />
								<?=GetMessage("CATALOG_VAT")?>:&nbsp;&nbsp;<span class="catalog-vat catalog-price"><?=$arPrice["VATRATE_VALUE"] > 0 ? $arPrice["PRINT_VATRATE_VALUE"] : GetMessage("CATALOG_NO_VAT")?></span>
							<?endif;?>
						<?endif?>
						</p>
					<?endif;?>
				<?endforeach;?>

				<p><?=strip_tags($arResult["PREVIEW_TEXT"])?></p>




				<?if($arResult["CAN_BUY"]):?>

					<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arResult["PRODUCT_PROPERTIES"])):?>
						<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" class="set-params" id="add2basketform">

						<?foreach($arResult["PRODUCT_PROPERTIES"] as $pid => $product_property):?>
							<?if(sizeof($product_property["VALUES"]) > 0){?>
							<div class="set-size">
								<div class="title"><?=GetMessage("CHOOSE")?> <?echo $arResult["PROPERTIES"][$pid]["NAME"]?>:  </div>
								<div class="sizes">
									<?if($arResult["PROPERTIES"][$pid]["PROPERTY_TYPE"] == "L" && $arResult["PROPERTIES"][$pid]["LIST_TYPE"] == "C"):?>
										<?foreach($product_property["VALUES"] as $k => $v):?>
											<label><input type="radio" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"checked"'?>><?echo $v?></label><br>
										<?endforeach;?>
									<?else:?>
										<select id="val<?echo $pid?>" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" style="display:none;">
											<?foreach($product_property["VALUES"] as $k => $v):?>
												<option value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo ' selected'?>><?echo $v?></option>
											<?endforeach;?>
										</select>
										<?foreach($product_property["VALUES"] as $k => $v):?>
											<span data-id="<?echo $pid?>" data-value="<?=$k?>"<?if($k == $product_property["SELECTED"]) echo 'class="active"'?>><?echo $v?></span>
										<?endforeach;?>
									<?endif;?>

								</div>
								<br />
								<a href="<?=SITE_DIR?>information/table-sizes/" target="_blank"><?=GetMessage("VIEW_SIZES")?></a>
							</div>
							<?}?>

						<?endforeach;?>


						<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
							<div class="set-count">
								<?=GetMessage("CT_BCE_QUANTITY")?>
								<span class="change-count" id="minus-count"></span>
								<input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" readonly  value="1" id="quantity" />
								<span class="change-count" id="plus-count"></span>
							</div>
						<?endif;?>

						<div class="sbmt">
							<input type="submit" value="<?=GetMessage("FAVORITES")?>" class="grey-but" id="FavoriteButton" />
							<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CATALOG_ADD_TO_BASKET")?>" class="orange-but">
						</div>

						<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
						<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arResult["ID"]?>">


						</form>
					<?else:?>
						<noindex>
						<a href="<?echo $arResult["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
						&nbsp;<a href="<?echo $arResult["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
						</noindex>
					<?endif;?>

				<?elseif((count($arResult["PRICES"]) > 0) || is_array($arResult["PRICE_MATRIX"])):?>
					<br /><b style="color:red;"><?=GetMessage("CATALOG_NOT_AVAILABLE2")?></b><br /><br />
					<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
						"NOTIFY_ID" => $arResult['ID'],
						"NOTIFY_URL" => htmlspecialcharsback($arResult["SUBSCRIBE_URL"]),
						"NOTIFY_USE_CAPTHA" => "N"
						),
						false
					);?>
				<?endif?>



			</div>
			<div class="clear"></div>




