<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	//echo "<pre>"; print_r($arResult); echo "</pre>";
?>
<div class="notetext">
<?
if (!empty($arResult["ORDER"]))
{
	?>
	<h3><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></h3>
	<div class="success">
	<p>
		<?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER_ID"]))?><br /><br />
		<?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
	</p>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<p><?=GetMessage("SOA_TEMPL_PAY")?>: <?= $arResult["PAY_SYSTEM"]["NAME"] ?></p><br />
		<?
		if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
		{
			if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
			{
				?>
				<script language="JavaScript">
					window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?= $arResult["ORDER_ID"] ?>');
				</script>
				<p><?=GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$arResult["ORDER_ID"])) ?></p>
				<?
			}
			else
			{
				if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
				{
					include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
				}
			}
		}
	} ?>

<script>
function Load(){
text = document.getElementById('check-area').innerHTML;
printwin = open('', 'printwin', 'width=600,height=800');
printwin.document.open();
printwin.document.writeln('<html><head><title></title><link href="<?=SITE_TEMPLATE_PATH?>/template_styles.css" type="text/css" rel="stylesheet" /></head><body style="width: 600pt; background: #fff; padding: 20px;" onload=print(); close(); >');
printwin.document.writeln('<div  id="check-area">'+text+'</div>');
printwin.document.writeln('</body></html>');
printwin.document.close();
}
</script>

	<input class="bt3" type="button" onclick="Load()" value="<?=GetMessage("PRINT_CHECK")?>" />

	</div>

	<div class="check"   id="check-area">
		<table cellspacing="0" cellpadding="0" border="0" class="check-table-top">
			<?
				$shopOfName=COption::GetOptionString("bijou", "shopOfName", GetMessage("SOA_TEMPL_SHOP_NAME"), $siteID);
			?>
			<?if(strlen($shopOfName)>0){?>
				<thead>
					<tr><td colspan="2"><?=$shopOfName?></td></tr>
					<tr><td colspan="2"></td></tr>
				</thead>
			<?}?>
			<?foreach($arResult['ORDER_ITEMS'] as $k=>$v){?>
				<tr>
					<td class="product_name" valign="bottom"><span><?=$v["NAME"]?></span></td>

					<td class="quantity" valign="bottom"><nobr><?=$v["QUANTITY"]?> x <?=SaleFormatCurrency($v["PRICE"], $v["CURRENCY"])?></nobr></td>
					</tr>
				<?}?>
			</table>
	
		<table cellspacing="0" cellpadding="0" border="0" class="check-table-bottom">
			<thead>
				<tr><td colspan="2"></td></tr>
			</thead>
			<tr>
				<td class="name" valign="top"><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></td>

				<td valign="top"><?=(intval($arResult["ORDER"]["PRICE_DELIVERY"])>0 ? SaleFormatCurrency($arResult["ORDER"]["PRICE_DELIVERY"], $arResult["ORDER"]["CURRENCY"]) : GetMessage("SOA_TEMPL_SUM_FREE"))?></td>
			</tr>
			<?if(intval($arResult["ORDER"]["DISCOUNT_VALUE"])>0){?>
				<tr>
					<td class="name" valign="top"><?=GetMessage("SOA_TEMPL_SUM_DISCOUNT")?></td>

					<td valign="top"><?=SaleFormatCurrency($arResult["ORDER"]["DISCOUNT_VALUE"], $arResult["ORDER"]["CURRENCY"])?></td>
				</tr>
			<?}?>
			<tr>
				<td class="name" valign="top"><?=GetMessage("SOA_TEMPL_SUM_IT")?></td>

				<td valign="top"><?=SaleFormatCurrency($arResult["ORDER"]["PRICE"], $arResult["ORDER"]["CURRENCY"])?></td>
			</tr>
			<?if (!empty($arResult["PAY_SYSTEM"])){?>
				<tr>
					<td class="name" valign="top"><?=GetMessage("SOA_TEMPL_PAY")?>:</td>

					<td class="quantity" valign="top"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></td>
				</tr>			
			<?}?>
			<?if (!empty($arResult["DELIVERY"])){?>
				<tr>
					<td class="name" valign="top"><?=GetMessage("SOA_TEMPL_SUM_DELIVERY_WAY")?></td>

					<td class="quantity" valign="top"><?=$arResult["DELIVERY"]["NAME"] ?>
						<?if(strlen($arResult["DELIVERY"]["DESCRIPTION"])>0){?>
							<br /><?=$arResult["DELIVERY"]["DESCRIPTION"]?>
						<?}?>
					</td>
				</tr>			
			<?}?>
			<?foreach($arResult['USER'] as $k=>$v){?>
				<tr>
					<td class="name" valign="top"><?=$v["NAME"]?>:</td>

					<td class="quantity" valign="top"><?=$v["VALUE"]?></td>
				</tr>
			<?}?>
			<tfoot>
				<tr><td colspan="2"><?=GetMessage("SOA_TEMPL_THANKYOU")?></td></tr>
				<tr><td colspan="2"></td></tr>
			</tfoot>
		</table>
	</div>
	<div style="clear:both;"></div>

	<?
}
else
{
	?>
	<p>
	<b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b><br />
	<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ORDER_ID"]))?>
	<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
	</p>
	<?
}
?>
</div>
