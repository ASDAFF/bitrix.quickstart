<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table border="0" cellspacing="0" cellpadding="5">
<tr>
	<td valign="top" width="60%" align="right">
		<input type="submit" name="contButton" value="<?= GetMessage("SALE_CONTINUE")?> &gt;&gt;">
	</td>
	<td valign="top" width="5%" rowspan="3">&nbsp;</td>
	<td valign="top" width="35%" rowspan="3">
		
		<?echo GetMessage("STOF_PROC_DIFFERS")?><br /><br />
		<?echo GetMessage("STOF_PRIVATE_NOTES")?>
		
	</td>
</tr>
<tr>
	<td valign="top" width="60%">
		<table class="sale_order_full_table">
			<tr>
				<td nowrap>
					<?echo GetMessage("STOF_SELECT_PERS_TYPE")?><br /><br />
					<?
					foreach($arResult["PERSON_TYPE_INFO"] as $v)
					{
						?><input type="radio" id="PERSON_TYPE_<?= $v["ID"] ?>" name="PERSON_TYPE" value="<?= $v["ID"] ?>"<?if ($v["CHECKED"]=="Y") echo " checked";?>> <label for="PERSON_TYPE_<?= $v["ID"] ?>"><?= $v["NAME"] ?></label><br /><?
					}
					?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" width="60%" align="right">
		<input type="submit" name="contButton" value="<?= GetMessage("SALE_CONTINUE")?> &gt;&gt;">
	</td>
</tr>
</table>