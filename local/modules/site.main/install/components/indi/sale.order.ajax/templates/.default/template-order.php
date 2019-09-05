<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * Отображает группу свойств заказа
 *
 * @param array $group Группа свойств
 * @param boolean $showTitle Выводить название группы
 * @return void
 */
$printPropsGroup = function($group, $showTitle = true) use ($APPLICATION, $arResult) {
	$locationTemplate = 'popup';
	?>
	<article>
		<?
		if ($showTitle) {
			?><h3><?=$group['NAME']?></h3><?
		}
		
		foreach ($group['ITEMS'] as $prop) {
			if ($prop['TYPE'] == 'HIDDEN') {
				?>
				<input
					type="hidden"
					name="<?=$prop['FIELD_NAME']?>"
					value="<?=$prop['VALUE']?>"
				/>
				<?
				continue;
			}
			$domId = 'soa-prop-' . $prop['ID'];
			$groupClass = array('prop-control');
			if ($prop['TYPE'] != 'LOCATION' || $locationTemplate != '') {
				$groupClass[] = 'form-group';
				$groupClass[] = 'has-feedback';
			}
			if (array_key_exists($prop['FIELD_NAME'], $arResult['ERRORS'])) {
				$groupClass[] = 'has-error';
			}
			?>
			<div class="row prop prop-<?=strtolower($prop['CODE'])?>">
				<div class="col-sm-7">
					<div class="<?=implode(' ', $groupClass)?>">
						<?switch ($prop['TYPE']) {
							case 'TEXT':
								$attrs = array(
									'class' => 'form-control',
									'type' => 'text',
								);
								if ($prop['REQUIED']) {
									$attrs['required'] = '';
								}
								if ($prop['IS_ZIP']) {
									$attrs['class'] .= ' type-zip';
									$attrs['maxlength'] = 6;
									$attrs['pattern'] = '[0-9]{6}';
									$attrs['data-ajax-gate'] .= $arResult['CONFIG']['ZIP_AJAX_GATE'];
								} elseif($prop['IS_EMAIL']) {
									$attrs['type'] = 'email';
								} elseif($prop['IS_PHONE']) {
									$attrs['type'] = 'tel';
								}
								foreach ($attrs as $attrName => &$attrValue) {
									$attrValue = $attrName . '="' . htmlspecialcharsEx($attrValue) . '"';
								}
								
								?><label class="control-label<?=$prop['REQUIED'] ? ' required' : ''?>" for="<?=$domId?>"><?=$prop['NAME']?>:</label><?
								
								if ($prop['IS_ZIP']) {
									?>
									<div class="input-group">
										<input
											name="<?=$prop['FIELD_NAME']?>"
											id="<?=$domId?>"
											value="<?=$prop['VALUE']?>"
											<?=implode(' ', $attrs)?>
										/>
										<span class="input-group-btn">
											<button class="btn btn-default btn-zip-check" type="button" data-placement="right" title="Tooltip on right"><?=GetMessage('SOA_DELIVERY_ZIP_CHECK')?></button>
										</span>
									</div>
									<?
								} else {
									?>
									<input
										name="<?=$prop['FIELD_NAME']?>"
										id="<?=$domId?>"
										value="<?=$prop['VALUE']?>"
										<?=implode(' ', $attrs)?>
									/>
									<?
								}
								break;
							
							case 'TEXTAREA':
								?>
								<label class="control-label<?=$prop['REQUIED'] ? ' required' : ''?>" for="<?=$domId?>"><?=$prop['NAME']?>:</label>
								<textarea class="form-control" name="<?=$prop['FIELD_NAME']?>" id="<?=$domId?>"<?=$prop['REQUIED'] ? ' required=""' : ''?>><?=$prop['VALUE']?></textarea>
								<?
								break;
							
							case 'CHECKBOX':
								?>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="<?=$prop['FIELD_NAME']?>" value="Y"<?=$prop['SELECTED'] ? ' checked=""' : ''?> autocomplete="off"/>
										<?=$prop['NAME']?>
									</label>
								</div>
								<?
								break;
							
							case 'RADIO':
								?>
								<label class="<?=$prop['REQUIED'] ? 'required' : ''?>"><?=$prop['NAME']?>:</label>
								<?foreach ($prop['VARIANTS'] as $variant) {
									?>
									<label class="radio-inline">
										<input type="radio" name="<?=$prop['FIELD_NAME']?>" value="<?=$variant['VALUE']?>"<?=$variant['SELECTED'] ? ' checked=""' : ''?> autocomplete="off"/>
										<?=$variant['NAME']?>
									</label>
									<?
								}?>
								<?
								break;
							
							case 'SELECT':
								?>
								<label class="control-label<?=$prop['REQUIED'] ? ' required' : ''?>" for="<?=$domId?>"><?=$prop['NAME']?>:</label>
								<select class="form-control" name="<?=$prop['FIELD_NAME']?>" id="<?=$domId?>"<?=$prop['REQUIED'] ? ' required=""' : ''?> autocomplete="off">
									<?foreach ($prop['VARIANTS'] as $variant) {
										?><option value="<?=$variant['VALUE']?>"<?=$variant['SELECTED'] ? ' selected=""' : ''?>><?=$variant['NAME']?></option><?
									}?>
								</select>
								<?
								break;
							
							case 'MULTISELECT':
								?>
								<label class="control-label<?=$prop['REQUIED'] ? ' required' : ''?>"><?=$prop['NAME']?>:</label>
								<?foreach ($prop['VARIANTS'] as $variant) {
									?>
									<label class="checkbox-inline">
										<input type="checkbox" name="<?=$prop['FIELD_NAME']?>" value="<?=$variant['VALUE']?>"<?=$variant['SELECTED'] ? ' checked=""' : ''?> autocomplete="off"/>
										<?=$variant['NAME']?>
									</label>
									<?
								}?>
								<?
								break;
							
							case 'FILE':
								?>
								<label class="control-label<?=$prop['REQUIED'] ? ' required' : ''?>" for="<?=$domId?>"><?=$prop['NAME']?>:</label>
								<input class="form-control widget uploadpicker" type="file" name="<?=$prop['FIELD_NAME']?>" id="<?=$domId?>"<?=$prop['REQUIED'] ? ' required=""' : ''?>/>
								<?
								break;
							
							case 'LOCATION':
								?>
								<label class="control-label<?=$prop['REQUIED'] ? ' required' : ''?>" for="<?=$domId?>"><?=$prop['NAME']?>:</label>
								
								<?
								$APPLICATION->IncludeComponent(
									'bitrix:sale.ajax.locations',
									$locationTemplate,
									array(
										'COUNTRY_INPUT_NAME' => 'COUNTRY_',
										'REGION_INPUT_NAME' => 'REGION_',
										'CITY_INPUT_NAME' => $prop['FIELD_NAME'],
										'CITY_OUT_LOCATION' => 'Y',
										'LOCATION_VALUE' => $prop['VALUE'],
										'ORDER_PROPS_ID' => $prop['ID'],
										'AJAX_CALL' => 'N',
										'REQUIRED' => $prop['REQUIED'],
										'DOM_ID' => $domId,
										'PROP_VALUE' => $prop['VALUE'],
										'PROP_VALUE_FORMATED' => $prop['VALUE_FORMATED'],
									),
									null,
									array(
										'HIDE_ICONS' => 'Y',
									)
								);
								break;
						}?>
					</div>
				</div>
				<?if ($prop['IS_ZIP'] || $prop['DESCRIPTION']) {
					?>
					<div class="col-sm-5">
						<div class="form-group prop-descr">
							<?
							if ($prop['IS_ZIP']) {
								print GetMessage('SOA_DELIVERY_ZIP_HELPER');
							}
							print $prop['DESCRIPTION'];
							?>
						</div>
					</div>
					<?
				}?>
			</div>
			<?
		}
		?>
	</article>
	<?
};

