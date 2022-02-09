<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<article class="b-detail-wrapper clearfix">
					<div class="b-detail-section">
						<h2 class="b-detail__h2">Ноутбук HP Pavilion g7-2156sr (B6K27EA)</h2>

		<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
				<?if(is_array($arResult["DETAIL_PICTURE"])):?>
					<div class="b-detail__image"><img border="0" id="b-detail__image" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></div>
				<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
					<div class="b-detail__image"><img border="0" id="b-detail__image" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></div>
				<?endif?>
				<?if(count($arResult["PROPERTIES"]["dop_pic"])>0):?>
						<div class="b-detail-slider__wrapper" id="b-detail-slider">
							<a href="#" class="b-slider__control m-prev"></a>
							<div class="b-slider clearfix">
<?
$i=1;
foreach($arResult["PROPERTIES"]["dop_pic"]["VALUE"] as $dop_pic):
$arImagesPath = CFile::GetPath($dop_pic);
$i++;
?>
<?if(reset($arResult["PROPERTIES"]["dop_pic"]["VALUE"])==$dop_pic){echo "<div><div class='b-detail-slider__item active'><a href=".$arResult["DETAIL_PICTURE"]["SRC"]."><img src=".$arResult["DETAIL_PICTURE"]["SRC"]." alt='' /></a></div>";}?>
									<div class="b-detail-slider__item"><a href="<?=$arImagesPath?>"><img src="<?=$arImagesPath?>" alt="" /></a></div>
