<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



<div id="right-wide-col">

					<div id="breadcrumbs">
							<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array('CACHE_TYPE' => 'N'), false); ?>
							&rarr; <strong><?=$arResult["NAME"];?></strong>
					</div>

					<div id="product">
						<h1><?= (!empty($arResult["PROPERTIES"]["HEADER1"]['VALUE']) ? $arResult["PROPERTIES"]["HEADER1"]['VALUE'] : $arResult["NAME"]); ?></h1>

						<div id="p-photos">


							<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
								<div id="main-img">
									<?if(is_array($arResult["DETAIL_PICTURE_280"])):?>
										<a id="main-image" title="<?= htmlspecialchars($arResult['PREVIEW_PICTURE']['SRC'])?>" rel="prettyPhoto" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>"><img src="<?=$arResult["DETAIL_PICTURE_280"]["SRC"]?>" alt="<?= htmlspecialchars($arResult['PREVIEW_PICTURE']['SRC'])?>" /></a>
										<div id="zoom"></div>
									<?elseif(is_array($arResult["DETAIL_PICTURE"])):?>
										<a id="main-image" title="<?= htmlspecialchars($arResult['PREVIEW_PICTURE']['SRC'])?>" rel="prettyPhoto" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>"><img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?= htmlspecialchars($arResult['PREVIEW_PICTURE']['SRC'])?>" /></a>
										<div id="zoom"></div>
									<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
										<a id="main-image" title="<?= htmlspecialchars($arResult['PREVIEW_PICTURE']['SRC'])?>" rel="prettyPhoto" href="<?=$arResult['PREVIEW_PICTURE']['SRC']?>"><img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?= htmlspecialchars($arResult['PREVIEW_PICTURE']['SRC'])?>" /></a>
										<div id="zoom"></div>
									<?endif?>
								</div>
							<?endif;?>


                            <?if(count($arResult["MORE_PHOTO"])>0):?>
                            	<div id="mit-wrap">
									<ul id="mi-thumbs">
										<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
											<li><a href="<?=$PHOTO['SRC']?>" rel="prettyPhoto[gallery]"><img src="<?=$PHOTO["PREVIEW"]['SRC']?>" alt="" /></a><i></i></li>
										<?endforeach?>
									</ul>
								</div>
							<?endif;?>




						</div>

						<div id="prod-right">


							<div class="pay-buts">

								<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
									<?if($arPrice["CAN_ACCESS"]):?>
										<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
											<div class="price-lbl"><div><span id="p-rice" pprice="<?=$arPrice["DISCOUNT_VALUE"]?>"><?=$arPrice["DISCOUNT_VALUE"]?></span> <span class="rubl">A</span></div></div>
											<div id="old-price"><?=$arPrice["VALUE"]?> <span class="rubl">A</span></div>
										<?else:?>
											<div class="price-lbl"><div><span id="p-rice" pprice="<?=$arPrice["VALUE"]?>"><?=$arPrice["VALUE"]?></span> <span class="rubl">A</span></div></div>
										<?endif?>
									<?endif;?>
								<?endforeach;?>




								<?if($arResult["CAN_BUY"]):?>

									<form action="<?=POST_FORM_ACTION_URI?>" method="get" class="set-params">

										<table>

										<tr>

										<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arResult["PRODUCT_PROPERTIES"])):?>
											<?foreach($arResult["PRODUCT_PROPERTIES"] as $pid => $product_property): if (!$product_property["VALUES"]) continue; ?>
												<td>
												<div class="set-param">
															<select id="val<?echo $pid?>" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?= $arResult["PROPERTIES"][$pid]['NAME']; ?>]">
																<option value=""><?= $arResult["PROPERTIES"][$pid]['NAME']; ?>:</option>
																<?foreach($product_property["VALUES"] as $k => $v):?>
																	<option value="<?echo $v?>" <?if($k == $product_property["SELECTED"]) echo '"selected"'?>><?echo $v?></option>
																<?endforeach;?>
															</select>
												</div>
												</td>
											<?endforeach;?>


										<?endif;?>
											<td><a class="button orange" ref="<?=$arResult["ID"]; ?>" props=".set-param select" settitle="<?=GetMessage("CATALOG_ADD_IN_BASKET")?>"><span><?=GetMessage("CATALOG_ADD_TO_BASKET")?></span></a></td>
	                                        </tr>
	                                        </table>
									</form>


								<?elseif((count($arResult["PRICES"]) > 0) || is_array($arResult["PRICE_MATRIX"])):?>


								<?endif?>


							</div>

							<table cellpadding="0" cellspacing="0" id="p-info">
								<col width="50%" />
								<col width="50%" />

								<?foreach($arResult["DISPLAY_PROPERTIES"] as $k => $v){
									if($k != "SIZE" && $k != "RECOMMEND" && $k != "SEX" && $k != "SALELEADER" && $k != "NEWPRODUCT" && $k != "SPECIALOFFER"){
										?><tr><td><div><span><?=$v["NAME"]?>:</span></div></td><td><i><?
										if(is_array($v["DISPLAY_VALUE"])){
											foreach($v["DISPLAY_VALUE"] as $key =>$val){
												?><?=($key > 0 ? "," : "")?> <?=$val?><?
											}
										}else{?>
											<?=$v["DISPLAY_VALUE"]?>
										<?}?>
										</i></td></tr>
										<?
									}
								}?>
							</table>

							<? if ($arResult['PREVIEW_TEXT']): ?>
								<div id="p-text">
									<? if ($arResult['PREVIEW_TEXT_TYPE'] == 'text'): ?>
										<p><?= nl2br($arResult['PREVIEW_TEXT']); ?></p>
									<? else: ?>
										<?= $arResult['PREVIEW_TEXT']; ?>
									<? endif; ?>
								</div>
							<? endif; ?>







                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/product_info.php"), false);?>


						</div>



						<?if(count($arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["VALUE"]) > 0):?>
							<div id="more-products">
									<div class="color-sep">
										<img src="<?=SITE_TEMPLATE_PATH?>/images/grad_line.png" alt="" />
										<div></div>
									</div>


		                                <? global $arRecPrFilter;

										$arRecPrFilter["ID"] = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["VALUE"];

										$APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "index-slider", array(
												"IBLOCK_TYPE" => "",
												"IBLOCK_ID" => $arParams["IBLOCK_ID"],
												"ELEMENT_SORT_FIELD" => "sort",
												"ELEMENT_SORT_ORDER" => "desc",
												"BASKET_URL" => $arParams["BASKET_URL"],
												"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
												"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
												"CACHE_TYPE" => $arParams["CACHE_TYPE"],
												"CACHE_TIME" => $arParams["CACHE_TIME"],
												"DISPLAY_COMPARE" => "N",
												"PRICE_CODE" => $arParams["PRICE_CODE"],
												"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
												"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
												"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
												"FILTER_NAME" => "arRecPrFilter",
												"DISPLAY_IMG_WIDTH" => "140",
												"DISPLAY_IMG_HEIGHT" => "210",
												"SHARPEN" => $arParams["SHARPEN"],
												"ELEMENT_COUNT" => 10,
												'DISPLAY_BLOCK_TITLE' => $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["NAME"],

											),
											false
										);
										?>


								</div>
							<?endif;?>







					</div>

					<div class="clearfix"></div>
				</div>