/**
 * Отображает раздел выбора способа доставки
 *
 * @param array $propsGroup Группа св-в, относящиеся к доставке
 * @return void
 */
$printDelivery = function($propsGroup) use($arResult, $arParams, $printPropsGroup) {
	?>
	<section class="order-group delivery">
		<article class="delivery-services">
			<h3><?=GetMessage('SOA_DELIVERY')?></h3>
			
			<div class="clearfix">
				<dl class="inline">
					<dt class="light basket-total-weight"><?=GetMessage('SOA_SUMMARY_WEIGHT')?>:</dt>
					<dd class="basket-total-weight"><?=$arResult['ORDER']['ORDER_WEIGHT_FORMATED']?></dd>
					<?
					if ($arResult['ORDER']['DELIVERY_PRICE'] > 0) {
						?>
						<dt class="light basket-total-delivery"><?=GetMessage('SOA_SUMMARY_DELIVERY')?>:</dt>
						<dd class="hard basket-total-delivery"><?=$arResult['ORDER']['DELIVERY_PRICE_FORMATED']?></dd>
						<?
					}
					?>
				</dl>
			</div>
			
			<?
			if ($arResult['DELIVERY_SERVICES']) {
				?>
				
				<div class="form-group">
					<?foreach ($arResult['DELIVERY_SERVICES'] as $deliveryService) {
						?>
						<div class="radio delivery-service">
							<div class="row">
								<div class="col-sm-4 delivery-service-control">
									<label>
										<input
											class="observable"
											type="radio"
											name="DELIVERY_ID"
											value="<?=$deliveryService['ID']?>"
											<?=$arResult['DATA']['DELIVERY_ID'] == $deliveryService['ID'] ? ' checked=""' : ''?>
											autocomplete="off"
										/>
										<?=$deliveryService['TYPE'] == 'automatic' ? $deliveryService['HANDLER']['NAME'] . ': ' : ''?><?=$deliveryService['NAME']?>
									</label>
								</div>
								<div class="col-sm-8 delivery-service-descr">
									<?=$deliveryService['DESCRIPTION']?>
									<?
									if (isset($deliveryService['PACKS_COUNT']) && $deliveryService['PACKS_COUNT'] > 1) {
										?>
										<p>
											<?=GetMessage('SOA_DELIVERY_PACKS_HELPER')?>:
											<b><?=$deliveryService['PACKS_COUNT']?></b>
										</p>
										<?
									}
									?>
								</div>
							</div>
						</div>
						<?
					}?>
				</div>
				<?
			} else {
				ShowError(GetMessage('SOA_ERROR_DELIVERY_SERVICE'));
			}
			
			$selectedDeliveryService = $arResult['DELIVERY_SERVICES'][$arResult['DATA']['DELIVERY_ID']];
			
			//Пункты выдачи товаров
			if (is_array($selectedDeliveryService['STORES']) && $selectedDeliveryService['STORES']) {
				?>
				<div class="row buyer-store">
					<div class="col-sm-7">
						<div class="form-group">
							<label for="soa-buyer-store"><?=GetMessage('SOA_STORE_SELECT')?>:</label>
							<select class="form-control" name="BUYER_STORE" id="soa-buyer-store">
								<?foreach ($selectedDeliveryService['STORES'] as $store) {
									?><option
										value="<?=$store['ID']?>"
										<?=$store['SELECTED'] ? ' selected=""' : ''?>
										data-for="soa-buyer-store-<?=$store['ID']?>"
									><?=$store['TITLE']?></option><?
								}?>
							</select>
						</div>
						<section class="form-group">
								<?foreach ($selectedDeliveryService['STORES'] as $store) {
									?>
									<article class="<?=$store['SELECTED'] ? '' : 'hidden '?>" id="soa-buyer-store-<?=$store['ID']?>">
										<p><?=GetMessage('SOA_STORE_ADDRESS')?>: <?=$store['ADDRESS']?></p>
										<?if ($store['PHONE']) {
											?><p><?=$store['PHONE']?></p><?
										}?>
										<?if ($store['SCHEDULE']) {
											?><p><?=$store['SCHEDULE']?></p><?
										}?>
										<?if ($store['GPS_N'] && $store['GPS_S']) {
											?>
											<div class="buyer-store-map">
												<a href="http://maps.yandex.ru/?pt=<?=$store['GPS_S']?>,<?=$store['GPS_N']?>&l=map" class="thumbnail" target="blank" rel="nofollow">
													<img src="//static-maps.yandex.ru/1.x/?ll=<?=$store['GPS_S']?>,<?=$store['GPS_N']?>&pt=<?=$store['GPS_S']?>,<?=$store['GPS_N']?>&&spn=0.002,0.002&l=map&size=600,300" alt="<?=$store['TITLE']?>"/>
												</a>
											</div>
											<?
										}?>
										<?if ($store['DESCRIPTION']) {
											?><p><?=$store['DESCRIPTION']?></p><?
										}?>
									</article>
									<?
								}?>
						</section>
					</div>
				</div>
				<?
			}
			
			//Свойства, связанные с доставкой
			if ($propsGroup) {
				?>
				<section class="order-group order-props">
					<?
					$printPropsGroup($propsGroup, false);
					?>
				</section>
				<?
			}
			?>
		</article>
	</section>
	<?
};

