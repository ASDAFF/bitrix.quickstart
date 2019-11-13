<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="order-item">
	<div class="order-title">

		<b class="r2"></b><b class="r1"></b><b class="r0"></b>
		<div class="order-title-inner">
			<span><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></span>
		</div>
	</div>
	<div class="order-info">
<table width="100%" cellpadding="6" cellspacing="0">
	<?
	if ($arResult["PAY_FROM_ACCOUNT"]=="Y")
	{
		?>
		<tr>
		<td colspan="2">
		<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
		<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()"> <label for="PAY_CURRENT_ACCOUNT"><b><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></b></label><br />
		<?=GetMessage("SOA_TEMPL_PAY_ACCOUNT_TEXT", Array("#MONEY#" => $arResult["CURRENT_BUDGET_FORMATED"]))?>
		<br /><br />
		</td></tr>
		<?
	}
	?>
	<?
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
				<br />
				<?=$arPaySystem["DESCRIPTION"]?>
				<?
			}
			?>
			</td>
			</tr>
			<?
		}
		else
		{
			?>
			<tr>
				<td valign="top" width="0%">
					<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?>>
				</td>
				<td valign="top" width="100%">
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
		}
	}
	?>
</table>
</div>
</div>