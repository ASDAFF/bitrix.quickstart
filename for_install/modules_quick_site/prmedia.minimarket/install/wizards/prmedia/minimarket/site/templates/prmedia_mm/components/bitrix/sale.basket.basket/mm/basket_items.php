<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<table class="basket">
	<tr>
		<?php if (in_array('NAME', $arParams['COLUMNS_LIST'])): ?>
			<th><?php echo GetMessage('PRMEDIA_MM_SBBM_NAME') ?></th>
		<?php endif; ?>
		<?php if (in_array('QUANTITY', $arParams['COLUMNS_LIST'])): ?>
			<th class="basket-form"><?php echo GetMessage('PRMEDIA_MM_SBBM_QUANTITY') ?></th>
		<?php endif; ?>
		<?php if (in_array('PRICE', $arParams['COLUMNS_LIST'])): ?>
			<th><?php echo GetMessage('PRMEDIA_MM_SBBM_PRICE') ?></th>
		<?php endif; ?>
		<?php if (in_array('SUM', $arParams['COLUMNS_LIST'])): ?>
			<th><?php echo GetMessage('PRMEDIA_MM_SBBM_SUM') ?></th>
		<?php endif; ?>
		<?php if (in_array('DELETE', $arParams['COLUMNS_LIST'])): ?>
			<th class="basket-del"><?php echo GetMessage('PRMEDIA_MM_SBBM_DELETE') ?></th>
		<?php endif; ?>
	</tr>
	<?php foreach ($arResult['ITEMS']['AnDelCanBuy'] as $item): ?>
		<tr>
			<?php if (in_array('NAME', $arParams['COLUMNS_LIST'])): ?>
				<td><a href="<?php echo $item['DETAIL_PAGE_URL'] ?>"><?php echo $item['NAME'] ?></a></td>
			<?php endif; ?>
			<?php if (in_array('QUANTITY', $arParams['COLUMNS_LIST'])): ?>
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
			<?php endif; ?>
			<?php if (in_array('PRICE', $arParams['COLUMNS_LIST'])): ?>
				<td class="price"><?php echo $item['PRICE_FORMATED'] ?></td>
			<?php endif; ?>
			<?php if (in_array('SUM', $arParams['COLUMNS_LIST'])): ?>
				<td class="price"><?php echo $item['SUM'] ?></td>
			<?php endif; ?>
			<?php if (in_array('DELETE', $arParams['COLUMNS_LIST'])): ?>
				<td class="basket-del"><a href="<?php echo $item['DELETE_LINK'] ?>" class="del"></a></td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	<tfoot>
		<tr>
			<td colspan="10" class="basket-controls clearfix">
				<p><b><?php echo GetMessage('PRMEDIA_MM_SBBM_ALL') ?></b> <?php echo $arResult['allSum_FORMATED'] ?></p>
				<input type="submit" value="<?php echo GetMessage('PRMEDIA_MM_SBBM_ORDER_LINK') ?>">
				<input type="submit" name="BasketRefresh" class="basket-recalc" value="<?php echo GetMessage('PRMEDIA_MM_SBBM_RECALC') ?>">
			</td>
		</tr>
	</tfoot>
</table>