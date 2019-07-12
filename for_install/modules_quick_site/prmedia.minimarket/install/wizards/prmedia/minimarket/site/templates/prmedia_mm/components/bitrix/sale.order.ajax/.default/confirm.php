<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php if (!empty($arResult["ORDER"])): ?>
	<b><?= GetMessage("SOA_TEMPL_ORDER_COMPLETE") ?></b>
	<p><?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"])) ?></p>
	<p><?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?></p>
	<?php if (!empty($arResult["PAY_SYSTEM"])): ?>
		<?
	if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
	{
		?>
				<?
				if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
				{
					?>
					<script language="JavaScript">
						window.open('<?= $arParams["PATH_TO_PAYMENT"] ?>?ORDER_ID=<?= urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])) ?>');
					</script>
					<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])))) ?>
					<?
					if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
					{
						?><br />
						<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])) . "&pdf=1&DOWNLOAD=Y")) ?>
						<?
					}
				}
				else
				{
					if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]) > 0)
					{
						echo '<div class="order-nobr">';
						include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
						echo '</div>';
					}
				}
				?>
		<?
	}
	?>
	<?php endif; ?>
<?php else: ?>
	<p><?= GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"])) ?><?= GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1") ?></p>
<?php endif; ?>