<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED): ?>
	<?php 
	echo '<div style="border: solid 1px #000; padding: 5px; font-weight:bold; color: #ff0000;">';
	echo GetMessage('PRMEDIA_MINIMARKET_DEMO_EXPIRED');
	echo '</div>';
	return;
	?>
<?php endif; ?>
<div class="catalog-element">
	<img src="<?php echo $arResult['PIC'] ?>" class="preview" alt="<?php echo $arResult['NAME'] ?>">
	<div class="description"><?php echo $arResult['DETAIL_TEXT'] ?></div>
	<div class="product-form">
		<?php if (!empty($arResult['PRICE_PARSED'])): ?>
			<div class="price">
				<span><?php echo $arResult['PRICE_PARSED']['R'] ?><?php if (!empty($arResult['PRICE_PARSED']['D'])): ?><sup><?php echo $arResult['PRICE_PARSED']['D'] ?></sup><?php endif; ?></span>
				<?php echo $arResult['PRICE_PARSED']['S'] ?> <?php echo GetMessage('PRMEDIA_MM_CEE_FOR') ?> <?php echo $arResult['PACKING'] ?>
			</div>
		<?php endif; ?>
		<?php if ($arResult['CAN_BUY']): ?>
			<form action="<?php echo POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
				<?php if ($arParams['USE_PRODUCT_QUANTITY']): ?>
					<div class="product-form-count clearfix">
						<a href="javascript:void(0);" class="count-btn minus"></a>
						<input type="text" name="<?php echo $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>" class="count-field" value="<?php echo $arResult['STEP'] ?>" data-step="<?php echo $arResult['STEP'] ?>">
						<a href="javascript:void(0);" class="count-btn plus"></a>
						<span class="packing"><?php echo $arResult['PACKING'] ?></span>
					</div>
				<?php else: ?>
					<input type="hidden" name="<?php echo $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>" value="<?php echo $arResult['STEP'] ?>">
				<?php endif; ?>
				<input type="hidden" name="<?php echo $arParams['ACTION_VARIABLE'] ?>" value="BUY">
				<input type="hidden" name="<?php echo $arParams['PRODUCT_ID_VARIABLE'] ?>" value="<?php echo $arResult['ID'] ?>">
				<div class="product-form-controls">
					<button type="submit" name="<?php echo $arParams['ACTION_VARIABLE'] . 'ADD2BASKET' ?>" class="to-basket"><?php echo GetMessage('PRMEDIA_MM_CEE_TO_BASKET') ?></button>
					<button type="submit" name="<?php echo $arParams['ACTION_VARIABLE'] . 'BUY' ?>" class="product-form-buy"><?php echo GetMessage('PRMEDIA_MM_CEE_BUY') ?></button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>