<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if ($arResult === false) return; ?>
<div class="basket">
	<p><?php echo GetMessage('PRMEDIA_MM_SBBL_ORDER_SUM') ?>: <span class="basket-sum"><?php echo $arResult['SUM_FORMATTED'] ?></span></p>
	<a href="<?php echo $arParams['PATH_TO_BASKET'] ?>" class="basket-link"><?php echo GetMessage('PRMEDIA_MM_SBBL_PATH_TO_BASKET') ?></a>
</div>