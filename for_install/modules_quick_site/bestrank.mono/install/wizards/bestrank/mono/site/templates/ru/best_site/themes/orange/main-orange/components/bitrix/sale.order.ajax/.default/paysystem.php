<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h2><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></h2>

<table>
	<?
	if ($arResult["PAY_FROM_ACCOUNT"]=="Y")
	{
		?>
		<tr>
		<td style="vertical-align:top;padding:0 5px;">
            <input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
            <input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()">
		</td>
		<td>
			<?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?>
            <p style="font-weight:normal;"><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT_TEXT", Array("#MONEY#" => $arResult["CURRENT_BUDGET_FORMATED"]))?></p>
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
                <?=$arPaySystem["NAME"];?>
                <p style="font-weight:normal;">
                <?
                if (strlen($arPaySystem["DESCRIPTION"])>0)
                {
                    ?>
                    <br />
                    <?=$arPaySystem["DESCRIPTION"]?>
                    <?
                }
                ?>
                </p>
			</td>
			</tr>
			<?
		}
		else
		{
			?>
			<tr>
				<td style="vertical-align:top;padding:0 5px;">
					<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?>>
				</td>
				<td>
					<label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>"><?= $arPaySystem["PSA_NAME"] ?></label>
                    <p style="font-weight:normal;">
					<?
					if (strlen($arPaySystem["DESCRIPTION"])>0)
					{
						?>
						<?=$arPaySystem["DESCRIPTION"]?>
						<br />
						<?
					}
					?>
					</p>
					
				</td>
			</tr>
			<?
		}
	}
	?>
</table>