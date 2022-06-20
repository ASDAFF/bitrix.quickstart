<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="section">
<div class="title"><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></div>

<table class="sale_order_table paysystem">
	<?
	if ($arResult["PAY_FROM_ACCOUNT"]=="Y")
	{
		?>
		<tr>
			<td colspan="2" class="account">
				<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">

				<label for="PAY_CURRENT_ACCOUNT">
					<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()">

					<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" alt="" <?=($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")?"class=\"active\"":"";?> />
					<div class="desc">
						<div class="name"><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></div>
						<div class="desc">
							<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
							<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?></div>
						</div>
					</div>
				</label>
			</td>
		</tr>
		<?
	}
	?>
	<tr>
		<td colspan="2">
	<?
	foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
	{
		if(count($arResult["PAY_SYSTEM"]) == 1)
		{
			?>
			<div class="logo selected">
				<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
				<?if (count($arPaySystem["PSA_LOGOTIP"]) > 0):?>
					<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" title="<?=$arPaySystem["PSA_NAME"];?>"/>
				<?else:?>
					<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" title="<?=$arPaySystem["PSA_NAME"];?>"/>
				<?endif;?>
				<div class="paysystem_name"><?=$arPaySystem["NAME"];?></div>
			</div>
			<?
			if (strlen($arPaySystem["DESCRIPTION"])>0)
			{
				?>
				<?=$arPaySystem["DESCRIPTION"]?>
				<?
			}
		}
		else
		{
		?>
			<div class="logo">
				<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm();" />

				<label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>">
				<?if (count($arPaySystem["PSA_LOGOTIP"]) > 0):?>
					<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" title="<?=$arPaySystem["PSA_NAME"];?>"/>
				<?else:?>
					<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/logo-default-ps.gif" title="<?=$arPaySystem["PSA_NAME"];?>" />
				<?endif;?>

					<div class="paysystem_name"><?=$arPaySystem["PSA_NAME"];?></div>
				</label>
			</div>
		<?
		}
	}
	?>
	</td>
</tr>
</table>
</div>