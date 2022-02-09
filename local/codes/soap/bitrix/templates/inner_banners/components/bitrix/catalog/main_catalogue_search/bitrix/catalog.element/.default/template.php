<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$elementname = $arResult['PROPERTIES']['type']['VALUE']." ".$arResult['NAME']." ".$arResult['PROPERTIES']['model']['VALUE']." (".$arResult['PROPERTIES']['article']['VALUE'].")";?>
<?//$APPLICATION->AddChainItem("название элемента", "/catalogue/planshety/apple/posledniy_tovar/");?>

<article class="b-detail-wrapper clearfix">
					<div class="b-detail-section">
						<h2 class="b-detail__h2"><?=$elementname?></h2>

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
						<div class="b-detail-sidebar__text"><?=$arResult["PREVIEW_TEXT"]?></div>
						<!--<div class="b-detail-sidebar__old_price"><span>20 000</span>.–</div>-->
						<div class="b-detail-sidebar__new_price">
						<div class="b-slider__price"><?=$arElement["PRICES"]["price"]["PRINT_VALUE_NOVAT"]?></div>
						<?if($arResult["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]):?>
						<div class="b-slider__price_clearing">Безнал <b><?=$arResult["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]?></b></div>
						<?endif;?>
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
						
                                                
                                                <?  foreach ($arResult['STORE'] as $store){?>
                                                
                                                <div class="b-detail-sidebar__pickup">
							<div class="b-metro m-green"><?=$store["STORE_NAME"]?></div>
                                                        <div class="b-availability-wrapper">Наличие: <span class="b-availability <?if($store['AMOUNT_%'] > 2 && $store['AMOUNT_%'] < 4 ){?> m-medium<?} elseif($store['AMOUNT_%']<=2){?>m-small<?}?>"><?=str_repeat('<span class="b-availability__item"></span>', $store['AMOUNT_%'])?></span></div>
							<div class="b-availability__text"><?=$store["STORE_ADDR"]?> <?=$store['SCHEDULE']?></div>
						</div>
						 
                                                <?}?>
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
</article>