/**
 * Отображает раздел выбора способа оплаты
 *
 * @return void
 */
$printPayment = function() use($arResult, $arParams, $templateFolder) {
	?>
	<section class="order-group payment-systems">
		<article>
			<?
			if ($arResult['PAY_FROM_ACCOUNT'] || $arResult['PAY_SYSTEMS']) {
				?>
				<h3><?=GetMessage('SOA_PAY_SYSTEM')?></h3>
				<?
				//Оплата с ЛС пользователя
				if ($arResult['PAY_FROM_ACCOUNT']) {
					?>
					<div class="form-group">
						<div class="checkbox pay-from-account">
							<div class="row">
								<div class="col-sm-4">
									<label>
										<input
											type="checkbox"
											name="PAY_CURRENT_ACCOUNT"
											value="Y"
											<?=$arResult['DATA']['PAY_CURRENT_ACCOUNT'] ? ' checked=""' : ''?>
											autocomplete="off"
											data-only-full="<?=$arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] ? 1 : 0?>"
										/>
										<?=GetMessage('SOA_PAY_ACCOUNT')?>
									</label>
								</div>
								<div class="col-sm-8 payment-system-descr">
									<p><?=GetMessage('SOA_PAY_ACCOUNT_HAVE')?>: <?=$arResult['USER_ACCOUNT']['CURRENT_BUDGET_FORMATED']?></p>
									<p><?=GetMessage($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] ? 'SOA_PAY_ACCOUNT_FULL' : 'SOA_PAY_ACCOUNT_PARTIAL')?></p>
								</div>
							</div>
						</div>
					</div>
					<?
				}
				?>
				
				<div class="form-group">
					<?foreach ($arResult['PAY_SYSTEMS'] as $paySystem) {
						?>
						<div class="radio payment-system">
							<div class="row">
								<div class="col-sm-4 payment-system-control">
									<label>
										<input
											class="observable"
											type="radio"
											name="PAY_SYSTEM_ID"
											value="<?=$paySystem['ID']?>"<?=$arResult['DATA']['PAY_SYSTEM_ID'] == $paySystem['ID'] ? ' checked=""' : ''?>
											autocomplete="off"
										/>
										<?=$paySystem['NAME']?>
									</label>
								</div>
								<div class="col-sm-8 payment-system-descr">
									<?=$paySystem['DESCRIPTION']?>
								</div>
							</div>
						</div>
						<?
					}?>
				</div>
				<img class="paysystems-approved" src="<?=$templateFolder?>/images/paysystems-approved.png" alt=""/>
				<?
			} else {
				ShowError(GetMessage('SOA_ERROR_PAY_SYSTEM'));
			}
			?>
		</article>
	</section>
	<?
};




