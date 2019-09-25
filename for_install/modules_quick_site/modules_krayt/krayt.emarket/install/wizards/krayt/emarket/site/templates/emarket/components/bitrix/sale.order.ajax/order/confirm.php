<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h1 class="order_title"><?=GetMessage("SOA_ORDER_TITLE")?></h1>
<?
if (!empty($arResult["ORDER"]))
{
	?>
    <div class="table_container">
    	<table class="sale_order_full_table">
            <tr>
                <th colspan="2"><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></th>
            </tr>
    		<tr>
    			<td class="left_column">
    				<div class="success_text"><?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?></div>
    				<p><?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?></p>
                    <p><?= GetMessage("SOA_TEMPL_ORDER_SUC2", Array("#LINK#" => SITE_DIR."personal/")) ?></p>
					<img src="<?=SITE_DIR?>/images/bag.png" alt="" />
    			</td>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<td class="right_column">
			<div class="pay_name"><?=GetMessage("SOA_TEMPL_PAY")?></div>
			<?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false);?>
			<p class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></p>
            <?
			if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
			{
				if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
				{
					?>
					<script language="JavaScript">
						window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
					</script>
					<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
					<?
					if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
					{
						?><br />
						<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
						<?
					}
				}
				else
				{
					if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
					{
						include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
					}
				}
			}
            ?>
		</td>
			<?
	}
    ?>
            </tr>
    	</table>
    </div>
    <?
}
else
{
	?>
	<b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b><br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
	<?
}
?>
