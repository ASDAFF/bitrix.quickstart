<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED): ?>
	<?php 
	echo '<div style="border: solid 1px #000; padding: 5px; font-weight:bold; color: #ff0000;">';
	echo GetMessage('PRMEDIA_MINIMARKET_DEMO_EXPIRED');
	echo '</div>';
	return;
	?>
<?php endif; ?>
<ul class="catalog-section">
	<?php foreach ($arResult['ITEMS'] as $item): ?>
		<?php
		$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
		$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
		$elementDeleteParams = array(
			'CONFIRM' => GetMessage('PRMEDIA_MM_CST_ELEMENT_DELETE_MSG')
		);
		$this->AddEditAction($item['ID'], $item['EDIT_LINK'], $elementEdit);
		$this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);
		$productContainerId = $this->GetEditAreaId($item['ID']);
		?>
		<li id="<?php echo $productContainerId ?>">
			<a href="<?php echo $item['DETAIL_PAGE_URL'] ?>" class="catalog-section-link"><?php echo $item['NAME'] ?></a>
			<a href="<?php echo $item['DETAIL_PAGE_URL'] ?>"><img src="<?php echo $item['PIC'] ?>" class="preview" alt="<?php echo $item['NAME'] ?>"></a>
			<div class="product-form">
				<?php if (!empty($item['PRICE_PARSED'])): ?>
					<div class="price">
						<span><?php echo $item['PRICE_PARSED']['R'] ?><?php if (!empty($item['PRICE_PARSED']['D'])): ?><sup><?php echo $item['PRICE_PARSED']['D'] ?></sup><?php endif; ?></span>
						<?php echo $item['PRICE_PARSED']['S'] ?> <?php echo GetMessage('PRMEDIA_MM_CST_FOR') ?> <?php echo $item['PACKING'] ?>
					</div>
				<?php endif; ?>
				<?php if ($item['CAN_BUY']): ?>
					<form action="<?php echo POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
						<?php if ($arParams['USE_PRODUCT_QUANTITY']): ?>
							<div class="product-form-count clearfix">
								<a href="javascript:void(0);" class="count-btn minus"></a>
								<input type="text" name="<?php echo $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>" class="count-field" value="<?php echo $item['STEP'] ?>" data-step="<?php echo $item['STEP'] ?>">
								<a href="javascript:void(0);" class="count-btn plus"></a>
								<span class="packing"><?php echo $item['PACKING'] ?></span>
							</div>
						<?php else: ?>
							<input type="hidden" name="<?php echo $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>" value="<?php echo $item['STEP'] ?>">
						<?php endif; ?>
						<input type="hidden" name="<?php echo $arParams['ACTION_VARIABLE'] ?>" value="BUY">
						<input type="hidden" name="<?php echo $arParams['PRODUCT_ID_VARIABLE'] ?>" value="<?php echo $item['ID'] ?>">
						<button type="submit" name="<?php echo $arParams['ACTION_VARIABLE'] . 'ADD2BASKET' ?>" class="to-basket"><?php echo GetMessage('PRMEDIA_MM_CST_TO_BASKET') ?></button>
					</form>
				<?php else: ?>
					<span><?php echo GetMessage('PRMEDIA_MM_CST_NOT_AVAILABLE') ?></span>
				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
