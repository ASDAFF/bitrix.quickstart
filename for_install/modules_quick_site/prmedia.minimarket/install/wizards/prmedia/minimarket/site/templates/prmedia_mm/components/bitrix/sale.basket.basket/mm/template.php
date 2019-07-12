<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if (CModule::IncludeModuleEx('prmedia.minimarket') == MODULE_DEMO_EXPIRED): ?>
	<?php 
	echo '<div style="border: solid 1px #000; padding: 5px; font-weight:bold; color: #ff0000;">';
	echo GetMessage('PRMEDIA_MINIMARKET_DEMO_EXPIRED');
	echo '</div>';
	return;
	?>
<?php endif; ?>
<?php if (strlen($arResult['ERROR_MESSAGE']) <= 0): ?>
	<?php
	if (is_array($arResult['WARNING_MESSAGE']) && !empty($arResult['WARNING_MESSAGE']))
	{
		foreach ($arResult['WARNING_MESSAGE'] as $v)
		{
			echo ShowError($v);
		}
	}
	?>
	<form method="post" action="<?php echo POST_FORM_ACTION_URI ?>" name="basket_form" id="basket_form">
		<?php
		include($_SERVER["DOCUMENT_ROOT"] . "$templateFolder/basket_items.php");
		include($_SERVER["DOCUMENT_ROOT"] . "$templateFolder/basket_mobile_items.php");
		?>
		<input type="hidden" name="BasketOrder" value="BasketOrder" />
	</form>
<?php else: ?>
	<?php ShowError($arResult['ERROR_MESSAGE']); ?>
<?php endif; ?>