<?if($i%4==0 AND end($arResult["PROPERTIES"]["dop_pic"]["VALUE"])!=$dop_pic){echo "</div><div>";}?>
<?if(end($arResult["PROPERTIES"]["dop_pic"]["VALUE"])==$dop_pic){echo "</div>";}?>
<?endforeach;?>
							</div>
							<a href="#" class="b-slider__control m-next"></a>
						</div>
				<?endif;?>
		<?endif;?>
						<div class="b-tab-head">
							<a href="#" class="b-tab-head__link active">Описание</a>
							<a href="#" class="b-tab-head__link">Технические зарактеристики</a>
							<a href="#" class="b-tab-head__link">Отзывы (17)</a>
							<span class="b-tab-head__link"><span class="b-rating"><span style="width: <?=($arResult['PROPERTIES']['rating']['VALUE']*20)?>%"></span></span></span>
						</div>
						<?if($arResult["DETAIL_TEXT"]):?>
							<div class="b-detail__text"><?=$arResult["DETAIL_TEXT"]?></div>
						<?endif;?>
						<?if($arResult["PROPERTIES"]["video_link"]["VALUE"]):?>
						<!-- max-width применён и к iframe -->
						<div class="b-detail__video">
						<iframe width="560" height="315" src="<?=$arResult["PROPERTIES"]["video_link"]["VALUE"];?>" frameborder="0" allowfullscreen></iframe>
						</div>
						<?endif;?>						
					</div>
					<div class="b-detail-sidebar">
						<div class="b-detail-sidebar__text">17.3" (1600 x 900), 2.98 кг, Pentium B950, Intel HD Graphics, 4 Гб DDR3, 500 Гб (5400 RPM), DVD Multi (запись CD/DVD), Bluetooth, батарея 6 ячеек, цвет корпуса: чёрный</div>
						<div class="b-detail-sidebar__old_price"><span>20 000</span>.–</div>
						<div class="b-detail-sidebar__new_price">
							<div class="b-slider__price">18 880.–</div>
							<div class="b-slider__price_clearing">Безнал <b>19 540.8.–</b></div>
						</div>
						<div class="b-detail-sidebar__btn">
						<?if($arResult["CAN_BUY"]):?>
							<noindex>
							<a class="b-button m-orange" id="b-detail__image" href="<?echo $arResult["ADD_URL"]?>" rel="nofollow" title="<?echo GetMessage("CATALOG_ADD_TO_BASKET")?>"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
							</noindex>
						<?endif;?>
							<br /><br />
							<button class="b-button" id="b-fast_order"><?echo GetMessage("CATALOG_FUST_ORDER")?></button>
							<div class="b-fast_order m-detail-fast_order fust_order">
								<form action="/includes/fust_order.php" name="fust_order" method="post">
										<input type="text" class="b-cart-field__input" placeholder="<?echo GetMessage("FUST_ORDER_PHONE")?>" name="phone"/>
										<input type="hidden" name="order" value=""/>
										<div class="b-fast_order__text"><?echo GetMessage("CATALOG_FUST_ORDER")?></div>
										<input type="submit" class="b-button__fast m-fast_order" id="fust_order-submit" value="<?echo GetMessage("CATALOG_FUST_ORDER_BUTTON")?>" />
								</form>
							</div>
						</div>
						<div class="b-detail-sidebar__delivery">Доставка 300.– в пределах МКАД<br /><br />Страна-производитель: Китай<br />Гарантия : 12 мес. (от производителя)</div>
						<h2 class="b-pickup__h2">Возможен самовывоз:</h2>
						<div class="b-detail-sidebar__pickup">
							<div class="b-metro m-green">Дубровка</div>
							<div class="b-availability-wrapper">Наличие: <span class="b-availability"><span class="b-availability__item"></span><span class="b-availability__item"></span><span class="b-availability__item"></span><span class="b-availability__item"></span></span></div>
							<div class="b-availability__text">Шарикоподшипниковская улица, д.13, стр.2 Торговый Центр «Дубровка», павильон П7-П9. Ежедневно с 10:00 до 20:00 </div>
						</div>
						<div class="b-detail-sidebar__pickup">
							<div class="b-metro">Пражская</div>
							<div class="b-availability-wrapper">Наличие: <span class="b-availability"><span class="b-availability__item"></span><span class="b-availability__item"></span><span class="b-availability__item"></span><span class="b-availability__item"></span><span class="b-availability__item"></span></span></div>
							<div class="b-availability__text">Шарикоподшипниковская улица, д.13, стр.2 Торговый Центр «Дубровка», павильон П7-П9. Ежедневно с 10:00 до 20:00 </div>
						</div>
						<div class="b-detail-sidebar__pickup">
							<div class="b-metro">Савеловская</div>
							<div class="b-availability-wrapper">Наличие: <span class="b-availability m-medium"><span class="b-availability__item"></span><span class="b-availability__item"></span><span class="b-availability__item"></span></span></div>
							<div class="b-availability__text">Шарикоподшипниковская улица, д.13, стр.2 Торговый Центр «Дубровка», павильон П7-П9. Ежедневно с 10:00 до 20:00 </div>
						</div>
						<div class="b-detail-sidebar__pickup">
							<div class="b-metro">Савеловская</div>
							<div class="b-availability-wrapper">Наличие: <span class="b-availability m-small"><span class="b-availability__item"></span><span class="b-availability__item"></span></span></div>
							<div class="b-availability__text">Шарикоподшипниковская улица, д.13, стр.2 Торговый Центр «Дубровка», павильон П7-П9. Ежедневно с 10:00 до 20:00 </div>
						</div>
						<div class="b-other-method"><?echo GetMessage("CATALOG_OTHER_ORDER")?></div>
						<div class="b-other-method__wrapper">
							<div><b><?echo GetMessage("CATALOG_BACK_CALL")?></b></div>
							<div class="fust_order">
								<form action="/includes/fust_order.php" name="fust_order" method="post">
									<div class="b-footer-form">
										<input type="text" class="b-footer-form__text" placeholder="<?echo GetMessage("CATALOG_YOUR_PHONE_NUMBER")?>" name="phone"/>
										<input type="hidden" name="order" value=""/>
										<input type="submit" class="b-footer-form__submit" value="" id="fust_order-submit"/>
					
									</div>
								</form>
							</div>
							<br />
							<div><?echo GetMessage("CATALOG_ORDER_TEXT")?></div>
							<br /><br />
							<div><b><?echo GetMessage("CATALOG_ONLINE_CALL")?></b></div>
							<div><button onclick="callto:echo" class="b-button__fast"><?echo GetMessage("CATALOG_ONLINE_CALL_BUTTON")?></button></div>
							<br />
							<div><b><?echo GetMessage("CATALOG_FUST_ORDER")?></b></div>
							<br />
							<div>235-979-794 Андрей<br />609-690-565 Николай</div>
						</div>
					</div>
			<td width="100%" valign="top">
				<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
					<?=$arProperty["NAME"]?>:<b>&nbsp;<?
					if(is_array($arProperty["DISPLAY_VALUE"])):
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					elseif($pid=="MANUAL"):
						?><a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
					else:
						echo $arProperty["DISPLAY_VALUE"];?>
					<?endif?></b><br />
				<?endforeach?>
			</td>
		</tr>
	</table>
	<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>
		<?foreach($arResult["OFFERS"] as $arOffer):?>
			<?foreach($arParams["OFFERS_FIELD_CODE"] as $field_code):?>
				<small><?echo GetMessage("IBLOCK_FIELD_".$field_code)?>:&nbsp;<?
						echo $arOffer[$field_code];?></small><br />
			<?endforeach;?>
			<?foreach($arOffer["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<small><?=$arProperty["NAME"]?>:&nbsp;<?
					if(is_array($arProperty["DISPLAY_VALUE"]))
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					else
						echo $arProperty["DISPLAY_VALUE"];?></small><br />
			<?endforeach?>
			<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
				<?if($arPrice["CAN_ACCESS"]):?>
					<p><?=$arResult["CAT_PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
					<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
						<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
					<?else:?>
						<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
					<?endif?>
					</p>
				<?endif;?>
			<?endforeach;?>
			<p>
			<?if($arParams["DISPLAY_COMPARE"]):?>
				<noindex>
				<a href="<?echo $arOffer["COMPARE_URL"]?>" rel="nofollow"><?echo GetMessage("CT_BCE_CATALOG_COMPARE")?></a>&nbsp;
				</noindex>
			<?endif?>
			<?if($arOffer["CAN_BUY"]):?>
				<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
					<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
					<table border="0" cellspacing="0" cellpadding="2">
						<tr valign="top">
							<td><?echo GetMessage("CT_BCE_QUANTITY")?>:</td>
							<td>
								<input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="1" size="5">
							</td>
						</tr>
					</table>
					<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
					<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arOffer["ID"]?>">
					<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="<?echo GetMessage("CATALOG_BUY")?>">
					<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CT_BCE_CATALOG_ADD")?>">
					</form>
				<?else:?>
					<noindex>
					<a href="<?echo $arOffer["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
					&nbsp;<a href="<?echo $arOffer["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CT_BCE_CATALOG_ADD")?></a>
					</noindex>
				<?endif;?>
			<?elseif(count($arResult["CAT_PRICES"]) > 0):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
				<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
					"NOTIFY_ID" => $arOffer['ID'],
					"NOTIFY_URL" => htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]),
					"NOTIFY_USE_CAPTHA" => "N"
					),
					$component
				);?>
			<?endif?>
			</p>
		<?endforeach;?>
	<?else:?>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
			<?if($arPrice["CAN_ACCESS"]):?>
				<p><?=$arResult["CAT_PRICES"][$code]["TITLE"];?>&nbsp;
				<?if($arParams["PRICE_VAT_SHOW_VALUE"] && ($arPrice["VATRATE_VALUE"] > 0)):?>
					<?if($arParams["PRICE_VAT_INCLUDE"]):?>
						(<?echo GetMessage("CATALOG_PRICE_VAT")?>)
					<?else:?>
						(<?echo GetMessage("CATALOG_PRICE_NOVAT")?>)
					<?endif?>
				<?endif;?>:&nbsp;
				<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
					<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
					<?if($arParams["PRICE_VAT_SHOW_VALUE"]):?><br />
						<?=GetMessage("CATALOG_VAT")?>:&nbsp;&nbsp;<span class="catalog-vat catalog-price"><?=$arPrice["DISCOUNT_VATRATE_VALUE"] > 0 ? $arPrice["PRINT_DISCOUNT_VATRATE_VALUE"] : GetMessage("CATALOG_NO_VAT")?></span>
					<?endif;?>
				<?else:?>
					<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
					<?if($arParams["PRICE_VAT_SHOW_VALUE"]):?><br />
						<?=GetMessage("CATALOG_VAT")?>:&nbsp;&nbsp;<span class="catalog-vat catalog-price"><?=$arPrice["VATRATE_VALUE"] > 0 ? $arPrice["PRINT_VATRATE_VALUE"] : GetMessage("CATALOG_NO_VAT")?></span>
					<?endif;?>
				<?endif?>
				</p>
			<?endif;?>
		<?endforeach;?>
		<?if(is_array($arResult["PRICE_MATRIX"])):?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="data-table">
			<thead>
			<tr>
				<?if(count($arResult["PRICE_MATRIX"]["ROWS"]) >= 1 && ($arResult["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arResult["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
					<td><?= GetMessage("CATALOG_QUANTITY") ?></td>
				<?endif;?>
				<?foreach($arResult["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
					<td><?= $arType["NAME_LANG"] ?></td>
				<?endforeach?>
			</tr>
			</thead>
			<?foreach ($arResult["PRICE_MATRIX"]["ROWS"] as $ind => $arQuantity):?>
			<tr>
				<?if(count($arResult["PRICE_MATRIX"]["ROWS"]) > 1 || count($arResult["PRICE_MATRIX"]["ROWS"]) == 1 && ($arResult["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arResult["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
					<th nowrap>
						<?if(IntVal($arQuantity["QUANTITY_FROM"]) > 0 && IntVal($arQuantity["QUANTITY_TO"]) > 0)
							echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_FROM_TO")));
						elseif(IntVal($arQuantity["QUANTITY_FROM"]) > 0)
							echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], GetMessage("CATALOG_QUANTITY_FROM"));
						elseif(IntVal($arQuantity["QUANTITY_TO"]) > 0)
							echo str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_TO"));
						?>
					</th>
				<?endif;?>
				<?foreach($arResult["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
					<td>
						<?if($arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"] < $arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"])
							echo '<s>'.FormatCurrency($arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]).'</s> <span class="catalog-price">'.FormatCurrency($arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"])."</span>";
						else
							echo '<span class="catalog-price">'.FormatCurrency($arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arResult["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"])."</span>";
						?>
					</td>
				<?endforeach?>
			</tr>
			<?endforeach?>
			</table>
			<?if($arParams["PRICE_VAT_SHOW_VALUE"]):?>
				<?if($arParams["PRICE_VAT_INCLUDE"]):?>
					<small><?=GetMessage('CATALOG_VAT_INCLUDED')?></small>
				<?else:?>
					<small><?=GetMessage('CATALOG_VAT_NOT_INCLUDED')?></small>
				<?endif?>
			<?endif;?><br />
		<?endif?>
		<?if($arResult["CAN_BUY"]):?>
			<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arResult["PRODUCT_PROPERTIES"])):?>
				<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
				<table border="0" cellspacing="0" cellpadding="2">
				<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
					<tr valign="top">
						<td><?echo GetMessage("CT_BCE_QUANTITY")?>:</td>
						<td>
							<input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="1" size="5">
						</td>
					</tr>
				<?endif;?>
				<?foreach($arResult["PRODUCT_PROPERTIES"] as $pid => $product_property):?>
					<tr valign="top">
						<td><?echo $arResult["PROPERTIES"][$pid]["NAME"]?>:</td>
						<td>
						<?if(
							$arResult["PROPERTIES"][$pid]["PROPERTY_TYPE"] == "L"
							&& $arResult["PROPERTIES"][$pid]["LIST_TYPE"] == "C"
						):?>
							<?foreach($product_property["VALUES"] as $k => $v):?>
								<label><input type="radio" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"checked"'?>><?echo $v?></label><br>
							<?endforeach;?>
						<?else:?>
							<select name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]">
								<?foreach($product_property["VALUES"] as $k => $v):?>
									<option value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"selected"'?>><?echo $v?></option>
								<?endforeach;?>
							</select>
						<?endif;?>
						</td>
					</tr>
				<?endforeach;?>
				</table>
				<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
				<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arResult["ID"]?>">
				<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="<?echo GetMessage("CATALOG_BUY")?>">
				<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CATALOG_ADD_TO_BASKET")?>">
				</form>
			<?else:?>
				<noindex>
				<a href="<?echo $arResult["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
				&nbsp;<a href="<?echo $arResult["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
				</noindex>
			<?endif;?>
		<?elseif((count($arResult["PRICES"]) > 0) || is_array($arResult["PRICE_MATRIX"])):?>
			<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
				"NOTIFY_ID" => $arResult['ID'],
				"NOTIFY_PRODUCT_ID" => $arParams['PRODUCT_ID_VARIABLE'],
				"NOTIFY_ACTION" => $arParams['ACTION_VARIABLE'],
				"NOTIFY_URL" => htmlspecialcharsback($arResult["SUBSCRIBE_URL"]),
				"NOTIFY_USE_CAPTHA" => "N"
				),
				$component
			);?>
		<?endif?>
	<?endif?>
		<br />

	<?if(count($arResult["LINKED_ELEMENTS"])>0):?>
		<br /><b><?=$arResult["LINKED_ELEMENTS"][0]["IBLOCK_NAME"]?>:</b>
		<ul>
	<?foreach($arResult["LINKED_ELEMENTS"] as $arElement):?>
		<li><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></li>
	<?endforeach;?>
		</ul>
	<?endif?>
	<?
	// additional photos
	$LINE_ELEMENT_COUNT = 2; // number of elements in a row
	if(count($arResult["MORE_PHOTO"])>0):?>
		<a name="more_photo"></a>
		<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
			<img border="0" src="<?=$PHOTO["SRC"]?>" width="<?=$PHOTO["WIDTH"]?>" height="<?=$PHOTO["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /><br />
		<?endforeach?>
	<?endif?>
	<?if(is_array($arResult["SECTION"])):?>
		<br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
	<?endif?>
</div>
