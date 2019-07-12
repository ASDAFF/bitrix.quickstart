<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php
foreach ($arResult['GRID']['ROWS'] as &$row)
{
	if (!empty($row['data']['NAME']) && !empty($row['data']['DETAIL_PAGE_URL']))
	{
		$row['data']['NAME'] = '<a href="' . $row['data']['DETAIL_PAGE_URL'] . '" target="_blank">' . $row['data']['NAME'] . '</a>';
	}
	
	if (!empty($row['columns']['PREVIEW_PICTURE']))
	{
		$row['data']['PREVIEW_PICTURE'] = $row['columns']['PREVIEW_PICTURE'];
	}
	else if (!empty($row['columns']['DETAIL_PICTURE']))
	{
		$row['data']['PREVIEW_PICTURE'] = $row['columns']['DETAIL_PICTURE'];
	}
	
	if (!empty($row['columns']['QUANTITY']))
	{
		$row['data']['QUANTITY'] = $row['columns']['QUANTITY'];
	}
}
unset($row);
?>
<div class="order-checkout-block order-checkout-goods">
	<h4><?php echo GetMessage('PRMEDIA_MM_SOA_BASKET') ?></h4>
	<div class="mobile-basket" style="margin-top: 0;">
		<?php foreach ($arResult['GRID']['ROWS'] as $k => $arData): ?>
			<table>
				<?php foreach ($arResult['GRID']['HEADERS'] as $id => $column): ?>
					<tr>
						<td class="fst"><?php echo $column['name'] ?></td>
						<td><?php echo $arData['data'][$column['id']] ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endforeach; ?>
	</div>
	<div class="basket" style="margin-top: 0;">
		<table>
			<thead>
				<tr>
					<?php foreach ($arResult['GRID']['HEADERS'] as $id => $column): ?>
						<th><?php echo $column['name']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($arResult['GRID']['ROWS'] as $k => $arData): ?>
					<tr>
						<?php foreach ($arResult['GRID']['HEADERS'] as $id => $column): ?>
							<td><?php echo $arData['data'][$column['id']] ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<div class="order-checkout-block order-checkout-comment">
	<h4><label for="order_description"><?php echo GetMessage('SOA_TEMPL_SUM_COMMENTS') ?></label></h4>
	<textarea name="ORDER_DESCRIPTION" id="order_description" rows="4"><?php echo $arResult['USER_VALS']['ORDER_DESCRIPTION'] ?></textarea>
</div>
<div class="order-checkout-block order-checkout-summary">
	<div>
		<?php echo GetMessage('SOA_TEMPL_SUM_WEIGHT_SUM') ?>
		<?php echo $arResult['ORDER_WEIGHT_FORMATED'] ?>
	</div>
	<div>
		<?php echo GetMessage('SOA_TEMPL_SUM_SUMMARY') ?>
		<?php echo $arResult['ORDER_PRICE_FORMATED'] ?>
	</div>
	<?php if (doubleval($arResult['DISCOUNT_PRICE']) > 0): ?>
		<div>
			<?php echo GetMessage('SOA_TEMPL_SUM_DISCOUNT') ?><?php if (strlen($arResult['DISCOUNT_PERCENT_FORMATED']) > 0): ?> (<?php echo $arResult['DISCOUNT_PERCENT_FORMATED']; ?>)<? endif; ?>:
			<?php echo $arResult['DISCOUNT_PRICE_FORMATED'] ?>
		</div>
	<?php endif; ?>
	<?php if (!empty($arResult['TAX_LIST'])): ?>
		<?php foreach ($arResult['TAX_LIST'] as $val): ?>
			<div>
				<?php echo $val['NAME'] ?> <?php echo $val['VALUE_FORMATED'] ?>:
				<?php echo $val['VALUE_MONEY_FORMATED'] ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (doubleval($arResult['DELIVERY_PRICE']) > 0): ?>
		<div>
			<?php echo GetMessage('SOA_TEMPL_SUM_DELIVERY') ?>
			<?php echo $arResult['DELIVERY_PRICE_FORMATED'] ?>
		</div>
	<?php endif; ?>
	<?php if (strlen($arResult['PAYED_FROM_ACCOUNT_FORMATED']) > 0): ?>
		<div>
			<?php echo GetMessage('SOA_TEMPL_SUM_PAYED') ?>
			<?php echo $arResult['PAYED_FROM_ACCOUNT_FORMATED'] ?>
		</div>
	<?php endif; ?>
	<?php if ($bUseDiscount): ?>
		<div>
			<?php echo GetMessage('SOA_TEMPL_SUM_IT') ?>
			<?php echo $arResult['ORDER_TOTAL_PRICE_FORMATED'] ?>
			<s><?php echo $arResult['PRICE_WITHOUT_DISCOUNT'] ?></s>
		</div>
	<?php else: ?>
		<?php echo GetMessage('SOA_TEMPL_SUM_IT') ?>
		<?php echo $arResult['ORDER_TOTAL_PRICE_FORMATED'] ?>
	<?php endif; ?>
</div>