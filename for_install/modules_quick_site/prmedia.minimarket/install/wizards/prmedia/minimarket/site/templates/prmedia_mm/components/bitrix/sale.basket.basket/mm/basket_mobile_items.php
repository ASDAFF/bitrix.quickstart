<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="mobile-basket">
	<?php foreach ($arResult['ITEMS']['AnDelCanBuy'] as $item): ?>
		<table>
			<?php if (in_array('NAME', $arParams['COLUMNS_LIST'])): ?>
				<tr>
					<td class="fst"><?php echo GetMessage('PRMEDIA_MM_SBBM_NAME') ?></td>
					<td><a href="<?php echo $item['DETAIL_PAGE_URL'] ?>"><?php echo $item['NAME'] ?></a></td>
				</tr>
			<?php endif; ?>
			<?php if (in_array('QUANTITY', $arParams['COLUMNS_LIST'])): ?>
				<tr>
					<td class="fst"><?php echo GetMessage('PRMEDIA_MM_SBBM_QUANTITY') ?></td>
					<td>
						<div class="product-form">
							<div class="product-form-count clearfix">
								<a href="javascript:void(0);" class="count-btn minus"></a>
								<input name="QUANTITY_<?php echo $item['ID'] ?>" type="text" class="count-field" value="<?php echo $item['QUANTITY'] ?>" data-step="<?php echo $item['MEASURE_RATIO'] ?>">
								<a href="javascript:void(0);" class="count-btn plus"></a>
								<span class="packing"><?php echo $item['MEASURE_TEXT'] ?></span>
							</div>
						</div>
					</td>
				</tr>
			<?php endif; ?>
			<?php if (in_array('PRICE', $arParams['COLUMNS_LIST'])): ?>
				<tr>
					<td class="fst"><?php echo GetMessage('PRMEDIA_MM_SBBM_PRICE') ?></td>
					<td><?php echo $item['PRICE_FORMATED'] ?></td>
				</tr>
			<?php endif; ?>
			<?php if (in_array('SUM', $arParams['COLUMNS_LIST'])): ?>
				<tr>
					<td class="fst"><?php echo GetMessage('PRMEDIA_MM_SBBM_SUM') ?></td>
					<td><?php echo $item['SUM'] ?></td>
				</tr>
			<?php endif; ?>
			<?php if (in_array('DELETE', $arParams['COLUMNS_LIST'])): ?>
				<tr>
					<td class="fst"><?php echo GetMessage('PRMEDIA_MM_SBBM_QUANTITY') ?></td>
					<td class="mobile-basket-del"><a href="<?php echo $item['DELETE_LINK'] ?>" class="del"></a></td>
				</tr>
			<?php endif; ?>
		</table>
	<?php endforeach; ?>
	<div class="basket-controls clearfix">
		<p><b><?php echo GetMessage('PRMEDIA_MM_SBBM_ALL') ?></b> <?php echo $arResult['allSum_FORMATED'] ?></p>
		<input type="submit" value="<?php echo GetMessage('PRMEDIA_MM_SBBM_ORDER_LINK') ?>">
		<input type="submit" name="BasketRefresh" class="basket-recalc" value="<?php echo GetMessage('PRMEDIA_MM_SBBM_RECALC') ?>">
	</div>
</div>