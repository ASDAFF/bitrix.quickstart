<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<? if (!empty($arResult["ORDER"])): ?>
	<div class="order-form">
		<h4><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></h4>
		<table class="sale_order_full_table">
			<tr>
				<td>
					<?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER_ID"]))?><br />
					<?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
				</td>
			</tr>
		</table>
		<? if (!empty($arResult["PAY_SYSTEM"])): ?>
			<table class="sale_order_full_table">
				<tr>
					<td>
						<?=GetMessage("SOA_TEMPL_PAY")?>: <?= $arResult["PAY_SYSTEM"]["NAME"] ?>
					</td>
				</tr>
				<? if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0): ?>
					<tr>
						<td>
							<? if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y"): ?>
								<script language="JavaScript">
									window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?= $arResult["ORDER_ID"] ?>');
								</script>
								<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$arResult["ORDER_ID"])) ?>
							<? else: ?>
								<?
									if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0) {
										include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
									}
								?>
							<? endif; ?>
						</td>
					</tr>
				<? endif; ?>
			</table>
		<? endif; ?>
	</div>
<? else: ?>
	<h4><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></h4>
	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ORDER_ID"]))?>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
<? endif; ?>