//Определяем группу свойств, которую нужно вывести отдельно, после списка способов доставки
$deliveryPropsGroup = array();
if (array_key_exists('DELIVERY', $arResult['ORDER_PROPS'])) {
	$deliveryPropsGroup = $arResult['ORDER_PROPS']['DELIVERY'];
	unset($arResult['ORDER_PROPS']['DELIVERY']);
}
?>

<div class="step-order">
	<div class="order-intro">
		<p><?=GetMessage('SOA_INTRO_TEXT')?></p>
	</div>
	
	<?if ($arResult['ERRORS']) {
		ShowError(implode("\n", $arResult['ERRORS']));
	}?>
	
	<form class="form form-sale-order" method="POST" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPageParam()?>" role="saleorder">
		<section class="order-group person">
			<?
			if ($arResult['PERSON_TYPES']) {
				if (count($arResult['PERSON_TYPES']) > 1) {
					?>
					<article class="person-type">
						<h3><?=GetMessage('SOA_PERSON_TYPE')?></h3>
						<div class="form-group">
							<?foreach ($arResult['PERSON_TYPES'] as $personType) {
								?>
								<label class="radio-inline">
									<input class="observable" type="radio" name="PERSON_TYPE_ID" value="<?=$personType['ID']?>"<?=$arResult['DATA']['PERSON_TYPE_ID'] == $personType['ID'] ? ' checked=""' : ''?> autocomplete="off"/>
									<?=$personType['NAME']?>
								</label>
								<?
							}?>
						</div>
					</article>
					<?
				} else {
					foreach ($arResult['PERSON_TYPES'] as $personType) {
						?><input type="hidden" name="PERSON_TYPE_ID" value="<?=$personType['ID']?>" autocomplete="off"/><?
					}
				}
			} else {
				ShowError(GetMessage('SOA_ERROR_PERSON_TYPE'));
			}
			
			if (count($arResult['USER_PROFILES']) > 1) {
				?>
				<article class="person-profile">
					<div class="form-group">
						<label for="soa-profile-id"><?=GetMessage('SOA_SELECT_PROFILE')?></label>
						<select class="form-control observable" name="PROFILE_ID" id="soa-profile-id" autocomplete="off">
							<?foreach ($arResult['USER_PROFILES'] as $userProfile) {
								?><option value="<?=$userProfile['ID']?>"<?=$arResult['DATA']['PROFILE_ID'] == $userProfile['ID'] ? ' checked=""' : ''?>>
									<?=$userProfile['NAME']?>
								</option><?
							}?>
						</select>
					</div>
				</article>
				<?
			} else {
				foreach ($arResult['USER_PROFILES'] as $userProfile) {
					?><input type="hidden" name="PROFILE_ID" value="<?=$userProfile['ID']?>" autocomplete="off"/><?
				}
			}?>
		</section>
		
		<section class="order-group order-props">
			<?foreach ($arResult['ORDER_PROPS'] as $propsGroup) {
				$printPropsGroup($propsGroup);
			}?>
		</section>
		
		<?
		if ($arParams['DELIVERY_TO_PAYSYSTEM'] == 'd2p') {
			$printDelivery($deliveryPropsGroup);
			$printPayment();
		} else {
			$printPayment();
			$printDelivery($deliveryPropsGroup);
		}
		?>
		
		<?
		if (strlen($arResult['PREPAY_ADIT_FIELDS'])) {
			?>
			<section class="order-group prepay-fields">
				<h3><?=GetMessage('SOA_ADIT_INFO')?></h3>
				<?=$arResult['PREPAY_ADIT_FIELDS']?>
			</section>
			<?
		}
		?>
		
		<section class="order-group order-description">
			<article>
				<h3><?=GetMessage('SOA_ORDER_DESCRIPTION')?></h3>
				<div class="row">
					<div class="col-sm-7">
						<div class="form-group">
							<textarea
								class="form-control"
								name="ORDER_DESCRIPTION"
								id="soa-order-description"
								placeholder="<?=htmlspecialcharsbx(GetMessage('SOA_ORDER_DESCRIPTION_PLACEHOLDER'))?>"
							><?=$arResult['DATA']['ORDER_DESCRIPTION']?></textarea>
						</div>
					</div>
				</div>
			</article>
		</section>
		
		<section class="order-group order-summary">
			<article>
				<h3><?=GetMessage('SOA_PRODUCTS_SUMMARY')?></h3>
				<table class="table table-striped basket-table">
					<thead>
						<tr>
							<th class="basket-name"><?=GetMessage('SOA_BASKET_ITEM_NAME')?></th>
							<th class="basket-price"><?=GetMessage('SOA_BASKET_ITEM_PRICE')?></th>
							<th class="basket-qty"><?=GetMessage('SOA_BASKET_ITEM_QUANTITY')?></th>
							<th class="basket-sum"><?=GetMessage('SOA_BASKET_ITEM_SUM')?></th>
						</tr>
					</thead>
					<tbody>
						<?foreach ($arResult['ORDER']['BASKET_ITEMS'] as $basketItem) {
							?>
							<tr>
								<td class="basket-name">
									<div class="row">
										<div class="col-sm-4 col-md-2">
											<a class="thumbnail" href="<?=$basketItem['DETAIL_PAGE_URL']?>">
												<?if ($basketItem['THUMB']) {
													?><img src="<?=$basketItem['THUMB']['src']?>" alt="<?=$basketItem['NAME']?>"/><?
												} else {
													?><img src="<?=$templateFolder?>/images/no-photo.png" alt="X"/><?
												}?>
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
																	<?if ($setItem['THUMB']) {
																		?><img src="<?=$setItem['THUMB']['src']?>" alt="<?=$setItem['NAME']?>"/><?
																	} else {
																		?><img src="<?=$templateFolder?>/images/no-photo.png" alt="X"/><?
																	}?>
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
								<td class="basket-price">
									<?=$basketItem['PRICE_FORMATED']?>
									<?if ($basketItem['DISCOUNT_PRICE'] > 0) {
										?><br/><s class="summary-old-price"><?=$basketItem['FULL_PRICE_FORMATED']?></s><?
									}?>
								</td>
								<td class="basket-qty"><?=$basketItem['QUANTITY_FORMATED']?></td>
								<td class="basket-sum"><?=$basketItem['SUM_FORMATED']?></td>
							</tr>
							<?
						}?>
					</tbody>
				</table>
				<div class="basket-total clearfix">
					<dl>
						<?
						if ($arResult['ORDER']['LOCAL_DISCOUNT'] > 0) {
							?>
							<dt class="light basket-subtotal"><?=GetMessage('SOA_BASKET_SUMMARY_SUM')?>:</dt>
							<dd class="light basket-subtotal"><?=$arResult['ORDER']['LOCAL_PRICE_WITHOUT_DISCOUNT_FORMATED']?></dd>
							
							<dt class="light basket-total-discount">
								<?=GetMessage('SOA_BASKET_SUMMARY_DISCOUNT')?><?if ($arResult['ORDER']['LOCAL_DISCOUNT_PERCENT'] > 0) {
									?> (<?=$arResult['ORDER']['LOCAL_DISCOUNT_PERCENT_FORMATED']?>)<?
								}?>:
							</dt>
							<dd class="light basket-total-discount"><?=$arResult['ORDER']['LOCAL_DISCOUNT_FORMATED']?></dd>
							
							<dt class="light basket-total-sum"><?=GetMessage('SOA_BASKET_SUMMARY_SUM_DISCOUNT')?>:</dt>
							<dd class="light basket-total-sum"><?=$arResult['ORDER']['ORDER_PRICE_FORMATED']?></dd>
							<?
						} else {
							?>
							<dt class="light basket-total-sum"><?=GetMessage('SOA_BASKET_SUMMARY_SUM')?>:</dt>
							<dd class="light basket-total-sum"><?=$arResult['ORDER']['ORDER_PRICE_FORMATED']?></dd>
							<?
						}
						?>
						
						<?
						if ($arResult['ORDER']['TAX_LIST']) {
							foreach ($arResult['ORDER']['TAX_LIST'] as $tax) {
								?>
								<dt class="light basket-total-tax"><?=$tax['NAME']?> <?=$tax['VALUE_FORMATED']?>:</dt>
								<dd class="light basket-total-tax"><?=$tax['VALUE_MONEY_FORMATED']?></dd>
								<?
							}
						}
						
						if ($arResult['ORDER']['DELIVERY_PRICE'] > 0) {
							?>
							<dt class="light basket-total-delivery"><?=GetMessage('SOA_SUMMARY_DELIVERY')?>:</dt>
							<dd class="light basket-total-delivery"><?=$arResult['ORDER']['DELIVERY_PRICE_FORMATED']?></dd>
							<?
						}
						
						if ($arResult['ORDER']['FROM_ACCOUNT_SUM'] > 0) {
							?>
							<dt class="basket-total-from-account"><?=GetMessage('SOA_SUMMARY_FROM_ACCOUNT_SUM')?>:</dt>
							<dd class="basket-total-from-account"><?=$arResult['ORDER']['FROM_ACCOUNT_SUM_FORMATED']?></dd>
							<?
						}
						?>
						
						<dt class="basket-total-summary basket-total-offset"><?=GetMessage('SOA_SUMMARY_TOTAL')?>:</dt>
						<dd class="basket-total-summary basket-total-offset"><?=$arResult['ORDER']['TOTAL_PRICE_FORMATED']?></dd>
					</dl>
				</div>
			</article>
		</section>
		
		<section class="form-confirm-text">
			<p><?=GetMessage('SOA_CONFIRM_TEXT')?></p>
		</section>
		
		<section class="row">
			<div class="col-sm-6 hidden-xs">
				<?if ($arParams['PATH_TO_CATALOG']) {
					?>
					<div class="form-toolbar form-toolbar-return">
						<a class="btn btn-default" href="<?=$arResult['CONFIG']['BASKET_URL']?>">
							<?=GetMessage('SOA_RETURN_TO_BASKET')?>
						</a>
					</div>
					<?
				}?>
			</div>
			<div class="col-sm-6">
				<div class="form-toolbar form-toolbar-confirm">
					<button class="btn btn-primary" type="submit">
						<?=GetMessage(in_array($arResult['DATA']['PAY_SYSTEM_ID'], $arParams['PAY_SYSTEMS_ONLINE']) ? 'SOA_SUBMIT_ONLINE' : 'SOA_SUBMIT')?>
					</button>
					<div><?=GetMessage('SOA_CONFIRM_AFTER')?></div>
				</div>
			</div>
		</section>
		
		<section class="order-group order-help text-right">
			<?=GetMessage('SOA_CONFIRM_HELPER', array(
				'#FEEDBACK_PHONE#' => $arParams['FEEDBACK_PHONE'],
				'#PATH_TO_FEEDBACK_FORM#' => $arParams['PATH_TO_FEEDBACK_FORM'],
				'#PATH_TO_BASKET#' => $arResult['CONFIG']['BASKET_URL'],
			))?>
		</section>
		
		<input type="hidden" name="CONFIRM_ORDER" value="N" autocomplete="off"/>
		<input type="hidden" name="PROFILE_CHANGE" value="N" autocomplete="off"/>
	</form>
</div>