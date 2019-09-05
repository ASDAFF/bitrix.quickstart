<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }?>

<?if(empty($arResult['ORDERS'])):?>
	<?ShowNote('У вас нет ни одного заказа.');?>
	<?return false;?>
<?endif?>

<?global $APPLICATION;?>
<?$TEMPLATE_PATH = $this->__component->__template->__folder;?>

<div class="order-list-page">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Дата заказа</th>
				<th class="main-number">Номер заказа</th>
				<th class="main-sum">Сумма</th>
				<th class="main-count">Товаров</th>
				<th class="main-status">Статус</th>
			</tr>
		</thead>
		<tbody>
			<?foreach($arResult["ORDERS"] as $key => $arOrder):?>
				<tr class="order">
					<td><nobr><span class="glyphicon glyphicon-plus-sign"<?if($key == 0):?> style="display:none;"<?endif?>></span><span class="glyphicon glyphicon-minus-sign"<?if($key != 0):?> style="display:none;"<?endif?>></span> <a class="show-details fake" href="#"><?=$arOrder["ORDER"]["DATE_INSERT_FORMATED"]?></a></nobr></td>
					<td class="number"><?=$arOrder["ORDER"]["ID"]?></td>
					<td class="sum"><nobr><?=$arOrder["ORDER"]["FORMATED_PRICE"]?></nobr></td>
					<td class="count"><?=count($arOrder["BASKET_ITEMS"])?></td>
					<td class="status"><?=$arResult["INFO"]["STATUS"][$arOrder["ORDER"]["STATUS_ID"]]["NAME"]?>&nbsp;

						<? // кнопка "оплатить" ?>
						<?if($arOrder["PAY_SYSTEM"]["NEW_WINDOW"] != "Y"):?>
							<?
							ob_start();
							$APPLICATION->IncludeFile(
								$TEMPLATE_PATH.'/include/payment.php',
								array(
									"ORDER_ID" => $arOrder["ORDER"]["ID"]
								),
								array(
									"SHOW_BORDER" => false
								)
							);
							$html = ob_get_contents();
							ob_end_clean();
							print str_replace(array(
								'type="submit"',
							), array(
								'type="submit" class="btn btn-default"',
							), $html);
							?>
						<?else:	// ссылка на страницу печатной формы ?>
							<?if(strlen($arParams["PATH_TO_PAYMENT"])):?>
								<a class="btn btn-default" href="<?=$arParams["PATH_TO_PAYMENT"].'?ORDER_ID='.$arOrder["ORDER"]["ID"]?>" target="_blank">Распечатать платежку</a>
							<?endif?>
						<?endif?>
					</td>
				</tr>
				<tr class="order-detail"<?if($key != 0):?> style="display:none;"<?endif?>>
					<td colspan="6">
						
						<table class="table table-striped basket-table">
							<thead>
								<tr>
									<th class="basket-name">Товар</th>
									<th class="basket-price">Цена</th>
									<th class="basket-qty">Количество</th>
									<th class="basket-sum">Сумма</th>
								</tr>
							</thead>
							<tbody>
								<?foreach($arOrder["BASKET_ITEMS"] as $key2 => $basketItem):?>
									<tr class="name">
										<td>
											<div class="row">
												<div class="col-sm-4 col-md-2">
													<a class="thumbnail" href="<?=$basketItem['DETAIL_PAGE_URL']?>">
														<?if($basketItem['PICTURE'] > 0):?>
															<?$image = CFile::ResizeImageGet($basketItem['PICTURE'], array('width' => 64, 'height' => 64));?>
															<img src="<?=$image['src']?>" alt="<?=$basketItem['NAME']?>"/>
														<?else:?>
															<img src="<?=SITE_TEMPLATE_PATH?>/images/nophoto.png" alt="X" height="64" width="64" />
														<?endif?>
													</a>
												</div>
												<div class="col-sm-8 col-md-10">
													<a href="<?=$basketItem['DETAIL_PAGE_URL']?>"><?=$basketItem['NAME']?></a>
													<?if ($basketItem['PROPS']) {
														?>
														<dl class="summary-props clearfix">
															<?foreach ($basketItem['PROPS'] as $prop) {
																?>
																<dt><?=$prop['NAME']?>:</dt>
																<dd><?=$prop['VALUE']?></dd>
																<?
															}?>
														</dl>
														<?
													}?>
												</div>
											</div>
											
											<?if (is_array($basketItem['SET_ITEMS'])) {
												?>
												<div class="row">
													<div class="col-sm-offset-4 col-md-offset-2 col-sm-8 col-md-10">
														<section class="basket-set-items">
															<?foreach ($basketItem['SET_ITEMS'] as $setItem) {
																?>
																<article class="row">
																	<div class="col-sm-4 col-md-2">
																		<a class="thumbnail" href="<?=$setItem['DETAIL_PAGE_URL']?>">
																			<?
																			if ($setItem['PREVIEW_PICTURE'])
																				$image = CFile::ResizeImageGet($setItem['PREVIEW_PICTURE'], array('width' => 64, 'height' => 64));
																			else
																				$image = CFile::ResizeImageGet($setItem['DETAIL_PICTURE'], array('width' => 64, 'height' => 64));
																			?>
																			
																			<?if(strlen($image["src"]) > 0):?>
																				<img src="<?=$image['src']?>" alt="<?=$setItem['NAME']?>"/>
																			<?else:?>
																				<img src="<?=SITE_TEMPLATE_PATH?>/images/nophoto.png" alt="X" height="64" width="64"/>
																			<?endif?>
																		</a>
																	</div>
																	<div class="col-sm-8 col-md-10">
																		<a href="<?=$setItem['DETAIL_PAGE_URL']?>"><?=$setItem['NAME']?></a>
																	</div>
																</article>
																<?
															}?>
														</section>
													</div>
												</div>
												<?
											}?>
										</td>
										<td align="right">
											<nobr><?=$basketItem['PRICE_FORMATED']?></nobr>
											<?if ($basketItem['DISCOUNT_PRICE'] > 0) {
												?><br/><s class="summary-old-price"><?=$basketItem['PRICE_WO_DISCOUNT_FORMATED']?></s><?
											}?>
										</td>
										<td align="center"><nobr><?=$basketItem["QUANTITY"]?> <?=$basketItem["MEASURE_NAME"]?></nobr></td>
										<td class="basket-sum"><nobr><?=$basketItem['PRICE_SUM_FORMATED']?></nobr></td>
									</tr>
								<?endforeach?>
							</tbody>
						</table>
						
						<div class="order-info-block">
							<div class="title">Детальная информация по заказу</div>
							<div class="row">
								<div class="col-sm-3">
									<?
									$user_name = '';
									if(strlen($arOrder["ORDER"]["USER_LAST_NAME"]) > 0)
										$user_name .= $arOrder["ORDER"]["USER_LAST_NAME"] . ' ';
									if(strlen($arOrder["ORDER"]["USER_LAST_NAME"]) > 0)
										$user_name .= $arOrder["ORDER"]["USER_NAME"];
									?>
									<?if(strlen($user_name) > 0):?>
										<b><?=$user_name?></b><br />
									<?endif?>
									<b><a href="mailto:<?=$arOrder["ORDER"]["USER_EMAIL"]?>"><?=$arOrder["ORDER"]["USER_EMAIL"]?></a></b><br />
									<b>8 (910) 123-12-12 [ верстка ]</b>
								</div>
								<div class="col-sm-4 col-md-5 col-lg-6">
									<?if(is_array($arResult["INFO"]["PAY_SYSTEM"][$arOrder["ORDER"]["PAY_SYSTEM_ID"]])):?>
										Оплата <b><?=$arResult["INFO"]["PAY_SYSTEM"][$arOrder["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?></b><br />
									<?endif?>

									<?$arDeliveryID = explode(':', $arOrder["ORDER"]["DELIVERY_ID"]);?>
									<?if(is_numeric($arOrder["ORDER"]["DELIVERY_ID"])):?>
										Доставка <b><?=$arResult["INFO"]["DELIVERY"][$arOrder["ORDER"]["DELIVERY_ID"]]["NAME"]?></b><br />
									<?elseif(count($arDeliveryID) > 1):?>
										Доставка <b><?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arDeliveryID[0]]["NAME"]?>: <?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arDeliveryID[0]]["PROFILES"][$arDeliveryID[1]]["TITLE"]?></b><br />
									<?endif?>
									<b>Ярославль, Некрасова 41а [ верстка ]</b>
								</div>
								<div class="col-sm-5 col-md-4 col-lg-3 sum-block">
									<?if ($arOrder['ORDER']['DISCOUNT_PRICE'] > 0) {
										?>
										<div class="order-sum"><nobr>Сумма заказа: <?=$arOrder['ORDER']['PRICE_WO_DISCOUNT_FORMATED']?></nobr></div>
										<div class="discount"><nobr>Ваша скидка (<?=$arOrder['ORDER']['DISCOUNT_PERCENT_FORMATED']?>): <?=$arOrder['ORDER']['DISCOUNT_PRICE_FORMATED']?></nobr></div>
										<div class="discount"><nobr>Сумма со скидкой: <?=$arOrder['ORDER']['PRICE_WO_DELIVERY_FORMATED']?></nobr></div>
										<?
									} else {
										?>
										<div class="order-sum"><nobr>Сумма заказа: <?=$arOrder['ORDER']['PRICE_WO_DELIVERY_FORMATED']?></nobr></div>
										<?
									}?>
									<div class="delivery"><nobr>Доставка: <?=$arOrder['ORDER']['PRICE_DELIVERY_FORMATED']?></nobr></div>
									<div class="order-sum-with-delivery"><nobr>Итого: <?=$arOrder["ORDER"]["FORMATED_PRICE"]?></nobr></div>
								</div>
							</div>	
						</div>
					
					</td>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>