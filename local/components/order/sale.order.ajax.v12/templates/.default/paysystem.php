<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<b><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></b>
<table class="sale_order_full_table">
	<?
	if ($arResult["PAY_FROM_ACCOUNT"]=="Y")
	{
		?>
		<tr>
		<td colspan="2">
		<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
		<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()">
		<label for="PAY_CURRENT_ACCOUNT"><b><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></b></label><br />
		<?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")?> <b><?=$arResult["CURRENT_BUDGET_FORMATED"]?></b>, <?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?>
		<br /><br />
		</td></tr>
		<?
	}

	foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
	{
		if(count($arResult["PAY_SYSTEM"]) == 1)
		{
			?>
			<tr>
			<td colspan="2">
			<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
			<b><?=$arPaySystem["NAME"];?></b>
			<?
			if (strlen($arPaySystem["DESCRIPTION"])>0)
			{
				?>
				<?=$arPaySystem["DESCRIPTION"]?>
				<br />
				<?
			}
			?>
			</td>
			</tr>
			<?
		}
		else
		{
			//if (!isset($_POST['PAY_CURRENT_ACCOUNT']) OR $_POST['PAY_CURRENT_ACCOUNT'] == "N") {
			?>
			<tr>
				<td width="0%" class="radio">
					<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm();" />
				</td>
				<td width="100%">
					<label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>">
							<b><?= $arPaySystem["PSA_NAME"] ?></b><br />
							<?
							if (strlen($arPaySystem["DESCRIPTION"])>0)
							{
								?>
								<?=$arPaySystem["DESCRIPTION"]?>
								<br />
								<?
							}
							?>
					</label>
				</td>
			</tr>
			<?
			//}
		}
	}
	?>
</table>