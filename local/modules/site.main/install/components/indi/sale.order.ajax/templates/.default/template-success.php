<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="step-success">
	<?
	if ($arResult['ORDER']) {
		?>
		<article class="details">
			<h2><?=GetMessage('SOA_ORDER_COMPLETE', array(
				'#PRICE_FORMATED#' => $arResult['ORDER']['PRICE_FORMATED'],
			))?></h2>
			
			<?
			if ($arResult['PAY_SYSTEM']) {
				?>
				<section class="payment">
					<article>
						<h3><?=GetMessage('SOA_ORDER_PAY')?></h3>
						<?if (is_array($arResult['PAY_SYSTEM']['LOGOTIP'])) {
							?><img class="img-thumbnail" src="<?=$arResult['PAY_SYSTEM']['LOGOTIP']['src']?>" alt="<?=$arResult['PAY_SYSTEM']['NAME']?>"/><?
						}?>
						<h4><?=$arResult['PAY_SYSTEM']['NAME']?></h4>
						<?
						if (strlen($arResult['PAY_SYSTEM']['ACTION_FILE'])) {
							if ($arResult['PAY_SYSTEM']['NEW_WINDOW'] == 'Y') {
								$url = $arParams['PATH_TO_PAYMENT'] . '?ORDER_ID=' . urlencode($arResult['ORDER']['ACCOUNT_NUMBER']);
								?>
								<script>
									window.open('<?=$url?>');
								</script>
								<p><?=GetMessage(
									in_array($arResult['ORDER']['PAY_SYSTEM_ID'], $arParams['PAY_SYSTEMS_ONLINE']) ? 'SOA_ORDER_PAY_LINK' : 'SOA_ORDER_PAY_PRINT',
									array(
										'#LINK#' => $url,
									)
								)?></p>
								
								<?
								if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE'])) {
									print GetMessage(
										'SOA_ORDER_PAY_PDF',
										array(
											'#LINK#' => $url . '&pdf=1&DOWNLOAD=Y',
										)
									);
								}
							} else {
								if (strlen($arResult['PAY_SYSTEM']['PATH_TO_ACTION'])) {
									ob_start();
									include($arResult['PAY_SYSTEM']['PATH_TO_ACTION']);
									$html = ob_get_contents();
									ob_end_clean();
									print str_replace(array(
										'type="submit"',
										'<font',
										'</font',
									), array(
										'type="submit" class="btn btn-default"',
										'<div',
										'</div',
									), $html);
								}
							}
						}
						?>
					</article>
				</section>
				<?
			}
			?>
			
			<div class="order-info clearfix">
				<div class="order-number">
					<?=GetMessage('SOA_ORDER_NUMBER')?>
					<div class="order-number-value"><?=$arResult['ORDER']['ACCOUNT_NUMBER']?></div>
				</div>
				
				<p><?=GetMessage('SOA_ORDER_SUCCESS1')?></p>
				<p><?=GetMessage('SOA_ORDER_SUCCESS2', array(
					'#DATE_INSERT#' => $arResult['ORDER']['DATE_INSERT'],
					'#PATH_TO_ORDERS_LIST#' => $arParams['PATH_TO_ORDERS_LIST'],
					'#FEEDBACK_PHONE#' => $arParams['FEEDBACK_PHONE'],
				))?></p>
			</div>
		</article>
		<?
	} else {
		?>
		<article class="error">
			<h2><?=GetMessage('SOA_ORDER_ERROR_ORDER')?></h2>
			<?
			ShowError(GetMessage('SOA_ORDER_ERROR_ORDER_LOST1', array(
				'#ORDER_ID#' => $arResult['ORDER_ID'],
			)));
			?>
			<p><?=GetMessage('SOA_ORDER_ERROR_ORDER_LOST2')?></p>
		</article>
		<?
	}
	?>
</div>