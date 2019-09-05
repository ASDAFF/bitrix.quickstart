<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * Отображает таблицу-корзину заданного типа
 *
 * @param array $group Группа свойств
 * @param boolean $showTitle Выводить название группы
 * @return void
 */
$printBasketTable = function($items, $type) use ($templateFolder) {
	?>
	<table class="table table-striped basket-table">
		<thead>
			<tr>
				<th class="basket-name"><?=GetMessage('SOA_BASKET_ITEM_NAME')?></th>
				<th class="basket-price"><?=GetMessage('SOA_BASKET_ITEM_PRICE')?></th>
				<th class="basket-qty"><?=GetMessage('SOA_BASKET_ITEM_QUANTITY')?></th>
				<th class="basket-sum"><?=GetMessage('SOA_BASKET_ITEM_SUM')?></th>
				<th class="basket-tools"></th>
			</tr>
		</thead>
		<tbody>
			<?foreach ($items as $basketItem) {
				?>
				<tr>
					<td class="basket-name">
						<div class="row">
							<div class="col-sm-5 col-md-3">
								<a class="thumbnail" href="<?=$basketItem['DETAIL_PAGE_URL']?>">
									<?if ($basketItem['THUMB']) {
										?><img src="<?=$basketItem['THUMB']['src']?>" alt="<?=$basketItem['NAME']?>"/><?
									} else {
										?><img src="<?=$templateFolder?>/images/no-photo.png" alt="X"/><?
									}?>
								</a>
							</div>
							<div class="col-sm-7 col-md-9">
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
								<div class="col-sm-offset-5 col-md-offset-3 col-sm-7 col-md-9">
									<section class="basket-set-items">
										<?foreach ($basketItem['SET_ITEMS'] as $setItem) {
											?>
											<article class="row">
												<div class="col-sm-5 col-md-3">
													<a class="thumbnail" href="<?=$setItem['DETAIL_PAGE_URL']?>">
														<?if ($setItem['THUMB']) {
															?><img src="<?=$setItem['THUMB']['src']?>" alt="<?=$setItem['NAME']?>"/><?
														} else {
															?><img src="<?=$templateFolder?>/images/no-photo.png" alt="X"/><?
														}?>
													</a>
												</div>
												<div class="col-sm-7 col-md-9">
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
					<td class="basket-qty">
						<div class="basket-qty-control">
							<div class="input-group input-group-sm">
								<span class="input-group-addon fast-control fast-control-dec">&minus;</span>
								<input class="form-control" type="text" name="QTY[<?=$basketItem['ID']?>]" value="<?=$basketItem['QUANTITY']?>"/>
								<span class="input-group-addon fast-control fast-control-inc">&plus;</span>
							</div>
						</div>
						<?=$basketItem['MEASURE_TEXT']?>
					</td>
					<td class="basket-sum"><?=$basketItem['SUM_FORMATED']?></td>
					<td class="basket-tools">
						<ul class="list-unstyled">
							<?switch ($type) {
								case 'ready':
									?>
									<li>
										<a class="fake tools-delay" href="#" data-id="<?=$basketItem['ID']?>"><?=GetMessage('SOA_BASKET_DELAY')?></a>
									</li>
									<?
									break;
								case 'delay':
									?>
									<li>
										<a class="fake tools-revert" href="#" data-id="<?=$basketItem['ID']?>"><?=GetMessage('SOA_BASKET_REVERT')?></a>
									</li>
									<?
									break;
							}?>
							<li>
								<a class="fake tools-delete" href="#" data-id="<?=$basketItem['ID']?>" data-confirm="<?=htmlspecialcharsEx(sprintf(GetMessage('SOA_BASKET_DELETE_CONFIRM'), $basketItem['NAME']))?>"><?=GetMessage('SOA_BASKET_DELETE')?></a>
							</li>
						</ul>
					</td>
				</tr>
				<?
			}?>
		</tbody>
	</table>
	<?
};
?>

<div class="step-basket">
	<form class="form form-sale-basket" method="GET" action="<?=$APPLICATION->GetCurPageParam('', array('order'))?>" role="salebasket">
		<?if ($arResult['BASKET']['BASKET_ITEMS']) {
			?>
			<section class="basket-section basket-section-ready">
				<?$printBasketTable($arResult['BASKET']['BASKET_ITEMS'], 'ready')?>
				
				<div class="basket-total clearfix">
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group form-coupon has-feedback<?=$arResult['COUPON_IS_VALID'] === true ? ' has-success' : ($arResult['COUPON_IS_VALID'] === false ? ' has-error' : '')?>">
								<label class="control-label" for="soa-coupon"><?=GetMessage('SOA_BASKET_COUPON')?>:</label>
								<div class="input-group">
									<input class="form-control" type="text" name="COUPON" id="soa-coupon" value="<?=$arResult['DATA']['COUPON']?>" autocomplete="off"/>
									<span class="input-group-btn">
										<button class="btn btn-default" type="button"><?=GetMessage('SOA_BASKET_COUPON_APPLY')?></button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-9">
							<div class="clearfix">
								<dl>
									<?
									if ($arResult['BASKET']['LOCAL_DISCOUNT'] > 0) {
										?>
										<dt class="light basket-subtotal"><?=GetMessage('SOA_BASKET_SUMMARY_SUM')?>:</dt>
										<dd class="light basket-subtotal"><?=$arResult['BASKET']['LOCAL_PRICE_WITHOUT_DISCOUNT_FORMATED']?></dd>
										
										<dt class="light basket-total-discount">
											<?=GetMessage('SOA_BASKET_SUMMARY_DISCOUNT')?><?if ($arResult['BASKET']['LOCAL_DISCOUNT_PERCENT'] > 0) {
												?> (<?=$arResult['BASKET']['LOCAL_DISCOUNT_PERCENT_FORMATED']?>)<?
											}?>:
										</dt>
										<dd class="light basket-total-discount"><?=$arResult['BASKET']['LOCAL_DISCOUNT_FORMATED']?></dd>
										
										<dt class="basket-total-sum"><?=GetMessage('SOA_BASKET_SUMMARY_SUM_DISCOUNT')?>:</dt>
										<dd class="basket-total-sum"><?=$arResult['BASKET']['LOCAL_PRICE_FORMATED']?></dd>
										<?
									} else {
										?>
										<dt class="basket-total-sum"><?=GetMessage('SOA_BASKET_SUMMARY_SUM')?>:</dt>
										<dd class="basket-total-sum"><?=$arResult['BASKET']['LOCAL_PRICE_FORMATED']?></dd>
										<?
									}
									?>
								</dl>
							</div>
							<div class="help-block text-right"><?=GetMessage('SOA_BASKET_DELIVERY_LATER')?></div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="form-toolbar form-toolbar-return">
								<a class="btn btn-default" href="<?=$arParams['PATH_TO_CATALOG']?>"><?=GetMessage('SOA_BASKET_RETURN_CATALOG')?></a>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-toolbar form-toolbar-confirm">
								<input type="hidden" name="ACTION_TYPE" value="" autocomplete="off"/>
								<input type="hidden" name="ACTION_ITEM" value="" autocomplete="off"/>
								<input class="hidden" type="submit"/>
								<a class="btn btn-primary btn-confirm" href="<?=$arResult['CONFIG']['ORDER_URL']?>"><?=GetMessage('SOA_BASKET_SUBMIT')?></a>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?
		} else {
			?>
			<?=GetMessage('SOA_BASKET_EMPTY')?>
			<div class="row">
				<div class="col-xs-6">
					<div class="form-toolbar form-toolbar-return">
						<a class="btn btn-default" href="<?=$arParams['PATH_TO_CATALOG']?>"><?=GetMessage('SOA_BASKET_RETURN_CATALOG')?></a>
					</div>
				</div>
			</div>
			<?
		}?>
		
		<?
		if ($arResult['BASKET']['DELAY_ITEMS']) {
			?>
			<section class="basket-section basket-section-delay">
				<h3><?=GetMessage('SOA_DELAY_TITLE')?></h3>
				<?$printBasketTable($arResult['BASKET']['DELAY_ITEMS'], 'delay')?>
				<div class="basket-total clearfix">
					<div class="row">
						<div class="col-sm-3">
						</div>
						<div class="col-sm-9">
							<dl class="clearfix">
								<?
								if ($arResult['BASKET']['LOCAL_DELAY_DISCOUNT'] > 0) {
									?>
									<dt class="light basket-subtotal"><?=GetMessage('SOA_DELAY_SUMMARY_SUM')?>:</dt>
									<dd class="light basket-subtotal"><?=$arResult['BASKET']['LOCAL_DELAY_PRICE_WITHOUT_DISCOUNT_FORMATED']?></dd>
									
									<dt class="light basket-total-discount">
										<?=GetMessage('SOA_DELAY_SUMMARY_DISCOUNT')?><?if ($arResult['BASKET']['LOCAL_DELAY_DISCOUNT_PERCENT'] > 0) {
											?> (<?=$arResult['BASKET']['LOCAL_DELAY_DISCOUNT_PERCENT_FORMATED']?>)<?
										}?>:
									</dt>
									<dd class="light basket-total-discount"><?=$arResult['BASKET']['LOCAL_DELAY_DISCOUNT_FORMATED']?></dd>
									
									<dt class="basket-total-sum"><?=GetMessage('SOA_BASKET_SUMMARY_SUM_DISCOUNT')?>:</dt>
									<dd class="basket-total-sum"><?=$arResult['BASKET']['LOCAL_DELAY_DISCOUNT_FORMATED']?></dd>
									<?
								} else {
									?>
									<dt class="basket-total-sum"><?=GetMessage('SOA_DELAY_SUMMARY_SUM')?>:</dt>
									<dd class="basket-total-sum"><?=$arResult['BASKET']['LOCAL_DELAY_PRICE_FORMATED']?></dd>
									<?
								}
								?>
							</dl>
						</div>
					</div>
				</div>
			</section>
			<?
		}
		?>
	</form>
</div>